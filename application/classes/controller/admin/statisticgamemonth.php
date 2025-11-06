<?php

class Controller_Admin_Statisticgamemonth extends Controller_Admin_Base
{



public function action_index()
{


    $office_id = arr::get($_GET,'office_id',-1);
    $currency=false;
    if ($office_id!=-1){
        $o=new Model_Office($office_id);
        $currency=new Model_Currency($o->currency_id);
    }
    $is_test = arr::get($_GET,'is_test','0')=='1';
    $month=arr::get($_GET,'month',date('m'));
    //$year=arr::get($_GET,'year',date('Y'));
    $year=2020;
    $game=arr::get($_GET,'game','all');
    $brand=arr::get($_GET,'brand','all');
    $partnerId=arr::get($_GET,'parnerId','all');
    $comma= arr::get($_GET,'comma',false)=='1';
    
    
    $time_from=date('Y-m-d', mktime(0,0,0, $month,1, $year));
    $nextMonth=$month+1;
    $nextYear=$year;
    
    if ($nextMonth>12){
        $nextMonth=1;
        $nextYear++;
    }
    
    
    $time_to=date('Y-m-d', mktime(0,0,0, $nextMonth,1, $nextYear));

    $sql_games ="Select g.visible_name as game,
            CASE
                WHEN bettype = 'double' THEN bettype
                ELSE
                    'normal'
            END AS bettype,
             sum(count) as count, sum(amount_in) as in, sum(amount_out) as out,
                sum(amount_in-amount_out) as win
        From statistics s
        join games g on s.game_id=g.id  
        join offices o on o.id=s.office_id and o.is_test=:isTest

        Where
            date >= :time_from
            AND date < :time_to and s.bettype in :types";
    
    
    $sqlOffice='select distinct office_id 
                    from statistics s
                    join games g on s.game_id=g.id  
                    join offices o on o.id=s.office_id and o.is_test=:isTest
                where date >= :time_from
            AND date < :time_to and s.bettype in :types ';

    if ($game!='all'){
        $sql_games .=' and game_id=:game';
        $sqlOffice.=' and game_id=:game';
    }

    if ($brand!='all'){
        $sql_games .=' and type=:brand';
        $sqlOffice.=' and type=:brand';
    }

    $sql_games .= " AND office_id in :officesEnabled ";
    $sqlOffice.=" AND office_id in :officesEnabled ";

    if ($office_id!=-1){
        $sql_games .= " AND office_id =:oid ";
        $sqlOffice.=" AND office_id =:oid ";
    }
    
    if ($partnerId!='all'){
        $sql_games .= ' and owner=:partner';
        $sqlOffice.=' and owner=:partner';
    }

    $sql_games .= ' GROUP BY 1,2
        ORDER BY game ';

    
    

    $statistic = db::query(1, $sql_games)->parameters([
        ':time_from' => $time_from,
        ':time_to' => $time_to,
        ':types' => ['normal','double','free'],
        ':office_id' => $office_id,
        ':officesEnabled'=>Person::user()->offices($is_test),
        ':oid'=>$office_id,
        ':game'=>$game,
        ':brand'=>$brand,
        ':partner'=>$partnerId,
        ':isTest'=>$is_test
    ])->execute()->as_array();

    
    $dataOffices=db::query(1, $sqlOffice)->parameters([
        ':time_from' => $time_from,
        ':time_to' => $time_to,
        ':types' => ['normal','double','free'],
        ':office_id' => $office_id,
        ':officesEnabled'=>Person::user()->offices($is_test),
        ':oid'=>$office_id,
        ':game'=>$game,
        ':brand'=>$brand,
        ':partner'=>$partnerId,
        ':isTest'=>$is_test
    ])->execute()->as_array();

    $offices=[];
    foreach($dataOffices as $o){
        $offices[]=$o['office_id'];
    }
    
    sort($offices);
    $data=[];
    $total=[];






    foreach($statistic as $row){

       if(isset($data[$row['game']][$row['bettype']])) {
           $data[$row['game']][$row['bettype']]['in']+=$row['in'];
           $data[$row['game']][$row['bettype']]['out']+=$row['out'];
           $data[$row['game']][$row['bettype']]['count']+=$row['count'];
           $data[$row['game']][$row['bettype']]['win']+=$row['win'];
       }
       else {
           $data[$row['game']][$row['bettype']]['in']=$row['in'];
           $data[$row['game']][$row['bettype']]['out']=$row['out'];
           $data[$row['game']][$row['bettype']]['count']=$row['count'];
           $data[$row['game']][$row['bettype']]['win']=$row['win'];
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

     foreach($data as &$d){
            if (isset($d['normal'])){
                $d['normal']['rtp']=$d['normal']['in']==0? 0 : round($d['normal']['out']/$d['normal']['in']*100,2);
                $d['normal']['avgbet']=$d['normal']['count']==0? '-' : round($d['normal']['in']/$d['normal']['count'],2);
            }
            if (isset($d['double'])){
                $d['double']['rtp']=$d['double']['in']==0? 0 : round($d['double']['out']/$d['double']['in']*100,2);
                $d['double']['avgbet']=$d['double']['count']==0? '&nbsp;' : round($d['double']['in']/$d['double']['count'],2);
            }
            if ($comma){
                if (isset($d['normal']['in'])){
                    $d['normal']['in']= str_replace('.',',',$d['normal']['in']);
                    $d['normal']['out']= str_replace('.',',',$d['normal']['out']);
                    $d['normal']['win']= str_replace('.',',',$d['normal']['win']);
                    $d['normal']['rtp']= str_replace('.',',',$d['normal']['rtp']);
                    $d['normal']['avgbet']= str_replace('.',',',$d['normal']['avgbet']);
                }
                    
                if (isset($d['double']['in'])){
                    $d['double']['in']= str_replace('.',',',$d['double']['in']);
                    $d['double']['out']= str_replace('.',',',$d['double']['out']);
                    $d['double']['win']= str_replace('.',',',$d['double']['win']);
                    $d['double']['rtp']= str_replace('.',',',$d['double']['rtp']);
                    $d['double']['avgbet']= str_replace('.',',',$d['double']['avgbet']);
                }
            }
            
    }

    
    foreach($total as $k=>&$v){
        $v['rtp']=$v['in']==0 ? '&nbsp;' : round($v['out']/$v['in']*100,2);
        $v['avgbet']=$v['count']==0 ? '&nbsp;' : round($v['in']/$v['count'],2);
        if ($comma){
            $v['in']= str_replace('.',',',$v['in']);
            $v['out']= str_replace('.',',',$v['out']);
            $v['win']= str_replace('.',',',$v['win']);
            $v['rtp']= str_replace('.',',',$v['rtp']);
            $v['avgbet']= str_replace('.',',',$v['avgbet']);
        }
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

    $brandList=['all'=>'Not select','agt'=>'AGT'];
    if (PROJECT==2){
        $brandList=['all'=>__('All'),'egt'=>'EGT','agt'=>'AGT', 'novomatic'=>'Novomatic', 'igrosoft'=>'Igrosoft' ];
        $gameBrand['egt']=$gameBrand['egt'] ?? [];
        $gameBrand['agt']=$gameBrand['agt'] ?? [];
        $gameBrand['novomatic']=$gameBrand['novomatic'] ?? [];
        $gameBrand['igrosoft']=$gameBrand['igrosoft'] ?? [];
    }
    
    $sql="select id,comment from persons where comment is not null and comment!='' order by comment";
    $partners=db::query(1,$sql)->execute()->as_array();
    $partnersList=['all'=>'All'];
    foreach($partners as $p){
        $partnersList[$p['id']]=$p['comment'];
    }

    $view=new View('admin/statisticgame/month');
    $view->month   = $month;
    $view->year     = $year;
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
    $view->currency=$currency;
    $view->partnersList=$partnersList;
    $view->partnerId=$partnerId;
    $view->comma=$comma;
    $view->offices=$offices;

    $this->template->content=$view;

}



}
