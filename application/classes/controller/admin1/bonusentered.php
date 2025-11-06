<?php

class Controller_Admin_Bonusentered extends Super{

	public $mark='Введенные бонус коды'; //имя
	public $model_name='bonus_codeentered'; //имя модели
	public $order_by=array('created','desc'); // сортировка
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure() {
        $this->search = [
            "ip",
            "code_id",
            "user_id",
        ];
        $this->list = [
            'id',
            'currency',
            'user_id',
            'ip',
            'code_id',
            'used',
            'created',
        ];

        $id = new Vidget_Userwithparent('user_id', $this->model);
        $id->param('related','user');
		$id->param('name','name');
        $this->vidgets['user_id'] = $id;

        $code = new Vidget_Related('code_id',$this->model);
		$code->param('related','bonuscode');
		$code->param('name','name');
		$this->vidgets['code_id'] = $code;

        $this->vidgets['created'] = new Vidget_Timestamp('created', $this->model);
        $this->vidgets['created']->param('encashment_time',$this->encashment_time);
        $this->vidgets['created']->param('zone_time',$this->zone_time);

        $office = new Vidget_Officecurrency('currency', $this->model);
        $this->vidgets['currency'] = $office;
    }
}