<?php


class Controller_Payment extends Controller_Base {

	public $auto_render=false;
	public $need_auth=true;

	public function before() {
		parent::before();
		if (empty(auth::$user_id)){
			throw new Exception;
		}
	}

	public function action_in1(){

		$view=new View('site/payment/in');
		$view->select=Kohana::$config->load('payment');
		$this->response->body($view->render());

	}


	public function action_in(){

        $sys=new Model_Payment_System;
        $systems = $sys->where('use','=','1')->where('direction','=','in')
            ->join('office_paysystems')->on('payment_system.id', '=', 'office_paysystems.paysys_id')
            ->where('office_paysystems.office_id', '=', OFFICE)->find_all();

        if($this->request->is_ajax()) {
            $view=new View('site/payment/incase');
            $bonuses = ORM::factory('Bonus_Code')->where('show','=',1)->and_where('office_id', '=', auth::user()->office_id)->find_all();
            $view->bonuses = $bonuses;

            $this->response->body($view->render());
        } else {
            $this->auto_render = true;
            $this->template = View::factory($this->template);

            if(THEME == 'sport') {
                $view = new View('site/payment/static');

                $count_all_systems = 0;
                $systems_groups = [];

                foreach ($systems as $s) {
                    $systems_groups[$s->group][] = $s;
                    $count_all_systems += 1;
                }

                $system_names = [
                    'cards' => __('Банковские карты'),
                    'emoney' => __('Электронные кошельки'),
                    'mobile' => __('Мобильные платежи')
                ];

                $view->systems = $systems_groups;
                $view->system_names = $system_names;
                $view->count_all_systems = $count_all_systems;

                $this->template->content = $view;

                return;
            }

            $view = new View('site/payment/static');
            $view->systems = $systems;

            $this->template->content = $view;
        }

	}


	public function action_inframe(){
		$view=new View('site/payment/inframe');
		$this->response->body($view->render());

	}



	public function action_out(){

		if (auth::parent_acc()->phone_confirm==0 OR auth::parent_acc()->email_confirm==0){
			$view=new View('site/payment/contacts');
		}
		else{
			$view=new View('site/payment/out');
		}
		$this->response->body($view->render());

	}

    public function action_payout(){

        $sys=new Model_Payment_System;
        $sys = $sys->where('use','=','1')->where('direction','=','out')
            ->join('office_paysystems')->on('payment_system.id', '=', 'office_paysystems.paysys_id')
            ->where('office_paysystems.office_id', '=', OFFICE)->find_all();

        if (auth::parent_acc()->phone_confirm==0 OR auth::parent_acc()->email_confirm==0){
			$view=new View('site/payment/contacts');
		}
		else{
			$view=new View('site/payment/out');

            $count_all_systems = 0;
            $systems = [];

            foreach ($sys as $s) {
                $systems[$s->group][] = $s;
                $count_all_systems += 1;
            }

            $system_names = [
                'cards' => __('Банковские карты'),
                'emoney' => __('Электронные кошельки'),
                'mobile' => __('Мобильные платежи')
            ];

            $view->systems = $systems;
            $view->system_names = $system_names;
            $view->count_all_systems = $count_all_systems;
		}

        $this->auto_render = true;
        $this->template = View::factory($this->template);

        $this->template->content = $view;
	}

    public function action_history(){

		$view=new View('site/payment/history');
		//отображаемые статусы в истории ставок
		$view_statuses = [10, 20, 30, 40];
		$p=new Model_Payment;
		$view->pays=$p->where('user_id','=',auth::$user_id)
						->where('created','>',time()-60*60*24*14)
						->and_where('status', 'in', $view_statuses)
						->order_by('created')
						->find_all();

        $sql_bonuses = <<<SQL
            Select id, bonus, created,
                CASE
                    WHEN type = 'activity' AND referal_id <> 0 THEN 'Бонус за игровую активность с реферала '
                    WHEN type = 'activity' THEN 'Бонус за игровую активность'
                    ELSE 'Бонус'
                END as type,
                CASE
                    WHEN type = 'activity' AND referal_id <> 0 THEN referal_id
                END as referal_id
            From bonuses
            Where user_id = :user_id AND payed = 1
SQL;
        $bonuses = db::query(database::SELECT, $sql_bonuses)->param(':user_id', auth::$user_id)->execute()->as_array();

        $view->bonuses = $bonuses;
		$view->status=kohana::$config->load('status.paystatus');
		$this->response->body($view->render());

	}


