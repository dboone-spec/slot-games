<?php

class Controller_Admin_Freespin extends Super{



	public $mark = 'Фриспины'; //имя
	public $model_name = 'freespin'; //имя модели
	public $controller = 'freespin'; //имя модели
	public $order_by=array('created', 'desc'); // сортировка
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure() {
        $this->search = [
            "user_id",
            "user_email",
        ];

        $this->list = [
            'user_id',
            "user_email",
            'bonuscode',
            'freespins_current',
            'freespins_break',
            'game',
            'bet',
            'lines',
            'active',
            'payed',
            'created',
        ];

        $id = new Vidget_Userwithparent('user_id', $this->model);
        $id->param('related','user');
		$id->param('name','name');
        $this->vidgets['user_id'] = $id;

        $uv = new Vidget_Relateduser('user_email',$this->model);
		$uv->param('related','user');
		$uv->param('name','name');
		$this->vidgets['user_email'] = $uv;

        $uv = new Vidget_Related('code_id',$this->model);
		$uv->param('related','bonuscode');
		$uv->param('name','name');
		$this->vidgets['bonuscode'] = $uv;

        $this->vidgets['active'] = new Vidget_CheckBox('active', $this->model);
        $this->vidgets['payed'] = new Vidget_CheckBox('payed', $this->model);
        $this->vidgets['created'] = new Vidget_Timestamp('created', $this->model);
        $this->vidgets['created']->param('encashment_time',$this->encashment_time);
        $this->vidgets['created']->param('zone_time',$this->zone_time);
    }
}