<?php

class Model_PinupFreespin extends ORM
{
    protected $_table_name='pinup_freespins';
	protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_serialize_columns = array('games');
}
