<?php

class Controller_Admin_Banks extends Super
{

    public $mark       = 'Банки'; //имя
    public $model_name = 'status'; //имя модели
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public $sh='admin/banks'; //шаблон


    public function configure()
    {
        $this->list = [
            'id',
            'last',
            'value_numeric',
            'type',
        ];

        $value = new Vidget_Reset('last',$this->model);
        $value->param('v',['value']);
        $this->vidgets['last'] = $value;

        $id = new Vidget_Nlstatus('id',$this->model);
        $this->vidgets['id'] = $id;

        $value = new Vidget_Input('value_numeric',$this->model);
        $value->param('can_edit',true);
        $this->vidgets['value_numeric'] = $value;
    }
    public function handler_search($vars) {
        $model = parent::handler_search($vars);
        return $model->where('id','in', ['bank','users'])->where('type','in',['1013','1015','1017']);
    }

}
