<?php defined('SYSPATH') or die('No direct script access.');

abstract class Social_Base {

	public $alias;
	protected $api_url;
	protected $access_token;
	protected $config;
	public $code;
	public $fingerprint;


	public static function factory( $type, $fingerprint=null )
	{
		$social = 'Social_'.ucfirst($type);
		$social = new $social;
		$social->alias = $type;
		$social->fingerprint = $fingerprint;
		$social->config = $social->config();
		$social->access_token = $social->get_access_token();
		return $social;
	}

	public function auth()
	{
		if($this->check_access_token() == false) {
            $req = $this->auth_request();
            Request::initial()->redirect($req);
		}
		else {
			$this->complete();
		}
	}

	public function callback()
	{
        if($this->alias == 'mailru' or $this->alias == 'google' or $this->alias == 'od' or $this->alias == 'yandex'){
            $u = parse_url($this->access_token_url());
            $tp = explode('&',$u['query']);
            $p = array();
            foreach($tp as $v){
                $t = explode('=', $v);
                $p[$t[0]] = urldecode($t[1]);
            }
            if($this->alias == 'od') $res = $this->curl_post('http://'.$u['host'].$u['path'], $p);
			else $res = $this->curl_post('https://'.$u['host'].$u['path'], $p);
        }
		else{
			$res = @file_get_contents($this->access_token_url());
        }

        if ($res === false)
        {
            exit();
        }

        $access_token = $this->access_token($res,1);

        $this->set_access_token($access_token);
	}

