<?php

class Controller_Admin1_Terminals extends Super{

    public $mark       = 'Терминалы'; //имя
    public $model_name = 'terminal'; //имя модели
    public $controller = 'terminals'; //имя модели
    public $canCreate=false;
    public $canDelete=false;
    public $sh='admin1/super';

    public function configure() {
        $this->search = [
                'id',
                'visible_name',
                'blocked',
        ];

        $this->list = [
                'id',
                'visible_name',
                'blocked',
                'amount',
        ];

        $this->show = [
                'id',
                'visible_name',
                'blocked',
                'amount',
        ];

        $this->vidgets['id'] = new Vidget_Echo('id', $this->model);
        $this->vidgets['amount'] = new Vidget_Echo('amount', $this->model);
        $this->vidgets['blocked'] = new Vidget_terminalstatus('blocked', $this->model);
    }

    public function handler_save($data)
    {
        $old_data=$this->model->object();

        if($old_data['blocked']==0 && $data['blocked']!=1) {
            $data['blocked']=$old_data['blocked'];
        }

        if($data['blocked']=='') {
            $data['blocked']=$old_data['blocked'];
        }

        if($old_data['blocked']==1) {
            $data['blocked']=$old_data['blocked'];
        }

        parent::handler_save($data);
    }

    public function handler_search($vars)
    {
        $model = parent::handler_search($vars);

        if (Person::$role=='sa'){
            return $model;
        }

        return $model->where('office_id','in',Person::user()->offices());
    }

}

