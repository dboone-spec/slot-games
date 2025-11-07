<?php defined('SYSPATH') or die('No direct script access.');

class Social_Mailru extends Social {

	protected $api_url = 'connect.mail.ru';

	public function auth_request()
	{
		return parent::auth_request() . '&response_type=code&host=http://'.$_SERVER['HTTP_HOST'];
	}

	public function get_profile_url()
	{
        $sign = md5("app_id=".$this->config['id']."method=users.getInfo"."secure=1"."session_key=".$this->access_token['access_token'].$this->config['secret']);
		return "http://www.appsmail.ru/platform/api?method=users.getInfo&app_id=".$this->config['id']."&session_key=" . $this->access_token['access_token']."&secure=1&sig=".$sign;
	}

	public function user_array($content)
	{
		$content = (array) json_decode($content,true);
		return array(
			'id' => $content[0]['uid'],
			'name' => $content[0]['last_name'].' '.$content[0]['first_name'],
			'email' => $content[0]['email'],
			);
	}
}