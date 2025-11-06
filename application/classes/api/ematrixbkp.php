<?php

class Api_Ematrix extends gameapi
{

    //https://site-domain.local/api23dev/launch/icecream100?currency=BRL&game-id=icecream100&lang=en-US&session-token=8a240058-3e03-4111-ba51-89968e381ece&user-id=60099691651553604_BRL
    public $key, $guid, $platform, $gameName, $session_token;
    protected $_url = '';
    protected $_password = '67V45tumtysWWqoI';
    private $_token = '';
    protected $_wrongBetType='';
    public $bet_transaction_id;
    public $win_transaction_id;
    public $cancel_transaction_id;

    protected function _send_headers()
    {
        return [
            'Content-Type: application/json',
        ];
    }

    public function setMode($test=true) {
        if($test) {
            $this->_token='jIA!H+uuI6|2@?IXMrkZvU=BOGJ#r9I)';
            return;
        }
        $this->_token = 'XkF|HBVHnPWBRY].gZ^s3MtRdH[)bE';
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


    public function checkUser($auth_data, $office_id)
    {
        $u = new Model_User(['office_id' => $office_id, 'external_id' => $auth_data['UserId'], 'api' => 6]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

        $u->amount="".$auth_data['TotalBalance'];

        $u->last_play_game=$this->gameName;

        $u->api_key = $this->session_token;
        $u->api_session_id = $auth_data['Token'];
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

    public function checkOffice($currency, $partner, $test = 1)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if($test) {
            $partner.=' [TEST]';
        }

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $partner,
            'is_test' => $test
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }

        return $o->id;
    }

    public function createUser($auth_data, $office_id)
    {

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $auth_data['UserId'] . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 6;
        $u->amount="".$auth_data['TotalBalance'];
        $u->api_key = $this->session_token;
        $u->api_session_id = $auth_data['Token'];
        $u->visible_name = $auth_data['UserName'];
        $u->external_id = $auth_data['UserId'];
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createOffice($currency, $partner, $test = 1)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o = new Model_Office;

        if($test) {
            $partner.=' [TEST]';
        }

        $o->currency_id = $currency->id;
        $o->visible_name = "EveryMatrix $partner {$currency->code}";
        $o->external_name = $partner;

        $o->apienable = 1;
        $o->apitype = 6;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
		$o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;

        $o->gameapiurl = $this->_url;

        $o->bonus_diff_last_bet = 8;
        $o->enable_bia = time();
        $o->rtp = 96;
        $o->owner = 1066;

        $o->dentabs = $currency->default_den;
        $o->default_dentab = $currency->default_dentab;
        $o->k_to_jp = 0.005;
        $o->k_max_lvl = $currency->default_k_max_lvl;
        $o->enable_jp = 1;

        $o->enable_moon_dispatch=1;

        $o->games_rtp = 96;
        $o->gameui = 1;

        $o->is_test = $test;
        $o->seamlesstype = 1;

        $o->secretkey = 'tySxZqkkdbNs';

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
                Where g.provider = 'our' and brand ='agt' and show=1 and g.category!='coming' and g.name != 'supabets'
SQL;

            db::query(Database::INSERT, $sql_games)
                ->param(':office_id', $o->id)
                ->execute();


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

    public function auth() {

        $url=$this->_url.'/Authenticate';
        $parser = new Parser();

        $data=[
            "LaunchToken" => $this->session_token,
            "RequestScope" => ""
        ];

        logfile::create(date('Y-m-d H:i:s') . ' [auth '.$this->session_token.'] request: ' . $url.PHP_EOL.print_r($data,1).PHP_EOL, 'ematrix');
        $start_time = microtime(1);

        $r=$parser->post($url,$data,true,$this->_send_headers());

        $response_time = microtime(1) - $start_time;
        logfile::create(date('Y-m-d H:i:s') . ' [auth '.$this->session_token.'] time: |' . $response_time . '| response: ' . $url . PHP_EOL . $r, 'ematrix');

        if (!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [ERROR auth ' . $this->session_token . '] time: |' . $response_time . '| ERROR response: <' . $parser->error . '> ' . $url, 'ematrix');
            return false;
        }

        $jr = json_decode($r, 1);

        if (!$jr) {
            return false;
        }

        if (!isset($jr['Status']) || $jr['Status'] != 'Ok') {
            return false;
        }

        if (!isset($jr['TotalBalance']) || (float)$jr['TotalBalance'] < 0) {
            return false;
        }

        return $jr;
    }

    public function checkBalance($login,$office_id,$url,$sess_id=null)
    {

        $user=new Model_User($login);

        if(!$user->loaded()) {
            throw new Exception('user not loaded');
        }

        $url=$url.'/GetBalance';

        $parser = new Parser();


        $data = [
            'Token'=>auth::getCustomSessionId($user->id, guid::create()),
            'Currency'=>$user->office->currency->code,
            'Hash'=>$this->hash('GetBalance'),
        ];

        logfile::create(date('Y-m-d H:i:s') . ' [' . $user->office_id . '] request: ' . $url . PHP_EOL . json_encode($data, 1).PHP_EOL, 'ematrix');

        $start_time = microtime(1);

        $r=$parser->post($url,$data,true,$this->_send_headers());

        $response_time = microtime(1) - $start_time;
        logfile::create(date('Y-m-d H:i:s') . ' [' . $user->office_id . '] time: |' . $response_time . '| response: ' . $url . PHP_EOL . $r. PHP_EOL, 'ematrix');


        if (!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $user->office_id . '][' . $user->id . '] time: |' . $response_time . '| ERROR response: <' . $parser->error . '> ' . $url, 'ematrix');
            return false;
        }

        $jr = json_decode($r, 1);

        if (!$jr) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' H1 '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jr['Status']) || $jr['Status'] != 'Ok') {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Status not Ok '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jr['Currency']) || ($jr['Currency']!=$data['Currency'])) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Currency different ['.$jr['Currency'].'!='.$data['Currency'].'] '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jr['TotalBalance']) || (float)$jr['TotalBalance'] < 0) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' TotalBalance bad '.PHP_EOL, 'ematrix');
            return false;
        }

        return $jr['TotalBalance'];
    }

    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $cashier_url = false)
    {
        $domain = kohana::$config->load('static.gameapi_domen_ematrix');

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

    public function hash($method){

	//return 'null';

        $str=implode('',[
            $method,
            date('Y:m:d:h'),
            $this->_password,
        ]);
        logfile::create(date('Y-m-d H:i:s') . ' [HASH '.$method.': '.date('Y:m:d:h').'; '.$str.'; md5: '.md5($str).']', 'ematrix');
        return md5($str);
    }

    public function isNeedSend($url)
    {
        return true;
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
        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['RoundId'] . '] request bet: ' . $url . PHP_EOL . json_encode($data, 1).PHP_EOL.print_r($this->_send_headers(),1), 'ematrix');

        $parser = new Parser();

        $start_time = microtime(1);

        $betRequest = $parser->post($url . '/Bet', $data, true, $this->_send_headers());

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['RoundId'] . '] time: |' . $response_time . '| response bet: ' . $url . PHP_EOL . $betRequest, 'ematrix');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['RoundId'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url, 'ematrix');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet', 'ematrix');

            return false;
        }

        if (!isset($jsonBet['Status']) || $jsonBet['Status'] != 'Ok') {
			if(isset($jsonBet['ErrorDescription'])) {
                $this->last_error = __($jsonBet['ErrorDescription']);
            }
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Status not Ok '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jsonBet['Currency']) || ($jsonBet['Currency']!=$data['Currency'])) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Currency different ['.$jsonBet['Currency'].'!='.$data['Currency'].'] '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jsonBet['TotalBalance']) || (float) $jsonBet['TotalBalance'] < 0) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' TotalBalance bad '.PHP_EOL, 'ematrix');
            return false;
        }

        return $jsonBet['TotalBalance'];
    }

    protected function _send_win_request($url,$data,$office) {
        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['RoundId'].'] request win: '.$url.PHP_EOL.json_encode($data,1).PHP_EOL.print_r($this->_send_headers(),1),'ematrix');

        $parser = new Parser();

        $start_time = microtime(1);

        $winRequest = $parser->post($url.'/Win',$data,true,$this->_send_headers());

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['RoundId'].'] time: |'.$response_time.'| response win: '.$url.PHP_EOL.$winRequest,'ematrix');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['RoundId'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url, 'ematrix');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if (!$jsonWin) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin', 'ematrix');
            return false;
        }

        if (!isset($jsonWin['Status']) || $jsonWin['Status'] != 'Ok') {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Status not Ok '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jsonWin['Currency']) || ($jsonWin['Currency']!=$data['Currency'])) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Currency different ['.$jsonWin['Currency'].'!='.$data['Currency'].'] '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jsonWin['TotalBalance']) || (float) $jsonWin['TotalBalance'] < 0) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' TotalBalance bad '.PHP_EOL, 'ematrix');
            return false;
        }

        return $jsonWin['TotalBalance'];
    }

    protected function _send_cancel_request($url,$data,$office) {

        foreach($data as $k=>$v) {
            if(!in_array($k,['Token','ExternalId','Hash'])) {
                unset($data[$k]);
            }
        }

        $data['CanceledExternalId']=$data['ExternalId'];
        $data['ExternalId']="".$this->commandId();
        $data["Hash"] = $this->hash('Cancel');

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['CanceledExternalId'].'] request cancel: '.$url.PHP_EOL.json_encode($data,1).PHP_EOL.print_r($this->_send_headers(),1),'ematrix');

        $parser = new Parser();

        $start_time = microtime(1);

        $cancelRequest = $parser->post($url.'/Cancel',$data,true,$this->_send_headers());

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['CanceledExternalId'].'] time: |'.$response_time.'| response cancel: '.$url.PHP_EOL.$cancelRequest,'ematrix');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['CanceledExternalId'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $url, 'ematrix');
            return false;
        }
        $jsonCancel=json_decode($cancelRequest,1);

        if (!$jsonCancel) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel', 'ematrix');
            return false;
        }

        if (!isset($jsonCancel['Status']) || $jsonCancel['Status'] != 'Ok') {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Status not Ok '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jsonCancel['Currency']) || ($jsonCancel['Currency']!=$data['Currency'])) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' Currency different ['.$jsonCancel['Currency'].'!='.$data['Currency'].'] '.PHP_EOL, 'ematrix');
            return false;
        }

        if (!isset($jsonCancel['TotalBalance']) || (float) $jsonCancel['TotalBalance'] < 0) {
            logfile::create(date('Y-m-d H:i:s') . PHP_EOL.' TotalBalance bad '.PHP_EOL, 'ematrix');
            return false;
        }

        return $jsonCancel['TotalBalance'];
    }

    public function bet($login,$url, $params=[],$is_repeat=false) {

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            throw Exception('user not found');
        }

        dbredis::instance()->select(0);
        $session_token=auth::getCustomSessionId($u->id,false);


        if(!$session_token){
            throw new Exception('session expired');
        }

        $o=$u->office;

        $amount = "".$params['amount'];
        $win = "".$params['win'];
        $bet_id = $params['bet_id'];

        $fin=true;
        $round_num=$bet_id;

        if(isset($params['poker_bet_id']) && $params['poker_bet_id']>0) {
            $round_num=$params['poker_bet_id'];
        }
        elseif(isset($params['initial_id']) && $params['initial_id']>0) {
            $round_num=$params['initial_id'];
        }
        elseif(in_array($params['game'],['tothemoon']) && ($params['win']==0)) {
            $fin=false;
        }
        elseif(in_array($params['game'],['acesandfaces','jacksorbetter','tensorbetter']) && $params['bettype']=='normal') {
            $fin=false;
        }

        $transaction_id="".$this->commandId();

        //bet
        $data=[
            "Token" => $session_token,
            "Amount" => $amount,
            "Currency" => $o->currency->code,
            "ExternalId" => $transaction_id,
            "GameId" => "".$params['game'],
            "RoundId" => $round_num,
            "Hash" => $this->hash('Bet'),
        ];

        //todo наверное не нужно нам
        /*if(bet::$jpwin>0) {
            $data['JackpotContribution']=[
                'JackpotId'=>$bet_id,
                'JackpotContributionAmount'=>"0",
            ];
        }*/

        $this->setMode($u->office->is_test==1);

        $betRequest = $this->_send_bet_request($url,$data,$o);

        if($betRequest===FALSE) {
            if(!$this->_send_cancel_request($url,$data,$o)) {
                th::sendCurlError('ematrix');
                return false;
            }
            th::sendCurlError('ematrix');
            throw new Exception_ApiResponse('bet rejected.');
        }

        $u->amount=$betRequest;

        $win_data=$data;
        $win_data["Hash"] = $this->hash('Win');

        $win_data['BetExternalId']=$data['ExternalId'];
        $win_data['ExternalId']="".$this->commandId();
        $win_data['Amount']=$win;
        $win_data['RoundEnd']=$fin;

        if($params['game']=='jp') {
            $win_data['Amount']=0;
            $win_data['GameId']=bet::getJP()->game;
            $win_data['JackpotPayout']=[
                'JackpotId'=>$bet_id,
                'JackpotPayoutAmount'=>$win,
            ];
            $win_data['RoundEnd']=true;
        }

        $winRequest = $this->_send_win_request($url,$win_data,$u->office);

        if(!$winRequest) {
            //try to send win again
            $winRequest = $this->_send_win_request($url,$win_data,$u->office);
            if(!$winRequest) {

                if(!$this->_send_cancel_request($url,$win_data,$u->office)) {
                    th::sendCurlError('ematrix');
                    return false;
                }
                else {
                    if(!$this->_send_cancel_request($url,$data,$u->office)) {

                    }
                    th::sendCurlError('ematrix');
                    return false;
                }
            }
        }

        $u->amount=$winRequest;

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

        return;

        if($bet->game=='jp') {
            $bet->game=bet::getJP()->game;
        }

        $b = new Model_WrongbetEmatrix();
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
//        $b->cancel_transaction_id = $this->cancel_transaction_id;
        $b->is_freespin = $params['is_freespin'];
        $b->created = $bet->created;
        $b->real_amount = $bet->real_amount;
        $b->real_win = $bet->real_win;
        $b->fs_id = $params['last_freespin_id']??null;
        $b->try = 0;
        $b->win_sended = 0;
        $b->save();
    }

    public function commandId(){

        $r=dbredis::instance();
        $value=$r->incr('ematrixCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('ematrixCommandId',time()*100+$ms);
            $value=$r->incr('ematrixCommandId');
        }

        return $value;


    }

    public function processWrongBets($user_id) {

        return false;

        $bets = db::query(1,'select * from wrongbetsematrix where user_id=:u_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->execute(null,'Model_WrongbetEmatrix');


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


