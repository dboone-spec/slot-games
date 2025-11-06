<?php

class Api_SoftGamings extends gameapi
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
        'test'=>['https://apiqa-site-domain.fundist.org/System/Merchants/site-domain/Callback/','ba8644d37d4b678d21dd074cff-site-domain-795b79'],
        'prod'=>['https://mpapi-site-domain.fundist.org/System/Merchants/site-domain/Callback/','ec41afa4f3ec2c4ae50c16f7ba-site-domain-ale5e2'],
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

    public function checkUser($auth_data, $office_id)
    {
        $u = new Model_User(['office_id' => $office_id, 'external_id' => $auth_data['userID'], 'api' => 8]);

        if (!$u->loaded() || $u->blocked) {
            return false;
        }

        $user_balance=bcdiv($auth_data['balance'],100,2);

        $u->amount="".$user_balance;

        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->api_key_time = time();
        $u->save();

        return $u->id;
    }

    public function createAnonym($userId){

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->office_id = -1;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 8;
        $u->amount = 0;
        $u->visible_name = $userId;
        $u->external_id = $userId;
        $u->save()->reload();

        return $u;
    }

    public function checkOffice($currency,$partner,$casino)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        $external_name='sg'.$currency.$partner.$casino;

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Can't create office with currency $currency ");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'is_test' => (int) $this->_is_test,
            'apitype'=>8
        ]);

        if (!$o->loaded() || $o->blocked) {
            return false;
        }

        return $o->id;
    }

    public function createUser($auth_data, $office_id)
    {

        $user_balance=bcdiv($auth_data['balance'],100,2);

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $auth_data['userID'] . '-' . $office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 8;
        $u->amount="".$user_balance;
        $u->api_key = $this->session_token;
        $u->api_session_id = $this->session_token;
        $u->visible_name = $auth_data['userID'];
        $u->external_id = $auth_data['userID'];
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createOffice($currency,$partner,$casino)
    {
        $currency = new Model_Currency(['code' => $currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o = new Model_Office;

        $external_name='sg'.$currency.$partner.$casino;

        $o->currency_id = $currency->id;
        $o->visible_name = "Softgamings " . ($this->_is_test ? '[TEST]' : '') . " {$currency->code} {$partner} {$casino}";
        $o->external_name = $external_name;

        $o->apienable = 1;
        $o->apitype = 8;
        $o->bank = $currency->default_bank;
        $o->use_bank = 1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max = $currency->max_bet;

        $o->gameapiurl = $this->_url;

        $o->bonus_diff_last_bet = 8;
//        $o->enable_bia = time(); //под вопросом. они не принимают ставку 0
        $o->rtp = 96;
        $o->owner = 1092;

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
                Where g.provider = 'our' and brand ='agt' and show=1 and g.category!='coming' 
                and g.name != 'supabets' and g.name != '1windoublehot'
SQL;

            db::query(Database::INSERT, $sql_games)
                ->param(':office_id', $o->id)
                ->execute();

//            $o->createProgressiveEventForOffice(); //под вопросом. они не принимают ставку 0

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

        $parser = new Parser();
        $parser->disableFailOnError();

        $data=[];
        $data['sessionID']=$this->session_token;
        $data['userID']=$u->external_id;
        $data['requestID']="".$this->commandId();


        logfile::create(date('Y-m-d H:i:s') . ' [' . $this->session_token . '] BALANCE request: ' . $this->_url.'balance'
            . PHP_EOL . json_encode($data, 1).PHP_EOL, 'softgamings');

        $start_time = microtime(1);

        $data['sign']=$this->sign('balance',$data);

        $r=$parser->post($this->_url.'/balance',$data,true);

        $response_time = microtime(1) - $start_time;
        logfile::create(date('Y-m-d H:i:s') . ' [' . $this->session_token . '] time: |' . $response_time . '| response: '
            . $this->_url.'balance' . PHP_EOL . $r, 'softgamings');

        if (!$r) {
            $this->last_error = 'bad response';
            return false;
        }

        $jr = json_decode($r, 1);

        if (!$jr) {
            return false;
        }

        if (!$this->statusIsOK($jr['status'])) {
            logfile::create(date('Y-m-d H:i:s') . '$jr[error]balance', 'softgamings');
            return false;
        }

        if (!isset($jr['balance']) || (float)$jr['balance'] < 0) {
            return false;
        }

        return $jr['balance'];
    }

    public function getGame($user_id, $lang, $force_mobile = false, $no_close = true, $closeurl = false, $cashier_url = false)
    {
        $domain = kohana::$config->load('static.gameapi_domen_softgamings');

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
        $value=$r->incr('softgamCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('softgamCommandId',time()*100+$ms);
            $value=$r->incr('softgamCommandId');
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
		
        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundID'] . '] request bet: ' . $url . '/bet'
            . PHP_EOL . json_encode($data, 1), 'softgamings');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $data['sign']=$this->sign('bet',$data);

        $betRequest = $parser->post($url . '/bet', $data, true);

        $response_time = microtime(1) - $start_time;

        logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundID'] . '] time: |' . $response_time . '| response bet: ' . $url . PHP_EOL . $betRequest, 'softgamings');

        if (!$betRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundID'] . '] time: |' . $response_time . '| ERROR response bet: <' . $parser->error . '> ' . $url, 'softgamings');
            return false;
        }
        $jsonBet = json_decode($betRequest, 1);

        if (!$jsonBet) {
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet'.PHP_EOL.Debug::vars($jsonBet).PHP_EOL.Debug::vars($betRequest), 'softgamings');
            return false;
        }

        if (!$this->statusIsOK($jsonBet['status'])) {
            $this->last_error=$jsonBet['msg'] ?? $this->getError($jsonBet['status']);
            logfile::create(date('Y-m-d H:i:s') . '$jsonBet[error]', 'softgamings');
            return false;
        }

        return $jsonBet['balance'];
    }

    protected function _send_win_request($url,$data,$office) {
        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundID'].'] request win: '
            .$url.PHP_EOL.json_encode($data,1),'softgamings');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $data['sign']=$this->sign('win',$data);

        $winRequest = $parser->post($url.'/win',$data,true);

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundID'].'] time: |'.$response_time.'| response win: '.$url.PHP_EOL.$winRequest,'softgamings');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundID'] . '] time: |' . $response_time . '| ERROR response win: <' . $parser->error . '> ' . $url, 'softgamings');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (!$this->statusIsOK($jsonWin['status'])) {
            $this->last_error=$jsonWin['msg'] ?? $this->getError($jsonWin['status']);
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'softgamings');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_betwin_request($url,$data,$office) {
		
		//$data['debugCode']=3000;
		
        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundID'].'] request betwin: '
            .$url.PHP_EOL.json_encode($data,1),'softgamings');

        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        $data['sign']=$this->sign('betwin',$data);

        $winRequest = $parser->post($url.'/betwin',$data,true);

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundID'].'] time: |'.$response_time.'| response betwin: '.$url.PHP_EOL.$winRequest,'softgamings');

        if (!$winRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundID'] . '] time: |' . $response_time . '| ERROR response betwin: <' . $parser->error . '> ' . $url, 'softgamings');
            return false;
        }

        $jsonWin=json_decode($winRequest,1);

        if(!$jsonWin) {
            return false;
        }

        if (!$this->statusIsOK($jsonWin['status'])) {
            $this->last_error_code=$jsonWin['status'];
            $this->last_error=$jsonWin['msg'] ?? $this->getError($jsonWin['status']);
            logfile::create(date('Y-m-d H:i:s') . '$jsonWin[error]', 'softgamings');
            return false;
        }

        return $jsonWin['balance'];
    }

    protected function _send_cancel_request($url,$data,$office) {

        $data['requestID']="".$this->commandId();


        $parser = new Parser();
        $parser->disableFailOnError();

        $start_time = microtime(1);

        unset($data['roundEnded']);
        unset($data['jackpotContributionFraction']);
        unset($data['jackpotContribution']);

        $data['sign']=$this->sign('refund',$data);

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundID'].'] request cancel: '.$url
            .PHP_EOL.json_encode($data,1),'softgamings');

        $cancelRequest = $parser->post($url.'refund',$data,true,$this->_send_headers(json_encode($data)));

        $response_time = microtime(1)-$start_time;

        logfile::create(date('Y-m-d H:i:s').' ['.$office->id.']['.$data['roundID'].'] time: |'.$response_time.'| response cancel: '.$url.PHP_EOL.$cancelRequest,'softgamings');

        if (!$cancelRequest) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s') . ' [' . $office->id . '][' . $data['roundID'] . '] time: |' . $response_time . '| ERROR response cancel: <' . $parser->error . '> ' . $url, 'softgamings');
            return false;
        }

        $jsonCancel=json_decode($cancelRequest,1);

        if(!$jsonCancel) {
            return false;
        }

        if (!$this->statusIsOK($jsonCancel['status'])) {
            $this->last_error_code=$jsonCancel['status'];
            logfile::create(date('Y-m-d H:i:s') . '$jsonCancel[error]', 'softgamings');
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
        $session_token=auth::getCustomSessionId($u->id,$u->api_session_id);


        if(!$session_token){
            throw new Exception('session expired');
        }


        $o=$u->office;

        $mult=$o->currency->mult ?? 2;

        $amount = "".floor($params['amount']*pow(10,$mult));
        $win = "".floor($params['win']*pow(10,$mult));
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
        elseif(in_array($params['game'],['tothemoon']) && ($params['win']==0)) {
            $fin=false;
        }
        elseif(in_array($params['game'],['acesandfaces','jacksorbetter','tensorbetter']) && $params['bettype']=='normal') {
            $fin=false;
        }

        if($need_send_betrequest && $params['amount']==0 && $params['win']==0 && $params['bettype']!='free') {
            return true;
        }

        if($params['bettype']=='free') {
            $fin=false;
            $need_send_betrequest=false;
            $round_num=$params['fg_first_bet_id'];
        }

        if($params['bettype']=='free' && $params['free_games_end']??false) {
            $fin=true;
        }

        $this->round_num=$round_num;
        $this->fin=(int) $fin;

        //cheat
        if($params['free_games_started']??false) {
            $this->fin=false;
        }

        $noBetWin=false;

        if($params['is_freespin']) {
            $need_send_betrequest=false;
            $noBetWin=true;
        }

        $transaction_id=$this->commandId();
        $this->request_id=$transaction_id;

        //bet
        $data=[
            "userID" => $u->external_id,
            "sessionID" => $session_token,
            "roundID" => "".$round_num,
            "betID" => "".$round_num,
            "amount" => (int) $amount,
            "fraction" => (int) $mult,
            "roundEnded" => (int) $this->fin,
            "requestID" => "".$transaction_id,
        ];

        if(($params['freeroundID']??0)>0) {
            $data['freeroundID']=$params['freeroundID'];
			$noBetWin=false;
        }

        $this->setUpEnv($u->office->is_test);

        $url=$this->_url;

        $request_type='betwin';

        if($o->enable_jp) {
            $data['jackpotContributionFraction']=((int) $mult)*2;
            $data['jackpotContribution']=floor(Model_Jackpot::calcNewValJP($params['amount'],$u->office)*pow(10,$data['jackpotContributionFraction']));
        }

        $data['isJackpot']=(int) ($params['game'] == 'jp');

        //betwin
        if($need_send_betrequest && $fin) {
            $data['betAmount']=$data['amount'];
            $data['winAmount']=(int) $win;
            $data['betFraction']=$data['fraction'];
            $data['winFraction']=$data['fraction'];

            $data['winID']="".$data['requestID'];

            unset($data['amount']);
            unset($data['fraction']);
        }
        //bet
        elseif($need_send_betrequest) {
            $request_type='bet';

            unset($data['isJackpot']);
        }
        //win
        else {
            $request_type='win';
            $data['winID']="".$data['requestID'];
            $data['amount']=(int) $win;
			$data["noBetWin"]=(int) $noBetWin;

            unset($data['jackpotContribution']);
            unset($data['jackpotContributionFraction']);
        }

        if($request_type=='bet' || $request_type=='betwin') {

            $method='_send_'.$request_type.'_request';

            $new_balance = $this->$method($url, $data, $u->office);

            if ($new_balance === false) {
                if($request_type=='betwin' && $this->last_error_code==2011) {
                    //repeat
                    $new_balance = $this->$method($url, $data, $u->office);
                    if ($new_balance === false) {
                        //move to wronbets as REPEAT
                        $this->_wrongBetType = 'repeat';
                        th::sendCurlError('softgamings');
                        throw new Exception_ApiResponse('bet rejected.');
                    }
                }
                else {
                    //cancel
                    if ($request_type=='bet' && !$this->_send_cancel_request($url, $data, $u->office)) {
                        //move to wronbets as CANCEL
                        $this->_wrongBetType = 'cancel';
                        th::sendCurlError('softgamings');
//                        throw new Exception_ApiResponse('cannot refund');
                        return false;
                    }
                    th::sendCurlError('softgamings');
                    return false;
                    throw new Exception_ApiResponse('bet rejected.');
                }

                $this->_wrongBetType = 'bet';
                return false;
            }
        }
        else {
            $new_balance = $this->_send_win_request($url,$data,$u->office);

            if($new_balance===false) {
                //repeat
                $new_balance = $this->_send_win_request($url,$data,$u->office);
                if($new_balance==false) {
                    $this->_wrongBetType = 'win';
                    return false;
                }
            }
        }

        $u->amount = bcdiv($new_balance,pow(10,$mult),$mult);

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

        $b = new Model_WrongbetSoftgamings();
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

        $bets = db::query(1,'select * from wrongbetssoftgamings where user_id=:u_id and processed=0 and try<6 order by bet_id')
            ->param(':u_id',$user_id)
            ->execute(null,'Model_WrongbetSoftgamings');


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
                    $amount = floor($bet->amount);
                    $win = floor($bet->win);
                }
                else {
                    $amount = (int) floor($bet->amount*pow(10,$mult));
                    $win = (int) floor($bet->win*pow(10,$mult));
                }


                //bet
                $data=[
                    "userID" => $u->external_id,
                    "sessionID" => $session_token,
                    "roundID" => "".$bet->round_num,
                    "betID" => "".$bet->round_num,
                    "amount" => (int) $amount,
                    "fraction" => (int) $mult,
                    "roundEnded" => (int) $bet->fin,
                    "requestID" => "".$bet->request_id,
                ];

                if(($bet->fs_id??0)>0) {
                    $data['freeroundID']=$bet->fs_id;
                }

                $need_send_betrequest=true;

                if($bet->poker_bet_id && $bet->poker_bet_id>0) {
                    $need_send_betrequest=false;
                }
                elseif($bet->initial_id && $bet->initial_id>0) {
                    $need_send_betrequest=false;
                }

                $this->setUpEnv($u->office->is_test);

                $url=$this->_url;

                $request_type='betwin';

                if($u->office->enable_jp) {
                    $data['jackpotContributionFraction']=((int) $mult)*2;
                    $data['jackpotContribution']=floor(Model_Jackpot::calcNewValJP($bet->amount,$u->office)*pow(10,$data['jackpotContributionFraction']));
                }

                $data['isJackpot']=(int) ($bet->game == 'jp');

                //betwin
                if($need_send_betrequest && $bet->win>0) {
                    $data['betAmount']=$data['amount'];
                    $data['winAmount']=(int) $win;
                    $data['betFraction']=$data['fraction'];
                    $data['winFraction']=$data['fraction'];

                    $data['winID']="".$data['requestID'];

                    unset($data['amount']);
                    unset($data['fraction']);
                }
                //bet
                elseif($need_send_betrequest) {
                    $request_type='bet';

                    unset($data['isJackpot']);
                }
                //win
                else {
                    $request_type='win';
                    $data['winID']="".$data['requestID'];
                    $data['amount']=(int) $win;

                    unset($data['jackpotContribution']);
                    unset($data['jackpotContributionFraction']);
                }

                if($request_type=='betwin') {

                    $method='_send_'.$request_type.'_request';

                    $new_balance = $this->$method($url, $data, $u->office);

                    if ($new_balance === false) {
                        $bet->try++;
                    }
                    else {
                        $bet->processed = 1;
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
                elseif($request_type=='bet') {
                    $cancel_request = $this->_send_cancel_request($url,$data,$u->office);
                    //todo нужно подождать ответа от софтгеймингс, по обработке возвратов
                    if($cancel_request===false && $this->last_error_code!=2012) {
                        $bet->try++;
                    }
                    else {
                        $bet->processed = 1;
                    }
                }

                $bet->save();
            }

            return $errors;
        }
        return false;
    }

    protected function recursiveArr($s,$arr) {
        ksort($arr);
        foreach($arr as $k=>$v) {
            if (is_array($v)) {
                $s = $this->recursiveArr($s, $v);
            } else {
                $s .= $k . "$v";
            }
        }
        return $s;
    }

    public function getError($code) {
        if(!isset($this->_error_codes[(int) $code])) {
            return 'Unknown error';
        }
        return $this->_error_codes[(int) $code];
    }


    public function sign($method,$params=[]) {
        $s='method'.$method;

        $s=$this->recursiveArr($s,$params);

        return md5($s.$this->_secretkey);
    }

    public function statusIsOK($code) {
        return in_array($code,[1000,1001,1002,1003,1004,1005,1006]);
    }

    protected $_error_codes=[
        1000 =>' Generic OK response.',
        1001 =>' Bet successful.',
        1002 =>' Win successful.',
        1003 =>' BetWin successful.',
        1004 =>' Refund successful.',
        1005 =>' getGamesList successful.',
        1006 =>' Balance successful.',

        2000 =>' Generic error response.',
        2001 =>' Incorrect incoming parameter/parameters. Parameter/parameters should be mentioned in “msg” response parameter if a parameter doesn’t have
    it’s own error code. ',
        2002 =>' Missing incoming parameter/parameters. Parameter/parameters should be mentioned in “msg” response parameter.',
        2003 =>' Security error, incorrect sign.',
        2004 =>' Security error, IP access denied.',
        2005 =>' Non-unique request ID.',
        2006 =>' Non-unique bet ID.',
        2007 =>' Non-unique win ID.',
        2008 =>' Unknown game ID.',
        2009 =>' Unknown user ID.',
        2010 =>' Session not found or expired.',
        2011 =>' Win error with OK incoming params. Request may be repeated.',
        2012 =>' Referrent refund transaction not found.',

        2100 =>' Jackpot interaction in a non-jackpot game. (Passed jackpotContribution on a non-jackpot game, jackpot win on a non-jackpot game, etc.).',
        2101 =>' Missing jackpotContribution on a jackpot game.',

        3000 =>' Generic balance error.',
        3001 =>' Insufficient balance.',

        4000 =>' Generic freeround OK response.',
        4001 =>' Freeround created successfully.',
        4002 =>' Freeround deleted successfully.',
        4003 =>' Freeround info retrieved successfully.',
        4004 =>' BetValues info retrieved successfully.',

        5000 =>' Generic freeround error response.',
        5001 =>' Freeround campaign not found.',
        5002 =>' Non-ISO 8601 compliant expireDate.',
        5003 =>' expireDate less than current date.',
        5004 =>' Specified lines amount on a game not supporting lines specification in freerounds.',
        5005 =>' Unsupported betValue.',

    ];
}