	public function action_go(){

        Cookie::set('user_domain', $_SERVER['HTTP_HOST']);

		$sys=arr::get($_POST,'paysys_current',-1);
		$sys=new Model_Payment_System($sys);
		if (!$sys->loaded() or $sys->use!=1 or $sys->direction!='in'){
			throw new HTTP_Exception_404;
		}

        $amount = (float) arr::get($_POST,'amount',500);

        $min_sum_pay = auth::user()->office->min_sum_pay;

        if($amount < $min_sum_pay) {
            $this->response->body(json_encode(['error'=>__('Минимальная сумма пополнения - ').$min_sum_pay . auth::user()->currency()]));
            return;
        }

        $a=arr::get($_POST,$sys->id,[]);

		$data=[];
		foreach($sys->attr->find_all() as $field){
			if(!isset($a[$field->name]) or empty($a[$field->name])){
				$ans['error']=__("Не указано необходимое поле ").$field->visible_name;
				$this->response->body(json_encode($ans));
				return null;
			}

			$f=$a[$field->name];
			if (!empty($field->reg_expr) and  !preg_match($field->reg_expr,$f)){
				$ans['error']=__("Неправильно заполнено поле ").$field->visible_name.", ".__("пример заполнения ").$field->example;
				$this->response->body(json_encode($ans));
				return null;
			}
			$data[$field->name]=$f;
		}

		$p=new Model_Payment();
		$p->user_id=auth::$user_id;
		$p->status=0;
		$p->amount= $amount;
		$p->gateway=$sys->gate;
		$p->currency= strtolower(auth::user()->office->currency->code);
        $p->data=json_encode($data);
		$p->payment_system_id=$sys->id;

        if (isset($_POST['havebonus'])){
            $p->bonus_code='default';
        }

        /*
         * чекаем последний введенный бонус код пользователем
         */
        if (auth::user()->last_bonus_code){
            $bonus_code = new Model_Bonus_Code(["id" => auth::user()->last_bonus_code]);
            if($bonus_code->loaded() AND $bonus_code->type != 'bezdep') {
                $success_pay_sum = $bonus_code->check_pay_sum($amount, auth::$user_id);

                if($success_pay_sum === true) {
                    $p->bonus_code = $bonus_code->name;

                    if($bonus_code->count > 0 AND !in_array($bonus_code->type, ['all', 'user_unique'])) {
                        $bonus_code->count = --$bonus_code->count;
                        $bonus_code->save();
                    }
                    auth::user()->last_bonus_code = null;
                    auth::user()->save();
                }
//                else {
//                    /*
//                     * если юзер пытается пополнить счет на сумму меньше заданной в бонус коде
//                     * уведомляем пользователя
//                     */
//
//                    if(arr::get($_POST,'is_ok',-1) < 0) {
//                        $this->response->body(json_encode([
//                            "error" => "Для активации бонус кода {$bonus_code->name} сумма платежа должна быть не менее {$bonus_code->min_sum_pay} руб.",
//                            "is_ok" => 1
//                        ]));
//                        return;
//                    }
//                }
            }
        }
		$p->save();

		if ($p->gateway=='payeer'){
			$a=[];
			$config=Kohana::$config->load('secret.payeer');
			$a['m_shop']=$config['m_shop'];
			$a['m_orderid']=$p->id;
			$a['m_amount']=(string) number_format($p->amount, 2, '.', '');
			$a['m_curr']=auth::user()->office->currency->code;
			$a['m_desc']=base64_encode(__('Пополнение счета'));

			$arHash=$a;
			$arHash['m_key']=$config['m_key'];
			$a['m_sign']=strtoupper(hash('sha256', implode(":", $arHash)));

            $a['form'] = [
                "curr[{$sys->id}]" => auth::user()->office->currency->code,
                'ps' => $sys->id
            ];

			$s=Kohana::$config->load('static.sitepaygo_payeer').url::query($a,false);
			$this->response->body(json_encode(['link' => $s, 'payment_id' => $p->id]));
            return;
		}


		if ($p->gateway=='interkassa'){
			$a=[];
			$config=Kohana::$config->load('secret.interkassa');
            $a['e'] = 'ikassabilling';
			$a['ik_co_id']=$config['kassa_id'];
			$a['ik_pm_no']=$p->id;
			$a['ik_am']=$p->amount;
			$a['ik_cur']=auth::user()->office->currency->code;
			$a['ik_act']='payway';
			$a['ik_desc']='Пополнение счета';
			$a['ik_pw_via']=$sys->name;
            $a['domain']= $_SERVER['HTTP_HOST'];

			$s=Kohana::$config->load('static.sitepaygo_interkassa') .url::query($a,false);
            $this->response->body(json_encode(['link' => $s, 'payment_id' => $p->id]));
            return;

		}


		if ($p->gateway=='trio'){


            $trio = new trio();
            $trio->addParam('amount',$p->amount)
                ->addParam('payway',$sys->name);

            if(!$trio->preinvoice()) {
                $this->response->body(json_encode([
                    "error" => Arr::get($trio->getErrors(),0,__("Ошибка платежа. Обратитесь в техническую поддержку"))
                ]));
                return;
            }

            //TODO тут надо вставить проверку, соответствуют ли поля в data и в их ответе в addons

            $trio->addParam('amount',$p->amount)
                ->addParam('payway',$sys->name)
                ->addParam('shop_invoice_id',$p->id)
                ->addParam('domain',$_SERVER['HTTP_HOST']); //проверить доп. параметр

            foreach($data as $f=>$name) {
                $trio->addParam($f,$name);
            }

            if(!$result = $trio->invoice()) {
                $this->response->body(json_encode([
                    "error" => Arr::get($trio->getErrors(),0,__("Ошибка платежа. Обратитесь в техническую поддержку"))
                ]));
                return;
            }

            $p->external_id=$result['invoice_id'];
            $p->save();

            if($result['method']=='OFFLINE') {
                $this->response->body(json_encode(['link' => '', 'info' => $result['data']['ru']]));
                return;
            }

            if($result['method']=='POST' AND isset($result['data']['iframe_source'])) {
                $this->response->body(json_encode(['iframe_source'=>$result['data']['iframe_source']]));
                return;
            }

            if(false && $result['method']=='POST') {
                $this->response->body(json_encode($result));
                return;
            }

            $this->response->body(json_encode(['link' => $result['source'].url::query($result['data'],false), 'payment_id' => $p->id]));
            return;

		}

		if ($p->gateway=='ex4money'){
			$n = new ex4money();

            $extra['extra_failurl']='https://abcde.loc/fail';
            $extra['extra_successurl']='https://abcde.loc/success';

			$r = $n->in($p->amount,$p->id,$sys->name,$data,$extra);

			if(!$r) {
				$this->response->body(json_encode([
					"error" => Arr::get($n->getErrors(),0,'Ошибка платежа. Обратитесь в техническую поддержку')
				]));
				return;
			}

            $p->external_id=$r['payment_id'];
            $p->save();

			$this->response->body(json_encode($r));
                	return;

		}

		if ($p->gateway=='piastrix'){


            $piastrix = new piastrix();
            $piastrix->addParam('amount',$p->amount)
                ->addParam('payway',$sys->name);

            $piastrix->addParam('currency',auth::user()->office->currency->iso_4217);

            $piastrix->addParam('shop_order_id',$p->id);

			if(auth::$user_id==540616) {
				$piastrix->addParam('test2','asd');
			}

            if(!$piastrix->preinvoice()) {
                $this->response->body(json_encode([
                    "error" => Arr::get($piastrix->getErrors(),0,__("Ошибка платежа. Обратитесь в техническую поддержку"))
                ]));
                return;
            }

            //TODO тут надо вставить проверку, соответствуют ли поля в data и в их ответе в addons

            $piastrix->addParam('currency',auth::user()->office->currency->iso_4217);

            $piastrix->addParam('amount',$p->amount)
                ->addParam('payway',$sys->name)
                ->addParam('shop_order_id',$p->id)
                ->addParam('domain',$_SERVER['HTTP_HOST']); //проверить доп. параметр

            foreach($data as $f=>$name) {
                $piastrix->addParam($f,$name);
            }

            if(!$result = $piastrix->invoice()) {
                $this->response->body(json_encode([
                    "error" => Arr::get($piastrix->getErrors(),0,__("Ошибка платежа. Обратитесь в техническую поддержку"))
                ]));
                return;
            }

            $p->external_id=$result['id'];
            $p->save();

            if($result['method']=='OFFLINE') {
                $this->response->body(json_encode(['link' => '', 'info' => $result['data']['ru']]));
                return;
            }

            if($result['method']=='POST' AND isset($result['data']['iframe_source'])) {
                $this->response->body(json_encode(['iframe_source'=>$result['data']['iframe_source']]));
                return;
            }

            if(false && $result['method']=='POST') {
                $this->response->body(json_encode($result));
                return;
            }

            $this->response->body(json_encode(['link' => $result['url'].url::query($result['data'],false), 'payment_id' => $p->id]));
            return;

		}

		if ($p->gateway=='freeob'){
            $f = new freeobmen();
            $data = $f->in($p->amount,$p->id,$sys->name,$sys->currency);

            if(!count($f->getErrors())) {

                $p->external_id=$data['id'];
                $p->save();

                if(isset($data['url'])) {
                    $this->response->body(json_encode(['link' => $data['url'], 'payment_id' => $p->id]));
                    return;
                }

                //crypt

            }

            $this->response->body(json_encode([
                    "error" => Arr::get($f->getErrors(),0,__("Ошибка платежа. Обратитесь в техническую поддержку"))
            ]));
        }

		if ($p->gateway=='freekassa'){

            if($amount>500) {
                $amount = $amount * (1 - ($sys->commission_persent / 100));
            }


			$a=[];
			$config=Kohana::$config->load('secret.freekassa');
			$a['m']=$config['shop_id'];
			$a['oa']=$amount;
			$a['o']=$p->id;

			$a['s'] = md5($a['m'].':'.$a['oa'].':'.$config['secretkey1'].':'.$a['o']);

			$a['i']=$p->currency; //валюта = тип оплаты. http://www.free-kassa.ru/docs/api.php#ex_currencies
			$a['lang']='ru';

			$s=Kohana::$config->load('static.sitepaygo_freekassa') .url::query($a,false);
			$this->response->body(json_encode(['link' => $s, 'payment_id' => $p->id]));
            return;

		}

		throw new HTTP_Exception_404;

	}


