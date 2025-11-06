<?php


class Model_Bonus_Codeentered extends ORM {
	
	
	protected $_table_name='bonus_codes_entered';
	protected $_created_column = array('column' => 'created', 'format' => true);
    
    protected $_belongs_to = [
		'bonuscode' => [
			'model'		 => 'bonuscode',
			'foreign_key'	 => 'code_id',
		],
        'user' => [
			'model'		 => 'user',
			'foreign_key'	 => 'user_id',
		],
	];
    
    
    public function labels() {
        return [
            'id' => 'ID',
            'created' => 'Создан',
            'user_id' => 'ID пользователя',
            'ip' => 'IP пользователя',
            'code_id' => 'Бонус код',
            'currency' => 'Валюта',
            'used' => 'Использован',
        ];
    }
	
}
