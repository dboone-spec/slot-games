<?php

class Api_BetConstruct extends gameapi
{

    public $key, $guid, $platform, $gameName, $session_token;
    protected $_url = '';
    private $_token = '';
    private $_secretkey = '';
    protected $_wrongBetType='';
    public $last_error_code=0;
    public $bet_transaction_id;
    public $win_transaction_id;

    protected $_is_test=false;

    private $_access=[
        'test'=>['https://staging-rgs-externalintegrationsapi.betconstruct.com/api/V2/site-domain','EFB3F55391C6406FB9C0126B29B7B1D1'],
        'prod'=>['https://rgs-externalintegrationsapi.betconstruct.com/api/V2/site-domain','5082B300A26B42E0890E1A3028128AF2'],
    ];


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

    public function checkUser($userId,$balance,$office_id){

        $u = new Model_User(['office_id' => $office_id, 'external_id' => $userId, 'api' => 9]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

        $user_balance=$balance;

        $u->amount="".$user_balance;

		$u->test = (int) in_array((int) $userId,$this->_testUserIDs);
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

	protected $_testUserIDs=[477160799,478633324,478507847,478533081,478561216,478596996,479085444,479049180,479002249,479019554];

    public function checkOffice($currency,$casinoId)
    {
        $external_name='bc'.$currency.$casinoId;

        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Can't create office with currency $currency $casinoId");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'is_test' => (int) $this->_is_test,
            'apitype'=>9
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }

        return $o->id;
    }

    public function createUser($userId,$balance,$office_id,$nickname)
    {

        $user_balance=$balance;

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $userId . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 9;
        $u->amount="".$user_balance;
		$u->test = (int) in_array((int) $userId,$this->_testUserIDs);
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->visible_name = $nickname;
        $u->external_id = $userId;
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createOffice($currency,$casinoId)
    {
        $external_name='bc'.$currency.$casinoId;

        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency $casinoId");
        }

        $o = new Model_Office;


        $o->currency_id = $currency->id;
        $o->visible_name = "BC " . ($this->_is_test ? '[TEST]' : '') . " {$currency->code} {$casinoId}";
        $o->external_name = $external_name;

        $o->apienable = 1;
        $o->apitype = 9;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;

        $o->gameapiurl = $this->_url;

        $o->bonus_diff_last_bet = 8;
        $o->enable_bia = 0;
        $o->rtp = 96;
        $o->owner = 1134;

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
		
		$o->max_win_eur=250000;

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
                Where g.provider = 'our' and brand ='agt' and show=1 and g.category!='coming' 
                and (g.branded=0 or g.name in :allow)
SQL;

            db::query(Database::INSERT, $sql_games)
                ->param(':office_id', $o->id)
                ->param(':allow', ['mrfirst'])
                ->execute();

            //$o->createProgressiveEventForOffice(); //под вопросом. они не принимают ставку 0

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
        $u=new Model_User($login);
        return $u->amount();
    }

    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $cashier_url = false)
    {
        $domain = kohana::$config->load('static.gameapi_domen_betconstruct');

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
            /*if ($closeurl !== urldecode($closeurl)) {
                $closeurl = urldecode($closeurl);
            }*/

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
        $value=$r->incr('betconstructCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('betconstructCommandId',time()*100+$ms);
            $value=$r->incr('betconstructCommandId');
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

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $time=date('d-m-Y H:i:s');

		if($data['is_our_fs']) {
            $data['gameId']='agt-promowin';
        }
        unset($data['is_our_fs']);

        $data=[
            'time'=>$time,
            'data'=>$data,
        ];
        $data['hash']=$this->sign('Withdraw',$data);


        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] request bet: ' . $url.'/Withdraw'
            . PHP_EOL . json_encode($data, JSON_PRESERVE_ZERO_FRACTION), 'betconstruct');

