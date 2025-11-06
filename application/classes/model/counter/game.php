<?php

class Model_Counter_Game extends ORM
{

    protected $_primary_key = 'game';
    protected $_table_name  = 'counters_games';

    public function labels()
    {
        return [
                'game' => 'Игра',
                'type' => 'Тип',
                'provider' => 'Провайдер',
                'in' => 'IN',
                'out' => 'OUT',
                'double_in' => 'IN',
                'double_out' => 'OUT',
                'percent_normal' => '%',
                'percent_double' => '%',
                'percent_free' => '%',
                'percent_bonus' => '%',
        ];
    }
}
