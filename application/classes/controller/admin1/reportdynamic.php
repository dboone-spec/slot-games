<?php

class Controller_Admin1_Reportdynamic extends Controller_Admin1_Base
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
        if (!isset($time_to)) {
            $time_to = date("Y-m-d");
        }

        //конвертация
        $c = '1';
        if ($convert = arr::get($_GET, 'convert')) {
            $c = 'c.val';
        }

        $sql_games = "Select date,s.office_id,c.code, 
            sum(bets_count) as count,
          
            sum(
                case when bettype = 'bet' then avgbet*$c else 0 end
            ) as avg,
  
            sum(
                case when bettype = 'ls1' then users_count else 0 end
            ) as ls1,
  
            sum(
                case when bettype = 'ls2' then users_count else 0 end
            ) as ls2,
  
            sum(
                case when bettype = 'ls1' or bettype = 'ls2' then users_count else 0 end
            ) as lsall,
   
            sum(
                case when bettype = 'cs' then users_count else 0 end
            ) as ds,
  
            sum(
                case when bettype = 'ls1' or bettype = 'ls2' then sumamount*$c else 0 end
            ) as ls_in,
    
            sum(
                case when bettype = 'ls1' or bettype = 'ls2' then sumwin*$c else 0 end
            ) as ls_out,
    
            sum(
                case when bettype = 'cs' then sumamount*$c else 0 end
            ) as ds_in,
    
            sum(
                case when bettype = 'cs' then sumwin*$c else 0 end
            ) as ds_out,
    
            sum(
                case when bettype = 'jp' then sumwin*$c else 0 end
            ) as jp_out,
  
            sum(
                case when bettype = 'jp' then bets_count*$c else 0 end
            ) as jp_count,
  
            c.mult
        From statistics_dynamics s
        join offices o on o.id=s.office_id
        join currencies c on o.currency_id=c.id
        Where
            date >= :time_from
            AND date <= :time_to";


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


        $data = [];
        $totalOffice = [];
        $total = [
            'users' => 0,
            'count' => 0,
            'avg' => 0,

            'ls1' => 0,
            'ls2' => 0,
            'lsall' => 0,

            'ds' => 0,

            'ls_in' => 0,
            'ls_out' => 0,
            'ds_in' => 0,
            'ds_out' => 0,
            'jp_count' => 0,
            'jp_out' => 0,
			'offices' => [],
        ];

        foreach ($statistic as $row) {

            $var = $row['date'];
            if ($by_month) {
                $var = date('Y-m', strtotime($row['date']));
            }

            if (isset($data[$var][$row['office_id']])) {
                $data[$var][$row['office_id']]['count'] += $row['count'];
                $data[$var][$row['office_id']]['avg'] += $row['avg'];
                $data[$var][$row['office_id']]['ls1'] += $row['ls1'];
                $data[$var][$row['office_id']]['ls2'] += $row['ls2'];
                $data[$var][$row['office_id']]['lsall'] += $row['lsall'];
                $data[$var][$row['office_id']]['ds'] += $row['ds'];
                $data[$var][$row['office_id']]['ls_in'] += $row['ls_in'];
                $data[$var][$row['office_id']]['ls_out'] += $row['ls_out'];
                $data[$var][$row['office_id']]['ds_in'] += $row['ds_in'];
                $data[$var][$row['office_id']]['ds_out'] += $row['ds_out'];
                $data[$var][$row['office_id']]['jp_count'] += $row['jp_count'];
                $data[$var][$row['office_id']]['jp_out'] += $row['jp_out'];
            } else {
                $data[$var][$row['office_id']] = [
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                    'count' => $row['count'],
                    'avg' => $row['avg'],
                    'ls1' => $row['ls1'],
                    'ls2' => $row['ls2'],
                    'lsall' => $row['lsall'],
                    'ds' => $row['ds'],
                    'ls_in' => $row['ls_in'],
                    'ls_out' => $row['ls_out'],
                    'ds_in' => $row['ds_in'],
                    'ds_out' => $row['ds_out'],
                    'jp_count' => $row['jp_count'],
                    'jp_out' => $row['jp_out'],
                ];
            }

            if (isset($totalOffice[$var])) {
                $totalOffice[$var]['count'] += $row['count'];
                $totalOffice[$var]['avg'] += $row['avg'];
                $totalOffice[$var]['ls1'] += $row['ls1'];
                $totalOffice[$var]['ls2'] += $row['ls2'];
                $totalOffice[$var]['lsall'] += $row['lsall'];
                $totalOffice[$var]['ds'] += $row['ds'];
                $totalOffice[$var]['ls_in'] += $row['ls_in'];
                $totalOffice[$var]['ls_out'] += $row['ls_out'];
                $totalOffice[$var]['ds_in'] += $row['ds_in'];
                $totalOffice[$var]['ds_out'] += $row['ds_out'];
                $totalOffice[$var]['jp_count'] += $row['jp_count'];
                $totalOffice[$var]['jp_out'] += $row['jp_out'];
            } else {
                $totalOffice[$var] = [
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                    'count' => $row['count'],
                    'avg' => $row['avg'],
                    'ls1' => $row['ls1'],
                    'ls2' => $row['ls2'],
                    'lsall' => $row['lsall'],
                    'ds' => $row['ds'],
                    'ls_in' => $row['ls_in'],
                    'ls_out' => $row['ls_out'],
                    'ds_in' => $row['ds_in'],
                    'ds_out' => $row['ds_out'],
                    'jp_count' => $row['jp_count'],
                    'jp_out' => $row['jp_out'],
                ];
            }

            if (isset($total['offices'][$row['office_id']])) {
                $total['offices'][$row['office_id']]['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';
                $total['offices'][$row['office_id']]['count'] += $row['count'];
                $total['offices'][$row['office_id']]['avg'] += $row['avg'];
                $total['offices'][$row['office_id']]['ls1'] += $row['ls1'];
                $total['offices'][$row['office_id']]['ls2'] += $row['ls2'];
                $total['offices'][$row['office_id']]['lsall'] += $row['lsall'];
                $total['offices'][$row['office_id']]['ds'] += $row['ds'];
                $total['offices'][$row['office_id']]['ls_in'] += $row['ls_in'];
                $total['offices'][$row['office_id']]['ls_out'] += $row['ls_out'];
                $total['offices'][$row['office_id']]['ds_in'] += $row['ds_in'];
                $total['offices'][$row['office_id']]['ds_out'] += $row['ds_out'];
                $total['offices'][$row['office_id']]['jp_count'] += $row['jp_count'];
                $total['offices'][$row['office_id']]['jp_out'] += $row['jp_out'];
                $total['offices'][$row['office_id']]['rows'] ++;

            } else {
                $total['offices'][$row['office_id']] = [
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                    'count' => $row['count'],
                    'avg' => $row['avg'],
                    'ls1' => $row['ls1'],
                    'ls2' => $row['ls2'],
                    'lsall' => $row['lsall'],
                    'ds' => $row['ds'],
                    'ls_in' => $row['ls_in'],
                    'ls_out' => $row['ls_out'],
                    'ds_in' => $row['ds_in'],
                    'ds_out' => $row['ds_out'],
                    'jp_count' => $row['jp_count'],
                    'jp_out' => $row['jp_out'],
                    'rows' => 1,
                ];
            }

            ksort($total['offices']);

            if (isset($total['currencies'][$row['code']])) {
                $total['currencies'][$row['code']]['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';
                $total['currencies'][$row['code']]['count'] += $row['count'];
                $total['currencies'][$row['code']]['avg'] += $row['avg'];
                $total['currencies'][$row['code']]['ls1'] += $row['ls1'];
                $total['currencies'][$row['code']]['ls2'] += $row['ls2'];
                $total['currencies'][$row['code']]['lsall'] += $row['lsall'];
                $total['currencies'][$row['code']]['ds'] += $row['ds'];
                $total['currencies'][$row['code']]['ls_in'] += $row['ls_in'];
                $total['currencies'][$row['code']]['ls_out'] += $row['ls_out'];
                $total['currencies'][$row['code']]['ds_in'] += $row['ds_in'];
                $total['currencies'][$row['code']]['ds_out'] += $row['ds_out'];
                $total['currencies'][$row['code']]['jp_count'] += $row['jp_count'];
                $total['currencies'][$row['code']]['jp_out'] += $row['jp_out'];
            } else {
                $total['currencies'][$row['code']] = [
                    'users' => $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                    'count' => $row['count'],
                    'avg' => $row['avg'],
                    'ls1' => $row['ls1'],
                    'ls2' => $row['ls2'],
                    'lsall' => $row['lsall'],
                    'ds' => $row['ds'],
                    'ls_in' => $row['ls_in'],
                    'ls_out' => $row['ls_out'],
                    'ds_in' => $row['ds_in'],
                    'ds_out' => $row['ds_out'],
                    'jp_count' => $row['jp_count'],
                    'jp_out' => $row['jp_out'],
                ];
            }

            ksort($total['currencies']);

            $total['users'] += $statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';
            $total['count'] += $row['count'];
            $total['avg'] += $row['avg'];
            $total['ls1'] += $row['ls1'];
            $total['ls2'] += $row['ls2'];
            $total['lsall'] += $row['lsall'];
            $total['ds'] += $row['ds'];
            $total['ls_in'] += $row['ls_in'];
            $total['ls_out'] += $row['ls_out'];
            $total['ds_in'] += $row['ds_in'];
            $total['ds_out'] += $row['ds_out'];
            $total['jp_count'] += $row['jp_count'];
            $total['jp_out'] += $row['jp_out'];
        }

        foreach($total['offices'] as $key=>$val){
            $total['offices'][$key]['avg']=$total['offices'][$key]['avg']/$total['offices'][$key]['rows'];
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

        $view = new View('admin1/report/dynamic');
        $view->time_from = $time_from;
        $view->time_to = $time_to;
        $view->office_id = $office_id;
        $view->owner = $owner;
        $view->is_test = $is_test;
        $view->convert = $convert;
        $view->only_total = $only_total;
        $view->by_month = $by_month;
        $view->officesList = $officesList;
        $view->data = $data;
        $view->total = $total;
        $view->totalOffice = $totalOffice;
        $view->owners = $owners;
        $view->owner_offices = $owner_offices;
        $this->template->content = $view;

    }


}
