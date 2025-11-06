<?php

class Model_Fsbackhistory extends ORM {

    protected $_table_name = 'fsback_history';
    protected $_created_column = array('column' => 'created', 'format' => true);

    public function labels()
    {
        return [
                'user_id'=>'User Id',
                'external_id'=>'Partner User Id'
        ];
    }
}

