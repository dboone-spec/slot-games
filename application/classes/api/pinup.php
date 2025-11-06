<?php

class Api_Pinup extends gameapi
{


    public $key, $guid, $platform, $gameName, $session_token;
    protected $_url = '';
    private $_token = '';
    private $_apitoken = '';
    protected $_wrongBetType='';
    public $bet_transaction_id;
    public $win_transaction_id;
    public $last_error_code;

    protected $_is_test=true;
	protected $_zone;

    private $_access=[
        'com'=>[
            'test'=>['https://agt-dev.slotsintegrationapi.com/agt','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm92aWRlcklkIjoiYWd0IiwidGltZXN0YW1wIjoiMjAyNC0wOS0xNlQxMTowMTozOS41MjY2NTU4NDlaIn0.eqOETlMbvjnyAEh1nC_SxfAE7darhkWtkQpe7fWdOkM'],
            'prod'=>['https://agt.slotsintegrationapi.com/agt','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm92aWRlcklkIjoiYWd0IiwidGltZXN0YW1wIjoiMjAyNC0wOS0yNVQwMjozOTo1My44MzYyNDUxMjNaIn0._tQVepOOUfRA7o2GdpV4nzwTqFjZLzR-tSX27VM66j8'],
        ],
        'ua'=>[
            'test'=>['https://agt-dev.slotsintegrationapi.com/agt','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm92aWRlcklkIjoiYWd0IiwidGltZXN0YW1wIjoiMjAyNC0wNy0xN1QwODozOToxMi43NTEzMjQyMzNaIn0.CDKCo8CLKY_d4sSD1LbeyR9MccOBANJTNUN2-CQ5P_E'],
            'prod'=>['https://agt-dev.slotsintegrationapi.com/agt','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm92aWRlcklkIjoiYWd0IiwidGltZXN0YW1wIjoiMjAyNC0wNy0xN1QwODozOToxMi43NTEzMjQyMzNaIn0.CDKCo8CLKY_d4sSD1LbeyR9MccOBANJTNUN2-CQ5P_E'],
        ],
		'pinco'=>[
            'test'=>['https://agt.devqwaqwaq.com/agt','qZCe]@8yagj!Nr2[.ZbYMMC,'],
            'prod'=>['https://agt.qwaqwaq.com/agt',"Y&hrt3gdE!Gc[2U#;AHX+=mK"],
        ],
		'preprodcom'=>[
            'test'=>['https://agt-dev.slotsintegrationapi.com/agt','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm92aWRlcklkIjoiYWd0IiwidGltZXN0YW1wIjoiMjAyNC0wNS0yOVQxMTowNjo1OC4wNjgzMzg1ODhaIn0.cSWBBVj0_Ac8T7DbO0m5mWwlMoXkm9Ud_R_zZLbT0bQ'],
            'prod'=>['https://agt-dev.slotsintegrationapi.com/agt','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm92aWRlcklkIjoiYWd0IiwidGltZXN0YW1wIjoiMjAyNC0wOC0wNlQxNDo0NzozOS45NTQ0NDc1MzNaIn0.3ui1N4kEbA8fOgRt_Ah5PHAv6QAEtCorxwkPA44I92g'],
        ],
    ];


    public function forceURL($url) {
        if(!empty($url)) {
            $this->_url=$url;
        }
    }
    public function setUpEnv($test=false,$zone='com') {

        $this->_is_test=$test;
		$this->_zone=$zone;

        if(defined('LOCAL') && LOCAL) {
            $controller='apipinup'.$zone;

            if(!$this->_is_test) {
                $controller.='live';
            }

            $this->_access[$zone]['test'][0]='https://site-domain.local/'.$controller.'/partner/pinupagt1000';
            $this->_access[$zone]['prod'][0]='https://site-domain.local/'.$controller.'/partner/pinupagt1000';
        }

        if($this->_is_test) {
            list($this->_url,$this->_apitoken)=$this->_access[$zone]['test'];
            return $this->_url;
        }

        list($this->_url,$this->_apitoken)=$this->_access[$zone]['prod'];

        return $this->_url;
    }

    protected function _send_headers($response)
    {
        return [
            'Content-Type: application/json',
//            'Authorization: ' . $this->_apitoken,
        ];
    }

