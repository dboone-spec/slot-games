<?php

class Controller_Admin_Statisticsold extends Super
{

    public $mark       = 'Статистика'; //имя
    public $model_name = 'statistics'; //имя модели
    public $sh         = 'admin/statisticsold'; //шаблон
    public $per_page   = 31;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    
    
    


    public function action_index()
    {
        $office_id = arr::get($_GET,'office_id',0);
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

        $sql_games = <<<SQL
            Select date, game, sum(count) as count_bets, sum(amount_in) as amount_in, sum(amount_out) as amount_out,
                CASE
                    WHEN bettype like 'free%' OR bettype = 'bonus' THEN bettype
                    WHEN bettype not in ('normal', 'double') THEN 'normal'
                    ELSE
                        bettype
                END AS bettype
            From statistics
            Where
                date >= :time_from
                AND date <= :time_to
SQL;
        if($office_id) {
            $sql_games .= "AND office_id = :office_id";
        }

        $sql_games .= <<<SQL
            GROUP BY date, game, bettype
            ORDER BY
                array_position(array['free','free_1','free_2','free_3','free_4','free_5','free_6','free_7','free_8','free_9','bonus','normal','double'], bettype::text),
                date DESC
SQL;

        $games = db::query(1, $sql_games)->parameters([
            ':time_from' => $time_from,
            ':time_to' => $time_to,
            ':office_id' => $office_id,
        ])->execute()->as_array();


        $all_games = [];
        $dates = [];
        $types = [];
        /*
         * храним всю стату тут
         */
        $data = [];

        foreach ($games as $g) {
            if(!in_array($g['game'], $all_games)) {
                $all_games[] = $g['game'];
            }

            if(!in_array($g['date'], $dates)) {
                $dates[] = $g['date'];
            }

            if(!isset($data[$g['game']][$g['date']][$g['bettype']])) {
                $type = $g['bettype'];
                $bettype = $g['bettype'];

                if(strpos($bettype, 'free') !== false) {
                    $type = $bettype;
                    $bettype = 'fg';
                } else {
                    $bettype = 'bets';
                }


                $data[$g['game']][$g['date']][$bettype][$type] = [
                    'count_bets'=>$g['count_bets'],
                    'amount_in'=>$g['amount_in'],
                    'amount_out'=>$g['amount_out'],
                ];

                if(!isset($types[$g['game']][$bettype][$type])) {
                    $types[$g['game']][$bettype][$type] = ['count_bets','amount_in','amount_out','perc'];
                }
            }
        }

        foreach ($data as $game_name=>$game) {
            foreach ($game as $date => $value) {
                foreach ($value as $bt => $val) {
                    foreach ($val as $t => $params) {
                        foreach ($params as $k => $v) {
                            if($t == 'free') {
                                if(!isset($data[$game_name][$date]['bets']['normal'])) {
                                    $data[$game_name][$date]['bets']['normal'] = [
                                        'count_bets' => 0,
                                        'amount_in' => 0,
                                        'amount_out' => 0,
                                    ];
                                }

                                $data[$game_name][$date]['bets']['normal'][$k] += $v;
                            }
                        }
                    }
                }
            }
        }

        foreach ($data as $game_name=>$game) {
            foreach ($game as $date => $value) {
                foreach ($value as $bt => $val) {
                    foreach ($val as $t => $params) {
                        if(strpos($t, 'free') !== false) {
                            $amount_norm_in = $value['bets']['normal']['amount_in']??0;
                            $percent = $amount_norm_in>0?round($params['amount_out']/$amount_norm_in*100, 2):0;
                        } else {
                            $percent = $params['amount_in']>0?round($params['amount_out']/$params['amount_in']*100, 2):0;
                        }

                        $data[$game_name][$date][$bt][$t]['perc'] = $percent;
                    }
                }
            }
        }

        $offices     = [0 => 'Все'];
        $sql_offices = <<<SQL
            Select o.id as office_id, c.code
            From offices o JOIN currencies c ON o.currency_id=c.id
SQL;
        $res_offices = db::query(1,$sql_offices)->execute()->as_array('office_id');

        foreach($res_offices as $off_id => $value)
        {
            $offices[$off_id] = $value['code'];
        }

        $labels = [
            'fg' => 'ФРИГЕЙМЫ',
            'free' => 'ВСЕГО ФРИГЕЙМОВ',
            'free_1' => 'ФРИГЕЙМЫ 1 уровня',
            'free_2' => 'ФРИГЕЙМЫ 2 уровня',
            'free_3' => 'ФРИГЕЙМЫ 3 уровня',
            'free_4' => 'ФРИГЕЙМЫ 4 уровня',
            'free_5' => 'ФРИГЕЙМЫ 5 уровня',
            'free_6' => 'ФРИГЕЙМЫ 6 уровня',
            'free_7' => 'ФРИГЕЙМЫ 7 уровня',
            'free_8' => 'ФРИГЕЙМЫ 8 уровня',
            'free_9' => 'ФРИГЕЙМЫ 9 уровня',
            'bonus' => 'бонусные игры',
            'bets' => 'ОБЫЧНЫЕ ставки',
            'normal' => 'ставка',
            'double' => 'удвоение',
            'count_bets' => 'количество ставок',
            'amount_in' => 'IN',
            'amount_out' => 'OUT',
            'perc' => '% out/in',
        ];

        $view        = new View($this->sh . '/index');

        $view->labels = json_encode($labels);
        $view->dates = json_encode($dates);
        $view->types = json_encode($types);
        $view->games = json_encode($all_games);
        $view->offices     = $offices;
        $view->curr_office = $office_id;
        $view->time_from   = $time_from;
        $view->time_to     = $time_to;
        $view->data        = json_encode($data);
        $view->list        = $this->list;
        $view->search      = $this->search;
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
