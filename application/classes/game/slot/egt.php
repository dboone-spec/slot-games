<?php

//логика игры

class Game_Slot_Egt extends Game_Slot_Agt
{

    protected $_name = 'egt';

    //login
    public function init()
    {
        //for new users
        if(!isset($this->_session['comb']) || empty($this->_session['comb']))
        {
            $this->_calc->amount         = 0;
            $this->_calc->cline          = 0;
            $this->_calc->amount_line    = 0;
            $this->_calc->spin();
            $this->_session['comb']      = array_values($this->_calc->sym());
            $this->_session['extracomb'] = $this->_calc->extrasym();
            $this->save();
        }

        $ans = [
                "gameIdentificationNumber" => $_GET['gameIdentificationNumber'],
                "gameNumber" => -1,
                "sessionKey" => $_GET['sessionKey'],
                "msg" => "success",
                "messageId" => $_GET['messageId'],
                "qName" => "app.services.messages.response.GameResponse",
                "command" => "settings",
                "eventTimestamp" => time() * 1000,
                'complex' => Kohana::$config->load($this->_name . '/' . $this->_game . '.egt'),
        ];

        $d1=array_map(function($n) { return $n*0.1; },$ans['complex']['denominations'][0]);
        $d2=array_map(function($n) { return $n*0.01; },$ans['complex']['denominations'][0]);

        $ans['complex']['denominations'][]=$d1;
        $ans['complex']['denominations'][]=$d2;

        $ans['complex']['jackpot']=false;
        return $ans;
    }

    public function restore()
    {

        $ans = parent::restore();

        if(empty($ans['extracomb'])) {
            $ans['extracomb']=$this->_calc->extrasym();
        }

        if($ans['li']==0) {
            $ans['li']=count($this->_config['lines']);
        }

        $ans['li'] = isset($this->_config['staticlines'])?$this->_config['staticlines'][0]:$ans['li'];

        $egtans = ["complex" => [
                        "currentState" => [
                            "gamblesUsed" => $ans['step'],
                            "freespinsUsed" => 0,
                            "previousGambles" => $ans['gamble_suit_history'],
                            "bet" => $ans['amount']*100/$ans['li'],
                            "numberOfLines" => $ans['li'],
                            "denomination" => 100,
                            "state" => $ans['step']>0 && $ans['step']<5?'gamble':'idle',//todo узнать что тут
                            "winAmount" => $ans['win']*100,
                            "reels" => arr::flatten($ans['extracomb']),
                            "lines" => [
                            ],
                            "scatters" => [
                            ],
                            "expand" => [
                            ],
                            "specialExpand" => [
                            ],
                            "gambles" => 5-$ans['step'],
                            "freespins" => 0,
                            "freespinScatters" => [
                            ],
                            "jackpot" => false
                        ],
                        "jackpotState" => [
                                "levelI" => 760500,
                                "levelII" => 2942200,
                                "levelIII" => 10853800,
                                "levelIV" => 28324600,
                                "winsLevelI" => 19470,
                                "largestWinLevelI" => 12792000,
                                "largestWinDateLevelI" => "Jan 18, 2019 9:23:21 PM",
                                "largestWinUserLevelI" => "player",
                                "lastWinLevelI" => 1970200,
                                "lastWinDateLevelI" => "Jul 26, 2019 1:27:07 PM",
                                "lastWinUserLevelI" => "player",
                                "winsLevelII" => 9721,
                                "largestWinLevelII" => 25566700,
                                "largestWinDateLevelII" => "Nov 30, 2018 10:28:39 AM",
                                "largestWinUserLevelII" => "player",
                                "lastWinLevelII" => 4138000,
                                "lastWinDateLevelII" => "Jul 26, 2019 1:19:16 PM",
                                "lastWinUserLevelII" => "player",
                                "winsLevelIII" => 2671,
                                "largestWinLevelIII" => 107521900,
                                "largestWinDateLevelIII" => "Dec 15, 2018 7:11:49 AM",
                                "largestWinUserLevelIII" => "player",
                                "lastWinLevelIII" => 17951000,
                                "lastWinDateLevelIII" => "Jul 26, 2019 12:25:56 PM",
                                "lastWinUserLevelIII" => "Demo Player - 1564143898178",
                                "winsLevelIV" => 987,
                                "largestWinLevelIV" => 261827300,
                                "largestWinDateLevelIV" => "Jan 9, 2019 12:08:47 AM",
                                "largestWinUserLevelIV" => "player",
                                "lastWinLevelIV" => 58269700,
                                "lastWinDateLevelIV" => "Jul 26, 2019 12:50:44 PM",
                                "lastWinUserLevelIV" => "player"
                        ]
                ],
                "gameIdentificationNumber" => $_GET['gameIdentificationNumber'],
                "gameNumber" => -1,
                "sessionKey" => $_GET['sessionKey'],
                "msg" => "success",
                "messageId" => $_GET['messageId'],
                "qName" => "app.services.messages.response.GameEventResponse",
                "command" => "subscribe",
                "eventTimestamp" => time() * 1000
        ];

        if($this->_game=='supremehot') {
            $ans['complex']['currentState']['combo'] = 27;
            $ans['complex']['currentState']['combos'] = [];
        }

        return $egtans;
    }

