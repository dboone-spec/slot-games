<?php

class Controller_ApiBC extends Controller
{

    protected $_api_url = '';
    protected $_secretkey = '';
    protected $_test_office = true;

    protected function _check_auth($login, $password)
    {
        if ($login != 'betconstruct' || $password != 'GHoP!fd=a28ras') {
            return false;
        }
        //todo need check or create auth
        return true;
    }

    //https://site-domain.local/apibc/launch?mode=demo&gameID=bigfive&language=eng
    //https://site-domain.local/apibc/launch?mode=real_play&gameID=bigfive&language=eng&token=123456789

    public function action_launch()
    {

        logfile::create(date('Y-m-d H:i:s') . 'Launch request: ' . print_r($this->request->query(),1) . PHP_EOL.'refferer: '.$this->request->referrer().PHP_EOL, 'betconstruct');

        $req_json = $this->request->query();

        $api = new Api_BetConstruct();

        $api->setUpEnv($this->_test_office);

        $mode = arr::get($req_json, 'mode');
        $token = arr::get($req_json, 'token');
        $game = arr::get($req_json, 'gameID');
        $casinoId = arr::get($req_json, 'casinoId');
        $is_demo = ($mode=='demo');
        $lang = arr::get($req_json, 'language');

        $api->gameName = $game;
        $demobalance = 2000;
        $exit_url = arr::get($req_json,'homeURL','/black');

        if($exit_url!==urldecode($exit_url)) {
            $exit_url=urldecode($exit_url);
        }
		
		$exit_url=urlencode($exit_url);

        if ($is_demo) {
            $demoUrl = 'https://demo.kolinz.xyz/games/agt/' . $api->gameName . '?demobalance=' . ($demobalance * 100);
            $demoUrl.='&closeurl='.$exit_url;
			$demoUrl.='&lang='.$lang;

            $this->request->redirect($demoUrl);
        }

        $playerInfo=$api->getPlayerInfo($token);

        if(!$playerInfo) {
            throw new Exception('no player info');
        }

        $playerInfo['userID']=''.$playerInfo['userID'];

        $currency = UTF8::strtoupper($playerInfo['currencyId']);

        $office_id = $api->checkOffice($currency,$casinoId);

        if (!$office_id) {
            $office_id = $api->createOffice($currency,$casinoId);
        } else if (!$api->checkGame($office_id)) {
            throw new Exception("Game not found: {$office_id} {$api->gameName}");
        }

        $api->session_token = $token;


        $user_id = $api->checkUser($playerInfo['userID'],$playerInfo['totalBalance'], $office_id);


        if (!$user_id) {
            $user_id = $api->createUser($playerInfo['userID'],$playerInfo['totalBalance'], $office_id,$playerInfo['nickName']);

            auth::setCustomSessionId($user_id,$api->session_token);
        } else {

            auth::setCustomSessionId($user_id,$api->session_token);

            $wasWrongBets = $api->processWrongBets($user_id);

            if ($wasWrongBets) {
                $updated_balance = $api->getPlayerInfo($token);

                if (!$updated_balance) {
                    throw new Exception('cant update balance');
                }

                $user_id = $api->checkUser($playerInfo['userID'],$updated_balance['totalBalance'], $office_id); //update balance again
            }
        }

        $this->request->redirect($api->getGame($user_id,$lang,false,false,$exit_url));
    }

