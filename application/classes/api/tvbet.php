<?php

class Api_Tvbet extends gameapi
{

    public $key, $guid, $platform, $gameName, $session_token;
    protected $_url = '';
    private $_token = '';
    private $_secretkey = '';
	private $_counterparty = '';
    protected $_wrongBetType='';
    public $last_error_code=0;
    public $bet_transaction_id;
    public $win_transaction_id;

    protected $_is_test=false;

    private $_access=[

        'test'=>['https://backend.bprod.net/betongames-mediator-service/api/v1/','DteDnfKIkZMBV7aX9csmk4e5ZvBlEkcqAohwf+W18usyXc2jjvk1YkcjLgrDeazT','216'],
        'prod'=>['https://backend.bcdprod.net/betongames-mediator-service/api/v1/','RdqcgGgkSBs+j7OuZUMaFd0CJH+5RrHBQUfeWfc8j66UjixmFo9CcJN+SHxgzfghZnhKoxnVfmGAfusd+78lbw==','7ea95cf8-79a2-4a32-a3cd-ff82ec4fcab2'],

        //'test'=>['https://site-domain.local/apitvbet/testlaunch/','DteDnfKIkZMBV7aX9csmk4e5ZvBlEkcqAohwf+W18usyXc2jjvk1YkcjLgrDeazT'],
        //'prod'=>['https://site-domain.local/apitvbet/testlaunch/','DteDnfKIkZMBV7aX9csmk4e5ZvBlEkcqAohwf+W18usyXc2jjvk1YkcjLgrDeazT'],
    ];


