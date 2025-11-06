<?php

class Controller_Admin1_Report extends Controller_Admin1_Base
{
/*
Statistic  test убрать
Statistic for month test убрать
Day report UTC добавить галочку тест
Day report Local time всегда показывает только реальных, тестовых нет
Month report UTC test убрать
DS LS report UTC ??
Discount report  test убрать
Day report UTC EUR ??
Promo report ??


тестовые только sa для всех остальных только реальные*/

    public function action_index()
    {


        $office_id = arr::get($_GET, 'office_id', -1);
        $owner = arr::get($_GET, 'owner', -1);
        $is_test = arr::get($_GET, 'is_test', false);
        $isTestUser =  arr::get($_GET, 'isTestUser', 0);
        $by_month = arr::get($_GET, 'by_month', false);
        $only_total = arr::get($_GET, 'only_total', false);
        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : null;

        if (!isset($time_from)) {
            $time_from = date("Y-m-d", strtotime("-1 months"));
        }
        if (!isset($time_to)) {
            $time_to = date("Y-m-d");
        }

        //конвертация
        $c = '1';
        if ($convert = arr::get($_GET, 'convert')) {
            $c = 'c.val';
        }

        $sql_games = "Select date,s.office_id,c.code, sum(count) as count,
            sum(
                case when  bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in*$c end
            ) as in,
            sum(amount_out*$c) as out,
            sum(((case when  bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in end)-amount_out)*$c) as win,
            sum(
                case when bettype = 'norcfs' then amount_in*$c else 0 end
            ) as cfsin,
            sum(
                case when bettype = 'norlfs' then amount_in*$c else 0 end
            ) as lfsin,
            sum(
                case when bettype = 'norafs' then amount_in*$c else 0 end
            ) as afsin,
            sum(
                case when bettype = 'norcfs' then amount_out*$c else 0 end
            ) as cfsout,
            sum(
                case when bettype = 'norlfs' then amount_out*$c else 0 end
            ) as lfsout,
            sum(
                case when bettype = 'norafs' then amount_out*$c else 0 end
            ) as afsout,
            sum(
                case when bettype = 'jp' then amount_out*$c else 0 end
            ) as jp,
            sum(
                case when bettype = 'prize' then amount_out*$c else 0 end
            ) as promoout,
            sum(
                case when bettype = 'prize' then count else 0 end
            ) as promocnt,
            c.mult
        From statistics s
        join offices o on o.id=s.office_id
        join currencies c on o.currency_id=c.id
        Where
            date >= :time_from
            AND date <= :time_to
            AND bettype in :types";


        $sql_games .= " AND s.office_id in :officesEnabled ";

        $sql_users = "select date||'::'||office_id as id,users,newusers
                from statistic_users s
                where date >= :time_from
                        AND date <= :time_to
                        AND s.office_id in :officesEnabled ";


        if ($office_id != -1) {
            $sql_games .= " AND s.office_id =:oid ";
            $sql_users .= " AND s.office_id =:oid ";
        }

        if ($owner != -1) {
            $sql_games .= " AND o.owner =:ownerid ";
        }

        if (Person::$role == 'sa'){
            $sql_games .= " AND s.test = :test ";
        }
        else{
            $sql_games .= " AND s.test = 0 ";
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
            ':test' => $isTestUser,
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


        $statisticUsers = db::query(1, $sql_users)->parameters([
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
            ],
            ':officesEnabled' => $enabledOffices,
            ':oid' => $office_id
        ])->execute()->as_array('id');

        //по тестовым не показываем статистику
        if ($isTestUser == 1){
            $statisticUsers = [];
        }

        $data = [];
        $totalOffice = [];
        $total = ['in' => 0,
            'cfsin' => 0, 'lfsin' => 0, 'afsin' => 0,
            'cfsout' => 0, 'lfsout' => 0, 'afsout' => 0,
            'out' => 0, 'count' => 0, 'win' => 0, 'offices' => [], 'rtp' => 0, 'currencies' => [], 'newusers' => 0,
            'jp' => 0, 'promoout' => 0, 'promocnt' => 0, 'users' => 0];


        foreach ($statistic as $row) {

            $var = $row['date'];
            if ($by_month) {
                $var = date('Y-m', strtotime($row['date']));
            }

            if (isset($data[$var][$row['office_id']])) {
                $data[$var][$row['office_id']]['in'] += $row['in'];
                $data[$var][$row['office_id']]['cfsin'] += $row['cfsin'];
                $data[$var][$row['office_id']]['lfsin'] += $row['lfsin'];
                $data[$var][$row['office_id']]['afsin'] += $row['afsin'];
                $data[$var][$row['office_id']]['cfsout'] += $row['cfsout'];
                $data[$var][$row['office_id']]['lfsout'] += $row['lfsout'];
                $data[$var][$row['office_id']]['afsout'] += $row['afsout'];
                $data[$var][$row['office_id']]['mult'] = $row['mult'];
                $data[$var][$row['office_id']]['out'] += $row['out'];
                $data[$var][$row['office_id']]['win'] += $row['win'];
                $data[$var][$row['office_id']]['count'] += $row['count'];
                $data[$var][$row['office_id']]['rtp'] = ($data[$var][$row['office_id']]['in'] == 0 ? 0 : round($data[$var][$row['office_id']]['out'] / $data[$var][$row['office_id']]['in'] * 100, 2));
                $data[$var][$row['office_id']]['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';
                $data[$var][$row['office_id']]['newusers'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';
                $data[$var][$row['office_id']]['jp'] += $row['jp'];
                $data[$var][$row['office_id']]['promoout'] += $row['promoout'];
                $data[$var][$row['office_id']]['promocnt'] += $row['promocnt'];
            } else {
                $data[$var][$row['office_id']] = [
                    'in' => $row['in'],
                    'mult' => $row['mult'],
                    'cfsin' => $row['cfsin'],
                    'lfsin' => $row['lfsin'],
                    'afsin' => $row['afsin'],
                    'cfsout' => $row['cfsout'],
                    'lfsout' => $row['lfsout'],
                    'afsout' => $row['afsout'],
                    'currency' => $row['code'],
                    'out' => $row['out'],
                    'win' => $row['win'],
                    'count' => $row['count'],
                    'rtp' => $row['in'] == 0 ? 0 : round($row['out'] / $row['in'] * 100, 2),
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                    'newusers' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                    'jp' => $row['jp'],
                    'promoout' => $row['promoout'],
                    'promocnt' => $row['promocnt']
                ];
            }

            if (isset($totalOffice[$var])) {
                $totalOffice[$var]['in'] += round($row['in'], $row['mult']);
                $totalOffice[$var]['mult'] = $row['mult'] > $totalOffice[$var]['mult'] ? $row['mult'] : $totalOffice[$var]['mult'];
                $totalOffice[$var]['cfsin'] += $row['cfsin'];
                $totalOffice[$var]['lfsin'] += $row['lfsin'];
                $totalOffice[$var]['afsin'] += $row['afsin'];
                $totalOffice[$var]['cfsout'] += $row['cfsout'];
                $totalOffice[$var]['lfsout'] += $row['lfsout'];
                $totalOffice[$var]['afsout'] += $row['afsout'];
                $totalOffice[$var]['out'] += round($row['out'], $row['mult']);
                $totalOffice[$var]['win'] += round($row['win'], $row['mult']);
                $totalOffice[$var]['count'] += $row['count'];
                $totalOffice[$var]['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';
                $totalOffice[$var]['newusers'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';
                $totalOffice[$var]['jp'] += $row['jp'];
                $totalOffice[$var]['promoout'] += $row['promoout'];
                $totalOffice[$var]['promocnt'] += $row['promocnt'];
                $totalOffice[$var]['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';


            } else {
                $totalOffice[$var] = [
                    'in' => round($row['in'], $row['mult']),
                    'mult' => $row['mult'],
                    'cfsin' => $row['cfsin'],
                    'lfsin' => $row['lfsin'],
                    'afsin' => $row['afsin'],
                    'cfsout' => $row['cfsout'],
                    'lfsout' => $row['lfsout'],
                    'afsout' => $row['afsout'],
                    'out' => round($row['out'], $row['mult']),
                    'win' => round($row['win'], $row['mult']),
                    'count' => $row['count'],
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                    'newusers' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                    'jp' => $row['jp'],
                    'promoout' => $row['promoout'],
                    'promocnt' => $row['promocnt'],
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0'
                ];
            }

            if (isset($total['offices'][$row['office_id']])) {
                $total['offices'][$row['office_id']]['in'] += $row['in'];
                $total['offices'][$row['office_id']]['mult'] = $row['mult'];
                $total['offices'][$row['office_id']]['cfsin'] += $row['cfsin'];
                $total['offices'][$row['office_id']]['lfsin'] += $row['lfsin'];
                $total['offices'][$row['office_id']]['afsin'] += $row['afsin'];
                $total['offices'][$row['office_id']]['cfsout'] += $row['cfsout'];
                $total['offices'][$row['office_id']]['lfsout'] += $row['lfsout'];
                $total['offices'][$row['office_id']]['afsout'] += $row['afsout'];
                $total['offices'][$row['office_id']]['out'] += $row['out'];
                $total['offices'][$row['office_id']]['win'] += $row['win'];
                $total['offices'][$row['office_id']]['count'] += $row['count'];
                $total['offices'][$row['office_id']]['newusers'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';
                $total['offices'][$row['office_id']]['jp'] += $row['jp'];
                $total['offices'][$row['office_id']]['promoout'] += $row['promoout'];
                $total['offices'][$row['office_id']]['promocnt'] += $row['promocnt'];
                $total['offices'][$row['office_id']]['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';

            } else {
                $total['offices'][$row['office_id']] = ['in' => $row['in'],
                    'mult' => $row['mult'],
                    'cfsin' => $row['cfsin'],
                    'lfsin' => $row['lfsin'],
                    'afsin' => $row['afsin'],
                    'cfsout' => $row['cfsout'],
                    'lfsout' => $row['lfsout'],
                    'afsout' => $row['afsout'],
                    'out' => $row['out'],
                    'win' => $row['win'],
                    'count' => $row['count'],
                    'currency' => $row['code'],
                    'newusers' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                    'jp' => $row['jp'],
                    'promoout' => $row['promoout'],
                    'promocnt' => $row['promocnt'],
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0'
                ];
            }

            ksort($total['offices']);

            if (isset($total['currencies'][$row['code']])) {
                $total['currencies'][$row['code']]['in'] += $row['in'];
                $total['currencies'][$row['code']]['mult'] = $row['mult'];
                $total['currencies'][$row['code']]['cfsin'] += $row['cfsin'];
                $total['currencies'][$row['code']]['lfsin'] += $row['lfsin'];
                $total['currencies'][$row['code']]['afsin'] += $row['afsin'];
                $total['currencies'][$row['code']]['cfsout'] += $row['cfsout'];
                $total['currencies'][$row['code']]['lfsout'] += $row['lfsout'];
                $total['currencies'][$row['code']]['afsout'] += $row['afsout'];
                $total['currencies'][$row['code']]['out'] += $row['out'];
                $total['currencies'][$row['code']]['win'] += $row['win'];
                $total['currencies'][$row['code']]['count'] += $row['count'];
                $total['currencies'][$row['code']]['newusers'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';
                $total['currencies'][$row['code']]['jp'] += $row['jp'];
                $total['currencies'][$row['code']]['promoout'] += $row['promoout'];
                $total['currencies'][$row['code']]['promocnt'] += $row['promocnt'];
                $total['currencies'][$row['code']]['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';


            } else {
                $total['currencies'][$row['code']] = ['in' => $row['in'],
                    'mult' => $row['mult'],
                    'cfsin' => $row['cfsin'],
                    'lfsin' => $row['lfsin'],
                    'afsin' => $row['afsin'],
                    'cfsout' => $row['cfsout'],
                    'lfsout' => $row['lfsout'],
                    'afsout' => $row['afsout'],
                    'out' => $row['out'],
                    'win' => $row['win'],
                    'count' => $row['count'],
                    'currency' => $row['code'],
                    'newusers' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                    'jp' => $row['jp'],
                    'promoout' => $row['promoout'],
                    'promocnt' => $row['promocnt'],
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0'
                ];
            }

            ksort($total['currencies']);

            $total['in'] += $row['in'];
            $total['cfsin'] += $row['cfsin'];
            $total['lfsin'] += $row['lfsin'];
            $total['afsin'] += $row['afsin'];
            $total['cfsout'] += $row['cfsout'];
            $total['lfsout'] += $row['lfsout'];
            $total['afsout'] += $row['afsout'];
            $total['out'] += $row['out'];
            $total['win'] += $row['win'];
            $total['count'] += $row['count'];
            $total['rtp'] = $total['in'] == 0 ? '&nbsp;' : round($total['out'] / $total['in'] * 100, 2);
            $total['newusers'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';
            $total['jp'] += $row['jp'];
            $total['promoout'] += $row['promoout'];
            $total['promocnt'] += $row['promocnt'];
            $total['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';
        }

        if (arr::get($_GET, 'chart')) {
            $view = new View('admin1/report/chart');
            $view->data = $data;
            $this->response->body($view->render());
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


        $view = new View('admin1/report/index');
        $view->time_from = $time_from;
        $view->time_to = $time_to;
        $view->office_id = $office_id;
        $view->owner = $owner;
        $view->is_test = $is_test;
        $view->isTestUser = $isTestUser;
        $view->convert = $convert;
        $view->only_total = $only_total;
        $view->by_month = $by_month;
        $view->officesList = $officesList;
        $view->data = $data;
        $view->total = $total;
        $view->totalOffice = $totalOffice;
        $view->owners = Person::user()->owners([-1=>'All']);
        $view->owner_offices = $owner_offices;
        $this->template->content = $view;

    }


}
