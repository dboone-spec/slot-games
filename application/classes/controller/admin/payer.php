<?php


class Controller_Admin_Payer extends Controller_Admin_Base {

    protected $_strict_payments = ['-1'];
    
    public function action_index(){
        $view=new View('admin/payer/in');

        $view->dir = $this->dir;
        $this->template->content = $view;
	}
    
    public function action_iframe() {
        $this->auto_render = false;
        
        $currency_id = $this->request->param('id')??1;
        
        $view=new View('admin/payer/form/in');

        $sys = new Model_Payment_System;
        $systems = $sys->where('use', '=', '1')->where('payment_system.id','not in', $this->_strict_payments)
                ->where('direction', '=', 'in')->where('currency_id', '=', $currency_id)->order_by('sort_index')->find_all();
        $currency = new Model_Currency($currency_id);
        
        $view->currency_id = $currency_id;
        $view->currency_code = $currency->code;
        
        $view->dir = $this->dir;
        $view->min_sum_pay = 500;
        $view->groups = kohana::$config->load('static.payment_groups');
        $view->systems = $systems;
        $this->response->body($view->render());
    }

    public function action_go(){
        $this->auto_render=false;

		$sys=arr::get($_POST,'paysys_current',-1);
		$sys=new Model_Payment_System($sys);
		if (!$sys->loaded() or $sys->use!=1 or $sys->direction!='in'){
			throw new HTTP_Exception_404;
		}

        $amount = (float) arr::get($_POST,'amount',500);

        $min_sum_pay = 500;
        if($amount < $min_sum_pay) {
            $this->response->body(json_encode(['error'=>__('Минимальная сумма пополнения - ').$min_sum_pay . ' ' . person::user()->currency($sys->currency_id)]));
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
		$p->user_id=person::$user_id;
		$p->status=0;
		$p->amount= $amount;
		$p->gateway=$sys->gate;
		$p->currency= strtolower(auth::user()->office->currency->code);
        $p->data=json_encode($data);
		$p->payment_system_id=$sys->id;
		$this->calc_changes($p,'payer_go');
        $p->save();
        $this->log_changes($p->id);

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
            $this->calc_changes($p,'trio');
            $p->save();
            $this->log_changes($p->id);

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

		if ($p->gateway=='piastrix'){


            $piastrix = new piastrix();
            $piastrix->addParam('amount',$p->amount)
                ->addParam('payway',$sys->name);

            
            $piastrix->addParam('currency', $sys->currency_model->iso_4217);

            $piastrix->addParam('shop_order_id',$p->id);

            if(!$piastrix->preinvoice()) {
                $this->response->body(json_encode([
                    "error" => Arr::get($piastrix->getErrors(),0,__("Ошибка платежа. Обратитесь в техническую поддержку"))
                ]));
                return;
            }

            //TODO тут надо вставить проверку, соответствуют ли поля в data и в их ответе в addons

            $piastrix->addParam('currency',$sys->currency_model->iso_4217);

            $piastrix->addParam('amount',$p->amount)
                ->addParam('payway',$sys->name)
                ->addParam('shop_order_id',$p->id)
                ->addParam('role', 'rmanager')    
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
            $this->calc_changes($p,'piastrix');
            $p->save();
            $this->log_changes($p->id);

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
}
