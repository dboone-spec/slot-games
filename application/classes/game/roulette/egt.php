<?php

//логика игры

class Game_Roulette_Egt
{

    protected $_name = 'egt';

    public function __construct($game)
    {
        $this->_game = $game;
        $this->_calc = new Roullete_Calc('egt',$this->_game);

        $this->_config_defaults = Kohana::$config->load($this->_name);
        $this->_config          = Kohana::$config->load($this->_name.'/' . $game);

        if(!empty(auth::$user_id) && $bets_arr = auth::user()->bets_arr) {
            $bets_arr = explode(',',auth::user()->bets_arr);

            if(!empty($bets_arr)) {
                foreach($bets_arr as $k=>$v) {
                    $bets_arr[$k]= floatval($v);
                }
                $this->_config['bets']=$bets_arr;
            }
        }
    }

    public function init() {
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

        $ans['complex']['jackpot']=false;
        return $ans;
    }

    public function restore() {
        $egtans = ["complex" => [
                        "currentState" => [
                            'history'=>[], //todo заполнить
                            'state'=>'idle',
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

        return $egtans;
    }

    public function spin() {
        $bet = arr::get($_GET,'bet',[]);
        if(empty($bet)) {
            throw new HTTP_Exception_404;
        }

        $amount = $bet['totalBet'] ?? 0;
        $betnum = $bet['bet'] ?? [];

        if(!$amount || empty($betnum)) {
            throw new HTTP_Exception_404;
        }

        game::session()->reload();

        $calc = $this->_calc;
        $calc->data = $betnum;

        foreach($calc->data as &$d) {
            $d=$d/100;
        }

        $calc->bettonum = $this->_config['betnum'];
        $calc->parsebet();
        $b_id = $calc->bet();

        $ans = [
                'balance' => auth::user(true)->amount()*100,
                'command' => "bet",
                'complex' => [
                    'gameCommand' => "bet",
                    'history' => [
                        [
                            'balance' => 499900,
                            'betSum' => 1100,
                            'gameNumber' => 1793593700722,
                            'playerBet' => $betnum,
                            'winAmount' => 1000,
                            'winNumber' => 30,
                        ],
                    ],
                    'jackpot' => false,
                    'loseBets' => $calc->lose_bets,
                    'winBets' => $calc->win_bets,
                    'winNumber' => $calc->num,
                ],
                "eventTimestamp" => time() * 1000,
                "gameIdentificationNumber" => $_GET['gameIdentificationNumber'],
                "gameNumber" => $b_id,
                "sessionKey" => $_GET['sessionKey'],
                "msg" => "success",
                "messageId" => $_GET['messageId'],
                'qName' => "app.services.messages.response.GameEventResponse",
                'state' => "idle",
                'winAmount' => $calc->win*100,
        ];

        return $ans;
    }

    public function save_win() {}

}
