<?php

/*
 *
Route::set('softswissemulate', 'apisw/v2/provider_a8r.<action>(/<id>)')
    ->defaults(array(
        'controller' => 'apisw',
        'action'     => 'unknown',
        'id'     => 'unknown',
        'apiver'     => '2',
    ));

Route::set('softswiss', 'apisw/v2/a8r_provider.<action>(/<id>)')
    ->defaults(array(
        'controller' => 'apisw',
        'action'     => 'unknown',
        'id'     => 'unknown',
        'apiver'     => '2',
    ));
 */

class Api_SoftSwiss extends gameapi
{

    public $key, $guid, $platform, $gameName, $session_token;
    protected $_url = '';
    private $_token = '';
    private $_secretkey = '';
    protected $_wrongBetType='';
    public $bet_transaction_id;
    public $win_transaction_id;

    protected $_is_test=false;
    protected $_api_ver=2;

    private $_access=[
        1=>[
			'test'=>['https://site-domain.local/apisw','agtintegrationkey'],
            'test'=>['https://provider.dev0.a8r.games/api/site-domain/devgame','kgWK8FqqUb^Dq7T@E+9yVWvwZ=QF$3jC'],
        ],
        2=>[
            'test'=>['https://site-domain.local/apisw','agtintegrationkey'],
            'test'=>['https://provider.dev0.a8r.games/api/site-domain/internal','3y65pq_JB+N7ZB8DRfpsnf+eq7V3XBk@'],
        ],
    ];

    const ENDPOINTS=[
        1=>[
            'balance'=>'/v2/provider_a8r.Player/Balance',
            'bet'=>'/v2/provider_a8r.Round/BetWin',
            'finish'=>'/v2/provider_a8r.Round/Finish',
            'cancel'=>'/v2/provider_a8r.Round/Rollback',
			'freespin'=>'/v2/provider_a8r.Freespins/Finish',
			'prize'=>'/v2/provider_a8r.Promo/Win',
        ],
        2=>[
            'balance'=>'/v2/provider_a8r.Player/Balance',
            'bet'=>'/v2/provider_a8r.Round/BetWin',
			'finish'=>'/v2/provider_a8r.Round/Finish',
			'cancel'=>'/v2/provider_a8r.Round/Rollback',
			'freespin'=>'/v2/provider_a8r.Freespins/Finish',
			'prize'=>'/v2/provider_a8r.Promo/Win',
        ],
    ];

    public function forceURL($url) {
        if(!empty($url)) {
            $this->_url=$url;
        }
    }
    public function setUpEnv($test=false,$api_ver=2) {

        $this->_is_test=$test;

        if($test) {
            list($this->_url,$this->_secretkey)=$this->_access[$api_ver]['test'];
            return $this->_url;
        }

        list($this->_url,$this->_secretkey)=$this->_access[$api_ver]['prod'];

        return $this->_url;
    }

	public function checkUserAndOffice($casino_id,$nickname) {
        $office_id = $this->checkOffice($casino_id);

        if(!$office_id) {
            $office_id = $this->createOffice($casino_id);
        }

        $user_id = $this->checkUser($office_id);

        if(!$user_id) {
            $user_id = $this->createUser($office_id,$nickname);
        }
        elseif(!empty($this->session_token)) {
            $wasWrongBets = $this->processWrongBets($user_id);

            if($wasWrongBets) {
                $user_id = $this->checkUser($office_id); //update balance again
            }
        }

        return [$office_id,$user_id];
    }
	
    protected function _send_headers($request)
    {
        return [
            'Content-Type: application/json',
            'X-REQUEST-SIGN: ' . $this->sign($request),
        ];
    }

    public function checkSign($body,$sign) {
        return $this->sign($body)==$sign;
    }

    public function sign($body) {

        return hash_hmac("sha256", $body, $this->_secretkey);
    }

