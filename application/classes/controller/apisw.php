<?php

class Controller_ApiSW extends Controller {

    protected $_api_url='';
    protected $_secretkey='';
    protected $_test_office=true;
	
	//api version
    protected $_casino=[
        'devgame'=>1,
        'internal-wallet-dev0-1'=>2,
    ];

    protected function ans($data,$code=200) {
        $this->response->status($code);
        echo $this->response->body(json_encode($data));
		logfile::create(date('Y-m-d H:i:s') . ' [RESPONSE]: '.json_encode($data). PHP_EOL, 'softswiss');
        exit;
    }

    public function  action_Player() {
        $this->ans([
            'balance'=>file_get_contents('bbb'),
        ]);
    }

	public function  action_Promo() {

        $action=$this->request->param('id');

        $request=file_get_contents('php://input');

        if(empty($request)) {
            throw new Exception('empty body');
        }

        logfile::create(date('Y-m-d H:i:s') . ' [Promo]: ' . $request. PHP_EOL, 'softswiss');

        $requestJSON=json_decode($request,1);

        if(empty($requestJSON)) {
            throw new Exception('wrong json');
        }

        if($action=='Win') {
            if(!LOCAL) {
                exit;
            }

            $api=new Api_SoftSwiss();
            $api->setUpEnv($this->_test_office);

            $signIN=$this->request->headers('X-REQUEST-SIGN');

            if(!$api->checkSign($request,$signIN)) {
                throw new Exception('wrong sign');
            }

            $balance=file_get_contents('bbb');
            $balance+=$requestJSON['amount'];
            file_put_contents('bbb',$balance);

            $this->ans([
                'balance'=>$balance,
                'id_provider'=>$requestJSON['id_provider'],
            ]);

        }
        echo '{}';

        exit;
    }

	public function  action_Freespins() {

        $action=$this->request->param('id');

        $request=file_get_contents('php://input');

        if(empty($request)) {
            throw new Exception('empty body');
        }

        logfile::create(date('Y-m-d H:i:s') . ' [Freespins '.$action.']: ' . $request. PHP_EOL, 'softswiss');

        $requestJSON=json_decode($request,1);

        if(empty($requestJSON)) {
            throw new Exception('wrong json');
        }

        if($action=='Issue') {
            $api=new Api_SoftSwiss();
            $api->setUpEnv($this->_test_office,$this->_casino[$requestJSON['casino_id']]);

            $signIN=$this->request->headers('X-REQUEST-SIGN');

            if(!$api->checkSign($request,$signIN)) {
                throw new Exception('wrong sign');
            }

            $provider='site-domain';

            $currency=UTF8::strtoupper(arr::get($requestJSON['account'],'currency',''));
            $country=UTF8::strtoupper(arr::get($requestJSON['account'],'country',''));
            $issue_id=UTF8::strtoupper(arr::get($requestJSON,'issue_id',''));
            $casino_id=arr::get($requestJSON,'casino_id','');
            $bet_level=arr::get($requestJSON,'bet_level',0);
            $games=arr::get($requestJSON,'games',[]);
            $valid_until=arr::get($requestJSON,'valid_until');
            $freespins_quantity=arr::get($requestJSON,'freespins_quantity');
            $account_id=arr::get($requestJSON['account'],'id','');
            $nickname=arr::get($requestJSON['account']['nickname'],'id','');

            $api->currency=$currency;
            $api->account_id=$account_id;

            list($office_id,$user_id)=$api->checkUserAndOffice($casino_id,$nickname);

            $games=arr::map(function($game) use($provider) {
                return str_replace($provider.':','',$game);
            },$games);

            $game_ids=array_keys(db::query(1,'select id from games where name in :names and type in :types')
                ->param(':types',['slot','shuffle'])
                ->param(':names',$games)
                ->execute()->as_array('id'));

            $swAward=new Model_SoftSwissAward(['issue_id'=>$issue_id,'user_id'=>$user_id]);

            if($swAward->loaded()) {
                throw new Exception('issue_id already given');
            }

            $swAward->account_country=$country;
            $swAward->account_currency=$currency;
            $swAward->account_id=$account_id;
            $swAward->bet_level=$bet_level;
            $swAward->bonus_type='freespins';
            $swAward->casino_id=$casino_id;
            $swAward->games=$game_ids;
            $swAward->issue_id=$issue_id;
            $swAward->valid_until=strtotime($valid_until);
            $swAward->freespins_quantity=$freespins_quantity;
            $swAward->user_id=$user_id;
            $swAward->office_id=$office_id;

            $swAward->save();



            /*$fs=new Model_Freespin(['fs_offer_id'=>$issue_id,'fs_offer_type'=>'sw']);

            if($fs->loaded()) {
                //error
                throw new Exception('fs_offer_id ['.$issue_id.'] already exists');
            }

            $fs->fs_offer_type='sw';
            $fs->fs_offer_id=$issue_id;

            $fs_id=$fs->giveFreespins($user_id,$office_id,$game_ids,$waitedFS->spinAmount,$waitedFS->betAmount,0,0,'api',false,null,false,null,$waitedFS->expireDate,$waitedFS->freespinId);*/

            /*
            if($fs_id) {
                $fs->activateFreespins($fs_id);
            }*/




        }
        elseif ($action=='Cancel') {

            $f=new Model_SoftSwissAward([
                'issue_id'=>$requestJSON['issue_id'],
                'casino_id'=>$requestJSON['casino_id'],
            ]);

            if(!$f->loaded() || $f->activated<0) {
                $this->ans([
                    'code'=>'404',
                    'msg'=>'issue_id not found',
                    'meta'=>[
                        'api_code'=>620,
                    ],
                ],400);
            }


            $f->activated=-time();
            $f->save();

            $fsOUR=new Model_Freespin([
                'fs_offer_type'=>'softswiss',
                'uuid'=>$requestJSON['issue_id'],
                'user_id'=>$f->user_id,
            ]);

            if($fsOUR->loaded()) {
                $fsOUR->declineFreespins($fsOUR->id);
            }
        }
        elseif ($action=='Finish') {
            if(!LOCAL) {
                exit;
            }
            $balance=file_get_contents('bbb');
            $balance+=$requestJSON['amount'];
            file_put_contents('bbb',$balance);

            $this->ans([
                'balance'=>$balance,
                'action'=>$action,
            ]);
        }
        echo '{}';

        exit;
    }

