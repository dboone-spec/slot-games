<?php

class Model_SoftSwissAward extends ORM
{
    protected $_table_name='softswiss_awards';
	protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_serialize_columns = array('games','user_ids');
}