    public function login() {

        $data['token']=$this->session_token;

        $jsonRequest=json_encode($data);

        logfile::create(date('Y-m-d H:i:s') . ' request login for token <'.$this->session_token.'>: '
            . PHP_EOL . $this->_url
            . PHP_EOL . json_encode($data, 1).PHP_EOL.print_r($this->_send_headers($jsonRequest),1), 'softswiss');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $loginRequest = $parser->post($this->_url . '/login', $data, true, $this->_send_headers($jsonRequest));
		
		$this->last_http_code=$parser->http_code;

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' response login for token <'.$this->session_token.'>: time: |' .
            $response_time . '| response login: ' . $this->_url . PHP_EOL . $loginRequest, 'softswiss');

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

    public function checkUser($office_id)
    {
        $u = new Model_User(['office_id' => $office_id, 'external_id' => $this->account_id, 'api' => 12]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

		if(empty($this->session_token)) {
            return $u->id;
        }

        $user_balance=$this->checkBalance();

        $u->amount="".$user_balance;

        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

    public function createUser($office_id,$nickname='')
    {

		$user_balance=0;
        
        if(!empty($this->session_token)) {
            $user_balance=$this->checkBalance();
        }
        
        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $this->account_id . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 12;
        $u->amount="".$user_balance;
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->visible_name = $nickname;
        $u->external_id = $this->account_id;
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function checkOffice($casino_id)
    {
        $currency = new Model_Currency(['code' => $this->currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Can't check office with currency $this->currency ");
        }

        $external_name='softsw'.$this->currency.$casino_id;

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'apitype'=>12,
            'is_test' => (int) $this->_is_test
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }
		
		/*if(empty($o->partner)) {
            $o->partner = $casino_id;
            $o->save();
        }*/

        return $o->id;
    }

    public function createOffice($casino_id)
    {
        $currency = new Model_Currency(['code' => $this->currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $this->currency ");
        }

        $o = new Model_Office;

        $external_name='softsw'.$this->currency.$casino_id;

        $o->currency_id = $currency->id;
        $o->visible_name = "Softswiss " . ($this->_is_test ? '[TEST]' : '') . " {$currency->code}{$casino_id}";
        $o->external_name = $external_name;

        $o->apienable = 1;
        $o->apitype = 12;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;
		
		if($o->bet_min*$currency->val<0.1) {
            $o->bet_min=0.1/$currency->val;
        }

        $o->max_win_eur = 250000;

        $o->gameapiurl = $this->_url;

        $o->bonus_diff_last_bet = 8;
        $o->enable_bia = time();
        $o->rtp = 96;
        $o->owner = 1205;
		//$o->partner = $casino_id;

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
        $data['currency']=$this->currency;
        $data['session_id']=$this->session_token;
        $data['account_id']=$this->account_id;
		$data['game_id']=$this->gameName;

        $endpoint=self::ENDPOINTS[$this->_api_ver]['balance'];

        $jsonRequest=json_encode($data);

        logfile::create(date('Y-m-d H:i:s') . ' [' . $this->session_token . '] BALANCE request: ' . $this->_url.$endpoint
            . PHP_EOL . json_encode($data, 1).PHP_EOL, 'softswiss');

        $start_time = microtime(1);

        $r=$parser->post($this->_url.$endpoint,$data,true,$this->_send_headers($jsonRequest));

        $response_time = microtime(1) - $start_time;
        logfile::create(date('Y-m-d H:i:s') . ' [' . $this->session_token . '] time: |' . $response_time . '| response: '
            . $this->_url.$endpoint . PHP_EOL . $r, 'softswiss');

        if (!$r) {
            $this->last_error = 'bad response';
            return false;
        }

        $jr = json_decode($r, 1);

        if (!$jr) {
            return false;
        }

        if (isset($json['code'])) {
            return false;
        }

        if (!isset($jr['balance']) || (float)$jr['balance'] < 0) {
            return false;
        }

        return $jr['balance'];
    }

    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $cashier_url = false)
    {
        $domain = kohana::$config->load('static.gameapi_domen_softswiss');

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
        $value=$r->incr('softswCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('softswCommandId',time()*100+$ms);
            $value=$r->incr('softswCommandId');
        }

        return $value;


    }

    public function isNeedSend($url)
    {
        return true;
    }

    protected function _check_errors() {

    }

    public $last_error_code='';
	public $last_http_code=0;
    public $last_error_json=[];

    protected function _send_bet_request($url,$data,$office)
    {

        $url=$this->_url;
        $endpoint=self::ENDPOINTS[$this->_api_ver]['bet'];
        $url.=$endpoint;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] request bet: ' . $url
            . PHP_EOL . json_encode($data, 1), 'softswiss');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);


        $betRequest = $parser->post($url, $data, true, $this->_send_headers(json_encode($data)));
		
		$this->last_http_code=$parser->http_code;

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| response bet: ' . $url . PHP_EOL . $betRequest, 'softswiss');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url, 'softswiss');
            return false;
        }

        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBet).PHP_EOL.Debug::vars($betRequest), 'softswiss');
            return false;
        }

        if (isset($jsonBet['code'])) {
            $this->last_error_code=$jsonBet['code'];
            $this->last_error=$jsonBet['msg'];
            $this->last_error_json=$jsonBet;
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[code]', 'softswiss');
            return false;
        }

        if(!isset($jsonBet['round_id']) || $jsonBet['round_id']!=$data['round_id']) {
            $this->last_error_code='canceled';
            $this->last_error='invalid argument [round_id]';
            return false;
        }

        if(!isset($jsonBet['transactions']) || empty($jsonBet['transactions'])) {
            $this->last_error_code='canceled';
            $this->last_error='invalid argument [transactions]';
            return false;
        }

        if(!isset($jsonBet['balance'])) {
            $this->last_error_code='canceled';
            $this->last_error='invalid argument [balance]';
            return false;
        }

        return $jsonBet['balance'];
    }

	protected function _send_finish_request($url,$data,$office)
    {

        unset($data['transactions']);

        $url=$this->_url;
        $endpoint=self::ENDPOINTS[$this->_api_ver]['finish'];
        $url.=$endpoint;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] request finish: ' . $url
            . PHP_EOL . json_encode($data, 1), 'softswiss');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);


        $finishRequest = $parser->post($url, $data, true, $this->_send_headers(json_encode($data)));
		
		$this->last_http_code=$parser->http_code;

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| response finish: ' . $url . PHP_EOL . $finishRequest, 'softswiss');

        if (!$finishRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id'] . '] time: |' . $response_time . '| ERROR response finish: <' . $parser->error . '> ' . $url, 'softswiss');
            return false;
        }

        $jsonFinish = json_decode($finishRequest, 1);

        if (!$jsonFinish) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonFinish).PHP_EOL.Debug::vars($finishRequest), 'softswiss');
            return false;
        }

        if (isset($jsonFinish['code'])) {
            $this->last_error_code=$jsonFinish['code'];
            $this->last_error=$jsonFinish['msg'];
            $this->last_error_json=$jsonFinish;
            logfile::create(date('Y-m-d H:i:s') . '$jsonFinish[code]', 'softswiss');
            return false;
        }

        if(!isset($jsonFinish['balance'])) {
            $this->last_error='invalid argument [balance]';
            return false;
        }

        return $jsonFinish['balance'];
    }

	protected function _send_freespin_request($url,$data,$office)
    {

        $url=$this->_url;
        $endpoint=self::ENDPOINTS[$this->_api_ver]['freespin'];
        $url.=$endpoint;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['issue_id'] . '] request freespin: ' . $url
            . PHP_EOL . json_encode($data, 1), 'softswiss');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);


        $finishRequest = $parser->post($url, $data, true, $this->_send_headers(json_encode($data)));
		
		$this->last_http_code=$parser->http_code;

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['issue_id'] . '] time: |' . $response_time . '| response freespin: ' . $url . PHP_EOL . $finishRequest, 'softswiss');

        if (!$finishRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['issue_id'] . '] time: |' . $response_time . '| ERROR response freespin: <' . $parser->error . '> ' . $url, 'softswiss');
            return false;
        }

        $jsonFinish = json_decode($finishRequest, 1);

        if (!$jsonFinish) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonFinish).PHP_EOL.Debug::vars($finishRequest), 'softswiss');
            return false;
        }

        if (isset($jsonFinish['code'])) {
            $this->last_error_code=$jsonFinish['code'];
            $this->last_error=$jsonFinish['msg'];
            $this->last_error_json=$jsonFinish;
            logfile::create(date('Y-m-d H:i:s') . '$jsonFinish[code]', 'softswiss');
            return false;
        }

        return $jsonFinish['balance'];
    }
	
	protected function _send_prize_request($url,$data,$office)
    {

        $url=$this->_url;
        $endpoint=self::ENDPOINTS[$this->_api_ver]['prize'];
        $url.=$endpoint;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['account_id'] . '] request prize: ' . $url
            . PHP_EOL . json_encode($data, 1), 'softswiss');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);


        $prizeRequest = $parser->post($url, $data, true, $this->_send_headers(json_encode($data)));
		
		$this->last_http_code=$parser->http_code;

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['account_id'] . '] time: |' . $response_time . '| response prize: ' . $url . PHP_EOL . $prizeRequest, 'softswiss');

        if (!$prizeRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['account_id'] . '] time: |' . $response_time . '| ERROR response prize: <' . $parser->error . '> ' . $url, 'softswiss');
            return false;
        }

        $jsonPrize = json_decode($prizeRequest, 1);

        if (!$jsonPrize) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonPrize'.PHP_EOL.Debug::vars($jsonPrize).PHP_EOL.Debug::vars($prizeRequest), 'softswiss');
            return false;
        }

        if (isset($jsonPrize['code'])) {
            $this->last_error_code=$jsonPrize['code'];
            $this->last_error=$jsonPrize['msg'];
            $this->last_error_json=$jsonPrize;
            logfile::create(date('Y-m-d H:i:s') . '$jsonPrize[code]', 'softswiss');
            return false;
        }

        return $jsonPrize['balance'];
    }
	
    protected function _send_win_request($url,$data,$office) {
		throw new Exception('not allow win');
        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] request win: '
            .$url.PHP_EOL.json_encode($data,1),'softswiss');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $winRequest = $parser->post($url.'/credit',$data,true,$this->_send_headers(json_encode($data)));
		
		$this->last_http_code=$parser->http_code;

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] time: |'.$response_time.'| response win: '.$url.PHP_EOL.$winRequest,'softswiss');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundId'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url, 'softswiss');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (isset($jsonWin['error'])) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'softswiss');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_cancel_request($url,$data,$office) {
        $url=$this->_url;
        $endpoint=self::ENDPOINTS[$this->_api_ver]['cancel'];
        $url.=$endpoint;

        $data['round_id_provider']=$data['round_id'];
        unset($data['round_id']);
        $data['transactions'][0]['type']='rollback';
        $data['transactions'][0]['original_id_provider']=$data['transactions'][0]['id_provider'];
        $data['transactions'][0]['id_provider']=$this->commandId();
        unset($data['transactions'][0]['jackpot_details']);
        unset($data['transactions'][0]['amount']);

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['round_id_provider'].'] request cancel: '.$url
            .PHP_EOL.json_encode($data,1),'softswiss');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $cancelRequest = $parser->post($url,$data,true,$this->_send_headers(json_encode($data)));
		
		$this->last_http_code=$parser->http_code;

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['round_id_provider'].'] time: |'.$response_time.'| response cancel: '.$url.PHP_EOL.$cancelRequest,'softswiss');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['round_id_provider'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $url, 'softswiss');
            return false;
        }

        $jsonCancel=json_decode($cancelRequest,1);

        if(!$jsonCancel) {
            return false;
        }

        if (isset($jsonCancel['code'])) {
            $this->last_error_code=$jsonCancel['code'];
            $this->last_error=$jsonCancel['msg'];
            $this->last_error_json=$jsonCancel;
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel[code]', 'softswiss');
            return false;
        }

        if(!isset($jsonCancel['balance'])) {
            $this->last_error='invalid argument [balance]';
            return false;
        }

        return $jsonCancel['balance'];
    }

	protected function floatFormat($float,$mult) {
        $formatted=rtrim(sprintf('%.'.($mult+4).'F',$float),'0');

        list($left,$right)=explode('.',$formatted);

        if(empty($right)) {
            return $left;
        }

        return $formatted;
    }

    public function bet($login,$url, $params=[],$is_repeat=false) {

		$is_prize=($params['event_id']??false)!==false;

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

        $amount = $this->floatFormat($params['amount'],$mult);
        $win = $this->floatFormat($params['win'],$mult);
        $bet_id = $params['bet_id'];

        $fin=true;
        $round_num=$bet_id;

        $need_send_betrequest=$params['amount']>0;

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

        $transactions=[];


        $jackpots=[];

        if($o->enable_jp && $params['amount']>0 && ($params['can_jp']??true)) {

            $jackpots=[
                "breakdown" => [],
            ];

            foreach(Model_Jackpot::calcAllJPs($params['amount'],$u->office) as $jp) {
                $jackpots['breakdown'][]=[
                    "contribution" => $this->floatFormat($jp,$mult+4),
                    "id" => guid::create(),
                ];
            }
        }

        $isFS=false;

        if(($params['freeroundID']??0)>0) {
            $isFS=true;
            $need_send_betrequest=false;
        }
		
        if($need_send_betrequest) {
            $this->bet_transaction_id = $this->commandId();
            $betTransaction=[
                //bet
                "amount" => $amount,
                "id_provider" => "".$this->bet_transaction_id,
                "type" => "bet",
            ];

            if($params['is_buy']??0==1) {
                $betTransaction['bet_type']='bonusBuy';
            }

            if(!empty($jackpots)) {
                $betTransaction['jackpot_details']=$jackpots;
                $betTransaction['jackpot_details']['total_contribution']=$this->floatFormat(Model_Jackpot::calcNewValJP($params['amount'],$u->office),$mult+4); //сейчас этот параметр без того, что ниже - не работает
                //$betTransaction['jackpot_contribution']=$betTransaction['jackpot_details']['total_contribution']; //deprecated after 01.07.2025
            }
            $transactions[]=$betTransaction;
        }

        if($fin && $params['win']>0 && !$isFS) {
            $this->win_transaction_id = $this->commandId();
            $winTransaction=[
                    //win
                    "amount" => $win,
                    "id_provider" => "".$this->win_transaction_id,
                    "type" => "win",
            ];

            if($params['game']=='jp') {
                $winTransaction['jackpot_details']=[];
                $winTransaction['jackpot_details']['total_win']=$winTransaction['amount'];
                $winTransaction['jackpot_win']=$winTransaction['amount'];
                //$winTransaction['amount']=0; //??check
            }

            $transactions[]=$winTransaction;
        }

        if(!$isFS && empty($transactions)) {
            return true;
        }

        $request_type='bet';

        $data = [
            "account_id" => $u->external_id,
            "currency" => $u->office->currency->code,
            "finished" => $fin,
            "game_id" => $params['game']=='jp'?bet::getJP()->game:$params['game'],
            "round_id" => "".$round_num,
            "session_id" => $session_token,
//            "sm_result" =>
//                "0:5;8;1;3;1#10;10;11;11;11#4;2;10;9;4#R#10#H10#122#MV#0.01#MT#1#R#10#H10#112#MV#0.01#MT#1#MG#0.08#",
            "transactions" => $transactions,
        ];

		if($isFS) {
            $data=[
                'amount'=>$win,
                'issue_id'=>$params['freeroundID'],
            ];
            $request_type='freespin';
        }
		
		if($is_prize) {
            $data=[
                "account_id" => $u->external_id,
                'amount'=>$win,
                "currency" => $u->office->currency->code,
                "event_id" => $params['event_id'],
                "event_type" => 'Promo',
                "id_provider" => guid::create(),
            ];
            $request_type='prize';
        }
		
        $this->setUpEnv($u->office->is_test);

        logfile::create(PHP_EOL.PHP_EOL.PHP_EOL, 'softswiss');


        if($fin && !$need_send_betrequest && $params['win']<=0 && !$isFS) {
            //finish round
            $request_type='finish';
        }

        $requestName='_send_'.$request_type.'_request';

        $new_balance=$this->$requestName($url, $data, $u->office);

        if($new_balance===false) {

            if($is_repeat) {
                return false;
            }

			//bug #9. no repeat and no cancel if custom error
            if($this->last_http_code>=400 && $this->last_http_code<500) {
                throw new Exception_ApiResponse($this->last_error);
            }

            $new_balance = $this->$requestName($url,$data,$u->office);

            if($new_balance===false) {

                if(in_array($this->last_error_code,[
                    'already_exists',
                ])) {
                    //ставка дошла ранее, просто обновляем баланс
                    $new_balance=$this->last_error_json['meta']['balance'];
                }
                elseif(in_array($this->last_error_code,[
                    'canceled',
                ]) && !$this->_send_cancel_request($url,$data,$u->office)) {
                    $this->_wrongBetType='cancel';
                    return false;
                }
                else {
                    return false;
                }
            }
        }

        if($new_balance<0) {
            //todo что лучше сделать?
            return false;
        }

        $u->amount=$new_balance;
        $u->save();

        return true;
    }

    public function jp($login,$url, $params=[]) {
        return false;
    }

    public function saveWrongBet($bet,$params,$poker_bet_id) {

		//bug #9. no repeat and no cancel if custom error
		if($this->last_http_code>=400 && $this->last_http_code<500) {
            return;
        }

        if($bet->game=='jp') {
            $bet->game=bet::getJP()->game;
        }

        $b = new Model_WrongbetSoftswiss();
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
		$b->event_id = $params['event_id']??null;
		
		//иначе задвоение. бонус начисляется пока не будет начислен, поэтому сохранять не нужно
        if(!empty($b->event_id)) {
            return;
        }
		
        $b->save();
    }

    public function processWrongBets($user_id) {

        $bets = db::query(1,'select * from wrongbetssoftswiss where user_id=:u_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->execute(null,'Model_WrongbetSoftswiss');


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

                $o=$u->office;

                $mult=$o->currency->mult ?? 2;

                $amount = $this->floatFormat($bet->amount,$mult);
                $win = $this->floatFormat($bet->win,$mult);
                $bet_id = $bet->bet_id;

                $fin=true;
                $round_num=$bet_id;

                $need_send_betrequest=$bet->amount>0;

                if(isset($bet->poker_bet_id) && $bet->poker_bet_id>0) {
                    $round_num=$bet->poker_bet_id;
                    $need_send_betrequest=false;
                }
                elseif(isset($bet->initial_id) && $bet->initial_id>0) {
                    $round_num=$bet->initial_id;
                    $need_send_betrequest=false;
                }
                elseif(th::isMoonGame($bet->game) && ($bet->win==0)) {
                    $fin=false;
                }
                elseif(in_array($bet->game,['acesandfaces','jacksorbetter','tensorbetter']) && $bet->type=='normal') {
                    $fin=false;
                }


                $transactions=[];


                $jackpots=[];

                if($o->enable_jp && $bet->amount>0 && $bet->type!='double' && !$bet->is_freespin) {

                    $jackpots=[
                        "breakdown" => [],
                    ];

                    foreach(Model_Jackpot::calcAllJPs($bet->amount,$u->office) as $jp) {
                        $jackpots['breakdown'][]=[
                            "contribution" => $this->floatFormat($jp,$mult+4),
                            "id" => guid::create(),
                        ];
                    }
                }

                $is_award=false;

                $request_type='cancel'; //!!!

                if($bet->is_freespin) {
                    $freespin = new Model_Freespinhistory([
                        'freespin_id'=>$bet->fs_id,
                        'fs_offer_type'=>'softswiss',
                    ]);

                    if($freespin->loaded()) {

                        $is_award=true;

                        $award=new Model_SoftSwissAward($freespin->fs_offer_id);

                        $need_send_betrequest=false;
                        $data=[
                            'amount'=>$bet->win,
                            'issue_id'=>$award->issue_id,
                        ];
                        $request_type='freespin';
                    }
                }

                if($need_send_betrequest) {
                    $betTransaction=[
                        //bet
                        "amount" => $amount,
                        "id_provider" => "".$bet->bet_transaction_id,
                        "type" => "bet",
                    ];

                    /*if($params['is_buy']==1) {
                        $betTransaction['bet_type']='bonusBuy';
                    }*/

                    if(!empty($jackpots)) {
                        $betTransaction['jackpot_details']=$jackpots;
                        $betTransaction['jackpot_details']['total_contribution']=$this->floatFormat(Model_Jackpot::calcNewValJP($bet->amount,$u->office),$mult+4); //сейчас этот параметр без того, что ниже - не работает
//                        $betTransaction['jackpot_contribution']=$betTransaction['jackpot_details']['total_contribution']; //deprecated after 01.07.2025
                    }
                    $transactions[]=$betTransaction;
                }

                if($fin && $bet->win>0 && !$is_award) {
                    $winTransaction=[
                        //win
                        "amount" => $win,
                        "id_provider" => "".$bet->win_transaction_id,
                        "type" => "win",
                    ];

                    if($bet->game == 'jp') {
                        $winTransaction['jackpot_details']=[];
                        $winTransaction['jackpot_details']['total_win']=$winTransaction['amount'];
                        $winTransaction['jackpot_win']=$winTransaction['amount'];
                        $winTransaction['amount']=0; //??check
                    }

                    $transactions[]=$winTransaction;
                }

                if(!$is_award && empty($transactions)) {
                    return true;
                }

                if(!$is_award) {
                    $data = [
                        "account_id" => $u->external_id,
                        "currency" => $u->office->currency->code,
                        "finished" => $fin,
                        "game_id" => $bet->game == 'jp'?bet::getJP()->game:$bet->game,
                        "round_id" => "".$round_num,
                        "session_id" => $session_token,
                        "transactions" => $transactions,
                    ];
                }

				if(!empty($bet->event_id)) {
                    $data=[
                        "account_id" => $u->external_id,
                        'amount'=>$bet->win,
                        "currency" => $u->office->currency->code,
                        "event_id" => $bet->event_id,
                        "event_type" => 'Promo',
                        "id_provider" => guid::create(),
                    ];
                    $request_type='prize';
                }


                $this->setUpEnv($u->office->is_test);

                $url=$this->_url;

               /*$request_type='bet';

                if($fin && !$need_send_betrequest && $bet->win<=0) {
                    //finish round
                    $request_type='finish';
                }*/


                $requestName='_send_'.$request_type.'_request';

                $new_balance=$this->$requestName($url, $data, $u->office);

                if($new_balance===false) {
                    $bet->try++;
                }
                else {
                    $bet->processed = 1;

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
                    }
                    catch(Exception $ex)
                    {
                        $errors=true;
                        $bet->try++;
                        $bet->processed=0;
                        //not crushed and not processed. need to fix internal
                        logfile::create(date('Y-m-d H:i:s')." ERROR BET!!!!!: ".$ex->getMessage(). "\n".$ex->getTraceAsString(),'wbsoftgamings');
                    }
                }

                $bet->save();
            }

            return $errors;
        }
        return false;
    }
	
	public static function checkAndPayAwards(Model_User $u,Model_Game $game) {

        if(!in_array($game->type,['slot','shuffle'])) {
            return false;
        }

        $time=time();
        $awards = db::query(1,'select * from softswiss_awards where user_id=:user_id and activated=0 and valid_until>:time')
            ->param(':user_id',auth::$user_id)
            ->param(':time',$time)
            ->execute()
            ->as_array();

        $o=$u->office;
        $c=$o->currency;

        $config_name='agt/' . $game->name;

        $configGame = Kohana::$config->load($config_name);

        $max_lines=$configGame['lines_choose'][0];

        if (isset($configGame['staticlines']) && !empty($configGame['staticlines'])) {
            $max_lines = $configGame['staticlines'][0];
        }


        if(count($awards)) {
            foreach($awards as $award) {
                if($award['bonus_type']=='freespins') {

                    $games=json_decode($award['games'],1);

                    if(!in_array($game->id,$games)) {
                        continue;
                    }

                    $bets=$game->getAllBets($o,$c,$max_lines);

                    $bet=$bets[$award['bet_level']];


                    db::query(database::UPDATE,'update softswiss_awards set activated=:time where id=:id')
                        ->param(':id',$award['id'])
                        ->param(':time',$time)
                        ->execute();

                    $fs=new Model_Freespin();
                    $fs->fs_offer_type='softswiss';
                    $fs->fs_offer_id=$award['id'];

                    $fs_id=$fs->giveFreespins($u->id,$u->office_id,$games,$award['freespins_quantity'],$bet,0,0,'api',false,null,false,null,$award['valid_until'],$award['issue_id']);

                    if($fs_id) {
                        $fs->activateFreespins($fs_id);
                    }
                }
            }
        }
    }

}


