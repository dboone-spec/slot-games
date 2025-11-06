<?php

class Model_TelegramBot extends ORM {

	protected $_table_name = 'telegram_phones';

	protected $_table_columns = array(
		"id" => array(
			"type" => "int",
			"min" => "-2147483648",
			"max" => "2147483647",
			"column_name" => "id",
			"column_default" => "nextval('telegram_phones_id_seq'::regclass)",
			"is_nullable" => FALSE,
			"data_type" => "integer",
			"character_maximum_length" => NULL,
			"numeric_precision" => "32",
			"numeric_scale" => "0",
			"datetime_precision" => NULL
		),
		"chat_id" => array(
			"type" => "int",
			"min" => "-2147483648",
			"max" => "2147483647",
			"column_name" => "chat_id",
			"column_default" => NULL,
			"is_nullable" => TRUE,
			"data_type" => "integer",
			"character_maximum_length" => NULL,
			"numeric_precision" => "32",
			"numeric_scale" => "0",
			"datetime_precision" => NULL
		),
		"offline" => array(
			"type" => "int",
			"min" => "-2147483648",
			"max" => "2147483647",
			"column_name" => "offline",
			"column_default" => NULL,
			"is_nullable" => TRUE,
			"data_type" => "integer",
			"character_maximum_length" => NULL,
			"numeric_precision" => "32",
			"numeric_scale" => "0",
			"datetime_precision" => NULL
		),
		"phone" => array(
			"type" => "string",
			"column_name" => "phone",
			"column_default" => NULL,
			"is_nullable" => TRUE,
			"data_type" => "character varying",
			"character_maximum_length" => NULL,
			"numeric_precision" => NULL,
			"numeric_scale" => NULL,
			"datetime_precision" => NULL
		),
	);
}
