<?php

class Controller_Admin_Report extends Controller_Admin_Base
{


public function action_index()
{


    $office_id = arr::get($_GET,'office_id',-1);
    $owner = arr::get($_GET,'owner',-1);
    $is_test = arr::get($_GET,'is_test',false);
    $only_total = arr::get($_GET,'only_total',false);
    $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
    $time_to   = isset($_GET['time_to']) ? $_GET['time_to'] : null;
    if(!isset($time_from))
    {
        $time_from = date("Y-m-d",strtotime("-1 months"));
    }
    if(!isset($time_to))
    {
        $time_to = date("Y-m-d");
    }

    //конвертация
    $c = '1';
    if($convert = arr::get($_GET,'convert')) {
        $c='c.val';
    }

    $sql_games ="Select date,s.office_id,c.code, sum(count) as count,
            sum(
                case when bettype = 'norafs' or bettype = 'norcfs' then 0 else amount_in*$c end
            ) as in,
            sum(amount_out*$c) as out,
            sum((case when bettype = 'norafs' or bettype = 'norcfs' then 0 else amount_in*$c end)*$c-amount_out*$c) as win,
            sum(
                case when bettype = 'norcfs' then amount_in*$c else 0 end
            ) as cfsin,
            sum(
                case when bettype = 'norafs' then amount_in*$c else 0 end
            ) as afsin
        From statistics s
        join offices o on o.id=s.office_id
        join currencies c on o.currency_id=c.id
        Where
            date >= :time_from
            AND date <= :time_to
            AND bettype in :types";



    $sql_games .= " AND s.office_id in :officesEnabled ";

    $sql_users="select date||'::'||office_id as id,users,newusers
                from statistic_users s
                where date >= :time_from
                        AND date <= :time_to
                        AND s.office_id in :officesEnabled ";


    if ($office_id!=-1){
        $sql_games .= " AND s.office_id =:oid ";
        $sql_users.= " AND s.office_id =:oid ";
    }

    if ($owner!=-1){
        $sql_games .= " AND o.owner =:ownerid ";
    }

    $sql_games .= ' GROUP BY date, s.office_id, c.code
        ORDER BY date, s.office_id ';


    $statistic = db::query(1, $sql_games)->parameters([
        ':time_from' => $time_from,
        ':time_to' => $time_to,
        ':office_id' => $office_id,
        ':ownerid' => $owner,
        ':types' => [
            'normal', //normal
            'norafs', //normal from freespin from api
            'norcfs', //normal from freespin from cashback

            'double', //double
            'douafs', //double from freespin from api
            'doucfs', //double from freespin from cashback

            'free',   //free
            'freafs', //free from freespin from api
            'frecfs', //free from freespin from cashback
        ],
        ':officesEnabled'=>Person::user()->offices($is_test),
        ':oid'=>$office_id
    ])->execute()->as_array();



    $statisticUsers=db::query(1, $sql_users)->parameters([
        ':time_from' => $time_from,
        ':time_to' => $time_to,
        ':office_id' => $office_id,
        ':ownerid' => $owner,
        ':types' => [
            'normal', //normal
            'norafs', //normal from freespin from api
            'norcfs', //normal from freespin from cashback

            'double', //double
            'douafs', //double from freespin from api
            'doucfs', //double from freespin from cashback

            'free',   //free
            'freafs', //free from freespin from api
            'frecfs', //free from freespin from cashback
        ],
        ':officesEnabled'=>Person::user()->offices($is_test),
        ':oid'=>$office_id
    ])->execute()->as_array('id');


    $data=[];
    $totalOffice=[];
    $total=['in'=>0,'cfsin'=>0,'afsin'=>0,'out'=>0,'count'=>0,'win'=>0, 'offices'=>[],'rtp'=>0, 'currencies'=>[],'newusers'=>0];


    foreach($statistic as $row){
            $data[$row['date']][$row['office_id']]=[
                                    'in'=>$row['in'],
                                    'cfsin'=>$row['cfsin'],
                                    'afsin'=>$row['afsin'],
                                    'currency'=>$row['code'],
                                    'out'=>$row['out'],
                                    'win'=>$row['win'],
                                    'count'=>$row['count'],
                                    'rtp'=>$row['in']==0? 0 : round($row['out']/$row['in']*100,2),
                                    'users'=>$statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                                    'newusers'=>$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                            ];

       if (isset($totalOffice[$row['date']])){
            $totalOffice[$row['date']]['in']+=$row['in'];
            $totalOffice[$row['date']]['cfsin']+=$row['cfsin'];
            $totalOffice[$row['date']]['afsin']+=$row['afsin'];
            $totalOffice[$row['date']]['out']+=$row['out'];
            $totalOffice[$row['date']]['win']+=$row['win'];
            $totalOffice[$row['date']]['count']+=$row['count'];
            $totalOffice[$row['date']]['users']+=$statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0';
            $totalOffice[$row['date']]['newusers']+=$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';


       }
       else{
           $totalOffice[$row['date']]=[
                    'in'=>$row['in'],
                    'cfsin'=>$row['cfsin'],
                    'afsin'=>$row['afsin'],
                    'out'=>$row['out'],
                    'win'=>$row['win'],
                    'count'=>$row['count'],
                    'users'=>$statisticUsers["{$row['date']}::{$row['office_id']}"]['users'] ?? '0',
                    'newusers'=>$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                ];
       }

       if (isset($total['offices'][$row['office_id']])){
            $total['offices'][$row['office_id']]['in']+=$row['in'];
            $total['offices'][$row['office_id']]['cfsin']+=$row['cfsin'];
            $total['offices'][$row['office_id']]['afsin']+=$row['afsin'];
            $total['offices'][$row['office_id']]['out']+=$row['out'];
            $total['offices'][$row['office_id']]['win']+=$row['win'];
            $total['offices'][$row['office_id']]['count']+=$row['count'];
            $total['offices'][$row['office_id']]['newusers']+=$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';


       }
       else{
           $total['offices'][$row['office_id']]=['in'=>$row['in'],
                                                'cfsin'=>$row['cfsin'],
                                                'afsin'=>$row['afsin'],
                                                'out'=>$row['out'],
                                                'win'=>$row['win'],
                                                'count'=>$row['count'],
                                                'currency'=>$row['code'],
                                                'newusers'=>$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                                                ];
       }

       ksort($total['offices']);

       if (isset($total['currencies'][$row['code']])){
            $total['currencies'][$row['code']]['in']+=$row['in'];
            $total['currencies'][$row['code']]['cfsin']+=$row['cfsin'];
            $total['currencies'][$row['code']]['afsin']+=$row['afsin'];
            $total['currencies'][$row['code']]['out']+=$row['out'];
            $total['currencies'][$row['code']]['win']+=$row['win'];
            $total['currencies'][$row['code']]['count']+=$row['count'];
            $total['currencies'][$row['code']]['newusers']+=$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';



       }
       else{
           $total['currencies'][$row['code']]=['in'=>$row['in'],
                                                'cfsin'=>$row['cfsin'],
                                                'afsin'=>$row['afsin'],
                                                'out'=>$row['out'],
                                                'win'=>$row['win'],
                                                'count'=>$row['count'],
                                                'currency'=>$row['code'],
                                                'newusers'=>$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0',
                                                ];
       }

       ksort($total['currencies']);

       $total['in']+=$row['in'];
       $total['cfsin']+=$row['cfsin'];
       $total['afsin']+=$row['afsin'];
       $total['out']+=$row['out'];
       $total['win']+=$row['win'];
       $total['count']+=$row['count'];
       $total['rtp']=$total['in']==0 ? '&nbsp;' : round($total['out']/$total['in']*100,2);
       $total['newusers']+=$statisticUsers["{$row['date']}::{$row['office_id']}"]['newusers'] ?? '0';
    }




    foreach($totalOffice as $key=>$val){
        $totalOffice[$key]['rtp']=  $totalOffice[$key]['in']==0 ? '&nbsp;' : round($totalOffice[$key]['out']/$totalOffice[$key]['in']*100,2);

    }

    foreach($total['offices'] as $key=>$val){
        $total['offices'][$key]['rtp']=  $total['offices'][$key]['in']==0 ? '&nbsp;' : round($total['offices'][$key]['out']/$total['offices'][$key]['in']*100,2);

    }



    $officesList=[-1=>'All']+Person::user()->officesName(null,true);

    $owners_sql = db::query(1,'select p.id,p.comment from persons p where comment is not null and comment !=\'\'')->execute()->as_array('id');
    $owner_offices_sql = db::query(1,'select id,owner from offices where owner is not null')->execute()->as_array('id');

    $owners = [-1=>'All'];
    $owner_offices = [];

    foreach($owners_sql as $s) {
        $owners[$s['id']]=$s['comment'];
    }

    foreach($owner_offices_sql as $s) {
        $owner_offices[$s['id']]=arr::get($owners_sql,$s['owner'],['comment'=>''])['comment'];
    }


    $view=new View('admin/report/index');
    $view->time_from   = $time_from;
    $view->time_to     = $time_to;
    $view->office_id=$office_id;
    $view->owner=$owner;
    $view->is_test=$is_test;
    $view->convert=$convert;
    $view->only_total=$only_total;
    $view->officesList=$officesList;
    $view->data=$data;
    $view->total=$total;
    $view->totalOffice=$totalOffice;
    $view->owners = $owners;
    $view->owner_offices = $owner_offices;
    $this->template->content=$view;

}



}
