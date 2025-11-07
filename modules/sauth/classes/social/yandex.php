<?php defined('SYSPATH') or die('No direct script access.');

class Social_Yandex extends Social {

	public function auth_request()
	{
		return parent::auth_request() . '&response_type=code';
	}
	public function get_profile_url()
	{
		return "https://login.yandex.ru/info?oauth_token=".$this->access_token['access_token']."&format=json";
	}
	public function user_array($content)
	{
		$content = (array) json_decode($content,true);

        return array(
			'id' => $content['id'],
			'name' => $content['login'],
//			'email' => $content['emails'][0],
			);
	}

}