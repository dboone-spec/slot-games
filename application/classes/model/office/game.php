<?php

class Model_Office_Game extends ORM {

//    protected $_load_with = ['game'];
    protected $_db_group = 'games';

    protected $_table_columns = array(
            'id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'id',
                    'column_default' => 'nextval(\'office_games_id_seq\'::regclass)',
                    'is_nullable' => false,
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
            'enable' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'enable',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'z' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'z',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '14',
                    'numeric_scale' => '2',
                    'datetime_precision' => NULL,
            ),
    );

    protected $_belongs_to = [
        'game' => [
            'model' => 'game',
            'foreign_key' => 'game_id',
        ],
        'office' => [
            'model' => 'office',
            'foreign_key' => 'office_id',
        ],
    ];
    public function labels() {
        return [
            'game_id' => __('Игра'),
            'office_id' => __('ППС'),
            'enable' => __('Включена'),
            'z' => __('Коэффициент возврата'),
        ];
    }
}