        $betRequest = $parser->post($url . '/Withdraw', $data, true,['Content-Type: application/json']);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| response bet: ' . $url.'/Withdraw' . PHP_EOL . $betRequest, 'betconstruct');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url.'/Withdraw', 'betconstruct');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBet).PHP_EOL.Debug::vars($betRequest), 'betconstruct');
            return false;
        }

        if (!$jsonBet['result'] || $jsonBet['err_code']) {
            $this->last_error_code=$jsonBet['err_code'];
            $this->last_error=$jsonBet['err_desc'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[error]', 'betconstruct');
            return false;
        }

        return $jsonBet['balance'];
    }

    public function needRevealToken($error_code) {
        return in_array($error_code,[106]);
    }

    protected function _send_fsbet_request($url,$data,$office)
    {

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $time=date('d-m-Y H:i:s');

		unset($data['is_our_fs']);

        $data=[
            'time'=>$time,
            'data'=>$data,
        ];
        $data['hash']=$this->sign('FSWithdraw',$data);


        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] request bet: ' . $url.'/FSWithdraw'
            . PHP_EOL . json_encode($data, JSON_PRESERVE_ZERO_FRACTION), 'betconstruct');

        $betRequest = $parser->post($url . '/FSWithdraw', $data, true,['Content-Type: application/json']);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| response bet: ' . $url.'/FSWithdraw' . PHP_EOL . $betRequest, 'betconstruct');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url.'/Withdraw', 'betconstruct');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBet).PHP_EOL.Debug::vars($betRequest), 'betconstruct');
            return false;
        }

        if (!$jsonBet['result'] || $jsonBet['err_code']) {
            $this->last_error_code=$jsonBet['err_code'];
            $this->last_error=$jsonBet['err_desc'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[error]', 'betconstruct');
            return false;
        }

        return $jsonBet['balance'];
    }

    protected function _send_fswin_request($url,$data,$office) {
        unset($data['betAmount']);
        unset($data['round_status']);

		unset($data['is_our_fs']);

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] request FSwin: '
            .$url.'/FSDeposit'.PHP_EOL.json_encode($data,JSON_PRESERVE_ZERO_FRACTION),'betconstruct');

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $time=date('d-m-Y H:i:s');
		

        $data=[
            'time'=>$time,
            'data'=>$data,
        ];
        $data['hash']=$this->sign('FSDeposit',$data);

        $winRequest = $parser->post($url.'/FSDeposit',$data,true,['Content-Type: application/json']);

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['data']['roundId'].'] time: |'.$response_time.'| response win: '.$url.'/FSDeposit'.PHP_EOL.$winRequest,'betconstruct');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url.'/Deposit', 'betconstruct');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (!$jsonWin['result'] || $jsonWin['err_code']) {
            $this->last_error_code=$jsonWin['err_code'];
            $this->last_error=$jsonWin['err_desc'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'betconstruct');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_win_request($url,$data,$office) {
		
		if($data['is_our_fs']) {
            $data['gameId']='agt-promowin';
        }
        unset($data['is_our_fs']);
		
        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] request win: '
            .$url.'/Deposit'.'win'.PHP_EOL.json_encode($data,JSON_PRESERVE_ZERO_FRACTION),'betconstruct');

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $time=date('d-m-Y H:i:s');

        
        $data=[
            'time'=>$time,
            'data'=>$data,
        ];
        $data['hash']=$this->sign('Deposit',$data);

        $winRequest = $parser->post($url.'/Deposit',$data,true,['Content-Type: application/json']);

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['data']['roundId'].'] time: |'.$response_time.'| response win: '.$url.'/Deposit'.PHP_EOL.$winRequest,'betconstruct');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url.'/Deposit', 'betconstruct');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (!$jsonWin['result'] || $jsonWin['err_code']) {
            $this->last_error_code=$jsonWin['err_code'];
            $this->last_error=$jsonWin['err_desc'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'betconstruct');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_betwin_request($url,$data,$office) {

		if($data['is_our_fs']) {
            $data['gameId']='agt-promowin';
        }
        unset($data['is_our_fs']);

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] request betwin: '
            .$url.'/WithdrawDeposit'.PHP_EOL.json_encode($data,JSON_PRESERVE_ZERO_FRACTION),'betconstruct');

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $time=date('d-m-Y H:i:s');

        $data=[
            'time'=>$time,
            'data'=>$data,
        ];
        $data['hash']=$this->sign('WithdrawDeposit',$data);

        $winRequest = $parser->post($url.'/WithdrawDeposit',$data,true,['Content-Type: application/json']);

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['data']['roundId'].'] time: |'.$response_time.'| response betwin: '.$url.'/WithdrawDeposit'.PHP_EOL.$winRequest,'betconstruct');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| ERROR response betwin: <' . $parser->error . '> ' . $url.'/WithdrawDeposit', 'betconstruct');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (!$jsonWin['result'] || $jsonWin['err_code']) {
            $this->last_error_code=$jsonWin['err_code'];
            $this->last_error=$jsonWin['err_desc'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'betconstruct');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_jp_request($url,$data,$office) {

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundId'].'] request jp: '
            .$url.'/JackpotDeposit'.PHP_EOL.json_encode($data,JSON_PRESERVE_ZERO_FRACTION),'betconstruct');

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $time=date('d-m-Y H:i:s');

		unset($data['is_our_fs']);

        $data=[
            'time'=>$time,
            'data'=>$data,
        ];
        $data['hash']=$this->sign('JackpotDeposit',$data);

        $winRequest = $parser->post($url.'/JackpotDeposit',$data,true,['Content-Type: application/json']);

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['data']['roundId'].'] time: |'.$response_time.'| response jp: '.$url.'/JackpotDeposit'.PHP_EOL.$winRequest,'betconstruct');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['roundId'] . '] time: |' . $response_time . '| ERROR response betwin: <' . $parser->error . '> ' . $url.'/JackpotDeposit', 'betconstruct');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (!$jsonWin['result'] || $jsonWin['err_code']) {
            $this->last_error_code=$jsonWin['err_code'];
            $this->last_error=$jsonWin['err_desc'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'betconstruct');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_cancel_request($url,$data,$office) {

        $parser = new Parser('betconstruct');
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $time=date('d-m-Y H:i:s');

        if($data['is_our_fs']) {
            $data['gameId']='agt-promowin';
        }
        unset($data['is_our_fs']);

		foreach($data as $k=>$v) {
            if(!in_array($k,['token','transactionId','gameId'])) {
                unset($data[$k]);
            }
        }

        $data=[
            'time'=>$time,
            'data'=>$data,
        ];
        $data['hash']=$this->sign('RollbackTransaction',$data);

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['data']['transactionId'].'] request cancel: '.$url.'/RollbackTransaction'
            .PHP_EOL.json_encode($data,JSON_PRESERVE_ZERO_FRACTION),'betconstruct');

        $cancelRequest = $parser->post($url.'/RollbackTransaction',$data,true,$this->_send_headers(json_encode($data)));

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['data']['transactionId'].'] time: |'.$response_time.'| response cancel: '.$url.'/RollbackTransaction'.PHP_EOL.$cancelRequest,'betconstruct');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['data']['transactionId'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $url.'/RollbackTransaction', 'betconstruct');
            return false;
        }

        $jsonCancel=json_decode($cancelRequest,1);

        if(!$jsonCancel) {
            return false;
        }

        if (!$jsonCancel['result'] || $jsonCancel['err_code']) {
            $this->last_error_code=$jsonCancel['err_code'];
            $this->last_error=$jsonCancel['err_desc'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel[error]', 'betconstruct');
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

        if($need_send_betrequest && $params['amount']==0 && $params['win']==0 && $params['bettype']!='free') {
            return true;
        }


        $this->round_num=$round_num;
        $this->fin=(int) $fin;



        $transaction_id=$this->commandId();
        $this->request_id=$transaction_id;

        //bet
        $data=[
            "token" => $session_token,
            "transactionId" => "".$transaction_id,
            "roundId" => "".$round_num,
            "gameId" => $params['game'],
            "currencyId" => $o->currency->code,
            "betAmount" => (double) rtrim(sprintf('%.'.$mult.'F',$amount),'0'),
        ];





        $this->setUpEnv($u->office->is_test);

        $url=$this->_url;

        $request_type='betwin';

        if($need_send_betrequest && $fin) {
            $data['betAmount']=$data['betAmount'];
            $data['winAmount']=(double) rtrim(sprintf('%.'.$mult.'F',$win),'0');
            $data['round_status']=3;

        }
        //bet
        elseif($need_send_betrequest) {
            $request_type='bet';
            $data['round_status']=1;
//            unset($data['isJackpot']);
        }
        //win
        else {
            $request_type='win';
            $data['winAmount']=(double) rtrim(sprintf('%.'.$mult.'F',$win),'0');
            $data['round_status']=3;
            unset( $data['betAmount']);
        }

        if($params['bettype']=='jp') {
            $request_type='win';
            $data['winAmount']=(double) rtrim(sprintf('%.'.$mult.'F',$win),'0');
            $data['round_status']=3;
            $data['gameId']=bet::getJP()->game;
            unset( $data['betAmount']);
        }

        $data['betInfo']="";

		$data['is_our_fs']=$params['is_luckyspin'] || $params['is_cashback'];

        if($request_type=='bet' || $request_type=='betwin') {

            if(($params['freeroundID']??0)>0) {
                $request_type='fswin';
            }

	
            $method='_send_'.$request_type.'_request';

            $new_balance = $this->$method($url, $data, $u->office);

            if ($new_balance === false && !th::isMoonGame($data['gameId'])) {
				
				if($this->last_error_code>0) {
                       throw new Exception_ApiResponse('bet rejected.');
                }
				
                //repeat
                logfile::create(date('Y-m-d H:i:s') . 'REPEAT START: '.print_r($data,1), 'betconstruct');
                $new_balance = $this->$method($url, $data, $u->office);
                logfile::create(date('Y-m-d H:i:s') . 'REPEAT END: '.Debug::vars($new_balance), 'betconstruct');
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

                        if(!$this->last_error_code) {
                            if ($can_cancel && !$this->_send_cancel_request($url, $data, $u->office)) {
                                $this->_wrongBetType = 'cancel';
                                th::sendCurlError('betconstruct');
                                return false;
                            }
                        }

                        throw new Exception_ApiResponse('bet rejected.');

                        $this->_wrongBetType = 'repeat';
                        th::sendCurlError('betconstruct');
                        return false;
                    }
                }
            }
            elseif($new_balance === false && th::isMoonGame($data['gameId'])) {
                $can_cancel=false;
                if($request_type=='bet') {
                    $can_cancel=true;
                }
                if($request_type=='betwin') {
                    $can_cancel=true;
                }

                if(!$this->last_error_code) {
                    if ($can_cancel && !$this->_send_cancel_request($url, $data, $u->office)) {
                        $this->_wrongBetType = 'cancel';
                        th::sendCurlError('betconstruct');
                        return false;
                    }
                }

                throw new Exception_ApiResponse('bet rejected.');
            }
        }
        else {

            $request_type='_send_'.$request_type.'_request';

            if(($params['freeroundID']??0)>0) {
                $request_type='_send_fswin_request';
            }

            $new_balance = $this->$request_type($url,$data,$u->office);

            if($new_balance===false) {
                //repeat
                $new_balance = $this->$request_type($url,$data,$u->office);
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

        $b = new Model_WrongbetBetConstruct();
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

        $bets = db::query(1,'select * from wrongbetsbetconstruct where user_id=:u_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->execute(null,'Model_WrongbetBetConstruct');


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

                $fin=$bet->fin;
                $round_num=$bet->round_num;


                $this->round_num=$round_num;
                $this->fin=(int) $fin;


                //bet
                $data=[
                    "token" => $session_token,
                    "transactionId" => "".$bet->request_id,
                    "roundId" => "".$round_num,
                    "gameId" => $bet->game,
                    "currencyId" => $currency->code,
                    "betAmount" => (double) rtrim(sprintf('%.'.$mult.'F',$amount),'0'),
                ];

				$data['is_our_fs']=in_array($bet->is_freespin,[1,3]);

                $need_send_betrequest=true;

                if($bet->poker_bet_id>0) {
                    $need_send_betrequest=false;
                }
                elseif($bet->initial_id>0) {
                    $need_send_betrequest=false;
                }

                $this->setUpEnv($u->office->is_test);

                $url=$this->_url;

                $request_type='betwin';

                if($need_send_betrequest && $fin) {
                    $data['betAmount']=$data['betAmount'];
                    $data['winAmount']=(double) rtrim(sprintf('%.'.$mult.'F',$win),'0');
                    $data['round_status']=3;

                }
                //bet
                elseif($need_send_betrequest) {
                    $request_type='bet';
                    $data['round_status']=1;
                }
                //win
                else {
                    $request_type='win';
                    $data['winAmount']=(double) rtrim(sprintf('%.'.$mult.'F',$win),'0');
                    $data['round_status']=3;

                    unset( $data['betAmount']);
                }
				
				if($bet->game_type=='jp') {
                    $request_type='win';
                    $data['winAmount']=(double) rtrim(sprintf('%.'.$mult.'F',$win),'0');
                    $data['round_status']=3;
                    unset( $data['betAmount']);
                }

                $data['betInfo']="";

                $is_canceled=false;

                //если была ставка-отменяем

                if($request_type=='betwin' || $request_type=='bet') {

                    $method='_send_'.$request_type.'_request';

                    unset($data['betInfo']);
                    unset($data['winAmount']);
                    unset($data['betAmount']);
                    unset($data['round_status']);
                    unset($data['roundId']);
                    unset($data['currencyId']);

                    $cancel_request = $this->_send_cancel_request($url,$data,$u->office);


                    if($cancel_request===false && empty($this->last_error_code)) {
                        $bet->try++;
                    }
                    else {
                        $bet->processed = 1;
                        $is_canceled=true;
                    }

                }
                elseif($request_type=='win') {
                    $new_balance = $this->_send_win_request($url,$data,$u->office);

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


    public function sign($method,$params=[]) {
        return md5($this->_secretkey.$params['time'].str_replace('"automaticForfeitValue":0.0','"automaticForfeitValue":0.0000',json_encode($params['data'],JSON_PRESERVE_ZERO_FRACTION)));
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