    public function curl_post($url, array $post = NULL, array $options = array()) {
		$defaults = array(
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; (R1 1.5))",
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 4,
			CURLOPT_POSTFIELDS => http_build_query($post)
		);
		$ch = curl_init();
		curl_setopt_array($ch, ($options + $defaults));
		$result = curl_exec($ch);
		if( ! $result)
		{
			trigger_error(curl_error($ch));
		}
		curl_close($ch);
		return $result;
	}

	public function access_token($res, $is_od = false)
	{
        if(!$is_od){
            parse_str($res,$access_token);
			$access_token['expires'] += time();
        }
        else{
            $access_token = json_decode($res,true);
        }

		return $access_token;
	}

	public function auth_request()
	{
		if($this->alias == 'vk') $url = 'http://oauth.vk.com/authorize?v=3.0&client_id='.$this->config['id']."&redirect_uri=".urlencode($this->callback_url());

		if($this->alias == 'fb') $url = 'https://www.facebook.com/dialog/oauth?client_id='.$this->config['id'].'&redirect_uri='.urlencode($this->callback_url()).'&scope=email'.'&state='.$this->fingerprint;

		if($this->alias == 'mailru') $url = 'https://connect.mail.ru/oauth/authorize?client_id='.$this->config['id'].'&redirect_uri='.urlencode($this->callback_url());

		if($this->alias == 'google') $url = 'https://accounts.google.com/o/oauth2/auth?client_id='.$this->config['id'].'&redirect_uri='.urlencode($this->callback_url());

		if($this->alias == 'yandex') $url = 'https://oauth.yandex.ru/authorize?client_id='.$this->config['id'].'&state='.$this->fingerprint;

		if($this->alias == 'od') $url = 'http://www.odnoklassniki.ru/oauth/authorize?client_id='.$this->config['id']."&redirect_uri=".urlencode($this->callback_url());

		return $url;
	}

	public function get_profile()
	{
        $content = @file_get_contents($this->get_profile_url());

		$user_arr = $this->user_array($content);

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


	public function access_token_url()
	{
		$this->code = Arr::get($_GET, 'code');

		if($this->alias == 'fb') $url = 'https://graph.facebook.com/oauth/access_token?client_id='.$this->config['id'].
		  								'&redirect_uri='.urlencode($this->callback_url()).
							   			'&client_secret='.$this->config['secret'].'&code='.Arr::get($_GET, 'code');
		elseif($this->alias == 'vk') $url = 'https://oauth.vk.com/access_token?v=3.0&client_id='.$this->config['id'].
		  								'&redirect_uri='.urlencode($this->callback_url()).
							   			'&client_secret='.$this->config['secret'].'&code='.Arr::get($_GET, 'code');
		elseif($this->alias == 'od') $url = 'http://api.odnoklassniki.ru/oauth/token.do?grant_type=authorization_code&client_id='.$this->config['id'].
		  								'&redirect_uri='.urlencode($this->callback_url()).
							   			'&client_secret='.$this->config['secret'].'&code='.Arr::get($_GET, 'code');
		elseif($this->alias == 'mailru') $url = 'https://connect.mail.ru/oauth/token?client_id='.$this->config['id'].
		  								'&redirect_uri='.urlencode($this->callback_url()).
							   			'&client_secret='.$this->config['secret'].'&code='.Arr::get($_GET, 'code').'&grant_type=authorization_code';
		elseif($this->alias == 'google') $url = 'https://accounts.google.com/o/oauth2/token?client_id='.$this->config['id'].
		  								'&redirect_uri='.urlencode($this->callback_url()).
							   			'&client_secret='.$this->config['secret'].'&code='.Arr::get($_GET, 'code').'&grant_type=authorization_code';
		elseif($this->alias == 'yandex') $url = 'https://oauth.yandex.ru/token?client_id='.$this->config['id'].'&state='.$this->fingerprint.
		  								'&redirect_uri='.urlencode($this->callback_url()).
							   			'&client_secret='.$this->config['secret'].'&code='.Arr::get($_GET, 'code').'&grant_type=authorization_code';

		return $url;
	}

	public function callback_url()
	{
		return 'http://'.$_SERVER['HTTP_HOST'].URL::site('sauth/callback/'.$this->alias.'?fingerprint='.$this->fingerprint);
	}

	public function get_access_token()
	{
		return Session::instance()->get($this->alias.'_access_token');
	}

	public function set_access_token( $access_token )
	{
        Session::instance()->set($this->alias.'_access_token',$access_token);
	}

	/* ������� users ���� api */
	/* 2 - vk (vk.com) */
	/* 3 - fb (facebook.com) */
	/* 4 - od (odnoklassniki.ru) */
	/* 5 - mailru (mail.ru) */
	/* 6 - yandex (yandex.ru) */
	/* 7 - google (google.com) */
	/* 8 - tw (twitter.com) � ������ Social/Tw */
	public function complete()
	{
		$u = $this->get_profile();

		$user = new Model_Usersocial();
		switch ($this->alias) {
			case 'vk':
				$user = $user->where('api_name','=',(string) $u['id'])->where('api','=',2)->find();
				$alias_number = 2;
				break;
			case 'fb':
				$user = $user->where('api_name','=',(string) $u['id'])->where('api','=',3)->find();
				$alias_number = 3;
				break;
			case 'od':
				$user = $user->where('api_name','=',(string) $u['id'])->where('api','=',4)->find();
				$alias_number = 4;
				break;
			case 'mailru':
				$user = $user->where('api_name','=',(string) $u['id'])->where('api','=',5)->find();
				$alias_number = 5;
				break;
			case 'yandex':
				$user = $user->where('api_name','=',(string) $u['id'])->where('api','=',6)->find();
				$alias_number = 6;
				break;
			case 'google':
				$user = $user->where('api_name','=',(string) $u['id'])->where('api','=',7)->find();
				$alias_number = 7;
				break;
			case 'tg':
				$user = $user->where('api_name','=',(string) $u['id'])->where('api','=',9)->find();
				$alias_number = 9;
				break;
			default: exit();
		}
		if($user->loaded()) {
			$user->visible_name = $u['name'];
			$user->avatar=$u['avatar'];
			$user->save();
			Auth::force_login_model($user,$this->fingerprint);
			Request::initial()->redirect('/');
		} else {
            $user->partner = intval(Cookie::get('partner')) ? Cookie::get('partner') : null;
            $user->project = Cookie::get('project');
            $user->msrc = Cookie::get('msrc');
            $user->office_id = 1;
            $user->dsrc = isset($_SERVER['HTTP_HOST']) ? str_replace(['.'],'',$_SERVER['HTTP_HOST']) : null;
			$user->visible_name = $u['name'];
//			$user->email = $u['id'].'@'.$this->alias.'.nq';
			$user->name = $this->alias.'*'.$u['id'];
			$user->password = md5(rand(0,time()).$u['id']);
			$user->api_name = (string) $u['id'];
			$user->api = $alias_number;
			$user->avatar=$u['avatar'];
			$user->save();
			if(Auth::force_login_model($user,$this->fingerprint)){
				Request::initial()->redirect('/');
			}
		}


	}

	public function config()
	{

        switch($_SERVER['HTTP_HOST']){
            
            case 'casinovulkanline.com':
                $domain='casinovulkanline';
                break;
            case 'vulkanline.com':
                $domain='vulkanline';
                break;
            case 'vulkanline1.com':
                $domain='vulkanline';
                break;
            default :
                $domain='casinoikvulkan';
        }
		return Kohana::$config->load('sauth.'.$this->alias);
		return Kohana::$config->load('sauth.'.$domain.'.'.$this->alias); //domain relation
	}


	public function check_access_token()
	{
        if(!is_array($this->access_token) || count($this->access_token) == 0){
            return false;
        }

        if(empty($this->access_token['access_token'])){
            return false;
        }
        return true;
	}

	abstract function get_profile_url();

	abstract function user_array($content);
}