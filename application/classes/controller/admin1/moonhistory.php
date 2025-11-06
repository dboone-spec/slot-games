<?php

class Controller_Admin1_Moonhistory extends Super
{

    public $mark       = 'Moon results history'; //имя
    public $model_name = 'moonhistory'; //имя модели
    public $canEdit    = false;
    public $canCreate  = false;
    public $canDelete  = false;
    public $canItem    = false;
    public $sh         = 'admin1/super';
    public $controller = 'moonhistory'; //имя модели
    public $order_by   = array('created','desc'); // сортировка
    public $scripts    = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->search = [
                "id",
        ];

        $this->list = [
                'id',
                'rate',
                'created',
                'finished',
        ];

        $this->vidgets['created'] = new Vidget_Timestamp('created',$this->model);
        $this->vidgets['created']->param('encashment_time',0);
        $this->vidgets['created']->param('zone_time',0);

        $this->vidgets['finished'] = new Vidget_Timestamp('finished',$this->model);
        $this->vidgets['finished']->param('encashment_time',0);
        $this->vidgets['finished']->param('zone_time',0);
    }



}
