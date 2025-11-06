<?php

class Controller_Admin_User extends Super
{

    public $mark       = 'Пользователи'; //имя
    public $model_name = 'user'; //имя модели
    public $order_by   = array('created','desc nulls last');
    public $per_page   = 100;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    
    public function configure()
    {
        $this->search = [
                'id',
                'email',
                'msrc',
                'partner',
                'phone',
                'id_list',
        ];

        $this->list = [
                'id',
                'email',
                'last_login',
                'created',
//                'amount',
//                'bonus',
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
            $this->restrict('msrc');
            $this->restrict('phone');
            $this->restrict('id_list');
        }

        if(!in_array(person::$role,['analitic','agent','rmanager','manager'])) {
            $this->restrict('office_id');
        }

        $amount = new Vidget_Echo('amount',$this->model);
		$this->vidgets['amount'] = $amount;


        $block_user = new Vidget_Blockuser('blocked', $this->model);
        $list = [
            0 => 'Нет',
            1 => 'Да',
        ];
        $block_user->param('list', $list);
        $this->vidgets['blocked'] = $block_user;

        $code = new Vidget_Codelink('code', $this->model);
        $code->param('link', '/login/passcode?code=');
        $this->vidgets['code'] = $code;

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
            'msrc',
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

    /*
     * блокировка пользователя
     */
    public function action_block(){
        $user_id = $this->request->param('id');
        $message = arr::get($_POST, 'blocked_text');

        $parent = new Model_User($user_id);

        $accounts = orm::factory('user')->where('parent_id', '=', $user_id)->find_all();

        database::instance()->begin();

        if($parent->loaded()) {
            $parent->blocked = 1;
            $parent->blocked_text = $message;
            $this->calc_changes($parent, 'update');
            $parent->save();
            $this->log_changes();
        }

        foreach ($accounts as $user) {
            $user->blocked = 1;
            $user->blocked_text = $message;

            /*
             * ставка для списания баланса
             */
            $in = $user->amount();
            $out = 0;
            $game_type = 'blocked';
            $game = 'blocked';
            $bettype = 'block';

            $bet = new Model_Bet();
            $bet->user_id = $user->id;
            $bet->amount = $in;
            $bet->game_type = $game_type;
            $bet->game = $game;
            $bet->type = $bettype;
            $bet->come = 0;
            $bet->result = $message;
            $bet->office_id = $user->office_id;
            $bet->win = 0;
            $bet->game_id = 0;
            $bet->method = 'blocked_user';
            $bet->is_freespin = 0;
            $bet->balance = $in;
            $bet->save();

            //Сначала списывается основной баланс, затем - бонусный.
            $sql='update users
                set amount=0,
                bonus=0,
                sum_amount=sum_amount+:in,
                sum_win=sum_win+:out,
                updated = :updated
                ';

            $sql.='where id=:uid';

            db::query(1, $sql)->param(':updated', time())
                    ->param(':in', (float) $in)
                    ->param(':out', (float) $out)
                    ->param(':uid', $user->id)
                    ->execute();

            //для счетчиков
            $sql = 'update counters set "in"="in"+:in, "out"="out"+:out where office_id = :oid and game = :game and type=:type and "bettype"=:bettype RETURNING id';
            $rows = db::query(Database::UPDATE, $sql)->param(':in', (float) $in)
                    ->param(':out', (float) $out)
                    ->param(':oid', $user->office_id)
                    ->param(':type', $game_type)
                    ->param(':game', $game)
                    ->param(':bettype', $bettype)
                    ->execute();

            if ($rows == 0) {
                $sql = 'insert into counters ( "in", "out", office_id,  type, game,  "bettype")
                                        values(:in,    :out, :oid,       :type, :game, :bettype)';

                db::query(Database::INSERT, $sql)->param(':in', (float) $in)
                        ->param(':out', (float) $out)
                        ->param(':oid', $user->office_id)
                        ->param(':type', $game_type)
                        ->param(':game', $game)
                        ->param(':bettype', $bettype)
                        ->execute();
            }

            $this->calc_changes($user, 'update');
            $user->save();
            $this->log_changes();
        }

        database::instance()->commit();

		$this->request->redirect($this->request->referrer());
	}

    public function handler_search($vars) {
        $model = parent::handler_search($vars);
        $model->distinct('*')
                ->select('users.amount',DB::expr('users.amount+users.bonus'),DB::expr('users.sum_in-users.sum_out'))
                ->join('users')
                ->on('users.parent_id', '=', 'user.id');

        $model->where('users.office_id','in',$this->offices());
        
        if ($day_period = arr::get($this->day_period, person::$role))
        {
            $model->where('users.last_bet_time','>=',time()-24*3600*$day_period);
        }
        
        $model->where('user.bind_ip','is',null);
        
        return $model; //rub, usd online
    }
}
