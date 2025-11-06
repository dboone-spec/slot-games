<?php

class Controller_Admin_Stats extends Super
{

    public $mark       = 'Статистика по играм'; //имя
    public $model_name = 'statistics'; //имя модели
    public $sh         = 'admin/stats'; //шаблон
    public $per_page   = 10;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function action_index()
    {
        $export     = isset($_GET['export']) ? $_GET['export'] : null;
        $office_id     = arr::get($_GET,'office_id',0);
        $game_type     = arr::get($_GET,'type',0);
        $game_provider = arr::get($_GET,'provider',0);
        $gameshow = arr::get($_GET,'gameshow',0);
        $game_brand = arr::get($_GET,'brand',0);
        $time_from     = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to       = isset($_GET['time_to']) ? $_GET['time_to'] : null;
        if(!isset($time_from))
        {
            $time_from = date("Y-m-d",strtotime("-1 months"));
        }
        if(!isset($time_to))
        {
            $time_to = date("Y-m-d");
        }

        $offices = $this->offices();
        

        $offices[0] = 'Все';
        $sql='SELECT
                s.game_id,
                --g.name||\' \'||left(g.provider, 1) as game,lower(g.brand) as brand,g.provider,
                bettype,
                SUM (amount_in) as amount_in,
                SUM (amount_out) as amount_out,
                SUM (amount_in - amount_out) AS win,
                SUM (count) as count
            FROM
                "statistics" s ';


            /*$sql .= <<<SQL
                JOIN games g ON s.game_id=g."id"
SQL;*/

        
        $sql .= <<<SQL
            WHERE s.date >= :time_from
            AND s.date <= :time_to
            AND s.office_id in :offices
            AND s.game_id is not null
SQL;
        
        $games_sql = 'select g.id as game_id,g.name||\' \'||left(g.provider, 1) as game,lower(g.brand) as brand,g.provider from games g where true';

        if(!$gameshow) {
            $games_sql.=' AND g.show<>0 ';
        }

        if($game_provider)
        {
            $games_sql .= <<<SQL
                AND g.provider=:provider
SQL;
        }

        if($game_brand)
        {
            $games_sql .= <<<SQL
                AND lower(g.brand)=:brand
SQL;
        }

        if($office_id)
        {
            $sql .= <<<SQL
                AND s.office_id = :office_id
SQL;
        }
        if($game_type)
        {
            $sql .= <<<SQL
                AND s.type = :type
SQL;
        }

        $sql.=' GROUP BY 1,2';
//        $sql.=' GROUP BY 1,2,3,4';
        
        $games_sql .= ' order by game';

        
        $params_games = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':office_id' => $office_id,
                ':type'      => $game_type,
                ':offices'      => array_keys($offices),
                ':provider'  => $game_provider,
                ':brand'  => strtolower($game_brand),
        ];

        $res = db::query(database::SELECT,$sql)->parameters($params_games)->execute()->as_array();
        
        $res_games = db::query(database::SELECT,$games_sql)->parameters($params_games)->execute('games')->as_array('game_id');
                
        
//        $games = arr::pluck($res, 'game');
        
        
        //++todo delete?
        $bettypes = arr::pluck($res, 'bettype');
        $bettypes = array_unique($bettypes);

        foreach($bettypes as $k=>$v) {
            if(!in_array($v,['free','bonus','normal','double'])) {
                unset($bettypes[$k]);
            }
        }
        //----

        $data=[];

        foreach($res_games as $id => $game) {
            $data[$id]=[
                'game' => $game['game'],
                'provider' => $game['provider'],
                'brand' => $game['brand'],
                'clean' => true,
                'normal_in'=>0,
                'normal_out'=>0,
                'normal_win'=>0,
                'normal_count'=>0,
                'fg_out'=>0,
//                'fg_win'=>0,
                'fg_count'=>0,
                'double_in'=>0,
                'double_out'=>0,
                'double_win'=>0,
                'double_count'=>0,
            ];
        }

