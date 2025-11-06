<?php

class Api_Infin extends gameapi{

    //http://mega/api21/launch?key=some_key&gameName=stalker&partner=some_partner&lang=en&platform=desktop
    public $key,$guid,$platform,$gameName,$wlid;
    protected $_url = '';

    public $canRestoreSession=true;
    public $wrongBetProcessed=0;
    protected $jp_repeat_count=2;

    public function session(){

        return 'site-domain';
    }

    public function setURL($url) {
        $this->_url=$url;
    }

    public function checkGame($office_id) {


        $g = new Model_Game(['name'=>$this->gameName]);

        if(!$g->loaded() || $g->show==0) {
            return false;
        }

        $og = new Model_Office_Game([
            'office_id' => $office_id,
            'game_id' => $g->id,
        ]);

        if(!$og->loaded() || $og->enable==0) {
            return false;
        }

        return true;

    }

    public function checkUser($userId,$balance,$office_id) {
        $u=new Model_User(['external_id'=>$userId,'api'=>4]);

        if(!$u->loaded() || $u->blocked) {
            return false;
        }

        if($u->office_id<0) {
            $u->name = $userId.'-'.$office_id;
            $u->office_id = $office_id;
        }

        $u->amount=bcdiv($balance,100,2);
        $u->api_key = $this->guid;
        $u->api_session_id=$this->guid;
        $u->api_key_time = time();

        $u->last_play_game=$this->gameName;

        $u->save();


        return $u->id;
    }

    public function checkOffice($currency,$partner,$test=1) {
        $currency=new Model_Currency(['code'=>$currency,'source'=>'agt']);

        if($test) {
            $partner.=' [TEST]';
        }


        if (!$currency->loaded()  || $currency->disable!=0 ){
            throw new Exception("Cann't create office with currency $currency ");
        }


        $o=new Model_Office([
                'currency_id'=>$currency->id,
                'external_name'=>$partner
        ]);

        if(!$o->loaded() || $o->blocked) {
            return false;
        }
		
		if($o->gameapiurl!=$this->_url) {
            $o->gameapiurl=$this->_url;
            $o->save();
        }

        return $o->id;
    }

    public function createUser($userId,$office_id,$balance){

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->name = $userId.'-'.$office_id;
        $u->office_id = $office_id;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 4;
        $u->amount = bcdiv($balance,100,2);
        $u->api_key = $this->guid;
        $u->api_session_id = $this->guid;
        $u->visible_name = $userId;
        $u->external_id = $userId;
        $u->api_key_time = time();
        $u->save()->reload();

        return $u->id;
    }

    public function createAnonym($userId){

        $u = new Model_User();

        $pas = mt_rand(10000000, 999999999);

        $u->office_id = -1;
        $u->salt = rand(1, 10000000);
        $u->password = auth::pass($pas, $u->salt);
        $u->api = 4;
        $u->amount = 0;
        $u->visible_name = $userId;
        $u->external_id = $userId;
        $u->save()->reload();

        return $u;
    }

