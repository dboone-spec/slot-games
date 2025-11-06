<?php

class Model_Gamblestat extends ORM{

    protected $_table_columns = array(
            'id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'id',
                    'column_default' => 'nextval(\'gamblestats_id_seq\'::regclass)',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'red' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'red',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'black' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'black',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'suit0' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'suit0',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'suit1' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'suit1',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'suit2' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'suit2',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'suit3' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'suit3',
                    'column_default' => '0',
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
    );
}