        foreach($res as $row) {
            
            
            if(!isset($data[$row['game_id']])) {
                continue;
            }
            
            $data[$row['game_id']]['clean'] = false;
            
            $row['game']=$res_games[$row['game_id']]['game'];
            $row['provider']=$res_games[$row['game_id']]['provider'];
            $row['brand']=$res_games[$row['game_id']]['brand'];
            
            $data[$row['game_id']]['provider']=$row['provider'];
            $data[$row['game_id']]['brand']=$row['brand'];
            
            if(in_array($row['bettype'],['normal','bonus','free','unknow','bet','spin','Gamble','block','test'])){
                
//                if(!isset($data[$row['game_id']]['normal_in']))
                
                $data[$row['game_id']]['normal_in']+=$row['amount_in'];
                $data[$row['game_id']]['normal_out']+=$row['amount_out'];
                $data[$row['game_id']]['normal_win']+=$row['amount_in']-$row['amount_out'];
                $data[$row['game_id']]['normal_count']+=$row['count'];

//                $data[$row['game']]['fg_win']+=$row['amount_in'];

                if(in_array($row['bettype'],['free','bonus'])) {
                    $data[$row['game_id']]['fg_out']+=$row['amount_out'];
//                    $data[$row['game']]['fg_win']+=-$row['amount_out'];
                    $data[$row['game_id']]['fg_count']+=$row['count'];
                }
            }
            elseif(in_array($row['bettype'],['double'])) {
                $data[$row['game_id']]['double_in']+=$row['amount_in'];
                $data[$row['game_id']]['double_out']+=$row['amount_out'];
                $data[$row['game_id']]['double_win']+=$row['amount_in']-$row['amount_out'];
                $data[$row['game_id']]['double_count']+=$row['count'];
            }
        }

        $sumArray = array();

        foreach ($data as $k=>$subArray) {

            if($subArray['clean']==true) {
                unset($data[$k]);
                continue;
            }
            
            unset($data[$k]['clean']);
            
            foreach ($subArray as $id=>$value) {
                if(!isset($sumArray[$id])) {
                    $sumArray[$id]=0;
                }
                $sumArray[$id]+=$value;
            }
        }

        $data['Итого']=$sumArray;

        $csv='';
        if($export){
            $head=[];
            $head[]='game';
            $head[]='provider';
            $head[]='brand';

            $head[]='normal_in';
            $head[]='normal_out';
            $head[]='normal_win';
            $head[]='normal_count';
            $head[]='normal_z';

            $head[]='fg_out';
            $head[]='fg_count';
            $head[]='fg_z';

            $head[]='double_in';
            $head[]='double_out';
            $head[]='double_win';
            $head[]='double_count';
            $head[]='double_z';

            $body=[];
            foreach($data as $game => $row){
                if($game=='Итого') {
                    continue;
                }
                $body[]=implode(';',[
                    $game,
                    $row['provider'],
                    $row['brand'],

                    th::number_format($row['normal_in'] ?? '0',','),
                    th::number_format($row['normal_out'] ?? '0',','),
                    th::number_format($row['normal_win'] ?? '0',','),
                    th::number_format($row['normal_count'] ?? '0',','),
                    $row['normal_in'] > 0 ? th::number_format(100 * $row['normal_out'] / $row['normal_in'],',') : '0',

                    th::number_format($row['fg_out'] ?? '0',','),
                    th::number_format($row['fg_count'] ?? '0',','),
                    $row['normal_in'] > 0 ? th::number_format(100 * $row['fg_out'] / $row['normal_in'],',') : '0',

                    th::number_format($row['double_in'] ?? '0',','),
                    th::number_format($row['double_out'] ?? '0',','),
                    th::number_format($row['double_win'] ?? '0',','),
                    th::number_format($row['double_count'] ?? '0',','),
                    $row['double_in'] > 0 ? th::number_format(100 * $row['double_out'] / $row['double_in'],',') : '0',

                ]);
            }
            $csv=array([implode(';',$head)], $body);
            th::to_csv($csv, $time_from, $time_to, $this->request->controller());
        }

//        $offices     = [0 => 'Все'];
//
//        $sql_offices = <<<SQL
//            Select o.id as office_id, c.code
//            From offices o JOIN currencies c ON o.currency_id=c.id
//            where o.id in :offices order by 1
//SQL;
//        $res_offices = db::query(1,$sql_offices)->param(':offices',array_keys($this->offices()))->execute()->as_array('office_id');
//
//        foreach($res_offices as $off_id => $value)
//        {
//            $offices[$off_id] = $off_id.' ['.$value['code'].']';
//        }

        $providers     = [0 => 'Все'];
        $sql_providers = <<<SQL
            Select DISTINCT provider
            FROM games
SQL;
        $res_providers = db::query(1,$sql_providers)->execute('games')->as_array('provider');