    public function action_frametest()
    {
        $host = 'https://app.site-domain.local';
//        $host='https://apisg.site-domain.com';

        $casinoID = 24;
        $userID = '553314AA1';

        $game = $this->request->param('id');

        echo '<h1>Freespins</h1>';
        foreach (db::query(1, 'select * from softgame_freerounds order by 1')->execute() as $sfr) {
            echo '<a href="/apisg/frametest?action=info&fsid=' . $sfr['freeround_id'] . '">' . $sfr['freeround_id'] . '</a>' . ($sfr['deleted'] > 0 ? 'Deleted' : '') . '<br />';
//            echo '<a href="/apisg/frametest?action=delete&fsid='.$sfr['freeround_id'].'">'.$sfr['freeround_id'].'</a>'.($sfr['deleted']>0?'Deleted':'').'<br />';
        }
        echo '<hr>';

        $action = arr::get($_GET, 'action');
        $fsid = arr::get($_GET, 'fsid');
        if ($action == 'delete' && $fsid) {

            $delparams = [
                'requestID' => uniqid(),
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
                'requestID' => uniqid(),
                'freeroundID' => $fsid,
                'userID' => $userID,
            ];

            $apiinfo = new Api_SoftGamings();
            $apiinfo->setUpEnv($this->_test_office);

            $infoparams['sign'] = $apiinfo->sign('getFreeroundsInfo', $infoparams);

            $p = new Parser();

            $info_request = $p->post($host . '/apisg/getFreeroundsInfo', $infoparams, true);
            var_dump($info_request);
        }

        if (!empty($game)) {

            echo '<form method="POST">
                fs_amount<input name="fs_amount" />
                fs_count<input name="fs_count" />
                <button type="submit">go</button>
                </form>';

            if ($this->request->method() == 'POST') {
                $apifs = new Api_SoftGamings();
                $apifs->setUpEnv($this->_test_office);

                $paramsfs = [
                    'userIDS' => [$userID],
                    'casinoID' => $casinoID,
                    'freeroundID' => mt_rand(1000, 9999) . "",
                    'count' => $_POST['fs_count'],
                    'betValue' => $_POST['fs_amount'],
                    'games' => [0],
                    'expireDate' => date(DATE_ISO8601, time() + 24 * 60 * 60),
                    'requestID' => uniqid(),
                ];

                $paramsfs['sign'] = $apifs->sign('createFreerounds', $paramsfs);

                $pfs = new Parser();

                $createFS_request = $pfs->post($host . '/apisg/createFreerounds', $paramsfs, true);

                if ($createFS_request) {
                    $createFS_request = json_decode($createFS_request, 1);
                    echo '<pre>';
                    var_dump($createFS_request, $paramsfs);
                    echo '</pre>';
                    if ($createFS_request['status'] != 4001) {
                        echo 'not okk';
                    } else {
                        echo 'OK';
                    }
                } else {
                    echo 'not ok';
                }
            }

            $params = [
                'userID' => $userID,
                'balance' => file_get_contents('bbb'),
                'userCurrency' => 'RUB',
                'userCountry' => 'es',
                'requestID' => uniqid(),
                'sessionID' => guid::create(),
                'isDemo' => 0,
                'partnerID' => 127,
                'casinoID' => $casinoID,
                'game' => $game,
            ];

            $api = new Api_SoftGamings();
            $api->setUpEnv($this->_test_office);

            $params['sign'] = $api->sign('launch', $params);

//            $params = json_decode('{"userID":"14528d_DemoUser","balance":"0","userCurrency":"EUR","userCountry":null,"userIP":"188.130.240.79","userLang":"ru","game":"keno","isDemo":1,"partnerID":0,"casinoID":"14528","sessionID":"276430869-6ee0a40556c44b125234ec808b612eae","requestID":"16826109671353870","sign":"d5b559c8334bfb16f4c91999330a19cc"}', 1);


            $p = new Parser();

            $gamelaunch_request = $p->post($host . '/apisg/launch', $params, true);


            if (!$gamelaunch_request) {
                throw new Exception('error launch');
            }

            $gamelaunch_request = json_decode($gamelaunch_request, 1);

            if (!$gamelaunch_request) {
                throw new Exception('error launch2');
            }

            echo '<iframe width="100%" height="100%" src="' . $gamelaunch_request['url'] . '"></iframe>';
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

        $gamelist_request = $p->post($host . '/apisg/getGamesList', $params, true);

        if (!$gamelist_request) {
            throw new Exception('where is my game list?');
        }

        $gamelist_request = json_decode($gamelist_request, 1);

        if ($gamelist_request && $gamelist_request['games']) {
            foreach ($gamelist_request['games'] as $gameArr) {
                echo '<a href="/apisg/frametest/' . $gameArr['gameid'] . '">' . $gameArr['name'] . '</a><br />';
            }
        } else {
            echo 'no games';
        }

    }

    public function action_testlaunch()
    {

        $pass = 'test123';

        $action = $this->request->param('id');

        $body = file_get_contents('php://input');

        $req_json = json_decode($body, 1);

        $api = new Api_BetConstruct();

//        if(hash("sha256", $body . $pass)!=$authhash) {
//            throw new Exception('bad hash');
//        }


        $curr = 'RUB';

        $mktime = microtime(1);

        switch ($action) {
            case 'login':

                $token = $req_json['token'];
                $new_token = md5($token . $mktime);

                $b = (float)file_get_contents('bbb');

                echo json_encode([
                    "token" => $new_token,
                    "balance" => $b,
                    "currency" => $curr,
                    "nickname" => "Andrew",
                    "country" => "China",
                    "timestamp" => implode('', explode('.', $mktime)),
                ]);

                break;
            case 'balance':

                $b = (float)file_get_contents('bbb');

                echo json_encode([
                    "status" => 1001,
                    "balance" => $b,
                ]);

                break;
            case 'bet':
            case 'betwin':
                $req_json = json_decode(file_get_contents('php://input'), 1);

                logfile::create(date('Y-m-d H:i:s') . ' DEBIT REQUEST: ' . file_get_contents('php://input'), 'testsg');

//                echo json_encode([
//                    'status'=>'3000',
//                ]);
//                exit;

                $b = (float)file_get_contents('bbb');

                //work
                if (0 && mt_rand(0, 4) == 0) {

                    logfile::create(date('Y-m-d H:i:s') . ' DEBIT CUSTOM ERROR BEFORE SAVE BET. BALANCE: ' . $b, 'testsg');

                    throw new Exception('asd3');
                }

                file_put_contents('last_transaction_id', $req_json['betID']);

                if ($action == 'bet') {
                    $b = $b - $req_json['amount'];
                } else {
                    $b = $b - $req_json['betAmount'] + $req_json['winAmount'];
                }

                if($b<=0) {
                    throw new Exception('wrong amount');

                }

                file_put_contents('bbb', $b);

                //work
                if (1 && mt_rand(0, 2) == 0) {

                    logfile::create(date('Y-m-d H:i:s') . ' DEBIT CUSTOM ERROR AFTER SAVE BET. BALANCE: ' . $b, 'testsg');

                    throw new Exception('asd4');
                }

                logfile::create(date('Y-m-d H:i:s') . ' SUCCESS DEBIT. BALANCE: ' . $b, 'testsg');

                $ans = [
                    "status" => 1001,
                    "balance" => $b,
                    "userID" => $req_json['userID'],
                    "sessionID" => $req_json['sessionID'],
                    "roundID" => $req_json['roundID'],
                    "betID" => $req_json['betID'],
                ];

                if (isset($req_json['freeroundID'])) {
                    $ans['freeroundID'] = $req_json['freeroundID'];
                }

                if ($action == 'betwin') {
                    $ans['winID'] = $req_json['winID'];
                }

                $ans['sign'] = $api->sign('bet', $ans);

                echo json_encode($ans);

                break;
            case 'refund':
                $req_json = json_decode(file_get_contents('php://input'), 1);

                $last_bet = file_get_contents('last_transaction_id');
                $last_win = file_get_contents('last_wintransaction_id');

                logfile::create(date('Y-m-d H:i:s') . ' ROLLBACK REQUEST: ' . file_get_contents('php://input'), 'testsg');

                $b = (float)file_get_contents('bbb');

                if (!in_array($req_json['betID'], [$last_bet])) {

                    logfile::create(date('Y-m-d H:i:s') . ' ROLLBACK ERROR NOT FOUND. BALANCE: ' . $b, 'testsg');

                    throw new Exception('bet not found [' . $req_json['betID'] . ']');
                }

                if (0 && mt_rand(0, 4) == 0) {

                    logfile::create(date('Y-m-d H:i:s') . ' ROLLBACK CUSTOM ERROR BEFORE SAVE. BALANCE: ' . $b, 'testsg');

                    throw new Exception('asd1');
                }


                $b = $b + $req_json['amount'];

                file_put_contents('bbb', $b);

                if (0 && mt_rand(0, 4) == 0) {

                    logfile::create(date('Y-m-d H:i:s') . ' ROLLBACK CUSTOM ERROR AFTER SAVE. BALANCE: ' . $b, 'testsg');

                    throw new Exception('asd6');
                }

                file_put_contents('last_transaction_id', '');
                file_put_contents('last_wintransaction_id', '');

                logfile::create(date('Y-m-d H:i:s') . ' SUCCESS ROLLBACK. BALANCE: ' . $b, 'testsg');

                $ans = [
                    "status" => 1004,
                    "balance" => $b,
                    "userID" => $req_json['userID'],
                    "sessionID" => $req_json['sessionID'],
                    "roundID" => $req_json['roundID'],
                    "betID" => $req_json['betID'],
                ];

                if (isset($req_json['freeroundID'])) {
                    $ans['freeroundID'] = $req_json['freeroundID'];
                }

                if ($action == 'betwin') {
                    $ans['winID'] = $req_json['winID'];
                }

                $ans['sign'] = $api->sign('bet', $ans);

                echo json_encode($ans);

                break;
            case 'win':
                $req_json = json_decode(file_get_contents('php://input'), 1);

                logfile::create(date('Y-m-d H:i:s') . ' WIN REQUEST: ' . file_get_contents('php://input'), 'testsg');

                $b = (float)file_get_contents('bbb');

                if (file_get_contents('last_wintransaction_id') == $req_json['winID']) {

                    //win already was
                    logfile::create(date('Y-m-d H:i:s') . ' WIN REQUEST REPEAT!!', 'testsg');

                    $ans = [
                        "status" => 1002,
                        "balance" => $b,
                        "userID" => $req_json['userID'],
                        "sessionID" => $req_json['sessionID'],
                        "roundID" => $req_json['roundID'],
                        "betID" => $req_json['betID'],
                        "winID" => $req_json['winID'],
                    ];

                    if (isset($req_json['freeroundID'])) {
                        $ans['freeroundID'] = $req_json['freeroundID'];
                    }

                    echo json_encode($ans);

                    exit;
                }

                if (0 && mt_rand(0, 3) == 0) {
                    logfile::create(date('Y-m-d H:i:s') . ' WIN CUSTOM ERROR BEFORE SAVE. BALANCE: ' . $b, 'testsg');
                    throw new Exception('asd2');
                }

                $checkWin = file_get_contents('last_wintransaction_id');

                if ($checkWin == $req_json['winID']) {
                    logfile::create(date('Y-m-d H:i:s') . ' WIN ALREADY EXITST. BALANCE: ' . $b, 'testsg');
                    throw new Exception('WIN ALREADY EXITST');
                }

                file_put_contents('last_wintransaction_id', $req_json['winID']);


                $b = $b + $req_json['amount'];

                file_put_contents('bbb', $b);


                if (0 && mt_rand(0, 2) == 0) {
                    logfile::create(date('Y-m-d H:i:s') . ' WIN CUSTOM ERROR AFTER SAVE. BALANCE: ' . $b, 'testsg');
                    throw new Exception('asd5');
                }

                logfile::create(date('Y-m-d H:i:s') . ' SUCCESS WIN. BALANCE: ' . $b, 'testsg');

                $ans = [
                    "status" => 1002,
                    "balance" => $b,
                    "userID" => $req_json['userID'],
                    "sessionID" => $req_json['sessionID'],
                    "roundID" => $req_json['roundID'],
                    "betID" => $req_json['betID'],
                    "winID" => $req_json['winID'],
                ];

                if (isset($req_json['freeroundID'])) {
                    $ans['freeroundID'] = $req_json['freeroundID'];
                }

                echo json_encode($ans);

                break;
            default:
                throw new Exception('unknown action');
        }
        exit;
    }

    public function action_testfs() {

        $api=new Api_BetConstruct();
        $api->setUpEnv($this->_test_office);

        $playerId=15271;
        $currency='USD';
        $fs_count=10;

        $now=time();

        $timeToStart=time()+Date::HOUR;
        $timeToEnd=time()+Date::HOUR;

        $p=new Parser();

        $params=[
            'playerId'=>$playerId,
            'operatorCode'=>'betconstruct',
            'currency'=>$currency,
            'externalReferenceId'=>guid::create(),
            'freeRoundValidity'=>date('d-m-Y H:i:s',$timeToStart),
            'bonusMoneyValidity'=>date('d-m-Y H:i:s',$timeToEnd),
            'numberOfFreeRounds'=>$fs_count,
            'gameIds'=>[
                'greenhot',
                'santa',
                'bigfive',
                'tesla',
//                'tothemoon',//запрещено сейчас по апи раздавать фс сюда
            ],
            'wagerRequirement'=>0,
            'automaticForfeitValue'=>0.0000,
        ];

        $data=[
            'time'=>date('d-m-Y H:i:s',$now),
            'data'=>$params
        ];

        $data['hash']=$api->sign('createFreespins',$data);

        $r=$p->post('http:'.URL::site('/apibc/createFreespins'), $data, true);

        echo($r);
    }

    public function action_createFreespins()
    {

        $body = file_get_contents('php://input');

        logfile::create(date('Y-m-d H:i:s') . 'createFreespins: '.($body), 'betconstruct');

        $req_json = json_decode($body, 1,512,JSON_PRESERVE_ZERO_FRACTION);

		$req_json = json_decode($body, 1);

        if(!$req_json) {
            throw new Exception('wrong json');
        }

        $api = new Api_BetConstruct();
        $api->setUpEnv($this->_test_office);


        $hash=$req_json['hash'];
        unset($req_json['hash']);

        if($api->sign('createFreespins',$req_json)!=$hash) {
            throw new Exception('wrong hash: '.$hash.'!='.$api->sign('createFreespins',$req_json));
        }

        $req_json=$req_json['data'];

        $f=new Model_BetconstructFreespin(['externalReferenceId'=>$req_json['externalReferenceId']]);


        if($f->loaded()) {
            throw new Exception('externalReferenceId ['.$req_json['externalReferenceId'].'] is not unique');
        }

        $f->playerId=$req_json['playerId'];
        $f->operatorCode=$req_json['operatorCode'];
        $f->currency=$req_json['currency'];
        $f->externalReferenceId=$req_json['externalReferenceId'];
        $f->freeRoundValidity=$req_json['freeRoundValidity'];
        $f->bonusMoneyValidity=$req_json['bonusMoneyValidity'];
        $f->numberOfFreeRounds=$req_json['numberOfFreeRounds'];
        $f->gameIds=$req_json['gameIds'];



//        $game_ids=array_keys(db::query(1,'select id from games where name in :names')
//            ->param(':names',$f->gameIds)
//            ->execute()
//            ->as_array('id'));

        $external_name='bc'.$f->currency.$f->operatorCode;

        $currency = new Model_Currency(['code' => $f->currency,'source'=>'agt']);

        if (!$currency->loaded() || $currency->disable != 0) {
            throw new Exception("currency problem $f->currency $f->operatorCode");
        }

        $o = new Model_Office([
            'currency_id' => $currency->id,
            'external_name' => $external_name,
            'is_test' => 0,
            'apitype'=>9
        ]);

        if(!$o->loaded()){
            throw new Exception("office not found: ".implode(',',[
                    'currency_id' => $currency->id,
                    'external_name' => $external_name,
                    'is_test' => 0,
                    'apitype'=>9
                ]));
        }

        $u=new Model_User(['api'=>9,'office_id'=>$o->id,'external_id'=>"".$f->playerId]);

        if($u->loaded()){
            $f->user_id=$u->id;
            $f->office_id=$u->office_id;
        }

        $f->save();


        echo json_encode([
            'result'=>true,
            'referenceId'=>guid::create()
        ]);

        /*$expire=strtotime($f->freeRoundValidity);

        $fs=new Model_Freespin();
        $fs->giveFreespins($u->id,$u->office_id,$game_ids,$f->numberOfFreeRounds,$calced['zzz'],$lines,$dentab_index,'api',false,null,$expire);*/


    }

}
