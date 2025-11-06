<?php

class Controller_Admin1_Reportmonth extends Controller_Admin1_Base
{


    public function action_index()
    {


        $office_id = arr::get($_GET, 'office_id', -1);
        $owner = arr::get($_GET, 'owner', -1);
        $is_test = arr::get($_GET, 'is_test', false);
        $m = $_GET['m'] ?? date('m', time() - 28 * 24 * 60 * 60);
        $y = $_GET['y'] ?? date('Y', time() - 28 * 24 * 60 * 60);

        $months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

        $timeFrom = date("Y-m-d", mktime(0, 0, 0, $m, 1, $y));

        $mNext = $m + 1;
        $yNext = $y;
        if ($mNext > 12) {
            $mNext = 1;
            $yNext++;
        }

        $timeToDB = mktime(0, 0, 0, $mNext, 1, $yNext);
        $timeTo = date("Y-m-d", mktime(0, 0, 0, $mNext, 1, $yNext) - 60 * 60 * 24);


        $sql = "Select s.office_id, o.external_name, c.code, sum(count) as count,
            sum(
                case when  bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in end
            ) as in,
            sum(amount_out) as out,
            sum((case when  bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in end)-amount_out) as win,
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
            ) as jp
        From statistics s
        join offices o on o.id=s.office_id
        join currencies c on o.currency_id=c.id
        Where
            date >= :time_from
            AND date <= :time_to
            AND bettype in :types
            AND s.office_id in :officesEnabled
            and s.test=0
            ";

        if ($office_id != -1) {
            $sql .= " AND s.office_id =:oid ";
        }

        if ($owner != -1) {
            $sql .= " AND o.owner =:ownerid ";
        }

        $sql .= ' GROUP BY s.office_id, o.external_name, c.code
            ORDER BY s.office_id ';

        if (person::$role == 'report') {
            $o_id = person::user()->office_id;
            $enabledOffices = [$o_id => $o_id];
        } else {
            $enabledOffices = Person::user()->offices($is_test);
        }


