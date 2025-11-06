<?php

class Controller_Admin_Userpayment extends Super
{

    public $mark       = 'Статистика платежей (по пользователям)'; //имя
    public $model_name = 'payment'; //имя модели
    public $sh = 'admin/userpayment'; //шаблон
    public $per_page   = 30;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function action_index()
    {
        $export     = isset($_GET['export']) ? $_GET['export'] : null;

        $user_id     = isset($_GET['user_id']) ? $_GET['user_id'] : null;

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
                FROM payments
                WHERE payed>=:time_from
                AND payed<=:time_to
SQL;

        if($user_id)
        {
            $sql .= <<<SQL
                AND user_id = :user_id
SQL;
        }

        $params = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':user_id' => $user_id,
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
                "user_id"    => "Пользователь",
                "sum_in"     => "Всего ввёл",
                "sum_out"    => "Всего вывел",
                "sumin_com"  => "Всего ввёл с комиссией",
                "sumout_com" => "Всего вывел с комиссией",
        ];

        $sql_stat = <<<SQL
            SELECT user_id, SUM(GREATEST(0, amount)) AS sum_in, -SUM(LEAST(0, amount)) AS sum_out, SUM(GREATEST(0, with_commission)) AS sumin_com, -SUM(LEAST(0, with_commission)) AS sumout_com
            FROM
                (   SELECT user_id, amount,
                        CASE amount
                            WHEN LEAST(0, amount) THEN (amount + total_commission)
                            WHEN GREATEST(0, amount) THEN (amount - total_commission)
                            ELSE 0
                        END AS with_commission
                    FROM payments
                    WHERE status = 30
                    AND payed > :time_from
                    AND payed <= :time_to
                    AND payment_system_id != '50d9ebfd8f2a2dd45d000015'
                ) sub

SQL;
        if($user_id){
            $sql_stat .= <<<SQL
               WHERE user_id=:user_id
SQL;
        }


        $sql_stat .= <<<SQL
            GROUP BY user_id
            ORDER BY user_id DESC
            OFFSET :offset
            LIMIT :limit
SQL;

        $params_query = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':user_id'   => $user_id,
                ':offset'    => $offset,
                ':limit'     => $this->per_page,
        ];

        $res = db::query(database::SELECT,$sql_stat)->parameters($params_query)->execute()->as_array();

        $csv='';
        if($export){
            $head=[];
            $head[0]='';
            foreach($headers as $header => $v){
                $head[0].=$v.';';
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
        $view = new View('admin/userpayment/index');

        $view->page        = Pagination::factory($page_data)->render('pagination/floating');
        $view->mark      = $this->mark;
        $view->dir       = $this->dir;
        $view->headers   = $headers;
        $view->data      = $res;
        $view->time_from = $time_from;
        $view->time_to   = $time_to;
        $view->user_id   = $user_id;
        $this->template->content = $view;
    }

}
