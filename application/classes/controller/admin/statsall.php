<?php

class Controller_Admin_Statsall extends Super
{

    public $mark       = 'Сводная статистика'; //имя
    public $model_name = 'statistics'; //имя модели
    public $sh         = 'admin/statsall'; //шаблон
    public $per_page   = 10;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function action_index()
    {
        $export     = isset($_GET['export']) ? $_GET['export'] : null;
        $office_id     = arr::get($_GET,'office_id',0);

        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to   = isset($_GET['time_to']) ? $_GET['time_to'] : null;

        if(!isset($time_from))
        {
            $time_from = mktime(0,0,0,date('m')-1,date('d'),date('Y'));
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

        $sql_bets = 'select date_part(\'epoch\',
                date_trunc(
                        \'day\',
                        to_timestamp(created) at time zone \'UTC\'
                )
            ) as created,sum(amount) as amount_in, sum(win) as amount_out, sum(amount-win) as win_inout
            from bets where office_id = :office_id and created >= :time_from and created <= :time_to group by 1';



        $params_games = [
                ':time_from' => $time_from,
                ':time_to'   => $time_to,
                ':office_id' => $office_id,
        ];

        $res_bets = db::query(database::SELECT,$sql_bets)->parameters($params_games)->execute()->as_array('created');

        $sql_balances = 'select date_part(\'epoch\',
                date_trunc(
                        \'day\',
                        to_timestamp("date") at time zone \'UTC\'
                )
            ) as created,sum(diff_balance) as balances
            from users_balances where "date" >= :time_from and office_id = :office_id and "date" <= :time_to group by 1';

        $res_balances = db::query(database::SELECT,$sql_balances)->parameters($params_games)->execute()->as_array('created');

        $sql_payments = 'select date_part(\'epoch\',
                date_trunc(
                        \'day\',
                        to_timestamp(p.created) at time zone \'UTC\'
                )
            ) as created,
            sum(case when p.amount>0 then p.amount else 0 end) as payment_in,
            sum(case when p.amount<0 then p.amount else 0 end) as payment_out,
            sum(p.amount)-sum(coalesce(p.commission,0)) as win_dh
            from payments p
            join users u on u.id = p.user_id
            where u.office_id = :office_id and p.status=30 and p.created is not null and p.created >= :time_from and p.created <= :time_to group by 1';

        $res_payments = db::query(database::SELECT,$sql_payments)->parameters($params_games)->execute()->as_array('created');

        $sql_operations = 'select date_part(\'epoch\',
                date_trunc(
                       \'day\',
                        to_timestamp(created) at time zone \'UTC\'
                )
            ) as created,
            sum(case when type=\'user_payment\' then amount else 0 end) as payment_in,
            sum(case when type=\'user_withdraw\' then -amount else 0 end) as payment_out,
            sum(amount) as win_dh
            from operations where created >= :time_from and created <= :time_to and office_id = :office_id and type in (\'user_payment\',\'user_withdraw\') group by 1';

        $res_operations = db::query(database::SELECT,$sql_operations)->parameters($params_games)->execute()->as_array('created');

        $sql_bonuses = 'select date_part(\'epoch\',
                date_trunc(
                        \'day\',
                        to_timestamp(coalesce(b.last_notification,b.created)) at time zone \'UTC\'
                )
            ) as created,coalesce(substring(log FROM \'\{\"bonus_name\"\:\"([a-z_0-9]+)\"\'),type) as type,sum(b.bonus) as bonus
            from bonuses b
            join users u on u.id = b.user_id
            where u.office_id = :office_id and b.payed=1 and coalesce(b.last_notification,b.created) >= :time_from and coalesce(b.last_notification,b.created) <= :time_to group by 1,2';

        $res_bonuses = db::query(database::SELECT,$sql_bonuses)->parameters($params_games)->execute()->as_array();

        $bonus_types = arr::pluck($res_bonuses,'type');
        array_unique($bonus_types);

        $formed_bonuses=[];
        foreach($res_bonuses as $row) {
            if(in_array($row['type'], ['bezdep_vager','bezdep_min10'])) {
                continue;
            }

            if(!isset($formed_bonuses[$row['created']])) {
                $formed_bonuses[$row['created']]=['sum_bonuses'=>0];
            }
            $formed_bonuses[$row['created']]['sum_bonuses']+=$row['bonus'];
            $formed_bonuses[$row['created']][$row['type']]=$row['bonus'];
        }

        $sql_compoints = 'select date_part(\'epoch\',
                date_trunc(
                        \'day\',
                        to_timestamp(c.created) at time zone \'UTC\'
                )
            ) as created,sum(c.count_swap*c.coeff) as comps
            from compoint_history c
            join users u on u.id = c.user_id
            where u.office_id = :office_id and c.created >= :time_from and c.created <= :time_to group by 1';

        $res_compoints = db::query(database::SELECT,$sql_compoints)->parameters($params_games)->execute()->as_array('created');

        $data=arr::merge($res_bets,$res_payments,$res_operations,$res_compoints,$formed_bonuses,$res_balances);
        krsort($data);

        $sumArray = array();

        foreach ($data as $k=>$subArray) {
            $data[$k]['check_sum']= th::number_format((float) arr::get($subArray,'win_dh',0)-(float) arr::get($subArray,'balances',0)-(float) arr::get($subArray,'win_inout',0)+(float) arr::get($subArray,'sum_bonuses',0)+(float) arr::get($subArray,'comps',0));

            $subArray['check_sum'] = $data[$k]['check_sum'];
            foreach ($subArray as $id=>$value) {
                if(!isset($sumArray[$id])) {
                    $sumArray[$id]=0;
                }
                $sumArray[$id]+=$value;
            }
        }

        $data['Итого']=$sumArray;

        $labels=[
//            'amount_in',
//            'amount_out',
            'check_sum',
            'win_dh',
            'balances',
            'win_inout',
            'sum_bonuses',
            'comps',
//            'payment_in',
//            'payment_out',
//            'bezdep_vager',
//            'bezdep_min10',
            'freespin',
            'activity',
            'payment',
            'for_error',
            'bezdep',
            'fixed_freespin',
        ];

        $labels = arr::merge($labels, $bonus_types);

        $csv='';
        if($export){
            $head=array_merge(['date'],$labels);
            $body=[];
            foreach($data as $game => $row){
                if($game=='Итого') {
                    continue;
                }
                $b = [date('m-d-Y',$game)];
                foreach($labels as $l) {
                    $b[]=th::number_format(arr::get($row,$l,0),',');
                }
                $body[]=implode(';',$b);
            }

            $csv=array([implode(';',$head)], $body);
            th::to_csv($csv, $time_from, $time_to, $this->request->controller());
        }

        /*$offices     = [0 => 'Все'];
        $sql_offices = <<<SQL
            Select o.id as office_id, c.code
            From offices o JOIN currencies c ON o.currency_id=c.id
SQL;
        $res_offices = db::query(1,$sql_offices)->execute()->as_array('office_id');

        foreach($res_offices as $off_id => $value)
        {
            $offices[$off_id] = $value['code'];
        }*/

        $offices = $this->offices();
        $offices[0]=__('Выберите ППС');
        ksort($offices);


        $this->handler_search($_GET);

        $view            = new View($this->sh . '/index');
        $view->offices       = $offices;
        $view->curr_office   = $office_id;
        //
        $view->time_from     = $time_from;
        $view->time_to       = $time_to;


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
