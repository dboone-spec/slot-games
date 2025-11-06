<?php

class Controller_Admin1_Agtuser extends Super
{

    public $mark       = 'Пользователи'; //имя
    public $model_name = 'user'; //имя модели
    public $order_by   = array('created','desc nulls last');
    public $per_page   = 20;
    public $sh='admin1/super';
    public $canEdit=false;
        public $canCreate=false;
        public $canDelete=false;
        public $canItem=false;

    public function configure()
    {
        $this->search = [
                'id',
                'office_id',
                'external_id',
        ];

        $this->list = [
                'id',
                'external_id',
                'office_id',
                'last_bet_time',
                'last_login',
                'created',
				'comment',
        ];

        $this->show = [];

        $timestamps = [
            'last_login',
            'created',
            'updated',
            'last_bet_time'
        ];

        foreach ($timestamps as $field) {
            $this->vidgets[$field] = new Vidget_Timestampecho($field, $this->model);
        }

        $this->vidgets['office_id']=new Vidget_List('office_id',$this->model);
        $this->vidgets['office_id']->param('list',Person::user()->officesName(null,true));

    }

    public function action_item(){

        if(person::$role=='sa') {
            return parent::action_item();
        }

        if (!in_array($this->request->param('office_id'),Person::user()->offices())){
            throw new HTTP_Exception_403();
        }
        return parent::action_item();

    }

    public function handler_search($vars){
        $model = parent::handler_search($vars);
        if(Person::$role=='sa') {
            return $model;
        }
        return $model->where('office_id','in', Person::user()->offices());
    }
}