    protected function _correctLimits($partner,$currency_code,Model_Office &$o){
        $currency_code=UTF8::strtoupper($currency_code);

		if(UTF8::strpos($partner,'1win-prod')!==FALSE && $currency_code=='VES') {
            $o->bet_min=10;
        }

        if(UTF8::strpos($partner,'redbox')!==FALSE) {

            $a=[
                'EUR'=>['min'=>0.2,'max'=>40],
                'PLN'=>['min'=>1,'max'=>200],
                'RUB'=>['min'=>15,'max'=>3000],
                'TRY'=>['min'=>4,'max'=>800],
                'USD'=>['min'=>0.20,'max'=>50.00],
                'ARS'=>['min'=>40,'max'=>8000],
                'MXN'=>['min'=>4,'max'=>800],
                'PEN'=>['min'=>1,'max'=>200],
                'CLP'=>['min'=>180,'max'=>36000],
                'ZAR'=>['min'=>4,'max'=>800],
                'NOK'=>['min'=>2,'max'=>400],
                'BRL'=>['min'=>1,'max'=>240],
                'CAD'=>['min'=>0.5,'max'=>80],
                'AUD'=>['min'=>0.5,'max'=>80],
                'CHF'=>['min'=>0.2,'max'=>40],
                'CZK'=>['min'=>5,'max'=>1000],
                'NZD'=>['min'=>0.4,'max'=>80],
                'INR'=>['min'=>20,'max'=>3600],
                'UAH'=>['min'=>10,'max'=>1600],
                'JPY'=>['min'=>25,'max'=>5200],
                'AZN'=>['min'=>0.5,'max'=>80],
                'UZS'=>['min'=>2600,'max'=>520000],
                'SEK'=>['min'=>2,'max'=>400],
                'KZT'=>['min'=>100,'max'=>20000],
                'GEL'=>['min'=>0.5,'max'=>120],
                'BTC'=>['min'=>0.000002,'max'=>0.000125],
				'BDT'=>['min'=>25,'max'=>4800],
            ];

            if(isset($a[$currency_code])) {
                $o->bet_min=$a[$currency_code]['min'];
                $o->bet_max=$a[$currency_code]['max'];
            }
        }
    }
    public function createOffice($currency,$partner,$test=1){
        $currency=new Model_Currency(['code'=>$currency,'source'=>'agt']);

        if($test) {
            $partner.=' [TEST]';
        }

        if (!$currency->loaded()  || $currency->disable!=0 ){
            throw new Exception("Cann't create office with currency $currency ");
        }

        $o=new Model_Office;

        $o->currency_id=$currency->id;
        $o->external_name = $partner;
        $o->visible_name="Infin $partner {$currency->code}";

        $o->apienable=1;
        $o->apitype=4;
        $o->bank=$currency->default_bank;
        $o->use_bank=1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max=$currency->max_bet;

        $this->_correctLimits($partner,$currency->code,$o);

        $o->gameapiurl=$this->_url;

        $o->bonus_diff_last_bet=8;
        $o->enable_bia=time();
        $o->rtp=96;
        $o->owner=1042;

        $o->dentabs=$currency->default_den;
        $o->default_dentab=$currency->default_dentab;
        $o->k_to_jp=0.005;
        $o->k_max_lvl=$currency->default_k_max_lvl;
        $o->enable_jp=1;

        $o->enable_moon_dispatch=1;

        $o->games_rtp=97;
        $o->gameui=1;

        $o->promopanel=1;

        $o->is_test = $test;
        $o->seamlesstype = 1;

        $o->secretkey='TYBrQZeG8P';

        database::instance()->begin();

        //TODO поификсить создание джекпотаов

        try {
            //создаем игры здесь
            $o->need_create_default_games=false;
            $o->save()->reload();


            database::instance()->direct_query('insert into person_offices (person_id,office_id)
                                        values ('.$o->owner.','.$o->id.'),(1043,'.$o->id.'),(1053,'.$o->id.')');


            $sql_games = <<<SQL
                insert into office_games(office_id, game_id, enable)
                Select :office_id, g.id, 1
                From games g
                Where g.provider = 'our' and g.branded=0 and brand ='agt' and show=1 and infin_show=1 and g.category!='coming'
SQL;

            db::query(Database::INSERT,$sql_games)
                    ->param(':office_id',$o->id)
                    ->execute();

            $o->createProgressiveEventForOffice();

            $redis = dbredis::instance();
            $redis->select(1);
            $redis->set('jpa-'.$o->id,1);

            for($i=1;$i<=4;$i++)
            {

                $redis->set('jpHotPercent-'.$o->id.'-'.($i),0.02);

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
	
	public function getDomain(Model_User $user)
    {
        if($user->id==4600700 && defined('X_DOMAIN')) {
            return 'https://'.X_DOMAIN;
        }

        $static = kohana::$config->load('static.gameapi_domen_infin');

        return $static;
    }

    public function getGame($user_id,$lang,$force_mobile=false,$no_close=true,$closeurl=false,$cashier_url=false) {
        $user = new Model_User($user_id);
        $domain = $this->getDomain($user);

        $g = new Model_Game(['name'=>$this->gameName]);

        $link = $g->get_link($domain);

        $user->api_key = guid::create();
        $user->lang=$lang;
        $user->save();

        $link_params=[];

        $link_params[]='user='.$user->api_name;
        $link_params[]='token='.$user->api_key;
        $link_params[]='force_mobile='.((int) $force_mobile);

        if($closeurl){
            if($closeurl!==urldecode($closeurl)) {
                $closeurl=urlencode($closeurl);
            }

            $link_params[]='closeurl='.$closeurl;
        }

        if($cashier_url) {
            $link_params[]='cashierurl='.$cashier_url;
        }

        $link_params[]='no_close='.((int) $no_close);

        $link.='?'.implode('&',$link_params);
        return $link;
    }

    public function commandId(){

        $r=dbredis::instance();
        $value=$r->incr('infinCommandId');
        //if commandId is not set
        if ($value<10000){

            $ms= microtime(true);
            $ms=$ms-floor($ms);
            $ms=$ms*100;
            $ms=round($ms);

            $r->set('infinCommandId',time()*100+$ms);
            $value=$r->incr('infinCommandId');
        }

        return $value;


    }


    public static function time($time=null,$ms=null) {

        if (is_null($time)){
            $time=time();
        }


        if (is_null($ms)){
            $ms= microtime(true);
            $ms=$ms-floor($ms);

            $ms=$ms*1000;
            $ms=round($ms);
            if ($ms==0) {
                $ms='000';
            }
            elseif ($ms<10) {
                $ms='00'.$ms;
            }
            elseif ($ms<100) {
                $ms='0'.$ms;
            }

        }

        return date('Y-m-d\TH:i:s.',$time).$ms;

    }

    protected $curl;


    public function send($data,$extra_log=[]){

        $extra_log_str='';

        if(!empty($extra_log)) {
            $extra_log_str = '['.implode('; ',$extra_log).']';
        }

        $url=$this->_url;

        $start_time = microtime(1);

        $guid=guid::create();

        logfile::create(date('Y-m-d H:i:s')." request [$guid]: $extra_log_str $url \r\n $data",'infin');

        $this->curl= curl_init();
        curl_setopt($this->curl, CURLOPT_URL,$url);
        curl_setopt($this->curl, CURLOPT_ENCODING, 0);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 7);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));

        $ans=curl_exec($this->curl);

        $error = curl_error($this->curl);

        $http_code=curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        curl_close($this->curl);

        $response_time = microtime(1)-$start_time;


        logfile::create(date('Y-m-d H:i:s')." response [$guid][$http_code] [$response_time]: $extra_log_str $ans $error",'infin');

        if(!$ans) {
            th::sendCurlError('infin');
        }

        return $ans;


    }

    /**
     * @return  SimpleXMLElement
     */
    public function parse($str){

        if($str=='Need roundbetwin command repeat') {
            return false;
        }

        try {
            $xml = simplexml_load_string($str);
        } catch (Exception $ex) {
            return false;
        }

        return $xml;
    }


    public function isNeedSend($url){
        return true;
    }

    public function getUrl($url) {
        $this->_url=$url;
        return $this->_url;
    }

    public function enter(){

        $session=$this->session();
        $time=$this->time();
        $id=$this->commandId();

        $xml=<<<XMLL
<server session="$session" time="$time">
<enter id="$id" guid="$this->guid" key="$this->key" platform="$this->platform">
<game name="$this->gameName"/>
</enter>
</server>
XMLL
;
        $data=$this->send($xml);
        $data=$this->parse($data);

        if(!$data) {
            throw new Exception_ApiResponse('connection problem. '.$xml);
        }

        /*$error_code = $data->roundbetwin->error->attributes()->code;

        if(in_array($error_code,['GAME_NOT_ALLOWED','INVALID_KEY','KEY_EXPIRED','MAX_LOGIN_EXCEED','USER_BLOCKED'])) {
            return false;
        }*/

        if(!$data->enter || $data->enter->attributes()->result!='ok') {
            logfile::create(date('Y-m-d H:i:s')." Enter response ERROR: ".print_r($data->command,1),'infin');
            throw new Exception('invalid response');
        }

        $this->wlid = (string) $data->enter->user->attributes()->wlid;

        $r=['balance'=> (string) $data->enter->balance->attributes()->value,
            'currency'=> (string) $data->enter->balance->attributes()->currency,
            'userId'=> $this->wlid, // unique number differs for different currencies
        ];


        return $r;
    }

    public function reenter(Model_User $u,$game){

        if(!$u->loaded()) {
            throw Exception('user not found');
        }

        $new_guid=guid::create();

        $session=$this->session();
        $time=$this->time();
        $id=$this->commandId();

        $wlid=$u->external_id;

        $xml=<<<XMLL
<server session="$session" time="$time">
<re-enter id="$id" guid="$this->guid" new-guid="$new_guid" wlid="$wlid">
<game name="$game"/>
</re-enter>
</server>
XMLL
        ;
        $data=$this->send($xml);
        $data=$this->parse($data);

        if(!$data) {
            throw new Exception_ApiResponse('connection problem. '.$xml);
        }

        if(!$data->{"re-enter"} || $data->{"re-enter"}->attributes()->result!='ok') {
            logfile::create(date('Y-m-d H:i:s')." ReEnter response ERROR: ".print_r($data->command,1),'infin');
            throw new Exception('invalid response');
        }

        dbredis::instance()->select(0);
        if(true || $u->office->is_test) {
            $this->setCustomSessionId($u->id,$game,$new_guid);
        }
        else {
            auth::setCustomSessionId($u->id,$new_guid);
        }
        $this->guid=$new_guid;
        $u->last_play_game=$game;
        $u->save();

        $this->wlid = (string) $data->{"re-enter"}->user->attributes()->wlid;

        $r=['balance'=> (string) $data->{"re-enter"}->balance->attributes()->value,
            'currency'=> (string) $data->{"re-enter"}->balance->attributes()->currency,
            'userId'=> $this->wlid, // unique number differs for different currencies
        ];


        return $r;
    }

    public function setCustomSessionId($user_id,$game,$id) {
        dbredis::instance()->set('CustomSessionId'.$user_id.$game,$id);
        dbredis::instance()->expire('CustomSessionId'.$user_id.$game, 365*24*60*60);
        dbredis::instance()->set('CustomSessionId'.$user_id.'jp',$id);
        dbredis::instance()->expire('CustomSessionId'.$user_id.'jp', 365*24*60*60);

        dbredis::instance()->set('LastCustomSessionId'.$user_id,$id);
        dbredis::instance()->expire('LastCustomSessionId'.$user_id, 365*24*60*60);
    }


    public function getLastCustomSessionId($user_id,$default_id) {
        $s = dbredis::instance()->get('LastCustomSessionId'.$user_id);
        if(!$s) {
            $s=$default_id;
        }
        return $s;
    }

    public function getCustomSessionId($user_id,$game,$default_id) {
        $s = dbredis::instance()->get('CustomSessionId'.$user_id.$game);
        if(!$s) {
            $s=$default_id;
        }
        return $s;
    }

    public function jp($login,$url, $params=[]) {
        //mango
        return false;
    }


    public function bet($login,$url, $params=[],$is_repeat=false) {


        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            throw Exception('user not found');
        }

        $session=$this->session();
        $time=$this->time();

        $amount = bcmul($params['amount'],100,0);
        $win = bcmul($params['win'],100,0);
        $bet_id = $params['bet_id'];

        if(!isset($params['bet_request_id']) || empty($params['bet_request_id'])) {
            $params['bet_request_id']=$this->commandId();
        }

        $id=$params['bet_request_id'];

        $this->bet_request_id=$id;

        $fin=1;
        $round_num=$bet_id;

        if(isset($params['poker_bet_id']) && $params['poker_bet_id']>0) {
            $fin=1;
            $round_num=$params['poker_bet_id'];
        }
        elseif(isset($params['initial_id']) && $params['initial_id']>0) {
            $fin=1;
            $round_num=$params['initial_id'];
        }
        elseif(th::isMoonGame($params['game'])) {
            $fin=0;
        }
        elseif(in_array($params['game'],['acesandfaces','jacksorbetter','tensorbetter']) && $params['bettype']=='normal') {
            $fin=0;
        }

        dbredis::instance()->select(0);
        $need_reenter=true;
        if(true || $u->office->is_test) {
            $need_reenter=false;
            $guid=$this->getCustomSessionId($u->id,$params['game'],$u->api_session_id);
        }
        else {
            $guid=auth::getCustomSessionId($u->id,$u->api_session_id);
        }
        $this->guid=$guid;

        if($need_reenter && $params['game']!='jp' && $params['game']!=$u->last_play_game) {
            $this->reenter($u,$params['game']);
        }

        $xml=<<<XMLL
<server session="$session" time="$time">
<roundbetwin id="$id" guid="$guid" bet="$amount" win="$win" type="spin">
<roundnum id="$round_num"/>
<roundfin value="$fin"/>
</roundbetwin>
</server>
XMLL;


        if(isset($params['is_last_freespin']) && $params['is_last_freespin']) {

            $fs_id = $params['last_freespin_id'];
            $fs_amount=bcmul($params['fs_amount'],100,0);

            $offer_name = $params['infin_guid'];
            $fs_win=bcmul($params['win'],100,0);

            $xml=<<<XMLL
<server session="$session" time="$time">
<roundbetwin id="$id" guid="$guid" bet="0" win="0" type="giftspin">
<roundnum id="$round_num"/>
<giftspin gift_id="$fs_id" offer="$offer_name"/>
<giftfin giftbet="$fs_amount" giftwin="$fs_win" />
<roundfin value="$fin"/>
</roundbetwin>
</server>
XMLL;
        }

        $data=$this->send($xml,['u'.$u->id,'b'.$bet_id,'o'.$u->office_id,'r'.$round_num]);
        $data=$this->parse($data);

        if(!$data) {
            //non-determistic

            if($params['game']=='jp' && $this->jp_repeat_count>0) {
                $this->jp_repeat_count--;
                return $this->bet($login,$url,$params);
            }

            return false; //061021

            if($is_repeat){
                return false;
            }

            return $this->bet($login,$url,$params,true);
        }

        if($data->roundbetwin->attributes()->result!='ok') {
            logfile::create(date('Y-m-d H:i:s')." BET response ERROR: ".print_r($data,1),'infin');

            $error_code = $data->roundbetwin->error->attributes()->code;

            if(in_array($error_code,['MAX_BET_EXCEED','MAX_TIME_EXCEED','NOT_ENOUGH_MONEY','USER_BLOCKED'])) {
                $this->wrongBetProcessed=3;
                return false;
            }

            if($error_code=='WL_ERROR') {
                if($params['game']=='jp' && $this->jp_repeat_count>0) {
                    $this->jp_repeat_count--;
                    return $this->bet($login,$url,$params);
                }
                return false;
            }

            if($is_repeat){
                return false;
            }

            return $this->bet($login,$url,$params,true);
        }

        $u->amount = bcdiv($data->roundbetwin->balance->attributes()->value,100,2);

        if($u->amount<0) {
            //todo что лучше сделать?
            return false;
        }

        $u->save();

        return true;
    }

