<?php

class Controller_Admin_Logins extends Super{



	public $mark = 'Авторизации'; //имя
	public $model_name = 'login'; //имя модели
	public $controller = 'logins'; //имя модели
	public $order_by=array('created', 'desc'); // сортировка
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure() {
        $this->search = [
            "user_id",
            "user_email",
            "ip",
            "fingerprint"
        ];

        $this->list = [
            'user_id',
            "user_email",
            'ip',
            'fingerprint',
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

        $this->vidgets['created'] = new Vidget_Timestamp('created', $this->model);
        $this->vidgets['created']->param('encashment_time',$this->encashment_time);
        $this->vidgets['created']->param('zone_time',$this->zone_time);
        $this->vidgets['fingerprint'] = new Vidget_Inputint('fingerprint', $this->model);
    }
}