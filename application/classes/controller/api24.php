<?php

class Controller_Api24 extends Controller {

    protected $_api_url='';
    protected $_test_office=false;
    protected $_user_id="2976011";

    protected function _check_auth($login,$password) {
        if($login!='ematrix' || $password!='yoPCs9t!f~U8rzd') {
            return false;
        }
        //todo need check or create auth
        return true;
    }


    public function  action_LaunchGame(){
		return false;
        $api=new Api_Ematrix();

        $is_test_mode=(arr::get($_GET,'mode','dev')=='dev');

        $api->setMode($is_test_mode);
        $api->setURL($this->_api_url);

        $api->gameName=arr::get($_GET,'gameId');
        $lang=UTF8::strtolower(arr::get($_GET,'language','en'));  //optional

        $is_demo=(arr::get($_GET,'freePlay','false')=='true');
        $forceMobile=(arr::get($_GET,'mobile','false')=='true');

        $noClose=!$forceMobile;
        $noClose=false;

        $exit_url=arr::get($_GET,'lobbyUrl');

        $demobalance=2000;


        if($is_demo) {
            $demoUrl='https://demo.site-domain.com/games/agt/'.$api->gameName.'?demobalance='.($demobalance*100).'&everymatrix6=1';

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

        $api->session_token=arr::get($_GET,'token');
        $api->currency=UTF8::strtoupper(arr::get($_GET,'currencyCode'));

        $partner='internal';

        //from every matrix api docs v1.30, Note 2
        if($clientId = arr::get($_GET,'clientId')) {
            $partner.=' '.$clientId;
        }
        else {
            $try_parse_token=explode('_',$api->session_token);
            if(count($try_parse_token)==3) {
                $partner.=' '.$try_parse_token[1];
            }
        }

        $auth_data=$api->auth();

        if(!$auth_data) {
            throw new Exception_ApiResponse('Cant auth');
        }

        if($auth_data['Currency']!=$api->currency) {
            throw new Exception_ApiResponse('Currency is different! ['.$auth_data['Currency'].'!='.$api->currency.']');
        }

        $office_id = $api->checkOffice($auth_data['Currency'],$partner,(int) $is_test_mode);

        if(!$office_id) {
            $office_id = $api->createOffice($auth_data['Currency'],$partner,(int) $is_test_mode);
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
                $user_id = $api->checkUser($auth_data,$office_id); //update balance again
            }
        }
        $this->request->redirect($api->getGame($user_id,$lang,$forceMobile,$noClose,$exit_url));
    }

