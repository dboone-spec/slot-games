<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'alpha'         => ':field '.__('может содержать только буквы'),
	'alpha_dash'    => ':field '.__('может содержать только цифры, буквы и тире'),
	'alpha_numeric' => ':field '.__('может содержать только цифры и буквы'),
	'color'         => ':field must be a color',
	'credit_card'   => ':field must be a credit card number',
	'date'          => __('Поле').' :field '.__('должно быть датой'),
	'decimal'       => ':field must be a decimal with :param2 places',
	'digit'         => __('Поле').' :field '.__('должно быть числом'),
	'email'         => ':field '.__('должен быть правильным email адресом'),
	'email_domain'  => ':field must contain a valid email domain',
	'equals'        => ':field must equal :param2',
	'exact_length'  => __('Поле') . ' :field ' . __('должно содержать') . ' :param2 '. __('символа'),
	'in_array'      => ':field must be one of the available options',
	'ip'            => ':field must be an ip address',
	'matches'       => ':field must be the same as :param2',
	'min_length'    => __('Поле').' :field '.__('должно быть не менее').' :param2 '.__('символов'),
	'max_length'    => __('Поле').' :field '.__('должно быть не более').' :param2 '.__('символов'),
	'not_empty'     => __('Поле').' :field '.__('должно быть заполнено'),
	'numeric'       => ':field '.__('должно быть числом'),
	'phone'         => __('Поле').' :field '.__('должно быть телефонным номером') ,
	'range'         => ':field must be within the range of :param2 to :param3',
	'regex'         => __('Поле').' :field '.__('имеет неправильный формат'),
	'url'           => __('Поле').' :field '.__('должно быть url адресом'),
	'Model_User::uniqueName'=>__('Пользователь с таким именем уже зарегистрирован'),
	'Model_User::uniqueEmail'=>__('Пользователь с таким email уже зарегистрирован'),
		
		

);