    public function  action_Round() {

        $balance=file_get_contents('bbb');
        $input=file_get_contents('php://input');

        if(empty($input)) {
            throw new Exception('empty data');
        }

        $json=json_decode($input,1);

        if(empty($json)) {
            throw new Exception('empty json');
        }

        if(mt_rand(0,1)==2) {
            $this->ans([
                'code'=>'random_exception',
                'msg'=>'random exception',
                'meta'=>[
                    'api_code'=>222,
                    'api_code'=>'random exception',
                    'balance'=>'api_message',
                ],
            ],400);
        }

        if($this->request->param('id')=='BetWin') {

            foreach($json['transactions'] as $transaction) {
                if($transaction['type']=='bet') {
                    $balance-=$transaction['amount'];
                }

                if($transaction['type']=='win') {
                    $balance+=$transaction['amount'];
                }
            }
        }

        file_put_contents('bbb',$balance);

        $this->ans([
            'balance'=>$balance,
            'action'=>$this->request->param('id'),
//            '$json'=>$json,
        ]);
    }

    public function  action_Launcher(){

        $api=new Api_SoftSwiss(); 

        $request=file_get_contents('php://input');

        if(empty($request)) {
            throw new Exception('empty body');
        }

		logfile::create(date('Y-m-d H:i:s') . ' [Launcher]: ' . $request. PHP_EOL, 'softswiss');

        $requestJSON=json_decode($request,1);

        if(empty($requestJSON)) {
            throw new Exception('wrong json');
        }

		$api->setUpEnv($this->_test_office,$this->_casino[$requestJSON['casino_id']]);

        $signIN=$this->request->headers('X-REQUEST-SIGN');

        if(!$api->checkSign($request,$signIN)) {
            throw new Exception('wrong sign');
        }

        $mode=$this->request->param('id');
        $noClose=false;

        $api->gameName=arr::get($requestJSON,'game');

        $forceMobile=(arr::get($requestJSON,'client_type','desktop')=='mobile');
        $lang=UTF8::strtolower(arr::get($requestJSON,'locale'));
        $exit_url=arr::get($requestJSON['urls'],'return_url');

        /*if($exit_url!==urldecode($exit_url)) {
            $exit_url=urlencode($exit_url);
        }*/


        if($mode=='Demo') {

            $demobalance=2000;

            $demoUrl='https://demo.kolinz.xyz/games/agt/'.$api->gameName.'?demobalance='.($demobalance*100);

            if($forceMobile) {
                $demoUrl.='&force_mobile=1';
            }
            if($noClose) {
                $demoUrl.='&no_close=1';
            }

            $demoUrl.='&closeurl='.$exit_url;
            $demoUrl.='&lang='.$lang;

            $this->ans([
                'launch_url'=>$demoUrl
            ]);
        }


        $currency=UTF8::strtoupper(arr::get($requestJSON['account'],'currency',''));
        $session=arr::get($requestJSON,'session_id',false);
        $casino_id=arr::get($requestJSON,'casino_id','');
        $account_id=arr::get($requestJSON['account'],'id','');
        $nickname=arr::get($requestJSON['account']['nickname'],'id','');

        if(!$session) {
            throw new Exception_ApiResponse('Empty session_id');
        }

        $api->session_token=$session;
        $api->currency=$currency;
        $api->account_id=$account_id;

        list($office_id,$user_id)=$api->checkUserAndOffice($casino_id,$nickname);

        if(!$api->checkGame($office_id)) {
            throw new Exception("Game not found");
        }

        $this->ans([
            'launch_url'=>$api->getGame($user_id,$lang,$forceMobile,$noClose,$exit_url)
        ]);
    }

