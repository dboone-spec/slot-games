<?php

if(!Person::$user_id)
{
    return [];
}

$menus = [
        'index' => '',
        'dashboard' => __('Управление'),
        'user' => __('Пользователи'),
        'terminal' => __('Терминалы'),
        'office' => __('ППС'),
        'jackpot' => __('Джекпоты'),
        'userhistory' => __('История игрока'),
        'paymentsystem' => __('Платежные системы'),
        'paymentfield' => __('Платежные системы - данные'),
        'payment' => __('Платежи'),
        'paymentstat' => __('Статистика платежей'),
        'operationstat' => __('Статистика платежей'),
        'userpayment' => __('Статистика платежей (по пользователям)'),
        'bet' => __('Ставки'),
        'operation' => __('Операции'),
        'persons' => __('Персонал'),
        'payer' => __('Пополнить баланс'),
        'userbet' => __('Ставки (по пользователям)'),
        'countergame' => __('Счетчики'),
        'game' => __('Игры'),
        'officegame' => __('Игры (ППС)'),
        'statistics' => __('Статистика'),
        'statsall' => __('Сводная статистика'),
        'stats' => __('Статистика по играм'),
        'reportday' => __('Отчет по дням'),
        'bonuscode' => __('Бонус коды'),
        'bonusentered' => __('Введенные бонус коды'),
        'share' => __('Акции'),
        'sharestats' => __('Акции (статистика)'),
        'shareunreg' => __('Акции (не клиенты)'),
        'sharelangs' => __('Перевод для акций'),
        'shareprizes' => __('Акции (Начисление призов)'),
        'bonus' => __('Бонусы'),
        'status' => __('Сервисы'),
        'userprofit' => __('Бонусы ТП'),
        'logins' => __('Авторизации'),
        'freespin' => __('Фриспины'),
        'userprofile' => __('Профили'),
        'profile' => __('Настройки'),
        'manuals' => __('Инструкции и ПО'),
        'newsletter' => __('Письма'),
		'banks' => __('Банки'),
        'lang' => '',
        'reportday' => __('Отчет по дням'),
];


if(Person::$role == 'sa')
{

    return [

        'index' => '',
        'office' => __('ППС'),
        'statisticgame' => __('Games statistic'),
        'statisticgamemonth' => __('Games statistic for month'),
        'userhistory' => __('История игрока'),
        'report'=>'Office report',
        'counters'=>'Counters',
        'freespin'=>'Freespins',
        'freespinhistory'=>'Freespins history',
        'fsbackhistory'=>'FSback history',
        'person'=>'Persons',
        'balance'=>'Balance report',
        'manuals'=>'Manuals',
        'jackpot' => __('Джекпоты'),
        'bet' => __('Ставки'),
        'achivebet' => __('Archive bets'),
        'games'=>'Games',
        'operation' => __('Операции'),
        'payment' => __('Платежи'),

        'user' => __('Пользователи'),
        'person' => __('Персонал'),
        'userbet' => __('Ставки (по пользователям)'),
        'countergame' => __('Счетчики'),
        'game' => __('Игры'),
        'officegame' => __('Игры (ППС)'),
        'statisticsold' => __('Статистика (old version)'),
        'statsall' => __('Сводная статистика'),
        'stats' => __('Статистика по играм'),
        'reportday' => __('Отчет по дням'),
        'status' => __('Сервисы'),
        'logins' => __('Авторизации'),
        'profile' => __('Настройки'),
    ];


}


if(Person::$role == 'gameman')
{

    return [

        'index' => '',
        //'dashboard' => __('Управление'),
        'office' => __('ППС'),
        'jackpot' => __('Джекпоты'),
        'statisticgame' => __('Games statistic'),
        'report'=>__('Office report'),
        'bet' => __('Ставки'),
        'person'=>__('Persons'),
        'balance'=>__('Balance report'),
        'manuals'=>__('Manuals'),
    ];
}


if(Person::$role == 'client')
{
    if(PROJECT==1) {
        return [

            'index' => 'New Office',
            'dashboard' => 'New Office',
            'office' => __('ППС'),
            'statisticgame' => __('Games statistic'),
            'report'=>'Office report',
            'bet' => __('Ставки'),
            'manuals'=>'Manuals',
        ];
    }
    return [
        'index' => '',
        'dashboard' =>__('New Office'),
        'office' => __('ППС'),
        'statisticgame' => __('Games statistic'),
        'report'=>__('Office report'),
        'operation' => __('Операции'),
        'payment' => __('Платежи'),
        'operationstat' => __('Статистика операций'),
        'bet' => __('Ставки'),
        'jackpot' => __('Джекпоты'),
        'person'=>__('Persons'),
        'balance'=>__('Balance report'),
        'manuals'=>__('Manuals'),
    ];
}

if(Person::$role == 'cashier')
{
    return [

        'index' => '',
        'dashboard' => __('Управление'),
        'operation' => __('Операции'),
        'bet' => __('Ставки'),
        'manuals'=>__('Manuals'),
    ];
}










if(person::$role=='analitic') {
    $menus['operationstat'] = __('Статистика операций');
}

$roles = [
        'lang' => ['analitic','agent','rmanager','manager','administrator','kassa'],
        'index' => ['analitic','agent','rmanager','manager','administrator','cashier','gameman'],
        'dashboard' => ['analitic','agent','rmanager','manager','administrator','kassa','gameman'],
        'user' => ['analitic','agent','rmanager','manager','administrator','kassa'],
        'terminal' => ['kassa','rmanager'],
        'office' => ['analitic','agent','rmanager','manager','administrator','kassa','gameman'],
        'userhistory' => ['analitic','agent','rmanager','manager','administrator','kassa'],
        'paymentsystem' => [],
        'paymentfield' => [],
        'payment' => ['analitic','agent'],
        'paymentstat' => ['analitic','agent'],
        'operationstat' => ['analitic','agent','rmanager','manager','administrator','kassa'],
        'userpayment' => [],
        'bet' => ['analitic','agent','rmanager','manager','administrator','kassa','gameman'],
        'operation' => ['analitic','agent','rmanager','manager','administrator','kassa'],
        'persons' => ['analitic','agent','rmanager','manager'],
        'payer' => ['agent'],
        'userbet' => [],
        'countergame' => ['analitic'],
        'game' => [],
        'officegame' => ['analitic'],
        'statistics' => [],
        'statsall' => ['analitic'],
        'stats' => ['analitic'],
        'report' => [],
        'bonuscode' => [],
        'jackpot' => ['analitic','agent'],
        'bonusentered' => [],
        'share' => [],
        'sharestats' => [],
        'shareunreg' => [],
        'sharelangs' => [],
        'shareprizes' => [],
        'bonus' => [],
        'status' => [],
        'userprofit' => [],
        'logins' => [],
        'freespin' => [],
        'profile' => ['analitic','agent','rmanager','manager','administrator','kassa'],
        'userprofile' => [],
        'manuals' => ['analitic','agent','rmanager','manager','administrator','kassa'],
        'newsletter' => [],
		'banks' => ['analitic'],
        'reportday' => ['cashier'],
];

foreach($menus as $menu=>$name) {
    if(!in_array(Person::$role,$roles[$menu])) {
        unset($menus[$menu]);
    }
}

return $menus;
