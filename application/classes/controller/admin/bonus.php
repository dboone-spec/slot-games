<?php

class Controller_Admin_Bonus extends Super{



	public $mark = 'Бонусы начисленные пользователям'; //имя
	public $model_name = 'bonus'; //имя модели
	public $order_by=array('created', 'desc'); // сортировка
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure() {
        $this->search = [
            "user_id",
            "referal_id"
        ];

        $this->list = [
            'user_id',
            'bonus',
            'currency',
            'created',
            'type',
            'last_notification',
            'accrual_days_ago',
        ];

        $id = new Vidget_Integer('user_id', $this->model);
        $this->vidgets['user_id'] = $id;

        $referal_id = new Vidget_Integer('referal_id', $this->model);
        $this->vidgets['referal_id'] = $referal_id;

        $created = new Vidget_Timestamp('created',$this->model);
        $created->param('encashment_time',$this->encashment_time);
        $created->param('zone_time',$this->zone_time);
		$this->vidgets['created'] = $created;

        $last_notification = new Vidget_Timestamp('last_notification',$this->model);
		$last_notification->param('encashment_time',$this->encashment_time);
        $last_notification->param('zone_time',$this->zone_time);
        $this->vidgets['last_notification'] = $last_notification;

        $office = new Vidget_Officecurrency('currency', $this->model);
        $this->vidgets['currency'] = $office;

        $json = new Vidget_Json('log', $this->model);
        $this->vidgets['log'] = $json;
    }
}