        $statistic = db::query(1, $sql)->parameters([
            ':time_from' => $timeFrom,
            ':time_to' => $timeTo,
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


        $sql = 'select currency as name, value 
                from currency_rates
                where date=:date';


        $rates = db::query(1, $sql)->param(':date', $timeToDB)->execute()->as_array('name');


        $bad = false;
        if (count($rates) < 2) {
            $bad = true;
        }

        $byCurrency = [];
        $byExternalName = [];

        $zero = [
            'owner' => '',
            'office' => '',
            'in' => 0,
            'out' => 0,
            'win' => 0,
            'count' => 0,
            'rate' => 0,
            'curencyIn' => 0,
            'curencyOut' => 0,
            'curencyWin' => 0,
        ];

        $total = $zero;

        foreach ($statistic as &$row) {

            $currency = $row['code'];

            $row['owner'] = isset($owner_offices[$row['office_id']]) ? $owner_offices[$row['office_id']] : '';
            $row['office'] = Person::user()->officesName($row['office_id'], true);
            $row['rtp'] = $row['in'] > 0 ? round($row['out'] / $row['in'] * 100, 2) : 0;
            $row['rate'] = $rates[$currency]['value'] ?? 0;
            $row['curencyIn'] = $row['rate'] * $row['in'];
            $row['curencyOut'] = $row['rate'] * $row['out'];
            $row['curencyWin'] = $row['rate'] * $row['win'];

            $total['count'] += $row['count'];
            $total['curencyIn'] += $row['curencyIn'];
            $total['curencyOut'] += $row['curencyOut'];
            $total['curencyWin'] += $row['curencyWin'];

            if (!isset($byCurrency[$currency])) {
                $byCurrency[$currency] = $zero;
                $byCurrency[$currency]['rate'] = $rates[$row['code']]['value'] ?? 0;
            }
            $byCurrency[$currency]['in'] += $row['in'];
            $byCurrency[$currency]['out'] += $row['out'];
            $byCurrency[$currency]['win'] += $row['win'];
            $byCurrency[$currency]['count'] += $row['count'];
            $byCurrency[$currency]['curencyIn'] += $row['curencyIn'];
            $byCurrency[$currency]['curencyOut'] += $row['curencyOut'];
            $byCurrency[$currency]['curencyWin'] += $row['curencyWin'];


            $externalName = empty($row['external_name']) ? 'others' : $row['external_name'];
            if (!isset($byExternalName[$externalName])) {
                $byExternalName[$externalName] = $zero;
            }

            $byExternalName[$externalName]['count'] += $row['count'];
            $byExternalName[$externalName]['curencyIn'] += $row['curencyIn'];
            $byExternalName[$externalName]['curencyOut'] += $row['curencyOut'];
            $byExternalName[$externalName]['curencyWin'] += $row['curencyWin'];

        }

        //Sorting
        ksort($byCurrency);
        ksort($byExternalName);
        if (isset($byExternalName['others'])) {
            $others = $byExternalName['others'];
            unset($byExternalName['others']);
            $byExternalName['others'] = $others;
        }


        //export to xslx
        if (arr::get($_GET, 'xls') == 'go') {
            $data = [];
            $data[] = [$months[intval($m)] . " $y"];
            $data[] = ['OWNER', 'OFFICE', 'CURRENCY', 'IN', 'OUT', 'WIN', 'COUNT', 'RTP', 'RATE (EUR)', 'IN (EUR)', 'OUT (EUR)', 'WIN (EUR)'];


            foreach ($statistic as $row1) {
                $data[] = [
                    $row1['owner'],
                    $row1['office'],
                    $row1['code'],
                    $row1['in'],
                    $row1['out'],
                    $row1['win'],
                    $row1['count'],
                    $row1['rtp'],
                    $row1['rate'],
                    $row1['curencyIn'],
                    $row1['curencyOut'],
                    $row1['curencyWin'],
                ];

            }

            $total = [
                '',
                'Total',
                '',
                '',
                '',
                '',
                $total['count'],
                '',
                '',
                $total['curencyIn'],
                $total['curencyOut'],
                $total['curencyWin'],
            ];

            $data[] = $total;

            foreach ($byCurrency as $curName => $row1) {
                $data[] = [
                    'Total per Currency',
                    '',
                    $curName,
                    $row1['in'],
                    $row1['out'],
                    $row1['win'],
                    $row1['count'],
                    '',
                    $row1['rate'],
                    $row1['curencyIn'],
                    $row1['curencyOut'],
                    $row1['curencyWin'],
                ];

            }
            $data[] = $total;

            foreach ($byExternalName as $name => $row1) {
                $data[] = [
                    'Total per Operator',
                    $name,
                    '',
                    '',
                    '',
                    '',
                    $row1['count'],
                    '',
                    '',
                    $row1['curencyIn'],
                    $row1['curencyOut'],
                    $row1['curencyWin'],
                ];

            }
            $data[] = $total;

            $data[] = [''];
            $data[] = [" To check the exchange rate:"];
            $data[] = ["1) Go to the link https://www.xe.com/currencytables/"];
            $data[] = ["2) Checking DATE $timeTo "];
            $data[] = ["3) Click Confirm"];
            $data[] = ["4) Press the key combination Ctrl+F"];
            $data[] = ["5) In the window, insert the currency, for example TRY"];


            $writer = new XLSXWriter();
            $writer->writeSheet($data, 'Sheet1');            // no headers
            $this->response->headers('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->response->headers('Content-Disposition', 'attachment;filename="' . $months[intval($m)] . '-' . $y . '.xlsx"');
            $this->response->headers('Cache-Control', 'max-age=0');
            $this->response->body($writer->writeToString());
            $this->auto_render = false;
            return null;
        }


        $officesList = [-1 => 'All'] + Person::user()->officesName(null, true);


        $nowYear = date('Y');

        for ($i = 2022; $i <= $nowYear; $i++) {
            $years[$i] = $i;
        }


        $view = new View('admin1/reportmonth/index');
        $view->timeFrom = $timeFrom;
        $view->timeTo = $timeTo;
        $view->bad = $bad;
        $view->m = $m;
        $view->y = $y;
        $view->office_id = $office_id;
        $view->owner = $owner;
        $view->is_test = $is_test;
        $view->officesList = $officesList;
        $view->data = $statistic;
        $view->byCurrency = $byCurrency;
        $view->byExternalName = $byExternalName;
        $view->total = $total;
        $view->months = $months;
        $view->years = $years;
        $view->owners = Person::user()->owners([-1 => 'All']);

        $this->template->content = $view;

    }


}
