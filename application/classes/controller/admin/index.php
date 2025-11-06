<?php

class Controller_Admin_Index extends Controller_Admin_Base{

	public function action_index(){

        if(!in_array(person::$role,['sa','analitic','gameman'])) {
            $this->request->redirect($this->dir.'/dashboard');
        }
		$this->request->redirect($this->dir.'/bet');
	}



	public function action_updatepayeer(){




		$payeer = new CPayeer();
		if ($payeer->isAuth()){
			$a=$payeer->getPaySystems();
			$p=new Model_Payment_System;
			$f=new Model_Payment_Field;
			foreach ($a['list'] as $sys) {
				$p->where('id','=',$sys['id'])
					->where('gate','=','payeer')
					->find();
				if (!$p->loaded()){
					$p->id=$sys['id'];
				}
				$p->name=$sys['name'];
				$p->gate='payeer';
				$p->commission=$sys['gate_commission'];
				$p->commission_min=$sys['gate_commission_min'];
				$p->commission_max=$sys['gate_commission_max'];
				$p->commission_persent=$sys['commission_site_percent'];
				$p->currency=$sys['currencies'];
				$p->direction='out';
				$p->save();



				foreach ($sys['r_fields'] as $name=>$field) {
					$f->where('payment_system_id','=',$p->id)
					->where('name','=',$name)
					->find();
					if (!$f->loaded()){
						$f->payment_system_id=$p->id;
						$f->name=$name;
					}
					$f->visible_name=$field['name'];
					$f->reg_expr=$field['reg_expr'];
					$f->example=$field['example'];
					$f->save();
					$f->clear();
				}
				$p->clear();

			}


			$this->template->content='update ok';
		}
		else{
			$this->template->content='<pre>'.print_r($payeer->getErrors(), true).'</pre>';
		}
	}



	public function action_updateinterkassa(){

		$ik=new interkassa();

		$p=new Model_Payment_System;
		$f=new Model_Payment_Field;
		foreach($ik->getPaySystems() as $id=>$sys){

			$p->where('id','=',$id)
					->where('gate','=','interkassa')
					->find();
			if (!$p->loaded()){
				$p->id=$id;
			}
			$p->name=$sys['als'];
			$p->gate='interkassa';
			$p->currency=$sys['curAls'];
			$p->direction='in';
			$p->visible_name=$sys['name'][0]['v'];
			$p->save();
			$p->clear();

		}

		foreach($ik->getOutSystems() as $id=>$sys){

			$p->where('id','=',$id)
					->where('gate','=','interkassa')
					->find();
			if (!$p->loaded()){
				$p->id=$id;
			}
			$p->name=$sys['als'];
			$p->gate='interkassa';
			$p->currency=$sys['curAls'];
			$p->direction='out';
			$p->visible_name=$sys['name'][0]['v'];
			$p->save();

			foreach ($sys['prm'] as $name=>$field) {
				$f->where('payment_system_id','=',$p->id)
				->where('name','=',$field['al'])
				->find();
				if (!$f->loaded()){
					$f->payment_system_id=$p->id;
					$f->name=$field['al'];
				}
				$f->visible_name=$field['tt'];
				$f->reg_expr=$field['re'];
				$f->example=isset($field['ex'])?$field['ex']:'';
				$f->save();
				$f->clear();
			}

			$p->clear();

		}


		$this->template->content='update ok';


	}