    public function action_frametest()
    {
        $host = 'https://app.site-domain.local';

        $game = $this->request->param('id');

        $intUserId=100;
        $mode='real';
        $currency='EUR';
        $country='AU';
        $lang='en';
        $forceMobile=false;
        $casinoID='2win';

        $session=guid::create();

        $userID = guid::v3($intUserId,'intUserId');

        $action = arr::get($_GET, 'action');
        $fsid = arr::get($_GET, 'fsid');
        if ($action == 'delete' && $fsid) {

            $delparams = [
                'requestID'   => uniqid(),
                'freeroundID' => $fsid,
            ];

            $apidelete = new Api_SoftGamings();
            $apidelete->setUpEnv($this->_test_office);

            $delparams['sign'] = $apidelete->sign('deleteFreerounds', $delparams);

            $p = new Parser();

            $delete_request = $p->post($host . '/apisg/deleteFreerounds', $delparams, true);
            var_dump($delete_request);
        }

        if ($action == 'info' && $fsid) {

            $infoparams = [
                'requestID'   => uniqid(),
                'freeroundID' => $fsid,
                'userID'      => $userID,
            ];

            $apiinfo = new Api_SoftGamings();
            $apiinfo->setUpEnv($this->_test_office);

            $infoparams['sign'] = $apiinfo->sign('getFreeroundsInfo', $infoparams);

            $p = new Parser();

            $info_request = $p->post($host . '/apisg/getFreeroundsInfo', $infoparams, true);
            var_dump($info_request);
        }

        if (!empty($game)) {

            $reqOpenGame=[
                "account" => [
                    "country" => $country,
                    "currency" => $currency,
                    "date_of_birth" => "1999-12-31T15:30:00Z",
                    "firstname" => "John",
                    "gender" => "m",
                    "id" => $userID,
                    "lastname" => "Doe",
                    "nickname" => "John.Doe",
                    "registered_at" => "2020-12-31T15:30:00Z",
                    "tags" => ["vip"],
                ],
                "casino_id" => $casinoID,
                "client_type" => $forceMobile?'mobile':'desktop',
                "game" => $game,
                "ip" => "8.8.8.8",
                "jurisdiction" => $country,
                "locale" => $lang,
                "session_id" => $session,
                "urls" => [
                    "deposit_url" => "https://casino.test/deposit",
                    "return_url" => "https://casino.test/",
                ]
            ];

            if($mode=='demo') {
                $reqOpenGame=[
                    "casino_id" => $casinoID,
                    "client_type" => $forceMobile?'mobile':'desktop',
                    "game" => $game,
                    "ip" => "8.8.8.8",
                    "jurisdiction" => "DE",
                    "locale" => $lang,
                    "urls" => ["return_url" => "https://casino.test/"],
                ];
            }

            $api = new Api_SoftSwiss();
            $api->setUpEnv($this->_test_office);

            $reqBody=json_encode($reqOpenGame);

            $headers = [
                'Content-Type: application/json',
                'X-REQUEST-SIGN: ' . $api->sign($reqBody),
            ];

            $p = new Parser();

            $url='/apisw/v2/a8r_provider.Launcher/';
            $url.=ucfirst($mode);

            $gamelaunch_request = $p->post($host . $url, $reqOpenGame, true,$headers);

            if (!$gamelaunch_request) {
                throw new Exception('error launch');
            }

            $gamelaunch_request = json_decode($gamelaunch_request, 1);


            if (!$gamelaunch_request) {
                throw new Exception('error launch2');
            }

            echo '<iframe width="100%" height="100%" src="' . $gamelaunch_request['launch_url'] . '"></iframe>';
            exit;
        }

        $params = [
            'requestID' => uniqid(),
//            'debugCode'=>'2000'
        ];

        $api = new Api_SoftGamings();
        $api->setUpEnv($this->_test_office);

        $params['sign'] = $api->sign('getGamesList', $params);

        $p = new Parser();

        $gamelist_request = $p->post($host . '/' . $this->request->controller() . '/getGamesList', $params, true);

        if (!$gamelist_request) {
            throw new Exception('where is my game list?');
        }

        $gamelist_request = json_decode($gamelist_request, 1);

        if ($gamelist_request && $gamelist_request['games']) {
            foreach ($gamelist_request['games'] as $gameArr) {
                echo '<a href="/' . $this->request->controller() . '/frametest/' . $gameArr['gameid'] . '">' . $gameArr['name'] . '</a><br />';
            }
        } else {
            echo 'no games';
        }
    }

