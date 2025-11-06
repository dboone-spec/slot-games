<?php

class Controller_Admin_Reportday extends Super{


    public $scripts = ['/js/compiled/main.4ecde5c.js'];
	public $mark='Отчет по дням'; //имя
	public $model_name='usersstatistics'; //имя модели

    public function configure()
	{

		$this->search = [
//			'date',
//			'pay_in_out',
//            'pay_forecast',
//            'bet_in_out',
//            'bet_forecast',
//			'reg_count',
//            'count_reg_deposit',
//            'count_reg_no_deposit',
		];


		$this->list = [
			'date',
            'currency_id',
			'payments_in',
            'profit',
            'forecast',
            'count_deposits',
		];

        $currency = new Vidget_Related('currency_id', $this->model);
		$currency->param('related', 'currency');
		$currency->param('name', 'code');
        $this->vidgets['currency_id'] = $currency;

        $this->order_by = ['date','desc'];
	}
}


