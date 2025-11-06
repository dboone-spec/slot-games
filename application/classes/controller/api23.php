<?php

//test infin env
class Controller_Api23 extends Controller {

    protected $_api_url='https://vertbet.com/casinoapi/api/v1';
    protected $_test_office=false;
    protected $_partner='';
    protected $_force_token='XkF|HBVHnPWBRY].gZ^s3MtRdH[)bE';

    public function  action_launch(){

        $api=new Api_Vertbet();
        $api->setURL($this->_api_url);

        if(!empty($this->_force_token)) {
            $api->setForceToken($this->_force_token);
        }

        $api->session_token=arr::get($_GET,'session-token');
        $api->gameName=arr::get($_GET,'game-id');
        $api->currency=arr::get($_GET,'currency'); //optional

        if(!empty($api->currency)) {
            $api->currency = UTF8::strtoupper($api->currency);
        }

        $api->platform=arr::get($_GET,'platform'); //optional
        $api->user_id=arr::get($_GET,'user-id'); //optional
        //todo  //optional - как быть если нет user_id или currency
        $lang=arr::get($_GET,'lang','en');  //optional
        if(strpos($lang,'-')!==false) {
            $lang=explode('-',$lang)[0];
        }

        $forceMobile=($api->platform=='mobile');

        $noClose=true;
        $exit_url = 'https:'.URL::site('/black','https');

        $office_id = $api->checkOffice($api->currency,(int) $this->_test_office,$this->_partner);

        if(!$office_id) {
            $office_id = $api->createOffice($api->currency,(int) $this->_test_office,$this->_partner);
        }
        else if(!$api->checkGame($office_id)) {
            throw new Exception("Game not found");
        }


        $user_id = $api->checkUser($api->user_id,$office_id);

        if(!$user_id) {
            $user_id = $api->createUser($api->user_id,$office_id);
        }
        else {
            $wasWrongBets = $api->processWrongBets($user_id);

            if($wasWrongBets) {
                $user_id = $api->checkUser($api->user_id,$office_id); //update balance again
            }
        }

        $this->request->redirect($api->getGame($user_id,$lang,$forceMobile,$noClose,$exit_url));
    }

    public function  action_testlaunch(){


        $action=$this->request->param('id');

        $mult=pow(10,2);
        $curr='EUR';

        switch ($action){
            case 'user-info':
//                $req_json=json_decode($this->request->post(),1);

                $b = (float) file_get_contents('bbb');

                echo json_encode([
                    "user_id" => "1_USD",
                    "display_name" => "John Doe",
                    "balance" => $b,
                    "balance_multiplier" => $mult,
                    "currency" => $curr,
                    "country" => "GB",
                    "language" => "pt-BR"
                ]);

                break;
            case 'bet':
                $req_json=json_decode(file_get_contents('php://input'),1);

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd3');
                }

                file_put_contents('last_transaction_id',$req_json['transaction_id']);

                $b = (float) file_get_contents('bbb');

                $b = $b-$req_json['amount'];

                file_put_contents('bbb',$b);

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd4');
                }

                echo json_encode([
                    "provider_transaction_id" => $req_json['transaction_id'],
                    "transaction_id" => guid::create(),
                    "balance" => $b,
                    "balance_multiplier" => $mult,
                    "currency" => $curr,
                    "duplicated"=> true
                ]);

                break;
            case 'cancel-transaction':
                $req_json=json_decode(file_get_contents('php://input'),1);

                $last_bet=file_get_contents('last_transaction_id');
                $last_win=file_get_contents('last_wintransaction_id');

                if(!in_array($req_json['reference_transaction_id'],[$last_bet,$last_win])) {
                    throw new Exception('bet not found ['.$req_json['reference_transaction_id'].']');
                }

                if(mt_rand(0,4)==0) {
//                    throw new Exception('asd1');
                }

                $b = (float) file_get_contents('bbb');

                $b = $b+$req_json['amount'];

                file_put_contents('bbb',$b);

                echo json_encode([
                    "reference_transaction_id" => $req_json['reference_transaction_id'],
                    "transaction_id" => guid::create(),
                    "balance" => $b,
                    "balance_multiplier" => $mult,
                    "currency" => $curr,
                    "duplicated"=> true
                ]);

                break;
            case 'win':
                $req_json=json_decode(file_get_contents('php://input'),1);

                if(false && mt_rand(0,4)==0) {
//                    throw new Exception('asd2');
                }

                file_put_contents('last_wintransaction_id',$req_json['transaction_id']);

                $b = (float) file_get_contents('bbb');

                $b = $b+$req_json['amount'];

                file_put_contents('bbb',$b);

                echo json_encode([
                    "provider_transaction_id" => $req_json['transaction_id'],
                    "transaction_id" => guid::create(),
                    "balance" => $b,
                    "balance_multiplier" => $mult,
                    "currency" => $curr,
                    "duplicated"=> true
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

}
