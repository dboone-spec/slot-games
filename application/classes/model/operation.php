<?php

class Model_Operation extends ORM {
	protected $_created_column = array('column' => 'created', 'format' => true);

    public function labels() {
        return [
            'created' => __('Дата'),
            'updated_id' => __('Логин'),
            'msrc' => __('# Терминала'),
            'person_id' => __('Персонал'),
            'office_id' => __('ППС'),
            'type' => __('Тип'),
            'amount' => __('Сумма'),
            'before' => __('Баланс до'),
            'after' => __('Баланс'),
            'office_amount' => __('Баланс ППС'),
        ];
    }

    protected $_belongs_to = [
		'user' => [
			'model'		 => 'user',
			'foreign_key'	 => 'updated_id',
		],
		'person' => [
			'model'		 => 'person',
			'foreign_key'	 => 'person_id',
		],
	];
}