    public function double($select)
    {

        $select='red';
        if(arr::get($_GET['bet'],'color')==1) {
            $select='black';
        }

        $ans = parent::double($select);
	
	$r = $ans['suite'];

        if(th::isMobile()) {
            switch($ans['suite']) {
                case 0:
                    $r=1;
                    break;
                case 1:
                    $r=1;
                    break;
                case 2:
                    $r=0;
                    break;
                case 3:
                    $r=0;
                    break;
            }
        }

        $a = [
                "complex" => [
                        "gambles" => $ans['gamble_max_steps']-$ans['step']-1,
                        "card" => $r,
                        "jackpot" => false,
                        "gameCommand" => "gamble"
                ],
                "state" => $ans['win']>0?'gamble':"idle",
                "winAmount" => (float) $ans['win'] * 100,
                "gameIdentificationNumber" => $_GET['gameIdentificationNumber'],
                "gameNumber" => bet::$last_bet_id,
                "balance" => $ans['balance'] * 100,
                "sessionKey" => $_GET['sessionKey'],
                "msg" => "success",
                "qName" => "app.services.messages.response.GameEventResponse",
                "command" => "bet",
                "eventTimestamp" => time() * 1000,
                "messageId" => $_GET['messageId'],
        ];



        return $a;
    }

