<?php

class Controller_Admin1_Fsprocesslist extends Super
{

    public $mark       = 'Freespins process list'; //имя
    public $model_name = 'freespinsstack'; //имя модели
    public $canEdit    = false;
    public $canCreate  = false;
    public $canDelete  = false;
    public $canItem    = false;
    public $sh         = 'admin1/super';
    public $controller = 'fsprocesslist'; //имя модели
    public $order_by   = array('created','desc'); // сортировка
    public $scripts    = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->search = [
        ];

        $this->list = [
                'name',
                'status',
                'created',
        ];

        $this->vidgets['id']  = new Vidget_Integer('id',$this->model);
        $this->vidgets['set_id']  = new Vidget_Integer('set_id',$this->model);
        $this->vidgets['status']  = new Vidget_fsprocessstatus('status',$this->model);
        $this->vidgets['created'] = new Vidget_Timestamp('created',$this->model);
        $this->vidgets['created']->param('encashment_time',0);
        $this->vidgets['created']->param('zone_time',0);

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
