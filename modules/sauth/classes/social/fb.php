<?php

defined('SYSPATH') or die('No direct script access.');

class Social_Fb extends Social
{

    protected $api_url = 'graph.facebook.com';

    public function auth_request()
    {
        return parent::auth_request() . '&response_type=code';
    }

    public function callback_url()
    {
        return 'https://'.$_SERVER['HTTP_HOST'].URL::site('sauth/callback/'.$this->alias);
    }
    
    public function get_profile_url()
    {
        return "https://graph.facebook.com/me?access_token=" . $this->access_token['access_token'].'&metadata=1';
    }

//	public function access_token($res, $is_od = false )
//	{
//		$access_token = NULL;
//        $result = explode('&',$res);
//		$result0 = explode('=',$result[0]);
//		$result1 = explode('=',$result[1]);
//		if($result0[0]=='access_token') $access_token['access_token'] = $result0[1];
//		elseif($result1[0]=='access_token') $access_token['access_token'] = $result1[1];
//		if($result0[0]=='expires') $access_token['expires'] = $result0[1];
//		elseif($result1[0]=='expires') $access_token['expires'] = $result1[1];
//		return $access_token;
//	}
//	public function user_array($content)
//	{
//		return (array) json_decode($content);
//	}

    public function access_token($res,$is_od = false)
    {
        $access_token                 = NULL;
        $result                       = explode('{',$res);
        $result0                      = explode('}',$result[1]);
        $result1                      = explode(',',$result0[0]);
        $ac_tok                       = explode(':',$result1[0]);
        $ac_tok1                      = explode('"',$ac_tok[1]);
        $expires                      = explode(':',$result1[2]);
        $access_token['access_token'] = $ac_tok1[1];
        $access_token['expires']      = $expires[1];
        return $access_token;
    }

    public function user_array($content)
    {
        $a = (array) json_decode($content);
        $a['avatar']=$a['metadata']->connections->picture;
        return $a;
    }

}
