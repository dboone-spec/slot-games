<?php

class Model_User_Profit extends ORM {
    
    protected $_belongs_to = [
        'person' => [
            'model'       => 'person',
            'foreign_key' => 'person_id',
        ],
    ];
    
    public function labels() {
        return [
            'date' => 'Дата',
            'amount' => 'Проигрыш пользователей за смену',
            'profit' => 'Бонус для ТП',
            'person_id' => 'Логин ТП',
        ];
    }
}

