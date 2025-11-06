<?php

class Model_Usersstatistics extends ORM { 
    protected $_table_name='users_statistics';
    
    protected $_belongs_to = [
        'currency' => [
            'model'		 => 'currency',
            'foreign_key'	 => 'currency_id',
        ],
    ];
    
    public function labels()
	{
		return [
            'id' => 'ID',
			'date' => 'Дата',
            'payments_in' => 'Платежи',
            'profit' => 'Доход',
            'forecast' => 'Прогноз',
            'count_deposits ' => 'Кол-во игроков сделавших депозит за день',
            'currency_id' => 'Валюта',
			'pay_in_out' => 'Платежи',
            'pay_forecast' => 'Платежи прогноз',
            'bet_in_out' => 'Ставки',
            'bet_forecast' => 'Ставки прогноз',
			'reg_count'	 => 'Кол-во рег.',
            'count_reg_deposit' => 'Кол-во депозитов зарег-ых',
            'count_reg_no_deposit' => 'Кол-во зарег-ых без депозита',
            'users_amount'	 => 'Разница балансов польз.',
		];
	}
    
}