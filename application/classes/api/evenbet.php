<?php

class Api_Evenbet extends gameapi
{

    public $key, $guid, $platform, $gameName, $session_token;
    protected $_url = '';
    private $_token = '';
    private $_secretkey = '';
    protected $_wrongBetType='';
    public $bet_transaction_id;
    public $win_transaction_id;

    protected $_is_test=false;

    private $_access=[
//        'test'=>['https://site-domain.local/apievenbet/testlaunch','agtintegrationkey'],
        'test'=>['https://api.legionpoker.com/api/web/casino/providers/agt/','agtintegrationkey'],
        'prod'=>['https://api.keopex.com/api/web/casino/providers/agt/','TWNKFodzMNLyLbfAja'],
    ];

    //https://apievenbet.site-domain.com/apievenbet/opengame?token=29fbd9c47a3c471cb2d8c6bbf1eea613&gameId=extraspin3&mode=0&language=en&platform=desktop&currency=USD&operator_id=legionpoker&callback_URL=http%3A%2F%2Fapi.keopex.com%2Fapi%2Fweb%2Fcasino%2Fproviders%2Fagt%2F

    public function forceURL($url) {
        if(!empty($url)) {
            $this->_url=$url;
        }
    }
    public function setUpEnv($test=false) {

        $this->_is_test=$test;

        if($test) {
            list($this->_url,$this->_secretkey)=$this->_access['test'];
            return $this->_url;
        }

        list($this->_url,$this->_secretkey)=$this->_access['prod'];

        return $this->_url;
    }

    protected function _send_headers($response)
    {
        return [
            'Content-Type: application/json',
            'Authorization: ' . $this->hash($response),
        ];
    }

    private function hash($request) {

        return hash("sha256", $request . $this->_secretkey);
    }

    public function login() {

        $data['token']=$this->session_token;

        $jsonRequest=json_encode($data);

        logfile::create(date('Y-m-d H:i:s') . ' request login for token <'.$this->session_token.'>: '
            . PHP_EOL . $this->_url
            . PHP_EOL . json_encode($data, 1).PHP_EOL.print_r($this->_send_headers($jsonRequest),1), 'evenbet');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $loginRequest = $parser->post($this->_url . '/login', $data, true, $this->_send_headers($jsonRequest));

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' response login for token <'.$this->session_token.'>: time: |' .
            $response_time . '| response login: ' . $this->_url . PHP_EOL . $loginRequest, 'evenbet');

        if (!$loginRequest) {
            $this->last_error = 'bad login response';
            return false;
        }
        $json = json_decode($loginRequest, 1);

        if (!$json) {
            return false;
        }

        if (isset($json['error'])) {
            return false;
        }

        $this->session_token=$json['token'];

        return $json;

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

    public function checkUser($auth_data, $office_id)
    {
        $u = new Model_User(['office_id' => $office_id, 'external_id' => $auth_data['nickname'], 'api' => 7]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

        $user_balance=bcdiv("".$auth_data['balance'],100,2);

        $u->amount="".$user_balance;

        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

    public function checkOffice($currency,$operator_id)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        $external_name='evenbet'.$currency.$operator_id;

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Can't create office with currency $currency ");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'apitype'=>7,
            'is_test' => (int) $this->_is_test
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }

        return $o->id;
    }

