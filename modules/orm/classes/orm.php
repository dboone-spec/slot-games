<?php defined('SYSPATH') or die('No direct script access.');

class ORM extends Kohana_ORM {

		public function select_fields($fields)
    {
        $this->_table_columns = $fields;
        return $this;
    }

}
