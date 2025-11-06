<?php

class Controller_Admin1_Index extends Controller_Admin1_Base
{

    protected function _userstats($offices=[]) {
        $sql_users="select date||'::'||office_id as id,users,newusers
                from statistic_users s
                where date >= :time_from
                        AND date <= :time_to
                        AND s.office_id in :officesEnabled ";

        if(!empty($offices)) {
            $sql_users.='and s.office_id in :offices';
        }

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
            ':officesEnabled'=>Person::user()->offices(),
            ':offices'=>$offices,
            ':oid'=>$office_id
        ])->execute()->as_array('id');
    }

    protected function _gamestats($offices=[])
    {
        //for month
        $brand = 'agt';


        $sql_games = "Select game, visible_name, sum(count) as count
                From statistics
                join games on games.name=statistics.game
                Where
                    date >= :time_from
                    AND date <= :time_to and statistics.bettype in :types";


        if($brand != 'all')
        {
            $sql_games .= ' and statistics.type=:brand';
        }

        $sql_games .= " AND office_id in :officesEnabled ";

        if(!empty($offices)) {
            $sql_games.='and office_id in :offices';
        }

        $sql_games .= ' GROUP BY 1,2';


        $statistic = db::query(1,$sql_games)->parameters([
                        ':time_from' => date("Y-m-d",strtotime("-1 months")),
                        ':time_to' => date("Y-m-d"),
                        ':types' => [
                                'normal',//normal
                                'norafs',//normal from freespin from api
                                'norcfs',//normal from freespin from cashback
                                'double',//double
                                'douafs',//double from freespin from api
                                'doucfs',//double from freespin from cashback
                                'free',//free
                                'freafs',//free from freespin from api
                                'frecfs',//free from freespin from cashback
                        ],
                        ':officesEnabled' => Person::user()->offices(),
                        ':offices' => $offices,
                        ':brand' => $brand
                ])->execute()->as_array();

//        $total_count = array_sum(arr::pluck($statistic,'count'));
//
//        foreach($statistic as &$s) {
//            $s['from_total'] = th::number_format(100*$s['count']/$total_count);
//        }

        usort($statistic,function ($a,$b) {
            return $a['count'] <= $b['count'];
        });

//        array_slice($statistic,0,5);

        return $statistic;
    }

    protected function _ownerstats($owner=0)
    {
        //for month
        $brand = 'agt';

        $sql_owners = "Select offices.owner,date,sum(count) as count
                From statistics
                join offices on offices.id=statistics.office_id
                Where
                    date >= :time_from
                    AND date <= :time_to and statistics.bettype in :types";


        if($brand != 'all')
        {
            $sql_owners .= ' and statistics.type=:brand';
        }

        $sql_owners .= " AND office_id in :officesEnabled ";

        if($owner>0) {
            $sql_owners .= " AND offices.owner = :owner ";
        }

        $sql_owners .= ' GROUP BY 1,2';


        $statistic = db::query(1,$sql_owners)->parameters([
                        ':time_from' => date("Y-m-d",strtotime("-1 months")),
                        ':time_to' => date("Y-m-d",strtotime("-1 day")),
                        ':types' => [
                                'normal',//normal
                                'norafs',//normal from freespin from api
                                'norcfs',//normal from freespin from cashback
                                'double',//double
                                'douafs',//double from freespin from api
                                'doucfs',//double from freespin from cashback
                                'free',//free
                                'freafs',//free from freespin from api
                                'frecfs',//free from freespin from cashback
                        ],
                        ':officesEnabled' => Person::user()->offices(),
                        ':brand' => $brand,
                        ':owner' => $owner,
                ])->execute()->as_array();

        $ownerstats = [
            'dates'=>[],
            'total_count'=>0,
            'data'=>[],
        ];

        foreach($statistic as $stats) {

            if(!isset($ownerstats['data'][$stats['owner']])) {
                $ownerstats['data'][$stats['owner']]=[];
            }

            if(!isset($ownerstats['data'][$stats['owner']][$stats['date']])) {
                $ownerstats['data'][$stats['owner']][$stats['date']]=0;
            }

            $ownerstats['data'][$stats['owner']][$stats['date']]+=$stats['count'];
        }

        return $ownerstats;
    }

    protected function _betstats($offices=[]) {



        $sql_betstats = "Select *
                From betstats
                Where
                    date >= :time_from
                    AND date <= :time_to";


        $sql_betstats .= " AND office_id in :officesEnabled";
        $sql_betstats .= " and bettype='normal' ";

        if(!empty($offices)) {
            $sql_betstats.='and office_id in :offices';
        }


        $sql_betstats .= ' ORDER BY date ';

        $statistic = db::query(1, $sql_betstats)->parameters([
            ':time_from' => date("Y-m-d",strtotime("-1 months")),
            ':time_to' => date("Y-m-d",strtotime("-1 day")),
            ':officesEnabled' => Person::user()->offices(),
            ':offices'=>$offices,
        ])->execute()->as_array();

        $not_use_keys = [
            'mozilla_mac',
            'opera_mac',
            'mozilla_ios',

            'safari_win',
            'other_os',

            'res0',
            'res1200',
            'res991',
            'res768',
            'res480',

            'res0v',
            'res1200v',
            'res991v',
            'res768v',
            'res480v',
        ];

        $betstats = [
                'dates'=>[],
                'total_count'=>0,
                'data'=>[],
        ];


        foreach($statistic as $stats) {
            foreach($stats as $sk=>$sv) {

                if($sk=='date' && !in_array($sv,$betstats['dates'])) {
                    $betstats['dates'][]=$sv;
                }

                if($sk=='total') {
                    $betstats['total_count']+=$sv;
                    continue;
                }

                if(in_array($sk,['date','office_id','bettype'])) {
                    continue;
                }

                if(in_array($sk,$not_use_keys)) {
                    continue;
                }

                if(!isset($betstats['data'][$sk][$stats['date']])) {
                    $betstats['data'][$sk][$stats['date']]=0;
                }

                $betstats['data'][$sk][$stats['date']]+=$sv;
            }
        }

        return $betstats;
    }

    public function action_index()
    {
        //недоSA и Дмитрий :)
        if (in_array(Person::$user_id, [1126, 1175, 1176,1179])) {
            $this->request->redirect($this->dir . '/promo');
        }

        if(in_array(person::$role,['cashier']))
        {
            $this->request->redirect($this->dir . '/cashusers');
        }

        if(in_array(person::$role,['promo','bet']))
        {
            $this->request->redirect($this->dir . '/promo');
        }

        if(!arr::get($_GET,'booga')) {
            $this->request->redirect($this->dir . '/report');
        }


        $menu = Kohana::$config->load('adminnavbar');

        $navBar=$menu['navBar'];

        foreach($navBar as $mainKey=>$linkGroup){

            foreach($linkGroup as $key=>$link){
                if(!in_array($key,$menu['personMenu'])){

                    unset($navBar[$mainKey][$key]);
                }
            }
        }


        $officesList=[-1=>'All']+Person::user()->officesName(null,true);

        $owners_sql = db::query(1,'select p.id,p.comment from persons p where comment is not null and comment !=\'\'')->execute()->as_array('id');
        $owner_offices_sql = db::query(1,'select id,owner from offices where owner is not null')->execute()->as_array('id');

        $owners = [-1=>'All'];
        $owner_offices = [];

        foreach($owners_sql as $s) {
            $owners[$s['id']]=$s['comment'];
        }

        ksort($owners);

        foreach($owner_offices_sql as $s) {
            $owner_offices[$s['id']]=arr::get($owners_sql,$s['owner'],['comment'=>''])['comment'];
        }

        $owner = arr::get($_GET,'owner',-1);

        $offices=[];

        if($owner>0) {
            $filtered=array_filter($owner_offices_sql,function($v) use($owner) {
                return $v['owner']==$owner;
            });
            $offices=Arr::pluck($filtered,'id');
        }

        $office_id = arr::get($_GET,'office_id',-1);

        if($office_id>0) {
            $offices=[$office_id];
        }

        $v = new View('admin1/index/index');

        $v->navBar = $navBar;
        $v->dir = $this->dir;

        $v->officesList=$officesList;
        $v->owners = $owners;
        $v->owner_offices = $owner_offices;

        $v->owner = $owner;
        $v->office_id = $office_id;

        $v->gamestats = $this->_gamestats($offices);

        $v->betstats = $this->_betstats($offices);
        $v->ownerstats = $this->_ownerstats($owner);


        $this->template->content = $v;
    }

}