	public function action_nobonus(){

            $ans = [];
            $ans['error'] = 1;

            if(auth::user()->last_bonus_code) {
                $b = new Model_Bonus_Code(auth::user()->last_bonus_code);
                if(!in_array($b->type, ['bezdep']) ) {
                    auth::user()->last_bonus_code = null;
                    auth::user()->save();
                }
                $ans['error'] = 0;
                /*
                 * пишем пользователя, его ip -шник  и id бонус кода
                 */
                /*$entered_code = new Model_Bonus_Codeentered([
                    "user_id" => auth::$user_id,
                    "ip" => $_SERVER['REMOTE_ADDR'],
                    "code_id" => $b->id,
                ]);

                if($entered_code->loaded()) {
                    $entered_code->delete();
                }*/
            }
            $this->response->body(json_encode($ans));
        }

	public function action_bonus(){
		$b=new Model_Bonus_Code([
            'name' => UTF8::strtolower($this->request->param('id')),
            'office_id' => auth::user()->office_id,
        ]);
		$ans=['error'=>1];
        $amount = $this->request->post('amount')??0;

        if($b->loaded()) {
            $used_code = auth::user()->check_use_bonus_code($b);
            $used_code_with_ip = auth::user()->ip_used_bonus_code($b->type, $b->id);
            $used_bezdep = auth::user()->used_bezdep($b->id, $b->type);
            $min_sum_pay = $b->check_pay_sum($amount, auth::user()->id);
            $profile_required = auth::parent_acc()->check_profile();


            if(($b->type == 'bezdep' || $b->type == 'bezdep_freespin') AND !$profile_required) {
                $ans['text'] = __("Заполните профиль");
            } elseif(false && $min_sum_pay !== true) {
                $ans['text'] = __("Минимальная сумма пополнения для активации бонус кода "). $min_sum_pay . auth::user()->currency() ;
            } elseif($used_code OR $used_bezdep) {
                $ans['text'] = __('Невозможно повторно использовать бонус код');
            } elseif($used_code_with_ip) {
                $ans['text'] = __('Бонус код недоступен');
            } elseif ($b->user_id AND auth::$user_id != $b->user_id){
                $ans['text'] = __('Вы не можете использовать данный бонус код');
            } elseif ($b->created > time()){
                $ans['text'] = __('Акция начнется через ') . th::n1(($b->created - time())/3600) . __(' ч.');
            } elseif ($b->count!=0 and ($b->time>time() or $b->time<0)){
                if(!in_array($b->type, ['bezdep']) ) {
                    auth::user()->last_bonus_code = $b->id;
                    auth::user()->save();
                }
                /*
                 * пишем пользователя, его ip -шник  и id бонус кода
                 */
                $entered_code = new Model_Bonus_Codeentered([
                    "user_id" => auth::$user_id,
                    "ip" => $_SERVER['REMOTE_ADDR'],
                    "code_id" => $b->id,
                ]);

                if(!$entered_code->loaded()) {
                    $entered_code->ip = $_SERVER['REMOTE_ADDR'];
                    $entered_code->user_id = auth::$user_id;
                    $entered_code->code_id = $b->id;
                    $entered_code->save();
                    $b->pay_bezdep_bonus();
                }

				$ans['error']=0;
                $ans['type'] = $b->type;
                $ans['text'] = $b->success_text_message();
            } else {
                $ans['text'] = __('Время действия бонус кода истекло');
            }
        } else {
            $ans['text'] = __('Неактуальный бонус код');
        }

		$this->response->body(json_encode($ans));

	}

