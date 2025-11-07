<?php defined('SYSPATH') or die('No direct script access.');

class Social_Tw extends Social {

	protected $api_url = 'api.twitter.com';


	public function auth()
	{
		Kohana::load(Kohana::find_file('vendor', 'twitteroauth', 'php'));
		$conf = $this->config();
		$connection = new TwitterOAuth($conf['id'], $conf['secret']);

		$request_token = $connection->getRequestToken('https://'.$_SERVER['HTTP_HOST'].'/sauth/callback/tw?fingerprint='.$this->fingerprint);
		$token = Arr::get($request_token, 'oauth_token');

		$sess = Session::instance();
		$sess->set('oauth_token', $token);
		$sess->set('oauth_token_secret', Arr::get($request_token, 'oauth_token_secret'));

		switch ($connection->http_code) {
			case 200:
				/* Build authorize URL and redirect user to Twitter. */
				header('Location: '.$connection->getAuthorizeURL($token));
				exit();
				break;
			default:
				/* Show notification if something went wrong. */
				echo 'Could not connect to Twitter. Refresh the page or try again later.';
		}
		exit();
	}

	public function callback()
	{
		if(!Arr::get($_REQUEST, 'oauth_token')) {
			exit();
		}
		Kohana::load(Kohana::find_file('vendor', 'twitteroauth', 'php'));
		$sess = Session::instance();

		$conf = $this->config();

		if(Arr::get($_REQUEST, 'oauth_token') && $sess->get('oauth_token') !== Arr::get($_REQUEST, 'oauth_token')) {
			$sess->set('oauth_status', 'oldtoken');
			$sess->destroy();
			exit();
		}


		$connection = new TwitterOAuth($conf['id'], $conf['secret'], $sess->get('oauth_token'), $sess->get('oauth_token_secret'));

		$access_token = $connection->getAccessToken(Arr::get($_REQUEST, 'oauth_verifier'));

		$sess->set('access_token', $access_token);
		$sess->delete('oauth_token');
		$sess->delete('oauth_token_secret');

		if (200 == $connection->http_code) {
			if($content = $connection->get('account/verify_credentials')) {
				$sess->set('status', 'verified');

                $u = th::ObjectToArray($content);

				$user = new Model_Usersocial();
				$user = $user->where('api_name','=',(string) $content->id)->where('api','=',8)->find();

				if($user->loaded()) {
                    //$user->visible_name = $u['name'];
                    //$user->avatar=$u['avatar'];
                    //$user->save();
                    Auth::force_login_model($user, $this->fingerprint);
                    Request::initial()->redirect('/');
                } else {
                    $user->partner = intval(Cookie::get('partner')) ? Cookie::get('partner') : null;
                    $user->project = Cookie::get('project');
                    $user->msrc = Cookie::get('msrc');
                    $user->dsrc = isset($_SERVER['HTTP_HOST']) ? str_replace(['.'],'',$_SERVER['HTTP_HOST']) : null;
                    $user->visible_name = $u['name'];
        //			$user->email = $u['id'].'@'.$this->alias.'.nq';
                    $user->name = $this->alias.'*'.$u['id'];
                    $user->password = md5(rand(0,time()).$u['id']);
                    $user->api_name = (string) $u['id'];
                    $user->api = 8;
                    $user->save();
                    if(Auth::force_login_model($user, $this->fingerprint)){
                        Request::initial()->redirect('/');
                    }
                }
			}
		} else {
			$sess->set('oauth_status', 'oldtoken');
			$sess->destroy();
		}
		exit();
	}

	public function get_profile()
	{
		return false;
	}

	public function generate_user_sid( $user )
	{
		$user->sid = md5(uniqid().'trololo');
	}

	public function check_access_token()
	{
		return parent::check_access_token() && ($this->access_token['expires_in'] > time());
	}

	public function get_profile_url()
	{
		return false;
	}

	public function user_array($content)
	{
		return false;
	}
}