        foreach($res_providers as $ga_t => $value)
        {
            $providers[$ga_t] = $value['provider'];
        }

        $brands     = [0 => 'Все'];
        $sql_brands = <<<SQL
            Select DISTINCT lower(brand) as brand
            FROM games
SQL;
        $res_brands = db::query(1,$sql_brands)->execute('games')->as_array('brand');

        foreach($res_brands as $ga_t => $value)
        {
            $brands[$ga_t] = $value['brand'];
        }

        $this->handler_search($_GET);

        $view            = new View($this->sh . '/index');
        $view->offices       = $offices;
        $view->curr_office   = $office_id;
        $view->curr_type     = $game_type;
        $view->providers     = $providers;
        $view->brands     = $brands;
        $view->curr_provider = $game_provider;
        $view->gameshow = $gameshow;
        $view->curr_brand = $game_brand;
        //
        $view->time_from     = $time_from;
        $view->time_to       = $time_to;


        $view->data        = $data;
        $view->list        = $this->list;
        $view->search      = $this->search;
        $view->search_vars = $this->search_vars;
        $view->label       = $this->model->labels();
        $view->model       = $this->controller;
        $view->mark        = $this->mark;
        $view->dir         = $this->dir;
        $view->vidgets     = $this->vidgets;
        $view->actions     = $this->actions;
        if($this->request->is_initial())
        {
            $this->template->content = $view->render();
        }
        else
        {
            $this->response->body($view->render());
        }


    }

    public function action_indexold()
    {
        $export     = isset($_GET['export']) ? $_GET['export'] : null;
        $office_id     = arr::get($_GET,'office_id',0);
        $game_type     = arr::get($_GET,'type',0);
        $game_provider = arr::get($_GET,'provider',0);
        $game_brand = arr::get($_GET,'brand',0);
        $time_from     = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to       = isset($_GET['time_to']) ? $_GET['time_to'] : null;
        if(!isset($time_from))
        {
            $time_from = date("Y-m-d",strtotime("-1 months"));
        }
        if(!isset($time_to))
        {
            $time_to = date("Y-m-d");
        }

        //пагинатор
        $page      = $this->request->param('id',1);
        $page      = max(1,$page);
        $offset    = $this->per_page * ($page - 1);

        $offices = $this->offices();

        if($export){
            $offset=0;
            $this->per_page=NULL;
        }

        $sql_games = <<<SQL
            Select  s.game, s.type
            FROM "statistics" s
SQL;

        if($game_provider)
        {
            $sql_games .= <<<SQL
                JOIN games g ON s.game=g."name"
SQL;
        }

        $sql_games .= <<<SQL
                WHERE s.date >= :time_from
            AND s.date <= :time_to
            AND s.office_id in :offices
SQL;

        if($game_provider)
        {
            $sql_games .= <<<SQL
                AND g.provider=:provider
                AND g.show<>0
SQL;
        }

        if($office_id)
        {
            $sql_games .= <<<SQL
                AND s.office_id = :office_id
SQL;
        }
        if($game_type)
        {
            $sql_games .= <<<SQL
                AND s.type = :type
SQL;
        }
        $sql_games .= <<<SQL
                group BY s.game, s.type
                ORDER BY s.game
SQL;

        $params_games = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':office_id' => $office_id,
                ':offices' => $offices,
                ':type'      => $game_type,
                ':provider'  => $game_provider,
        ];
        $res_games    = db::query(database::SELECT,$sql_games)->parameters($params_games)->execute()->as_array();//Количество игр за выбранный период
//        Kohana::$log->add(LOG::NOTICE,debug::vars($res_games));


        $page_data = array
                (
                'total_items'    => count($res_games),
                'items_per_page' => $this->per_page,
                'current_page'   => array(
                        'source' => 'route',
                        'key'    => 'id'
                ),
                'auto_hide'      => TRUE,
        );

        //основные данные
        $view            = new View($this->sh . '/index');
        $sql_gamesonpage = <<<SQL
                Select s.game,COUNT(s.game), s.type
                FROM "statistics" s

SQL;
        if($game_provider)
        {
            $sql_gamesonpage .= <<<SQL
                 JOIN games g ON s.game=g."name"
SQL;
        }

        $sql_gamesonpage .= <<<SQL
                WHERE s.date >= :time_from
                AND s.date <= :time_to
                AND s.office_id in :offices