    public function action_check() {
        $payment_id = $this->request->param("id");

        $ans = [
            "success" => 0,
            "status" => 0
        ];

        $payment = new Model_Payment($payment_id);
        if($payment->loaded()) {
            $ans["success"] = 1;
            $ans["status"] = $payment->status;
        }

        $this->response->body(json_encode($ans));
    }

	public function action_phone(){
		$phone=arr::get($_GET,'phone');
		$phone=th::clearphone($phone);
		$ans=['error'=>1];

		if (th::checkphone($phone)){
			$ans['text']=__('Неверный номер телефона ').$phone;
			$this->response->body(json_encode($ans));
			return null;
		}

        $user_with_phone = new Model_User([
            'phone' => $phone,
            'phone_confirm' => 1,
        ]);

        if($user_with_phone->loaded()) {
            $ans['text']=__('Номер уже используется другим пользователем');
			$this->response->body(json_encode($ans));
			return null;
        }

		if (auth::parent_acc()->last_sms_send+60*60>time()){
			$ans['text']=__('СМС можно отправлять не чаще раза в час');
			$this->response->body(json_encode($ans));
			return null;
		}


		$code=mt_rand(10000,99999);
		auth::parent_acc()->phone=$phone;
		auth::parent_acc()->phone_code=$code;
		auth::parent_acc()->last_sms_send=time();
		auth::parent_acc()->save();

		if (th::smssend($phone,$code)){
            $ans['error']=0;
            $ans['text']=__('Смс отправлено на указанный номер телефона');
        }
		else{
			$ans['text']=__('Не удалось отправить СМС. Попробуйте позже');
		}

		$this->response->body(json_encode($ans));

	}


