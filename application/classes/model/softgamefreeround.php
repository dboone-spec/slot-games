<?php

class Model_SoftGameFreeround extends ORM
{
    protected $_table_name='softgame_freerounds';
	protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_serialize_columns = array('games','user_ids');
}