    public function  action_testlaunch(){

        $pass='agtintegrationkey';

        $action=$this->request->param('id');

        $body=file_get_contents('php://input');

        $req_json=json_decode($body,1);

        $authhash=$this->request->headers('authorization');


        if(hash("sha256", $body . $pass)!=$authhash) {
            throw new Exception('bad hash');
        }


        $curr='EUR';

        $mktime=microtime(1);

        logfile::create(PHP_EOL.PHP_EOL.PHP_EOL,'testevenbet');

        switch ($action){
            case 'login':

                $token=$req_json['token'];
                $new_token=md5($token.$mktime);

                $b = (float) file_get_contents('bbb');

                echo json_encode([
                     "token" => $new_token,
                     "balance" => $b,
                     "currency" => $curr,
                     "nickname" => "Andrew",
                     "country" => "China",
                     "timestamp" => implode('',explode('.',$mktime)),
                ]);

                break;
            case 'balance':

                $b = (float) file_get_contents('bbb');

                echo json_encode([
                    "balance" => $b,
                ]);

                break;
            case 'debit':
                $req_json=json_decode(file_get_contents('php://input'),1);

                logfile::create(date('Y-m-d H:i:s') .' DEBIT REQUEST: '.file_get_contents('php://input'),'testevenbet');

//                echo json_encode([
//                    'Status'=>'Error',
//                    'ErrorDescription'=>'TokenNotFound',
//                ]);
//                exit;

                $b = (float) file_get_contents('bbb');

                //work
                if(0 && mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' DEBIT CUSTOM ERROR BEFORE SAVE BET. BALANCE: '.$b,'testevenbet');

                    throw new Exception('asd3');
                }

                file_put_contents('last_transaction_id',$req_json['transactionId']);


                $b = $b-$req_json['amount'];

                file_put_contents('bbb',$b);

                //work
                if(0 && mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' DEBIT CUSTOM ERROR AFTER SAVE BET. BALANCE: '.$b,'testevenbet');

                    throw new Exception('asd4');
                }

                logfile::create(date('Y-m-d H:i:s') .' SUCCESS DEBIT. BALANCE: '.$b,'testevenbet');

                echo json_encode([
                    "balance" => $b,
                    "transactionId" => $req_json['transactionId'],
                ]);

                break;
            case 'rollback':
                $req_json=json_decode(file_get_contents('php://input'),1);

                $last_bet=file_get_contents('last_transaction_id');
                $last_win=file_get_contents('last_wintransaction_id');

                logfile::create(date('Y-m-d H:i:s') .' ROLLBACK REQUEST: '.file_get_contents('php://input'),'testevenbet');

                $b = (float) file_get_contents('bbb');

                if(!in_array($req_json['refTransactionId'],[$last_bet,$last_win])) {

                    logfile::create(date('Y-m-d H:i:s') .' ROLLBACK ERROR NOT FOUND. BALANCE: '.$b,'testevenbet');

                    throw new Exception('bet not found ['.$req_json['refTransactionId'].']');
                }

