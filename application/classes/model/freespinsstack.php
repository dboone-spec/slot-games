<?php

class Model_freespinsstack extends ORM
{

    protected $_table_name        = 'freespins_stack';
    protected $_created_column    = array('column' => 'created','format' => true);
    protected $_serialize_columns = array('params');
    protected $_table_columns     = array(
            'set_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'set_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'status' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'status',
                    'column_default' => NULL,
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
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'last_user_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'last_user_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'id',
                    'column_default' => 'nextval(\'freespins_stack_id_seq\'::regclass)',
                    'is_nullable' => false,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'params' =>
            array(
                    'type' => 'string',
                    'column_name' => 'params',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'text',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'name' =>
            array(
                    'type' => 'string',
                    'column_name' => 'name',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '30',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'visible_name' =>
            array(
                    'type' => 'string',
                    'column_name' => 'visible_name',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '30',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'mass' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'mass',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'game' =>
            array(
                    'type' => 'string',
                    'column_name' => 'game',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '16',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
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
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'dentab_index' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'dentab_index',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'lines' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'lines',
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
            'login' =>
            array(
                    'type' => 'string',
                    'column_name' => 'login',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '40',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'updated' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'updated',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
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
            'time_to_start' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'time_to_start',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
    );

    public function getStatusText() {
        $a=$this->status;
        if($a==0){
            $a='new';
        }
        elseif($a==1) {
            $a='processing';
        }
        elseif($a==2) {
            $a='finished';
        }
        elseif($a==3) {
            $a='canceled';
        }
        return $a;
    }

}