SQL;

        if($game_provider)
        {
            $sql_gamesonpage .= <<<SQL
                 AND g.provider=:provider
                 AND g.show<>0
SQL;
        }
        if($office_id)
        {
            $sql_gamesonpage .= <<<SQL
                AND s.office_id = :office_id
SQL;
        }
        if($game_type)
        {
            $sql_gamesonpage .= <<<SQL
                AND s.type = :type
SQL;
        }
        $sql_gamesonpage    .= <<<SQL
                GROUP BY s.game, s.type
                ORDER BY s.game ASC
                OFFSET :offset
                LIMIT :limit
SQL;
        $params_gamesonpage = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':offset'    => $offset,
                ':limit'     => $this->per_page,
                ':office_id' => $office_id,
                ':offices' => $offices,
                ':type'      => $game_type,
                ':provider'  => $game_provider,
        ];
        $res_gamesonpage    = db::query(database::SELECT,$sql_gamesonpage)->parameters($params_gamesonpage)->execute()->as_array();// Игры на странице

        $sql_dayspergame = <<<SQL
                Select distinct date
                FROM "statistics"
                WHERE date >= :time_from
                AND date <= :time_to
                AND game=:game
                AND type = :type
                AND office_id in :offices
SQL;
        if($office_id)
        {
            $sql_dayspergame .= <<<SQL
                AND office_id = :office_id
SQL;
        }
        $daysonpage = [];//Дней на странице
        foreach($res_gamesonpage as $key => $game)
        {
            $params_dayspergame = [
                    ':game'      => $game['game'],
                    ':type'      => $game['type'],
                    ':office_id' => $office_id,
                    ':offices' => $offices,
                    ':time_from' => $time_from,
                    ':time_to'   => $time_to,
            ];
            $res_dayspergame    = db::query(database::SELECT,$sql_dayspergame)->parameters($params_dayspergame)->execute()->as_array();

            foreach($res_dayspergame as $v)
            {
                $daysonpage[] = $v['date'];
            }
        }
        $daysonpage = array_unique($daysonpage);
        rsort($daysonpage);

        $sql_bettype = <<<SQL
                SELECT bettype FROM
                (
                    SELECT DISTINCT
                    CASE WHEN bettype not in ('bonus', 'free', 'normal', 'double') THEN 'normal' ELSE bettype END
                    FROM "statistics"
                    WHERE date=:date
                    AND office_id in :offices
SQL;
        if($office_id)
        {
            $sql_bettype .= <<<SQL
                    AND office_id = :office_id
SQL;
        }
        if($game_type)
        {
            $sql_bettype .= <<<SQL
                    AND type = :type
SQL;
        }
        $sql_bettype .= <<<SQL
                ) s
                GROUP BY bettype
                    ORDER BY
                        CASE bettype
                            WHEN 'bonus' THEN 1
                            WHEN 'free' THEN 2
                            ELSE 3
                        END, bettype
SQL;

        $daybetonpage = [];//дни - типы игры

        foreach($daysonpage as $day)
        {
            $params_bettype = [
                    ':date'      => $day,
                    ':office_id' => $office_id,
                    ':offices' => $offices,
                    ':type'      => $game_type,
            ];
            $res_bettype    = db::query(database::SELECT,$sql_bettype)->parameters($params_bettype)->execute()->as_array('bettype');

            $daybetonpage[$day] = $res_bettype;
        }

        $sql_totalperiod = <<<SQL
                SELECT sum(s.amount_in) as sumin, sum(s.amount_out) as sumout, sum(s.count) as sumcount
                FROM "statistics" s

SQL;
        if($game_provider)
        {
            $sql_totalperiod .= <<<SQL
                JOIN games g ON s.game=g."name"
SQL;
        }

        $sql_totalperiod .= <<<SQL
                WHERE s.date=:date
                AND s.bettype in :bettype
                AND s.date >= :time_from
                AND s.date <= :time_to
                AND s.office_id in :offices

