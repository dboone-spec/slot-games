<?php

class gameapi {

    public $last_error;
    public $bet_request_id;
    public $canRestoreSession=false;
    protected static $_instance;
    protected static $_instance_type;

    /**
	 * @param   int   $type    apitype
	 * @return  gameapi
	 */
    public static function instance($type=0) {

        if(static::$_instance) {
            return static::$_instance;
        }

        if($type==12) {
            static::$_instance=new Api_SoftSwiss();
        }
        elseif($type==11) {
            static::$_instance=new Api_Tvbet();
        }
        elseif($type==10) {
            static::$_instance=new Api_Pinup();
        }
        elseif($type==9) {
            static::$_instance=new Api_BetConstruct();
        }
        elseif($type==8) {
            static::$_instance=new Api_SoftGamings();
        }
        elseif($type==7) {
            static::$_instance=new Api_Evenbet();
        }
        elseif($type==6) {
            static::$_instance=new Api_Ematrix();
        }
        elseif($type==5) {
            static::$_instance=new Api_Vertbet();
        }
        elseif($type==4) {
            static::$_instance=new Api_Infin();
        }
        else {
            static::$_instance=new gameapi();
        }

        static::$_instance_type=$type;
        return static::$_instance;
    }

    public function saveWrongBet($bet,$params,$poker_bet_id) {
            $b = new Model_Wrongbet();
            $b->bet_id = $bet->id;
            $b->user_id = $bet->user_id;
            $b->amount = $bet->amount;
            $b->game_type = $bet->game_type;
            $b->game = $bet->game;
            $b->type = $bet->type; //normal, free game, bonus game, double
            $b->come = $bet->come;
            $b->result = $bet->result;
            $b->office_id = $bet->office_id;
            $b->win = $bet->win;

            $b->login = auth::user()->external_id;

            $b->game_id = $bet->game_id;
            $b->method = $bet->method;
            $b->balance = $bet->balance;//amount + bonus
            $b->poker_bet_id = $poker_bet_id;
            $b->initial_id = !empty($bet->initial_id) ? $bet->initial_id : $poker_bet_id;
            $b->is_freespin = $bet->is_freespin??0;
            $b->created = $bet->created;

            $u=auth::user();
            $b->session_id = auth::getCustomSessionId($u->id,$u->api_session_id);
			$b->game_session_id = auth::getCustomGameSessionId($u->id,$bet->game,$u->api_session_id);
			$b->fs_uuid = $bet->fs_uuid;

            $b->save();
    }

    public function isNeedSend($url) {
        return static::$_instance_type==0 && !empty($url);
    }

    public static $our_api_owners=[1023,1089,1100,1123,1030,1128,1131,1132,1139,1142];

    public static function isOurAPI($person_id) {
        $p=new Model_Person($person_id);
        if(!$p->loaded()) {
            return false;
        }
        return !!$p->our_api;
    }

	public function getDomain(Model_User $user) {

        return '';
    }

    public function getUrl($url) {

        //test
        /*if(mt_rand(0,1)==1) {
            return str_replace('mega','asddd',$url);
        }*/

        return $url;
    }

    public function wrongbets($office_id,$url, $bets=[]) {
        $parser = new Parser();
        $o = new Model_Office($office_id);

        $time=time();

        $data=[];
        $data['bets']=$bets;
        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$office_id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] request: '.$url.PHP_EOL.json_encode($data,1),'gameapi');

