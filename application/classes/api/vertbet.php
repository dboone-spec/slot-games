<?php

class Api_Vertbet extends gameapi
{

    //https://site-domain.local/api23dev/launch/icecream100?currency=BRL&game-id=icecream100&lang=en-US&session-token=8a240058-3e03-4111-ba51-89968e381ece&user-id=60099691651553604_BRL
    public $key, $guid, $platform, $gameName, $session_token;
    protected $_url = '';
    private $_token = '';
    protected $_wrongBetType='';
    public $bet_transaction_id;
    public $win_transaction_id;

    protected function _send_headers()
    {
        return [
            'Content-Type: application/json',
            'X-Integration-Token: ' . $this->_token,
        ];
    }

    public function setForceToken($token) {
        $this->_token = $token;
    }

    public function setURL($url)
    {
        $this->_url = $url;
    }

    public function checkGame($office_id)
    {


        $g = new Model_Game(['name' => $this->gameName]);

        if (!$g->loaded() || $g->show == 0) {
            return false;
        }

        $og = new Model_Office_Game([
            'office_id' => $office_id,
            'game_id' => $g->id,
        ]);

        if (!$og->loaded() || $og->enable == 0) {
            return false;
        }

        return true;

    }


    public function checkUser($userId, $office_id)
    {
        $u = new Model_User(['office_id' => $office_id, 'external_id' => $userId, 'api' => 5]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

        $user_balance=$this->checkBalance($userId,$office_id,$this->_url,$this->session_token);

        if(!$user_balance) {
            throw new Exception('external user not found');
        }

        $u->amount="".$user_balance;

        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

    public function checkOffice($currency, $test = 1,$partner='')
    {
        $currency = new Model_Currency(['external_id' => $currency,'source'=>'vertbet']);

        $external_name='vertbet'.$partner;

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'is_test' => $test
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }

        return $o->id;
    }

    public function createUser($userId, $office_id)
    {

        $user_balance=$this->checkBalance($userId,$office_id,$this->_url,$this->session_token);

        if(!$user_balance) {
            throw new Exception('external user not found');
        }

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $userId . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 5;
        $u->amount="".$user_balance;
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->visible_name = $userId;
        $u->external_id = $userId;
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createOffice($currency, $test = 1,$partner='')
    {
        $currency = new Model_Currency(['external_id' => $currency,'source'=>'vertbet']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o = new Model_Office;

        $external_name='vertbet'.$partner;

        $o->currency_id = $currency->id;
        $o->visible_name = "Vertbet$partner " . ($test == 1 ? '[TEST]' : '') . " {$currency->code}";
        $o->external_name = $external_name;

        $o->apienable = 1;
        $o->apitype = 5;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;

        $o->gameapiurl = $this->_url;

        $o->bonus_diff_last_bet = 8;
        $o->enable_bia = time();
        $o->rtp = 96;
        $o->owner = 1061;

        $o->dentabs = $currency->default_den;
        $o->default_dentab = $currency->default_dentab;
        $o->k_to_jp = 0.005;
        $o->k_max_lvl = $currency->default_k_max_lvl;
        $o->enable_jp = 1;
		
		$o->enable_moon_dispatch=1;

        $o->games_rtp = 97;
        $o->gameui = 1;

        $o->is_test = $test;
        $o->seamlesstype = 1;

        $o->secretkey = $this->_token;

        database::instance()->begin();

        //TODO поификсить создание джекпотаов

        try {
            //создаем игры здесь
            $o->need_create_default_games = false;
            $o->save()->reload();


            database::instance()->direct_query('insert into person_offices (person_id,office_id)
                                        values (' . $o->owner . ',' . $o->id . '),(1061,' . $o->id . ')');


            $sql_games = <<<SQL
                insert into office_games(office_id, game_id, enable)
                Select :office_id, g.id, 1
                From games g
                Where g.provider = 'our' and brand ='agt' and show=1 and vertbet_show=1 and g.category!='coming'
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
            throw $ex;
        }

        database::instance()->commit();

        return $o->id;

    }

    public function checkBalance($login, $office_id, $url, $sess_id = null)
    {

        $url=$url.'/user-info';

        $parser = new Parser();
        $parser->disableFailOnError();

        $o = Office::instance($office_id)->office();

        $data = [
            'session_token'=>$sess_id
        ];

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office_id . '] request: ' . $url . PHP_EOL . json_encode($data, 1).PHP_EOL.print_r($this->_send_headers(),1), 'vertbet');

        $start_time = microtime(1);

        $r=$parser->post($url,$data,true,$this->_send_headers());

        $response_time = microtime(1) - $start_time;
        logfile::create(date('Y-m-d H:i:s') . ' [' . $o->id . '] time: |' . $response_time . '| response: ' . $url . PHP_EOL . $r, 'vertbet');


        if (!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $o->id . '][' . $login . '] time: |' . $response_time . '| ERROR response: <' . $parser->error . '> ' . $url, 'vertbet');
            return false;
        }

        $jr = json_decode($r, 1);

        if (!$jr) {
            return false;
        }

        if (!isset($jr['balance']) || (float)$jr['balance'] < 0) {
            return false;
        }

        if((int) $jr['balance_multiplier']==0) {
            return "".$jr['balance'];
        }

        return bcdiv($jr['balance'],$jr['balance_multiplier'],substr_count($jr['balance_multiplier'],'0'));
    }

    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $cashier_url = false)
    {
        $domain = kohana::$config->load('static.gameapi_domen_vertbet');

        $g = new Model_Game(['name' => $this->gameName]);

        $link = $g->get_link($domain);
        $user = new Model_User($user_id);

        $user->api_key = guid::create();
        $user->lang = $lang;
        $user->save();

        $link_params = [];

        $link_params[] = 'user=' . $user->api_name;
        $link_params[] = 'token=' . $user->api_key;
        $link_params[] = 'force_mobile=' . ((int)$force_mobile);

        if ($closeurl) {
            if ($closeurl !== urldecode($closeurl)) {
                $closeurl = urlencode($closeurl);
            }

            $link_params[] = 'closeurl=' . $closeurl;
        }

        if ($cashier_url) {
            $link_params[] = 'cashierurl=' . $cashier_url;
        }

        $link_params[] = 'no_close=' . ((int)$no_close);

        $link .= '?' . implode('&', $link_params);

        return $link;
    }

    public function commandId(){

        $r=dbredis::instance();
        $value=$r->incr('vertbetCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('vertbetCommandId',time()*100+$ms);
            $value=$r->incr('vertbetCommandId');
        }

        return $value;


    }

    public function isNeedSend($url)
    {
        return true;
    }

    public function getCurrencies() {
        $parser = new Parser();

        logfile::create(date('Y-m-d H:i:s') . ' [GETTING CURRENCIES] request: ' . $this->_url.'/currencies', 'vertbet');

        $res = $parser->get($this->_url.'/currencies');

        logfile::create(date('Y-m-d H:i:s') . ' [GETTING CURRENCIES] response: ' . $res, 'vertbet');

        return $res;
    }

    public function getUrl($url)
    {
        $this->_url = $url;
        return $this->_url;
    }

    protected function _check_errors() {

    }

    protected function _send_bet_request($url,$data,$office)
    {
        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] request bet: ' . $url . PHP_EOL . json_encode($data, 1).PHP_EOL.print_r($this->_send_headers(),1), 'vertbet');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $betRequest = $parser->post($url . '/bet', $data, true, $this->_send_headers());

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| response bet: ' . $url . PHP_EOL . $betRequest, 'vertbet');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url, 'vertbet');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet', 'vertbet');

