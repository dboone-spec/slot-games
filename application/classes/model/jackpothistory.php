<?php

    class Model_JackpotHistory extends ORM
    {

        protected $_table_name = "jackpot_history";
        protected $_created_column = array('column' => 'created', 'format' => true);
        protected $_serialize_columns = ['cards'];

        public function labels()
        {
            return [
                    'user_id'=>'User Id',
                    'external_id'=>'Partner User Id'
            ];
        }
    }
