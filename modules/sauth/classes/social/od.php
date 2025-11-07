<?php defined('SYSPATH') or die('No direct script access.');

class Social_Od extends Social {

	public function auth_request()
	{
		return parent::auth_request().'&response_type=code';
	}
	public function get_profile_url()
	{
		$sign = md5("application_key=".$this->config['public']."format=jsonmethod=users.getCurrentUser".md5($this->access_token['access_token'].$this->config['secret']));
		return "http://api.odnoklassniki.ru/fb.do?access_token=".$this->access_token['access_token']."&method=users.getCurrentUser"."&application_key=".$this->config['public']."&format=json"."&sig=".$sign;;
	}
	public function user_array($content)
	{
		$content = (array) json_decode($content,true);
		return array(
			'id' => $content['uid'],
			'name' => $content['name'],
			);
	}

}