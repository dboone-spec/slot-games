<?php

class Controller_Admin_Statisticgame extends Controller_Admin_Base
{



public function action_index()
{


    $office_id = arr::get($_GET,'office_id',-1);
    $is_test = arr::get($_GET,'is_test','0')=='1';
    $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
    $time_to   = isset($_GET['time_to']) ? $_GET['time_to'] : null;
    $game=arr::get($_GET,'game','all');
    $brand=arr::get($_GET,'brand','all');

    if(!isset($time_from))
    {
        $time_from = date("Y-m-d",strtotime("-1 months"));
    }
    if(!isset($time_to))
    {
        $time_to = date("Y-m-d");
    }



    $sql_games ="Select date, game, sum(count) as count, sum(amount_in) as in, sum(amount_out) as out,
                sum(amount_in-amount_out) as win,
            CASE
                WHEN bettype = 'double' OR bettype = 'doucfs' OR bettype = 'douafs' THEN 'double'
                when bettype = 'normal' or bettype = 'free' or bettype = 'freafs' or bettype = 'frecfs' then 'normal'
                ELSE
                    bettype
            END AS bettype
        From statistics
        Where
            date >= :time_from
            AND date <= :time_to and statistics.bettype in :types";

    if ($game!='all'){
        $sql_games .=' and game_id=:game';
    }

    if ($brand!='all'){
        $sql_games .=' and type=:brand';
    }

    $sql_games .= " AND office_id in :officesEnabled ";

    if ($office_id!=-1){
        $sql_games .= " AND office_id =:oid ";
    }

    $sql_games .= ' GROUP BY date, game, bettype
        ORDER BY date,game ';


    $statistic = db::query(1, $sql_games)->parameters([
        ':time_from' => $time_from,
        ':time_to' => $time_to,
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
        ':office_id' => $office_id,
        ':officesEnabled'=>Person::user()->offices($is_test),
        ':oid'=>$office_id,
        ':game'=>$game,
        ':brand'=>$brand
    ])->execute()->as_array();


    $data=[];
    $total=[];


    foreach($statistic as $row){

       if(isset($data[$row['date']]['total'][$row['bettype']])) {
           $data[$row['date']]['total'][$row['bettype']]['in']+=$row['in'];
           $data[$row['date']]['total'][$row['bettype']]['out']+=$row['out'];
           $data[$row['date']]['total'][$row['bettype']]['count']+=$row['count'];
           $data[$row['date']]['total'][$row['bettype']]['win']+=$row['win'];
       }
       else {
           $data[$row['date']]['total'][$row['bettype']]['in']=$row['in'];
           $data[$row['date']]['total'][$row['bettype']]['out']=$row['out'];
           $data[$row['date']]['total'][$row['bettype']]['count']=$row['count'];
           $data[$row['date']]['total'][$row['bettype']]['win']=$row['win'];
       }

       if(isset($data[$row['date']][$row['game']][$row['bettype']])) {
           $data[$row['date']][$row['game']][$row['bettype']]['in']+=$row['in'];
           $data[$row['date']][$row['game']][$row['bettype']]['out']+=$row['out'];
           $data[$row['date']][$row['game']][$row['bettype']]['count']+=$row['count'];
           $data[$row['date']][$row['game']][$row['bettype']]['win']+=$row['win'];

       }
       else {
            $data[$row['date']][$row['game']][$row['bettype']]=[
                                                'in'=>$row['in'],
                                                'out'=>$row['out'],
                                                'count'=>$row['count'],
                                                'win'=>$row['win'],
                                            ];
       }

       if (isset($total[$row['bettype']])) {
            $total[$row['bettype']]['in']+=$row['in'];
            $total[$row['bettype']]['out']+=$row['out'];
            $total[$row['bettype']]['count']+=$row['count'];
            $total[$row['bettype']]['win']+=$row['win'];

       }
       else{
           $total[$row['bettype']]=['in'=>$row['in'],
                                    'out'=>$row['out'],
                                    'count'=>$row['count'],
                                    'win'=>$row['win'],
                                   ];
       }

    }


    foreach($data as &$date){
        foreach($date as $game=>&$d) {
            if (isset($d['normal'])){
                $d['normal']['rtp']=$d['normal']['in']==0? 0 : round($d['normal']['out']/$d['normal']['in']*100,2);
            }
            if (isset($d['double'])){
                $d['double']['rtp']=$d['double']['in']==0? 0 : round($d['double']['out']/$d['double']['in']*100,2);
            }
        }
    }

    foreach($total as $k=>&$v){
        $v['rtp']=$v['in']==0 ? '&nbsp;' : round($v['out']/$v['in']*100,2);
    }


    $sql='select distinct g.id,g.name,brand
            from office_games og
            join games g on g.id=og.game_id
            where office_id in :oid
            and og.enable=1
            and g.show=1
            order by g.name
            ';

    if(Person::$role=='sa') {
        $sql= "select g.id,g.name,brand
            from games g
            order by g.name";
    }

    $games= db::query(1, $sql)->param(':oid',Person::user()->offices())
                                          ->execute()
                                          ->as_array('id');
    $gamesList =['all'=>__('All')];
    $gameBrand=[];
    foreach ($games as $id=>$g){
        if ($brand!='all' and $brand==$g['brand']){
            $gamesList[$id]=$g['name'];
        }
        $gameBrand[$g['brand']][$g['id']]=$g['name'];
    }




    $officesList=[-1=>__('All')]+Person::user()->officesName(null,true);

    $brandList=['not'=>'Not select','agt'=>'AGT'];
    if (PROJECT==2){
        $brandList=['all'=>__('All'),'egt'=>'EGT','agt'=>'AGT', 'novomatic'=>'Novomatic', 'igrosoft'=>'Igrosoft' ];
        $gameBrand['egt']=$gameBrand['egt'] ?? [];
        $gameBrand['agt']=$gameBrand['agt'] ?? [];
        $gameBrand['novomatic']=$gameBrand['novomatic'] ?? [];
        $gameBrand['igrosoft']=$gameBrand['igrosoft'] ?? [];
    }

    $view=new View('admin/statisticgame/index');
    $view->time_from   = $time_from;
    $view->time_to     = $time_to;
    $view->office_id=$office_id;
    $view->officesList=$officesList;
    $view->data=$data;
    $view->is_test=$is_test;
    $view->games=$games;
    $view->gamesList=$gamesList;
    $view->brandList=$brandList;
    $view->total=$total;
    $view->game=$game;
    $view->brand=$brand;
    $view->gameBrand=$gameBrand;

    $this->template->content=$view;

}



}
