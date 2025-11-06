<?php

class Controller_Admin1_Statisticgame extends Controller_Admin1_Base
{


    protected $group=0;

    protected function group($date){

        if ($this->group == 1){
            list($y,$m,$d) = explode('-',$date);
            $date = mktime(0,0,0,$m,1,$y);
            return date('m-Y',$date);
        }

        return $date;

    }

    public function action_index()
    {


        $office_id = arr::get($_GET, 'office_id', -1);
        $owner = arr::get($_GET, 'owner', -1);
        $is_test = arr::get($_GET, 'is_test', '0') == '1';
        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : null;
        $game = arr::get($_GET, 'game', 'all');
        $this->group = arr::get($_GET, 'group', 0);




        if (!isset($time_from)) {
            $time_from = date("Y-m-d", strtotime("-1 day"));
        }
        if (!isset($time_to)) {
            $time_to = date("Y-m-d");
        }

        //конвертация
        $c = '1';
        if ($convert = arr::get($_GET, 'convert')) {
            $c = 'c.val';
        }

        $sql_games = "Select s.date, g.visible_name as game,s.office_id, sum(s.count) as count, sum(s.amount_in*$c) as in, sum(s.amount_out*$c) as out,
                sum((s.amount_in-s.amount_out)*$c) as win,
            CASE
                WHEN bettype = 'double' OR bettype = 'doucfs' OR bettype = 'douafs' THEN 'double'
                when bettype = 'normal' or bettype = 'free' or bettype = 'freafs' or bettype = 'frecfs' then 'normal'
                ELSE
                    bettype
            END AS bettype,
            c.mult
        From statistics s
        join games g on g.id=s.game_id
        join offices o on o.id=s.office_id
        join currencies c on o.currency_id=c.id
        Where
            s.date >= :time_from
            and s.test = 0
            AND s.date <= :time_to and s.bettype in :types";

        if ($game != 'all') {
            $sql_games .= ' and s.game_id=:game';
        }

        $sql_games .= " AND s.office_id in :officesEnabled ";

        if ($office_id != -1) {
            $sql_games .= " AND s.office_id =:oid ";
        }

        if ($owner != -1) {
            $sql_games .= " AND o.owner =:ownerid ";
        }

        $sql_games .= ' GROUP BY 1,2,s.office_id, bettype,c.code, c.mult
        ORDER BY 1,2';


        $statistic = db::query(1, $sql_games)->parameters([
            ':time_from' => $time_from,
            ':time_to' => $time_to,
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
            ':office_id' => $office_id,
            ':officesEnabled' => Person::user()->offices($is_test),
            ':oid' => $office_id,
            ':game' => $game,
        ])->execute()->as_array();


        $data = [];
        $total = [];


        foreach ($statistic as $row) {

            if($game!='all') {
                $row['game']=$row['office_id'];
            }

            $date = $this->group($row['date']);

            if (isset($data[$date]['total'][$row['bettype']])) {
                $data[$date]['total'][$row['bettype']]['in'] += $row['in'];
                $data[$date]['total'][$row['bettype']]['out'] += $row['out'];
                $data[$date]['total'][$row['bettype']]['count'] += $row['count'];
                $data[$date]['total'][$row['bettype']]['win'] += $row['win'];
            } else {
                $data[$date]['total'][$row['bettype']]['in'] = $row['in'];
                $data[$date]['total'][$row['bettype']]['out'] = $row['out'];
                $data[$date]['total'][$row['bettype']]['count'] = $row['count'];
                $data[$date]['total'][$row['bettype']]['win'] = $row['win'];
            }

            if (isset($data[$date][$row['game']][$row['bettype']])) {
                $data[$date][$row['game']][$row['bettype']]['in'] += $row['in'];
                $data[$date][$row['game']][$row['bettype']]['out'] += $row['out'];
                $data[$date][$row['game']][$row['bettype']]['count'] += $row['count'];
                $data[$date][$row['game']][$row['bettype']]['win'] += $row['win'];

            } else {
                $data[$date][$row['game']][$row['bettype']] = [
                    'in' => $row['in'],
                    'out' => $row['out'],
                    'count' => $row['count'],
                    'win' => $row['win'],
                ];
            }

            if (isset($total[$row['bettype']])) {
                $total[$row['bettype']]['in'] += $row['in'];
                $total[$row['bettype']]['out'] += $row['out'];
                $total[$row['bettype']]['count'] += $row['count'];
                $total[$row['bettype']]['win'] += $row['win'];

            } else {
                $total[$row['bettype']] = ['in' => $row['in'],
                    'out' => $row['out'],
                    'count' => $row['count'],
                    'win' => $row['win'],
                ];
            }

        }


        foreach ($data as &$date) {
            foreach ($date as $name => &$d) {
                if (isset($d['normal'])) {
                    $d['normal']['rtp'] = $d['normal']['in'] == 0 ? 0 : round($d['normal']['out'] / $d['normal']['in'] * 100, 2);
                }
                if (isset($d['double'])) {
                    $d['double']['rtp'] = $d['double']['in'] == 0 ? 0 : round($d['double']['out'] / $d['double']['in'] * 100, 2);
                }
            }
        }

        //old
        foreach($total as $k=>&$v){
            $v['rtp']=$v['in']==0 ? '&nbsp;' : round($v['out']/$v['in']*100,2);
        }


        $sql = 'select distinct g.id,g.name,g.visible_name
            from office_games og
            join games g on g.id=og.game_id
            where office_id in :oid
            and og.enable=1
            and g.show=1
            order by g.visible_name
            ';

        if (Person::$role == 'sa') {
            $sql = "select g.id,g.name,brand,g.visible_name
            from games g
            order by g.visible_name";
        }

        $games = db::query(1, $sql)->param(':oid', Person::user()->offices())
            ->execute()
            ->as_array('id');
        $gamesList = ['all' => __('All')];

        foreach ($games as $id => $g) {
            $gamesList[$id] = $g['visible_name'];
        }


        $officesList = [-1 => __('All')] + Person::user()->officesName(null, true);

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

        if(Person::$user_id==1149) {
            $owners=[
                -1 => 'All',
                1128=>'Olimp',
                1142=>'BMP',
            ];

            foreach ($owner_offices as $id=>$s) {
                if(!isset($owners[$id])) {
                    unset($owner_offices[$id]);
                }
            }
        }

        $view = new View('admin1/statisticgame/index');
        $view->time_from = $time_from;
        $view->time_to = $time_to;
        $view->office_id = $office_id;
        $view->owner = $owner;
        $view->owners = $owners;
        $view->owner_offices = $owner_offices;
        $view->officesList = $officesList;
        $view->data = $data;
        $view->convert=$convert;
        $view->is_test = $is_test;
        $view->games = $games;
        $view->group = $this->group;
        $view->gamesList = $gamesList;

        $view->total = $total;
        $view->game = $game;

        $this->template->content = $view;

    }


}