SQL;

        if($game_provider)
        {
            $sql_totalperiod .= <<<SQL
                AND g.provider=:provider
                AND g.show<>0
SQL;
        }

        if($office_id)
        {
            $sql_totalperiod .= <<<SQL
                AND s.office_id = :office_id
SQL;
        }
        if($game_type)
        {
            $sql_totalperiod .= <<<SQL
                AND s.type = :type
SQL;
        }

        $total_period = [];
        foreach($daybetonpage as $day => $bettypes)
        {
            foreach($bettypes as $bettype => $v)
            {

                 if(in_array($bettype,['bonus','free','double'])){
                            $bettype_filter=[$bettype];
                        }
                        else{
                            $bettype_filter=['normal','bonus','free','unknow','bet','spin','Gamble','block', 'test'];
                        }
                $params_totalperiod           = [
                        ':time_from' => $time_from,
                        ':time_to'   => $time_to,
                        ':date'      => $day,
                        ':bettype'   => $bettype_filter,
                        ':office_id' => $office_id,
                        ':offices' => $offices,
                        ':type'      => $game_type,
                        ':provider'      => $game_provider,
                ];
                $res_totalperiod              = db::query(database::SELECT,$sql_totalperiod)->parameters($params_totalperiod)->execute()->as_array();
                $total_period[$day][$bettype] = [
                        'sumin'    => $res_totalperiod[0]['sumin'],
                        'sumout'   => $res_totalperiod[0]['sumout'],
                        'sumcount' => $res_totalperiod[0]['sumcount'],
                        'percent'  => $res_totalperiod[0]['sumin'] == 0 ? '-' : round($res_totalperiod[0]['sumout'] / $res_totalperiod[0]['sumin'] * 100,1)
                ];
            }
        }

        $sql_gamedata = <<<SQL
                Select *
                FROM "statistics"
                WHERE date=:date
                AND game=:game
                AND type=:type
                AND bettype = :bettype
                AND office_id in :offices
SQL;
        if($office_id)
        {
            $sql_gamedata .= <<<SQL
                AND office_id = :office_id
SQL;
        }

        $data = [];//Игры - дни - типы игры - данные

        foreach($res_gamesonpage as $v)
        {
            foreach($daybetonpage as $day => $d)
            {
                foreach($d as $bettype => $b)
                {

                    $params_gamedata = [
                            ':date'      => $day,
                            ':game'      => $v['game'],
                            ':type'      => $v['type'],
                            ':bettype'   => $bettype,
                            ':office_id' => $office_id,
                    ];
                    $res_gamedata    = db::query(database::SELECT,$sql_gamedata)->parameters($params_gamedata)->execute()->as_array();

//                    $data[$v['game']][$v['type']][$day][$bettype] = $res_gamedata;
                    if(isset($res_gamedata[0])){

                        if(in_array($bettype,['normal','bonus','free','unknow','bet','spin','Gamble','block', 'test'])){

    //
                            if(!isset($data[$v['game']][$v['type']][$day]['normal']))
                            {
                                $data[$v['game']][$v['type']][$day]['normal'] = $res_gamedata;
                            }else{

                                $data[$v['game']][$v['type']][$day]['normal'][0]['amount_in'] += $res_gamedata[0]['amount_in'];
                                $data[$v['game']][$v['type']][$day]['normal'][0]['amount_out'] += $res_gamedata[0]['amount_out'];
                                $data[$v['game']][$v['type']][$day]['normal'][0]['count'] += $res_gamedata[0]['count'];
                            }
                            if(in_array($bettype,['bonus','free'])){
                                $data[$v['game']][$v['type']][$day][$bettype] = $res_gamedata;
                            }
                        }else{
                            $data[$v['game']][$v['type']][$day][$bettype] = $res_gamedata;
                        }
                    }else{
                        $data[$v['game']][$v['type']][$day][$bettype] = $res_gamedata;
                    }
                }
            }
        }

//        $offices     = [0 => 'Все'];
//        $sql_offices = <<<SQL
//            Select o.id as office_id, c.code
//            From offices o JOIN currencies c ON o.currency_id=c.id
//SQL;
//        $res_offices = db::query(1,$sql_offices)->execute()->as_array('office_id');
//
//        foreach($res_offices as $off_id => $value)
//        {
//            $offices[$off_id] = $value['code'];
//        }


        $types     = [0 => 'Все'];
        $sql_types = <<<SQL
            Select DISTINCT type
            FROM "statistics"
SQL;
        $res_types = db::query(1,$sql_types)->execute()->as_array('type');

        foreach($res_types as $ga_t => $value)
        {
            $types[$ga_t] = $value['type'];
        }


        $providers     = [0 => 'Все'];
        $sql_providers = <<<SQL
            Select DISTINCT provider
            FROM games
