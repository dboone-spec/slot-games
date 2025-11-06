<?php

class Controller_ApiEvenbet extends Controller {

    protected $_api_url='';
    protected $_secretkey='';
    protected $_test_office=true;

    protected function _check_auth($login,$password) {
        if($login!='evenbet' || $password!='yoPC&js*28rzd') {
            return false;
        }
        //todo need check or create auth
        return true;
    }


    public function  action_opengame(){


		logfile::create('OPEN GAME REQUEST:'.PHP_EOL.json_encode($_GET).PHP_EOL,'evenbet');


        $api=new Api_Evenbet();

        $is_test_mode=$this->_test_office;

        $api->gameName=arr::get($_GET,'gameId');
        $lang=UTF8::strtolower(arr::get($_GET,'language','en'));

        $is_demo=(arr::get($_GET,'mode','0')=='1');
        $forceMobile=(arr::get($_GET,'platform','desktop')=='mobile');

        $currency=UTF8::strtoupper(arr::get($_GET,'currency',''));
        $token=arr::get($_GET,'token',false);
        $operator_id=arr::get($_GET,'operator_id','');

        $api->setUpEnv($this->_test_office);
        $api->forceURL(arr::get($_GET,'callback_URL'));

        $noClose=!$forceMobile;
        $noClose=true;

        $demobalance=2000;

        $exit_url='/black';

        if($is_demo) {
            $demoUrl='https://demo.kolinz.xyz/games/agt/'.$api->gameName.'?demobalance='.($demobalance*100);

            if($forceMobile) {
                $demoUrl.='&force_mobile=1';
            }
            if($noClose) {
                $demoUrl.='&no_close=1';
            }

            if($exit_url!==urldecode($exit_url)) {
                $exit_url=urlencode($exit_url);
            }

            $demoUrl.='&closeurl='.$exit_url;
            $demoUrl.='&lang='.$lang;

            $this->request->redirect($demoUrl);
        }

        if(!$token) {
            throw new Exception_ApiResponse('Empty token');
        }

        $api->session_token=$token;
        $api->currency=$currency;

        $auth_data=$api->login();

        if(!$auth_data) {
            throw new Exception_ApiResponse('Cant login');
        }

        if($auth_data['currency']!=$api->currency) {
            throw new Exception_ApiResponse('Currency is different! ['.$auth_data['currency'].'!='.$api->currency.']');
        }

        $office_id = $api->checkOffice($auth_data['currency'],$operator_id);

        if(!$office_id) {
            $office_id = $api->createOffice($auth_data['currency'],$operator_id);
        }
        else if(!$api->checkGame($office_id)) {
            throw new Exception("Game not found");
        }

        $user_id = $api->checkUser($auth_data,$office_id);

        if(!$user_id) {
            $user_id = $api->createUser($auth_data,$office_id);
        }
        else {
            $wasWrongBets = $api->processWrongBets($user_id);

            if($wasWrongBets) {
                $updated_balance=$api->checkBalance();

                if(!$updated_balance) {
                    throw new Exception('cant update balance');
                }

                $auth_data['balance']=$updated_balance;
                $user_id = $api->checkUser($auth_data,$office_id); //update balance again
            }
        }
        $this->request->redirect($api->getGame($user_id,$lang,$forceMobile,$noClose,$exit_url));
    }

    public function  action_frametest(){

        $token=arr::get($_GET,'token');

        if($this->request->method()=='POST') {
            $fs_params=[];
            $fs_params['UserId']=$this->_user_id;
            $fs_params['BonusId']=time();
            $fs_params['GameIds']=$_POST['game_ids'];
            $fs_params['NumberOfFreeRounds']=$_POST['fs_count'];
            $fs_params['Currency']='EUR';
            $fs_params['CoinValue']=$_POST['CoinValue'] ?? 0.1;
            $fs_params['BetValueLevel']=$_POST['BetValueLevel'] ?? 1;
            $fs_params['LineCount']=$_POST['LineCount'] ?? 0.1;
            $fs_params['BetLevel']=$_POST['BetLevel'] ?? 1;
            $fs_params['BetValue']=$_POST['BetValue'];
            $fs_params['FreeRoundsEndDate']=$_POST['FreeRoundsEndDate'];

            $p=new Parser();
            $r = $p->post('http://site-domain.local/api24/AwardBonus',$fs_params,true,[
                'Content-Type: application/json',
            ]);
            echo($r);
        }

        $currencies=db::query(1,'select code from currencies where source=\'agt\' order by 1')
                    ->execute()
                    ->as_array('code');


        $games = db::query(1,'select name,visible_name from games where show=1 and brand=\'agt\'')
            ->execute()
            ->as_array('name');


        $possible_params=[
            'currency'=>array_combine(array_keys($currencies),array_keys($currencies)),
            'gameId'=>array_combine(array_keys($games),Arr::pluck($games,'visible_name')),
            'language'=>array_combine(array_keys(Kohana::$config->load('languages.lang')),array_keys(Kohana::$config->load('languages.lang'))),
            'mode'=>['true'=>'true','false'=>'false'],
            'mobile'=>['true'=>'true','false'=>'false'],
        ];

        $form = Form::open(null,['method'=>'get']);
        $form.= Form::input('token',$token);
        foreach($possible_params as $param_key=>$possible_values) {
            $form.= Form::label($param_key,$param_key).' ';
            $form.= Form::select($param_key,$possible_values,Arr::get($_GET,$param_key));
        }
        $form.= Form::hidden('lobbyUrl','https://qooqle.com');
        $form.= Form::submit('go','go');
        $form.= Form::close();

        if(!empty($token)) {
            echo '<h2>User token: '.$token.'</h2>';
        }

        echo $form;


        $form_fs = Form::open(null,['method'=>'POST']);

        $form_fs.= Form::label('fs_count','fs count').' ';
        $form_fs.= Form::input('fs_count',mt_rand(5,15));
        $form_fs.= Form::label('BetValue','BetValue').' ';
        $form_fs.= Form::input('BetValue',mt_rand(1,100));

        $form_fs.='<br />';
        $form_fs.= '<label>stalker';
        $form_fs.= Form::checkbox('game_ids[]','stalker');
        $form_fs.= '</label>';
        $form_fs.='<br />';
        $form_fs.= '<label>tothemoon';
        $form_fs.= Form::checkbox('game_ids[]','tothemoon');
        $form_fs.= '</label>';
        $form_fs.='<br />';
        $form_fs.= '<label>vangogh';
        $form_fs.= Form::checkbox('game_ids[]','vangogh');
        $form_fs.= '</label>';
        $form_fs.='<br />';

        $form_fs.=Form::label('FreeRoundsEndDate','FreeRoundsEndDate');
        $form_fs.='<input type="datetime-local" name="FreeRoundsEndDate" />';

        $form_fs.= Form::submit('gofs','gofs');
        $form_fs.= Form::close();

        echo $form_fs;

        $a=[
            'currencyCode'=>'EUR',
            'gameId'=>'stalker',
            'language'=>'EN',
            'freePlay'=>'false',
            'mobile'=>'false',
            'mode'=>'dev',
            'token'=>'4228032_6_CETbMsalzku1m1ewb8vWGA',
            'lobbyUrl'=>'https://google.com',
        ];

        if(!empty($_GET)) {
            $a=$_GET;
        }
//        echo '<pre>';
//        echo http_build_query($a);
//        echo '</pre>';


        if(!empty($token)) {
            echo '<iframe width="100%" height="100%" name="evwindow" src="/apievenbet/opengame?' . http_build_query($a) . '"></iframe>';
        }
        exit;
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