    public function setUpEnv($test=false) {

        $this->_is_test=$test;

        if($test) {
            list($this->_url,$this->_secretkey,$this->_counterparty)=$this->_access['test'];
            return $this->_url;
        }

        list($this->_url,$this->_secretkey,$this->_counterparty)=$this->_access['prod'];

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

    public function getPlayerInfo($token)
    {

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $time=date('d-m-Y H:i:s');

        $data=[
            'time'=>$time,
            'data'=>[
                'token'=>$token
            ],
        ];
        $data['hash']=$this->sign('GetPlayerInfo',$data);


        logfile::create(date('Y-m-d H:i:s') . ' [' . $token . '] GetPlayerInfo request: ' . $this->_url.'/GetPlayerInfo'
            . PHP_EOL . json_encode($data,JSON_PRESERVE_ZERO_FRACTION).PHP_EOL, 'betconstruct');


        $start_time = microtime(1);

        $r=$parser->post($this->_url.'/GetPlayerInfo',$data,true,['Content-Type: application/json']);

        $response_time = microtime(1) - $start_time;
        logfile::create(date('Y-m-d H:i:s') . ' [' . $token . '] time: |' . $response_time . '| response: '
            . $this->_url.'/GetPlayerInfo' . PHP_EOL . $r, 'betconstruct');


        if (!$r) {
            $this->last_error = 'bad response';
            return false;
        }

        $jr = json_decode($r, 1);

        if (!$jr) {
            return false;
        }

        if ($jr['err_code']) {
            logfile::create(date('Y-m-d H:i:s') . '$jr[error]checkuser', 'betconstruct');
            return false;
        }

        if (!isset($jr['totalBalance']) || (float)$jr['totalBalance'] < 0) {
            return false;
        }

        return $jr;
    }

    public function checkUser($userId,$office_id){

        $u = new Model_User(['office_id' => $office_id, 'external_id' => $userId, 'api' => 11]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

        $o=$u->office;

        $user_balance=$this->checkBalance($userId,$office_id,$o->gameapiurl);

		if($user_balance===false) {
            return false;
        }

        $u->amount="".$user_balance;

        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

    public function checkOffice($currency,$moonParams=[])
    {
        $external_name='tvbet'.$currency;

        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Can't create office with currency $currency");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'is_test' => (int) $this->_is_test,
            'apitype'=>11
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }

		$val=$currency->val;

        $changes=false;

        if(!empty($val) && $val>=0.0001) {
            $changes=true;

            $o->bet_min = 0.01/$val;
            $o->bet_max = 150/$val;
        }

        if(!empty($moonParams)) {
            $o->moon_min_bet=arr::get($moonParams,'minBet');
            $o->moon_max_bet=arr::get($moonParams,'maxBet');

            if($o->bet_min>$o->moon_min_bet) {
                $o->bet_min=$o->moon_min_bet;
            }

            if($o->bet_max<$o->moon_max_bet) {
                $o->bet_max=$o->moon_max_bet;
            }

            $changes=true;
        }

        if($changes) {
            $o->save();
        }
     

        return $o->id;
    }

    public function createUser($userId,$office_id)
    {

        $o=office::instance($office_id)->office();

        $user_balance=$this->checkBalance($userId,$office_id,$o->gameapiurl);

		if($user_balance===false) {
            throw new Exception('external user not found');
        }

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $userId . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 11;
        $u->amount="".$user_balance;
        $u->test = (int) $this->_is_test;
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->visible_name = $userId;
        $u->external_id = $userId;
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createOffice($currency,$moonParams=[])
    {
        $external_name='tvbet'.$currency;

        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency");
        }

        $o = new Model_Office;


        $o->currency_id = $currency->id;
        $o->visible_name = "TVBET " . ($this->_is_test ? '[TEST]' : '') . " {$currency->code}";
        $o->external_name = $external_name;

        $o->apienable = 1;
        $o->apitype = 11;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;

        $o->gameapiurl = $this->_url;
		
		$o->strict_double = 1;

        $o->bonus_diff_last_bet = 8;
        $o->enable_bia = time();
		$o->enable_bia = 0;
        $o->rtp = 96;
        $o->owner = 1156; //todo
		
		if(!empty($moonParams)) {
            $o->moon_min_bet=arr::get($moonParams,'minBet');
            $o->moon_max_bet=arr::get($moonParams,'maxBet');
        }

        $o->dentabs = $currency->default_den;
        $o->default_dentab = $currency->default_dentab;
        $o->k_to_jp = 0.005;
        $o->k_max_lvl = $currency->default_k_max_lvl;
        $o->enable_jp = 1;
		
		$o->promopanel=0;
		
		//15.04.2025
        $o->enable_jp = 0;

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
                Where (g.provider = 'our' and brand ='agt' and show=1 and tvbet_show=1 and g.category!='coming' 
                and g.branded=0) or g.name=:aerobet
SQL;

            db::query(Database::INSERT, $sql_games)
                ->param(':office_id', $o->id)
                ->param(':aerobet', 'aerobet')
                ->execute();

            //$o->createProgressiveEventForOffice();

            $redis = dbredis::instance();
            $redis->select(1);
            $redis->set('jpa-' . $o->id, $o->enable_jp);

            for ($i = 1; $i <= 4; $i++) {

                $redis->set('jpHotPercent-' . $o->id . '-' . ($i), 0.02);

                $j = new Model_Jackpot();
                $j->office_id = $o->id;
                $j->type = $i;
                $j->active = $o->enable_jp;

                $j->save();
            }

        } catch (Exception $ex) {
            database::instance()->rollback();
            throw $ex;
        }

        database::instance()->commit();

        return $o->id;

    }

    public function checkBalance($login = NULL,$office_id = NULL,$url = NULL,$sess_id = NULL)
    {

        $parser = new Parser('tvbet');

        $parser->disableFailOnError();

        $balanceURL=$url.'sessions/'.$this->session_token.'/balance';

        $headers=[
            'X-Counterparty-Id'=>$this->_counterparty,
            'X-Timestamp'=>time()*1000,
            'X-Request-Id'=>guid::create(true),
        ];

        logfile::create(date('Y-m-d H:i:s').' ['.$login.']['.$office_id.']['.$balanceURL.'] request balance: '.json_encode($headers,1),'tvbet');

        $start_time = microtime(1);

        $headers['X-Sign']=$this->sign($headers);

        $http_headers=['Content-type: text/plain'];
        foreach($headers as $hk=>$hv) {
            $http_headers[]=$hk.': '.$hv;
        }

        $balanceRequest = $parser->get($balanceURL,$http_headers);

        $response_time = microtime(1)-$start_time;

        if(!$balanceRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s').' ['.$login.']['.$office_id.']['.$balanceURL.'] response balance time: |'.$response_time.'| ERROR response: <'.$parser->error.'> ','tvbet');
            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$login.']['.$office_id.']['.$balanceURL.'] response balance: '.PHP_EOL.$balanceRequest,'tvbet');

        $jr = json_decode($balanceRequest,1);

        if(!$jr) {
            return false;
        }

        if(!isset($jr['_responseCode']) || $jr['_responseCode']!='1') {
            return false;
        }

        if(!isset($jr['balance']) || (float) $jr['balance'] < 0) {
            return false;
        }

        return $jr['balance'];
    }

    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $cashier_url = false)
    {
        $domain = kohana::$config->load('static.gameapi_domen_tvbet');

        $g = new Model_Game(['name' => $this->gameName]);

        $link = $g->get_link($domain);

        $user = new Model_User($user_id);

        $user->api_key = guid::create(true);
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
        $value=$r->incr('tvbetCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('tvbetCommandId',time()*100+$ms);
            $value=$r->incr('tvbetCommandId');
        }

        return $value;


    }

