<?php

class Model_Archivebet extends ORM
{

    protected $_table_name = 'bets_archive';
	protected $_created_column = array('column' => 'created', 'format' => true);

	public function labels()
	{
            $l = [
                'user_id' => __('User Id'),
                'user_email' => __('Email'),
                'amount' => __('Ставка'),
                'balance' => __('Баланс'),
                'msrc' => __('# Терминала'),
                'win' => __('Выигрыш'),
                'game' => __('Игра'),
                'created' => __('Время'),
                'result' => __('Результат'),
                'game_type' => __('Бренд'),
                'currency' => __('Валюта'),
                'office_id' => __('ППС'),
                'come'=>__('Lines'),
                'balance_before'=>__('Balance before'),
                'balance_after'=>__('Balance after'),
                'visible_name'=>__('Visible name'),
                'external_id'=>__('Partner User Id'),

            ];

            if(person::$role!='sa') {
                $l['user_id'] = __('Логин');
            }

            return $l;
	}

	protected $_belongs_to = [
		'user' => [
			'model'		 => 'user',
			'foreign_key'	 => 'user_id',
		],
		'gamem' => [
			'model'		 => 'game',
			'foreign_key'	 => 'game_id',
		],
        'office' => [
			'model'		 => 'office',
			'foreign_key'	 => 'office_id',
		],
	];

}