    /**
     * выдает информацию о ставке
     * @throws Exception
     */
    public function  action_betinfo() {

        if($this->request->method()!='POST') {
            throw new Exception('wrong method');
        }

        $req_json=json_decode(file_get_contents('php://input'),1);

        $bet_id=arr::get($req_json,'RoundId');
        $game_id=arr::get($req_json,'GameId');
        $user_id=arr::get($req_json,'UserId');

        if(empty($bet_id) || empty($game_id) || empty($user_id)) {
            throw new Exception('not enough parameters');
        }

        $sql='select * from bets where id=:bet_id';

        $bet_model=false;

        $find_bet=db::query(1,$sql)->param(':bet_id',$bet_id)->execute(null,'Model_Bet');

        if(!count($find_bet)) {

            $this->_error_response("Bet not found");
        }
        else {
            $bet_model=$find_bet->current();
        }

        if($bet_model) {
            if($bet_model->game!=$game_id) {
                $this->_error_response('wrong game');
            }
            if($bet_model->user->external_id!=$user_id) {
                $this->_error_response('wrong user');
            }

            $slotresult=new Vidget_Slotresult('result',$bet_model);
            $betcome=new Vidget_Betcome('come',$bet_model);


            $view=new View('block/betinfo');
            $view->bet_model=$bet_model;
            $view->currency=$bet_model->office->currency;
            $view->slotresult=$slotresult->_item($bet_model);
            $view->betcome=$betcome->_item($bet_model);

            $view_name = md5(implode('',[$bet_id,$user_id,$game_id])).'.html';

            $path = realpath(DOCROOT.'../'.'www') .DIRECTORY_SEPARATOR . "betviews";
            if( !is_dir($path)) {
                mkdir($path, 02777);
                chmod($path, 02777);
            }

            file_put_contents($path.DIRECTORY_SEPARATOR.$view_name,$view->render());

            echo 'https://site-domain.com/betviews/'.$view_name;
            exit;
        }

        $this->_error_response("Bet was moved to archive and not available");
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
            'currencyCode'=>array_combine(array_keys($currencies),array_keys($currencies)),
            'gameId'=>array_combine(array_keys($games),Arr::pluck($games,'visible_name')),
            'language'=>array_combine(array_keys(Kohana::$config->load('languages.lang')),array_keys(Kohana::$config->load('languages.lang'))),
            'freePlay'=>['true'=>'true','false'=>'false'],
            'mobile'=>['true'=>'true','false'=>'false'],
            'mode'=>['dev'=>'dev','prod'=>'prod'],
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
            echo '<a href="javascript:updBalance()">Update Balance</a> ';
            echo '<a href="javascript:stopAutoSpin()">Stop auto spin</a> ';
            echo '<a href="javascript:offMusic()">Off Music</a> ';
            echo '<a href="javascript:onMusic()">On Music</a> ';
            echo '<a href="javascript:showHelp()">Show Help</a> ';
            echo '<a href="javascript:togglePaytable()">Toggle Paytable</a> ';

            echo '<script>
            window.addEventListener("message",function(event) {
                console.log(event.data,"FROM VENDOR!");
            });
            </script>';

            echo '<iframe width="100%" height="100%" name="emwindow" src="/api24dev/LaunchGame?' . http_build_query($a) . '"></iframe>';
            echo '<script>
            function stopAutoSpin() {
                window.frames.emwindow.postMessage(
                    {
                        name:"stopAutospins",
                        sender: "emwindow",
                    }
                ,"*");
            }
            function showHelp() {
                window.frames.emwindow.postMessage(
                    {
                        name:"showHelp",
                        sender: "emwindow",
                    }
                ,"*");
            }
            function togglePaytable() {
                window.frames.emwindow.postMessage(
                    {
                        name:"togglePaytable",
                        sender: "emwindow",
                    }
                ,"*");
            }
            function offMusic() {
                window.frames.emwindow.postMessage(
                    {
                        name:"setAudio",
                        sender: "emwindow",
                        data: false
                    }
                ,"*");
            }
            function onMusic() {
                window.frames.emwindow.postMessage(
                    {
                        name:"setAudio",
                        sender: "emwindow",
                        data: true
                    }
                ,"*");
            }
            function updBalance() {
                window.frames.emwindow.postMessage(
                    {
                        name:"updateBalance",
                        sender: "emwindow",
                    }
                ,"*");
            }
</script>';
        }
        exit;
    }
    public function  action_testlaunch(){

        $pass='nicepass';

        $action=$this->request->param('id');

        $req_json=json_decode(file_get_contents('php://input'),1);

        if(isset($req_json['hash']) && !empty($req_json['hash'])) {
            if(md5(implode('',[
                $action,
                date('Y:m:d:H'),
                $pass
            ]))!=$req_json['hash']) {
                throw new Exception('bad hash');
            }
        }


        $curr='EUR';

        switch ($action){
            case 'Authenticate':

                $token=$req_json['LaunchToken'];
                $new_token=md5($token.microtime(1));

                $b = (float) file_get_contents('bbb');

                echo json_encode([
                     "Token" => $new_token,
                     "TotalBalance" => $b,
                     "Currency" => $curr,
                     "UserName" => "Andrew",
                     "UserId" => $this->_user_id,
                     "Country" => "China",
                     "Age" => "26",
                     "Sex" => "female",
                     "Status" => "Ok"
                ]);

                break;
            case 'GetBalance':

                $b = (float) file_get_contents('bbb');

                echo json_encode([
                    "TotalBalance" => $b,
                    "Currency" => $curr,
                    "Status" => "Ok"
                ]);

                break;
            case 'Bet':
                $req_json=json_decode(file_get_contents('php://input'),1);

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd3');
                }

                file_put_contents('last_transaction_id',$req_json['ExternalId']);

                $b = (float) file_get_contents('bbb');

                $b = $b-$req_json['Amount'];

                file_put_contents('bbb',$b);

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd4');
                }

                echo json_encode([
                    "TotalBalance" => $b,
                    "Currency" => $curr,
                    "Status" => "Ok"
                ]);

                break;
            case 'Cancel':
                $req_json=json_decode(file_get_contents('php://input'),1);

                $last_bet=file_get_contents('last_transaction_id');
                $last_win=file_get_contents('last_wintransaction_id');

                if(!in_array($req_json['CanceledExternalId'],[$last_bet,$last_win])) {
                    throw new Exception('bet not found ['.$req_json['CanceledExternalId'].']');
                }

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd1');
                }

                $b = (float) file_get_contents('bbb');

                $b = $b+123;

                file_put_contents('bbb',$b);

                echo json_encode([
                    "TotalBalance" => $b,
                    "Currency" => $curr,
                    "Status" => "Ok"
                ]);

                break;
            case 'Win':
                $req_json=json_decode(file_get_contents('php://input'),1);

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd2');
                }

                file_put_contents('last_wintransaction_id',$req_json['ExternalId']);

                $b = (float) file_get_contents('bbb');

                $b = $b+$req_json['Amount'];

                file_put_contents('bbb',$b);

                echo json_encode([
                    "TotalBalance" => $b,
                    "Currency" => $curr,
                    "Status" => "Ok"
                ]);

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd5');
                }

                break;
            default:
                throw new Exception('unknown action');
        }
        exit;
     }

     public function action_testbetinfo() {
        $id=957438998;
        $gameid='vangogh';
        $userid=3459617;

        $p=new Parser();
        $r=$p->post('https://api24dev.site-domain.com/api24dev/betinfo',[
            'RoundId'=>$id,
            'GameId'=>$gameid,
            'UserId'=>$userid,
        ],true);
        var_dump($r);
     }

    protected function _error_response($message,$code=null) {
         echo json_encode([
             "Success" => false,
             "Message" => $message,
             "ErrorCode" => $code
         ],JSON_FORCE_OBJECT);
         exit;
     }

    public function  action_AwardBonus() {

        $post_params=json_decode(file_get_contents('php://input'),1);

        $UserId = arr::get($post_params,'UserId');
        $BonusId = arr::get($post_params,'BonusId');
        $GameIds = arr::get($post_params,'GameIds',[]);
        $NumberOfFreeRounds = (int) arr::get($_POST,'NumberOfFreeRounds',0);
        $Currency = arr::get($post_params,'Currency','EUR');
        $CoinValue = arr::get($post_params,'CoinValue');
        $BetValueLevel = arr::get($post_params,'BetValueLevel');
        $LineCount = arr::get($post_params,'LineCount');
        $BetLevel = arr::get($post_params,'BetLevel');
        $BetValue = arr::get($post_params,'BetValue');
        $FreeRoundsEndDate = arr::get($post_params,'FreeRoundsEndDate');

        $expiry_datetime = new DateTime($FreeRoundsEndDate);

        $timezone=3;

        var_dump($expiry_datetime->getTimestamp()-$timezone*Date::HOUR);
        exit;

        if($NumberOfFreeRounds<=0) {
            $this->_error_response("Wrong number of rounds");
        }

        if(empty($GameIds)) {
            $this->_error_response("Empty game list");
        }

        $u = new Model_User(['external_id' => $UserId, 'api' => 6]);

        if(!$u->loaded()) {
            $this->_error_response("User not found");
        }

        $fs                = new Model_Freespin();
        $fs->fs_offer_id   = $BonusId;
        $fs->fs_offer_type = 'ematrixaward';
        $last_fs_id        = $fs->giveFreespins($u->id,$u->office_id,$calced['game_id'],$calced['cnt'],$calced['zzz'],$lines,$dentab_index,'api',false,null,$expire);

    }

}