    public function spin($li = 0,$amount = 0,$didx = 0)
    {
        $li     = (int) arr::get($_GET['bet'],'lines',-1);
        $amount = $li * arr::get($_GET['bet'],'bet',-1) / 100;

        if(isset($this->_config['staticlines']) && count($this->_config['staticlines'])>0) {
            $li = $this->_config['staticlines'][0];
        }

        if(auth::user(true)->last_game && $this->_game == auth::user()->last_game) {
            $ans = $this->bonus_game();
        }
        else {
            $ans = parent::spin($li,$amount,$didx);
        }

        $ex = $this->_calc->findReplaceBarSymbolPos();

        $a = [
                "complex" => [
                        "reels" => arr::flatten($ans['extracomb']),
                        "lines" => [
                        ],
                        "scatters" => [
                        ],
                        "expand" => [
                        ],
                        "specialExpand" => [],
                        "gambles" => 0,
                        "freespins" => $ans['bonus_win'],
                        "jackpot" => false,
                        "gameCommand" => "bet"
                ],
                'comb' => $ans['comb'],
                "state" => "idle",
                "winAmount" => (int) ($ans['win'] * 100),
                "gameIdentificationNumber" => $_GET['gameIdentificationNumber'],
                "gameNumber" => bet::$last_bet_id,
                "balance" => $ans['balance'] * 100,
                "sessionKey" => $_GET['sessionKey'],
                "msg" => "success",
                "qName" => "app.services.messages.response.GameEventResponse",
                "command" => "bet",
                "eventTimestamp" => time() * 1000,
                "messageId" => $_GET['messageId'],
                'jpwin' => bet::$jpwin,
        ];

        if(!empty($ex)) {
            $a['complex']['specialExpand'] = [$ex[0][0]-1,$ex[0][1]];
        }
        //монетки и выпадение скаттеров!!!!!!!!!
        /*
         * balance: 357000
            command: "bet"
            complex: {reels: [5, 5, 7, 1, 1, 1, 7, 3, 3, 3, 1, 1, 1, 7, 5, 2, 2, 7, 0, 0, 3, 3, 3, 2, 2], lines: [],…}
            expand: []
            freespins: 0
            gambles: 1
            gameCommand: "bet"
            jackpot: false
            lines: []
            reels: [5, 5, 7, 1, 1, 1, 7, 3, 3, 3, 1, 1, 1, 7, 5, 2, 2, 7, 0, 0, 3, 3, 3, 2, 2]
            scatters: [{scatterName: 7, cells: [0, 1, 1, 0, 2, 2, 3, 1], winAmount: 40000, freespins: 0}]
                0: {scatterName: 7, cells: [0, 1, 1, 0, 2, 2, 3, 1], winAmount: 40000, freespins: 0}
            cells: [0, 1, 1, 0, 2, 2, 3, 1]
            freespins: 0
            scatterName: 7
            winAmount: 40000
            specialExpand: []
            eventTimestamp: 1564496690116
            gameIdentificationNumber: 803
            gameNumber: 1778649915673
            messageId: "r-r_c7ac8a466507c4bf741d7118e5ef6f3f7912d33b"
            msg: "success"
            qName: "app.services.messages.response.GameEventResponse"
            sessionKey: "55ef4736d31287c17ac996b79ed923fa"
            state: "gamble"
            winAmount: 40000
         */

        //after scatters and coins!!!!

        /*
         * command: "event"
            complex: {levelI: 1492400, levelII: 6792400, levelIII: 8430100, levelIV: 87103600, winsLevelI: 20022,…}
            largestWinDateLevelI: "Jan 18, 2019 9:23:21 PM"
            largestWinDateLevelII: "Nov 30, 2018 10:28:39 AM"
            largestWinDateLevelIII: "Dec 15, 2018 7:11:49 AM"
            largestWinDateLevelIV: "Jan 9, 2019 12:08:47 AM"
            largestWinLevelI: 12792000
            largestWinLevelII: 25566700
            largestWinLevelIII: 107521900
            largestWinLevelIV: 261827300
            largestWinUserLevelI: "player"
            largestWinUserLevelII: "player"
            largestWinUserLevelIII: "player"
            largestWinUserLevelIV: "player"
            lastWinDateLevelI: "Jul 30, 2019 2:22:29 PM"
            lastWinDateLevelII: "Jul 30, 2019 1:49:55 PM"
            lastWinDateLevelIII: "Jul 30, 2019 2:21:50 PM"
            lastWinDateLevelIV: "Jul 30, 2019 3:25:52 AM"
            lastWinLevelI: 3023600
            lastWinLevelII: 1549500
            lastWinLevelIII: 22967200
            lastWinLevelIV: 27550600
            lastWinUserLevelI: "player"
            lastWinUserLevelII: "player"
            lastWinUserLevelIII: "Demo Player - 1564496320841"
            lastWinUserLevelIV: "player"
            levelI: 1492400
            levelII: 6792400
            levelIII: 8430100
            levelIV: 87103600
            winsLevelI: 20022
            winsLevelII: 9980
            winsLevelIII: 2732
            winsLevelIV: 1018
            eventTimestamp: 1564496690157
            gameIdentificationNumber: 803
            gameNumber: -1
            messageId: "5b8cf08264d72726520faf92c99d9aa5"
            msg: "success"
            qName: "app.services.messages.response.GameEventResponse"
         */

        //and next
        /*
         *command: "event"
          complex: {levelI: 1505800, levelII: 6805800, levelIII: 8443500, levelIV: 87117000, winsLevelI: 20022,…}
            largestWinDateLevelI: "Jan 18, 2019 9:23:21 PM"
            largestWinDateLevelII: "Nov 30, 2018 10:28:39 AM"
            largestWinDateLevelIII: "Dec 15, 2018 7:11:49 AM"
            largestWinDateLevelIV: "Jan 9, 2019 12:08:47 AM"
            largestWinLevelI: 12792000
            largestWinLevelII: 25566700
            largestWinLevelIII: 107521900
            largestWinLevelIV: 261827300
            largestWinUserLevelI: "player"
            largestWinUserLevelII: "player"
            largestWinUserLevelIII: "player"
            largestWinUserLevelIV: "player"
            lastWinDateLevelI: "Jul 30, 2019 2:22:29 PM"
            lastWinDateLevelII: "Jul 30, 2019 1:49:55 PM"
            lastWinDateLevelIII: "Jul 30, 2019 2:21:50 PM"
            lastWinDateLevelIV: "Jul 30, 2019 3:25:52 AM"
            lastWinLevelI: 3023600
            lastWinLevelII: 1549500
            lastWinLevelIII: 22967200
            lastWinLevelIV: 27550600
            lastWinUserLevelI: "player"
            lastWinUserLevelII: "player"
            lastWinUserLevelIII: "Demo Player - 1564496320841"
            lastWinUserLevelIV: "player"
            levelI: 1505800
            levelII: 6805800
            levelIII: 8443500
            levelIV: 87117000
            winsLevelI: 20022
            winsLevelII: 9980
            winsLevelIII: 2732
            winsLevelIV: 1018
            eventTimestamp: 1564496695165
            gameIdentificationNumber: 803
            gameNumber: -1
            messageId: "74b64e82dc22c1c605e34d6dab3d3c16"
            msg: "success"
            qName: "app.services.messages.response.GameEventResponse"
         */

        if($ans['win'] > 0)
        {
            if(!empty($this->_calc->replaced_symbols_in_bar)) {
                $a['complex']['expand']=explode(',',implode(',',$this->_calc->replaced_symbols_in_bar));
                $a['complex']['expand'] = array_map('intval',$a['complex']['expand']);
            }

            $a['state'] = 'gamble';
            $lines_cnf  = $this->_config['lines'];
            $allcells=[];
            foreach($ans['linesValue'] as $lk => $lv)
            {
                if($lv == 0)
                {
                    continue;
                }

                $cells = [];

                //scatters. need remake answer
                if($lk <= 0)
                {
                    $mask = str_pad(decbin($ans['linesMask'][$lk]), $this->_calc->heigth*$this->_calc->barcount, "0", STR_PAD_LEFT);
                    for($i=0; $i<strlen($mask);$i++) {
                        if($mask[$i]==1) {
                            $cells[]=$i%$this->_calc->barcount;
                            $cells[]=floor($i/$this->_calc->barcount);
                        }
                    }

                    $a['complex']['scatters']=[
                        [
                            'scatterName' => $this->_config['anypay'][0],
                            'cells' => $cells,
                            'winAmount' => $lv*100,
                            'freespins' => $ans['bonus_win']
                        ],
                    ];
                }
                else {
                    $card = -1;
                    for($y = 0; $y < count($lines_cnf[$lk]); $y++)
                    {
                        for($x = 0; $x < count($lines_cnf[$lk][$y]); $x++)
                        {
                            if($lines_cnf[$lk][$y][$x] == 1 && str_pad(decbin($ans['linesMask'][$lk]), $this->_calc->heigth, "0", STR_PAD_LEFT)[$x] == 1)
                            {
                                $cells[] = $x;
                                $cells[] = $y;
                                //$card = $ans['comb'][$y*$this->_calc->heigth + $x];
                            }
                        }
                    }

		    for($i=0;$i<=(count($cells)/2);$i=$i+2) {

                        $sym_index = ($cells[$i+1])*$this->_calc->barcount+$cells[$i];
                        if(!in_array($ans['comb'][$sym_index],$this->_config['wild'])) {
                            $card = $ans['comb'][$sym_index];

                            break;
                        }
                    }

                    if($card==-1) {
                        $card = $this->_config['wild'][0];
                    }

                    $a['complex']['lines'][] = [
                            'line' => $lk - 1,
                            "winAmount" => $lv*100,
                            "cells" => $cells,
                            "freespins" => $ans['bonus_win'],
                            "card" => $card
                    ];

                    $allcells[]=$cells;

                    //supremehot
//                    card: 0
//                    cells: [0, 2, 1, 0, 1, 1, 2, 1, 2, 2]
//                    count: 4
//                    len: 3
//                    multiplier: 1
//                    winAmount: 4000
//                    winPerCount: 1000

                }
            }

            if($this->_game=='supremehot') {
                $combos = [
                        'card' => 0,
                        'cells' => arr::flatten($allcells),
                        'count' => count($a['complex']['lines']),
                        'len' => 3,
                        'multiplier' => 1,
                        'winAmount' => $a['complex']['lines'][0]['winAmount']*count($a['complex']['lines']),
                        'winPerCount' => $a['complex']['lines'][0]['winAmount'],
                ];
                unset($a['complex']['lines']);
                $a['complex']['combos'] = [$combos];
            }

            $a['complex']['gambles'] = 5;

            //rodo добавить условие, если последний фриспин то gambles = 1
        }

        if($ans['bonus_all']>0 && $ans['bonus_win']!=$ans['bonus_all']) {
            $a['winAmount'] = (int) ($ans['session_total_win_free'] ?? 0)*100;
        }

        if($ans['bonus']>0) {
            $a['state']='freespin';
            $a['complex']['gambles'] = 0;
        }

        if($ans['bonus_win']>0) {
            $a['complex']['freespinScatters'] = $this->_config['scatter'];
            /*
             * {
                "complex": {
                    "reels": [4,0,9,9,2,4,0,11,1,5,6,6,10,4,2,0,11,2,6,6,2,5,11,3,7,7,2,4,6,6],
                    "lines": [ ],
                    "scatters": [
                        {
                            "scatterName": 11,
                            "cells": [
                                1,
                                0,
                                2,
                                3,
                                3,
                                3
                            ],
                            "winAmount": 20000,
                            "freespins": 10
                        }
                    ],
                    "expand": [ ],
                    "specialExpand": [ ],
                    "gambles": 0,
                    "freespins": 10,
                    "freespinScatters": [
                        11
                    ],
                    "jackpot": false,
                    "gameCommand": "bet"
                },
                "state": "freespin",
                "winAmount": 20000,
                "gameIdentificationNumber": 847,
                "gameNumber": 1782097080346,
                "balance": 436500,
                "sessionKey": "a7b931ba2c1effd860ae784cd4a053d7",
                "msg": "success",
                "messageId": "r-r_bdac0e469d051f9e44a424575c873fe6bee8be1e",
                "qName": "app.services.messages.response.GameEventResponse",
                "command": "bet",
                "eventTimestamp": 1565338282493
            }
             */
        }

        if($this->_calc->bonus_win>0) {
            $lines_cnf  =  $this->_config['lines'];

            $a['complex']['freeSpinsExpandLines']=[];

            foreach($ans['bonus_super_symbol_win']['linesValue'] as $lk => $lv)
            {
                if($lv == 0)
                {
                    continue;
                }

                $cells = [];

                //scatters. need remake answer
                $card = $ans['extra_param'];
                for($y = 0; $y < count($lines_cnf[$lk+1]); $y++)
                {
                    for($x = 0; $x < count($lines_cnf[$lk+1][$y]); $x++)
                    {
                        if($lines_cnf[$lk+1][$y][$x] == 1
                                && str_pad(decbin($ans['bonus_super_symbol_win']['linesMask'][$lk]), 5, "0", STR_PAD_LEFT)[$x] == 1)
                        {
                            $cells[] = $x;
                            $cells[] = $y;
                        }
                    }
                }
                $a['complex']['freeSpinsExpandLines'][] = [
                        'line' => $lk,
                        "winAmount" => $lv*100,
                        "cells" => $cells,
                        "card" => $card
                ];
                $a['complex']['freeSpinsExpandWinAmount']=$ans['bonus_super_symbol_win']['win'];
            }
        }

        $a['sess']=$this->_session;

        $a['oldans']=$ans;

        return $a;
    }

    public function save_win()
    {
        $ans = parent::save_win();

        $a = [
                'balance' => $ans['balance'],
                'command' => "bet",
                'complex' => [
                    'gameCommand' => "collect"
                ],
                'gameCommand' => "collect",
                "eventTimestamp" => time() * 1000,
                "messageId" => $_GET['messageId'],
                "gameIdentificationNumber" => $_GET['gameIdentificationNumber'],
                'gameNumber' => bet::$last_bet_id,
                'msg' => "success",
                'qName' => "app.services.messages.response.GameEventResponse",
                "sessionKey" => $_GET['sessionKey'],
                'state' => "idle",
                "winAmount" => $ans['real_win'],
        ];
        return $a;
    }
}
