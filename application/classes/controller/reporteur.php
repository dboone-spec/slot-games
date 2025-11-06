<?php

class Controller_Admin1_Reporteur extends Controller_Admin1_Base
{


    public function action_index()
    {


        $office_id = arr::get($_GET, 'office_id', -1);
        $owner = arr::get($_GET, 'owner', -1);
        $is_test = arr::get($_GET, 'is_test', false);
        $by_month = arr::get($_GET, 'by_month', false);
        $only_total = arr::get($_GET, 'only_total', false);
        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : null;

        if (!isset($time_from)) {
            $time_from = date("Y-m-d", strtotime("-1 months"));
        }
        if (!isset($time_to) || $time_to==date('Y-m-d')) {
            $time_to = date("Y-m-d", strtotime("-1 day"));
        }


        $unix_from = strtotime($time_from);
        $unix_to = strtotime($time_to);


        $sql_games = "Select date,s.office_id,c.code, sum(count) as count,
            sum(
                case when  bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in end
            ) as in,
            sum(amount_out) as out,
            sum(((case when  bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in end)-amount_out)) as win,
            sum(
                case when bettype = 'norcfs' then amount_in else 0 end
            ) as cfsin,
            sum(
                case when bettype = 'norlfs' then amount_in else 0 end
            ) as lfsin,
            sum(
                case when bettype = 'norafs' then amount_in else 0 end
            ) as afsin,
            sum(
                case when bettype = 'jp' then amount_out else 0 end
            ) as jp,
            c.mult
        From statistics s
        join offices o on o.id=s.office_id
        join currencies c on o.currency_id=c.id
        Where
            date >= :time_from
            AND date <= :time_to
            AND bettype in :types
            and s.test=0
            ";


        $sql_games .= " AND s.office_id in :officesEnabled ";


        if ($office_id != -1) {
            $sql_games .= " AND s.office_id =:oid ";
        }

        if ($owner != -1) {
            $sql_games .= " AND o.owner =:ownerid ";
        }

        $sql_games .= ' GROUP BY date, s.office_id, c.code, c.mult
        ORDER BY date, s.office_id ';

        if (person::$role == 'report') {
            $o_id = person::user()->office_id;
            $enabledOffices = [$o_id => $o_id];
        } else {
            $enabledOffices = Person::user()->offices($is_test);
        }


        $statistic = db::query(1, $sql_games)->parameters([
            ':time_from' => $time_from,
            ':time_to' => $time_to,
            ':office_id' => $office_id,
            ':ownerid' => $owner,
            ':types' => [
                'normal', //normal
                'norafs', //normal from freespin from api
                'norcfs', //normal from freespin from cashback
                'norlfs', //normal from freespin from luckyspins

                'double', //double
                'douafs', //double from freespin from api
                'doucfs', //double from freespin from cashback
                'doulfs', //double from freespin from luckyspins

                'free',   //free
                'freafs', //free from freespin from api
                'frecfs', //free from freespin from cashback
                'frelfs', //free from freespin from luckyspins
                'jp',
                'prize'
            ],
            ':officesEnabled' => $enabledOffices,
            ':oid' => $office_id
        ])->execute()->as_array();

        $currency_rates = db::query(1, 'select date as d,* from currency_rates as c where date >= :time_from
            AND date <= :time_to')
            ->parameters([
                ':time_from' => $unix_from,
                ':time_to' => $unix_to,
            ])
            ->execute()->as_array();

        $day_rate = [];

        foreach ($currency_rates as $cr) {
            $day_rate[date('Y-m-d', $cr['d'])][$cr['currency']] = $cr['value'];
        }

        $data = [];
        $totalOffice = [];
        $total = ['in' => 0, 'inEUR'=>0, 'out' => 0, 'outEUR' => 0, 'count' => 0, 'win' => 0, 'winEUR'=>0, 'offices' => [], 'rtp' => 0, 'currencies' => []];


        foreach ($statistic as $row) {

            $var = $row['date'];
            if ($by_month) {
                $var = date('Y-m', strtotime($row['date']));
            }

            if (isset($data[$var][$row['office_id']])) {
                $data[$var][$row['office_id']]['in'] += $row['in'];
                $data[$var][$row['office_id']]['mult'] = $row['mult'];
                $data[$var][$row['office_id']]['out'] += $row['out'];
                $data[$var][$row['office_id']]['win'] += $row['win'];
                $data[$var][$row['office_id']]['count'] += $row['count'];
                $data[$var][$row['office_id']]['rtp'] = ($data[$var][$row['office_id']]['in'] == 0 ? 0 : round($data[$var][$row['office_id']]['out'] / $data[$var][$row['office_id']]['in'] * 100, 2));
            } else {
                $data[$var][$row['office_id']] = [
                    'in' => $row['in'],
                    'mult' => $row['mult'],
                    'currency' => $row['code'],
                    'out' => $row['out'],
                    'win' => $row['win'],
                    'count' => $row['count'],
                    'rtp' => $row['in'] == 0 ? 0 : round($row['out'] / $row['in'] * 100, 2),
                ];
            }

            if (isset($totalOffice[$var])) {
                $totalOffice[$var]['in'] += round($row['in'], $row['mult']);
                $totalOffice[$var]['inEUR'] += round($this->_getRate($day_rate,$row['date'],$row['code'])*$row['in'], $row['mult']);
                $totalOffice[$var]['mult'] = $row['mult'] > $totalOffice[$var]['mult'] ? $row['mult'] : $totalOffice[$var]['mult'];
                $totalOffice[$var]['out'] += round($row['out'], $row['mult']);
                $totalOffice[$var]['outEUR'] += round($this->_getRate($day_rate,$row['date'],$row['code'])*$row['out'], $row['mult']);
                $totalOffice[$var]['win'] += round($row['win'], $row['mult']);
                $totalOffice[$var]['winEUR'] += round($this->_getRate($day_rate,$row['date'],$row['code'])*$row['win'], $row['mult']);
                $totalOffice[$var]['count'] += $row['count'];


            } else {
                $totalOffice[$var] = [
                    'in' => round($row['in'], $row['mult']),
                    'inEUR' => round($this->_getRate($day_rate,$row['date'],$row['code'])*$row['in'], $row['mult']),
                    'mult' => $row['mult'],
                    'out' => round($row['out'], $row['mult']),
                    'outEUR' => round($this->_getRate($day_rate,$row['date'],$row['code'])*$row['out'], $row['mult']),
                    'win' => round($row['win'], $row['mult']),
                    'winEUR' => round($this->_getRate($day_rate,$row['date'],$row['code'])*$row['win'], $row['mult']),
                    'count' => $row['count'],
                ];
            }

            if (isset($total['offices'][$row['office_id']])) {
                $total['offices'][$row['office_id']]['in'] += $row['in'];
                $total['offices'][$row['office_id']]['inEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['in'];
                $total['offices'][$row['office_id']]['mult'] = $row['mult'];
                $total['offices'][$row['office_id']]['out'] += $row['out'];
                $total['offices'][$row['office_id']]['outEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['out'];
                $total['offices'][$row['office_id']]['win'] += $row['win'];
                $total['offices'][$row['office_id']]['winEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['win'];
                $total['offices'][$row['office_id']]['count'] += $row['count'];

            } else {
                $total['offices'][$row['office_id']] = ['in' => $row['in'],
                    'inEUR' => $this->_getRate($day_rate,$row['date'],$row['code'])*$row['in'],
                    'mult' => $row['mult'],
                    'out' => $row['out'],
                    'outEUR' => $this->_getRate($day_rate,$row['date'],$row['code'])*$row['out'],
                    'win' => $row['win'],
                    'winEUR' => $this->_getRate($day_rate,$row['date'],$row['code'])*$row['win'],
                    'count' => $row['count'],
                    'currency' => $row['code'],
                ];
            }

            ksort($total['offices']);

            if (isset($total['currencies'][$row['code']])) {
                $total['currencies'][$row['code']]['in'] += $row['in'];
                $total['currencies'][$row['code']]['inEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['in'];
                $total['currencies'][$row['code']]['mult'] = $row['mult'];
                $total['currencies'][$row['code']]['out'] += $row['out'];
                $total['currencies'][$row['code']]['outEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['out'];
                $total['currencies'][$row['code']]['win'] += $row['win'];
                $total['currencies'][$row['code']]['winEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['win'];
                $total['currencies'][$row['code']]['count'] += $row['count'];


            } else {
                $total['currencies'][$row['code']] = ['in' => $row['in'],
                    'inEUR' => $this->_getRate($day_rate,$row['date'],$row['code'])*$row['in'],
                    'mult' => $row['mult'],
                    'out' => $row['out'],
                    'outEUR' => $this->_getRate($day_rate,$row['date'],$row['code'])*$row['out'],
                    'win' => $row['win'],
                    'winEUR' => $this->_getRate($day_rate,$row['date'],$row['code'])*$row['win'],
                    'count' => $row['count'],
                    'currency' => $row['code'],
                ];
            }

            ksort($total['currencies']);

            $total['in'] += $row['in'];
            $total['inEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['in'];
            $total['out'] += $row['out'];
            $total['outEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['out'];
            $total['win'] += $row['win'];
            $total['winEUR'] += $this->_getRate($day_rate,$row['date'],$row['code'])*$row['win'];
            $total['count'] += $row['count'];
            $total['rtp'] = $total['in'] == 0 ? '&nbsp;' : round($total['out'] / $total['in'] * 100, 2);
        }


        foreach ($totalOffice as $key => $val) {
            $totalOffice[$key]['rtp'] = $totalOffice[$key]['in'] == 0 ? '&nbsp;' : round($totalOffice[$key]['out'] / $totalOffice[$key]['in'] * 100, 2);

        }

        foreach ($total['offices'] as $key => $val) {
            $total['offices'][$key]['rtp'] = $total['offices'][$key]['in'] == 0 ? '&nbsp;' : round($total['offices'][$key]['out'] / $total['offices'][$key]['in'] * 100, 2);

        }


        $officesList = [-1 => 'All'] + Person::user()->officesName(null, true);

        $owners_sql = db::query(1, 'select p.id,p.comment from persons p where comment is not null and comment !=\'\'')->execute()->as_array('id');
        $owner_offices_sql = db::query(1, 'select id,owner from offices where owner is not null')->execute()->as_array('id');

        $owners = [-1 => 'All'];
        $owner_offices = [];

        foreach ($owners_sql as $s) {
            $owners[$s['id']] = $s['comment'];
        }

        foreach ($owner_offices_sql as $s) {
            $owner_offices[$s['id']] = arr::get($owners_sql, $s['owner'], ['comment' => ''])['comment'];
        }


        $view = new View('admin1/report/eur');
        $view->time_from = $time_from;
        $view->time_to = $time_to;
        $view->office_id = $office_id;
        $view->owner = $owner;
        $view->is_test = $is_test;
        $view->only_total = $only_total;
        $view->by_month = $by_month;
        $view->officesList = $officesList;
        $view->data = $data;
        $view->day_rate = $day_rate;
        $view->total = $total;
        $view->totalOffice = $totalOffice;
        $view->owners = $owners;
        $view->owner_offices = $owner_offices;
        $this->template->content = $view;

    }

    protected function _getRate($day_rate,$date,$currency) {
        if(isset($day_rate[$date][$currency])) {
            return $day_rate[$date][$currency];
        }
        return 0;
    }

}
