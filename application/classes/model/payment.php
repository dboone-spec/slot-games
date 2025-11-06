<?php


class Model_Payment extends ORM {

	protected $_created_column = array('column' => 'created', 'format' => true);

	protected $_belongs_to = [
	    'user' => [
		    'model'		 => 'user',
		    'foreign_key'	 => 'user_id',
	    ],
        'system' => [
            'model'	=> 'payment_system',
		    'foreign_key' => 'payment_system_id',
        ],
        'code' => [
            'model'	=> 'bonus_code',
		    'foreign_key' => 'bonus_code',
            'far_key' => 'name',
        ],
	];

	public function labels()
	{
		return [
			'user_id'	 => 'Пользователь',
			'amount'	 => 'Сумма',
            'system_percent' => 'Процент комиссии',
            'dsrc' => 'ресурс "Домен"',
            'msrc' => 'ресурс "Метка"',
			'win'		 => 'Сумма выигрыша',
			'created'	 => 'Время создания',
            'payed'	 => 'Время зачисления',
			'status'	 => 'Статус',
			'gateway'	 => 'Провайдер',
			'bonus_code'	 => 'Промокод',
            'total_commission' => 'Комиссия системы',
            'currency' => 'Валюта',
            'paymentamount' => 'Тип операции',
		];
	}

    public $payout_ids=[];
    
    public function get_comission_system() {
        $paym_sys = new Model_Payment_System($this->payment_system_id);
        return $paym_sys->comission_system;
    }

    public function get_comission_fixed() {
        $paym_sys = new Model_Payment_System($this->payment_system_id);
        return $paym_sys->fixed_commission;
    }

	public function pay(){
        $u=new Model_User($this->user_id);
		$b=new Model_Bonus_Code([
            'name' => $this->bonus_code,
            'office_id' => $u->office_id,
        ]);
		$bonus=0;
		$vager=0;

        /*
        if($u->office->currency->code != $this->currency) {
            $parent = $u->parent_acc();

            $sql = <<<SQL
                Select o.id as office_id
                From offices o JOIN currencies c ON o.currency_id=c.id
                Where c.code LIKE '%:curr_code%'
SQL;
            $res = db::query(1, $sql)->param(':curr_code', $this->currency)->execute();

            $office_id = $res[0]['office_id']??1;

            $u = new Model_User([
                'parent_id' => $u->parent_id,
                'office_id' => $office_id,
            ]);

            if(!$u->loaded()) {
                $u = auth::create_office_account($parent, $office_id);

                $this->user_id = $u->id;
            }
        }
         *
         */

        $bind_freespins = false;
        $payment_bonus = 0;

        if($b->loaded()) {
            if(in_array($b->type, ['freespin', 'fixed_freespin', 'bonus_freespin'])) {
                $bind_freespins = true;
                $payment_bonus += $b->spins * $b->lines * $b->bet;
            }

            if(in_array($b->type, ['fixed', 'fixed_freespin'])) {
                $bonus=$b->bonus;
                $vager=$bonus*($b->vager);
            } elseif (!in_array($b->type, ['freespin'])){
                $bonus=$this->amount*$b->bonus*$u->office->currency_coeff;
                $vager=$bonus*($b->vager);
            }
            
            $payment_bonus += $bonus;
        }

		//Нужно отыграть только реальные деньги.
		$this->user_amount=$u->amount;
		$this->user_bonus_before=$u->bonus;
		$this->bonus=$payment_bonus;

        
		database::instance()->begin();

        /*
         * todo уточнить что писать в last_drop
         */
			$this->drop = $u->amount+$this->amount;
			$this->save();

			$sql='update users
				set amount=amount+:amount,
                    sum_in=sum_in+:amount,
					last_drop=:drop,
					bonus=bonus+:bonus,
                    sum_bonus=sum_bonus+:bonus,
					bonusbreak=bonusbreak+:vager

				where id=:uid';

			db::query(1,$sql)
				->param(':amount',$this->amount*$u->office->currency_coeff)
				->param(':drop',$this->drop)
				->param(':bonus',$bonus)
				->param(':vager',$vager)
				->param(':uid',$this->user_id)
				->execute();


			$sql="update status
					set value_numeric=value_numeric+:bank
					where id='bank' and type=:type";

			db::query(1,$sql)->parameters([
                ':bank' => $this->amount*$u->office->currency_coeff,
                ':type' => $u->office_id,
            ])->execute();

            if($bind_freespins) {
                $b->bind_freespins($this->user_id);
            } elseif($b->loaded()) {
                /*
                 * помечаем запись в bonus_codes_entered
                 * для пользователя как использованную
                 */
                $bonus_code_entered = new Model_Bonus_Codeentered([
                    "user_id" => $this->user_id,
                    "code_id" => $this->code->id,
                ]);

                if($bonus_code_entered->loaded() AND $bonus_code_entered->used != 1) {
                    $bonus_code_entered->used = 1;
                    $bonus_code_entered->save();
                }
            }

            if($b->loaded() AND $b->type != 'freespin') {
                $log_data = [
                    "bonus_code" => $b->name,
                    "bonus_id" => $b->id,
                    "bonus" => $bonus,
                    "amount" => $this->amount,
                    "currency" => $this->currency,
                ];

                if(in_array($b->type, ['fixed', 'fixed_freespin'])) {
                    $log_data["fixed_sum"] = $b->bonus;
                } else {
                    $log_data["bonus_payment_coeff"] = $b->bonus;
                }

                $bonus_model = new Model_Bonus();
                $bonus_model->user_id = $this->user_id;
                $bonus_model->bonus = $bonus;
                $bonus_model->type = 'payment';
                $bonus_model->payed = 1;
                $bonus_model->log = json_encode($log_data);
                $bonus_model->save();
            }

		database::instance()->commit();



	}

