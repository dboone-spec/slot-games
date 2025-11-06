<?php

class Model_BetconstructFreespin extends ORM
{
    protected $_table_name='betconstruct_freespins';
	protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_serialize_columns = array('gameIds');
}
