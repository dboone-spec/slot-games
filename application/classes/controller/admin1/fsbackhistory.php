<?php

class Controller_Admin1_Fsbackhistory extends Super
{

    public $mark       = 'FSback history'; //имя
    public $model_name = 'fsbackhistory'; //имя модели
    public $canEdit    = false;
    public $canCreate  = false;
    public $canDelete  = false;
    public $canItem    = false;
    public $sh         = 'admin1/super';
    public $controller = 'fsbackhistory'; //имя модели
    public $order_by   = array('created','desc'); // сортировка
    public $scripts    = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->search = [
                "user_id",
                "external_id",
                'created',
        ];

        $this->list = [
                'user_id',
                'office_id',
                'coef',
                'change',
                'created',
        ];

        $this->vidgets['created'] = new Vidget_Timestamp('created',$this->model);
        $this->vidgets['created']->param('encashment_time',0);
        $this->vidgets['created']->param('zone_time',0);
	$this->vidgets['user_id']  = new Vidget_Integer('user_id',$this->model);
        $this->vidgets['external_id']=new Vidget_Relatedtable('external_id',$this->model);
        $this->vidgets['external_id']->param('related','user');
        $this->vidgets['external_id']->param('name','external_id');
        $this->vidgets['external_id']->param('fkey','user_id');
    }

    public function handler_search($vars)
    {
        $model = parent::handler_search($vars);
        if(Person::$role == 'sa')
        {
            return $model;
        }

        return $model->where('office_id','in',Person::user()->offices());
    }

}