    public function out($amount,$sys,$data){
        $status = PAY_NEW;
        
        $last_payment = (new Model_Payment())->where('user_id','=',auth::$user_id)->where('status','=','30')->where('amount','>',0)->order_by('payed','desc')->find();

        if(!$last_payment->loaded()) {
            //депозитов не делал
            return false;
        }

        $min = Kohana::$config->load('static.min_sum_pay');
        $office_max_out = Kohana::$config->load('static.office_max_out');
        
        $autopay = new Model_Status([
            'id' => 'autopay',
            'type' => 'main',
        ]);

        if(!$autopay->loaded()) {
            $autopay->value=1;
            $autopay->value_numeric=0;
            $autopay->last = 0;
            $autopay->id='autopay';
            $autopay->type='main';
            $autopay->save()->reload();
        }
        
        if($autopay->value && $last_payment->user_amount<10 && auth::user()->last_drop>=$min && auth::parent_acc()->autopay==1) {
            
            if($amount<=15000) {
                $status=PAY_APPROVED;
            }
            
            if($amount<=30000 && (auth::user()->sum_out_last_day() AND auth::user()->sum_inout_last_30())) {
                $status=PAY_APPROVED;
            }
        }
        
        $max_out=intval($sys->max_out);
        $count_payments=0;
        $bank=$amount;
        
        database::instance()->begin();
        
        while($bank>0) {
            $count_payments++;
            if($count_payments>$office_max_out/$max_out) {
                break;
            }
            
            $s_amount = $bank>=$max_out?$max_out:$bank;
            if($s_amount>0) {
                $part_payment = new self();
                $id = $part_payment->out_part($s_amount,$sys,$data,$status);
                $this->payout_ids[]=$id;
            }
            $bank=$bank-$s_amount;
        }

        database::instance()->commit();
        
		return $status;

	}

    protected function out_part($amount,$sys,$data, $status) {
        $com10=0;
		if (auth::user()->drop_limit('amount')>0 and auth::user()->drop_limit('win')>0){
			$com10=$amount*0.1;
		}
        
        $amount_with_coeff = $amount/auth::user()->office->currency_coeff;

		$this->amount=-$amount_with_coeff;
		$this->commission=$com10;
		$this->user_id=auth::$user_id;
		$this->user_amount=auth::user()->amount-auth::user()->bonus;
		$this->gateway=$sys->gate;
		$this->currency= strtolower(auth::user()->office->currency->code);
		$this->status=$status;
		$this->ps=$sys->name;
		$this->payment_system_id=$sys->id;
        $this->system_percent = $sys->comission_system;
        $this->total_commission = $amount_with_coeff*$sys->comission_system/100 + $sys->fixed_commission;
		$this->data=json_encode($data);

        $this->save()->reload();

        $sql='update users
            set amount=amount-:amount,
                sum_out=sum_out+:amount,
                bonusbreak=0,
                bonus=0,
                bonuscurrent=0
            where id=:uid';

        db::query(1,$sql)->param(':amount',$amount+$com10)
                        ->param(':uid',(int) $this->user_id)
                        ->execute();


        $sql="update status
                set value_numeric=value_numeric-:bank
                where id='bank' and type=:type";

        db::query(1,$sql)->parameters([
            ':bank' => $amount,
            ':type' => auth::user()->office_id,
        ])->execute();

		
        /*
         * для хранения в текущей авторизации корректных данных
         */
        auth::user(true);
        
        return $this->id;
    }

    public function cancel(){

        if ($this->amount>0){
            throw new Exception("Cann't cancel in payments");
        }


		$amount=abs($this->amount)+abs($this->commission);
		$this->status=PAY_CANCEL;

		database::instance()->begin();
        try {
			$this->save();

			$sql='update users
				set amount=amount+:amount,
                sum_out=sum_out-:amount
				where id=:uid';

			db::query(1,$sql)->param(':amount',$amount)
							->param(':uid',(int) $this->user_id)
							->execute();


			$sql="update status
					set value_numeric=value_numeric+:bank
					where id='bank' and type=:type";

            $u = new Model_User($this->user_id);

			db::query(1,$sql)->parameters([
                ':bank' => $amount,
                ':type' => $u->office_id,
            ])->execute();

            database::instance()->commit();
        } catch (Database_Exception $ex) {
            database::instance()->rollback();
        }
	}
    
    public function pay_rmanager(){
        $p=new Model_Person($this->user_id);
		  
        $currency_id = $this->system->currency_id;
        $p_balance = $p->balance($currency_id);
        
        $this->drop=$this->amount+$p_balance;
		$this->user_amount=$p_balance;

        $amount = $this->amount*100/$p->percent;
        
		database::instance()->begin();

        $this->save();

        $sql = <<<SQL
            update person_balances
                set amount=amount+:amount,
                sum_in=sum_in+:drop,
                last_drop=:drop
            where person_id=:pid
                AND currency_id = :currency_id
SQL;

        db::query(1,$sql)
            ->param(':amount', $amount)
            ->param(':drop', $this->amount)
            ->param(':pid',$this->user_id)
            ->param(':currency_id',$currency_id)
            ->execute();

		database::instance()->commit();
	}
    
    public function confirm($ids=[]) {
        db::query(Database::UPDATE, 'update payments set status=case when status=0 then 10 else status end where id in :ids')->param(':ids', $ids)->execute();
    }
}
