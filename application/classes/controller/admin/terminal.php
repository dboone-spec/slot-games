<?php

class Controller_Admin_Terminal extends Super
{

    public $mark       = 'Терминалы'; //имя
    public $model_name = 'terminal'; //имя модели
    public $order_by   = array('created','desc nulls last');
    public $per_page   = 100;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public $day_period = [
            'kassa' => 5,
            'administrator' => 5,
            'manager' => 5,
            'rmanager' => 5,
            'agent' => 5,
    ];

    public function before() {
        parent::before();
        if(count($this->order_by) ) {
            switch ($this->order_by[0]) {
                case 'balances':
                    $this->order_by[0] = 'users.amount';
                    if(person::$role=='sa') {
                        $this->order_by[0] = DB::expr('users.amount+users.bonus');
                    }
                    break;
                case 'sum_diff':
                    $this->order_by[0] = DB::expr('users.sum_in-users.sum_out');
                    break;
            }
        }
    }

    public function configure()
    {
        $this->search = [
                'id',
                'msrc'
        ];

        $this->list = [
                'id',
                'visible_name',
                'bind',
                'msrc',
                'blocked',
                'bets_arr',
                'email',
                'last_login',
                'created',
                'balances',
                'bonusbreak',
                'bonuscurrent',
                'userinfo',
                'sum_win',
                'sum_amount',
                'sum_in',
                'sum_out',
                'sum_diff',
                'sum_bonus',
                'partner',
                'comp_current',
                'office_id',
        ];

        $this->show = [
                'id',
                'name',
                'email',
                'code',
                'last_login',
                'visible_name',
                'created',
                'balances',
//                'amount',
                'getspam',
//                'bonus',
                'bonusbreak',
                'bonuscurrent',
                'phone',
                'phone_confirm',
                'phone_code',
                'sum_win',
                'sum_amount',
                'sum_in',
                'sum_out',
                'sum_bonus',
                'email_confirm',
                'last_confim_email',
                'msrc',
                'dsrc',
                'referal_link',
                'comp_current',
                'updated',
                'blocked',
                'blocked_text',
                'last_bonus_type',
                'last_bonus',
                'partner',
                'autopay',
                'email_valid',
        ];

        if(person::$role!='sa') {
            $this->restrict('email');
            $this->restrict('partner');
            $this->restrict('compoint');
            $this->restrict('bonusbreak');
            $this->restrict('bonuscurrent');
            $this->restrict('sum_bonus');
            $this->restrict('comp_current');
            $this->restrict('userinfo');
            $this->restrict('phone');
            $this->restrict('id_list');
        }

        if(!in_array(person::$role,['analitic','agent','rmanager','manager'])) {
            $this->restrict('office_id');
        }

        $amount = new Vidget_Echo('amount',$this->model);
        $this->vidgets['amount'] = $amount;

        $terminal_bind = new Vidget_Terminalbind('id',$this->model);
        $this->vidgets['bind'] = $terminal_bind;

        $msrc = new Vidget_Selectdyn('msrc',$this->model);

        $fields = [];
        $searchfields=[];

        $exists_msrc = array_keys(db::query(1, 'select msrc from users where bind_ip = :bind_ip and (msrc is not null or msrc != \'\' )')
                ->param(':bind_ip',$_SERVER['REMOTE_ADDR'])
                ->execute()
                ->as_array('msrc'));

        for($i=1;$i<=99;$i++) {
            $searchfields[$i]=str_pad($i,2,'0',STR_PAD_LEFT);
            if(in_array($i,$exists_msrc)) {
                continue;
            }
            $fields[str_pad($i,2,'0',STR_PAD_LEFT)]=str_pad($i,2,'0',STR_PAD_LEFT);
        }

        $msrc->param('fields',$fields);
        $msrc->param('searchfields',$searchfields);
        $this->vidgets['msrc'] = $msrc;

        $block_user = new Vidget_CheckBox('blocked', $this->model);
        $list = [
            0 => 'Нет',
            1 => 'Да',
        ];

        $block_user->param('list', $list);
        $this->vidgets['blocked'] = $block_user;

        $code = new Vidget_Codelink('code', $this->model);
        $code->param('link', '/login/passcode?code=');
        $this->vidgets['code'] = $code;

        $bets_arr = new Vidget_Input('bets_arr',$this->model);
        $bets_arr->param('default',implode(',',[1,2,5,10,20,50,100,0.1,0.2,0.5]));
        $bets_arr->param('can_edit',true);
        $this->vidgets['bets_arr']=$bets_arr;


        $balances = new Vidget_Currencystat('balances', $this->model);
        $fields = ['amount', 'bonus'];
        if(Person::$role!='sa') {
            $fields = 'amount';
        }
        $balances->param('fields', $fields);
        $this->vidgets['balances'] = $balances;

        $bonuscurrent = new Vidget_Currencystat('bonuscurrent', $this->model);
        $bonuscurrent->param('fields', 'bonuscurrent');
        $this->vidgets['bonuscurrent'] = $bonuscurrent;

        $bonusbreak = new Vidget_Currencystat('bonusbreak', $this->model);
        $bonusbreak->param('fields', 'bonusbreak');
        $this->vidgets['bonusbreak'] = $bonusbreak;

        $sum_win = new Vidget_Currencystat('sum_win', $this->model);
        $sum_win->param('fields', 'sum_win');
        $this->vidgets['sum_win'] = $sum_win;

        $sum_amount = new Vidget_Currencystat('sum_amount', $this->model);
        $sum_amount->param('fields', 'sum_amount');
        $this->vidgets['sum_amount'] = $sum_amount;

        $sum_in = new Vidget_Currencystat('sum_in', $this->model);
        $sum_in->param('fields', 'sum_in');
        $this->vidgets['sum_in'] = $sum_in;

        $sum_out = new Vidget_Currencystat('sum_out', $this->model);
        $sum_out->param('fields', 'sum_out');
        $this->vidgets['sum_out'] = $sum_out;

        $sum_bonus = new Vidget_Currencystat('sum_bonus', $this->model);
        $sum_bonus->param('fields', 'sum_bonus');
        $this->vidgets['sum_bonus'] = $sum_bonus;

        $sum_diff = new Vidget_Currencystat('sum_diff', $this->model);
        $this->vidgets['sum_diff'] = $sum_diff;

        $partner = new Vidget_Integer('partner', $this->model);
        $this->vidgets['partner'] = $partner;

        $id_list = new vidget_arraysearch('id_list', $this->model);
        $this->vidgets['id_list'] = $id_list;

        $id_list = new Vidget_Userinfo('id', $this->model);
        $this->vidgets['userinfo'] = $id_list;

        $timestamps = [
            'last_confim_email',
            'created',
            'updated',
            'last_login'
        ];

        foreach ($timestamps as $field) {
            $this->vidgets[$field] = new Vidget_Timestampecho($field, $this->model);
        }

        $check_boxes = [
            'autopay',
            'email_valid',
            'phone_confirm',
            'email_confirm',
            'getspam',
        ];

        foreach ($check_boxes as $field) {
            $this->vidgets[$field] = new Vidget_CheckBox($field, $this->model);
        }

        $no_edit_fields = [
            'id',
            'name',
            'email',
            'visible_name',
            'phone',
            'phone_code',
            'dsrc',
            'last_bonus_type',
            'last_bonus',
            'partner',
            'referal_link',
            'comp_current',
        ];

        foreach ($no_edit_fields as $field) {
            $this->vidgets[$field] = new Vidget_Echo($field, $this->model);
        }
    }

    public function action_bind(){

        $u = new Model_User($this->request->param('id'));
        if($u->loaded() && !$u->office_id) {

            $db = Database::instance();
            $db->begin();

            try {
                $u->office_id = person::user()->office_id;
                $u->save()->reload();
                $u->office->white_ips = array_merge($u->office->white_ips,[$_SERVER['REMOTE_ADDR']]);
                auth::create_office_account($u);
                $db->commit();
            } catch (Exception $ex) {
                $db->rollback();
                throw $ex;
            }


        }
        $this->request->redirect($this->request->referrer());
    }

    public function handler_search($vars) {
        $model = parent::handler_search($vars);
        $model->distinct('*')
                ->select('users.amount',DB::expr('users.amount+users.bonus'),DB::expr('users.sum_in-users.sum_out'))
                ->join('users','left')
                ->on('users.parent_id', '=', 'terminal.id');

        $model->where('terminal.bind_ip','=',arr::get($_SERVER,'REMOTE_ADDR','-1'));
        $model->where('users.blocked','=',0);
        return $model; //rub, usd online
    }
}
