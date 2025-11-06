<?php

class Model_Person_Office extends ORM {

    protected $_belongs_to = [
        'office' => [
            'model'       => 'office',
            'foreign_key' => 'office_id',
        ],
    ];
    
    protected $_has_many = [
        'offices' => [
            'model'       => 'person_office',
            'foreign_key' => 'person_id',
        ],
    ];
    
}