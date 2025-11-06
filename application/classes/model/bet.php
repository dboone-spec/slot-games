<?php

class Model_Bet extends ORM
{

	protected $_created_column = array('column' => 'created', 'format' => true);

    protected $_table_columns = array(
            'id' =>
            array(
                    'type' => 'int',
                    'min' => '-9223372036854775808',
                    'max' => '9223372036854775807',
                    'column_name' => 'id',
                    'column_default' => 'nextval(\'bets_id_seq\'::regclass)',
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
            'amount' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'amount',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '18',
                    'numeric_scale' => '8',
                    'datetime_precision' => NULL,
            ),
            'win' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'win',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '18',
                    'numeric_scale' => '8',
                    'datetime_precision' => NULL,
            ),
            'real_amount' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'real_amount',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '18',
                    'numeric_scale' => '8',
                    'datetime_precision' => NULL,
            ),
            'real_win' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'real_win',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '18',
                    'numeric_scale' => '8',
                    'datetime_precision' => NULL,
            ),
            'game' =>
            array(
                    'type' => 'string',
                    'column_name' => 'game',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'info' =>
            array(
                    'type' => 'string',
                    'column_name' => 'info',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'request_id' =>
            array(
                    'type' => 'string',
                    'column_name' => 'request_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'session_id' =>
            array(
                    'type' => 'string',
                    'column_name' => 'session_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
			'game_session_id' =>
            array(
                    'type' => 'string',
                    'column_name' => 'game_session_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'fs_uuid' =>
            array(
                    'type' => 'string',
                    'column_name' => 'fs_uuid',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
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
            'come' =>
            array(
                    'type' => 'string',
                    'column_name' => 'come',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'country' =>
            array(
                'type' => 'string',
                'column_name' => 'country',
                'column_default' => NULL,
                'is_nullable' => true,
                'data_type' => 'character varying',
                'character_maximum_length' => NULL,
                'numeric_precision' => NULL,
                'numeric_scale' => NULL,
                'datetime_precision' => NULL,
            ),
            'result' =>
            array(
                    'type' => 'string',
                    'column_name' => 'result',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
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
            'game_type' =>
            array(
                    'type' => 'string',
                    'column_name' => 'game_type',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'type' =>
            array(
                    'type' => 'string',
                    'column_name' => 'type',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '6',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
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
            'method' =>
            array(
                    'type' => 'string',
                    'column_name' => 'method',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '15',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'balance' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'balance',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '14',
                    'numeric_scale' => '2',
                    'datetime_precision' => NULL,
            ),
            'is_freespin' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'is_freespin',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'external_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'external_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'initial_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'initial_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'calc' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'calc',
                    'column_default' => '1',
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'fg_level' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'fg_level',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
    );

    public function labels()
	{
            $l = [
                'user_id' => __('User Id'),
                'user_email' => __('Email'),
                'amount' => __('Ставка'),
                'real_amount' => __('Ставка'),
                'balance' => __('Баланс'),
                'msrc' => __('# Терминала'),
                'win' => __('Выигрыш'),
                'real_win' => __('Выигрыш'),
                'game' => __('Игра'),
                'created' => __('Время'),
                'result' => __('Результат'),
                'game_type' => __('Бренд'),
                'currency' => __('Валюта'),
                'currency' => 'Cur',
                'office_id' => __('ППС'),
                'come'=>__('Lines'),
                'balance_before'=>__('Balance'."<br>".'before'),
                'balance_after'=>__('Balance'."<br>".'after'),
                'visible_name'=>__('Visible name'),
                'is_freespin'=>__('FS'),
                'external_id'=>__('Partner User Id'),

            ];

            if(person::$role!='sa') {
                $l['user_id'] = __('Логин');
            }

            return $l;
	}

    public function unselect_fields($fields=[])
    {
        foreach($fields as $f) {
            unset($this->_table_columns[$f]);
        }
        return $this;
    }

    public function select_fields($fields)
    {
        $this->_table_columns = $fields;
        return $this;
    }

	protected $_belongs_to = [
		'user' => [
			'model'		 => 'user',
			'foreign_key'	 => 'user_id',
		],
		'office' => [
			'model'		 => 'office',
			'foreign_key'	 => 'office_id',
		],
		'gamem' => [
			'model'		 => 'game',
			'foreign_key'	 => 'game_id',
		],
        'pokerbets' => [
            'model' => 'pokerbet',
            'foreign_key' => 'external_id',
        ],
	];

    public function roundNum() {
        $round_num=$this->id;

        if(!empty($this->poker_bet_id) && $this->poker_bet_id>0) {
            $round_num=$this->poker_bet_id;
        }
        elseif(!empty($this->initial_id) && $this->initial_id>0) {
            $round_num=$this->initial_id;
        }
        elseif(!empty($this->external_id) && $this->external_id>0) {
            $round_num=$this->external_id;
        }
        return $round_num;
    }

    public function isComplete() {
        $fin=true;

        if(!empty($this->poker_bet_id) && $this->poker_bet_id>0) {
            $fin=true;
        }
        elseif(!empty($this->initial_id) && $this->initial_id>0) {
            $fin=true;
        }
        elseif(th::isMoonGame($this->game)) {
            $fin=false;
        }
        elseif(in_array($this->game,['acesandfaces','jacksorbetter','tensorbetter']) && $this->type=='normal') {
            $fin=false;
        }
        return $fin;
    }

}
