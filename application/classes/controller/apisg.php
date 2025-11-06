<?php

class Controller_ApiSG extends Controller {

    protected $_api_url='';
    protected $_secretkey='';
    protected $_test_office=false;

    protected function _check_auth($login,$password) {
        if($login!='softgamings' || $password!='yoPS&ksd28rzd') {
            return false;
        }
        //todo need check or create auth
        return true;
    }


    public function  action_launch(){

        $body=file_get_contents('php://input');

        logfile::create(date('Y-m-d H:i:s') . 'Launch request: '.$body.PHP_EOL, 'softgamings');

        $req_json=json_decode($body,1);

        $api=new Api_SoftGamings();

        $api->setUpEnv($this->_test_office);

        $user_id=arr::get($req_json,'userID');
        $balance=arr::get($req_json,'balance');
        $currency=arr::get($req_json,'userCurrency');
        $country=arr::get($req_json,'userCountry');
        $license=arr::get($req_json,'license');
        $ip=arr::get($req_json,'userIP');
        $game=arr::get($req_json,'game');
        $is_demo=arr::get($req_json,'isDemo');
        $partner_id=arr::get($req_json,'partnerID');
        $casino_id=arr::get($req_json,'casinoID');
        $session_id=arr::get($req_json,'sessionID');
        $debug_code=arr::get($req_json,'debugCode');
        $request_id=arr::get($req_json,'requestID');

        $inSign=$req_json['sign'];
        unset($req_json['sign']);


        if($inSign!=$api->sign('launch',$req_json)) {
            throw new Exception('wrong sign: '.$inSign.' '.$api->sign('launch',$req_json));
        }

        $api->gameName=$game;
        $demobalance=2000;
        $exit_url='/black';

        if($is_demo) {
            $demoUrl='https://demo.kolinz.xyz/games/agt/'.$api->gameName.'?demobalance='.($demobalance*100);

			$ans=[
                'status',
                'url'=>$demoUrl,
            ];
            $ans['sign']=$api->sign('launch',$ans);

            echo json_encode($ans);
            exit;

            $this->request->redirect($demoUrl);
        }

        $currency=UTF8::strtoupper($currency);

        $office_id = $api->checkOffice($currency,$partner_id,$casino_id);

        if(!$office_id) {
            $office_id = $api->createOffice($currency,$partner_id,$casino_id);
        }
        else if(!$api->checkGame($office_id)) {
            throw new Exception("Game not found: {$office_id} {$api->gameName}");
        }

        $api->session_token=$session_id;

        $user_id = $api->checkUser($req_json,$office_id);

        if(!$user_id) {
            $user_id = $api->createUser($req_json,$office_id);
        }
        else {
            $wasWrongBets = $api->processWrongBets($user_id);

            if($wasWrongBets) {
                $updated_balance=$api->checkBalance($user_id);

                if(!$updated_balance) {
                    throw new Exception('cant update balance');
                }

                $auth_data['balance']=$updated_balance;
                $user_id = $api->checkUser($req_json,$office_id); //update balance again
            }
        }

        $ans=[
            'status',
            'url'=>$api->getGame($user_id,null,false,true,$exit_url),
        ];
        $ans['sign']=$api->sign('launch',$ans);

        echo json_encode($ans);
        exit;
    }

