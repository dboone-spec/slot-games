<?php

class Model_Freespinhistory extends ORM {

    protected $_table_name = 'freespins_history';
    protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_serialize_columns = array('log','gameids');

    protected $_table_columns = array(
            'id' =>
            array(
                    'type' => 'int',
                    'min' => '-9223372036854775808',
                    'max' => '9223372036854775807',
                    'column_name' => 'id',
                    'column_default' => 'nextval(\'freespins_history_id_seq\'::regclass)',
                    'is_nullable' => false,
                    'data_type' => 'bigint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '64',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'user_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'user_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'game_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'game_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'lines' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'lines',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'amount' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'amount',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '12',
                    'numeric_scale' => '2',
                    'datetime_precision' => NULL,
            ),
            'fs_count' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'fs_count',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'fs_played' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'fs_played',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'src' =>
            array(
                    'type' => 'string',
                    'column_name' => 'src',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '8',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'active' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'active',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'created' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'created',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'log' =>
            array(
                    'type' => 'string',
                    'column_name' => 'log',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '255',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'freespin_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'freespin_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'office_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'office_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'type' =>
            array(
                    'type' => 'string',
                    'column_name' => 'type',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '30',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'expirtime' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'expirtime',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'gameids' =>
            array(
                'type' => 'string',
                'column_name' => 'gameids',
                'column_default' => NULL,
                'is_nullable' => true,
                'data_type' => 'character varying',
                'character_maximum_length' => '0',
                'numeric_precision' => NULL,
                'numeric_scale' => NULL,
                'datetime_precision' => NULL,
            ),
            'fs_offer_type' =>
            array(
                    'type' => 'string',
                    'column_name' => 'fs_offer_type',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '46',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'fs_offer_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'fs_offer_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'sum_win' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'sum_win',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '12',
                    'numeric_scale' => '2',
                    'datetime_precision' => NULL,
            ),
    );
    protected $_belongs_to = [
		'user' => [
			'model'		 => 'user',
			'foreign_key'	 => 'user_id',
		],
		'game' => [
			'model'		 => 'game',
			'foreign_key'	 => 'game_id',
		],
		'office' => [
			'model'		 => 'office',
			'foreign_key'	 => 'office_id',
		],
		'freespin' => [
			'model'		 => 'freespin',
			'foreign_key'	 => 'freespin_id',
		],
	];

    public function labels()
    {
        return [
                'user_id'=>'User Id',
                'fs_count'=>'FS count',
                'fs_played'=>'FS played',
                'game_id'=>'Game',
                'external_id'=>'Partner User Id',
                'office_id'=>'Office',
                'active'=>'Status',
        ];
    }

    public function status() {
        if($this->loaded()) {
            if($this->active=='-2') {
                return 'declined (auto)';
            }
            if($this->active=='-1') {
                return 'declined';
            }
            if($this->active=='1') {
                return 'accepted';
            }
            return 'new';
        }
        return '?';
    }

}

