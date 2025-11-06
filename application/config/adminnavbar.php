<?php
if (!Person::$user_id) {
    return [];
}


$icons = [
    'Dasboard' => 'home',
    'Management' => 'sliders',
    'Reports' => 'dollar-sign',
    'Bets' => 'play',
    'Freespins' => 'gift',
    'Manuals' => 'book',
    'Profile' => 'user',
];

$navBar = [

    'Dasboard' => ['dashboard' => 'Dashboard'],

    'CashUsers' => ['cashusers' => 'Users'],
    'Management' => [
        'terminals' => 'Terminals',
        'person' => 'Persons',
        'agtuser' => 'Users',
        'office' => 'Offices',
        'jackpot' => 'Jackpots',
        'games' => 'Games',
        'gameslimits' => 'Games limits',
        'gamesessions' => 'Game sessions',
        'jpstatus' => 'Jackpots status',
        'lockstatus' => 'Process status',
        'sessions' => 'Sessions',
        'moonroundbets' => 'ToTheMoon round bets',
        'moonban' => 'Moonban',
        'replays' => 'Replays',
		'sort' => 'Games order',
		'service' => 'Service',
    ],

    'Reports' => [
        'operation' => 'Operations',
        'operationstat' => 'Cash statistic by cashier',
        'playercashstat' => 'Cash statistic by user',
        'statisticgame' => 'Statistic',
        'statisticgamemonth' => 'Statistic for month',
        'report' => 'Day report UTC',
        'reportlocal' => 'Day report Local time',
        'reportmonth' => 'Month report UTC',
        'reportdynamic' => 'DS LS report UTC',
        'counters' => 'Counters',
        'counterspartners' => 'Counters Partners',
        'balance' => 'Balance report',
        'userbet' => 'Bet per user',
        'discount' => 'Discount report',
        'reporteur' => 'Day report UTC EUR',
        'weekactivity' => 'Week activity',
		'reportpromo' => 'Promo report'
    ],

    'Bets' => [
        'bet' => 'Bets',
		'bets' => 'Bets (fast)',
        'achivebet' => 'Archive bets',
        'jphistory' => 'JP history',
        'activity' => 'Activity',
        'moonhistory' => 'Moon results history',
    ],

    'Freespins' => [
        'freespins' => 'Freespins',
        'fshistory' => 'Freespins history',
        'fsbackhistory' => 'FSback history',
        'fsapi' => 'FS API constructor',
        'fsapi/give' => 'FS give',
        'fsprocesslist' => 'FS API process list',
        'events' => 'Events',
    ],
    'Manuals' => ['manuals' => 'Manuals'],
    'Promo' => ['promo' => 'Promo'],
    'Profile' => ['profile' => 'Profile'],

];


if (Person::$role == 'sa') {

    $personMenu = [

        'index',
        'office',
        //'jackpot',
        'jphistory',
        'statisticgame',
        'statisticgamemonth',
        'report',
        'userbet',
        'counters',
        'counterspartners',
        'person',
        'balance',
        'manuals',
        'bet',
		'bets',
        'achivebet',
        'freespins',
        'fshistory',
        'agtuser',
        'fsbackhistory',
        'games',
        'gameslimits',
        'profile',
        'reportlocal',
        //'reportdynamic',
        'gamesessions',
        'jpstatus',
        'lockstatus',
        'activity',
        'fsapi',
        'fsapi/give',
        'fsprocesslist',
        'sessions',
        'promo',
        'reportmonth',
        'moonhistory',
        'discount',
        'events',
        'moonroundbets',
        'moonban',
        'replays',
        'reporteur',
        'weekactivity',
		'currency',
        'countries',
		'reportpromo',
		'sort',
        'service'
		
    ];


}


if (Person::$role == 'gameman') {

    $personMenu = [

        'index',
        'office',
        //'jackpot',
        'statisticgame',
        'counterspartners',
        'report',
        'reporteur',
        'operationstat',
        'playercashstat',
        'bet',
		'achivebet',
        'agtuser',
        'fshistory',
        'freespins',
        'fsbackhistory',
        'fsapi',
        'fsprocesslist',
        'person',
        'balance',
        'manuals',
        'profile',
        'promo',
        'reportmonth',
        'moonhistory',
        'gameslimits',
        'jphistory',
		'currency',
		'countries',
        'discount' ,

    ];
}


if (Person::$role == 'client') {
    $personMenu = [

        'index',
        'person',
        'office',
        'statisticgame',
        'statisticgamemonth',
        'report',
        'reporteur',
        'operationstat',
        'playercashstat',
        'bet',
        'agtuser',
        'fshistory',
        'freespins',
        'fsbackhistory',
        'fsapi',
        'fsprocesslist',
        'manuals',
        'profile',
        'promo',
        'moonhistory',
        'reportmonth',
        'gameslimits',
        'jphistory',
		'currency',
		'countries'
    ];


}

if (Person::$role == 'cashier') {
    $personMenu = [

        'index',
        //'dashboard',
        'terminals',
        'operation',
        'operationstat',
        'bet',
        //'manuals',
        'profile',
        'cashusers'
    ];
}

if (Person::$role == 'bet') {
    $personMenu = [
        'index',
        'profile',

        'currency',
        'countries',
        'bet'
    ];
}

if (Person::$role == 'promo') {
    $personMenu = [
        'index',
        'profile',
        'promo',
		'currency',
		'countries'
    ];
}

if (Person::$role == 'report') {
    $personMenu = [
        'index',
		'profile',
        'report',
        'bet',
        'counterspartners',
        'statisticgamemonth',
        'promo',
		'currency',
		'countries'
    ];
}

if (Person::$role == 'fowner') {
    $personMenu = [
        'index',
        'report',
        'bet',
        'counterspartners',
        'statisticgamemonth',
        'manuals',
        'promo',
        'jphistory',
		'currency',
		'countries'
    ];
}

//sergei acc manager
if (in_array(Person::$user_id, [1214])) {
    $personMenu[]='discount';
	$personMenu[]='activity';
}

//olga manager
if (in_array(Person::$user_id, [1232])) {
    $personMenu[]='discount';
    $personMenu[]='report';
    $personMenu[]='manuals';
    $personMenu[]='reportmonth';
    $personMenu[]='statisticgamemonth';
}

if (in_array(Person::$user_id, [1023])) {
    $personMenu[]='games';
}

//недоSA срочно от дмитрия
if (in_array(Person::$user_id, [1175, 1176,1179])) {

    $personMenu = [

        'index',
        'office',
        //'jackpot',
        'jphistory',
        'userbet',
        'person',
        'balance',
        'manuals',
        'bet',
        'bets',
        'achivebet',
        'freespins',
        'fshistory',
        'agtuser',
        'fsbackhistory',
        'games',
        'gameslimits',
        'profile',
        'gamesessions',
        'jpstatus',
        'lockstatus',
        'activity',
        'fsapi',
        'fsapi/give',
        'fsprocesslist',
        'sessions',
        'promo',
        'moonhistory',
        'events',
        'moonroundbets',
        'moonban',
        'replays',
        //'reporteur',
        'weekactivity',
        'currency',
        'countries',
        'reportpromo',
        'sort'

    ];


}

return ['personMenu' => $personMenu, 'navBar' => $navBar, 'icons' => $icons];

