<?php

class Controller_Admin_Operationstat extends Super{



    public $mark='Статистика платежей'; //имя
    public $model_name='operation'; //имя модели
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    public $sh='admin/paymentstat'; //шаблон
    public $payment_types=['user_payment','user_withdraw'];
    public $per_page   = 140; //commit


    public function action_index() {

        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : date('Y-m-d');
        $office_id = isset($_GET['office_id']) ? (int) $_GET['office_id'] : 0;


        $offices = Person::user()->officesName(null,true);

        $encashment_time = $this->encashment_time-$this->zone_time;

        $limit = mktime($encashment_time,0,0,date('m'),date('d')-$this->day_period[person::$role],date('Y'));
        if(!isset($time_from)) {
            $time_from = $limit;
        } else {
            $time_from_arr = explode('-', $time_from);
            $time_from = mktime($encashment_time, 0, 0, $time_from_arr[1], $time_from_arr[2], $time_from_arr[0]);
        }

        if($time_from<$limit){
                $time_from=$limit;
        }

        if(!isset($time_to)) {
            $time_to = time();
        } else {
            $time_to_arr = explode('-', $time_to);
            $time_to = mktime($encashment_time-1, 59, 59, $time_to_arr[1], $time_to_arr[2], $time_to_arr[0]);
        }


         //пагинатор
        $page     = $this->request->param('id',1);
        $page     = max(1,$page);
        $offset   = $this->per_page * ($page - 1);

        $sql = <<<SQL
                Select
                    date_part('epoch', date_trunc( 'day',  to_timestamp(o.created) at time zone 'UTC' ) ) as payed
                FROM
                operations o
                WHERE
                o.office_id in :o_id
                and
                o.type in :types
                AND o.created > :time_from AND o.created <= :time_to
                group by date_trunc('day', to_timestamp(o.created) at time zone 'UTC')
                ORDER BY
                1 DESC
SQL;

        $o_ids = array_keys($offices);

        $params = [
            ':time_from' => $time_from,
            ':time_to'   => $time_to,
            ':o_id'   => $o_ids,
            ':types'   => $this->payment_types,
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
            "payed" => __("Дата"),
            "office_id" => __("ППС"),
            "person_id" => __("Персонал"),
            "amount_in" => "DROP",
            "amount_out" => "HANDPAY",
            "amount" => "WIN",
        ];

        $sql_stat = <<<SQL
            Select
                    date_part('epoch', date_trunc( 'day',  to_timestamp(o.created) at time zone 'UTC' ) ) as payed,
                    o.office_id,o.person_id,
                    SUM (o.amount) as amount,
                    sum(case when o.amount > 0 then o.amount else 0 end) as amount_in,
                    sum(case when o.amount < 0 then o.amount else 0 end) as amount_out
                FROM
                operations o
                WHERE
                o.office_id in :o_id
                and
                o.type in :types
                AND o.created > :time_from AND o.created <= :time_to
                group by date_trunc('day', to_timestamp(o.created) at time zone 'UTC'),o.office_id,o.person_id
                ORDER BY
                1 DESC
                OFFSET :offset
                LIMIT :limit
SQL;

        if($office_id) {
            $o_ids=[$office_id];
        }

        $params_query = [
            ':time_from' => $time_from,
            ':time_to' => $time_to,
            ':o_id'   => $o_ids,
            ':types'   => $this->payment_types,
            ':offset'    => $offset,
            ':limit'     => $this->per_page,
        ];

        $res = db::query(database::SELECT, $sql_stat)->parameters($params_query)->execute()->as_array();
        $data = $res;
        $all = [];

        foreach ($res as $value) {

            if(!isset($all[$office_id][$value['person_id']]['all'])) {
                $all[$office_id][$value['person_id']]['all'] = [
                    'total' => 0,
                    'total_in' => 0,
                    'total_out' => 0,
                ];
            }

            $all[$office_id][$value['person_id']]['all']['total'] += $value['amount'];
            $all[$office_id][$value['person_id']]['all']['total_in'] += $value['amount_in'];
            $all[$office_id][$value['person_id']]['all']['total_out'] += -1*$value['amount_out'];
        }


        $offices[0]=__('Все');

        ksort($offices);

        $view = new View('admin/paymentstat/operations');
        $view->page        = Pagination::factory($page_data)->render('pagination/floating');
        $view->mark = $this->mark;
        $view->headers = $headers;
        $view->offices = $offices;
        $view->current_office = (int) $office_id;
        $view->data = $data;
        $view->all = $all;
        $view->time_from = $time_from+($this->zone_time*60*60);
        $view->time_to = $time_to+($this->zone_time*60*60);
        $view->dir = $this->dir;

        $this->template->content = $view;
    }

}