    public function processWrongBets($user_id) {

        $bets = db::query(1,'select * from wrongbetsinfin where user_id=:u_id and processed=0 and created>=:time and (game=:g or game=\'jp\') order by bet_id')
            ->param(':time',time()-60*60*24)
            ->param(':g',$this->gameName)
            ->param(':u_id',$user_id)
            ->execute(null,true);

        if(!th::isMoonGame($this->gameName) && count($bets)>3) {
            db::query(3,'update wrongbetsinfin set processed=6 where user_id=:u_id and processed=0 and (game=:g or game=\'jp\')')
                ->param(':g',$this->gameName)
                ->param(':u_id',$user_id)
                ->execute();

            th::ceoAlert('infin bad user '.($user_id).'; too many wrong bets ['.$this->gameName.']');

            return false;
        }

//        db::query(database::UPDATE,'update wrongbetsinfin set processed=4 where user_id=:u_id and processed=0 and (game=:g or game=\'jp\')')
//            ->param(':g',$this->gameName)
//            ->param(':u_id',$user_id)
//            ->execute();

        if(count($bets)) {

            $processed=[];
            $crushed=[];

            $u = new Model_User($user_id);

            if(!$u->loaded()) {
                throw new Exception('user not found!');
            }

            $errors=false; //todo check it. didnt test

            $errors=true;

            foreach($bets as $bet) {
                if(empty($bet->fs_guid) && !$bet->is_freespin && $bet->amount>0 && $bet->win==0) {
                    try {
                        $result = $this->refund($bet);

                        if(!$result) {
                            $crushed[]=$bet->id;
//                            db::query(Database::UPDATE,'update wrongbetsinfin set processed=2 where id = :id')
//                                ->param(':id',$bet->id)
//                                ->execute();
                        }
                        else {
                            $processed[]=$bet->id;
                            db::query(Database::UPDATE,'update wrongbetsinfin set processed=1 where id = :id')
                                ->param(':id',$bet->id)
                                ->execute();
                        }

                    } catch (Exception $ex) {
                        $crushed[]=$bet->id;
                    }
                }
                else {
                    //retry


                    $amount = (float) $bet->amount;

                    switch($bet->type) {
                        case 'normal':
                        case 'norcfs':
                        case 'norlfs':
                        case 'norafs':
                        case 'normfs':
                            $bettype = 'normal';
                            break;
                        case 'double':
                        case 'doucfs':
                        case 'doulfs':
                        case 'douafs':
                        case 'doumfs':
                            $bettype = 'double';
                            break;
                        case 'free':
                        case 'frecfs':
                        case 'frelfs':
                        case 'freafs':
                        case 'fremfs':
                            $bettype = 'free';
                            break;
                        default:
                            $bettype = $bet->type;
                    }

                    if(isset($bet->is_freespin) && $bet->is_freespin && $bettype=='normal') {
                        $amount=0;
                    }

                    $params = [
                            'amount'=>$amount,
                            'fs_amount'=>(float) $bet->amount,
                            'win'=>(float) $bet->win,
                            'game'=>$bet->game,
                            'game_id'=>$bet->game_id,
                            'game_type'=>$bet->game_type,
                            'bettype'=>$bettype,
                            'bet_id'=>$bet->bet_id,
                            'bet_request_id'=>$bet->request_id,
                            'come'=>$bet->come,
                            'result'=>$bet->result,
                            'is_freespin'=>$bet->is_freespin,
                            'base_amount' => (float) $bet->amount,
                            'created' => $bet->created,
                            'poker_bet_id'=>$bet->poker_bet_id,
                            'initial_id'=> !empty($bet->initial_id) ? $bet->initial_id : $bet->poker_bet_id,
                    ];

                    bet::$api_request_id=$bet->request_id;

                    $params['is_last_freespin']=(int) !empty($bet->fs_guid);
                    $params['last_freespin_id']=$bet->fs_id;
                    $params['infin_guid']=$bet->fs_guid;


                    $url = $this->getUrl($u->office->gameapiurl);

                    try {
                        $result = $this->bet($u->name,$url,$params,true); //already repeat
                        if($result) {
                            try
                            {
                                $betArr = (array) $bet;
                                $betArr['game_type']='agt';
                                $betArr['game_name']=$betArr['game'];
                                $betArr['can_jp']=false;
                                $betArr['send_api']=false;

                                $info = 'wb; '.date('Y-m-d H:i:s',$bet->created);
                                if(th::isMoonGame($bet->game) && !empty($bet->initial_id)) {
                                    $info.='; '.$bet->initial_id;
                                }
                                $betArr['info']=$info;

                                auth::$user_id=$user_id;
                                bet::make($betArr,$betArr['type'],[],true,true);

                                db::query(Database::UPDATE,'update wrongbetsinfin set processed=1 where id = :id')
                                    ->param(':id',$bet->id)
                                    ->execute();

                                if(th::isMoonGame($betArr['game']) && $betArr['initial_id']>0) {
                                    game_moon_agt::updateUserBetHistory($user_id,$betArr);
                                }
                                $processed[]=$bet->id;
                            }
                            catch(Exception $ex)
                            {
                                $errors=true;
                                db::query(Database::UPDATE,'update wrongbetsinfin set processed=0 where id = :id')
                                    ->param(':id',$bet->id)
                                    ->execute();
                                //not crushed and not processed. need to fix internal
                                logfile::create(date('Y-m-d H:i:s')." ERROR BET!!!!!: ".$ex->getMessage(). "\n".$ex->getTraceAsString(),'infin');
                            }
                        }
                        else {
                            $crushed[]=$bet->id;
//                            db::query(Database::UPDATE,'update wrongbetsinfin set processed=2 where id = :id')
//                                ->param(':id',$bet->id)
//                                ->execute();
                        }
                    }
                    catch(Exception $ex) {
                        $crushed[]=$bet->id;
                        $errors=true;
//                        db::query(Database::UPDATE,'update wrongbetsinfin set processed=2 where id = :id')
//                            ->param(':id',$bet->id)
//                            ->execute();
                        logfile::create(date('Y-m-d H:i:s')." ERROR BET2!!!!!: ".$ex->getMessage(). "\n".$ex->getTraceAsString(),'infin');
                    }
                }
            }



            /*if(count($processed)>0) {
                db::query(Database::UPDATE,'update wrongbetsinfin set processed=1 where id in :ids')
                        ->param(':ids',$processed)
                        ->execute();
            }

            if(count($crushed)>0) {
                db::query(Database::UPDATE,'update wrongbetsinfin set processed=2 where id in :ids')
                        ->param(':ids',$crushed)
                        ->execute();
            }*/

            if(count($crushed)>0) {
                return 'notpass';
            }

            return $errors;
        }
        return false;
    }

