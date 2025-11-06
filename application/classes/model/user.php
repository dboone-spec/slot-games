<?php

class Model_User extends ORM
{

//    protected $_load_with = ['office'];
    protected $_created_column = array('column' => 'created','format' => true);
    protected $_updated_column = array('column' => 'updated','format' => true);


    protected $_table_columns = [
            'id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'id',
                'column_default' => 'nextval("users_id_seq"::regclass)',
                'is_nullable' => '',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'name' =>
            [
                'type' => 'string',
                'column_name' => 'name',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'external_id' =>
            [
                'type' => 'string',
                'column_name' => 'external_id',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'password' =>
            [
                'type' => 'string',
                'column_name' => 'password',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'salt' =>
            [
                'type' => 'string',
                'column_name' => 'salt',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
			'comment' =>
            [
                'type' => 'string',
                'column_name' => 'comment',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'email' =>
            [
                'type' => 'string',
                'column_name' => 'email',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'code' =>
            [
                'type' => 'string',
                'column_name' => 'code',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'remember' =>
            [
                'type' => 'string',
                'column_name' => 'remember',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'last_login' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'last_login',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'promo_started' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'promo_started',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'promo_end_time' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'promo_end_time',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'promo_inout' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'promo_inout',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'last_bonus' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'last_bonus',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'api_name' =>
            [
                'type' => 'string',
                'column_name' => 'api_name',
                'column_default' => 'nextval("users_id_seq"::regclass)',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'api' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'api',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
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
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'avatar' =>
            [
                'type' => 'string',
                'column_name' => 'avatar',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
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
            'amount' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'amount',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '18',
                'numeric_scale' => '8',
                'datetime_precision' => '',
            ],
            'getspam' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'getspam',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'phone' =>
            [
                'type' => 'string',
                'column_name' => 'phone',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'phone_confirm' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'phone_confirm',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'phone_code' =>
            [
                'type' => 'string',
                'column_name' => 'phone_code',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'last_sms_send' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'last_sms_send',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'last_game' =>
            [
                'type' => 'string',
                'column_name' => 'last_game',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '16',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'last_play_game' =>
            [
                'type' => 'string',
                'column_name' => 'last_play_game',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '16',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'last_bonus_type' =>
            [
                'type' => 'string',
                'column_name' => 'last_bonus_type',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '16',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'last_coeff_calc' =>
            [
                'type' => 'string',
                'column_name' => 'last_coeff_calc',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '16',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'last_drop' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'last_drop',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
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
            'sum_win' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'sum_win',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'sum_amount' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'sum_amount',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'sum_in' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'sum_in',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'sum_out' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'sum_out',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'email_confirm' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'email_confirm',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'api_imperium' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'api_imperium',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'msrc' =>
            [
                'type' => 'string',
                'column_name' => 'msrc',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '30',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'dsrc' =>
            [
                'type' => 'string',
                'column_name' => 'dsrc',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '20',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'referal_link' =>
            [
                'type' => 'string',
                'column_name' => 'referal_link',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'updated' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'updated',
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
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'partner',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'project' =>
            [
                'type' => 'string',
                'column_name' => 'project',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '40',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'invited_by' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'invited_by',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'blocked' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'blocked',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'ds_notify' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'ds_notify',
                'column_default' => '1',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'blocked_text' =>
            [
                'type' => 'string',
                'column_name' => 'blocked_text',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'email_valid' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'email_valid',
                'column_default' => '1',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'office_id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'office_id',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'last_confim_email' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'last_confim_email',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'lang' =>
            [
                'type' => 'string',
                'column_name' => 'lang',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'reg_fs' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'reg_fs',
                'column_default' => '"-1"::integer',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'registr_with' =>
            [
                'type' => 'string',
                'column_name' => 'registr_with',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '6',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'autopay' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'autopay',
                'column_default' => '1',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'last_bet_time' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'last_bet_time',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'last_bonus_calc' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'last_bonus_calc',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'bind_ip' =>
            [
                'type' => 'string',
                'column_name' => 'bind_ip',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '32',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'chrome_ext_id' =>
            [
                'type' => 'string',
                'column_name' => 'chrome_ext_id',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '32',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'bets_arr' =>
            [
                'type' => 'string',
                'column_name' => 'bets_arr',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '32',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'api_key' =>
            [
                'type' => 'string',
                'column_name' => 'api_key',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '40',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],

            'api_session_id' =>
            [
                'type' => 'string',
                'column_name' => 'api_session_id',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '40',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'api_key_time' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'api_key_time',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],


            'tg_id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'tg_id',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'tg_name' =>
            [
                'type' => 'string',
                'column_name' => 'tg_name',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],

            'wcode' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'wcode',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],

            'wamount' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'wamount',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '14',
                'numeric_scale' => '2',
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

         'tg_last_game' =>
            [
                'type' => 'string',
                'column_name' => 'tg_last_game',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
        'tg_bet' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'wamount',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '12',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'ds_inout' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'ds_inout',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '12',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'ds_in_out' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'ds_in_out',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '12',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],

            'ls_wager' =>
            [
                'type' => 'float',
                'exact' => '1',
                'column_name' => 'ls_wager',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'numeric',
                'character_maximum_length' => '',
                'numeric_precision' => '12',
                'numeric_scale' => '2',
                'datetime_precision' => '',
            ],
            'ds_info' =>
            [
                'type' => 'string',
                'column_name' => 'ds_info',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
        'barcode' =>
            [
                'type' => 'string',
                'column_name' => 'barcode',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
		 'test' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'test',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],


        ];

    public function select_fields($fields)
    {
        $this->_table_columns = $fields;
        return $this;
    }

    protected $_has_one    = [
            'profile' => [
                    'model' => 'user_profile',
                    'foreign_key' => 'user_id',
            ],
    ];
    protected $_belongs_to = [
            'office' => [
                    'model' => 'office',
                    'foreign_key' => 'office_id',
            ],
    ];
    protected $_has_many   = [
            'messages' => [
                    'model' => 'user_message',
                    'foreign_key' => 'user_id',
            ],
            'pushtokens' => [
                    'model' => 'user_pushtoken',
                    'foreign_key' => 'user_id',
            ],
            'freespins' => [
                    'model' => 'freespin',
                    'foreign_key' => 'user_id',
            ],
            'fingerprint' => [
                    'model' => 'fingerprint',
                    'foreign_key' => 'user_id',
            ],
    ];

    public function filters()
    {
        return [
                'msrc' => [
                        [[$this,'check_msrc']]
                ],
        ];
    }

    public function save(\Validation $validation = NULL)
    {
        if(empty($this->last_bonus_calc)) {
            $this->last_bonus_calc = time();
        }
        return parent::save($validation);
    }

    public function check_msrc($value)
    {
        $value = trim($value);
        $value = strlen($value) > 0 ? $value : null;

        return $value;
    }

    public function labels()
    {
        $l = [
                'id' => __('ИД'),
                'name' => __('Логин'),
                'email' => __('Эл. почта'),
                'last_login' => __('Посл. вход'),
                'visible_name' => __('Видимое имя'),
                'created' => __('Дата регистрации'),
                'amount' => __('Баланс'),
                'getspam' => 'Спам?',
                'bonus' => __('Бонусов'),
                'bonusbreak' => __('Отыгрыш'),
                'bonuscurrent' => __('Отыграно'),
                'phone' => __('Телефон'),
                'phone_confirm' => __('Тел. подтвержден'),
                'sum_win' => __('Выиграно'),
                'sum_amount' => __('Ставок'),
                'sum_in' => __('Ввел'),
                'sum_out' => __('Вывел'),
                'sum_bonus' => __('Бонусов всего'),
                'email_confirm' => __('Почта подтверждена'),
                'msrc' => __('Метка'),
                'dsrc' => __('Домен'),
                'referal_link' => __('Реф. ссылка'),
                'comp_current' => __('Компоинтов'),
                'updated' => __('Обновлен'),
                'blocked' => __('Заблокирован?'),
                'blocked_text' => __('Текст указанный при блокировке'),
                'code' => __('Код для восстановления пароля'),
                'partner' => __('Партнер'),
                'last_bonus_type' => __('Тип последнего бонуса'),
                'last_bonus' => __('Сумма последнего бонуса (>0 если польз. не забрал)'),
                'autopay' => __('Автовыплата'),
                'email_valid' => 'Валидная почта?',
                'balances' => 'Балансы (Баланс/Бонусов)',
                'phone_code' => __('Код подтверждения для телефона'),
                'last_confim_email' => __('Последнее подтверждение почты'),
                'sum_diff' => __('Вин'),
                'last_bet_time' => __('Last bet time'),
                'external_id'=>__('Partner User Id'),
                'office_id'=>__('Office Id'),
        ];


        if(person::$role != 'sa')
        {
            $l['id']       = __('Логин');
            $l['balances'] = __('Баланс');
            $l['office_id'] = __('ППС');
        }

        return $l;
    }

    public function freespins_info() {
        return;
    }

    public function fs_win_sum_current() {
        return 0;
    }

    public function get_freespins() {
        return;
    }

    public function game_freespins() {
        return [];
    }

    public function use_freespins_now() {
        return;
    }

    protected $_freespins=[];

    public function getFreespins($user_id,$count_update=false,$is_active=false,$checkGame=false) {
        if(!isset($this->_freespins[$user_id])) {
            $this->_freespins[$user_id]  = new Model_Freespin();

            $this->_freespins[$user_id]->where('user_id','=',$user_id);
            if($is_active) {
                $this->_freespins[$user_id]->where('active','=','1');
            }
            if($checkGame) {
                $this->_freespins[$user_id]->where('game_id','=',$checkGame);
            }
            $this->_freespins[$user_id]->where('expirtime','>=',time());

            $this->_freespins[$user_id]->and_where_open();
            $this->_freespins[$user_id]->where('starttime','<=',time());
            $this->_freespins[$user_id]->or_where('starttime','is',null);
            $this->_freespins[$user_id]->and_where_close();

            $this->_freespins[$user_id]->order_by(DB::expr('src desc, created asc'));
            $this->_freespins[$user_id]->find();

            if($this->_freespins[$user_id]->loaded() && $count_update) {
                $this->_freespins[$user_id]->updated=$this->_freespins[$user_id]->updated+1;
                $this->_freespins[$user_id]->save();
            }
        }

        return $this->_freespins[$user_id];
    }

    public function parent_acc()
    {
        if($this->parent_id)
        {
            return new Model_User($this->parent_id);
        }
        return $this;
    }

    public function drop_limit($type = null)
    {
        return 0;
        $sql_bets = <<<SQL
            Select coalesce(sum(amount),0) as amount, coalesce(sum(win),0) as win
            From bets
            Where user_id = :user_id
                AND created >= (
                    Select payed
                    From payments
                    Where user_id = :user_id
                        AND status = 30
                        AND amount>0
                    Order by payed desc
                    LIMIT 1
                )
SQL;

        $res_bets = db::query(1,$sql_bets)->param(':user_id',$this->id)->execute()->as_array();

        //возвращает сумму, которой не хватает до 0% комиссии
        $a = [
                'amount' => $this->last_drop * ((float) Kohana::$config->load('static.dropk')) * $this->office->currency_coeff,
                'win' => $this->last_drop * ((float) Kohana::$config->load('static.dropk')) * $this->office->currency_coeff,
        ];

        foreach($res_bets as $value)
        {
            foreach($a as $k => $v)
            {
                $a[$k] -= $value[$k];
            }
        }

        if($type)
        {
            return Arr::get($a,$type);
        }

        return $a;
    }

    //Использовать для вывода общей суммы на счету.
    public function amount()
    {
        $amount = bcdiv($this->amount,1,$this->office->currency->mult ?? 2);
        return $amount;
    }

    public function generate_email_code()
    {
        $this->code              = md5($this->name . $this->email);
        $this->last_confim_email = time();
        $this->save();

        return $this->code;
    }

    /*
     * проверяем количество пользователей, которые
     * зарегистрировались по рефер. ссылке текущего
     * пользователя с одинаковым IP
     *
     * не более 5 пользователей 1-го уровня
     * не более 15 пользователей 2-го уровня
     */

    public function check_referals_ip()
    {
        //ip рефералов 1-го уровня
        $sql_ref_first = <<<SQL
            Select ip, count(ip)
            From bonus_referals
            Where user_id = :user_id AND ip = :ip
            GROUP BY ip
SQL;
        $params        = [
                ':user_id' => $this->id,
                ':ip' => $_SERVER['REMOTE_ADDR']
        ];
        $res           = db::query(database::SELECT,$sql_ref_first)->parameters($params)->execute()->as_array();

        if($res AND $res[0]['count'] >= 5)
        {
            return false;
        }

        return true;
    }

    public function check_referals()
    {
        $sql_referals = <<<SQL
            Select count(user_id)
            From bonus_referals
            Where user_id = :user_id
SQL;
        $params       = [
                ':user_id' => $this->id,
        ];
        $result       = db::query(database::SELECT,$sql_referals)->parameters($params)->execute()->as_array();
        if($result[0]['count'] == 0)
        {
            return false;
        }

        return true;
    }

    public function make_ref_link()
    {
        if(!$this->referal_link)
        {
            $this->referal_link = md5($this->id . $this->email);
            $this->save();
        }
        return URL::base(true) . "?referallink={$this->referal_link}";
    }

    public function get_domain()
    {
        $domain = Dd::get_domain('default');

        if(!is_null($this->dsrc) AND ! in_array($this->dsrc,[]))
        {
            $domain = str_replace('com','.com',$this->dsrc);
        }

        $type   = dd::get_theme($domain);
        $domain = Dd::get_domain($type);

        return $domain;
    }

    protected $_can_play=[];

    public function canPlay($game = null)
    {
        return true;
        if(!is_null($game) && isset($this->_can_play['allcanplay'])) {
            return $this->_can_play['allcanplay'];
        }

        if($game)
        {
            if(isset($this->_can_play[$game])) {
                return $this->_can_play[$game];
            }

            if($this->last_drop <= 0)
            {
                $this->_can_play[$game] = true;
                return true;
            }
            elseif($this->amount() > 0) {
                $this->_can_play[$game] = true;
                return true;
            }
        }
        return false;
    }

    public function check_profile()
    {
        $profile_required = true;
        $columns          = ['first_name','last_name','middle_name','birthday','gender'];



        foreach($columns as $col)
        {
            if(is_null($this->profile->$col))
            {
                $profile_required = false;
            }
        }

        if($this->phone_confirm == 0 OR $this->email_confirm == 0 OR ! $profile_required)
        {
            return false;
        }

        return true;
    }


    /*
     * возвращает количество новых сообщений
     * без учёта push-сообщений
     */

    public function count_new_messages()
    {
        $sql = <<<SQL
            Select *
            From user_messages
            Where user_id = :user_id
                AND push = :push
                AND time_read is null
                AND (
                    time_end is null
                    OR
                    time_end >= :curzone_time
                )
SQL;
        $res = db::query(1,$sql)
                ->parameters([
                        ":user_id" => $this->parent_id,
                        ":push" => 0,
                        ":curzone_time" => time(),
                ])
                ->execute();

        return count($res);
    }

    /*
     * получаем сообщения для пользователя
     * без учета тех которые он "удалил"
     */

    public function messages()
    {
        $messages = [];

        $sql     = <<<SQL
                Select id, title, text, time_read, created
                From user_messages
                Where
                    user_id = :user_id
                    AND show = :show
                    AND push = :push
                ORDER BY created desc
SQL;
        $res_sql = db::query(1,$sql)
                ->parameters([
                        ":user_id" => $this->parent_id,
                        ":show" => 1,
                        ":push" => 0,
                ])
                ->execute();

        foreach($res_sql as $mess)
        {
            $messages[$mess['id']] = [
                    "text" => $mess['text'],
                    "title" => $mess['title'],
                    "time_read" => $mess['time_read'],
                    "created" => date('d.m.Y',$mess['created']),
            ];
        }

        return $messages;
    }

    /*
     * создаем новое сообщение для пользователя, в качестве
     * параметров могут быть все поля модели Model_User_Message
     */

    public function new_message($params)
    {
        $mess_row     = new Model_User_Message();
        $message_keys = array_keys($mess_row->list_columns());

        $message = array_merge($mess_row->params_message,$params);

        foreach($message as $k => $v)
        {
            if(in_array($k,$message_keys))
            {
                $mess_row->$k = $v;
            }
        }

        $mess_row->save();
    }

    public function currency()
    {
        $text = $this->office->currency->code;

        if($text != 'RUB')
        {
            $text .= ' coins';
        }

        return $text;
    }

    public function check_fingerprint($user_id = null)
    {
        $flag = false;

        if(!$user_id)
        {
            $user_id = $this->id;
        }

        $sql = <<<SQL
            Select l.user_id
            From (
                Select l.fingerprint, u.parent_id
                From logins l JOIN users u ON l.user_id=u.parent_id
                Where fingerprint is not null
                    AND u.id = :user_id
                GROUP BY l.fingerprint, u.parent_id
            ) as t JOIN logins l ON t.fingerprint=l.fingerprint
            Where t.parent_id<>l.user_id
                AND t.fingerprint is not null
                AND t.fingerprint >0
SQL;

        $count_logins = db::query(1,$sql)->param(':user_id',$user_id)->execute()->count();

        if($count_logins)
        {
            $flag = true;
        }

        return $flag;
    }

    public function check_reg_fs()
    {
        if($this->office_id == 888)
        {
            return true;
        }
        if($this->check_fingerprint())
        {
            return false;
        }

        $flag  = true;
        $games = kohana::$config->load('static.reg_fs_games');

        foreach($games as $g_name)
        {
            $code = new Model_Bonus_Code([
                    'name' => 'fs_reg_' . $g_name,
                    //для разделения фриспинов при регистрации по офисам
                    'office_id' => $this->office_id
            ]);

            $res = $this->can_use_code($code->id);

            if(!isset($res['error']) OR $res['error'])
            {
                $flag = false;
            }
        }

        return $flag;
    }

    /*
     * бонусы минус сумма бонусов фриспинов
     */

    public function bonus()
    {
        return $this->bonus - $this->sum_all_freespins();
    }

    public function short_email()
    {
        if(Valid::email($this->name))
        {
            $dog_pos = strpos($this->name,'@');

            return substr($this->name,0,$dog_pos);
        }

        return $this->name;
    }

    public function sum_out_last_day()
    {
        $sql = <<<SQL
            Select coalesce(abs(sum(amount)),0) as sum
            From payments
            Where amount<0
                and status=30
                and user_id=:u_id
                and payed>=:time
SQL;
        return key(db::query(1,$sql)->param(':u_id',$this->id)->param(':time',time() - 24 * 60 * 60)->execute()->as_array('sum')) < 30000;
    }

    public function sum_inout_last_30()
    {
        $sql = <<<SQL
            Select coalesce(sum(amount),0) as sum
            From payments
            Where status=30
                and user_id=:u_id
                and payed>=:time
SQL;
        return key(db::query(1,$sql)->param(':u_id',$this->id)->param(':time',time() - 30 * 24 * 60 * 60)->execute()->as_array('sum')) > 0;
    }

    public function dayly_bonuses()
    {
        /*
         * уже начислили за сегодня
         */
        if($this->last_dayly_accrual == mktime(0,0,0))
        {
            return true;
        }

        $diff_days   = (mktime(0,0,0) - $this->last_dayly_accrual) / (24 * 60 * 60);
        $current_day = $this->active_day + 1;

        if($diff_days > 1)
        {
            $step_back   = $this->active_day - $diff_days * 2;
            $current_day = $step_back > 0 ? $step_back : 1;

            $sql_fs     = <<<SQL
                Select coalesce(sum(bonus),0) as sum
                From dayly_bonuses
                Where type = :type
                    AND day < :day
SQL;
            $current_fs = key(db::query(1,$sql_fs)
                    ->param(':day',$current_day)
                    ->param(':type',auth::user()->parent_acc()->dayly_bonus_type)
                    ->execute()->as_array('sum'));


            $history          = new Model_Daylyhistory();
            $history->user_id = $this->id;
            $history->type    = 'reset_fs';
            $history->bonus   = $current_fs - $this->dayly_freespins;
            $history->save();

            $this->dayly_freespins = $current_fs;
        }

        $daylybonuses = orm::factory('daylybonus')
                ->where('day','=',$current_day)
                ->where('type','=',auth::user()->parent_acc()->dayly_bonus_type)
                ->find_all();

        foreach($daylybonuses as $b)
        {
            switch($b->type)
            {
                case 'freespins' OR 'freespins2':
                    $this->dayly_freespins += $b->bonus;

                    $history          = new Model_Daylyhistory();
                    $history->user_id = $this->id;
                    $history->type    = 'add_fs';
                    $history->bonus   = $b->bonus;
                    $history->save();

                    break;
                case 'cashback':
                    $this->cashback       += $b->bonus;
                    $this->reset_cashback = mktime(0,0,0) + 5 * 24 * 60 * 60;

                    $history          = new Model_Daylyhistory();
                    $history->user_id = $this->id;
                    $history->type    = 'add_cashback';
                    $history->bonus   = $b->bonus;
                    $history->save();

                    break;
            }
        }

        if($this->reset_cashback AND $this->reset_cashback <= mktime(0,0,0))
        {
            $cashback = kohana::$config->load('static.cashback');

            $history          = new Model_Daylyhistory();
            $history->user_id = $this->id;
            $history->type    = 'reset_cashback';
            $history->bonus   = $cashback - $this->cashback;
            $history->save();

            $this->cashback = $cashback;
        }

        $this->active_day         = $current_day;
        $this->last_dayly_accrual = mktime(0,0,0);

        $this->save();

        return true;
    }

    protected $_fsback_coef_calc_period = Date::DAY;

    public function calc_next_coeff() {

        $can_calc=true;

        $curr_day = mktime(0,0,0);

        if(empty($this->last_coeff_calc)) {
            $this->bonus_coeff = $this->_fs_min;
			$this->ds_in_out=0;
            $this->last_coeff_calc=$curr_day;
            $this->save();

            $this->fshistory([
                    'user_id'=>$this->id,
                    'office_id'=>$this->office_id,
                    'coef'=>$this->bonus_coeff,
                    'change'=>$this->bonus_coeff,
            ]);
        }

        $before = $this->bonus_coeff;

        if($this->last_coeff_calc+$this->_fsback_coef_calc_period<=$curr_day) {

            if(!empty($this->last_bet_time)) {
                $less = (floor((time()-$this->last_bet_time)/60/60/24))*$this->_fs_less;
                if($less<=0) {
                    $less=0;
                    $this->bonus_coeff += $this->_fs_more;
                }
                else {
                    $this->bonus_coeff-= $less;
                }
            }

            if(!empty($this->last_bet_time) && $this->last_bet_time+$this->_fsback_coef_calc_period*6<=time()) {
                $this->bonus_coeff = $this->_fs_min;
                $can_calc=false;
            }


            if($this->bonus_coeff>$this->_fs_max) {
                $this->bonus_coeff=$this->_fs_max;
            }


            if($this->bonus_coeff<$this->_fs_min) {
                $this->bonus_coeff=$this->_fs_min;
            }

			//если уже прошло много времени, то обнуляем ему накопленное
            if(!$can_calc) {
                $this->ds_in_out=0;
            }

            $this->last_coeff_calc=$curr_day;
            $this->save();
        }

        if($before!=$this->bonus_coeff) {
            $this->fshistory([
                    'user_id'=>$this->id,
                    'office_id'=>$this->office_id,
                    'coef'=>$this->bonus_coeff,
                    'change'=>$this->bonus_coeff-$before,
            ]);
        }

        return $can_calc;
    }

    protected $_fs_min = 0.045;
    protected $_fs_max = 0.1;
    protected $_fs_less = 0.01;
    protected $_fs_more = 0.005;

    public function getBetsForDS($need_update=false) {

        if($need_update) {
            $gamestrict = ['jp']+th::getMoonGames();

            $gamestrict = array_merge($gamestrict,array_keys((array) Kohana::$config->load('videopoker')));
			
			$gamestrict = array_merge($gamestrict,th::$_strict_for_FSback);

            $last_moon_round=db::query(1,'select id from moon_results order by 1 desc limit 1')->execute()->as_array();

			$last_calc=$this->last_bonus_calc;

            if($this->office->enable_bia>$last_calc) {
                $last_calc=$this->office->enable_bia;
            }

			if($last_calc<time()-Date::DAY*10) {
                $days10=strtotime('-10 days');
                $last_calc=mktime(0,0,0,date('m',$days10),date('d',$days10),date('Y',$days10));
            }

            db::query(database::UPDATE,"UPDATE users 
                SET ds_inout = s.SUM - (case when promo_inout>0 then promo_inout else 0 end)
                FROM
                    (
                    SELECT SUM
                        ( b.amount - b.win ),b.user_id
                    FROM
                        bets b
                    WHERE
                        b.user_id=:user_id
                        and b.created >= :last_bonus_calc
                        AND b.is_freespin = 0 
                        AND ( ( b.game NOT IN :gamestrict ) OR ( b.game IN :moongames AND b.come != :moon_round ) ) 
                    GROUP BY
                        b.user_id 
                    ) 
                AS s where id=:user_id;")
                ->parameters([
                    ':gamestrict'=>$gamestrict,
                    ':user_id'=>$this->id,
                    ':moongames'=>th::getMoonGames(),
                    ':last_bonus_calc'=>$last_calc,
                    ':moon_round'=>$last_moon_round[0]['id'],
                ])
                ->execute();
            $this->reload();

            logfile::create(date('Y-m-d H:i:s') .
                ' [update ds_inout '.$this->id.'][last_bonus_calc: '.$this->last_bonus_calc.' ('.date('Y-m-d H:i:s',$this->last_bonus_calc).')]'.
                ' ds_inout: '.$this->ds_inout.'; ds_in_out: '.$this->ds_in_out.PHP_EOL, 'dsinout');
        }

        return [
            'sum'=>$this->ds_inout,
            'ds_info'=>json_decode($this->ds_info,1),
        ];
    }

    public function pay_fsback($started=false,$game=false,$game_id=false) {
        $currTime = time();

        if(!$this->loaded()) {
            return;
        }

        if($this->office->enable_bia<=0) {
            return;
        }

        if($this->blocked) {
            return;
        }

        if(!empty($this->last_game) && !$game && !$game_id) {
            return;
        }
		
		if(th::isBackupRunning()) {
			Kohana::$log->add(Log::INFO,'backup is running: '.$this->id);
            return;
        }

        if(!Kohana::$is_windows) {
            $la=sys_getloadavg();
            $la=$la[0];

            if($la>40) {
                Kohana::$log->add(Log::INFO,'too much load: '.$this->id);
                return;
            }
        }

        if(empty($this->last_bonus_calc)) {
            $this->last_bonus_calc=$this->last_bet_time??$currTime;
            $this->save();
        }

        $can_calc = $this->calc_next_coeff();

        if(!$can_calc) {
            return;
        }

        if(is_null($this->last_bet_time)) {
            return;
        }

        $max_time = (int) $this->last_bet_time;

        $min_time = $this->last_bonus_calc;

        if($this->office->enable_bia>$max_time) {
            $max_time=$this->office->enable_bia;
        }

        if($this->office->enable_bia>$min_time) {
            $min_time=$this->office->enable_bia;
        }

        if($max_time+$this->office->bonus_diff_last_bet*60*60>$currTime) {
            return;
        }

        if($this->last_bet_time<$this->last_bonus_calc) {
            return;
        }

        $bets=$this->getBetsForDS(true);

	    //если были только в покер
        if(empty($bets['ds_info'])) {
            $this->last_bonus_calc=$currTime;
            $this->ds_in_out=0;
            $this->save();
            return;
        }

        $sum_all = $bets['sum'];

        logfile::create(date('Y-m-d H:i:s').' CALC ['.$this->id.'] bets: '.
                print_r($bets,1).'; sum_all: '.$sum_all.
                '; bonus_coeff: '.$this->bonus_coeff.
                '; last_bet_time: '.$this->last_bet_time.
                '; min_time: '.$min_time.
                '; to: '.time().
                '; last_bonus_calc: '.$this->last_bonus_calc,'calcfs');

        $sum_fs = $sum_all*$this->bonus_coeff;

        $bets_filter = array_values(array_filter($bets['ds_info'],function($v) {
            return !th::cantFSback($v['game']);
        }));

        if(!count($bets_filter)) {
            $bets_filter=th::getRandomGameId($this->office_id);
        }

        if(!$game) {
            $game=$bets_filter[0]['game'];
        }

        if(!$game_id) {
            $game_id=$bets_filter[0]['game_id'];
        }

        $res = false;

        $fs_id = '-1';

        $mult = $this->office->currency->mult;


        if($sum_fs>=10/pow(10,$mult)) {

            $res = $this->calc_fsback($sum_fs,$game,$game_id,true);

            if($res) {
                $ex = explode('-',$res['near']);

                $lines = (int) $ex[0];
                $dentab = (float) $ex[1];

                $z = floor($res['win']/$res['zzz']);

                Database::instance()->begin();
                try {
                    $f = new Model_Freespin();
                    $fs_id = $f->giveFreespins($this->id,$this->office_id,$res['game_id'],$z,$res['zzz'],$lines,$dentab,'cashback',true,[$sum_all,$this->bonus_coeff,$bets_filter[0]??[],$this->last_bonus_calc,$max_time],$started,null,time()+12*60*60);

                    Database::instance()->commit();
                }
                catch(Database_Exception $ex) {
                    Database::instance()->rollback();
                    throw $ex;
                }
            }
        }

        logfile::create(date('Y-m-d H:i:s').' CALC PROCESSED ['.$this->id.'] FSID: '.$fs_id,'calcfs');

        $this->ds_in_out=0;
        $this->ds_inout=0;
        $this->ds_info=null;
        $this->last_bonus_calc=$currTime;
        $this->save();

        logfile::create(date('Y-m-d H:i:s') . ' [clear ds_inout '.$this->id.']'.PHP_EOL, 'dsinout');

        return !!$res;

    }

    public function calc_fsback($win, $fgame, $fgame_id,$no_extra_search=false, $max_lines=false, $no_limit=false,$force_line_amount=false) {

        $c = kohana::$config->load('agt/'.$fgame);

        $max_spins=30;

        $moon_min_bet_limit=0.1;
        $moon_max_bet_limit=100;

        if(th::isMoonGame($fgame) && $this->office_id) {
            $max_spins=5;
            $curr=$this->office->currency;
            $moon_min_bet_limit=$curr->moon_min_bet ?? $moon_min_bet_limit;
            $moon_max_bet_limit=$curr->moon_max_bet ?? $moon_max_bet_limit;
        }

        $zzz = [];

        if($max_lines) {
            $c['lines_choose']=[$c['lines_choose'][0]];
        }

        foreach($c['lines_choose'] as $li=>$l) {
            if(isset($c['staticlines']) && !empty($c['staticlines']) && $l!=$c['staticlines'][0]) {
                continue;
            }

            foreach($this->office->get_k_list() as $ki=>$k) {
                foreach($c['bets'] as $bi=>$b) {
                    if(th::isMoonGame($fgame) && ((($k*$b)<$moon_min_bet_limit) || (($k*$b)>$moon_max_bet_limit))) {
                        continue;
                    }



                    $zzz[$l.'-'.$ki.'-'.$bi]=$k*$b;
                    if(!isset($c['staticlines']) || empty($c['staticlines'])) {
                        $zzz[$l.'-'.$ki.'-'.$bi]*=$l;
                    }
                }
            }
        }

        $cc=0;
        $i=-1;

        $min_mod = 9999;

        $results = [];


        if($no_limit) {
            $max_spins=150;
        }


        $max_bet=max($zzz);

        if($max_bet*$max_spins<$win) {
            $max_spins=round($win/$max_bet);
        }

        foreach($zzz as $k=>$aa) {
            $z = floor($win/$aa);

            $new_mod = $win/$aa-floor($win/$aa);

            if($z>=1 && $z<=$max_spins && ($new_mod==0  || ($min_mod>$new_mod))) {
                $cc=$z;
                $i=$k;

                $results[$i]=$cc;

                $min_mod=$new_mod;

                if($new_mod==0) {
                    break;
                }
            }
        }


        if(isset($c['staticlines']) && $i==-1 && $this->office_id && !$no_extra_search) {

            $glob = glob(APPPATH.'config'.DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR.'*');
            foreach($glob as $cnf) {

                $sql="select g.name, g.id as game_id
                        from games g
                        join office_games og on og.game_id = g.id
                        where og.office_id = :o_id
                            and og.enable = 1
                            and g.show=1
                            and g.provider='our'
                            and g.type='slot'
                            order by g.sort";

                $result = db::query(1,$sql)
                        ->param(':o_id',$this->office_id)
                        ->execute()
                        ->as_array();

                $found=false;

                $gname=null;
                $gname_id=null;

                $break=0;

                while(!$found) {

                    $break++;

                    if($break>=50) {
                        $found=true;
                        //todo log error!!
                    }

                    $gi = math::array_rand(array_keys($result));
                    $gname = $result[$gi]['name'];
                    $gname_id = $result[$gi]['game_id'];

                    $cnf = Kohana::$config->load('agt.'.$gname);

                    if(!isset($cnf['staticlines'])) {
                        $found=true;
                    }
                }

                return $this->calc_fsback($win,$gname,$gname_id);
            }
        }

        if($i==-1) {
            return false;
        }


        arsort($results);

        $i=key($results);
        $cc=$results[$i];

      return ['near'=>$i,'zzz'=>$zzz[$i],'win'=>$win,'game'=>$fgame, 'game_id'=>$fgame_id,'totalbet'=>$aa,'cnt'=>$cc];

    }

    public function pay_biafs($force_game='') {

        $currTime = time();

        if(!$this->loaded()) {
            return;
        }

        if($this->office->enable_bia<=0) {
            return;
        }

        if($this->blocked) {
            return;
        }

        if(!empty($this->last_game)) {
            return;
        }

        if(empty($this->last_bonus_calc)) {
            $this->last_bonus_calc=$this->last_bet_time??$currTime;
            $this->save();
        }

        $this->calc_next_coeff();

        if(is_null($this->last_bet_time)) {
            return;
        }

        if($this->last_bet_time+6*$this->_fsback_coef_calc_period<$currTime && $this->last_bonus_calc+$this->_fsback_coef_calc_period<$currTime) {

            $change = $this->bonus_coeff-0.05;

            $this->bonus_coeff = 0.05;
            $this->last_bonus_calc=$currTime;
            $this->save();

            $this->fshistory([
                    'user_id'=>$this->id,
                    'office_id'=>$this->office_id,
                    'coef'=>0.05,
                    'change'=>-$change,
            ]);

            return;
        }


        $max_time = (int) $this->last_bet_time;


        if($this->office->enable_bia>$max_time) {
            $max_time=$this->office->enable_bia;
        }

        if($max_time+$this->office->bonus_diff_last_bet*60*60>$currTime) {
            return;
        }


        if($this->office->bonus_coeff<0.01) {
            return;
        }

        if(empty($this->bonus_coeff)) {
            $this->bonus_coeff = $this->office->bonus_coeff;
        }

        if($this->bonus_coeff<0.01) {
            return;
        }

        if($this->last_bet_time<$this->last_bonus_calc) {


//            if($this->last_bonus_calc+$this->office->bonus_diff_last_bet*60*60>$currTime) {
//
//                $this->bonus_coeff = $this->bonus_coeff-0.01;
//
//                if($this->bonus_coeff<=0.05) {
//                    $this->bonus_coeff = 0.05;
//                }
//
//                $this->last_bonus_calc=$currTime;
//                $this->save();
//            }

            return;
        }


        $bets = db::query(1,'select sum(amount-win),sum(amount) as in,sum(win) as out,count(amount) as cnt,avg(amount),'
                . 'game,game_id from bets '
                . 'where is_freespin=0 and user_id=:u_id and created>=:from and created<=:to group by game,game_id')
                ->parameters([
                        ':u_id'=>$this->id,
                        ':from'=>$this->last_bonus_calc,
                        ':to'=>$max_time,
                ])
                ->execute()
                ->as_array();

        $sum_all = 0;

        foreach($bets as $b) {
            $sum_all+=$b['sum'];
        }

        if($sum_all>0) {
            usort($bets, function ($item1, $item2) {
                return $item2['cnt'] <=> $item1['cnt'];
            });

            $win = floor($sum_all*$this->bonus_coeff);
            $bet = $bets[0]['avg'];

            if($win>=0.1) {
                $z = floor($win/$bet);

                while($z<1) {
                    $bet = $bet/10;
                    $z = floor($win/$bet);
                }

                if($z>=1) {

                    if($z<5) {
                        $bet = $bet/5;
                        $z = floor($win/$bet);
                    }
                    if($z>50) {
                        $bet = $bet*50;
                        $z = floor($win/$bet);
                    }

                    $c = kohana::$config->load('agt/'.$bets[0]['game']);
                    $ac = $this->office->get_k_list();

                    $zzz = [];

                    foreach($c['lines_choose'] as $li=>$l) {
                        if($li>0) { //max lines
                            continue;
                        }
                        foreach($ac as $ki=>$k) {
                            foreach($c['bets'] as $b) {
                                $zzz[$l.'-'.$k.'-'.$b]=$l*$k*$b;
                            }
                        }
                    }

                    $near = $this->searchNearest($bet,$zzz);

                    $ex = explode('-',$near);

                    $lines = $ex[0];
                    $dentab = $ex[1];
                    $bet = $ex[2];

                    $z = floor($win/$zzz[$near]);

                    $f = new Model_Freespin();
                    $f->giveFreespins($this->id,$this->office_id,$bets[0]['game_id'],$z,$zzz[$near],$lines,$dentab,'cashback',true,[$sum_all,$this->bonus_coeff,$bets[0]]);

                }
            }
        }

        $this->bonus_coeff = $this->next_bonus_coeff;

        $this->last_bonus_calc=$currTime;
        $this->save();
    }

    public function fshistory($params=[]) {
        $fsh = new Model_Fsbackhistory();

        foreach($params as $k=>$v) {
            $fsh->$k=$v;
        }
        $fsh->save();
    }
    public function pay_bia($started=false,$game=false,$game_id=false) {

        if(PROJECT==1) {
            return $this->pay_fsback($started,$game,$game_id);
            return $this->pay_biafs();
        }

        if(!$this->loaded()) {
            return;
        }

        if($this->office->enable_bia<=0) {
            return;
        }

        if($this->blocked) {
            return;
        }

        if(!is_null($this->last_game)) {
            return;
        }

        if(in_array($this->office_id,[777])) {
            return;
        }

        if(is_null($this->last_bet_time)) {
            return;
        }

        $max_time = time()-($this->office->bonus_pay_period*24*60*60);

        if($this->office->enable_bia>$max_time) {
            $max_time=$this->office->enable_bia;
        }

        if($max_time<=$this->office->bonus_diff_last_bet*60*60) {
            return;
        }

        if($this->office->bonus_coeff<0.01) {
            return;
        }

        db::query(Database::UPDATE,'update bonuses set payed=2 where created<=:time')
            ->param(':time',$max_time)
            ->execute();

        //берем последнее время: начисление бонуса или последний вывод. либо с момента включения биа

//        $sql_last_time = <<<SQL
//            Select coalesce(max(b.created), 0) as time_from
//            From bonuses b
//            Where b.user_id = :user_id
//                AND type = 'activity'
//            UNION
//            Select coalesce(max(created), 0)
//            From operations o
//            Where o.updated_id = :user_id AND o.amount < 0
//SQL;
//
//        $result_time = db::query(database::SELECT, $sql_last_time)
//                ->param(':user_id', $this->id)
//                ->execute()
//                ->as_array();
//
//        $max_time = max($result_time)['time_from'];

        //сумма вводов и выводов

        $sql_amount = <<<SQL
                Select coalesce(sum(amount), 0) as sum
                From operations
                Where updated_id = :user_id and created > :time
SQL;

        $params_amount = [
            ':user_id' => $this->id,
            ':time' => $max_time,
        ];
        $amount_res = db::query(database::SELECT, $sql_amount)->parameters($params_amount)->execute()->as_array();
        $amount = $amount_res[0]['sum']??0;

        $sql_bonus = <<<SQL
                Select coalesce(sum(bonus), 0) as sum
                From bonuses
                Where user_id = :user_id and created > :time
SQL;

        $allbonus_res = db::query(database::SELECT, $sql_bonus)->parameters($params_amount)->execute()->as_array();
        $allbonus = $allbonus_res[0]['sum']??0;

        //расчет бонуса!
        $bonus = $this->office->bonus_coeff * ($amount-$this->amount())-$allbonus;

        $data_bonus = [
            'user_id' => $this->id,
            'referal_id' => 0,
            'bonus' => $bonus,
            'created' => time(),
            'type' => 'activity',
            'payed' => 1,
            'balance' => $this->amount()+$bonus,
            'log' => json_encode([
                'sum_in_out' => $amount,
                'allbonus' => $allbonus,
                'balance_with_block' => $this->amount(),
                'coeff' => $this->office->bonus_coeff,
            ]),
        ];

        database::instance()->begin();
        try {
            $sql_insert = <<<SQL
                Insert into bonuses("user_id", "bonus", "created", "type", "referal_id", "payed", "log", "last_notification", "balance")
                VALUES (:user_id, :bonus, :created, :type, :referal_id, :payed, :log, :last_notification, :balance)
SQL;
            $params_insert = [
                ':user_id' => $data_bonus['user_id'],
                ':referal_id' => $data_bonus['referal_id'],
                ':bonus' => $data_bonus['bonus'],
                ':balance' => $data_bonus['balance'],
                ':created' => time(),
                ':type' => $data_bonus['type'],
                ':payed' => $data_bonus['payed'],
                ':log' => $data_bonus['log'],
                ':last_notification' => time(),
            ];
            db::query(database::INSERT, $sql_insert)->parameters($params_insert)->execute();

            if($bonus>0) {
                db::query(database::UPDATE,'update users set amount=amount+:bonus, last_bonus=:bonus,last_bonus_type=:last_bonus_type where id=:user_id')
                    ->param(':bonus',$data_bonus['bonus'])
                    ->param(':user_id',$this->id)
                    ->param(':last_bonus_type',$data_bonus['type'])
                    ->execute();
            }

            database::instance()->commit();

            return $bonus;
        } catch (Exception $e) {
            database::instance()->rollback();
            kohana::$log->writeException($e);
            return;
        }
    }


    /**
     * Начисляет фриспины игроку согласно акции
     * @param   Model_Game   $game   модель игры
     * @param   Model_Event   $event   событие
     * @return  void
     */
    public function joinEvent($game,$event) {

        if($event->type=='promo') {

            if($this->promo_started===0) {
                return false;
            }
            elseif($this->promo_started===null || (!$this->promo_started!==null && date('dmY',$this->promo_started)!=date('dmY'))) {
                $this->promo_started=time();
                $this->promo_end_time=$event->startTime()+$event->duration;
                $this->save()->reload();

                logfile::create(date('Y-m-d H:i:s').' user '.$this->id.' joined promo ['.$event->id.']: promo_started: '.$this->promo_started,'promocalc');
            }
            else {
                //logfile::create(date('Y-m-d H:i:s').' user '.$this->id.' cant! joined promo ['.$event->id.']: promo_started: '.$this->promo_started,'promocalc');
            }

            return $event;
        }

        $lines=kohana::$config->load('agt/'.$game->name)['lines_choose'][0];
        $dentab_index = 0;

        $f = new Model_Freespin();
        $f->event_id=$event->id;
        $f->event_end_time=$event->startTime()+$event->duration;

        $fs_count=$event->fs_count;

        if($event->type=='progressive') {
            $fs_count=$event->getProgressiveFScount($this->id);
        }

        database::instance()->begin();

        try {

            $nextSunday=strtotime('next sunday');
            $expirtime=mktime(23,59,59,date('m',$nextSunday),date('d',$nextSunday),date('Y',$nextSunday));

            if(($expirtime-time())>Date::WEEK) {
                //сегодня воскресенье
                $expirtime=mktime(23,59,59);
            }

            $f->giveFreespins($this->id,$this->office_id,$game->id,$fs_count,$event->fs_amount,$lines,$dentab_index,'lucky', true, [],false, null, $expirtime);

            db::query(database::UPDATE,'update users set ls_wager=0 where id=:id')->param(':id',$this->id)->execute();

            database::instance()->commit();

        }
        catch(Exception $e) {
            database::instance()->rollback();
            throw $e;
        }

        logfile::create(date('Y-m-d H:i:s') . ' [ls wager clear '.$this->id.'] '.PHP_EOL, 'lswager');

    }

    /**
     * выплачиваем за турнир
     * @param Model_Game $game
     * @return void
     */
    public function payForEvent(Model_Game $game, Model_Event $event) {
        $bet=[];

        $bet['amount']=0;
        $bet['come']='';
        $bet['result']='';
        $bet['win']=$this->promo_inout;
        $bet['game_id']=$game->id;
        $bet['can_jp']=false;
        $bet['game_name']=$game->name;
        $bet['method']='prize';
        $bet['promo_prize']=true;
		
		$bet['event_id']=$event->id;

        bet::make($bet,'prize',null,true);
    }

    /**
     * Проверяет, есть ли подходящие на текущий момент акции по игре
     * @param   Model_Game   $game   модель игры
     * @return  boolean | Model_Event
     */
    public function checkEvents($game,$checkPay=false) {
        $events = db::query(1,"select e.* from events e
                            where 
                            e.office_id=:o_id
                            and starts<=extract(epoch from now() at time zone 'UTC')::int and 
                            ends>=extract(epoch from now() at time zone 'UTC')::int 
                            and active=1
                            order by e.type='progressive' desc,e.type='dayweek' desc, e.type='promo' desc")
							->param(':o_id',auth::user()->office_id)
            ->execute(null,'Model_Event')->as_array();

        foreach($events as $event) {
            if($checkPay && $event->canPay($game,$this)) {
                return $event;
            }
            if($event->canJoin($game,$this)) {
                return $event;
            }
        }

        return false;
    }

    /**
     * Отдает будущие и текущие события
     * @param   int   $office_id
     * @param   array   $types
     * @return  Model_event[]|boolean
     */
    public function futureAndNowEvents($office_id,$types=[]) {

        $sql = "select * from events 
                            where starts<=extract(epoch from now() at time zone 'UTC')::int and 
                            ends>=extract(epoch from now() at time zone 'UTC')::int
                            and active=1
                            and office_id=:o_id";

        if(!empty($types)) {
            $sql.=' and type in :types';
        }

        $events = db::query(1,$sql)
            ->param(':o_id',$office_id)
            ->param(':types',$types)
            ->execute(null,'Model_Event')->as_array();

        return $events;
    }

    public function promopanel_enable() {
        return $this->office->promopanel;
        return in_array($this->office_id,[444,777,999,1030,1032,1219,1261,1355,1047]);
    }

    public function searchNearest($value, $inArray) {
        $lastKey = null;
        $lastDif = null;
        foreach ($inArray as $k => $v) {
            if ($v == $value) {
                return $k;
            }
            $dif = abs ($value - $v);
            if (is_null($lastKey) || $dif < $lastDif) {
                $lastKey = $k;
                $lastDif = $dif;
            }
        }

        return $lastKey;
    }
}
