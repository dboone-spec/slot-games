<?php

class Model_User_Message extends ORM {
    protected $_created_column = array('column' => 'created', 'format' => true);
    
    public $params_message = [
        "push" => 0,
        "show" => 1,
        "sended" => 0,
    ];
}

