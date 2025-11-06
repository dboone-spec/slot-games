<?php

class Controller_Admin_Paymentstat extends Super{



    public $mark='Статистика платежей'; //имя
    public $model_name='payment'; //имя модели
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    public $sh='admin/paymentstat'; //шаблон

    public $per_page   = 40;

    public function action_index() {

        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : null;

        if(!isset($time_from)) {
            $time_from = mktime(0, 0, 0, date('m')-1, date('d'), date('Y'));
        } else {
            $time_from_arr = explode('-', $time_from);
            $time_from = mktime(0, 0, 0, $time_from_arr[1], $time_from_arr[2], $time_from_arr[0]);
        }

        if(!isset($time_to)) {
            $time_to = time();
        } else {
            $time_to_arr = explode('-', $time_to);
            $time_to = mktime(23, 59, 59, $time_to_arr[1], $time_to_arr[2], $time_to_arr[0]);
        }

         //пагинатор
        $page     = $this->request->param('id',1);
        $page     = max(1,$page);
        $offset   = $this->per_page * ($page - 1);

        $join_sql = '';
        if(person::$role=='analitic') {
            $join_sql = ' join persons on persons.id = p.user_id ';
        }
        
        $sql = <<<SQL
                Select currency, date_part('epoch', date_trunc( 'day',  to_timestamp(P.payed) at time zone 'UTC' ) ) as payed, P.gateway
                FROM
                payments P
                {$join_sql}
                WHERE
                P.status = 30
                AND p.payed > :time_from AND p.payed <= :time_to
                AND p.payment_system_id != '50d9ebfd8f2a2dd45d000015'
                GROUP BY currency,
                date_trunc('day', to_timestamp(P.payed) at time zone 'UTC'), P.gateway
                ORDER BY
                2 DESC,
                P.gateway
SQL;

        $params = [
            ':time_from' => $time_from,
            ':time_to'   => $time_to,
        ];

        $res_page    = db::query(database::SELECT,$sql)->parameters($params)->execute()->as_array();
        $page_data   = array
                (
                'total_items'    => count($res_page),
                'items_per_page' => $this->per_page,
                'current_page'   => array
                        (
                        'source' => 'route',
                        'key'    => 'id'
                ),
                'auto_hide'      => TRUE,
        );

        $headers = [
            "payed" => "Дата",
            "payment_in" => "drop",
            "payment_out" => "handpay",
            "amount" => "win",
            "payment_with_in" => "drop с комиссией",
            "payment_with_out" => "handpay с комиссией",
            "total_with_comission" => "win с комиссией",
            "currency" => "Валюта",
        ];

        $sql_stat = <<<SQL
            Select currency,
            date_part('epoch',
                date_trunc(
                        'day',
                        to_timestamp(P.payed) at time zone 'UTC'
                )
            ) as payed,
            SUM(P.amount) as amount,
            P.gateway,
            sum(case when p.amount>0 then p.amount else 0 end) as payment_in,
            sum(case when p.amount<0 then p.amount else 0 end) as payment_out,
            SUM(p.amount - p.total_commission) AS total_with_comission,
            sum(case when p.amount>0 then p.amount-p.total_commission else 0 end) as payment_with_in,
            sum(case when p.amount<0 then p.amount-p.total_commission else 0 end) as payment_with_out
            FROM
                payments P
            {$join_sql}
            WHERE 
                P.status = 30
            AND p.payment_system_id != '50d9ebfd8f2a2dd45d000015'
            AND p.payed > :time_from AND p.payed <= :time_to
            GROUP BY
                currency,
                date_trunc(
                    'day',
                    to_timestamp(P.payed) at time zone 'UTC'
                ),
                P.gateway
            ORDER BY
                2 DESC,
                P.gateway
                OFFSET :offset
                LIMIT :limit
SQL;

        $params_query = [
            ':status' => PAY_SUCCES,
            ':time_from' => $time_from,
            ':time_to' => $time_to,
            ':offset'    => $offset,
            ':limit'     => $this->per_page,
        ];

        $res = db::query(database::SELECT, $sql_stat)->parameters($params_query)->execute()->as_array();

        $data = [];
        $all = [];

        foreach ($res as $value) {
            $date = $value['payed'];

            if(!isset($data[$date][$value['currency']]['rows'])) {
                $data[$date][$value['currency']]['rows'] = [];
            }

            if(!isset($data[$date][$value['currency']]['day_all'])) {
                $data[$date][$value['currency']]['day_all'] = [
                    "total" => 0,
                    "total_with_comission" => 0,
                ];
            }

            $data[$date][$value['currency']]['rows'][] = $value;
            $data[$date][$value['currency']]['day_all']["total"] += $value["amount"];
            $data[$date][$value['currency']]['day_all']["total_with_comission"] += $value["total_with_comission"];

            if(!isset($all[$value['currency']][$value['gateway']]['total'])) {
                $all[$value['currency']][$value['gateway']]['total'] = 0;
            }

            if(!isset($all[$value['currency']][$value['gateway']]['total_with_comission'])) {
                $all[$value['currency']][$value['gateway']]['total_with_comission'] = 0;
            }

			
            if(!isset($all[$value['currency']][$value['gateway']]['payment_in'])) {
                $all[$value['currency']][$value['gateway']]['payment_in'] = 0;
            }
			
            if(!isset($all[$value['currency']][$value['gateway']]['payment_out'])) {
                $all[$value['currency']][$value['gateway']]['payment_out'] = 0;
            }

			
            $all[$value['currency']][$value['gateway']]['total'] += $value['amount'];
            $all[$value['currency']][$value['gateway']]['total_with_comission'] += $value['total_with_comission'];

            $all[$value['currency']][$value['gateway']]['payment_in'] += $value['payment_in'];
            $all[$value['currency']][$value['gateway']]['payment_out'] += $value['payment_out'];

            if(!isset($all[$value['currency']]['all'])) {
                $all[$value['currency']]['all'] = [
                    'total' => 0,
                    'total_with_comission' => 0,
					'payment_in' => 0,
                    'payment_out' => 0
                ];
            }

            $all[$value['currency']]['all']['total'] += $value['amount'];
            $all[$value['currency']]['all']['total_with_comission'] += $value['total_with_comission'];
			$all[$value['currency']]['all']['payment_in'] += $value['payment_in'];
            $all[$value['currency']]['all']['payment_out'] += $value['payment_out'];
        }

        $view = new View('admin/paymentstat/index');
        $view->page        = Pagination::factory($page_data)->render('pagination/floating');
        $view->mark = $this->mark;
        $view->headers = $headers;
        $view->data = $data;
        $view->all = $all;
        $view->time_from = $time_from;
        $view->time_to = $time_to;
        $view->dir = $this->dir;

        $this->template->content = $view;
    }

}


