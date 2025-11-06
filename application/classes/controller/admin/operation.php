<?php

class Controller_Admin_Operation extends Super {

    public $template = 'layout/admin';
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    public $mark='Операции'; //имя
	public $model_name='operation'; //имя модели
    public $per_page   = 20;
    public $sh='super/normal';
    public $canEdit=false;
    public $canCreate=false;
    public $canDelete=false;
    public $canItem=false;
    public $notSortable=['before','after'];

    public function configure() {
        $this->search = [
            'created',
            'updated_id',
            'type'
        ];

        $this->list = [
            'id',
            'created',
            'updated_id',
            'before',
            'amount',
            'after',
            'type',
            'office_amount',
            'person_id',
            'office_id',
        ];

        if(Person::$role=='cashier') {
            $this->search[]='onlyme';
        }

        $this->order_by = ['created','desc'];

        foreach (['person_id', 'office_id'] as $v) {
            $this->vidgets[$v] = new Vidget_Integer($v, $this->model);
        }

        $this->vidgets['amount'] = new Vidget_AmountOperation('amount', $this->model);

        $type = new Vidget_Select('type', $this->model);

        $f = [
            0 => __('Все'),
            'user_payment' => __('Пополнение'),
            'user_withdraw' => __('Снятие'),
        ];

        if(Person::$role!='cashier') {
            $f['payment_office'] = __('ППС');
        }

        $type->param('fields', $f);

        $this->vidgets['type'] = $type;

        $id = new Vidget_onlyme('person_id', $this->model);
        $this->vidgets['onlyme'] = $id;

        $id = new Vidget_Integer('updated_id', $this->model);
        $this->vidgets['updated_id'] = $id;

        $id = new Vidget_Integer('person_id', $this->model);
        $this->vidgets['person_id'] = $id;

        $balance = new Vidget_BalanceWithAmount('balance', $this->model);
        $this->vidgets['balance'] = $balance;

        $this->vidgets['created'] = new Vidget_Timestamp('created', $this->model);
        $this->vidgets['created']->param('zone_time',$this->zone_time);
        $this->vidgets['created']->param('encashment_time',$this->encashment_time);
        if ($day_period = arr::get($this->day_period, person::$role))
        {
            $this->vidgets['created']->param('day_period', $day_period);
        }
    }

    public function handler_search($vars) {
        $model = parent::handler_search($vars);
        return $model->where('office_id','in',Person::user()->offices());
    }
}