        $r =  $parser->post($url,$data);
        if(!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] ERROR response: '.$url,'gameapi');
            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] response: '.$url.PHP_EOL.$r,'gameapi');

        $jr = json_decode($r,1);
        if(!$jr) {
            $this->last_error = 'bad json response';
            return false;
        }

        if(!isset($jr['error']) || $jr['error']!='0') {
            $this->last_error = arr::get($jr,'error_message','');
            return false;
        }

        return true;
    }

    public function jp($login,$url, $params=[]) {
        $parser = new Parser();

        $o=auth::user()->office;
        $time = time();

        $data=[];
        $data['login']= UTF8::str_ireplace('-'.$o->id,'',$login);
        $data['action']='jp';
        $data['win']=$params['win'];
        $data['game']=$params['game'];
        $data['game_id']=$params['game_id'];

        $data['time']=$time;

        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$o->id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$data['bet_id'].'] JP request: '.$url.PHP_EOL.json_encode($data,1),'gameapi');

        $r = $parser->post($url,$data);
        if(!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$data['bet_id'].'] ERROR JP response: '.$url,'gameapi');
            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$data['bet_id'].'] JP response: '.$url.PHP_EOL.$r,'gameapi');

        $jr = json_decode($r,1);
        if(!$jr) {
            $this->last_error = 'bad json response';
            return false;
        }
    }

    public function bet($login,$url, $params=[],$no_resend=false) {
        $parser = new Parser();

        $u=new Model_User(['name'=>$login]);
        $o=$u->office;

        $time = time();

        $params['ulogin']=$login;
        $params['user_id']=$u->id;
        $params['office_id']=$u->office_id;
        $params['balance']=$u->amount();

        $a = explode('-',$login);

        array_pop($a);

        $data=[];
        $data['login']= implode('-',$a);

        $data['action']='bet';
        $data['amount']=$params['amount'];
        $data['win']=$params['win'];
		$data['office_id']=$params['office_id'];
        $data['game']=$params['game'];
        $data['come']=$params['come'];
        $data['result']=$params['result'];
        $data['is_freespin']=$params['is_freespin']??false;
        $data['base_amount']=$params['base_amount']??0;
        $data['is_cashback']=(int) ($params['is_cashback']??0);
        $data['fs_amount']=($params['fs_amount']??0);
        $data['game_type']=$params['game_type']??'';

        if($params['bettype']=='double') {
            if(is_int($data['come'])) {
                $suits = [0=>'♥',1=>'♦',2=>'♠',3=>'♣'];
                $data['come'] = $suits[$data['come']];
                $data['result'] = $suits[$data['result']];
            }
            else {
                if($data['win']>0) {
                    $data['result']=$data['come'];
                }
                else {
                    $data['result'] = ($data['come']=='black')?'red':'black';
                }
            }
        }
        elseif($params['bettype']=='jp') {
            $data['result']=card::print_card(json_decode($data['result']));
			$data['trigger_bet_id']=(int) ($params['trigger_bet_id']??0);
        }

        if(is_array($data['come'])) {
            $data['come']=json_encode($data['come']);
        }


        if(isset($params['poker_bet_id']) && $params['poker_bet_id']>0) {
            $data['poker_bet_id']=$params['poker_bet_id'];
            $data['initial_bet_id']=$params['poker_bet_id'];
        }

        if(isset($params['initial_id']) && $params['initial_id']>0) {
            $data['initial_bet_id']=$params['initial_id'];
        }

        if(isset($params['slot_win_lines']) && $params['slot_win_lines']>0) {
            $data['slot_win_lines']=json_encode($params['slot_win_lines']);
        }

        $fin=1;
        $round_num=$params['bet_id'];

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
        elseif(in_array($params['game'],['tensorbetter','jacksorbetter','acesandfaces']) && $params['bettype']=='normal') {
            $fin=0;
        }

        $data['finished']=$fin;
        $data['round_num']=$round_num;
        $data['game_id']=$params['game_id'];
        $data['bet_type']=$params['bettype'];
        $data['bet_id']=$params['bet_id'];
        $data['created']=$params['created'];
        $data['time']=$time;
        $data['session_id']=auth::getCustomSessionId(auth::$user_id, md5(auth::$token));
		$data['game_session_id']=auth::getCustomGameSessionId(auth::$user_id, $data['game'], md5(auth::$token.$data['game']));
        $data['fs_uuid']=$params['fs_uuid']??'';
//        Cookie::get('api_session_id',md5(Session::instance()->id()));

		if($data['win']>0) {
            $mult=$o->currency->mult ?? 2;
            if($mult>0) {
                $data['win']=(float) rtrim(sprintf('%.'.$mult.'F',$data['win']),'0');
            }
        }

        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$o->id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$data['bet_id'].'] request: '.$url.PHP_EOL.json_encode($data,1),'gameapi');

        $start_time = microtime(1);

        $r = $parser->post($url,$data);

        $response_time = microtime(1)-$start_time;

        if(!$r) {
            $this->last_error = 'bad response';

            $extra='FIRST';
            if($no_resend) {
                $extra='RESEND';
            }


            logfile::create(date('Y-m-d H:i:s').$extra.' ['.$o->id.']['.$data['bet_id'].'] time: |'.$response_time.'| ERROR response: <'.$parser->error.'> '.$url,'gameapi');

            $error_type=$parser->getErrorType($response_time);

            if(!$no_resend && $o->seamlesstype==1 && $error_type==Parser::ERROR_CONNECT_TIMEOUT) {

                $try=$this->forceWrongBet($data,$o,$params);

                if($try) {
                    //update balance???
                    $u->amount = $u->amount-$params['amount']+$params['win'];
                    $u->save();
                }
                else {
                    //alert sms
                    th::sendCurlError(static::$_instance_type);
                }

                return $try;
            }

            th::sendCurlError(static::$_instance_type);

            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$data['bet_id'].'] time: |'.$response_time.'| response: '.$url.PHP_EOL.$r,'gameapi');

        $jr = json_decode($r,1);
        if(!$jr) {
            $this->last_error = 'bad json response';
            return false;
        }

        if(!isset($jr['error']) || $jr['error']!='0') {
            $this->last_error = arr::get($jr,'error_message','');
            return false;
        }

        if(!isset($jr['balance']) || (float) $jr['balance'] < 0) {
            $this->last_error = 'not enough balance';
            return false;
        }

        /*if((float) $jr['balance']<(float) $data['win']) {
            $this->last_error = 'balance error';
            return false;
        }*/


        if(!$u->loaded()) {
            $this->last_error = 'user not found';
            return false;
        }

        $u->amount = $jr['balance'];
        $u->save();


        return true;
    }

    public function forceWrongBet($betdata,Model_Office $o,$betparams) {

        $time=time();

        $sql_nextval = <<<SQL
            Select nextval('wrongbets_id_seq')
SQL;
        $wbet_id = key(db::query(1, $sql_nextval)->execute()->as_array('nextval'));

        $poker_bet_id=$betdata['poker_bet_id']??0;
        $initial_bet_id=$betdata['initial_bet_id']??0;

        if($poker_bet_id) {
            $initial_bet_id=$poker_bet_id;
        }

        $wrongbet_data=[];
        $wrongbet_data['id']=(string) $wbet_id;
        $wrongbet_data['bet_id']=(string) $betdata['bet_id'];
        $wrongbet_data['created']=(string) $betdata['created'];
        $wrongbet_data['office_id']=(string) $betparams['office_id'];
        //$wrongbet_data['processed']='0';
        $wrongbet_data['user_id']=(string) $betparams['user_id'];
        if(office::instance($wrongbet_data['office_id'])->office()->owner!=1023) {
            $wrongbet_data['login']=(string) $betdata['login'];
        }
        $wrongbet_data['amount']=(string) $betdata['amount'];
        $wrongbet_data['game_type']=(string) $betparams['game_type'];
        $wrongbet_data['game']=(string) $betdata['game'];
        $wrongbet_data['come']=(string) $betdata['come'];
        $wrongbet_data['result']=(string) $betdata['result'];
        $wrongbet_data['win']=(string) $betdata['win'];
        $wrongbet_data['game_id']=(string) $betdata['game_id'];
        $wrongbet_data['balance']=(string) $betparams['balance'];
        $wrongbet_data['type']=(string) $betdata['bet_type'];
        $wrongbet_data['poker_bet_id']=(string) $poker_bet_id;
        $wrongbet_data['initial_bet_id']=(string) $initial_bet_id;
        $wrongbet_data['is_freespin']=(string) ((int) $betparams['is_freespin']);
		$wrongbet_data['is_cashback']=((int) $betparams['is_freespin']==1);
        $wrongbet_data['base_amount']=(string) $betdata['base_amount'];
        $wrongbet_data['fs_amount']=(string) $betdata['fs_amount'];
        $wrongbet_data['session_id']=(string) $betdata['session_id'];
		$wrongbet_data['game_session_id']=(string) $betdata['game_session_id'];
        $wrongbet_data['fs_uuid']=(string) $betdata['fs_uuid'];

        $data=[];
        $data['bets']=[$betdata['bet_id']];
        $data['full_bets']=json_encode([(string) $betdata['bet_id']=>$wrongbet_data],JSON_FORCE_OBJECT);
        $data['action']='wrongbets';
        $data['time']=$time;
        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$o->id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$betdata['bet_id'].'] try to send wrongbet'.PHP_EOL.json_encode($data).PHP_EOL,'gameapi');

        $parser = new Parser();

        $response=$parser->post($this->getUrl($o->gameapiurl),$data);

        //
        if(!$response) {
            logfile::create(date('Y-m-d H:i:s').' ERROR ['.$o->id.']['.$betdata['bet_id'].'] try to send wrongbet| response error: '.$parser->error,'gameapi');
            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$betdata['bet_id'].'] try to send wrongbet| response: '.$response,'gameapi');

        $jr = json_decode($response,1);
        if(!$jr) {
            //todo log
        }
        else if(!isset($jr['error']) || $jr['error']!='0') {
            //todo log
        }
        else if(!isset($jr['bets']) || empty($jr['bets'])) {
            //todo log
        }
        else {

            $bet_id=key($jr['bets']);

            if($bet_id!=$betdata['bet_id']) {
                throw new Exception('wtf');
            }

            //bet already exists
            if(((int) $jr['bets'][$bet_id])==1) {
                return true;
            }

            return $this->bet($betparams['ulogin'],$this->getUrl($o->gameapiurl),$betparams,true);
        }

        return false;
    }

    public function getAuthTGLink($tg_name,$office_id) {
        $parser = new Parser();

        $o = Office::instance($office_id)->office();
        $url=$o->gameapiurl;
        $time=time();
        $data=['action'=>'gettgauthlink', 'tgname'=>$tg_name, 'time'=>$time];

        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$o->id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] request: '.$url.PHP_EOL.json_encode($data,1),'gameapi');

        $start_time = microtime(1);

        $r = $parser->post($url,$data);

        $response_time = microtime(1)-$start_time;

        if(!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$tg_name.'] time: |'.$response_time.'| ERROR response: <'.$parser->error.'> '.$url,'gameapi');
            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] time: |'.$response_time.'| response: '.$url.PHP_EOL.$r,'gameapi');

        if(!$r) {
            return false;
        }

        $jr = json_decode($r,1);
        if(!$jr) {
            return false;
        }

        return arr::get($jr,'url',false);
    }

    public function getInfoTextTG($office_id) {
        $parser = new Parser();

        $o = Office::instance($office_id)->office();
        $url=$o->gameapiurl;
        $time=time();
        $data=['action'=>'gettginfotext', 'time'=>$time];

        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$o->id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] request: '.$url.PHP_EOL.json_encode($data,1),'gameapi');

        $start_time = microtime(1);

        $r = $parser->post($url,$data);

        $response_time = microtime(1)-$start_time;

        if(!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']gettginfotext time: |'.$response_time.'| ERROR response: <'.$parser->error.'> '.$url,'gameapi');
            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']gettginfotext time: |'.$response_time.'| response: '.$url.PHP_EOL.$r,'gameapi');

        if(!$r) {
            return false;
        }

        $jr = json_decode($r,1);
        if(!$jr) {
            return false;
        }

        return arr::get($jr,'infotext',false);
    }

    public function checkTGUser($tg_name,$office_id,$url) {
        $parser = new Parser();

        $o = Office::instance($office_id)->office();
        $time=time();
        $data=['action'=>'gettguser', 'tgname'=>$tg_name, 'time'=>$time];

        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$o->id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] request: '.$url.PHP_EOL.json_encode($data,1),'gameapi');

        $start_time = microtime(1);

        $r = $parser->post($url,$data);

        $response_time = microtime(1)-$start_time;

        if(!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$tg_name.'] time: |'.$response_time.'| ERROR response: <'.$parser->error.'> '.$url,'gameapi');
            return false;
        }

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] time: |'.$response_time.'| response: '.$url.PHP_EOL.$r,'gameapi');


        if(!$r) {
            return false;
        }

        $jr = json_decode($r,1);
        if(!$jr) {
            return false;
        }

        if(!isset($jr['error']) || $jr['error']!='0') {
            return false;
        }

        if(!isset($jr['login']) || empty($jr['login'])) {
            return false;
        }

        if(!isset($jr['balance']) || (float) $jr['balance'] < 0) {
            return false;
        }

        return $jr;

    }

    public function checkBalance($login,$office_id,$url,$sess_id=null) {
        $parser = new Parser();

        $o = Office::instance($office_id)->office();
        $time=time();
        $data=['action'=>'balance', 'login'=>$login, 'time'=>$time,'office_id'=>$o->id];
        if($sess_id) {
            $data['session_id']=$sess_id;
        }
        $data['sign']=$o->sign([
            'time'=>$time,
            'office_id'=>$o->id,
        ]);

        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] request: '.$url.PHP_EOL.json_encode($data,1),'gameapi');

        $start_time = microtime(1);

        $r = $parser->post($url,$data);

        $response_time = microtime(1)-$start_time;

        if(!$r) {
            $this->last_error = 'bad response';
            logfile::create(date('Y-m-d H:i:s').' ['.$o->id.']['.$login.'] time: |'.$response_time.'| ERROR response: <'.$parser->error.'> '.$url,'gameapi');
            return false;
        }


        logfile::create(date('Y-m-d H:i:s').' ['.$o->id.'] time: |'.$response_time.'| response: '.$url.PHP_EOL.$r,'gameapi');


        if(!$r) {
            return false;
        }

        $jr = json_decode($r,1);
        if(!$jr) {
            return false;
        }

        if(!isset($jr['error']) || $jr['error']!='0') {
            return false;
        }

        if(!isset($jr['balance']) || (float) $jr['balance'] < 0) {
            return false;
        }

        //TODO Нужно одного пользователя делать, а не 20 штук с разными валютами
        //в других местах удалить тоже
        $u = new Model_User(['name'=>$login.'-'.$office_id]);

        if(!$u->loaded()) {
            return false;
        }

        $u->amount="".$jr['balance'];

        try {
            $u->save();
        } catch (Exception $ex) {
            Kohana::$log->add(Log::ALERT,$ex->getMessage());
            return false;
        }

        return true;
    }

}