SQL;
        $res_providers = db::query(1,$sql_providers)->execute('games')->as_array('provider');

        foreach($res_providers as $ga_t => $value)
        {
            $providers[$ga_t] = $value['provider'];
        }

        $csv='';
        if($export){
            $l=$this->model->labels();
            $lst=$this->list;
            $head=[];
            $head[0]='Игра;';
            $head[1]=' ;';
            $head[2]=' ;';
            foreach($daybetonpage as $day => $bettypes){
                $head[0].=$day.';';
                $colspan=count($bettypes) * 4;
                for($i=1;$i<$colspan;$i++){
                       $head[0].=';';
                }
                foreach($bettypes as $btype => $v){
                    $head[1].=$btype.';';
                    for($k=1;$k<4;$k++){
                       $head[1].=';';
                    }
                    $head[2].=isset($l[$lst[5]]) ? $l[$lst[5]] : $lst[5];$head[2].=';';
                    $head[2].=isset($l[$lst[6]]) ? $l[$lst[6]] : $lst[6];$head[2].=';';
                    $head[2].=isset($l[$lst[7]]) ? $l[$lst[7]] : $lst[7];$head[2].=';';
                    $head[2].=isset($l[$lst[8]]) ? $l[$lst[8]] : $lst[8];$head[2].=';';
                }
            }
            $body=[];
            $i=0;
            foreach($data as $gm => $tps){
                foreach($tps as $tp => $vl){
                    $body[$i]='';
                    $body[$i].=$gm.' - '.$tp.';';
                    foreach($daybetonpage as $day => $bettypes){
                        foreach($bettypes as $btype => $v){
                            $body[$i].=$vl[$day][$btype][0]['amount_in'] ?? '-';$body[$i].=';';
                            $body[$i].=$vl[$day][$btype][0]['amount_out'] ?? '-';$body[$i].=';';
                            $body[$i].=$vl[$day][$btype][0]['count'] ?? '-';$body[$i].=';';
                            $body[$i].=(isset($vl[$day][$btype][0]['amount_in']) AND $vl[$day][$btype][0]['amount_in'] > 0) ? round($vl[$day][$btype][0]['amount_out'] / $vl[$day][$btype][0]['amount_in'] * 100,1) : '-';$body[$i].=';';
                        }
                    }
                    $i++;
                }
            }
            $foot=[];
            $foot[0]='Итого за период;';
            foreach($total_period as $day => $btypes){
                foreach($btypes as $btype => $v){
                    $foot[0].=$v['sumin'].';';
                    $foot[0].=$v['sumout'].';';
                    $foot[0].=$v['sumcount'].';';
                    $foot[0].=$v['percent'].';';
                }
            }
            $csv=array($head, $body, $foot);
            th::to_csv($csv, $time_from, $time_to, $this->request->controller());
        }


        $view->offices       = $offices;
        $view->curr_office   = $office_id;
        $view->types         = $types;
        $view->curr_type     = $game_type;
        $view->providers     = $providers;
        $view->curr_provider = $game_provider;
        //
        $view->time_from     = $time_from;
        $view->time_to       = $time_to;


        $view->dbp         = $daybetonpage;
        $view->tp          = $total_period;
        $view->data        = $data;
        $view->data2       = $this->handler_search($_GET)->find_all()->as_array();
        $view->list        = $this->list;
        $view->search      = $this->search;
        $view->search_vars = $this->search_vars;
        $view->label       = $this->model->labels();
        $view->model       = $this->controller;
        $view->mark        = $this->mark;
        $view->dir         = $this->dir;
        $view->vidgets     = $this->vidgets;
        $view->page        = Pagination::factory($page_data)->render('pagination/floating');
        $view->actions     = $this->actions;
        if($this->request->is_initial())
        {
            $this->template->content = $view->render();
        }
        else
        {
            $this->response->body($view->render());
        }
    }

    public function configure()
    {
        $this->search = [
        ];
        $this->list   = [
                'date',
                'type',
                'game',
                'bettype',
                'office_id',
                'amount_in',
                'amount_out',
                'count',
                'persent',
        ];

//		$ai = new Vidget_Persent('amount_out',$this->model);
//		$ai->param('all',['amount_in']);
//		$this->vidgets['persent'] = $ai;
    }

}