    public function saveWrongBet($bet,$params,$poker_bet_id) {
        $b = new Model_WrongbetInfin();
        $b->bet_id = $bet->id;
        $b->request_id = $params['bet_request_id']??null;
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
        $b->is_freespin = $params['is_freespin'];
        $b->created = $bet->created;
        $b->real_amount = $bet->real_amount;
        $b->real_win = $bet->real_win;
        $b->fs_id = $params['last_freespin_id']??null;
        $b->fs_guid = $params['infin_guid']??null;
        $b->save();
    }

    public function refund($bet) {

        $session=$this->session();
        $time=$this->time();

        $amount = bcmul($bet->amount,100,0);
        $win = bcmul($bet->win,100,0);
        $refund_id = $bet->request_id;
        $bet_id = $bet->bet_id;
        $guid = $this->guid;
        $guid_last = $bet->guid;
        $wlid = $this->wlid;
        $game=$bet->game;

        $id=$this->commandId();

        $xml=<<<XMLL
<server session="$session" time="$time">
<refund id="$id" guid="$guid" cash="$amount">
<storno cmd="roundbetwin" id="$refund_id" wlid="$wlid" gameid="$game" guid="$guid_last" cash="$amount">
<roundnum id="$bet_id"/>
</storno>
</refund>
</server>
XMLL
;

        $data=$this->send($xml);

        if(!$data) {
            throw new Exception('wrong refund');
        }
        return true;
    }

    public function getBalance() {
        $session=$this->session();
        $time=$this->time();

        $guid = $this->guid;

        $id=$this->commandId();

        $xml=<<<XMLL
<server session="$session" time="$time">
<getbalance id="$id" guid="$guid"/>
</server>
XMLL;

        $data=$this->send($xml);
        $data=$this->parse($data);


        if(!$data) {
            return -1;
        }


        if($data->getbalance->attributes()->result!='ok'){
            return -1;
        }


        $balance = (int) $data->getbalance->balance->attributes()->value;

        return $balance;
    }

}

