<?php

class Controller_Admin1_Jphistory extends Super
{

    public $mark       = 'JP history'; //имя
    public $model_name = 'jackpothistory'; //имя модели
    public $canEdit    = false;
    public $canCreate  = false;
    public $canDelete  = false;
    public $canItem    = false;
    public $sh         = 'admin1/super';
    public $controller = 'jphistory'; //имя модели
    public $order_by   = array('created','desc'); // сортировка
    public $scripts    = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->search = [
                "user_id",
                "office_id",
                "external_id",
                'created',
        ];

        $this->list = [
                'user_id',
                'office_id',
                'game',
                'level',
                'win',
                'created',
                'cards',
        ];

        if(Person::$role=='sa') {
            $this->list[]='hotstart';
            $this->list[]='hotstartsum';
            $this->list[]='triggernum';
            $this->list[]='triggersum';
            $this->list[]='triggertime';
        }

        $json = new Vidget_pokercards('cards', $this->model);
        $this->vidgets['cards'] = $json;

        $id = new Vidget_Integer('user_id', $this->model);
        $this->vidgets['user_id'] = $id;

        $this->vidgets['office_id']=new Vidget_List('office_id',$this->model);
        $this->vidgets['office_id']->param('list',Person::user()->officesName(null,true));

        $dv = new Vidget_Timestamp('hotstart', $this->model);
        $dv->param('encashment_time', 0);
        $dv->param('zone_time',0);
        $this->vidgets['hotstart'] = $dv;

        $dv = new Vidget_Timestamp('triggertime', $this->model);
        $dv->param('encashment_time', 0);
        $dv->param('zone_time',0);
        $this->vidgets['triggertime'] = $dv;

        $dv = new Vidget_Timestamp('created', $this->model);
        $dv->param('encashment_time', 0);
        $dv->param('zone_time',0);
        if ($day_period = arr::get($this->day_period, person::$role,10))
        {
            $dv->param('day_period', $day_period);
        }
        $this->vidgets['created'] = $dv;

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
