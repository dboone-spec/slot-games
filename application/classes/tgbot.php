<?php

//telegram bot

class tgbot {

	protected $_config = array();
	protected $_bot;
	protected $_log;


	public static function phoneExists($phone) {
		return ORM::factory('TelegramBot',array(
			'phone' => $phone,
            'offline' => (int) OFFLINE,
		))->loaded();
	}

	public static function getChatId($phone) {
		$o = ORM::factory('TelegramBot',array(
			'phone' => $phone,
            'offline' => (int) OFFLINE,
		));
		if($o->loaded()) {
			return $o->chat_id;
		}
		return 0;
	}

	public function __construct($botname=null) {
        if(is_null($botname)) {
            $this->_config = Kohana::$config->load('tgbot');
        }
        else {
            $this->_config = Kohana::$config->load('tgbot.'.$botname);
        }
        $this->_bot = $botname;
        $this->_log = new logfile();
	}

	public static function send($chat_id,$msg,$params=[]) {
        $short_theme = dd::get_short_theme(THEME);
        $msg_text = $short_theme . ' ' . $msg;

        if(!OFFLINE) {
            $tg = new self();
        }
        else {
            
        }

        if(!empty($params)) {
            $params['chat_id']=$chat_id;
            $params['text']=$msg_text;
            return $tg->apiRequestJson("sendMessage",$params);
        }

		return $tg->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $msg_text));
	}

	public function apiRequestWebhook($method, $parameters) {
		if (!is_string($method))
		{
			$this->_log->telegram = "Method name must be a string";
			return false;
		}

		if (!$parameters)
		{
			$parameters = array();
		} else if (!is_array($parameters))
		{
			$this->_log->telegram = "Parameters must be an array";
			return false;
		}

		$parameters["method"] = $method;

		$this->response->headers(array('Content-Type','application/json'))->body(json_encode($parameters));
		return true;
	}

	public function execCurlRequest($handle) {


		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($handle);

		if ($response === false)
		{
			$errno = curl_errno($handle);
			$error = curl_error($handle);
			$this->_log->telegram = "Curl returned error $errno: $error\n";
			curl_close($handle);
			return false;
		}

		$http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
		curl_close($handle);

		if ($http_code >= 500)
		{
// do not wat to DDOS server if something goes wrong
			sleep(10);
			return false;
		} else if ($http_code != 200)
		{
			$response = json_decode($response, true);
			$this->_log->telegram = "Request has failed with error {$response['error_code']}: {$response['description']}\n";
			if ($http_code == 401)
			{
				throw new Exception('Invalid access token provided');
			}
			return false;
		} else
		{
			$response = json_decode($response, true);
			if (isset($response['description']))
			{
				$this->_log->telegram = "Request was successfull: {$response['description']}\n";
			}
			$response = $response['result'];
		}

		return $response;
	}

	public function apiRequest($method, $parameters) {
		if (!is_string($method))
		{
			$this->_log->telegram = "Method name must be a string\n";
			return false;
		}

		if (!$parameters)
		{
			$parameters = array();
		} else if (!is_array($parameters))
		{
			$this->_log->telegram = "Parameters must be an array\n";
			return false;
		}

		foreach ($parameters as $key => &$val)
		{
// encoding to JSON array parameters, for example reply_markup
			if (!is_numeric($val) && !is_string($val))
			{
				$val = json_encode($val);
			}
		}
		$url = $this->_config['api_url'] . $method . '?' . http_build_query($parameters);
		$this->_log->telegram = "Request to telegram: {$url}";
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($handle, CURLOPT_TIMEOUT, 5);

		return $this->execCurlRequest($handle);
	}

	public function apiRequestJson($method, $parameters) {
		if (!is_string($method))
		{
			error_log("Method name must be a string\n");
			return false;
		}

		if (!$parameters)
		{
			$parameters = array();
		} else if (!is_array($parameters))
		{
			error_log("Parameters must be an array\n");
			return false;
		}

		$parameters["method"] = $method;
		$this->_log->telegram = "Request json to telegram: ".json_encode($parameters);
		$handle = curl_init($this->_config['api_url']);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($handle, CURLOPT_TIMEOUT, 60);
		curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
		curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

		return $this->execCurlRequest($handle);
	}

	public function getUpdates($offset_id=false) {
        $a=[];
        if($offset_id!==false) {
            $a['offset']=$offset_id;
        }
        $result = $this->apiRequest('getUpdates',$a);

        $phones = Kohana::$config->load('static.alertphones');

        if($result) {
            $update_id=0;
            foreach($result as $user) {
                if(isset($user['message'])) {
                    $this->processMessage($user['message']);
                    $update_id = $user['update_id'];
                }
                elseif(isset($user['callback_query'])) {
                    $json = json_decode($user['callback_query']['data'],1);
                    if($json['type']=='payment_confirm' AND $json['theme']==THEME) {
                        $p = new Model_Payment();
                        $p->confirm($json['ids']);
                        foreach($phones as $phone) {
                            th::tgsend($phone, $p->id.' confirmed');
                        }
                    }
                }
            }
            //clear messages
            $this->apiRequest('getUpdates',['offset'=>$update_id+1]);
        }

    }

	public function processMessage($message) {
		// process incoming message
		$chat_id = $message['chat']['id'];
		if (isset($message['text']))
		{
			// incoming text message
			$text = $message['text'];

			if (strpos($text, "/start") === 0)
			{
				$this->apiRequestJson("sendMessage",
					array(
						'chat_id' => $chat_id,
						"text" => 'Hello! Please, confirm your phone.',
						'reply_markup' => array(
							'keyboard' => array(array(
								array('text' => 'Confirm phone number',
									'request_contact' => true,
								))),
						'one_time_keyboard' => false,
						'resize_keyboard' => true)));
			} else
			{
//                $this->apiRequestJson("sendMessage",
//					array(
//						'chat_id' => $chat_id,
//						"text" => 'What do you want',
//						'reply_markup' => array(
//							'remove_keyboard',
//						'resize_keyboard' => true)));
//				$this->apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "text" => 'Please, confirm your phone.'));
			}
		} else
		{
			//TODO check if the answer is the correct phone

			if(isset($message['contact'])) {
				$phone = preg_replace("#[^0-9]*#is", "", $message['contact']['phone_number']);
				$chat_id = $message['contact']['user_id'];

				$tg = ORM::factory('TelegramBot',array('phone' => $phone,'offline'=>null));

				if(!$tg->loaded()) {
					$tg->phone = $phone;
				}
				$tg->chat_id = $chat_id;
				$tg->offline = null;
				$tg->save();

				/*$this->apiRequestJson("sendMessage",
					array(
						'chat_id' => $chat_id,
						"text" => 'Choose',
						'reply_markup' => array(
							'inline_keyboard' => array(
                                array(
                                    array(
                                        'text' => 'A ALERT',
                                        'callback_data' => '{td:a}',
                                    )
                                ),
                                array(
                                    array(
                                        'text' => 'N ALERT',
                                        'callback_data' => '{td:n}',
                                    )
                                ),
                                array(
                                    array(
                                        'text' => 'V ALERT',
                                        'callback_data' => '{td:v}',
                                    )
                                ),
                            ),
						)));*/

                //todo это правильный запрос. удаляет кнопку
//				$this->apiRequestJson("sendMessage",
//					array(
//						'chat_id' => $chat_id,
//						"text" => 'Your phone confirmed!',
//                        'reply_markup' => array(
//						'remove_keyboard' => true)));
			}
			else {
				$this->_log->telegram = "response: ".json_encode($message);
				$this->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Could not confirm phone.'));
			}
		}
	}

}
