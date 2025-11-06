<?php

class Controller_Spam extends Controller {


    public function action_sms() {

        exit;

        $log=new logfile();

        foreach(file(DOCROOT.'spam.csv') as $b) {
            $c = explode(',',$b);
            $phone = $c[1];

            //autoreg
            $u=new Model_User();
            $u->name=$phone;
            $u->phone=$phone;
            $u->getspam=0;
            $pas=mt_rand(100000,999999);
            $u->salt=rand(1,10000000);
            $u->password=auth::pass($pas,$u->salt);
            $u->api=0;
            $u->dsrc='';
            $u->amount=0;
            $u->save()->reload();


            $msg = '/play/'.$u->id.' ';

            if($this->sms2send($phone,$msg)) {
                $log->smswelcome = $phone.' send'."\r\n";
            }
            else {
                $log->smswelcome = $phone.' not send'."\r\n";
            }
        }
    }

    public function sms2send($phone,$text){

        $curl = curl_init();
        $log=new logfile();
		$url = "http://my.smskanal.ru/get/send.php?";
		$param = array(
				"login" => Kohana::$config->load("secret.sms2_service_login"),
				"phone" => $phone,
				"text" => UTF8::clean($text),
				"timestamp" => file_get_contents('http://my.smskanal.ru/get/timestamp.php'),
				"sender" => Kohana::$config->load("secret.sms2_sender_id"),
		);

        $a=$param;
        ksort($a);
        reset($a);

        $param['signature']=md5(implode($a).Kohana::$config->load("secret.sms2_apikey"));

		$param = http_build_query($param);
		$url = $url . $param;

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, "SMS SENDER");

		$result = curl_exec($curl);
		curl_close($curl);
        $log->sms2="\r\n\r\n".th::date()." send  ".print_r($param,true)."Result: $result";
		if (strpos($result, "ERROR") !== false){
			return false;
		}

		return true;

	}

    public function action_promo1215() {

        //запускается один раз
        exit;

        $b = new Model_Bonus_Code();
        $b->name = 1215;
        $b->count=9999;
        $b->time = mktime(0,0,0,date('n'),16)-1;
        $b->bonus=2;
        $b->vager=50;
        $b->created=time();
        $b->type='unique_user';
        $b->min_sum_pay=750;
        $b->save();

        foreach(ORM::factory('user')->where('email','is not',null)->where('getspam','=','1')->find_all() as $user) {
            if(Valid::email($user->email)) {

                $message = View::factory('email/promo1215')->bind('user',$user)->render();

                $newsletter = new Model_Newsletter();
                $newsletter->to = $user->email;
                $newsletter->from = Email::from($user->dsrc);
                $newsletter->title = '200% бонусов с 12 по 15 октября!';
                $newsletter->message = $message;
                $newsletter->hash = md5(implode(':',[$user->email,Email::from(),$message]));
                $newsletter->need_to_send = mktime(8,0,0,date('n'),12); //отправляем в 11 утра
                $newsletter->sended = 0;
                $newsletter->save();
            }
        }
    }
}