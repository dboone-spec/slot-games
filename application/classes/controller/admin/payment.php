<?php

class Controller_Admin_Payment extends Super{



	public $mark='Платежи'; //имя
	public $model_name='payment'; //имя модели
        public $scripts = ['/js/compiled/main.4ecde5c.js'];
	public $sh='admin/payment'; //шаблон


	public function configure()
	{

            $this->search = [
                    'id',
                    'user_id',
                    'amount',
                    'paymentamount',
                    'status',
                    'external_id',
                    'gateway',
                    'created',
            ];


            $this->list = [
                    'id',
                    'user_id',
                    'currency',
                    'amount',
                    'status',
        //            'system_percent',
                    'total_commission',
                    'dsrc',
                    'msrc',
                    'bonus_code',
                    'external_id',
                    'gateway',
                    'created',
                    'payed'
            ];

            if(arr::get($_GET, 'status')==0) {
                $this->order_by = ['created','desc nulls last'];
            } else {
                $this->order_by = ['payed','desc nulls last'];
            }

            $this->vidgets['status'] = new Vidget_PaymentStatus('status',$this->model);

            $dv = new Vidget_Timestamp('created',$this->model);
            $dv->param('encashment_time',$this->encashment_time);
            $dv->param('zone_time',$this->zone_time);
            
            if ($day_period = arr::get($this->day_period, person::$role))
            {
                $dv->param('day_period', $day_period);
            }
            
            $this->vidgets['created'] = $dv;

        //когда был зачислен платеж
            $payed = new Vidget_Timestamp('payed',$this->model);
            $payed->param('encashment_time',$this->encashment_time);
            $payed->param('zone_time',$this->zone_time);
            $this->vidgets['payed'] = $payed;

            $uv = new Vidget_Relateduser('user_id',$this->model);
            $uv->param('related','user');
            $uv->param('name','name');
            $this->vidgets['user_id'] = $uv;

            $dsrc = new Vidget_Related('user_id',$this->model);
            $dsrc->param('related','user');
            $dsrc->param('name','dsrc');
            $this->vidgets['dsrc'] = $dsrc;

            $msrc = new Vidget_Related('user_id',$this->model);
            $msrc->param('related','user');
            $msrc->param('name','msrc');
            $this->vidgets['msrc'] = $msrc;

            $this->vidgets['data'] = new Vidget_Json('data',$this->model);

            $office = new Vidget_Officecurrency('currency', $this->model);
            $this->vidgets['currency'] = $office;

            $paymentamount = new Vidget_Paymentamount('paymentamount', $this->model);
            $this->vidgets['paymentamount'] = $paymentamount;
	}


	public function action_delete(){

		throw new HTTP_Exception_404;

	}


	public function action_item() {
		if ($this->request->method() == 'POST'){
			throw new HTTP_Exception_404;
		}
		return parent::action_item();
	}


	public function action_cancel(){
		$p=new Model_Payment($this->request->param('id'));

		if (!$p->loaded()){
			throw new HTTP_Exception_404;
		}

		if ($p->status>PAY_BEGIN){
			$this->template->content='Нельзя отменить платеж';
			return null;
		}

		$p->cancel();

		$this->request->redirect($this->request->referrer());
	}

	public function action_approved(){
		$p=new Model_Payment($this->request->param('id'));

		if (!$p->loaded()){
			throw new HTTP_Exception_404;
		}

		if ($p->status>=PAY_APPROVED){
			$this->template->content='Нельзя поставить платеж на выплату';
			return null;
		}

		$p->status=PAY_APPROVED;
		$this->calc_changes($p,'payment_approved');
                $p->save();
                $this->log_changes($p->id);

                //оповещение на почту о подтвержденном выводе
                $message = 'Ваш вывод №' . $p->id . ' на сумму ' . abs($p->amount) . ' подтвержден.';
                email::send($p->user->email, email::from(), 'Подтверждение вывода', $message);

		$this->request->redirect($this->request->referrer());
	}

	public function action_end(){
		$p=new Model_Payment($this->request->param('id'));

		if (!$p->loaded()){
			throw new HTTP_Exception_404;
		}

		if ($p->status>PAY_BEGIN){
			$this->template->content='Нельзя пометить платеж как выплаченный';
			return null;
		}

		$p->payed=time();
		$p->status=PAY_SUCCES;
                $this->calc_changes($p,'payment_end');
                $p->save();
                $this->log_changes($p->id);

		$this->request->redirect($this->request->referrer());
	}

        public function handler_search($vars)
        {
            $model = parent::handler_search($vars);
            if(in_array(person::$role, ['analitic','agent'])) {
                $model->join('persons')
                        ->on('persons.id','=','payment.user_id');
            }
            
            return $model;
        }
}