    public function createUser($auth_data, $office_id)
    {

        $user_balance=bcdiv("".$auth_data['balance'],100,2);

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $auth_data['nickname'] . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 7;
        $u->amount="".$user_balance;
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->visible_name = $auth_data['nickname'];
        $u->external_id = $auth_data['nickname'];
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createOffice($currency,$operator_id)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o = new Model_Office;

        $external_name='evenbet'.$currency.$operator_id;

        $o->currency_id = $currency->id;
        $o->visible_name = "Evenbet " . ($this->_is_test ? '[TEST]' : '') . " {$currency->code}{$operator_id}";
        $o->external_name = $external_name;

        $o->apienable = 1;
        $o->apitype = 7;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;

        $o->gameapiurl = $this->_url;

        $o->bonus_diff_last_bet = 8;
        $o->enable_bia = time();
        $o->rtp = 96;
        $o->owner = 1090;

        $o->dentabs = $currency->default_den;
        $o->default_dentab = $currency->default_dentab;
        $o->k_to_jp = 0.005;
        $o->k_max_lvl = $currency->default_k_max_lvl;
        $o->enable_jp = 1;

        $o->games_rtp = 97;
        $o->gameui = 1;

        $o->is_test = (int) $this->_is_test;
        $o->seamlesstype = 1;
        $o->enable_moon_dispatch = 1;

        $o->secretkey = $this->_token;

        database::instance()->begin();

        //TODO поификсить создание джекпотаов

        try {
            //создаем игры здесь
            $o->need_create_default_games = false;
            $o->save()->reload();


            database::instance()->direct_query('insert into person_offices (person_id,office_id)
                                        values (' . $o->owner . ',' . $o->id . ')');


            $sql_games = <<<SQL
                insert into office_games(office_id, game_id, enable)
                Select :office_id, g.id, 1
                From games g
                Where g.provider = 'our' and g.branded=0 and brand ='agt' and show=1 and g.category!='coming'
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

    public function checkBalance($login=null, $office_id=null, $url=null, $sess_id = NULL)
    {

        $parser = new Parser();
        $parser->disableFailOnError();

        $data=[];
        $data['token']=$this->session_token;

        $jsonRequest=json_encode($data);

        logfile::create(date('Y-m-d H:i:s') . ' [' . $this->session_token . '] BALANCE request: ' . $this->_url.'balance'
            . PHP_EOL . json_encode($data, 1).PHP_EOL, 'evenbet');

        $start_time = microtime(1);

        $r=$parser->post($this->_url.'balance',$data,true,$this->_send_headers($jsonRequest));

        $response_time = microtime(1) - $start_time;
        logfile::create(date('Y-m-d H:i:s') . ' [' . $this->session_token . '] time: |' . $response_time . '| response: '
            . $this->_url.'balance' . PHP_EOL . $r, 'evenbet');

        if (!$r) {
            $this->last_error = 'bad response';
            return false;
        }

        $jr = json_decode($r, 1);

        if (!$jr) {
            return false;
        }

        if (isset($json['error'])) {
            return false;
        }

        if (!isset($jr['balance']) || (float)$jr['balance'] < 0) {
            return false;
        }

        return $jr['balance'];
    }

    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $cashier_url = false)
    {
        $domain = kohana::$config->load('static.gameapi_domen_evenbet');

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
        $value=$r->incr('evenbetCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('evenbetCommandId',time()*100+$ms);
            $value=$r->incr('evenbetCommandId');
        }

        return $value;


    }

    public function isNeedSend($url)
    {
        return true;
    }

    protected function _check_errors() {

    }

    protected function _send_bet_request($url,$data,$office)
    {
        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundId'] . '] request bet: ' . $url
            . PHP_EOL . json_encode($data, 1), 'evenbet');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $betRequest = $parser->post($url . '/debit', $data, true, $this->_send_headers(json_encode($data)));

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundId'] . '] time: |' . $response_time . '| response bet: ' . $url . PHP_EOL . $betRequest, 'evenbet');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundId'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url, 'evenbet');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBet).PHP_EOL.Debug::vars($betRequest), 'evenbet');
            return false;
        }

        if (isset($jsonBet['error'])) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[error]', 'evenbet');
            return false;
        }

        return $jsonBet['balance'];
    }

    protected function _send_win_request($url,$data,$office) {
        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] request win: '
            .$url.PHP_EOL.json_encode($data,1),'evenbet');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $winRequest = $parser->post($url.'/credit',$data,true,$this->_send_headers(json_encode($data)));

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] time: |'.$response_time.'| response win: '.$url.PHP_EOL.$winRequest,'evenbet');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundId'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url, 'evenbet');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (isset($jsonWin['error'])) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'evenbet');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_cancel_request($url,$data,$office) {

        $data['refTransactionId']=$data['transactionId'];
        $data['transactionId']="".$this->commandId();

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] request cancel: '.$url
            .PHP_EOL.json_encode($data,1),'evenbet');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $cancelRequest = $parser->post($url.'/rollback',$data,true,$this->_send_headers(json_encode($data)));

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] time: |'.$response_time.'| response cancel: '.$url.PHP_EOL.$cancelRequest,'evenbet');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundId'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $url, 'evenbet');
            return false;
        }

        $jsonCancel=json_decode($cancelRequest,1);

        if(!$jsonCancel) {
            return false;
        }

        if (isset($jsonCancel['error'])) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel[error]', 'evenbet');
            return false;
        }

        return $jsonCancel['transactionId'];
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

        $fin=($params['win']==0);
        $round_num=$bet_id;

        $need_send_betrequest=true;

        if(isset($params['poker_bet_id']) && $params['poker_bet_id']>0) {
            $round_num=$params['poker_bet_id'];
            $need_send_betrequest=false;
        }
        elseif(isset($params['initial_id']) && $params['initial_id']>0) {
            $round_num=$params['initial_id'];
            $need_send_betrequest=false;
        }
        elseif(th::isMoonGame($params['game']) && ($params['win']==0)) {
            $fin=false;
        }
        elseif(in_array($params['game'],['acesandfaces','jacksorbetter','tensorbetter']) && $params['bettype']=='normal') {
            $fin=false;
        }

        $this->round_num=$round_num;
        $this->fin=(int) $fin;

        $transaction_id=$this->commandId();


        //bet
        $data=[
            "token" => $session_token,
            "gameId" => $params['game'],
            "endRound" => $fin,
            "roundId" => "".$round_num,
            "transactionId" => "".$transaction_id,
            "amount" => (float) $amount,
        ];

        $this->setUpEnv($u->office->is_test);

        if($need_send_betrequest) {

            if($params['game']=='jp') {
                $data['gameId']=bet::getJP()->game;
            }

            $betRequest = $this->_send_bet_request($url,$data,$u->office);
            $this->bet_transaction_id=$data['transactionId'];

            if($betRequest===false) {
                if(!$this->_send_cancel_request($url,$data,$u->office)) {
                    //move to wronbets as CANCEL
                    $this->_wrongBetType='cancel';
                    th::sendCurlError('evenbet');
                    return false;
                }
                th::sendCurlError('evenbet');
                throw new Exception_ApiResponse('bet rejected.');

                $this->_wrongBetType='bet';
                return false;
            }

            $u->amount = bcdiv("".$betRequest,pow(10,$mult),$mult);

        }

        //send win
        if($win>0 || !$need_send_betrequest) {


            if($params['game']=='jp') {
                $data['gameId']=bet::getJP()->game;
            }

            $data['transactionId']="".$this->commandId();
            $data['endRound']=true;
            $data['amount']=(float) $win;

            $this->win_transaction_id=$data['transactionId'];

            $winRequest = $this->_send_win_request($url,$data,$u->office);

            if(!$winRequest) {
                //try to send win again
                $winRequest = $this->_send_win_request($url,$data,$u->office);
                if(!$winRequest) {
                    if(!$this->_send_cancel_request($url,$data,$u->office)) {
                        //move to wrongbets as CANCEL
                        $this->_wrongBetType='cancel';
                        th::sendCurlError('evenbet');
                        return false;
                    }
                    else {
                        $this->_wrongBetType='win';
                        //move to wrongbets as WIN
                        th::sendCurlError('evenbet');
                        return false;
                    }
                }
            }

            $u->amount = bcdiv("".$winRequest,pow(10,$mult),$mult);
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

        $b = new Model_WrongbetEvenbet();
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

        $bets = db::query(1,'select * from wrongbetsevenbet where user_id=:u_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->execute(null,'Model_WrongbetEvenbet');


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
                    $amount = (float) floor($bet->amount);
                    $win = (float) floor($bet->win);
                }
                else {
                    $amount = (float) floor($bet->amount*pow(10,$mult));
                    $win = (float) floor($bet->win*pow(10,$mult));
                }


                //bet
                $data=[
                    "token" => $session_token,
                    "gameId" => $bet->game,
                    "endRound" => true,
                    "roundId" => "".$bet->bet_id,
                    "transactionId" => "".$bet->win_transaction_id,
                    "amount" => $amount,
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
                        $data['transactionId'] = $bet->bet_transaction_id;
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


