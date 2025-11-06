<?php

class Model_Status extends ORM
{
    protected $_table_name='status';
    
    public function labels() {
        return [
            'last' => 'Последний запуск',
            'value' => 'Вкл/Выкл',
        ];
    }
}
