<?php

class Controller_Pay extends Controller_Base {

    public $need_auth = true;
    public $auto_render = false;

    protected $_strict_payments = ['-1'];

    public function before()
    {

        parent::before();	
	if(auth::$user_id==540616) {
		$this->_strict_payments = ['-1'];
	}

    }

    public function action_in() {
        $sys = new Model_Payment_System;
        $systems = $sys->where('use', '=', '1')
                ->where('payment_system.id','not in', $this->_strict_payments)
                ->where('direction', '=', 'in')
                ->join('office_paysystems')->on('payment_system.id', '=', 'office_paysystems.paysys_id')
                ->where('office_paysystems.office_id', '=', OFFICE)
                ->order_by('sort_index')
                ->find_all();

        $bonuses = db::query(1, 'select distinct bc.* from bonus_codes bc '
            . 'left join bonus_codes_entered bce on bce.code_id = bc.id '
            . 'where bc.type in :types and bc.count>0 and min_sum_pay>0 and bc.share_prize = 0 '
            . 'and ((bce.used is null or bce.used = 0 or bc.type is null) or bce.user_id is null or bce.user_id != :user_id) '
            . 'and bc.time>:time and bc.office_id=:o_id order by sort_index,time limit 6')
            ->param(':user_id', auth::$user_id)
            ->param(':types', ['freespin',null,'unique_user','all'])
            ->param(':o_id', auth::user()->office_id)
            ->param(':time', time())
            ->execute(NULL, 'Model_Bonus_Code');

        $view = new View('site/payment/form/in');
        $view->bonuses = $bonuses;
        $view->min_sum_pay = auth::user()->office->min_sum_pay;
        $view->groups = kohana::$config->load('static.payment_groups');
        $view->systems = $systems;
        $games = th::gamelist();
        $view->games = $games;
        $this->response->body($view);
    }

    public function action_out() {
        if (auth::parent_acc()->phone_confirm==0 OR auth::parent_acc()->email_confirm==0){
			$view=new View('site/payment/contacts');
		}
		else{
			$view=new View('site/payment/form/out');

            $sys=new Model_Payment_System;
            $office = new Model_Office(OFFICE);

            $view->min_sum_pay = $office->min_sum_pay;
			$view->systems=$sys->where('use','=',1)->where('payment_system.id','not in', $this->_strict_payments)->where('direction','=','out')
                    ->join('office_paysystems')->on('payment_system.id', '=', 'office_paysystems.paysys_id')
                    ->where('office_paysystems.office_id', '=', OFFICE)->find_all();
            /*
             * считаем доступную сумму для вывода
             */
            $aviable = auth::user()->amount;
            if (auth::user()->drop_limit('amount')>0 AND auth::user()->drop_limit('win')>0) {
                $aviable = (10*$aviable)/11;
            }
            $view->aviable = $aviable;
		}
		$this->response->body($view->render());
    }


}
