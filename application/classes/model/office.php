<?php

    class Model_Office extends ORM
    {

        public $need_create_default_games=true;

        protected $_created_column = array('column' => 'created','format' => true);
        protected $_serialize_columns = ['payment_sums','white_ips'];
        protected $_table_columns = [
            'id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'id',
                'column_default' => 'nextval("offices_id_seq"::regclass)',
                'is_nullable' => '',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'currency_id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'currency_id',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'created_time' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'created_time',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'currency_coeff' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'currency_coeff',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'limit_fsapi' =>
                [
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'limit_fsapi',
                    'column_default' => '10',
                    'is_nullable' => '1',
                    'data_type' => 'smallint',
                    'character_maximum_length' => '',
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => '',
                ],
            'external_name' =>
            [
                'type' => 'string',
                'column_name' => 'external_name',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '30',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'min_sum_pay' =>
            [
                'type' => 'string',
                'column_name' => 'min_sum_pay',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'payment_sums' =>
            [
                'type' => 'string',
                'column_name' => 'payment_sums',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'white_ips' =>
            [
                'type' => 'string',
                'column_name' => 'white_ips',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'amount' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'amount',
                'column_default' => '300000',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'fsamount' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'fsamount',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'alert_max_win' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'alert_max_win',
                'column_default' => '10000',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'bet_min' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'bet_min',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '7',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'bet_max' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'bet_max',
                'column_default' => '50000',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '7',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'default_bet' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'default_bet',
                'column_default' => '50000',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '7',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'encashment_time' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'encashment_time',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'promopanel' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'promopanel',
                'column_default' => '1',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'zone_time' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'zone_time',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'visible_name' =>
            [
                'type' => 'string',
                'column_name' => 'visible_name',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '32',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'cashback' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'cashback',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'blocked' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'blocked',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'fslastmonth' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'fslastmonth',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'apienable' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'apienable',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'showfakeversion' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'showfakeversion',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'apitype' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'apitype',
                'column_default' => '1',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'visualization' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'visualization',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'secretkey' =>
            [
                'type' => 'string',
                'column_name' => 'secretkey',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '40',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'gameapiurl' =>
            [
                'type' => 'string',
                'column_name' => 'gameapiurl',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '100',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'created' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'created',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'owner' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'owner',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],


            'k_to_jp' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'k_to_jp',
                'column_default' => '0.005',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'k_max_lvl' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'k_max_lvl',
                'column_default' => '1',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],


            'enable_jp' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'enable_jp',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'bank' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'bank',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'max_win_limit' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'max_win_limit',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'users' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'users',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'min_deposit' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'min_deposit',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'max_deposit' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'max_deposit',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'min_withdraw' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'min_withdraw',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'max_withdraw' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'max_withdraw',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],


            'use_bank' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'use_bank',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'bonus_diff_last_bet' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'bonus_diff_last_bet',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'bonus_pay_period' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'bonus_pay_period',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'bonus_coeff' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'bonus_coeff',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'enable_bia' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'enable_bia',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],


            'rtp' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'rtp',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'games_rtp' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'games_rtp',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'is_test' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'is_test',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'strict_double' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'strict_double',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'comment' =>
            [
                'type' => 'string',
                'column_name' => 'comment',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'lang' =>
            [
                'type' => 'string',
                'column_name' => 'lang',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '2',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'seamlesstype' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'seamlesstype',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'enable_moon_dispatch' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'enable_moon_dispatch',
                'column_default' => '0',
                'is_nullable' => '0',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'moon_delayed_bets' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'moon_delayed_bets',
                'column_default' => '0',
                'is_nullable' => '0',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            //default, terminal, users, terminal+users
            //if api=1 then error
            'workmode' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'workmode',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            //classic, modern, classic+modern [0,1,2]
            'gameui' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'gameui',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'default_dentab' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'default_dentab',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'dentabs' =>
            [
                'type' => 'string',
                'column_name' => 'dentabs',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'tg_cashusers' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'tg_cashusers',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
			'partner' =>
            [
                'type' => 'string',
                'column_name' => 'partner',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
			'max_win_eur' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'max_win_eur',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
			'moon_min_bet' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'moon_min_bet',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
			'moon_max_bet' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'moon_max_bet',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
			'moon_max_win' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'moon_max_win',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
			'check_new_ls' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'check_new_ls',
                'column_default' => '0',
                'is_nullable' => '0',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
			'ls_first_wager' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'ls_first_wager',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
			'show_game_history' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'show_game_history',
                'column_default' => '0',
                'is_nullable' => '0',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

        ];

        public function __construct($id = NULL)
        {
            $m = parent::__construct($id);
            if(PROJECT==2) {
                $this->_table_columns['postpay']=[
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'postpay',
                    'column_default' => '1',
                    'is_nullable' => '1',
                    'data_type' => 'smallint',
                    'character_maximum_length' => '',
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => '',
                ];

                $this->_table_columns['agtenable']=[
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'agtenable',
                    'column_default' => '0',
                    'is_nullable' => '1',
                    'data_type' => 'integer',
                    'character_maximum_length' => '',
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => '',
                ];
            }
            return $m;
        }

        protected $_belongs_to = [
            'currency' => [
                'model' => 'currency',
                'foreign_key' => 'currency_id',
            ],
            'person' => [
                'model' => 'person',
                'foreign_key' => 'owner',
            ],
        ];
        protected $_has_many = [
            'settings' => [
                'model' => 'office_setting',
                'foreign_key' => 'office_id',
            ],
            'jackpots' => [
                'model' => 'jackpot',
                'foreign_key' => 'office_id',
            ],
        ];

        public function labels()
        {
            $a = [
                'id' => __('ID'),
                'visible_name' => __('Название'),
                'amount' => __('Баланс'),
                'zone_time' => __('Часовой пояс'),
                'blocked' => __('Заблокирован'),
                'lang' => __('Язык'),
                'currency_id'=>__('Currency'),
                'enable_bia'=>__('Enable bonuses'),
                'bonus_diff_last_bet'=>__('Diff last bet (Hours)'),
                'bonus_pay_period'=>__('Bonus pay period (Days)'),
                'bonus_coeff'=>__('Bonus coeff (Persent)'),
                'encashment_time'=>__('Время инкасации'),
                'rtp'=>__('RTP'),
                'games_rtp'=>__('Games RTP (Slot config)'),
                'k_to_jp'=>__('Jackpot\'s accumulation from bets'),
                'k_max_lvl'=>__('Set of jackpot'),
                'secretkey'=>__('Secret key'),
                'gameapiurl'=>__('API URL'),
                'default_dentab'=>__('Default denomination'),
                'enable_jp'=>__('Enable JP'),
                'enable_moon_dispatch'=>__('Enable ToTheMoon results dispatch'),
                'strict_double'=>__('Disable X2 at games'),
                'selectgames'=>__('Enabled games'),
                'showfakeversion'=>__('Show ingame version'),
				'ls_first_wager' => __('Wager for LS on first day'),
				'show_game_history' => __('Show bet history in game'),
				'max_win_eur' => __('Max explosure in EUR (default 1 500 000 EUR)'),
            ];

            if(PROJECT==1) {
                $a['enable_bia']='Enable FSback';
                $a['apienable']='API';
                $a['workmode']='Work Mode';
                $a['gameui']='Game UI version';
				$a['check_new_ls'] = 'No LS for non-players';
            }
            return $a;
        }

        public function __get($column)
        {
            if(auth::$user_id==540616 && $column=='min_sum_pay') {
                return 0;
            }
			if ($this->loaded() && $column == 'enable_bia' && parent::__get('id')==1219 && time()>=mktime(23,59,59,1,31)) {
				return 0;
			}
            if(!$this->loaded() && $column=='k_to_jp') {
                return 0.005;
            }
            return parent::__get($column);
        }

        public function updateFSamount($amount) {
            db::query(Database::UPDATE,"update offices set 
                   fsamount=
                       case 
                           when EXTRACT( epoch from date_trunc('month', now() at time zone 'utc' ) )<fslastmonth 
                           then :newamount 
                           else fsamount+:newamount 
                       end,
                    fslastmonth=EXTRACT( epoch from date_trunc('month', now() at time zone 'utc' ) )
                    where id=:id")
                ->param(':id',$this->id)
                ->param(':newamount',$amount)
                ->execute();
        }

        public function checkFSApiLimit($amount) {

            if($this->is_test) {
                return true;
            }
			
			if (th::isB2B($this->owner)) {
                return true;
            }

            $limit=!empty($this->limit_fsapi)?$this->limit_fsapi:10;

            $start_date=date('Y-m-d', strtotime("first day of last month"));
            $end_date=date('Y-m-d', strtotime("last day of last month"));

            $bettypes=[
                'normal', //normal
                'norafs', //normal from freespin from api
                'norcfs', //normal from freespin from cashback
                'norlfs', //normal from freespin from luckyspins
                'normfs', //normal from freespin from moon cashback

                'double', //double
                'douafs', //double from freespin from api
                'doucfs', //double from freespin from cashback
                'doulfs', //double from freespin from luckyspins
                'doumfs', //double from freespin from moon cashback

                'free',   //free
                'freafs', //free from freespin from api
                'frecfs', //free from freespin from cashback
                'frelfs', //free from freespin from luckyspins
                'fremfs', //free from freespin from moon cashback
                'jp'
            ];

            $sql="select sum(((case when  bettype = 'norcfs' or bettype = 'norlfs' or bettype = 'normfs' then 0 else amount_in end)-amount_out)) as win
                    from statistics s
                    where
                    s.office_id=:o_id
                    and s.date >= :time_from
                    and s.date <= :time_to and s.bettype in :types";

            $windata=db::query(1,$sql)
                ->param(':o_id',$this->id)
                ->param(':time_from',$start_date)
                ->param(':time_to',$end_date)
                ->param(':types',$bettypes)
                ->execute()
                ->as_array();

            $win=$windata[0]['win'] ?? 0;

            $current_fs_amount=$this->fsamount;
            if($this->fslastmonth!=mktime(0,0,0,date('m'),1)) {
                $current_fs_amount=0;
            }
			
			if($win<(10000/$this->currency->val) && ($amount + $current_fs_amount)<(100/$this->currency->val)) {
				return true;
			}

            if(($win*$limit/100)>($amount+$current_fs_amount)) {
                return true;
            }

            return false;
        }

        public function clearcounters($type='all') {

            $sql = 'insert into counters_history ("date",office_id,game_id,"in","out","count","free",free_count,bonus,bonus_count,double_in,double_out,double_count,
                fs_api_in,fs_api_out,fs_api_count,fs_cash_in,fs_cash_out,fs_cash_count,fs_lucky_in,fs_lucky_out,fs_lucky_count)
                SELECT extract(\'epoch\' from CURRENT_TIMESTAMP) as "date",office_id,game_id,"in","out","count","free",free_count,bonus,bonus_count,double_in,double_out,double_count,
                        fs_api_in,fs_api_out,fs_api_count,fs_cash_in,fs_cash_out,fs_cash_count,fs_lucky_in,fs_lucky_out,fs_lucky_count from counters';

            $sql.=' where office_id = :office_id';

            db::query(Database::INSERT,$sql)
                    ->param(':office_id',$this->id)
                    ->execute();

            $sql = 'delete from counters where office_id = :office_id';

            db::query(Database::DELETE,$sql)
                ->param(':office_id',$this->id)
                ->execute();
        }

        public function setzimperium($z=94) {
            $this->imperium_hall = arr::get(Kohana::$config->load('imperium'),$z);
        }

        public function setz($z=94) {

            db::query(Database::UPDATE,'update office_games set z=:z where office_id = :oid')
                    ->param(':z',$z/100)
                    ->param(':oid',$this->id)
                    ->execute();
        }

        public function sign($params) {

            if(isset($params['bgimg'])) {
                unset($params['bgimg']);
            }

            foreach($params as $k=>$v) {
                if((string)($v)=='') {
                    unset($params[$k]);
                }
            }

            ksort($params, SORT_STRING);
            $signString = implode(':', $params);
            $signString.=$this->secretkey;

            return hash('sha256', $signString);
        }

        public function check_sign($params,$sign) {

            if(!$this->loaded()) {
                return false;
            }

            if(!$this->secretkey) {
                return false;
            }
            if(isset($params['bgimg'])) {
                unset($params['bgimg']);
            }

            $office_sign = $this->sign($params);

            if($office_sign!=$sign) {
                return false;
            }

            return true;
        }


        public function activeJackpots() {

            if($this->enable_jp=='0') {
                return [];
            }

            return $this->jackpots->order_by('type')->find_all();
        }

        public function updateEventGames() {
            $event = new Model_Event(['office_id'=>$this->id,'is_auto_gen'=>1]);

            if($event->loaded()) {
                $event->games_ids=$event->randomGames($this->id);
                $event->save();
            }
        }

        public function createProgressiveEventForOffice() {

			if(in_array($this->owner,[1023,1128])) {
				return;
			}

            $office=$this;

            $defaults=[10,12,14,16,18,20,22];
            $def_amount_eur=0.02;

            $currency = $office->currency;

            $one_amount = round($def_amount_eur/$currency->val,$currency->mult);

            $event = new Model_Event();

            $event->h=0;
            $event->m=0;

            $event->dom=-1;
            $event->mon=-1;

            $event->dow=-1;
            $event->starts=time();
            $event->ends=time()+Date::YEAR*12;
            $event->is_auto_gen=1;

            $event->office_id=$this->id;
            $event->active=1;
            $event->once=0;
            $event->fs_amount=$one_amount;
            $event->fs_count=0;
            $event->type='progressive';
            $event->extra_params=$defaults;

            $event->games_ids=$event->randomGames($this->id);

            $event->duration=23*Date::HOUR+59*Date::MINUTE;

            $event->save();
        }

		public static $error_api_text='';

        public static function newFromAPI($parameters = [])
		{
			$visible_name = arr::get($parameters, 'title', '');
			$currency_code = arr::get($parameters, 'currency_code');
			$owner = arr::get($parameters, 'owner');
			$url = arr::get($parameters, 'apiurl', '');
			$secretkey = arr::get($parameters, 'secretkey', '');
			$partner = arr::get($parameters, 'partner');

			$currency = new Model_Currency(['code' => $currency_code, 'source' => 'agt']);

			if (!$currency->loaded() || $currency->disable != 0) {
				self::$error_api_text="Cann't create office with currency $currency_code ";
				return false;
			}

			$params = [
				':currency_id' => $currency->id,
				':external_name' => $visible_name,
				':visible_name' => $owner->comment . " $visible_name {$currency->code}",
				':apienable' => 1,
				':apitype' => 0,
				':bank' => $currency->default_bank,
				':use_bank' => 1,
				':bet_min' => $currency->min_bet,
				':bet_max' => $currency->max_bet,
				':gameapiurl' => $url,
				':bonus_diff_last_bet' => 8,
				':enable_bia' => time(),
				':rtp' => 96,
				':owner' => $owner->id,
				':dentabs' => $currency->default_den,
				':default_dentab' => $currency->default_dentab,
				':k_to_jp' => 0.005,
				':k_max_lvl' => $currency->default_k_max_lvl,
				':enable_jp' => 1,
				':enable_moon_dispatch' => 1,
				':games_rtp' => 97,
				':gameui' => 1,
				':promopanel' => 1,
				':is_test' => 0,
				':seamlesstype' => 1,
				':secretkey' => $secretkey,
				':partner' => $partner,
			];

			$into = arr::map(function ($el) {
				return str_replace(':', '', $el);
			}, array_keys($params));
			$values = array_keys($params);

			$sql = 'insert into offices(' . implode(',', $into) . ')
						values(' . implode(',', $values) . ')
						on conflict(currency_id,external_name,owner,apienable,apitype,is_test) ';

			$updates = [];

			if (!empty($url)) {
				$updates[] = 'gameapiurl=:gameapiurl';
			} else {
				$updates[] = 'gameapiurl=EXCLUDED.gameapiurl';
			}

			if (!empty($secretkey)) {
				$updates[] = 'secretkey=:secretkey';
			} else {
				$updates[] = 'secretkey=EXCLUDED.secretkey';
			}

			if (!empty($partner)) {
				$updates[] = 'partner=:partner';
			} else {
				$updates[] = 'partner=EXCLUDED.partner';
			}

			$sql .= ' do update set ';
			$sql .= implode(',', $updates);
			$sql .= ' returning *';

			$sql .= ',case when xmax::text::int8 > 0 then 0 else 1 end as is_new';

			database::instance()->begin();

			//TODO поификсить создание джекпотаов

			try {
				//создаем игры здесь

				$o = db::query(1, $sql)
					->parameters($params)
					->execute(null, 'Model_Office')[0];

				$is_new = ($o->is_new == '1');

				if (!$is_new) {
					database::instance()->commit();
					return $o;
				}

				database::instance()->direct_query('insert into person_offices (person_id,office_id)
											values (' . $o->owner . ',' . $o->id . ')');


				$sql_games = <<<SQL
					insert into office_games(office_id, game_id, enable)
					Select :office_id, g.id, 1
					From games g
					Where g.provider = 'our' and brand ='agt' and show=1 and g.category!='coming' 
					and g.branded=0
	SQL;

				db::query(Database::INSERT, $sql_games)
					->param(':office_id', $o->id)
					->execute();

				$o->createProgressiveEventForOffice();


				$redis = dbredis::instance();
				$redis->select(1);
				$redis->set('jpa-' . $o->id, 1);

				for ($i = 1; $i <= 4; $i++) {

					$redis->set('jpHotPercent-' . $o->id . '-' . ($i), 0.02);

					$j = new Model_Jackpot();
					$j->office_id = $o->id;
					$j->type = $i;
					$j->active = 1;

					$j->save();
				}

			} catch (Exception $ex) {
				database::instance()->rollback();
				
				self::$error_api_text="Cann't create office. Contact support. ";
				return false;
				
				throw $ex;
			}

			database::instance()->commit();

			th::ceoAlert('Office ' . $o->id . ' [' . $o->visible_name . ']' . ' created!');

			return $o;
		}

        public function create(Validation $validation = NULL){


            $r=parent::create($validation);


            $Gameman=new Model_Person(Person::user()->parent_id);

            if (Person::$role=='client'){
                $sql='insert into person_offices (person_id,office_id)
                        values (:person_id,:office_id)';

                //current person
                db::query(Database::INSERT,$sql)->param(':person_id', Person::$user_id )
                                                ->param(':office_id', $this->id )
                                                ->execute();

                //gameman
                db::query(Database::INSERT,$sql)->param(':person_id', Person::user()->parent_id )
                                                ->param(':office_id', $this->id )
                                                ->execute();

                //parent gameman
                if ($Gameman->parent_id>0){
                    db::query(Database::INSERT,$sql)->param(':person_id', $Gameman->parent_id )
                                                ->param(':office_id', $this->id )
                                                ->execute();

                }

            }

            if(PROJECT==1 && $this->need_create_default_games) {
                th::default_office_games($this->id);

                $this->createProgressiveEventForOffice();

            }

            th::ceoAlert('Office '.$this->id.' ['.$this->visible_name.']'.' created!');

            return $r;

        }

        public function rules()
        {
            return [
                'workmode' => array(
                    array(function(Validation $object)
                        {
                            $data = $object->data();
                            if((int) $data['apienable'] > 0 && (int) $data['workmode'] > 0)
                            {
                                $object->error('workmode',__('Disable API for use this Work Mode'));
                            }
                        },array(':validation')
                    ),
                ),
            ];
        }

        public function get_k_list() {

            if(!$this->loaded()){
                return Kohana::$config->load('agt')['k_list'];
            }

            if(empty($this->dentabs)) {
                $k_list = Kohana::$config->load('agt')['k_list'];
                return $k_list;
            }

            return explode('|',$this->dentabs);

        }
		
		public $sorted_games=[];
		
	public function sort($key = 'id')
    {
        $defaultOffice = 1073;
        $bigId = 10000000;

        $office = $this;

        if (($this->created > time() - 60 * 60 * 24 * 31) || $this->id==777) {
            $office = new Model_Office($defaultOffice);
        }

        $static= kohana::$config->load('static');

        $sql = "select g.id, 
                g.name,COALESCE(gs.sort,$bigId) as sort,
                COALESCE(gs_default.sort,$bigId) as sortDefault,
                g.created as game_created,
                g.branded,
                g.brand,
                g.type as game_type,
                g.mobile,
                g.online,
                g.softg_show,
                g.infin_show,
                g.evenbet_show,
				g.pinup_show,
				g.tvbet_show,
                g.category,
                g.visible_name,
                '{$static['static_domain']}'||replace(g.image,'thumb','sqthumb') as image,
                g.id as game_id,
                g.demo,
                gs.created,
                gs.type,
                Case when (g.branded = 0) then g.site_category else 'branded' end as site_category   
                from games g 
                join office_games og on og.game_id = g.id and og.office_id = :real_oid
                left join games_sort gs on gs.game_id = g.id and gs.office_id = :oid and gs.use =1
                left join games_sort gs_default on gs_default.game_id = g.id and gs_default.office_id = $defaultOffice and gs.use =1
                where  og.enable = 1
                        and g.show=1
                        and brand='agt'
                order by 3,4,g.created desc";



        $data = db::query(1, $sql)
            ->param(':real_oid', $this->id)
            ->param(':oid', $office->id)
            ->execute()
            ->as_array();

        $sort = [];
        $notSort = [];
        $maxDate = 0;
        foreach ($data as $row) {
            $maxDate = max($maxDate, $row['created']);
        }

        $new = [];
        $branded = [];
        $all = [];


        foreach ($data as $row) {
            if ($row['game_created'] > $maxDate || $row['type']=='new') {
                $new[] = $row;
                continue;
            }

            if ($row['branded'] == 1  || $row['type']=='brand') {
                $branded[] = $row;
                continue;
            }

            $all[] = $row;
        }



        $sortIndex = 100;
        $sort = [];
        foreach ($new as $row) {
            $sort[$row[$key]] = $sortIndex;
            $this->sorted_games[$row[$key]] = $row;
            $sortIndex += 100;
        }

        foreach ($branded as $row) {
            $sort[$row[$key]] = $sortIndex;
            $this->sorted_games[$row[$key]] = $row;
            $sortIndex += 100;
        }

        foreach ($all as $row) {
            $sort[$row[$key]] = $sortIndex;
            $this->sorted_games[$row[$key]] = $row;
            $sortIndex += 100;
        }

        return $sort;
    }
	
	public function strictGoAnotherGame() {
        return th::isB2B($this->owner) || $this->apitype=='9';
    }

	public function getWinLimit() {
        //или INFINITY?
        $maxWin = 1500000;
        if ((int) $this->max_win_eur > 0) {
            $maxWin = $this->max_win_eur;
        }

        $maxWin = $maxWin / $this->currency->val;

        return $maxWin;
    }
}
