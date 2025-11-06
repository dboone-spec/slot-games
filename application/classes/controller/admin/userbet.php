<?php

class Controller_Admin_Userbet extends Super
{

    public $mark       = 'Ставки (по пользователям)'; //имя
    public $model_name = 'bet'; //имя модели
    public $sh = 'admin/userbet'; //шаблон
    public $per_page   = 30;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function action_index()
    {
        $export     = isset($_GET['export']) ? $_GET['export'] : null;

        $user_id     = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        $game     = isset($_GET['game']) ? $_GET['game'] : null;
        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to   = isset($_GET['time_to']) ? $_GET['time_to'] : null;

        if(!isset($time_from))
        {
            $time_from = mktime(0,0,0,date('m') - 1,date('d'),date('Y'));
            $tf=date("Y-m-d",strtotime("-1 months"));
        }
        else
        {
            $tf=$time_from;
            $time_from_arr = explode('-',$time_from);
            $time_from     = mktime(0,0,0,$time_from_arr[1],$time_from_arr[2],$time_from_arr[0]);
        }

        if(!isset($time_to))
        {
            $time_to = time();
            $tt=date("Y-m-d");
        }
        else
        {
            $tt=$time_to;
            $time_to_arr = explode('-',$time_to);
            $time_to     = mktime(23,59,59,$time_to_arr[1],$time_to_arr[2],$time_to_arr[0]);
        }
        //пагинатор
        $page     = $this->request->param('id',1);
        $page     = max(1,$page);
        $offset   = $this->per_page * ($page - 1);

        if($export){
            $offset=0;
            $this->per_page=NULL;
        }

        $sql = <<<SQL
                SELECT count(DISTINCT user_id)
                FROM bets b JOIN users u ON b.user_id = u.id
                WHERE b.created>=:time_from
                AND b.created<=:time_to
SQL;

        if($user_id)
        {
            $sql .= <<<SQL
                AND (
                    u.id = :user_id
                    OR
                    u.parent_id = :user_id
                )
SQL;
        }
        if($game)
        {
            $sql .= <<<SQL
                AND game=:game
SQL;
        }

        $params = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':user_id' => $user_id,
                ':game' => $game,
        ];
        $res_page    = db::query(database::SELECT,$sql)->parameters($params)->execute()->as_array();
        $page_data   = array
                (
                'total_items'    => intval($res_page[0]['count']),
                'items_per_page' => $this->per_page,
                'current_page'   => array
                        (
                        'source' => 'route',
                        'key'    => 'id'
                ),
                'auto_hide'      => TRUE,
        );


        //Данные
        $headers = [
//                "created"    => "Дата",
                "user_id"    => "Пользователь",
                "game"    => "Игра",
                "sum_amount"     => "Сумма ставок",
                "sum_win"    => "Сумма выигрышей",
                "win"  => "WIN",
                "count_bets"  => "количество ставок",
                "percent"  => "% сумма выигрышей/сумма ставок",
        ];

        $sql_stat = <<<SQL
            SELECT coalesce(u.parent_id, u.id) as user_id,
            COUNT(b.id) as count_bets,
            SUM(b.amount) AS sum_amount, SUM(b.win) AS sum_win,
            SUM(b.amount-b.win) as win,
            b.game
            FROM bets b
            JOIN users u on user_id = u.id
            WHERE b.created>=:time_from
            AND b.created<=:time_to
SQL;
        if($user_id){
            $sql_stat .= <<<SQL
               AND (u.id=:user_id OR u.parent_id=:user_id)
SQL;
        }

        if($game){
            $sql_stat .= <<<SQL
               AND game=:game
SQL;
        }


        $sql_stat .= <<<SQL
            GROUP BY 1,b.game
            ORDER BY 1 desc
            OFFSET :offset
            LIMIT :limit
SQL;

        $params_query = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':user_id'   => $user_id,
                ':game'   => $game,
                ':offset'    => $offset,
                ':limit'     => $this->per_page,
        ];

        $res = db::query(database::SELECT,$sql_stat)->parameters($params_query)->execute()->as_array();

        $csv='';
        if($export){
            $head=[];
            $head[0]='';
            foreach($headers as $header => $v){
                $head[0].=$header.';';
            }
            $body=[];
            $i=0;
            foreach($res as $key => $val){
                $body[$i]='';
                foreach($val as $k => $v){
                    $body[$i].=(is_numeric($v)?th::number_format($v,','):$v).';';
                }
                $i++;
            }
            $csv=array($head, $body);
            th::to_csv($csv, $tf, $tt, $this->request->controller());
        }
        $view = new View('admin/userbet/index');

        $view->page        = Pagination::factory($page_data)->render('pagination/floating');
        $view->mark      = $this->mark;
        $view->headers   = $headers;
        $view->data      = $res;
        $view->time_from = $time_from;
        $view->time_to   = $time_to;
        $view->user_id   = $user_id;
        $view->game   = $game;
        $view->dir       = $this->dir;
        $this->template->content = $view;
    }

}