    public function  action_frametest(){
        $host='https://app.site-domain.local';
//        $host='https://apisg.site-domain.com';

        $casinoID=22;
        $userID='553344A';

        $game = $this->request->param('id');

        echo '<h1>Freespins</h1>';
        foreach(db::query(1,'select * from softgame_freerounds order by 1')->execute() as $sfr) {
            echo '<a href="/apisg/frametest?action=info&fsid='.$sfr['freeround_id'].'">'.$sfr['freeround_id'].'</a>'.($sfr['deleted']>0?'Deleted':'').'<br />';
//            echo '<a href="/apisg/frametest?action=delete&fsid='.$sfr['freeround_id'].'">'.$sfr['freeround_id'].'</a>'.($sfr['deleted']>0?'Deleted':'').'<br />';
        }
        echo '<hr>';

        $action=arr::get($_GET,'action');
        $fsid=arr::get($_GET,'fsid');
        if($action=='delete' && $fsid) {

            $delparams=[
                'requestID'=>uniqid(),
                'freeroundID'=>$fsid,
            ];

            $apidelete=new Api_SoftGamings();
            $apidelete->setUpEnv($this->_test_office);

            $delparams['sign']=$apidelete->sign('deleteFreerounds',$delparams);

            $p=new Parser();

            $delete_request=$p->post($host.'/apisg/deleteFreerounds',$delparams, true);
            var_dump($delete_request);
        }

        if($action=='info' && $fsid) {

            $infoparams=[
                'requestID'=>uniqid(),
                'freeroundID'=>$fsid,
                'userID'=>$userID,
            ];

            $apiinfo=new Api_SoftGamings();
            $apiinfo->setUpEnv($this->_test_office);

            $infoparams['sign']=$apiinfo->sign('getFreeroundsInfo',$infoparams);

            $p=new Parser();

            $info_request=$p->post($host.'/apisg/getFreeroundsInfo',$infoparams, true);
            var_dump($info_request);
        }

        if(!empty($game)) {

            echo '<form method="POST">
                fs_amount<input name="fs_amount" />
                fs_count<input name="fs_count" />
                <button type="submit">go</button>
                </form>';

            if($this->request->method()=='POST') {
                $apifs=new Api_SoftGamings();
                $apifs->setUpEnv($this->_test_office);

                $paramsfs=[
                    'userIDS'=>[$userID],
                    'casinoID'=>$casinoID,
                    'freeroundID'=>mt_rand(1000,9999)."",
                    'count'=>$_POST['fs_count'],
                    'betValue'=>$_POST['fs_amount'],
                    'games'=>['hotpepper100','bigfive','greenhot','bluestar100'],
                    'expireDate'=>date(DATE_ISO8601,time()+24*60*60),
                    'requestID'=>uniqid(),
                ];

                $paramsfs['sign']=$apifs->sign('createFreerounds',$paramsfs);

                $pfs=new Parser();

                $createFS_request=$pfs->post($host.'/apisg/createFreerounds',$paramsfs, true);

                if($createFS_request) {
                    $createFS_request=json_decode($createFS_request,1);
                    echo '<pre>';
                    var_dump($createFS_request,$paramsfs);
                    echo '</pre>';
                    if($createFS_request['status']!=4001) {
                        echo 'not okk';
                    }
                    else {
                        echo 'OK';
                    }
                }
                else {
                    echo 'not ok';
                }
            }

            $params=[
                'userID'=>$userID,
                'balance'=>file_get_contents('bbb'),
                'userCurrency'=>'TRY',
                'userCountry'=>'es',
                'requestID'=>uniqid(),
                'sessionID'=>guid::create(),
                'IsDemo'=>0,
                'partnerID'=>11,
                'casinoID'=>$casinoID,
                'game'=>$game,
            ];

            $api=new Api_SoftGamings();
            $api->setUpEnv($this->_test_office);

            $params['sign']=$api->sign('launch',$params);

            $p=new Parser();

            $gamelaunch_request=$p->post($host.'/apisg/launch',$params, true);

            if(!$gamelaunch_request) {
                throw new Exception('error launch');
            }

            $gamelaunch_request=json_decode($gamelaunch_request,1);

            if(!$gamelaunch_request) {
                throw new Exception('error launch2');
            }

            echo '<iframe width="100%" height="100%" src="' . $gamelaunch_request['url'] . '"></iframe>';
            exit;
        }

        $params=[
            'requestID'=>uniqid(),
//            'debugCode'=>'2000'
        ];

        $api=new Api_SoftGamings();
        $api->setUpEnv($this->_test_office);

        $params['sign']=$api->sign('getGamesList',$params);

        $p=new Parser();

        $gamelist_request=$p->post($host.'/apisg/getGamesList',$params, true);

        if(!$gamelist_request) {
            throw new Exception('where is my game list?');
        }

        $gamelist_request=json_decode($gamelist_request,1);

        if($gamelist_request && $gamelist_request['games']) {
            foreach($gamelist_request['games'] as $gameArr) {
                echo '<a href="/apisg/frametest/'.$gameArr['gameid'].'">'.$gameArr['name'].'</a><br />';
            }
        }
        else {
            echo 'no games';
        }

    }

