<?php defined('SYSPATH') or die('No direct script access.');

class Social_Google extends Social {

	public function auth_request()
	{
		return parent::auth_request() . '&response_type=code&scope=https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
	}
	public function get_profile_url()
	{
		return "https://www.googleapis.com/oauth2/v1/userinfo?access_token=".$this->access_token['access_token'].'&client_id='.$this->config['id'].
		  								'&redirect_uri='.urlencode($this->callback_url()).
							   			'&client_secret='.$this->config['secret'].'&grant_type=authorization_code'.'&code='.$this->code;
	}
	public function user_array($content)
	{
		return (array) json_decode($content);
	}

}