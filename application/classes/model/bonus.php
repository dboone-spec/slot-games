<?php

class Model_Bonus extends ORM {

    protected $_created_column = array('column' => 'created', 'format' => true);
    
    protected $_belongs_to = [
	    'user' => [
		    'model'		 => 'user',
		    'foreign_key'	 => 'user_id',
	    ]
    ];      
    
    public function labels() {
        return [
            "id" => "ИД",
            "user_id" => "Пользователь(ID)",
            "bonus" => "Сумма бонуса",
            "created" => "Создан",
            "type" => "Тип",
            "referal_id" => "Реферал(ID)",
            "payed" => "Выплачен?",
            "log" => "Лог",
            "last_notification" => "Последнее оповещение",
            "accrual_days_ago" => "Дней прошло с момента начисления",
            "currency" => "Валюта",
        ];
    }
}

