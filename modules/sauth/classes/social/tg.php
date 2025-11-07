<?php

defined('SYSPATH') or die('No direct script access.');

class Social_Tg extends Social
{


    public function check_access_token()
    {
        $auth_data = Request::current()->query();

        $check_hash     = $auth_data['hash'];
        unset($auth_data['hash']);
        unset($auth_data['fingerprint']);
        unset($auth_data['kohana_uri']);
        $data_check_arr = [];
        foreach($auth_data as $key => $value)
        {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n",$data_check_arr);
        $secret_key        = hash('sha256',$this->config['bot_token'],true);
        $hash              = hash_hmac('sha256',$data_check_string,$secret_key);
        if(strcmp($hash,$check_hash) !== 0)
        {
            Request::initial()->redirect('/');
        }
        if((time() - $auth_data['auth_date']) > 86400)
        {
            Request::initial()->redirect('/');
        }

        return true;
    }

    public function get_profile()
    {
        $user_arr = $this->user_array(Request::current()->query());

		$uid = Arr::get($user_arr, 'id');
		$email = Arr::get($user_arr, 'email',null);
		if(!empty($uid)) {
			$user = array();
			$user['id'] = $uid;
			$user['name'] = Arr::get($user_arr, 'name');
			$user['avatar'] = Arr::get($user_arr, 'avatar');
			if(isset($email)) $user['email'] = $email;

			return $user;
		}

		return false;
    }

    public function user_array($content)
    {
        $a = [];
        $a['id'] = Arr::get($content,'id');
        $a['name'] = Arr::get($content,'username');
        $a['avatar'] = Arr::get($content,'photo_url');

        return $a;
    }

    public function get_profile_url()
    {
        return false;
    }

}