    public function isNeedSend($url)
    {
        return true;
    }

    protected function _check_errors() {

    }

    protected function _send_bet_request($url,$data,Model_User $user)
    {

        $parser = new Parser('tvbet');
        $parser->disableFailOnError();

        $betURL=$url.'sessions/'.$this->session_token.'/bets/pay-in';

        $headers=[
            'X-Counterparty-Id'=>$this->_counterparty,
            'X-Timestamp'=>time()*1000,
            'X-Request-Id'=>guid::create(true),
        ];

        $start_time = microtime(1);

        $s=array_values($headers);
        array_unshift($s,json_encode($data));

        $headers['X-Sign']=$this->sign($s);

        $http_headers=['Content-type: application/json'];
        foreach($headers as $hk=>$hv) {
            $http_headers[]=$hk.': '.$hv;
        }

        $office=$user->office;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] request bet: '.PHP_EOL.print_r($http_headers,1).PHP_EOL
            .$betURL.PHP_EOL.json_encode($data),'tvbet');

        $betRequest = $parser->post($betURL, $data, true,$http_headers);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| response bet: ' . $betURL . PHP_EOL . $betRequest, 'tvbet');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $betRequest, 'tvbet');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBet).PHP_EOL.Debug::vars($betRequest), 'tvbet');
            return false;
        }

        if (!$jsonBet['_responseCode'] || $jsonBet['_responseCode']!=1) {
            $this->last_error_code=$jsonBet['_responseCode'];
            $this->last_error=$jsonBet['_responseCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[error]', 'tvbet');
            return false;
        }

        return $jsonBet['balance'];
    }

    protected function _send_win_request($url,$data,Model_User $user) {
        $parser = new Parser('tvbet');
        $parser->disableFailOnError();

        $winURL=$url.'sessions/'.$this->session_token.'/bets/pay-out';

        $headers=[
            'X-Counterparty-Id'=>$this->_counterparty,
            'X-Timestamp'=>time()*1000,
            'X-Request-Id'=>guid::create(true),
        ];

        $start_time = microtime(1);

        $s=array_values($headers);
        array_unshift($s,json_encode($data));

        $headers['X-Sign']=$this->sign($s);

        $http_headers=['Content-type: application/json'];
        foreach($headers as $hk=>$hv) {
            $http_headers[]=$hk.': '.$hv;
        }

        $office=$user->office;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] request win: '.PHP_EOL.print_r($http_headers,1).PHP_EOL
            .$winURL.PHP_EOL.json_encode($data),'tvbet');

        $winRequest = $parser->post($winURL, $data, true,$http_headers);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| response win: ' . $winURL . PHP_EOL . $winRequest, 'tvbet');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $winRequest, 'tvbet');
            return false;
        }
        $jsonWin = json_decode($winRequest, 1);

        if (!$jsonWin) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonWin).PHP_EOL.Debug::vars($winRequest), 'tvbet');
            return false;
        }

        if (!$jsonWin['_responseCode'] || $jsonWin['_responseCode']!=1) {
            $this->last_error_code=$jsonWin['_responseCode'];
            $this->last_error=$jsonWin['_responseCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'tvbet');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_betwin_request($url,$data,Model_User $user) {

        $parser = new Parser('tvbet');
        $parser->disableFailOnError();

        $betWinURL=$url.'sessions/'.$this->session_token.'/bets/pay-in-out';

        $headers=[
            'X-Counterparty-Id'=>$this->_counterparty,
            'X-Timestamp'=>time()*1000,
            'X-Request-Id'=>guid::create(true),
        ];

        $start_time = microtime(1);

        $s=array_values($headers);
        array_unshift($s,json_encode($data));

        $headers['X-Sign']=$this->sign($s);

        $http_headers=['Content-type: application/json'];
        foreach($headers as $hk=>$hv) {
            $http_headers[]=$hk.': '.$hv;
        }

        $office=$user->office;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] request betwin: '.PHP_EOL.print_r($http_headers,1).PHP_EOL
            .$betWinURL.PHP_EOL.json_encode($data),'tvbet');

        $betWinRequest = $parser->post($betWinURL, $data, true,$http_headers);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| response betwin: ' . $betWinURL . PHP_EOL . $betWinRequest, 'tvbet');

        if (!$betWinRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| ERROR response betwin: <' . $parser->error . '> ' . $betWinRequest, 'tvbet');
            return false;
        }
        $jsonBetWin = json_decode($betWinRequest, 1);

        if (!$jsonBetWin) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBetWin).PHP_EOL.Debug::vars($jsonBetWin), 'tvbet');
            return false;
        }

        if (!$jsonBetWin['_responseCode'] || $jsonBetWin['_responseCode']!=1) {
            $this->last_error_code=$jsonBetWin['_responseCode'];
            $this->last_error=$jsonBetWin['_responseCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonBetWin[error]', 'tvbet');
            return false;
        }

        return $jsonBetWin['balance'];
    }

    protected function _send_cancel_request($url,$data,Model_User $user) {

        $parser = new Parser('tvbet');
        $parser->disableFailOnError();

        $cancelURL=$url.'sessions/'.$this->session_token.'/bets/refund';

        $data=[
            'betId'=>$data['betId']
        ];

        $headers=[
            'X-Counterparty-Id'=>$this->_counterparty,
            'X-Timestamp'=>time()*1000,
            'X-Request-Id'=>guid::create(true),
        ];

        $start_time = microtime(1);

        $s=array_values($headers);
        array_unshift($s,json_encode($data));

        $headers['X-Sign']=$this->sign($s);

        $http_headers=['Content-type: application/json'];
        foreach($headers as $hk=>$hv) {
            $http_headers[]=$hk.': '.$hv;
        }

        $office=$user->office;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['betId'].'] request cancel: '.PHP_EOL.print_r($http_headers,1).PHP_EOL
            .$cancelURL.PHP_EOL.json_encode($data),'tvbet');

        $cancelRequest = $parser->post($cancelURL, $data, true,$http_headers);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['betId'] . '] time: |' . $response_time . '| response cancel: ' . $cancelURL . PHP_EOL . $cancelRequest, 'tvbet');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['betId'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $cancelRequest, 'tvbet');
            return false;
        }
        $jsonCancel = json_decode($cancelRequest, 1);

        if (!$jsonCancel) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel'.PHP_EOL.Debug::vars($jsonCancel), 'tvbet');
            return false;
        }

        if (!$jsonCancel['_responseCode'] || $jsonCancel['_responseCode']!=1) {
            $this->last_error_code=$jsonCancel['_responseCode'];
            $this->last_error=$jsonCancel['_responseCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel[error]', 'tvbet');
            return false;
        }

        return $jsonCancel['balance'];
    }

    public function bet($login,$url, $params=[],$is_repeat=false) {

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            throw Exception('user not found');
        }

        dbredis::instance()->select(0);
        $session_token=$this->session_token=auth::getCustomSessionId($u->id,$u->api_key);


        if(!$session_token){
            throw new Exception('session expired');
        }

        $o=$u->office;

        $mult=$o->currency->mult ?? 2;

        $amount = "".$params['amount'];
        $win = "".$params['win'];
        $bet_id = $params['bet_id'];

        $fin=true;
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
        $this->request_id=$transaction_id;

        //bet
        $data=[
            "betAmount" => $amount,
            "betId" => guid::v3('betId',$round_num),
            "gameRoundId" => guid::v3('round',$round_num),
            "sessionId" => $session_token,
            "winAmount" => $win,
        ];
		
		

		logfile::create(date('Y-m-d H:i:s') . 'START BET/WIN/CANCEL: '.$round_num, 'tvbet');

        $this->setUpEnv($u->office->is_test);
		
		if($this->_is_test) {
            $data['betId']="".$round_num;
        }

        $url=$this->_url;

        $request_type='betwin';

        if($need_send_betrequest && $fin) {

        }
        //bet
        elseif($need_send_betrequest) {
            $request_type='bet';
            unset($data['winAmount']);
        }
        //win
        else {
            $request_type='win';
            unset( $data['betAmount']);
        }

		if($params['game']=='aerobet' && $request_type=='win') {
            $data['winOdd']=$params['result'];
            if($params['result']==0) {
                $request_type='cancel';
            }
        }

        if($params['bettype']=='jp') {
            unset($data['betAmount']);
            $request_type='jp';
            $request_type='win';
        }

        if($request_type=='bet' || $request_type=='betwin') {

            $method='_send_'.$request_type.'_request';

            $new_balance = $this->$method($url, $data, $u);

            if ($new_balance === false && !th::isMoonGame($params['game_id'])) {

                if($this->last_error_code>0 && $this->last_error_code<=8) {
                    throw new Exception_ApiResponse('bet rejected.');
                }

                //repeat
                logfile::create(date('Y-m-d H:i:s') . 'REPEAT START: '.print_r($data,1), 'tvbet');
                $new_balance = $this->$method($url, $data, $u);
                logfile::create(date('Y-m-d H:i:s') . 'REPEAT END: '.Debug::vars($new_balance), 'tvbet');
                if ($new_balance === false) {

                    //второй раз не прошел, смотрим код ошибки.

                    if($this->last_error_code==104) {
                        //все таки прошла, успех
                    }
                    else {
                        $can_cancel=false;
                        if($request_type=='bet') {
                            $can_cancel=true;
                        }
                        if($request_type=='betwin') {
                            $can_cancel=true;
                        }

                        if(!$this->last_error_code || $this->last_error_code==99) {
                            if ($can_cancel && !$this->_send_cancel_request($url, $data, $u)) {
                                $this->_wrongBetType = 'cancel';
                                th::sendCurlError('tvbet');
                                return false;
                            }
                        }

                        throw new Exception_ApiResponse('bet rejected.');

                        $this->_wrongBetType = 'repeat';
                        th::sendCurlError('tvbet');
                        return false;
                    }
                }
            }
            elseif($new_balance === false && th::isMoonGame($params['game_id'])) {
                $can_cancel=false;
                if($request_type=='bet') {
                    $can_cancel=true;
                }
                if($request_type=='betwin') {
                    $can_cancel=true;
                }

                if(!$this->last_error_code) {
                    if ($can_cancel && !$this->_send_cancel_request($url, $data, $u)) {
                        $this->_wrongBetType = 'cancel';
                        th::sendCurlError('tvbet');
                        return false;
                    }
                }

                throw new Exception_ApiResponse('bet rejected.');
            }
        }
        else {

            $request_type='_send_'.$request_type.'_request';

            $new_balance = $this->$request_type($url,$data,$u);

            if($new_balance===false) {
                //repeat
                $new_balance = $this->$request_type($url,$data,$u);
                if($new_balance==false) {
                    $this->_wrongBetType = 'win';
                    return false;
                }
            }
        }

        $u->amount = $new_balance;

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

        $b = new Model_WrongbetTvbet();
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
        $b->round_num = $this->round_num;
        $b->request_id = $this->request_id;
        $b->fin = $this->fin;
        $b->save();
    }

    public function processWrongBets($user_id) {

        $bets = db::query(1,'select * from wrongbetstvbet where user_id=:u_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->execute(null,'Model_WrongbetTvbet');


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

                $amount = $bet->amount;
                $win = $bet->win;

                $bet_id = $bet->bet_id;

                $fin=true;
                $round_num=$bet_id;

                $need_send_betrequest=true;

                if(!empty($bet->poker_bet_id) && $bet->poker_bet_id>0) {
                    $round_num=$bet->poker_bet_id;
                    $need_send_betrequest=false;
                }
                elseif(isset($bet->initial_id) && $bet->initial_id>0) {
                    $round_num=$bet->initial_id;
                    $need_send_betrequest=false;
                }
                elseif(th::isMoonGame($bet->game) && ($win==0)) {
                    $fin=false;
                }
                elseif(in_array($bet->game,['acesandfaces','jacksorbetter','tensorbetter']) && $bet->type=='normal') {
                    $fin=false;
                }


                $this->round_num=$round_num;
                $this->fin=(int) $fin;

                $transaction_id=$this->commandId();
                $this->request_id=$transaction_id;

                //bet
                $data=[
                    "betAmount" => $amount,
                    "betId" => guid::v3('betId',$round_num),
                    "gameRoundId" => guid::v3('round',$round_num),
                    "sessionId" => $session_token,
                    "winAmount" => $win,
                ];

                $this->setUpEnv($u->office->is_test);
				
				if($this->_is_test) {
                    $data['betId']="".$round_num;
                }

                $url=$this->_url;

                $request_type='betwin';

                if($need_send_betrequest && $fin) {

                }
                //bet
                elseif($need_send_betrequest) {
                    $request_type='bet';
                    unset( $data['winAmount']);
                }
                //win
                else {
                    $request_type='win';

                    unset( $data['betAmount']);
                }

                $is_canceled=false;

                //если была ставка-отменяем

                if($request_type=='betwin' || $request_type=='bet') {

                    $data['betId']=$bet->bet_id;

                    $cancel_request = $this->_send_cancel_request($url,$data,$u);

                    if($cancel_request===false && empty($this->last_error_code)) {
                        $bet->try++;
                    }
                    else {
                        $bet->processed = 1;
                        $is_canceled=true;
                    }

                }
                elseif($request_type=='win') {
                    $new_balance = $this->_send_win_request($url,$data,$u);

                    if($new_balance===false) {
                        $bet->try++;
                    }
                    else {
                        $bet->processed = 1;
                    }
                }

                if($bet->processed && !$is_canceled) {

                    try
                    {
                        $betArr = (array) $bet->as_array();

                        $betArr['game_type']=($bet->game == 'jp')?'jp':'agt';
                        $betArr['game_name']=$betArr['game'];
                        $betArr['can_jp']=false;
                        $betArr['send_api']=false;

                        $info = 'wb; '.date('Y-m-d H:i:s',$bet->created);
                        if(th::isMoonGame($bet->game) && !empty($bet->initial_id)) {
                            $info.='; '.$bet->initial_id;
                        }
                        $betArr['info']=$info;

                        auth::$user_id=$user_id;
                        bet::make($betArr,$betArr['type'],[],true,false);

                        if(th::isMoonGame($betArr['game']) && $betArr['initial_id']>0) {
                            game_moon_agt::updateUserBetHistory($user_id,$betArr);
                        }

                        $errors=false;
                    }
                    catch(Exception $ex)
                    {
                        $errors=true;
                        $bet->try++;
                        $bet->processed=0;
                        //not crushed and not processed. need to fix internal
                        logfile::create(date('Y-m-d H:i:s')." ERROR BET!!!!!: ".$ex->getMessage(). "\n".$ex->getTraceAsString(),'wbbetconstruct');
                    }
                }
                $bet->save();
            }

            return $errors;
        }
        return false;
    }


    public function sign($params=[]) {
        logfile::create(date('Y-m-d H:i:s') . ' signstring: '.implode('.',$params). PHP_EOL.'key: '.$this->_secretkey . PHP_EOL, 'tvbet');
        return hash_hmac('sha256', implode('.',$params),$this->_secretkey);
    }

    public static function checkAndPayFS(Model_User $u,$gameName) {

        $o=$u->office;
        $currencyCode=$o->currency->code;

        $operatorCode=str_replace('bc'.$currencyCode,'',$o->external_name);

        $waitedFS=new Model_BetconstructFreespin(['playerId'=>$u->external_id,'currency'=>$o->currency->code,'operatorCode'=>$operatorCode,'paid'=>0]);

        if($waitedFS->loaded()) {

            if(!in_array($gameName,$waitedFS->gameIds)) {
                return false;
            }

            $waitedFS->user_id=$u->id;
            $waitedFS->office_id=$u->office_id;

            if(strtotime($waitedFS->bonusMoneyValidity)>=time()) {
                $fs=new Model_Freespin();

                $game_ids=array_keys(db::query(1,'select id from games where name in :names')
                    ->param(':names',$waitedFS->gameIds)
                    ->execute()
                    ->as_array('id'));


                $min_bet_limit=$u->office->bet_min ?? 0.1;

                if(th::isMoonGame($gameName) && $u->office_id) {
                    $curr=$u->office->currency;
                    $min_bet_limit=$curr->moon_min_bet ?? $min_bet_limit;
                }

                $waitedFS->paid=time();
                $waitedFS->save();

                $fs->fs_offer_type='betconst';
                $fs->fs_offer_id=$waitedFS->id;

                $fs->giveFreespins($u->id,$u->office_id,$game_ids,$waitedFS->numberOfFreeRounds,$min_bet_limit,0,0,'api',false,null,false,null,strtotime($waitedFS->freeRoundValidity),$waitedFS->id);


            }
            else {
                $waitedFS->paid=-time();
                $waitedFS->save();
            }

            return $waitedFS;
        }

        return false;
    }
}
