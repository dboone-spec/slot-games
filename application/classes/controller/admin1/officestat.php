<?php

class Controller_Admin_Officestat extends Super{



    public $mark='Статистика ППС'; //имя
    public $model_name='operation'; //имя модели
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    public $sh='admin/officestat'; //шаблон
    public $per_page   = 40;


    public function action_index() {

        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : null;
        $office_id = isset($_GET['office_id']) ? (int) $_GET['office_id'] : 0;


        $offices = $this->offices();

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

        $o_ids = [];
        if($office_id>0) {
            $o_ids[]=$office_id;
        }

        if(person::$role=='analitic' && $office_id==0) {
            $o_ids=$offices;
        }

        $page_data   = array
                (
                'total_items'    => count($offices),
                'items_per_page' => $this->per_page,
                'current_page'   => array
                        (
                        'source' => 'route',
                        'key'    => 'id'
                ),
                'auto_hide'      => TRUE,
        );



        $headers = [
            "office_id" => "ППС",
            "visible_name" => "Название",
            "agent" => "Агент",
            "amount" => "Баланс",
            "win_month" => "win за месяц",
            "win_current" => "win текущий",
        ];

        $sql_stat = <<<SQL
            SELECT
                o. ID as office_id,
                o.visible_name,
                o.amount,
                P.NAME as agent,
                sum(case when oo.created >= :time_30 and oo.type in ('user_withdraw','user_payment') then oo.amount else 0 end),
                sum(case when oo.created >= :time_current and oo.type in ('user_withdraw','user_payment') then oo.amount else 0 end),
                sum(case when oo.created >= :time_30 and oo.type in ('payment_office') then oo.amount else 0 end)
            FROM
                offices o
            JOIN person_offices po ON o. ID = po.office_id
            JOIN persons P ON P . ID = po.person_id
            join operations oo on o.id=oo.office_id
            where p.role = 'agent' and oo.type in ('user_withdraw','user_payment') and oo.created>=:time_30
            GROUP BY 1,2,3,4
            OFFSET :offset
            LIMIT :limit
SQL;

        $params_query = [
            ':time_30' => mktime(0,0,0,date('n')-1) - 365*24*60*60,
            ':time_current' => mktime(0,0,0,date('n'),1),
            ':offset'    => $offset,
            ':limit'     => $this->per_page,
        ];
echo db::query(database::SELECT, $sql_stat)->parameters($params_query)->compile(Database::instance()); exit;
        $res = db::query(database::SELECT, $sql_stat)->parameters($params_query)->execute()->as_array();
        $data = [];
        $all = [];

        $data = $res;
        var_dump($data);
        exit;
        /*foreach ($res as $value) {
            $date = $value['payed'];

            if(!isset($data[$date][$value['office_id']]['rows'])) {
                $data[$date][$value['office_id']]['rows'] = [];
            }

            if(!isset($data[$date][$value['office_id']]['day_all'])) {
                $data[$date][$value['office_id']]['day_all'] = [
                    "total" => 0,
                    "total_in" => 0,
                    "total_out" => 0,
                ];
            }

            $data[$date][$value['office_id']]['rows'][] = $value;
            $data[$date][$value['office_id']]['day_all']["total"] += $value["amount"];
            $data[$date][$value['office_id']]['day_all']["total_in"] += $value["amount_in"];
            $data[$date][$value['office_id']]['day_all']["total_out"] += -1*$value["amount_out"];


            if(!isset($all[$office_id]['all'])) {
                $all[$office_id]['all'] = [
                    'total' => 0,
                    'total_in' => 0,
                    'total_out' => 0,
                ];
            }

            $all[$office_id]['all']['total'] += $value['amount'];
            $all[$office_id]['all']['total_in'] += $value['amount_in'];
            $all[$office_id]['all']['total_out'] += -1*$value['amount_out'];
        }*/


        $offices[0]='Все';

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