	public function action_updateex(){

		$ik=new ex4money();


		$p=new Model_Payment_System;


		foreach($ik->getsystems() as $id=>$sys){

			$id='ex4_'.$sys['id'];

			$p->where('id','=',$id)
					->where('gate','=','ex4money')
					->find();
			if (!$p->loaded()){
				$p->id=$id;
			}


			$p->name=$sys['id'];
			$p->gate='ex4money';
			$p->currency='rub';
			$p->direction=$sys['direction'];
			$p->visible_name=$sys['name'];
			$p->save();
			$p->clear();

			foreach ($sys['fields'] as $name=>$field) {

				$f=new Model_Payment_Field;

				$f->where('payment_system_id','=',$id)
				->where('name','=',$field['name'])
				->find();

				if (!$f->loaded()){
					$f->payment_system_id=$id;
					$f->name=$field['name'];
				}

				$f->visible_name=$field['visible_name'];
				$f->reg_expr=$field['reg_expr'];
				$f->example=isset($field['example'])?$field['example']:'';
				$f->save();
				$f->clear();
			}



		}




		$this->template->content='update ok';


	}
    
    protected $_dir='in';


    public function action_updatefreeob(){

		$ik=new freeobmen();


		$p=new Model_Payment_System;

        th::vd($ik->payways(),false);
        
		foreach($ik->payways() as $id=>$sys){

            if(!isset($sys['id'])) {
                continue;
            }
            
			$id='frob_'.$this->_dir.'_'.$sys['id'];

			$p->where('id','=',$id)
					->where('gate','=','freeob')
					->find();
			if (!$p->loaded()){
				$p->id=$id;
			}


			$p->name=$sys['paymentSystemCode'];
			$p->gate='freeob';
			$p->currency=$sys['currencyCode'];
			$p->direction=$this->_dir;
			$p->visible_name=$sys['paymentSystemCode'].' '.$sys['currencyCode'];
            
            if($this->_dir=='in') {
                $p->comission_system=$sys['depositCommissionPercent'];
                $p->fixed_commission=$sys['depositCommissionAmount'];
                //todo добавить мин и макс ИН
            }
            else {
                $p->min_out=$sys['withdrawalMinAmount'];
                $p->max_out=$sys['withdrawalMaxAmount'];
                
                $p->comission_system=$sys['withdrawalCommissionPercent'];
                $p->fixed_commission=$sys['withdrawalCommissionAmount'];
            }
            
            
			$p->save();
			$p->clear();

			/*foreach ($sys['fields'] as $name=>$field) {

				$f=new Model_Payment_Field;

				$f->where('payment_system_id','=',$id)
				->where('name','=',$field['name'])
				->find();

				if (!$f->loaded()){
					$f->payment_system_id=$id;
					$f->name=$field['name'];
				}

				$f->visible_name=$field['visible_name'];
				$f->reg_expr=$field['reg_expr'];
				$f->example=isset($field['example'])?$field['example']:'';
				$f->save();
				$f->clear();
			}*/



		}

        if($this->_dir=='in') {
            $this->_dir='out';
            $this->action_updatefreeob();
        }
        

		$this->template->content='update ok';


	}


	public function action_updatepayeerin(){

		$el=[];
		$el[]=['Payeer','2609'];
		$el[]=['QIWI Wallet','20916096'];
		$el[]=['Advcash','88106414'];
		$el[]=['OkPay','1577275'];
		$el[]=['Paxum','29669322'];
		$el[]=['VISA','84686071'];
		$el[]=['Swift','3591195'];
		$el[]=['Альфа Клик','4500639'];
		$el[]=['Сбербанк ОнЛ@йн','4540167'];
		$el[]=['Промсвязь банк','26215075'];
		$el[]=['Русский стандарт','2619'];
		$el[]=['Евросеть','74905960'];
		$el[]=['Связной','86440462'];
		$el[]=['Билайн','88002442'];
		$el[]=['МТС','87815397'];
		$el[]=['Мегафон','88004700'];
		$el[]=['Tele 2','87996940'];

		$p=new Model_Payment_System;
		foreach($el as list($name,$id)){

			$p->where('id','=',$id)
					->where('gate','=','payeer')
					->find();
			if (!$p->loaded()){
				$p->id=$id;
			}
			$p->name=$name;
			$p->gate='payeer';
			$p->currency='rub';
			$p->direction='in';
			$p->visible_name=$name;
			$p->save();
			$p->clear();

		}


		$this->template->content='update ok';




	}

}

