<?php

class Model_Login extends ORM {

    protected $_created_column = array('column' => 'created', 'format' => true);


    public function labels()
	{
		return [
			'user_id'	 => 'Пользователь(Id)',
            'user_email'	 => 'Пользователь(Email)',
			'created'	 => 'Время',
		];
	}

	protected $_belongs_to = [
		'user' => [
			'model'		 => 'user',
			'foreign_key'	 => 'user_id',
		],
	];

    public function filters()
    {
        return [
            'fingerprint' => [
                [[$this, 'check_fingerprint']]
            ],
        ];
    }

    public function check_fingerprint($value) {
        $value = trim($value);
        $value = strlen($value)>0?$value:null;

        return $value;
    }

}

