<?php

class Controller_Admin_Status extends Super
{

    public $mark       = 'Сервисы'; //имя
    public $model_name = 'status'; //имя модели
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public $sh='admin/status'; //шаблон

    public function action_reset() {
        $this->auto_render = false;
        $id = $this->request->param('id');
        $field = arr::get($_POST, 'field');
        $value = intval(arr::get($_POST, 'value', 0));

        $ans = ['error' => 1];
        $model = ORM::factory($this->model_name, $id);

        if($model->loaded() ) {
            $model->__set($field, $value);

            $this->calc_changes($model,'update');
            $model->save();
            $this->log_changes();

            $ans['error'] = 0;
        }

        $this->response->body(json_encode($ans));
    }

    public function configure()
    {
        $this->list = [
            'id',
            'last',
            'value',
        ];

        $value = new Vidget_Reset('last',$this->model);
        $value->param('v',['value']);
        $this->vidgets['last'] = $value;

        $id = new Vidget_Nlstatus('id',$this->model);
        $this->vidgets['id'] = $id;

        $value = new Vidget_CheckBox('value',$this->model);
        $this->vidgets['value'] = $value;
    }
    public function handler_search($vars) {
        $model = parent::handler_search($vars);
        return $model->where('id','in', ['newsletter', 'autopay']);
    }

}
