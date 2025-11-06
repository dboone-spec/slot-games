<?php defined('SYSPATH') or die('No direct script access.');
$a=[
	//нельзя сменитть стол после последней ставки сек
	'table_time_friz'=>60,

	//максимальный выигрыш, который не проверяется в банке в кредитах
	'zero_win'=>10,

    'alert_max_win'=>10000,

	//максимальный выигрыш, которые не проверяется в банке в отношении к первоначальной ставке 1 - (100%)
	//идет или с zero win
	'zero_win_percent'=>1,

	//сколько от остатка банка можно выиграть за раз
	'bank_percent_win'=>0.30,


	'dropk' => 2,
	'chat_tokens' => [

    ],
    /*
     * для ежедневного отчета в телегу
     */
	'reportphones' => [
    ],
    /*
     * оповещения в телегу
     */
    'alertphones' => [
    ],
    //минимальная сумма платежа
    'min_sum_pay' => 300,

    'payment_groups' => [
            'emoney' => 'Электронные деньги',
            'cards' => 'Банковские карты',
    ],
    //время действия бонуса
    'active_time_bonus' => 2*60*60,
    'chrome' => '',
    //key - порядковый номер c 1, value - id игры в таблице
    'gamestopwins' => [
        1 => 1,
        2 => 3,
        3 => 33,
        4 => 7,
        5 => 5,
    ],
    /*
     * между какими офисами может переключаться игрок
     * 555 - fun
     */
    'offices' => [1,4],
    /*
     * список игр в которых можно выбрать фриспины при регистрации
     * name из games
     */
    'reg_fs_games' => ['luckyladycharmd','dolphinsd','columbusd','coldspell'],
    'office_max_out' => 100000,
    /*
     * список игр в которых можно получить ежедневные фриспины
     */
    'choice_dayly_games' => ['luckyladycharmd','dolphinsd','columbusd','coldspell','bookofrad'],
    'cashback' => 0.2,
    'test_offices' => [888],
    'static_domain' => 'https://content.kolinz.xyz',
    'gameapi_domen' => PROJECT=='1'?((isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'],['app.agtint.ru']))?'https://content.agtint.ru':'https://content.megawinapp.com'):'https://content.mangobet.org',
	'gameapi_domen' => PROJECT=='1'?'https://content.kolinz.xyz':'https://content.mangobet.org',
	
	'gameapi_ru_domen' => 'https://'.GAMECONTENT_DOMAIN,

	'jptime'=>2*60,
    'jp_wss_url'=>'wss://ws.site-domain.com:2096/',
    'jp_wss_url'=>(auth::$user_id && auth::user()->office->owner==1042)?'wss://ws.gamecontent1.com:2096/':'wss://ws.kolinz.xyz:2096/',

    'gameapi_domen_infin' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_infin' => 'https://'.GAMECONTENT_DOMAIN,
	
	'aerobet_wss_url'=>'wss://ws.kolinz.xyz:2087/',
    'moon_wss_url'=>'wss://ws.kolinz.xyz:8443/',
    'terminal_wss_url'=>'wss://leon247.com:8443/',

    'gameapi_domen_vertbet' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_vertbet' => 'https://'.GAMECONTENT_DOMAIN,

    'gameapi_domen_ematrix' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_ematrix' => 'https://'.GAMECONTENT_DOMAIN,
	
	'gameapi_domen_evenbet' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_evenbet' => 'https://'.GAMECONTENT_DOMAIN,
	
	'gameapi_domen_softgamings' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_softgamings' => 'https://'.GAMECONTENT_DOMAIN,
	
	'gameapi_domen_betconstruct' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_betconstruct' => 'https://'.GAMECONTENT_DOMAIN,
	
	'gameapi_domen_pinup' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_pinup' => 'https://'.GAMECONTENT_DOMAIN,

	'gameapi_domen_pinco' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_pinco' => 'https://'.GAMECONTENT_DOMAIN,
		
	'gameapi_domen_tvbet' => 'https://'.GAMECONTENT_DOMAIN,
    'static_domain_tvbet' => 'https://'.GAMECONTENT_DOMAIN,
			
	'gameapi_domen_softswiss' => 'https://'.GAMECONTENT_DOMAIN,
    'gameapi_domen_softswiss' => 'https://'.GAMECONTENT_DOMAIN,

    'curl_timeouts'=>[
        'default'=>[2,7],
        'infin'=>[2,7],
        'vertbet'=>[2,7],
		'evenbet'=>[2,7],
		'betconstruct'=>[2,7],
		'tvbet'=>[2,7],
    ],
];

	if(defined('INFINSOC_DOMAIN') && INFINSOC_DOMAIN){
        $a['gameapi_domen_infin']='https://content.events-io.com';
        $a['static_domain_infin']='https://content.events-io.com';
        $a['jp_wss_url']='wss://events-io.com:2096/';
        $a['moon_wss_url']='wss://events-io.com:2083/';
    }
	
	if(defined('X_DOMAIN') && auth::$user_id==4600700) {
		$a['jp_wss_url']='wss://'.X_DOMAIN.'/ws/';
		$a['moon_wss_url']='wss://'.X_DOMAIN.'/ws1/';
		$a['aerobet_wss_url']='wss://'.X_DOMAIN.'/ws2/';
	}

return $a;