                if(mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' ROLLBACK CUSTOM ERROR BEFORE SAVE. BALANCE: '.$b,'testevenbet');

                    throw new Exception('asd1');
                }


                $b = $b+$req_json['amount'];

                file_put_contents('bbb',$b);

                if(mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' ROLLBACK CUSTOM ERROR AFTER SAVE. BALANCE: '.$b,'testevenbet');

                    throw new Exception('asd6');
                }

                file_put_contents('last_transaction_id','');
                file_put_contents('last_wintransaction_id','');

                logfile::create(date('Y-m-d H:i:s') .' SUCCESS ROLLBACK. BALANCE: '.$b,'testevenbet');

                echo json_encode([
                    "balance" => $b,
                    "transactionId" => $req_json['transactionId'],
                ]);

                break;
            case 'credit':
                $req_json=json_decode(file_get_contents('php://input'),1);

                logfile::create(date('Y-m-d H:i:s') .' WIN REQUEST: '.file_get_contents('php://input'),'testevenbet');

                $b = (float) file_get_contents('bbb');

                if(file_get_contents('last_wintransaction_id')==$req_json['transactionId']) {

                    //win already was
                    logfile::create(date('Y-m-d H:i:s') .' WIN REQUEST REPEAT!!','testevenbet');

                    echo json_encode([
                        "balance" => $b,
                        "transactionId" => $req_json['transactionId'],
                    ]);

                    exit;
                }

                if(mt_rand(0,4)==0) {
                    logfile::create(date('Y-m-d H:i:s') .' WIN CUSTOM ERROR BEFORE SAVE. BALANCE: '.$b,'testevenbet');
                    throw new Exception('asd2');
                }

                $checkWin=file_get_contents('last_wintransaction_id');

                if($checkWin==$req_json['transactionId']) {
                    logfile::create(date('Y-m-d H:i:s') .' WIN ALREADY EXITST. BALANCE: '.$b,'testevenbet');
                    throw new Exception('WIN ALREADY EXITST');
                }

                file_put_contents('last_wintransaction_id',$req_json['transactionId']);


                $b = $b+$req_json['amount'];

                file_put_contents('bbb',$b);


                if(mt_rand(0,4)==0) {
                    logfile::create(date('Y-m-d H:i:s') .' WIN CUSTOM ERROR AFTER SAVE. BALANCE: '.$b,'testevenbet');
                    throw new Exception('asd5');
                }

                logfile::create(date('Y-m-d H:i:s') .' SUCCESS WIN. BALANCE: '.$b,'testevenbet');

                echo json_encode([
                    "balance" => $b,
                    "transactionId" => $req_json['transactionId'],
                ]);

                break;
            default:
                throw new Exception('unknown action');
        }
        exit;
     }

    public function action_gamelist() {
        $static = kohana::$config->load('static');
        $languages = array_keys(Kohana::$config->load('languages.lang'));

        $office=new Model_Office(2158);

        $sort=$office->sort();
        $dbgames=$office->sorted_games;

        $games=[];
        foreach($dbgames as $v) {
            if($v['evenbet_show']=='0') {
                continue;
            }
            $a=[
                'name'=>$v['visible_name'],
                'gameId'=>$v['name'],
                'platform'=>'desktop,mobile',
                'image'=>str_replace('sqthumb','thumb',$v['image']),
                'languages'=>implode(',',$languages),
                'category'=>'slots',
            ];
            if($v['game_type']=='videopoker') {
                $a['category']='video_poker';
            }
            elseif($v['game_type']=='slot') {
                $a['category']='slots';
            }
            elseif($v['game_type']=='miner') {
                $a['category']='arcade';
            }
            elseif($v['game_type']=='shuffle') {
                $a['category']='slots';
            }
            elseif($v['game_type']=='roshambo') {
                $a['category']='casual';
            }
            elseif($v['game_type']=='keno') {
                $a['category']='lottery';
            }
            elseif($v['game_type']=='moon') {
                $a['category']='arcade';
            }
            $games[]=$a;
        }

        echo json_encode($games);
        exit;
    }

    protected function _error_response($message,$code=null) {
         echo json_encode([
             "Success" => false,
             "Message" => $message,
             "ErrorCode" => $code
         ]);
         exit;
     }

}
