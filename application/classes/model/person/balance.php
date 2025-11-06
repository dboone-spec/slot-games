<?php

class Model_Person_Balance extends ORM {
            
    protected $_belongs_to = [
        'currency' => [
            'model'		 => 'currency',
            'foreign_key'	 => 'currency_id',
        ],
    ];
    
}