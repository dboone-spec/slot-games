<?php

class Model_Usersocial extends ORM {
    protected $_table_name='users';
    protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_updated_column = array('column' => 'updated', 'format' => true);

    public function filters()
    {
        return [
            'msrc' => [
                [[$this, 'check_msrc']]
            ],
        ];
    }

    public function check_msrc($value) {
        $value = trim($value);
        $value = strlen($value)>0?$value:null;

        return $value;
    }

}