	public function action_code(){
		$code=arr::get($_GET,'code');

		$ans['error']=1;
		$ans['text']=__('Неверный код подтверждения');

		if (auth::parent_acc()->phone_code==$code){
			$ans['error']=0;
            $ans['email_confirm']=auth::parent_acc()->email_confirm;
			$ans['text']='';
			auth::parent_acc()->phone_confirm=1;
			auth::parent_acc()->save();

		}


		$this->response->body(json_encode($ans));

	}

    public function action_double() {

		if ($this->request->method()!="POST"){
			throw new HTTP_Exception_404;
		}

        $ans = [
            'error' => 1,
            'win' => 0,
            'text' => __('Ошибка при выводе средств'),
        ];

        $amount = intval(arr::get($_POST,'amount',0));
        $come = arr::get($_POST,'card',0);

        if($amount>0 && $come) {
            $d = new Double_Payout();
            $d->set_amount($amount);
            $d->bet($come);

            $ans['error']=0;
            $ans['win']=$d->result();
            $ans['text']='';
        }

        $this->response->body(json_encode($ans));

    }

	public function action_moneyout(){

		if ($this->request->method()!="POST"){
			throw new HTTP_Exception_404;
		}

		$amount=floor(arr::get($_POST,'amount',0));
		$com10=arr::get($_POST,'com10',-1);
//		$nobonus=arr::get($_POST,'nobonus',-1);

		$ans['error']=1;
		$ans['text']=__('Ошибка при выводе средств');

		if (!is_numeric($amount) or $amount<=0){
			$ans['text']=__('Неверная сумма вывода');
			$this->response->body(json_encode($ans));
			return null;
		}

		if (auth::user()->bonus>0){
			$ans['text']=__('Для вывода выигрыша необходимо отыграть все бонусы');
			$this->response->body(json_encode($ans));
			return null;
		}

		if ($com10==-1 and (auth::user()->drop_limit('amount')>0 && auth::user()->drop_limit('win')>0)){
			$ans['text']=__('Для вывода выигрыша необходимо согласиться с комиссией при выводе');
			$this->response->body(json_encode($ans));
			return null;
		}

		$have=auth::user()->amount()-auth::user()->bonus;
        /*
         * рассчитываем сколько пользователь сможет
         * вывести без учета 10% -ой комиссии
         */
        if (auth::user()->drop_limit('amount')>0 AND auth::user()->drop_limit('win')>0) {
            $have = (10*$have)/11;
        }

		if ($have<$amount){
			$ans['text']=__('Вы не можете вывести больше чем ').floor($have).' '.auth::user()->currency();
			$this->response->body(json_encode($ans));
			return null;
		}

		$sys=arr::get($_POST,'paysys_current',-1);
		$sys=new Model_Payment_System($sys);
		if (!$sys->loaded() or $sys->use!=1){
			$ans['text']=__('Выбранная вами система для вывода сейчас не доступна');
			$this->response->body(json_encode($ans));
			return null;
		}

		//проверяем мин. сумму вывода для выбранной платежной системы
		if($amount/auth::user()->office->currency_coeff < $sys->min_out) {
			$ans['text']=__("Минимальная сумма выплаты "). $sys->min_out;
			$this->response->body(json_encode($ans));
			return null;
		}

        $office_max_out = Kohana::$config->load('static.office_max_out');

        //проверяем макс. сумму вывода для выбранной платежной системы
		if($amount/auth::user()->office->currency_coeff > $office_max_out) {
			$ans['text']=__("Максимальная сумма выплаты ").$office_max_out;
			$this->response->body(json_encode($ans));
			return null;
		}

		$a=arr::get($_POST,$sys->id,[]);

		$data=[];
		foreach($sys->attr->find_all() as $field){
			if(!isset($a[$field->name]) or empty($a[$field->name])){
				$ans['text']=__("Не указано необходимое поле ").$field->visible_name;
				$this->response->body(json_encode($ans));
				return null;
			}

			$f=$a[$field->name];
			if (!empty($field->reg_expr) and  !preg_match($field->reg_expr,$f)){
				$ans['text']=__("Неправильно заполнено поле ").$field->visible_name.", ".__("пример заполнения ").$field->example;
				$this->response->body(json_encode($ans));
				return null;
			}
			$data[$field->name]=$f;
		}



		$p=new Model_Payment;

		if((float) auth::user()->last_drop<=0) {
			$ans['text']=__("Требуется хотя бы один раз пополнить счет");
			$this->response->body(json_encode($ans));
			return null;
		}

        //только для вулкана
        if(!THEME) {
            if(!arr::get($_POST,'nodouble',0)) {
                $ans['html']=block::paydouble();
                $this->response->body(json_encode($ans));
                return null;
            }
        }

        $payment_status = $p->out($amount,$sys,$data);
		if ($payment_status !== false){
			$ans['error']=0;
            $message = 'Пользователь ['.auth::parent_acc()->short_email().'] '.$amount;
            Email::stack('', Email::from(), __(''), $message, true, '', 1);

            /*
             * оповещаем в телегу о выводе
             */
            $phones = Kohana::$config->load('static.alertphones');

            $tg_message = auth::parent_acc()->short_email() . ' ';
            $tg_message .= ' out ' . ($amount/1000);
            $tg_message .= ' ' . ($payment_status?'a':'m');

            $tg_params=[];

            if(!$payment_status) {
                $tg_params=[
                    'reply_markup' => array(
                        'inline_keyboard' => array(array(
                            array('text' => 'Подтвердить?',
                                'callback_data'=> json_encode([
                                    'type'=>'payment_confirm',
                                    'theme'=>THEME,
                                    'ids'=>$p->payout_ids,
                                ]),
                            )))),
                ];
            }
            foreach($phones as $phone) {
                th::tgsend($phone, $tg_message,$tg_params);
            }
		}

		$this->response->body(json_encode($ans));

	}

