<?php

class Controller_Admin_Paymentsystem extends Super{



	public $mark='Платежные системы'; //имя
	public $model_name='Payment_System'; //имя модели
	public $order_by=array('image'); // сортировка
	public $controller='paymentsystem';
    public $scripts = ['/js/compiled/main.4ecde5c.js'];



	public function configure(){


		$this->search = [
			'id',
			'gate',
			'visible_name',
			'currency',
			'direction',
			'use',
		];


		$this->list = [
			'id',
			'gate',
            'min_out',
            'max_out',
			'visible_name',
			'comission_system',
            'fixed_commission',
			'direction',
			'use',
		];

        $this->show = [
            'id',
			'gate',
            'min_out',
            'max_out',
			'visible_name',
			'comission_system',
            'fixed_commission',
			'direction',
			'use',
        ];

        $this->vidgets['use'] = new Vidget_CheckBox('use', $this->model);
	}
}