    public function  action_testlaunch(){

        $pass='test123';

        $action=$this->request->param('id');

        $body=file_get_contents('php://input');

        $req_json=json_decode($body,1);

        $api=new Api_SoftGamings();

//        if(hash("sha256", $body . $pass)!=$authhash) {
//            throw new Exception('bad hash');
//        }


        $curr='TRY';

        $mktime=microtime(1);

        logfile::create(PHP_EOL.PHP_EOL.PHP_EOL,'testsg');

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
                    "status" => 1001,
                    "balance" => $b,
                ]);

                break;
            case 'bet':
            case 'betwin':
                $req_json=json_decode(file_get_contents('php://input'),1);

                logfile::create(date('Y-m-d H:i:s') .' DEBIT REQUEST: '.file_get_contents('php://input'),'testsg');

//                echo json_encode([
//                    'Status'=>'Error',
//                    'ErrorDescription'=>'TokenNotFound',
//                ]);
//                exit;

                $b = (float) file_get_contents('bbb');

                //work
                if(0 && mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' DEBIT CUSTOM ERROR BEFORE SAVE BET. BALANCE: '.$b,'testsg');

                    throw new Exception('asd3');
                }

                file_put_contents('last_transaction_id',$req_json['betID']);

                if($action=='bet') {
                    $b = $b-$req_json['amount'];
                }
                else {
                    $b = $b-$req_json['betAmount']+$req_json['winAmount'];
                }

                file_put_contents('bbb',$b);

                //work
                if(0 && mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' DEBIT CUSTOM ERROR AFTER SAVE BET. BALANCE: '.$b,'testsg');

                    throw new Exception('asd4');
                }

                logfile::create(date('Y-m-d H:i:s') .' SUCCESS DEBIT. BALANCE: '.$b,'testsg');

                $ans=[
                    "status" => 1001,
                    "balance" => $b,
                    "userID" => $req_json['userID'],
                    "sessionID" => $req_json['sessionID'],
                    "roundID" => $req_json['roundID'],
                    "betID" => $req_json['betID'],
                ];

                if(isset($req_json['freeroundID'])) {
                    $ans['freeroundID']=$req_json['freeroundID'];
                }

                if($action=='betwin') {
                    $ans['winID']=$req_json['winID'];
                }

                $ans['sign']=$api->sign('bet',$ans);

                echo json_encode($ans);

                break;
            case 'refund':
                $req_json=json_decode(file_get_contents('php://input'),1);

                $last_bet=file_get_contents('last_transaction_id');
                $last_win=file_get_contents('last_wintransaction_id');

                logfile::create(date('Y-m-d H:i:s') .' ROLLBACK REQUEST: '.file_get_contents('php://input'),'testsg');

                $b = (float) file_get_contents('bbb');

                if(!in_array($req_json['betID'],[$last_bet])) {

                    logfile::create(date('Y-m-d H:i:s') .' ROLLBACK ERROR NOT FOUND. BALANCE: '.$b,'testsg');

                    throw new Exception('bet not found ['.$req_json['betID'].']');
                }

                if(0 && mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' ROLLBACK CUSTOM ERROR BEFORE SAVE. BALANCE: '.$b,'testsg');

                    throw new Exception('asd1');
                }


                $b = $b+$req_json['amount'];

                file_put_contents('bbb',$b);

                if(0 && mt_rand(0,4)==0) {

                    logfile::create(date('Y-m-d H:i:s') .' ROLLBACK CUSTOM ERROR AFTER SAVE. BALANCE: '.$b,'testsg');

                    throw new Exception('asd6');
                }

                file_put_contents('last_transaction_id','');
                file_put_contents('last_wintransaction_id','');

                logfile::create(date('Y-m-d H:i:s') .' SUCCESS ROLLBACK. BALANCE: '.$b,'testsg');

                $ans=[
                    "status" => 1004,
                    "balance" => $b,
                    "userID" => $req_json['userID'],
                    "sessionID" => $req_json['sessionID'],
                    "roundID" => $req_json['roundID'],
                    "betID" => $req_json['betID'],
                ];

                if(isset($req_json['freeroundID'])) {
                    $ans['freeroundID']=$req_json['freeroundID'];
                }

                if($action=='betwin') {
                    $ans['winID']=$req_json['winID'];
                }

                $ans['sign']=$api->sign('bet',$ans);

                echo json_encode($ans);

                break;
            case 'win':
                $req_json=json_decode(file_get_contents('php://input'),1);

                logfile::create(date('Y-m-d H:i:s') .' WIN REQUEST: '.file_get_contents('php://input'),'testsg');

                $b = (float) file_get_contents('bbb');

                if(file_get_contents('last_wintransaction_id')==$req_json['winID']) {

                    //win already was
                    logfile::create(date('Y-m-d H:i:s') .' WIN REQUEST REPEAT!!','testsg');

                    $ans=[
                        "status" => 1002,
                        "balance" => $b,
                        "userID" => $req_json['userID'],
                        "sessionID" => $req_json['sessionID'],
                        "roundID" => $req_json['roundID'],
                        "betID" => $req_json['betID'],
                        "winID" => $req_json['winID'],
                    ];

                    if(isset($req_json['freeroundID'])) {
                        $ans['freeroundID']=$req_json['freeroundID'];
                    }

                    echo json_encode($ans);

                    exit;
                }

                if(mt_rand(0,4)==0) {
                    logfile::create(date('Y-m-d H:i:s') .' WIN CUSTOM ERROR BEFORE SAVE. BALANCE: '.$b,'testsg');
                    throw new Exception('asd2');
                }

                $checkWin=file_get_contents('last_wintransaction_id');

                if($checkWin==$req_json['winID']) {
                    logfile::create(date('Y-m-d H:i:s') .' WIN ALREADY EXITST. BALANCE: '.$b,'testsg');
                    throw new Exception('WIN ALREADY EXITST');
                }

                file_put_contents('last_wintransaction_id',$req_json['winID']);


                $b = $b+$req_json['amount'];

                file_put_contents('bbb',$b);


                if(mt_rand(0,4)==0) {
                    logfile::create(date('Y-m-d H:i:s') .' WIN CUSTOM ERROR AFTER SAVE. BALANCE: '.$b,'testsg');
                    throw new Exception('asd5');
                }

                logfile::create(date('Y-m-d H:i:s') .' SUCCESS WIN. BALANCE: '.$b,'testsg');

                $ans=[
                    "status" => 1002,
                    "balance" => $b,
                    "userID" => $req_json['userID'],
                    "sessionID" => $req_json['sessionID'],
                    "roundID" => $req_json['roundID'],
                    "betID" => $req_json['betID'],
                    "winID" => $req_json['winID'],
                ];

                if(isset($req_json['freeroundID'])) {
                    $ans['freeroundID']=$req_json['freeroundID'];
                }

                echo json_encode($ans);

                break;
            default:
                throw new Exception('unknown action');
        }
        exit;
     }

    public function action_getGamesList() {

        $body=file_get_contents('php://input');

		logfile::create(date('Y-m-d H:i:s') . 'getGamesList request: ' . $body . PHP_EOL, 'softgamings');

        $req_json=json_decode($body,1);

        if(!isset($req_json['requestID']) || empty($req_json['requestID'])) {
            throw new Exception('no requestID');
        }

        $debugCode=arr::get($req_json,'debugCode');

        $api=new Api_SoftGamings();
        $api->setUpEnv($this->_test_office);

        $inSign=$req_json['sign'];
        unset($req_json['sign']);
        
        if($inSign!=$api->sign('getGamesList',$req_json)) {
            throw new Exception('wrong sign');
        }

        if(!empty($debugCode)) {
            $a=[
                'status'=>$debugCode,
                'msg'=>$api->getError($debugCode)
            ];
            $a['sign']=$api->sign('getGamesList',$a);
            echo json_encode($a);
            exit;
        }

        $static = kohana::$config->load('static');
        $languages = array_keys(Kohana::$config->load('languages.lang'));

        $office=new Model_Office(1673);
        $office->sort();

        $dbgames = $office->sorted_games;

        $games=[];
        foreach ($dbgames as $v) {
            if($v['softg_show']=='0') {
                continue;
            }
			if(in_array($v['name'],['supabets'])) {
                continue;
            }
            $a = [
                'gameid' => $v['name'],
                'name' => $v['visible_name'],
                'isFreeround' => ((int)th::cantFSback($v['name'])) . '',
                'isJackpot' => ((int)($v['game_type'] == 'moon')) . '',
                'isLive' => '0',
                'hasDemo' => '1',
            ];
            $games[] = $a;
        }
        $ans=[
            'status'=>1000,
            'games'=>$games,
        ];
        $ans['sign']=$api->sign('getGamesList',$ans);
        echo json_encode($ans);
		
		logfile::create(date('Y-m-d H:i:s') . 'getGamesList response: ' . json_encode($ans) . PHP_EOL, 'softgamings');
		
        exit;
    }

    public function action_getFreeroundsInfo() {
        $body=file_get_contents('php://input');

        $req_json=json_decode($body,1);

        if(!isset($req_json['requestID']) || empty($req_json['requestID'])) {
            throw new Exception('no requestID');
        }

        $api=new Api_SoftGamings();
        $api->setUpEnv($this->_test_office);

        $inSign=$req_json['sign'];
        unset($req_json['sign']);

        if($inSign!=$api->sign('getFreeroundsInfo',$req_json)) {
            throw new Exception('wrong sign');
        }

        $debugCode=arr::get($req_json,'debugCode');

        $freeroundID=$req_json['freeroundID'];
        $userID=$req_json['userID'];

        $freeround = new Model_SoftGameFreeround(['freeround_id'=>$freeroundID]);
        $user = new Model_User(['external_id'=>$userID,'api'=>8]);

        if(!$freeround->loaded()) {
            $a=[
                'status'=>5001,
                'msg'=>$api->getError(5001)
            ];
            $a['sign']=$api->sign('getFreeroundsInfo',$a);
            echo json_encode($a);
            exit;
        }

        if(!$user->loaded()) {
            $a=[
                'status'=>5000,
                'msg'=>$api->getError(5000).' User not found'
            ];
            $a['sign']=$api->sign('getFreeroundsInfo',$a);
            echo json_encode($a);
            exit;
        }

        $freespin=new Model_Freespinhistory([
            'user_id'=>$user->id,
            'fs_offer_type'=>'softgaming',
            'fs_offer_id'=>$freeround->id,
        ]);

        if(!$freespin->loaded()) {
            $a=[
                'status'=>5000,
                'msg'=>$api->getError(5000).' Empty FS history'
            ];
            $a['sign']=$api->sign('getFreeroundsInfo',$a);
            echo json_encode($a);
            exit;
        }

        $games=array_keys(db::query(1,'select name from games where id in :ids')->param(':ids',$freespin->gameids)->execute()->as_array('name'));

        $a=[
            'status'=>4003,
            'userID'=>$userID,
            'freeroundID'=>$freeroundID,
            'freeroundsLeft'=>$freespin->fs_count-$freespin->fs_played,
            'betValue'=>$freespin->amount,
            'games'=>$games,
            'expireDate'=>$freeround->expire_date,
        ];
        $a['sign']=$api->sign('deleteFreerounds',$a);
        echo json_encode($a);
        exit;
    }

    public function action_deleteFreerounds() {
        $body=file_get_contents('php://input');

		logfile::create(date('Y-m-d H:i:s') . 'deleteFreerounds request: ' . $body . PHP_EOL, 'softgamings');
		
        $req_json=json_decode($body,1);

		logfile::create(date('Y-m-d H:i:s') . 'deleteFreerounds request JSON: ' . print_r($req_json,1) . PHP_EOL, 'softgamings');
		
        if(!isset($req_json['requestID']) || empty($req_json['requestID'])) {
            throw new Exception('no requestID');
        }

        $api=new Api_SoftGamings();
        $api->setUpEnv($this->_test_office);

        $inSign=$req_json['sign'];
        unset($req_json['sign']);

        if($inSign!=$api->sign('deleteFreerounds',$req_json)) {
            throw new Exception('wrong sign');
        }

        $debugCode=arr::get($req_json,'debugCode');

        $freeroundID="".$req_json['freeroundID'];

        $freeround = new Model_SoftGameFreeround(['freeround_id'=>$freeroundID,'deleted'=>0]);

        if($freeround->loaded()) {
            database::instance()->begin();
            try {
                db::query(database::DELETE,'delete from freespins where fs_offer_type=\'softgaming\' and fs_offer_id=:fid')
                    ->param(':fid',$freeroundID)
                    ->execute();
                db::query(database::UPDATE,'update freespins_history set active=-3, expirtime=:expirtime where fs_offer_type=\'softgaming\' and fs_offer_id=:fid')
                    ->param(':expirtime',time())
                    ->param(':fid',$freeroundID)
                    ->execute();
                $freeround->deleted=time();
                $freeround->save();

                database::instance()->commit();
            }
            catch (Database_Exception $e) {
                database::instance()->rollback();

                $a=[
                    'status'=>5000,
                    'msg'=>$api->getError(5000)
                ];
				
				logfile::create(date('Y-m-d H:i:s') . 'deleteFreerounds response: [ERROR]' . $e->getMessage() . PHP_EOL, 'softgamings');

				
                $a['sign']=$api->sign('deleteFreerounds',$a);
                echo json_encode($a);
                exit;
            }

            $a=[
                'status'=>4002,
                'freeroundID'=>$freeroundID,
            ];
			
			logfile::create(date('Y-m-d H:i:s') . 'deleteFreerounds response: ' . json_encode($a) . PHP_EOL, 'softgamings');

			
            $a['sign']=$api->sign('deleteFreerounds',$a);
            echo json_encode($a);
            exit;
        }
        else {
            $a=[
                'status'=>5001,
                'msg'=>$api->getError(5001)
            ];
			
			logfile::create(date('Y-m-d H:i:s') . 'deleteFreerounds response: [ERROR2]' . json_encode($a) . PHP_EOL, 'softgamings');
            
            $a['sign']=$api->sign('deleteFreerounds',$a);
            echo json_encode($a);
            exit;
        }
    }

    public function action_createFreerounds() {

        $body=file_get_contents('php://input');

		logfile::create(date('Y-m-d H:i:s') . 'createFreerounds request: ' . $body . PHP_EOL, 'softgamings');
		
		$partnerID=0; //нет в апи поддержки разных partnerID!!!

        $req_json=json_decode($body,1);

        if(!isset($req_json['requestID']) || empty($req_json['requestID'])) {
            throw new Exception('no requestID');
        }

        $debugCode=arr::get($req_json,'debugCode');

        $freeroundID=$req_json['freeroundID'];

        $freeround = new Model_SoftGameFreeround(['freeround_id'=>$freeroundID]);

        $api=new Api_SoftGamings();
        $api->setUpEnv($this->_test_office);

        if(!empty($debugCode)) {
            $a=[
                'status'=>$debugCode,
                'msg'=>$api->getError($debugCode)
            ];
            $a['sign']=$api->sign('createFreerounds',$a);
			logfile::create(date('Y-m-d H:i:s') . 'createFreerounds response: [DEBUG] ' . json_encode($a) . PHP_EOL, 'softgamings');
            echo json_encode($a);
            exit;
        }

        if (!$freeround->loaded()) {

            $freeround->user_ids = $req_json['userIDS'];
            $freeround->casino_id = $req_json['casinoID'];
            $freeround->freeround_id = $req_json['freeroundID'];
            $freeround->fs_count = $req_json['count'];
            $freeround->fs_amount = $req_json['betLevel'];
//            $freeround->fs_amount = $req_json['betValue'];

            $freeround->games = $req_json['games'];
            $freeround->expire_date = strtotime($req_json['expireDate']);

            $freeround->save();

            $sqlgames = 'select id,name from games where show=1 and brand=\'agt\' and type in :allowedtypes';

            if (!empty($req_json['games'])) {
                $sqlgames .= ' and name in :names';
            }

            $game_ids = db::query(1, $sqlgames)
                ->param(':names', $req_json['games'])
                ->param(':allowedtypes', ['slot', 'shuffle'])
                ->execute()
                ->as_array('id');

            if (!empty($game_ids)) {
                $game_ids = array_keys($game_ids);
            } else {
                $game_ids = null;
            }

            $currencies=db::query(1,'select o.id as o_id,o.external_name, c.id as c_id, c.code from currencies c 
                            join offices o on c.id=o.currency_id 
                            where o.apitype=8 and o.is_test=:test and o.blocked=0 and o.external_name like \'sg\'||c.id||:ex and c.source=\'agt\' and c.code in :codes')
                ->param(':test',(int) $this->_test_office)
                ->param(':ex',$partnerID.$freeround->casino_id)
                ->param(':codes',array_values((array) $freeround->user_ids))
                ->execute()
                ->as_array('code');


            foreach ($freeround->user_ids as $userID=>$currency) {

                $o_id=$currencies[$currency]['o_id'];

                $u = new Model_User(['office_id'=>$o_id,'external_id' => $userID, 'api' => 8]);

                if (!$u->loaded()) {
                    $u = $api->createAnonym($userID,$o_id);
                }

                if (!$u->blocked) {

                    $dentab_index = 0;

                    $fs = new Model_Freespin();
                    $fs->fs_offer_id = $freeround->id;
                    $fs->fs_offer_type = 'softgaming';
                    $last_fs_id = $fs->giveFreespins(
                        $u->id,
                        $o_id,
                        $game_ids,
                        $freeround->fs_count,
                        $freeround->fs_amount,
                        0,
                        $dentab_index,
                        'api',
                        false, null, $freeround->expire_date);

                    if (!$last_fs_id) {
                        $a = [
                            'status' => 5004,
                            'msg' => $api->getError(5004)
                        ];
                        $a['sign'] = $api->sign('createFreerounds', $a);

                        logfile::create(date('Y-m-d H:i:s') . 'createFreerounds response: [ERROR] ' . json_encode($a) . PHP_EOL, 'softgamings');

                        echo json_encode($a);
                        exit;
                    }
                }
            }

            $a = [
                'status' => 4001,
                'freeroundID' => $req_json['freeroundID']
            ];
            $a['sign'] = $api->sign('createFreerounds', $a);
            logfile::create(date('Y-m-d H:i:s') . 'createFreerounds response: [OK] ' . json_encode($a) . PHP_EOL, 'softgamings');
            echo json_encode($a);
            exit;
        } else {
            $a = [
                'status' => 5004,
                'msg' => $api->getError(5004) . '; Freeround compaign already exists'
            ];
            $a['sign'] = $api->sign('createFreerounds', $a);
            logfile::create(date('Y-m-d H:i:s') . 'createFreerounds response: [ERROR] ' . json_encode($a) . PHP_EOL, 'softgamings');
            echo json_encode($a);
            exit;
        }
    }
	
	public function action_getRoundView() {

        $body = file_get_contents('php://input');

        $req_json = json_decode($body, 1);

        if (!isset($req_json['roundID']) || empty($req_json['roundID'])) {
            throw new Exception('no roundID');
        }

        $api = new Api_SoftGamings();
        $api->setUpEnv($this->_test_office);

        $bet_id = $req_json['roundID'];

        $sql = 'select id from bets_id where server=1 and id=:b_id limit 1';

        $data = db::query(1, $sql)
            ->parameters([':b_id'=>$bet_id])
            ->execute('clickhouse')->as_array();

        if(empty($data)) {
            $a = [
                'status' => 2000,
                'msg' => $api->getError(2000) . '; Round not found'
            ];
            $a['sign'] = $api->sign('getRoundView', $a);
            echo json_encode($a);
            return;
        }

        $domain = kohana::$config->load('static.gameapi_domen_softgamings');
        $parsed=parse_url($domain);
        $parsed=explode('.',$parsed['host']);

        if(count($parsed)>2) {
            $domain=implode('.',[$parsed[count($parsed)-2],$parsed[count($parsed)-1]]);
        }

        $result='https://history.'.$domain.'/bet.php?b='.$bet_id;

        $a = [
            'status' => 2000,
            'url' => $result
        ];
        $a['sign'] = $api->sign('getRoundView', $a);
        echo json_encode($a);
        exit;
    }

}