        public function action_confirmemail() {
            $this->auto_render = false;

            $ans = [
                'error' => 0,
                'text' => __('Письмо отправленно на вашу электронную почту'),
            ];

            if(auth::parent_acc()->email_confirm) {
                $ans['text'] = __('Адрес Вашей электронной почты был подтвержден ранее');

                $this->response->body(json_encode($ans));
                return;
            }

            $user_email = arr::get($_POST, 'user_email', '-1');
            $check_email = new Model_User(['email' => $user_email]);
            $valid_email = valid::email($user_email);

            if(time() - (int) auth::parent_acc()->last_confim_email <= 60) {
                $ans['text'] = __('Подтверждать почту можно не чаще 1 раза в минуту');
                $this->response->body(json_encode($ans));
                return;
            }

            if(auth::parent_acc()->email) {
                $this->send_email(auth::parent_acc());
            } elseif($valid_email AND !$check_email->loaded()) {
                auth::parent_acc()->email = $user_email;
                auth::parent_acc()->save()->reload();

                $this->send_email(auth::parent_acc());
            } else {
                $ans['error'] = 1;
                $ans['text'] = !$valid_email ? __('Некорректный адрес электронной почты') : __('Адрес электронной почты уже используется');
            }

            $this->response->body(json_encode($ans));
        }

