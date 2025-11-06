<?php

class Controller_Admin_Bonuscode extends Super{

	public $mark='Бонус коды'; //имя
	public $model_name='bonuscode'; //имя модели
	public $order_by=array('created','desc'); // сортировка
    public $scripts = ['/js/compiled/main.4ecde5c.js'];


    public function configure() {
        $this->search = [
            "name",
        ];

        $this->list = [
            'name',
            'bonus',
            'currency',
            'type',
            'vager',
            'min_sum_pay',
            'game',
            'spins',
            'lines',
            'bet',
            'show',
            'sort_index',
            'time',
            'created',
        ];

        $this->vidgets['show'] = new Vidget_CheckBox('show', $this->model);
        $this->vidgets['share_prize'] = new Vidget_CheckBox('share_prize', $this->model);

        $this->vidgets['time'] = new Vidget_Timestamp('time', $this->model);
        $this->vidgets['time']->param('encashment_time',$this->encashment_time);
        $this->vidgets['time']->param('zone_time',$this->zone_time);

        $this->vidgets['created'] = new Vidget_Timestamp('created', $this->model);
        $this->vidgets['created']->param('encashment_time',$this->encashment_time);
        $this->vidgets['created']->param('zone_time',$this->zone_time);

        $office = new Vidget_Officecurrency('currency', $this->model);
        $this->vidgets['currency'] = $office;

    }






}


