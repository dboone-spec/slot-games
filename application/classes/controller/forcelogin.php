<?php

class Controller_Forcelogin extends Controller_Base
{

    public function before()
    {

        if(!TERMINAL) {
            throw new HTTP_Exception_403;
        }

        parent::before();

        if($md5 = $this->request->action())
        {
            $user = new Model_User(['name'=>'frln*'.$md5]);

            $ip = $_SERVER['REMOTE_ADDR']??null;

            if(!$user->loaded()) {
                $user->msrc = Cookie::get('msrc');
                $user->name = 'frln*'.$md5;
                $user->password = md5(rand(0,time()).$md5);
                $user->api_name = (string) $md5;
                $user->api = 30;
                $user->chrome_ext_id = arr::get($_GET,'id');
                $user->blocked = -1;
                $user->bind_ip = $ip; //сразу привязываем айпи адрес
                $user->save()->reload();
                //TODO долбанная ORM
                $user->office_id = (int) arr::get($_GET,'office_id');
                $user->save();
            }
		
	$user->api_session_id = guid::create();
            $user->save();

            $wips = $user->office->white_ips;
            if(!is_array($wips)) {
                $wips=[$ip];
            }

            if(!$user->bind_ip || (int) $user->blocked!=0 || !$user->office_id || !in_array($ip,$wips)) {

		$v = View::factory('site/terminal/error');
                $_GET['terminal_id']=$user->id;
		Kohana::$log->add(Log::DEBUG,'ip: '.$ip.PHP_EOL.Debug::vars($user->as_array()));
                echo $v->render();
                exit;
                
                $this->request->redirect('/terminal/error?terminal_id='.$user->id);

                $this->request->redirect('/terminal/error?terminal_id='.$user->id);
            }
            Session::instance()->set('show_start_terminal',1);
            auth::force_login($user->name,null,true);
        }

        Auth::instance()->get_user();

        if ($this->need_auth and empty(auth::$user_id)){
                throw new HTTP_Exception_404;
        }

        $this->request->redirect('/',301);
    }

}
