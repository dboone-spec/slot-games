<?php defined('SYSPATH') or die('No direct script access.');

class Social_Vk extends Social {

	protected $api_url = 'oauth.vk.com';

	public function auth_request()
	{
		return parent::auth_request() . '&response_type=code';
	}

	public function check_access_token()
	{
		return parent::check_access_token() && ($this->access_token['expires_in'] > time());
	}

	public function get_profile_url()
	{
		return "https://api.vk.com/method/users.get?v=3.0uids=".$this->access_token['user_id']."&access_token=" . $this->access_token['access_token']."&fields=uid,first_name,last_name,nickname,sex,bdate,city,country,photo,photo_medium,photo_big,photo_rec";
	}

	public function user_array($content)
	{
		$content = Arr::get(Arr::get((array) json_decode($content,true), 'response'), 0);

		return array(
			'id' => $content['uid'],
			'name' => $content['first_name'].' '.$content['last_name'],
			'avatar' => $content['photo']
			);
	}

	public function access_token($res, $is_od = false )
	{
        $access_token = json_decode($res,true);
        $access_token['expires_in'] += time();
		return $access_token;
	}
}