            return false;
        }

        if (isset($jsonBet['code'])) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[code]', 'vertbet');
            return false;
        }

        if($jsonBet['provider_transaction_id']!=$data['transaction_id']) {
            logfile::create(date('Y-m-d H:i:s') . 'provider_transaction_id!=transaction_id', 'vertbet');
            return false;
        }

        $mult=$office->currency->mult ?? 2;

        if($mult>0 && pow(10,$mult)!=$jsonBet['balance_multiplier']) {
            logfile::create(date('Y-m-d H:i:s') . 'wrong balance_multiplier ['.pow(10,$mult).'!='.$jsonBet['balance_multiplier'].']', 'vertbet');
            return false;
        }

        return $jsonBet;
    }

    protected function _send_win_request($url,$data,$office) {
        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['round_id'].'] request win: '.$url.PHP_EOL.json_encode($data,1).PHP_EOL.print_r($this->_send_headers(),1),'vertbet');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $winRequest = $parser->post($url.'/win',$data,true,$this->_send_headers());

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['round_id'].'] time: |'.$response_time.'| response win: '.$url.PHP_EOL.$winRequest,'vertbet');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url, 'vertbet');
            return false;
        }
        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (!isset($jsonWin['provider_transaction_id'])) {
            logfile::create(date('Y-m-d H:i:s') . 'WIN empty provider_transaction_id', 'vertbet');
            return false;
        }

        if($jsonWin['provider_transaction_id']!=$data['transaction_id']) {
            //cancel transaction
            return false;
        }

        $mult=$office->currency->mult ?? 2;

        if($mult>0 && pow(10,$mult)!=$jsonWin['balance_multiplier']) {
            return false;
        }

        return $jsonWin;
    }

    protected function _send_cancel_request($url,$data,$office) {

        $data['reference_transaction_id']=$data['transaction_id'];
        unset($data['transaction_id']);

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['round_id'].'] request cancel: '.$url.PHP_EOL.json_encode($data,1).PHP_EOL.print_r($this->_send_headers(),1),'vertbet');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $cancelRequest = $parser->post($url.'/cancel-transaction',$data,true,$this->_send_headers());

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['round_id'].'] time: |'.$response_time.'| response cancel: '.$url.PHP_EOL.$cancelRequest,'vertbet');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $url, 'vertbet');
            return false;
        }
        $jsonCancel=json_decode($cancelRequest,1);

        if(!$jsonCancel) {
            return false;
        }

        if (isset($jsonCancel['code'])) {
            return false;
        }

        if($jsonCancel['reference_transaction_id']!=$data['reference_transaction_id']) {
            //cancel transaction
            return false;
        }

        $mult=$office->currency->mult ?? 2;

        if($mult>0 && pow(10,$mult)!=$jsonCancel['balance_multiplier']) {
            return false;
        }

        return $jsonCancel['transaction_id'];
    }

    public function bet($login,$url, $params=[],$is_repeat=false) {

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            throw Exception('user not found');
        }

        dbredis::instance()->select(0);
        $session_token=auth::getCustomSessionId($u->id,$u->api_session_id);


        if(!$session_token){
            throw new Exception('session expired');
        }

        $o=$u->office;

        $mult=$o->currency->mult ?? 2;

        $amount = "".floor($params['amount']*pow(10,$mult));
        $win = "".floor($params['win']*pow(10,$mult));
        $bet_id = $params['bet_id'];

        $transaction_id=$this->commandId();

        //bet
        $data=[
            "session_token" => $session_token,
            "player_id" => $u->external_id,
            "game_id" => $params['game'],
            "transaction_id" => "".$transaction_id,
            "round_id" => "".$bet_id,
            "round_closed" => $win==0,
            "amount" => $amount,
            "currency" => $u->office->currency->code
        ];

        $this->setURL($u->office->gameapiurl);
        $this->setForceToken($u->office->secretkey);

        if($amount>0) {
            $betRequest = $this->_send_bet_request($url,$data,$u->office);
            $this->bet_transaction_id=$data['transaction_id'];

            if(!$betRequest) {
                if(!$this->_send_cancel_request($url,$data,$u->office)) {
                    //move to wronbets as CANCEL
                    $this->_wrongBetType='cancel';
                    th::sendCurlError('vertbet');
                    return false;
                }
                th::sendCurlError('vertbet');
                throw new Exception_ApiResponse('bet rejected.');

                $this->_wrongBetType='bet';
                return false;
            }

            if(intval($betRequest['balance_multiplier'])>0) {
                $u->amount = bcdiv($betRequest['balance'],$betRequest['balance_multiplier'],substr_count($betRequest['balance_multiplier'],'0'));
            }
            else {
                $u->amount=$betRequest['balance'];
            }

        }

        //send win
        if($win>0) {


            if($params['game']=='jp') {
                $data['game_id']=bet::getJP()->game;
            }

            $data['transaction_id']="".$this->commandId();
            $data['round_closed']=true;
            $data['amount']=$win;

            $this->win_transaction_id=$data['transaction_id'];

            $winRequest = $this->_send_win_request($url,$data,$u->office);

            if(!$winRequest) {
                //try to send win again
                $winRequest = $this->_send_win_request($url,$data,$u->office);
                if(!$winRequest) {
                    if(!$this->_send_cancel_request($url,$data,$u->office)) {
                        //move to wrongbets as CANCEL
                        $this->_wrongBetType='cancel';
                        th::sendCurlError('vertbet');
                        return false;
                    }
                    else {
                        $this->_wrongBetType='win';
                        //move to wrongbets as WIN
                        th::sendCurlError('vertbet');
                        return false;
                    }
                }
            }

            if(intval($winRequest['balance_multiplier'])>0) {
                $u->amount = bcdiv($winRequest['balance'],$winRequest['balance_multiplier'],substr_count($winRequest['balance_multiplier'],'0'));
            }
            else {
                $u->amount=$winRequest['balance'];
            }
        }

        if($u->amount<0) {
            //todo что лучше сделать?
            return false;
        }

        $u->save();

        return true;
    }

    public function jp($login,$url, $params=[]) {
        return false;
    }

    public function saveWrongBet($bet,$params,$poker_bet_id) {

        if($bet->game=='jp') {
            $bet->game=bet::getJP()->game;
        }

        $b = new Model_WrongbetVertbet();
        $b->bet_id = $bet->id;
        $b->request_id = $this->bet_request_id;
        $b->user_id = $bet->user_id;
        $b->amount = $bet->amount;
        $b->game_type = $bet->game_type;
        $b->guid = $this->guid;
        $b->processed=$this->wrongBetProcessed ?? 0;
        $b->game = $bet->game;
        $b->type = $bet->type; //normal, free game, bonus game, double
        $b->come = $bet->come;
        $b->result = $bet->result;
        $b->office_id = $bet->office_id;
        $b->win = $bet->win;
        $b->game_id = $bet->game_id;
        $b->method = $bet->method;
        $b->balance = $bet->balance;//amount + bonus
        $b->poker_bet_id = $poker_bet_id;
        $b->initial_id = !empty($bet->initial_id) ? $bet->initial_id : $poker_bet_id;
        $b->bet_transaction_id = $this->bet_transaction_id;
        $b->win_transaction_id = $this->win_transaction_id;
        $b->is_freespin = $params['is_freespin'];
        $b->created = $bet->created;
        $b->real_amount = $bet->real_amount;
        $b->real_win = $bet->real_win;
        $b->fs_id = $params['last_freespin_id']??null;
        $b->try = 0;
        $b->win_sended = 0;
        $b->save();
    }

    public function processWrongBets($user_id) {
        $bets = db::query(1,'select * from wrongbetsvertbet where user_id=:u_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->execute(null,'Model_WrongbetVertbet');


        if(count($bets)) {

            $u = new Model_User($user_id);

            if(!$u->loaded()) {
                throw new Exception('user not found!');
            }

            $office = $u->office;
            $currency=$u->office->currency;
            $mult = $currency->mult??2;

            dbredis::instance()->select(0);
            $session_token=auth::getCustomSessionId($u->id,$u->api_session_id);

            if(!$session_token) {
                return false;// todo what to do?
            }


            $errors=false; //todo check it. didnt test

            $errors=true;

            foreach($bets as $bet) {
                $can_cancel_bet=false;

                if($mult==0) {
                    $amount = "".floor($bet->amount);
                    $win = "".floor($bet->win);
                }
                else {
                    $amount = "".floor($bet->amount*pow(10,$mult));
                    $win = "".floor($bet->win*pow(10,$mult));
                }


                //bet
                $data=[
                    "session_token" => $session_token,
                    "player_id" => $u->external_id,
                    "game_id" => $bet->game,
                    "transaction_id" => "".$bet->win_transaction_id,
                    "round_id" => "".$bet->bet_id,
                    "round_closed" => true,
                    "amount" => $amount,
                    "currency" => $currency->code
                ];

                if($bet->game_type=='jp') {
                    $data['amount'] = $win;
                    $r = $this->_send_win_request($this->_url, $data, $office);
                    if ($r) {
                        $bet->processed = 1;
                        $bet->win_sended = 1;

                        $betArr = $bet->as_array();
                        $betArr['game_type']='jp';
                        $betArr['game_name']='jp';
                        $betArr['can_jp']=false;
                        $betArr['send_api']=false;
                        auth::$user_id=$user_id;
                        bet::make($betArr,$betArr['type'],[],true,false);
                    } else {
                        $bet->try++;
                    }
                }
                else {
                    if (!empty($bet->win_transaction_id) && $bet->win_sended == 0 && $bet->win > 0) {
                        $data['amount'] = $win;
                        $r = $this->_send_cancel_request($this->_url, $data, $office);

                        if ($r) {
                            $bet->win_sended = 1;
                            $can_cancel_bet = true;
                        } else {
                            $bet->try++;
                        }
                    } else {
                        $can_cancel_bet = true;
                    }


                    if ($can_cancel_bet) {
                        $data['transaction_id'] = $bet->bet_transaction_id;
                        $data['amount'] = $amount;
                        $r = $this->_send_cancel_request($this->_url, $data, $office);
                        if ($r) {
                            $bet->processed = 1;
                        } else {
                            $bet->try++;
                        }
                    }
                }
                $bet->save();
            }

            return $errors;
        }
        return false;
    }

}


