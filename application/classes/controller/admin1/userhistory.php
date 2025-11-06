<?php

class Controller_Admin_Userhistory extends Super
{

    public $mark       = 'История игрока'; //имя
    public $model_name = 'statistics'; //имя модели
    public $sh         = 'admin/userhistory'; //шаблон
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    public $per_page   = 100;

    public function action_index()
    {
        $export     = isset($_GET['export']) ? $_GET['export'] : null;

        $user_id     = arr::get($_GET,'user_id');
        $user_email     = arr::get($_GET,'user_email');

        $params = [
                ':email' => $user_email,
                ':o_id' => $this->offices(),
                ':user_id' => $user_id,
        ];

        if((int) $user_id>0) {
            $sql_user = 'select u.id from users u '
                    . 'where true and '
                    . 'u.id = :user_id '
                    . 'and u.office_id in :o_id';

            $res_user = db::query(database::SELECT,$sql_user)
                    ->parameters($params)
                    ->execute()
                    ->as_array();

            if(isset($res_user[0]['id'])){
                $user_id=$res_user[0]['id'];
            }
        }

        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to   = isset($_GET['time_to']) ? $_GET['time_to'] : null;

        //пагинатор
        $page     = $this->request->param('id',1);
        $page     = max(1,$page);
        $offset   = $this->per_page * ($page - 1);

        if(!isset($time_from))
        {
            $time_from = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        }
        else
        {
            $time_from_arr = explode('-',$time_from);
            $time_from     = mktime(0,0,0,$time_from_arr[1],$time_from_arr[2],$time_from_arr[0]);
        }

        if($day_period = arr::get($this->day_period,person::$role)) {
            $limit = mktime(0,0,0,date('m'),date('d')-$day_period,date('Y'));
            if($limit>=$time_from) {
                $time_from=$limit;
            }
        }

        if(!isset($time_to))
        {
            $time_to = time();
        }
        else
        {
            $time_to_arr = explode('-',$time_to);
            $time_to     = mktime(23,59,59,$time_to_arr[1],$time_to_arr[2],$time_to_arr[0]);
        }

        $types=['user_payment','user_withdraw'];

        $params_games = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':office_id' => $this->offices(),
                ':user_id' => (int) $user_id,
                ':types' => $types,
        ];

        $sql_bets = 'select created, user_id, \'bet [\'||game||\']\' as type, balance,win-amount as sum, win, amount
            from bets where user_id = :user_id and created >= :time_from and created <= :time_to and office_id in :office_id';
        $res_bets = db::query(database::SELECT,$sql_bets)->parameters($params_games)->execute()->as_array('created');

        $res_payments=[];
        $res_bonuses=[];
        $res_compoints=[];

        if(person::$role=='sa') {
            $sql_payments = 'select created, person_id, \'payment\' as type,
                after-before as sum,
                after as balance, after-before as amount,
                0 as win
                from operations
                where updated_id = :user_id
                and "type" in :types
                and office_id in :office_id
                and created is not null
                and created >= :time_from and created <= :time_to';
            $res_payments = db::query(database::SELECT,$sql_payments)->parameters($params_games)->execute()->as_array('created');

            $sql_bonuses = 'select created, user_id, \'bonus\' as type,
                coalesce(substring(log FROM \'\{\"bonus_name\"\:\"([a-z_0-9]+)\"\'),type) as b_type,
                balance, bonus as win,
                bonus as sum, 0.00 as amount
                from bonuses where user_id = :user_id and
                payed=1 and created >= :time_from and
                created <= :time_to';
            $res_bonuses = db::query(database::SELECT,$sql_bonuses)->parameters($params_games)->execute()->as_array('created');

            $bonus_types = arr::pluck($res_bonuses,'type');
            array_unique($bonus_types);

//            $sql_compoints = 'select created, user_id, \'compoint\' as type,
//                count_swap*coeff as sum
//                from compoint_history
//                where user_id = :user_id and created >= :time_from and created <= :time_to';
//            $res_compoints = db::query(database::SELECT,$sql_compoints)->parameters($params_games)->execute()->as_array('created');

            $res_compoints=[];
        }
        else {
            $sql_payments = 'select created, person_id, \'payment\' as type,
                after-before as sum,
                0 as win,
                after as balance, after-before as amount
                from operations
                where updated_id = :user_id
                and "type" in :types
                and office_id in :office_id
                and created is not null
                and created >= :time_from and created <= :time_to';

            $res_payments = db::query(database::SELECT,$sql_payments)->parameters($params_games)->execute()->as_array('created');
        }

        $data=arr::merge($res_bets,$res_payments,$res_compoints,$res_bonuses);
        krsort($data);

//        var_dump($data);
//        exit;

        if(arr::get($_GET,'test')) {
            echo Debug::vars($data);

        }
        $page_data   = array
                (
                'total_items'    => count($data),
                'items_per_page' => $this->per_page,
                'current_page'   => array
                        (
                        'source' => 'route',
                        'key'    => 'id'
                ),
                'auto_hide'      => TRUE,
        );

        $labels=[
            'type'=>'type',
            'amount'=>'in',
            'win'=>'out',
            'sum'=>'win',
            'balance'=>'balance',
        ];

        $csv='';
        if($export){
            $head=[];

            $head[]='date';
            $head[]='type';
            $head[]='sum';
            $head[]='balance';
            $head[]='win';
            $head[]='amount';

            $body=[];
            foreach($data as $date => $row){
                    if($date=='Итого') continue;
                    if(isset($row['b_type']) && in_array($row['b_type'], ['bezdep_min10','bezdep_vager'])) continue;

                    $b = [date('Y-m-d H:i:s',$date)];
                    foreach($labels as $label => $ilable) {
                        if(isset($row['b_type']) AND $label=="type") {
                            $b[]=arr::get($row,$label,0)." - ".$row['b_type'];
                        }
                        else {
                            $b[]=is_numeric(arr::get($row,$label))?th::number_format(arr::get($row,$label),','):arr::get($row,$label);
                        }
                    }
                    $body[]=implode(';',$b);
            }
            $csv=array([implode(';',$head)], $body);
            th::to_csv($csv, $time_from, $time_to, $this->request->controller());
        }

        $data= array_slice($data, $offset, $this->per_page, TRUE);

        $terminals=[];

        if(false && THEME=='robot') {
            foreach(db::query(1,'select id, visible_name, msrc from users where bind_ip=:ip and parent_id is null order by msrc::int4')->param(':ip',arr::get($_SERVER,'REMOTE_ADDR','-1'))->execute()->as_array() as $ter) {
                $terminals[$ter['id']] = $ter['visible_name'];
            }
        }

        $this->handler_search($_GET);

        $view            = new View($this->sh . '/index');
        $view->page        = Pagination::factory($page_data)->render('pagination/floating');
        //
        $view->time_from     = $time_from;
        $view->time_to       = $time_to;
        $view->user_id       = $user_id;
        $view->user_email       = $user_email;

        $view->terminals = $terminals;

        $view->data        = $data;
        $view->list        = $this->list;
        $view->search      = $this->search;
        $view->search_vars = $this->search_vars;
        $view->labels       = $labels;
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
                'created',
                'type',
                'game',
                'bettype',
                'office_id',
                'amount_in',
                'amount_out',
                'count',
                'persent',
        ];

    }

}
