<?php

//model only for admin

class Model_Bonuscode extends Model_Bonus_Code {
    protected $_primary_key = 'id';
    protected $_table_name='bonus_codes';
    protected $_created_column = array('column' => 'created', 'format' => true);

    protected $_belongs_to = [
	    'user' => [
		    'model'		 => 'user',
		    'foreign_key'	 => 'user_id',
	    ],
    ];
    
    public function labels() {
        return [
            'name' => 'Код',
            'count' => 'Количество (9999)',
            'bonus' => 'Бонус (процент/100 или сумма, для fixed_freespin указать сумму, для bonus_freespin указать процент от пополнения/100)',
            'created' => 'Создан',
            'type' => 'Тип (unique_user,all,freespin,fixed,fixed_freespin,bonus_freespin)',
            'min_sum_pay' => 'Минимальная сумма пополнения',
            'game' => 'Игра (dolphinsd ..)',
            'spins' => 'Спины',
            'lines' => 'Линии',
            'bet' => 'Ставка(на линию)',
            'show' => 'Показывать в платежке?',
            'sort_index' => 'Сортировка для платежки',
            'vager' => 'Вейджер',
            'time' => 'Действителен до',
            'currency' => 'Валюта',
            'share_prize' => 'Приз для начисления в лотерее/турнире?',
        ];
    }
}