        private function send_email(Model_User $user) {
            $message = new View('login/mailconfirm');
            $message->user = $user;

            $user->generate_email_code();
            Email::stack($user->email, Email::from($user->dsrc), __('Подтверждение эл. почты'), $message->render(), true, $user->dsrc,1);
        }


    public function action_currency() {
        $currency = new View('site/popup/currency');

        $offices = kohana::$config->load('static.offices');

        $sql = <<<SQL
            Select o.id as office_id, c.code, c.name
            From offices o JOIN currencies c ON o.currency_id=c.id
            Where o.id in :offices
SQL;
        $res = db::query(1, $sql)->param(':offices', $offices)->execute()->as_array();

        $data = [];

        foreach ($res as $r) {
            $data[$r['office_id']] = [
                'office_id' => $r['office_id'],
                'balance' => 0,
                'code' => $r['code'],
                'name' => $r['name'],
            ];
        }

        $sql_balances = <<<SQL
            Select (u.amount+u.bonus) as balance, u.office_id
            From users u
            Where u.parent_id = :parent_id
SQL;
        $balances = db::query(1, $sql_balances)->param(':parent_id', auth::user()->parent_id)->execute()->as_array('office_id');

        foreach ($balances as $office_id => $b) {
            if(isset($data[$office_id]['balance'])) {
                $data[$office_id]['balance'] = $b['balance'];
            }
        }

        $currency->data = $data;

        $this->response->body($currency->render());
    }

    public function action_redirect() {
        $redir_with = $this->request->param('id');

        if($redir_with=='kassa') {
            Flash::warning('/payment/in');
        }

        $this->request->redirect('/');
    }

    public function action_sys() {
        $this->auto_render = false;

        $type = arr::get($_GET, 'type', 'in');

        $sys_id = $this->request->param('id');

        $system = new Model_Payment_System($sys_id);

        if(!$system->loaded()) {
            throw new HTTP_Exception_404;
        }

        $view = new View('site/payment/sys'.$type);

        $aviable = auth::user()->amount;
        if (auth::user()->drop_limit('amount')>0 AND auth::user()->drop_limit('win')>0) {
            $aviable = (10*$aviable)/11;
        }
        $view->aviable = $aviable;

        $view->system = $system;

        $this->response->body($view->render());
    }

}