    public function sessionRefresh($playerId,$token,$game_id) {


        $data['changeToken']=false;
        $data['tokenLifeTime']=Game_Session::$session_time-30*Date::MINUTE;
        $data['sessionId']=$token;
        $data['playerId']="".$playerId;

        $jsonRequest=json_encode($data);

        $URL=$this->_url.'/session?token='.urlencode($this->_apitoken);

        logfile::create(date('Y-m-d H:i:s') . ' request session refresh: '
            . PHP_EOL . $URL
            . PHP_EOL . $jsonRequest.PHP_EOL, 'pinup');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $loginRequest = $parser->post($URL, $data, true, $this->_send_headers($jsonRequest));

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' response session refresh: time: |' .
            $response_time . '| response session refresh: ' . $URL . PHP_EOL . $loginRequest, 'pinup');

        if (!$loginRequest) {
            $this->last_error = 'bad session refresh response';
            return false;
        }
        $json = json_decode($loginRequest, 1);

        if (!$json) {
            return false;
        }

        if ((int) $json['errCode']!=0) {
            return false;
        }

        return $this->getSession($this->_apitoken,$playerId,$game_id);
    }

    public function getSession($sessionId,$playerId,$game_id) {

        $data['token']=$this->_apitoken;
        $data['sessionId']=$sessionId;
        $data['playerId']=$playerId;
        $data['gameId']=$game_id;


        logfile::create(date('Y-m-d H:i:s') . ' request session <'.$sessionId.'>: '
            . PHP_EOL . $this->_url . '/session?'.http_build_query($data)
            . PHP_EOL . json_encode($data, 1).PHP_EOL, 'pinup');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $loginRequest = $parser->get($this->_url . '/session?'.http_build_query($data), $data, true);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' response session <'.$sessionId.'>: time: |' .
            $response_time . '| ' . $this->_url.'/session?'.http_build_query($data) . PHP_EOL . $loginRequest, 'pinup');

        if (!$loginRequest) {
            $this->last_error = 'bad login response';
            return false;
        }
        $json = json_decode($loginRequest, 1);

        if (!$json) {
            return false;
        }

		if($json['errCode']==17) {
            $p=new Parser();

            $tokenURL=parse_url($this->_url);
            $tokenURL='https://'.$tokenURL['host'].'/provider/auth/token';

            $r=$p->post($tokenURL,['providerId'=>'agt'],true,['Content-Type: application/json',]);
            
            th::critAlert('new token for pinup '.$this->_url.' ['.$this->_zone.']'.($this->_is_test?'[TEST]':'').' is: '.$r);
        }

        if ($json['errCode']!=0) {
            return false;
        }

        $this->session_token=$sessionId;

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

    public function checkUser($playerId, $balance, $office_id,$isTest)
    {
        $u = new Model_User(['office_id' => $office_id, 'external_id' => $playerId, 'api' => 10]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

        $u->amount="".$balance;

		$u->test=$isTest;
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

    public function checkOffice($currency,$zone,$isTest)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        $external_name='pinup'.$currency.$zone;

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Can't create office with currency $currency ");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'apitype'=>10,
            'is_test' => (int) $isTest
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }

        return $o->id;
    }

    public function createUser($playerId, $balance, $office_id, $userName,$isTest)
    {

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $playerId . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 10;
        $u->amount="".$balance;
		$u->test=$isTest;
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->visible_name = $userName;
        $u->external_id = $playerId;
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createOffice($currency,$zone,$isTest)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o = new Model_Office;

        $external_name='pinup'.$currency.$zone;

        $o->currency_id = $currency->id;
        $o->visible_name = "Pinup " . ($isTest ? '[TEST]' : '') . " {$currency->code}{$zone}";
        $o->external_name = $external_name;

        $o->apienable = 1;
        $o->apitype = 10;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;

        $o->gameapiurl = $this->_url;

        $o->bonus_diff_last_bet = 8;
        $o->enable_bia = time();
        $o->rtp = 96;
        $o->owner = 1150;
		
		if($zone=='pinco') {
            $o->owner = 1152;
        }

        $o->dentabs = $currency->default_den;
        $o->default_dentab = $currency->default_dentab;
        $o->k_to_jp = 0.005;
        $o->k_max_lvl = $currency->default_k_max_lvl;
        $o->enable_jp = 1;

        $o->games_rtp = 97;
        $o->gameui = 1;

        $o->is_test = (int) $isTest;
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
                Where g.provider = 'our' and g.branded=0 and brand ='agt' and show=1 and pinup_show=1 and g.category!='coming'
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

    public function checkBalance($token,$player_id,$game_id, $sess_id = NULL)
    {

        $session=$this->getSession($token,$player_id,$game_id);

        if(!$session) {
            throw new Exception('no session');
        }

        return $session['balance'];
    }

    public function getLaunchURL($path,$req_json,$zone) {

        $domain = kohana::$config->load('static.gameapi_domen_pinup');

		if($zone=='pinco') {
            $domain = kohana::$config->load('static.gameapi_domen_pinco');
        }

        return $domain.'/'.$path.'?'.http_build_query($req_json);
    }
    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $zone)
    {
        $domain = kohana::$config->load('static.gameapi_domen_pinup');

		if($zone=='pinco') {
            $domain = kohana::$config->load('static.gameapi_domen_pinco');
        }

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

        

        $link_params[] = 'no_close=' . ((int)$no_close);

        $link .= '?' . implode('&', $link_params);

        return $link;
    }

    public function commandId(){

        $r=dbredis::instance();
        $value=$r->incr('pinupCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('pinupCommandId',time()*100+$ms);
            $value=$r->incr('pinupCommandId');
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

        $this->last_error_code=null;

        if(!isset($data['transactionType'])) {
            $data['transactionType']=2;
        }

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] request bet: ' . $url.'/action'
            . PHP_EOL . json_encode($data, 1).PHP_EOL, 'pinup');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $betRequest = $parser->post($url . '/action?token='.urlencode($this->_apitoken), $data, true, $this->_send_headers($data));

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| response bet: ' . $url . PHP_EOL . $betRequest.PHP_EOL, 'pinup');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url, 'pinup');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBet).PHP_EOL.Debug::vars($betRequest), 'pinup');
            return false;
        }

        if ($jsonBet['errCode']!=0) {
            $this->last_error_code=$jsonBet['errCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[error]', 'pinup');
            return false;
        }

        return $jsonBet['balance'];
    }

    protected function _send_win_request($url,$data,$office) {

        $this->last_error_code=null;

        if(!isset($data['transactionType'])) {
            $data['transactionType']=1;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] request win: '
            .$url.PHP_EOL.json_encode($data,1).PHP_EOL,'pinup');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $winRequest = $parser->post($url.'/action?token='.urlencode($this->_apitoken),$data,true,$this->_send_headers(json_encode($data)));

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] time: |'.$response_time.'| response win: '.$url.PHP_EOL.$winRequest.PHP_EOL,'pinup');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url, 'pinup');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if ($jsonWin['errCode']!=0) {
            $this->last_error_code=$jsonWin['errCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'pinup');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_fswin_request($url,$data,$office) {

        $this->last_error_code=null;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] request fswin: '
            .$url.'/action/freespin'.PHP_EOL.json_encode($data,1).PHP_EOL,'pinup');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $winRequest = $parser->post($url.'/action/freespin?token='.urlencode($this->_apitoken),$data,true,$this->_send_headers(json_encode($data)));

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] time: |'.$response_time.'| response fswin: '.$url.PHP_EOL.$winRequest.PHP_EOL,'pinup');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| ERROR response fswin: <' . $parser->error . '> ' . $url, 'pinup');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonFSWinssss[error]', 'pinup');
            return false;
        }

        if ($jsonWin['errCode']!=0) {
            $this->last_error_code=$jsonWin['errCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonFSWin[error]', 'pinup');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_cancel_request($url,$data,$office) {

        $this->last_error_code=null;

        if(!isset($data['transactionType'])) {
            $data['transactionType']=-2;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] request cancel: '.$url
            .PHP_EOL.json_encode($data,1),'pinup');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $cancelRequest = $parser->post($url.'/action?token='.urlencode($this->_apitoken),$data,true,$this->_send_headers(json_encode($data)));

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['gameRoundId'].'] time: |'.$response_time.'| response cancel: '.$url.PHP_EOL.$cancelRequest.PHP_EOL,'pinup');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['gameRoundId'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $url, 'pinup');
            return false;
        }

        $jsonCancel=json_decode($cancelRequest,1);

        if(!$jsonCancel) {
            return false;
        }

        if ($jsonCancel['errCode']!=0) {
            $this->last_error_code=$jsonCancel['errCode'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel[error]', 'pinup');
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

        $amount = "".$params['amount'];
        $win = "".$params['win'];
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
            "transactionId" => "".$transaction_id,
            "sessionId" => $session_token,
            "playerId" => $u->external_id,
            "gameId" => $params['game'],
            "gameRoundId" => "".$round_num,
            "roundStatus" =>!($win>0 || !$need_send_betrequest)?2:1,
            "amount" => "".$amount,
        ];

        $isFSWin=false;
		$isFS=false;

        $zone='com';
        if($u->office->owner==1152) {
            $zone='pinco';
        }

        $this->setUpEnv($u->office->is_test,$zone);

        if(($params['freeroundID']??0)>0) {
			$isFS=true;
            $data['freespinId']=$params['freeroundID'];
            unset($data['roundStatus']);
            $need_send_betrequest=false;
            $isFSWin=$win>0;
        }

        if($need_send_betrequest) {

            if($params['game']=='jp') {
                $data['gameId']=bet::getJP()->game;
            }

            $betRequest = $this->_send_bet_request($url,$data,$u->office);
            $this->bet_transaction_id=$data['transactionId'];

            if($betRequest===false) {

                //try one more time

                $betRequest = $this->_send_bet_request($url,$data,$u->office);

                if($betRequest===false) {
                    if(!$this->_send_cancel_request($url,$data,$u->office) && !empty($this->last_error_code) && $this->last_error_code!=6) {
                        th::sendCurlError('pinup');
                        return false;
                    }
                    th::sendCurlError('pinup');
                    throw new Exception_ApiResponse('bet rejected.');
                }
            }

            $u->amount = $betRequest;
        }

        //send win
        if(!$isFS && ($win>0 || !$need_send_betrequest)) {

            if($params['game']=='jp') {
                $data['gameId']=bet::getJP()->game;
                $data['transactionType']=3;
				$data['transactionId']="".$this->commandId();
            }

            $data['roundStatus']=2;
            $data['amount']=$win;

            $this->win_transaction_id=$data['transactionId'];

            $winRequest = $this->_send_win_request($url,$data,$u->office);

            if(!$winRequest) {
                //try to send win again
                $winRequest = $this->_send_win_request($url,$data,$u->office);
                if(!$winRequest) {
                    $this->_wrongBetType='win';
                    //move to wrongbets as WIN
                    th::sendCurlError('pinup');
                    return false;
                }
            }

            $u->amount = $winRequest;
        }

        if($isFS) {

            $data['amount']=$win;

            $winFSRequest = $this->_send_fswin_request($url,$data,$u->office);

            if(!$winFSRequest) {
                //try to send win again
                $winFSRequest = $this->_send_fswin_request($url,$data,$u->office);
                if(!$winFSRequest) {
                    $this->_wrongBetType='fswin';
                    //move to wrongbets as WIN
                    th::sendCurlError('pinup');
                    return false;
                }
            }

            $u->amount = $winFSRequest;
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

        $b = new Model_WrongbetPinup();
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
        $b->fs_id = $params['freeroundID']??null;
        $b->try = 0;
        $b->fin = (int) $this->fin;
        $b->win_sended = 0;
        $b->save();
    }

    public function processWrongBets($user_id) {

        $bets = db::query(1,'select * from wrongbetspinup where user_id=:u_id and game=:g_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->param(':g_id',$this->gameName)
            ->execute(null,'Model_WrongbetPinup');


        if(count($bets)) {

            $u = new Model_User($user_id);

            if(!$u->loaded()) {
                throw new Exception('user not found!');
            }

            $office = $u->office;
            $currency=$office->currency;
            $mult = $currency->mult??2;

            $zone='com';
            if($office->owner==1152) {
                $zone='pinco';
            }

            $this->setUpEnv($office->is_test,$zone);

            dbredis::instance()->select(0);
            $session_token=auth::getCustomSessionId($u->id,$u->api_session_id);

            if(!$session_token) {
                return false;// todo what to do?
            }

            $errors=false;

            foreach($bets as $bet) {

                $amount = $bet->amount;
                $win = $bet->win;

                $need_send_winrequest=$win>0;
                $need_send_betrequest=true;
                $isFSWin=!empty($bet->fs_id);

                if($bet->poker_bet_id>0 || $bet->initial_id>0) {
                    $need_send_betrequest=false;
                }

                $data=[
                    "transactionId" => "".$this->commandId(),
                    "sessionId" => $session_token,
                    "playerId" => $u->external_id,
                    "gameId" => $bet->game,
                    "gameRoundId" => $bet->bet_id,
                    "roundStatus" =>!($win>0 || !$need_send_betrequest)?2:1,
                    "amount" => "".$amount,
                ];

				$is_canceled=false;

                if($isFSWin) {
                    $data['freespinId']=$bet->fs_id;
                    unset($data['roundStatus']);
                }
                elseif($need_send_betrequest && !$need_send_winrequest) {
                    $cancelRequest=$this->_send_cancel_request($this->_url,$data,$u->office);
                    if(!$cancelRequest) {
                        if($this->last_error_code==6) {
                            $bet->processed = 1;
							$is_canceled=true;
                        }
                        else {
                            $bet->try++;
							$errors=true;
                        }
                    }
                    else {
                        $bet->processed = 1;
						$is_canceled=true;
                        $u->amount = $cancelRequest;
                    }
                }
                elseif($need_send_betrequest && $need_send_winrequest) {

                    if($bet->game_type=='jp') {
                        $data['gameId']=$bet->game;
                    }

                    $betRequest = $this->_send_bet_request($this->_url,$data,$u->office);

                    if($betRequest===false) {
                        $bet->try++;
						$errors=true;
                    }
                    else {
                        $u->amount = $betRequest;
                    }
                }

                //send win
                if($need_send_winrequest && !$isFSWin) {

                    if($bet->game_type=='jp') {
                        $data['gameId']=$bet->game;
                        $data['transactionType']=3;
						//todo временно
                        if($bet->created<=1732880614) {
                            th::ceoAlert('pinup old jp refund! '.$bet->user_id.', '.$bet->id);
                        }
						$bet->win_transaction_id=$this->commandId();
						$data['transactionId']="".$bet->win_transaction_id;
                    }

                    $data['roundStatus']=2;
                    $data['amount']=$win;

                    $winRequest = $this->_send_win_request($this->_url,$data,$u->office);

                    if(!$winRequest) {
                        //try to send win again
                        $winRequest = $this->_send_win_request($this->_url,$data,$u->office);
                        if(!$winRequest) {
                            $bet->try++;
                        }
                        else {
                            $bet->processed = 1;
							$u->amount = $winRequest;
                        }
                    }
					else {
                        $bet->processed = 1;
                        $u->amount = $winRequest;
                    }
                    
                }

                if($isFSWin) {

                    $data['amount']=$win;

                    $winFSRequest = $this->_send_fswin_request($this->_url,$data,$u->office);

                    if(!$winFSRequest) {
                        $bet->try++;
						$errors=true;
                    }
                    else {
                        $bet->processed = 1;
						$u->amount = $winFSRequest;
                    }
                    
                }
				
				if($bet->processed==1 && !$is_canceled) {
					$u->save();
					$betArr = $bet->as_array();
					$betArr['game_type']=($bet->game == 'jp')?'jp':'agt';
					$betArr['game_name']=$betArr['game'];
					$betArr['can_jp']=false;
					$betArr['send_api']=false;
					$info = 'wb; '.date('Y-m-d H:i:s',$bet->created);
					if(th::isMoonGame($bet->game) && !empty($bet->initial_id)) {
						$info.='; '.$bet->initial_id;
					}
					$betArr['info']=$info;
					auth::$user_id=$u->id;
					bet::make($betArr,$betArr['type'],[],true,false);

					if(th::isMoonGame($betArr['game']) && $betArr['initial_id']>0) {
						game_moon_agt::updateUserBetHistory($user_id,$betArr);
					}

					$errors=false;
				}

                $bet->save();
            }

            return !$errors;
        }
        return false;
    }

    public static function checkAndPayFS(Model_User $u,$gameName) {

        $o=$u->office;
        $currencyCode=$o->currency->code;

        $waitedFS=new Model_PinupFreespin(['user_id'=>$u->id,'currency'=>$currencyCode,'paid'=>0]);

        if($waitedFS->loaded()) {

            if(!in_array($gameName,$waitedFS->games)) {
                return false;
            }

            $waitedFS->user_id=$u->id;
            $waitedFS->office_id=$u->office_id;

            if($waitedFS->expireDate>=time()) {
                $fs=new Model_Freespin();

                $game_ids=array_keys(db::query(1,'select id from games where name in :names')
                    ->param(':names',$waitedFS->games)
                    ->execute()
                    ->as_array('id'));

                $waitedFS->paid=time();
                $waitedFS->save();

                $fs->fs_offer_type='pinup';
                $fs->fs_offer_id=$waitedFS->id;

                //$fs->giveFreespins($u->id,$u->office_id,$game_ids,$waitedFS->spinAmount,$waitedFS->betAmount,0,0,'api',false,null,false,null,$waitedFS->expireDate,$waitedFS->freespinId);
				$fs_id=$fs->giveFreespins($u->id,$u->office_id,$game_ids,$waitedFS->spinAmount,$waitedFS->betAmount,0,0,'api',false,null,false,null,$waitedFS->expireDate,$waitedFS->freespinId);
                if($fs_id) {
                    $fs->activateFreespins($fs_id);
                }
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


