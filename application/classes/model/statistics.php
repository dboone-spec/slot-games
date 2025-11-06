<?php

class Model_Statistics extends ORM
{

	protected $_table_name = 'statistics';

	public function labels()
	{
		return [
			'date'		 => __('Дата'),
			'office_id'	 => __('ППС [ИД]'),
			'type'		 => __('Тип игры'),
			'game'		 => __('Игра'),
			'bettype'	 => __('Тип ставки'),
			'amount_in'	 => 'IN',
			'amount_out'	 => 'OUT',
			'count'		 => __('Количество ставок'),

			'persent'	 => '% out/in',
		];
	}

}
