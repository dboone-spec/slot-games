<?php

class Model_Counter extends ORM{

	public function labels()
	{
		return [
			'id' => '#',
			'type' => __('Тип'),
			'game' => __('Игра'),
			'in' => __('Ввод'),
			'out' => __('Вывод'),
			'office_id' => __('№ ППС'),
		];
	}
}

