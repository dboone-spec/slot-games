<?php


class Model_Payment_System extends ORM{

	protected $_created_column = array('column' => 'created', 'format' => true);
	protected $_updated_column = array('column' => 'updated', 'format' => true);
	protected $_serialize_columns = array('currency');

    protected $_has_many = array(
            'attr' => array(
                'model'       => 'payment_field',
                'foreign_key' => 'payment_system_id',
            ),
    );
    
    protected $_belongs_to = [
        'currency_model' => [
            'model'		 => 'currency',
            'foreign_key'	 => 'currency_id',
        ],
    ];

    public function labels() {
        return [
            'id' => 'Идентификатор',
			'gate' => 'Направление',
            'min_out' => 'Минимальная сумма вывода',
            'max_out' => 'Максимальная сумма вывода',
			'comission_system' => 'Комиссия(%)',
			'direction' => 'Действие',
            'fixed_commission' => 'Фиксированная комиссия',
            'visible_name' => 'Тип',
			'use' => 'Используется?',
        ];
    }

}

