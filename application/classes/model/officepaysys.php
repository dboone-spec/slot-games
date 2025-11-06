<?php


class Model_Officepaysys extends ORM {
    protected $_table_name = 'office_paysystems';

    protected $_belongs_to = [
	    'office' => [
		    'model'	=> 'office',
		    'foreign_key' => 'office_id',
	    ],
    ];
}