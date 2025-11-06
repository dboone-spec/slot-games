<?php

    class Controller_Login extends Controller_Base
    {

        public $template='layout/login1';
        public $link = '/login';

        //for random player of 1049
        public function action_makedemo() {
            $uname = 'ud.'.time();
            $amount=1000;

            $uname = mt_rand(100000,999999);

            $u = new Model_User();
            $u->name = $uname;
            $u->amount=$amount;
            $u->office_id=1049;

            $salt = mt_rand(10000,99999);
            $pass = mt_rand(1000000,99999999);

            $u->salt = $salt;
            $u->password = auth::pass($pass,$salt);


            $u->save()->reload();

            echo 'Login: '.$uname.'<br>'.'Password: '.$pass;
            exit;
        }

        public function action_testauth() {
            $uname = 'ud.'.time();
            $amount=1000;

            $u = new Model_User();
            $u->name = $uname;
            $u->amount=$amount;

            $salt = mt_rand(10000,99999);
            $pass = mt_rand(1000000,99999999);

            $u->salt = $salt;
            $u->password = auth::pass($pass,$salt);

            $u->save()->reload();
            auth::force_login($u->name);
            $this->request->redirect('/');
        }

        public function action_index()
        {

            auth::logout();

            if(PROJECT==1) {
                $view = View::factory('/login/index1');
            }
            else {
                $view = View::factory('/login/index');
            }
            $view->link = $this->link;

            if ($_POST)
            {

                if (Auth::login(Arr::get($_POST, 'login'), Arr::get($_POST, 'password'), isset($_POST['remember'])))
                {
                    $this->request->redirect('/');
                }
                $view->bad_login = true;
            }
            $this->template->content = $view;
        }

        public function action_profile()
        {

            if (empty(auth::$user_id))
            {
                throw new HTTP_Exception_404;
            }

            if ($this->request->is_ajax())
            {
                $this->auto_render = false;

                if ($_POST)
                {
                    $ans = [];

                    $phone = th::clearphone(arr::get($_POST, 'phone'));
                    $phone_confirm_code = arr::get($_POST, 'phone_confirm_code');
                    $visible_name = arr::get($_POST, 'visible_name');
                    $profile_post = arr::get($_POST, 'profile');
                    $hairstyle = arr::get($_POST, 'hairstyle', 1);
                    $email = trim(arr::get($_POST, 'email'));

                    $user_email = new Model_User(['email' => $email]);

                    if ($user_email->loaded())
                    {
                        $ans['email'] = [
                            'error' => 1,
                            'text' => __('Невозможно использовать данный email'),
                        ];
                    }
                    elseif (!auth::parent_acc()->registr_with OR auth::parent_acc()->registr_with == 'email')
                    {
                        $ans['email'] = [
                            'error' => 1,
                            'text' => __('Невозможно изменить email'),
                        ];
                    }
                    elseif (!Valid::email($email))
                    {
                        $ans['email'] = [
                            'error' => 1,
                            'text' => __('Не верный email'),
                        ];
                    }
                    else
                    {
                        $ans['email'] = [
                            'error' => 0,
                            'text' => __('Email сохранен'),
                        ];
                        auth::parent_acc()->email = $email;
                    }

                    if (!auth::parent_acc()->phone_confirm AND $phone AND auth::parent_acc()->phone != $phone)
                    {
                        $user_with_phone = new Model_User([
                            'phone' => $phone,
                            'phone_confirm' => 1,
                        ]);

                        if (th::checkphone($phone))
                        {
                            $ans['phone'] = [
                                'error' => 1,
                                'text' => __('Неверный номер телефона '),
                            ];
                        }
                        elseif ($user_with_phone->loaded())
                        {
                            $ans['phone'] = [
                                'error' => 1,
                                'text' => __('Номер уже используется другим пользователем'),
                            ];
                        }
                        else
                        {
                            auth::parent_acc()->phone = $phone;
                            $ans['phone'] = [
                                'error' => 0,
                                'text' => __('Номер телефона сохранен'),
                            ];
                        }
                    }

                    if ($phone_confirm_code AND ! auth::parent_acc()->phone_confirm)
                    {
                        if (auth::parent_acc()->phone_code == $phone_confirm_code)
                        {
                            auth::parent_acc()->phone_confirm = 1;
                            $ans['phone_confirm_code'] = [
                                'error' => 0,
                                'text' => __('Номер телефона подтвержден'),
                                'data' => auth::parent_acc()->phone,
                            ];
                        }
                        else
                        {
                            $ans['phone_confirm_code'] = [
                                'error' => 1,
                                'text' => __('Неверный код подтверждения'),
                            ];
                        }
                    }

                    if (!auth::parent_acc()->visible_name AND $visible_name)
                    {
                        auth::parent_acc()->visible_name = $visible_name;
                        $ans['visible_name'] = [
                            'error' => 0,
                            'text' => __('Никнейм сохранен'),
                        ];
                    }

                    $profile = new Model_User_Profile(['user_id' => auth::user()->parent_id]);
                    if (!$profile->loaded())
                    {
                        $profile->user_id = auth::user()->parent_id;
                    }

                    if ($hairstyle)
                    {
                        $profile->hairstyle = $hairstyle;
                    }

                    if (is_null($profile->gender) AND in_array(arr::get($profile_post, 'gender'), ['m', 'f']))
                    {
                        $profile->gender = ($profile_post['gender'] == 'm') ? 1 : 0;
                    }
                    if (!$profile->birthday)
                    {
                        if (empty(arr::get($profile_post, 'birthday_new', [])))
                        {
                            $birthday_data = arr::get($profile_post, 'birthday', []);
                        }
                        else
                        {
                            $bd_data = arr::get($profile_post, 'birthday_new', []);
                            $pieces = explode('-', $bd_data);
                            $birthday_data['year'] = $pieces[0];
                            $birthday_data['month'] = $pieces[1];
                            $birthday_data['day'] = $pieces[2];
                        }
                        if ($day = arr::get($birthday_data, 'day') AND $month = arr::get($birthday_data, 'month') AND $year = arr::get($birthday_data, 'year'))
                        {
                            $profile->birthday = mktime(0, 0, 0, $month, $day, $year);
                        }
                    }

                    foreach (['first_name', 'last_name', 'middle_name'] as $param)
                    {
                        if (!$profile->$param AND $param_value = arr::get($profile_post, $param))
                        {
                            $profile->$param = $param_value;
                        }
                    }

                    auth::parent_acc()->save();
                    $profile->save();

                    $this->response->body(json_encode($ans));
                    return;
                }

                $view = new View('site/popup/profile');

                $this->response->body($view->render());
            }
            else
            {
                $this->request->redirect('/');
            }
        }

        public function action_bonuses()
        {
            $this->auto_render = false;

            if (th::isMobile())
            {
                $view = new View('profile/mobile/bonuses');
            }
            else
            {
                $view = new View('profile/bonuses');
            }
            $bonuses = ORM::factory('Bonus_Code')->where('show', '=', 1)->and_where('office_id', '=', auth::user()->office_id)->find_all();
            $games = th::gamelist();

            $games_fs = [];

            if (auth::user()->check_reg_fs())
            {
                $reg_fs_games = kohana::$config->load('static.reg_fs_games');
                $games_fs = orm::factory('game')->where('name', 'in', $reg_fs_games)->find_all();
            }

            $sql_referals = <<<SQL
            Select coalesce(count(*),0) as count
            From users
            Where invited_by = :user_id
                AND parent_id is null
SQL;

            $referals = db::query(1, $sql_referals)->param(':user_id', auth::parent_acc()->id)->execute()->as_array();

            $view->count_referals = $referals[0]['count'];
            $view->games = $games;
            $view->games_fs = $games_fs;
            $view->bonuses = $bonuses;

            $this->response->body($view->render());
        }

        public function action_loyality()
        {
            $this->auto_render = false;

            $view = new View('profile/loyality');
            $view->comp_config = model_user::get_compoint_config();
            $this->response->body($view->render());
        }

        
        
        public function loginTG($user){
            
            $ans = ['refresh' => 0, 'error' => '', 'id' => 'login','needcode'=>false];
            if ($user->blocked)
                {
                    $ans['error'] = __('Ваш аккаунт заблокирован. Для разблокировки обратитесь в техподдержку.');
                    return ($ans);
                }
            
            if (!th::officeIsEnable($user->office_id)){
                $ans['error'] = __('Ваш аккаунт заблокирован. Для разблокировки обратитесь в техподдержку.');
                return ($ans);
            }
                
            if (Auth::pass(arr::get($_POST,'password'), $user->salt)!=$user->password){
                $ans['error']= __('User does not exist or wrong password');
                return ($ans);
            }
            
            if  (empty(arr::get($_POST,'code'))){
                $ans['error'] = __('Enter the code.');
                $ans['needcode'] = true;
                $user->wcode=rand(1000,9999);
                $user->save();
                $this->send($user->tg_id,$user->wcode);
                return ($ans);
            }
            
            if (arr::get($_POST,'code')!=$user->wcode){
                $ans['error'] = __('Wrong code.');
                return ($ans);
            }
            
            
            $fingerprint = intval(arr::get($_POST, 'fingerprint'));
            if  (Auth::login($user->name, Arr::get($_POST, 'password'), 0, $fingerprint)){
                Cookie::delete('bad_login_user');
                Cookie::delete('bad_captcha');
                $ans['refresh'] = 1;
                cookie::delete('regfs');
                return ($ans);
            }
            
            $ans['error'] = __('Internal error try again');
            return ($ans);
            

            
            
            
        }
        
public function action_secreta() {
            //$this->send(371527172,'hkaskjdjas');
        }

 public function send($chatid,$message){
        
        $token='2136283114:AAFMCKqpgoie8-t3FkqE8ZoCGj7OXi51iWg';
	$token='5780743937:AAF_cX8WjYG6b4UYV1ASqz66kSZQFZ5bqxE';
        
                
        $message= urlencode($message);
        
        
        $url="https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatid}&text={$message}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result= json_decode($result);
        
        if(!isset($result->ok)){
            return false;
        }
        
        if ($result->ok===true){
            return true;
        }

        return false;
        
    }
    
    
        public function action_login()
        {

            $this->auto_render = false;

            if ($this->request->method() == 'POST')
            {
                $ans = ['refresh' => 0, 'error' =>'', 'id' => 'login','needcode'=>false];
                $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : null;
                $user = new Model_User();
                $user->where('name','=',Arr::get($_POST, 'login'))
                        ->or_where('email', '=', Arr::get($_POST, 'login'))
                        ->find();
                
                
                if ($user->office->tg_cashusers>0){
                    
                    $ans=$this->loginTG($user);
                    $this->response->body(json_encode($ans));
                    return null;
                }




                $fingerprint = intval(arr::get($_POST, 'fingerprint'));

                if ($user->blocked)
                {
                    $ans['error'] = __('Ваш аккаунт заблокирован. Для разблокировки обратитесь в техподдержку.');
                }
                elseif (th::officeIsEnable($user->office_id) and  Auth::login($user->name, Arr::get($_POST, 'password'), isset($_POST['remember']), $fingerprint))
                {
                    if (isset($captcha) AND ! Captcha::valid($captcha))
                    {
                        auth::logout();
                        $ans['error'] = __('Неправильно заполнена капча');
                        $ans['captcha'] = Captcha::instance()->render();
                        if (false && $this->checkBlocking())
                        {
                            $ans['error'] = __('Ваш аккаунт заблокирован. Для разблокировки обратитесь в техподдержку.');
                        }
                    }
                    else
                    {
                        Cookie::delete('bad_login_user');
                        Cookie::delete('bad_captcha');
                        $ans['refresh'] = 1;
                        cookie::delete('regfs');
                    }
                }
                else
                {
                    $ans['error'] = __('Введен неверный логин или пароль');

                    if (!Cookie::get('bad_login_user'))
                    {
                        Cookie::set('bad_login_user', 1);
                    }
                    else
                    {
                        $bad_login_count = Cookie::get('bad_login_user') + 1;
                        Cookie::set('bad_login_user', $bad_login_count);
                        if ($bad_login_count >= 3)
                        {
                            $ans['captcha'] = Captcha::instance()->render();
                        }
                    }
                }

                if($this->request->is_ajax()) {
                    $this->response->body(json_encode($ans));
                }
                else {
                    $this->request->redirect('/');
                }
            }
        }

        private function checkBlocking()
        {
            $blocked = 0;
            $user = new Model_User(["name" => Arr::get($_POST, 'login')]);

            if ($user->blocked)
            {
                return 1;
            }

            if (!Cookie::get('bad_captcha'))
            {
                Cookie::set('bad_captcha', 1);
            }
            else
            {
                $bad_captcha_count = Cookie::get('bad_captcha') + 1;
                if ($user->loaded() AND $bad_captcha_count >= 3)
                {
                    $user->blocked = 1;
                    $user->save();
                    $blocked = 1;
                    Cookie::delete('bad_captcha');
                }
                else
                {
                    Cookie::set('bad_captcha', $bad_captcha_count);
                }
            }
            return $blocked;
        }

        public function action_logout()
        {
            $this->auto_render = false;
            Auth::logout();
	
			if(defined('TELEGRAM') && TELEGRAM) {
                $this->link='/black';
            }

	        if(defined('SBC_DOMAIN') && SBC_DOMAIN) {
                $this->request->redirect('/');
            }

            $this->request->redirect($this->link);
        }

        public function action_rfid() {
            $code = $this->request->param('id');
            if($code) {
                $u=new Model_User(['rfid'=>$code]);
                if($u->loaded()) {
                    auth::force_login_model($u);
                    echo 'ok';
                    exit;
                }
            }
            throw new HTTP_Exception_404;
        }

        public function action_signin()
        {

            throw new HTTP_Exception_404;

            $u = new Model_User();
            $errors = array();

            if ($this->request->method() == 'POST')
            {


                $u->name = arr::get($_POST, 'name');
                $u->visible_name = $u->name;
                $u->email = arr::get($_POST, 'email');
                $pas = arr::get($_POST, 'password');
                $u->password = $pas;
                $pas1 = arr::get($_POST, 'password_confirm');


                $errors = array();
                $v = $u->validation();
                $v->check();
                $errors = $v->errors('userreg');


                if ($pas1 != $pas)
                {
                    $errors[] = __('Пароли не совпадают');
                }

                if (count($errors) == 0)
                {
                    $u->salt = rand(1, 10000000);
                    $u->password = auth::pass($pas, $u->salt);
                    $u->api = 0;
                    $u->save();
//                    $message = new View('login/mailreg');
                    //Email::send($u->email, 'robot@nevesta', 'Регистрация nevesta', $message->render(), true);
                    auth::force_login($u->name);
                    $this->request->redirect('/');
                }
            }

            $view = new View('login/signin');
            $view->link = $this->link;
            $view->u = $u;
            $view->errors = $errors;
            $this->template->content = $view;
        }

        public function action_nospam()
        {
            $view = new View('login/nospam');
            if ($mail = Arr::get($_GET, 'mail'))
            {
                $n = new logfile();
                $n->nospam = $mail . ' [' . THEME . ']';
                $u = new Model_User(array('email' => $mail));
                if ($u->loaded())
                {
                    $u->getspam = 0;
                    $u->save();
                }
            }
            $this->template->content = $view;
        }

        public function action_signinajax()
        {

            $this->auto_render = false;

            $u = new Model_User();
            $errors = array();


            if ($this->request->method() == 'POST')
            {

                $ans = ['refresh' => 0, 'errors' => [], 'id' => 'register'];
                $u->name = UTF8::strtolower(arr::get($_POST, 'email'));
                $u->email = UTF8::strtolower(arr::get($_POST, 'email'));
                $u->visible_name = UTF8::strtolower(arr::get($_POST, 'visible_name'));
                $email_repeat = UTF8::strtolower(arr::get($_POST, 'email_repeat'));


                //new reg
                $u->name = $u->email;
                $u->visible_name = null;
                $u->comment = UTF8::strtolower(arr::get($_POST, 'comment'));
                $email_repeat = $u->email;



                $u->getspam = isset($_POST['get_info']) ? 1 : 0;
                $pas = arr::get($_POST, 'password');
                $pas1 = arr::get($_POST, 'password_confirm');
                $phone = arr::get($_POST, 'phone', -1);
                $office_id = intval(arr::get($_POST, 'office_id', OFFICE));
                $fingerprint = intval(arr::get($_POST, 'fingerprint', 0));

                $havebonus = isset($_POST['havebonus']);

                $errors = [];

                $office = new Model_Office($office_id);
                if ($office->loaded())
                {
                    $u->office_id = $office_id;
                }
                else
                {
                    $errors['office'] = __('Ошибка при сохранении данных');
                }

                if (!in_array($phone, [-1, '']))
                {
                    $u->phone = th::clearphone($phone);
                    $phone_user = new Model_User(['phone' => $u->phone, 'phone_confirm' => 1]);

                    if (th::checkphone($u->phone))
                    {
                        $errors['phone'] = __('Неверный номер телефона');
                    }
                    elseif ($phone_user->loaded())
                    {
                        $errors['phone'] = __('Невозможно использовать данный номер телефона');
                    }
                }

                if (!valid::email($u->email))
                {
                    $errors['email'] = __('Неверный email');
                }

                $u1 = new Model_User(['email' => $u->email]);
                if ($u1->loaded())
                {
                    $errors['email'] = __('Такой email уже зарегистрирован');
                }

                if ($u->email != $email_repeat)
                {
                    $errors['email'] = __('Введенные Email адреса не совпадают');
                    $errors['email_repeat'] = __('Введенные Email адреса не совпадают');
                }

                if (false && !isset($_POST['rules']))
                {
                    $errors['rules'] = __('Для регистрации необходимо согласиться с правилами');
                }

//                      подтверждение пароля
//			if ($pas1!=$pas){
//				$errors['password_confirm']=__('Пароли не совпадают');
//			}

                if (strlen($pas) < 7)
                {
                    $errors['password'] = __('Пароль должен быть не меньше 7 символов');
                }


                if (count($errors) == 0)
                {
                    try
                    {
                        $u->salt = rand(1, 10000000);
                        $u->password = auth::pass($pas, $u->salt);
                        $u->api = 0;
                        $u->amount = 0;
                        if(OFFICE==444 && PROJECT==2) {
                            $u->amount = 1000;
                        }
                        $u->save()->reload();

//                        $currency_account = auth::create_office_account($u);
//
//                        if ($havebonus)
//                        {
//                            $bonuslink = Session::instance()->get('bonuslink');
//                            if ($bonuslink)
//                            {
//                                $b = new Model_Bonus_Link($bonuslink);
//                                if ($b->loaded() && $b->use == 0)
//                                {
//                                    $currency_account->amount = $b->amount;
//                                    $currency_account->last_drop = $b->amount;
//                                    $currency_account->amount1 = $b->amount;
//                                    $currency_account->save();
//
//                                    $b->use = 1;
//                                    $b->save();
//                                }
//                            }
//                            else
//                            {
//                                $m = new Model_Bonus_Register(th::packIP());
//                                if (!$m->loaded())
//                                {
//                                    $currency_account->bonus = 1000;
//                                    $currency_account->amount = 1000;
//                                    $currency_account->bonusspinall = 1000 * 90;
//                                    $currency_account->save();
//
//                                    $m->id = th::packIP();
//                                    $m->ip = $_SERVER['REMOTE_ADDR'];
//                                    $m->amount = $b->amount;
//                                    $m->save();
//                                }
//                            }
//                        }
//
//                        $referal_link = Session::instance()->get('referallink');
//
//                        if ($referal_link)
//                        {
//                            $referer = new Model_User(['referal_link' => $referal_link]);
//                            /*
//                             * пишем id-шник пригласившего
//                             */
//                            $u->invited_by = $referer->parent_id ?? $referer->id;
//                            $u->save();
//
//                            $currency_account->invited_by = $u->invited_by;
//                            $currency_account->save();
//
//                            $referal = new Model_Bonus_Referal();
//                            $referal->user_id = $referer->id;
//                            $referal->referal_id = $u->id;
//                            $referal->ip = $_SERVER['REMOTE_ADDR'];
//                            $referal->save();
//                        }
                    }
                    catch (Exception $e)
                    {
                        $errors['critical'] = $e->getMessage();
                    }

//                    Session::instance()->delete('bonuslink');
//
//                    $message = new View('login/mailreg');
//                    $message->u = $u;
//                    $message->pas = $pas;
//                    //генерим код для подтверждения почты
//                    $u->generate_email_code();
//                    Email::stack($u->email, Email::from($u->dsrc), __('Регистрация в ' . UTF8::strtolower(isset($_SERVER['HTTP_HOST']) ? UTF8::ucfirst($_SERVER['HTTP_HOST']) : '')), $message->render(), true, $u->dsrc, 1);
                    /*
                     * добавляем сообщение о регистрации
                     */
//                    $theme = THEME == false ? 'default' : THEME;
//                    $register_messages = kohana::$config->load('messages.' . $theme . '.register');
//
//                    foreach ($register_messages as $message)
//                    {
//                        $u->new_message([
//                            "user_id" => $u->id,
//                            "title" => $message['title'],
//                            "text" => $message['text'],
//                        ]);
//                    }
                    auth::force_login($u->name, $fingerprint);
                    $ans['refresh'] = 1;
                }

                $ans['errors'] = $errors;
                $this->response->body(json_encode($ans));
            }
        }

        public function action_forget()
        {
            throw new HTTP_Exception_404;

            $view = new View('login/forget');
            $view->link = $this->link;
            if ($this->request->method() == 'POST')
            {

                if (isset($_POST['button_login']))
                {
                    $name = arr::get($_POST, 'name');
                    $u = new Model_User(array('name' => $name));
                }
                else
                {
                    $email = arr::get($_POST, 'email');
                    $u = new Model_User(array('email' => $email));
                }



                if (!$u->loaded())
                {
                    $view->bad = 1;
                }
                else
                {
                    $u->code = md5(rand(1, PHP_INT_MAX));
                    $u->save();
                    $message = new View('login/mailforget');
                    $message->user = $u;
                    Email::stack($u->email, Email::from($u->dsrc), __('Восстановление пароля'), $message->render(), true, $u->dsrc, 1);
                    $this->request->redirect('/login/passcode');
                }
            }

            $this->template->content = $view;
        }

        public function action_remindajax()
        {

            $this->auto_render = false;

            $ans = [];

            if ($this->request->method() == 'POST')
            {

                $remind = arr::get($_POST, 'remind');
                $u = new Model_User(array('name' => $remind));

                //TODO добавить проверку на факт отправки
                if ($u->loaded())
                {

                    if (!Valid::email($remind) && !th::checkphone($u->phone))
                    {
                        //phone message
                        $u->code = mt_rand(100000, 999999);
                        $u->save()->reload();
                        th::smssend($u->phone, 'Code: ' . $u->code);
                        $ans = ['refresh' => 0, 'redirect' => '/login/phonecode?remind=' . $remind,];
                    }
                    else
                    {
                        $u->code = md5(rand(1, PHP_INT_MAX));
                        $u->save()->reload();
                        $message = new View('login/mailforget');
                        $message->user = $u;
                        Email::stack($u->email, Email::from($u->dsrc), __('Восстановление пароля'), $message->render(), true, $u->dsrc, 1);
                        $ans = ['refresh' => 0, 'errors' => ['remind' => __('На почту, указанную при регистрации, отправлены инструкции для восстановления пароля')], 'id' => 'remind'];
                    }
                }
                else
                {
                    $ans = ['refresh' => 0, 'errors' => ['remind' => __('Проверьте введенный адрес электронной почты')], 'id' => 'remind'];
                }
            }

            $this->response->body(json_encode($ans));
        }

        public function action_phonecode()
        {
            $remind = Arr::get($_GET, 'remind') ? $_GET['remind'] : Arr::get($_POST, 'remind');
            $answer = [
                'enter_code' => 1,
                'success' => 1,
                'remind' => $remind,
                'text' => null
            ];

            $view = new View('login/phonecode');
            $view->enter_code = 0;

            if ($this->request->post())
            {
                $view->enter_code = 1;

                $code = Arr::get($_POST, 'code', -1);

                $u = new Model_User(array('name' => $remind));

                if ($u->loaded() AND $code == $u->code)
                {
                    $u->phone_confirm = 1;

                    //генерим новый пароль
                    $password = rand(1111111, 9999999);
                    $u->salt = rand(1, 10000000);
                    $u->password = auth::pass($password, $u->salt);
                    $u->code = null;
                    $u->save()->reload();

                    th::smssend($u->phone, 'Password: ' . $password);
                }
                else
                {
                    $answer['success'] = 0;
                    $answer['text'] = __('Неверный код подтверждения, введите код повторно');
                }
            }

            $view->answer = $answer;
            $this->template->content = $view;
        }

        public function action_passcode()
        {
            $view = new View('login/passcode');
            $view->link = $this->link;
            $code = arr::get($_GET, 'code');
            if (!empty($code))
            {
                $u = new Model_User(array('code' => $code));

                if ($u->loaded())
                {
                    $pas = rand(100000, 1000000);
                    $u->code = null;
                    $u->salt = rand(1, 10000000);
                    $u->password = auth::pass($pas, $u->salt);
                    $u->save();

                    $message = new View('login/mailpass');
                    $message->user = $u;
                    $message->pas = $pas;
                    Email::stack($u->email, Email::from($u->dsrc), __('Новый пароль '), $message->render(), true, $u->dsrc, 1);
                    $view->good_code = 1;

                    $this->response->headers('Refresh:', '5; url=/?popup=login');
                }
                else
                {
                    $view->bad_code = 1;
                }
            }


            $this->template->content = $view;
        }

        public function action_emailconfirm()
        {
            $view = new View('login/mailconfirmed');
            $view->status = 'error';
            $code = arr::get($_GET, 'code', 'default');

            if ($code == auth::parent_acc()->code)
            {
                auth::parent_acc()->email_confirm = 1;
                auth::parent_acc()->save();

                $view->email = auth::parent_acc()->email;
                $view->status = 'success';
            }

            $this->template->content = $view;
        }

        public function action_pushmessages()
        {
            $this->auto_render = false;

            $endpoint = arr::get($_POST, 'url');

            $message = [
                'newMessage' => 0,
                'title' => 'Заголовок',
                'message' => 'Текст сообщения',
                'icon' => '/assets/img/pushmessage.png',
                'notification' => [
                    'data' => '/'
                ],
            ];

            $subscriber_token = th::get_subscriber_for_push($endpoint);
            $message_model = new Model_User_Message(['push_token' => $subscriber_token]);

            if ($message_model->loaded())
            {
                $sql = <<<SQL
                Select id, title, text, push_link, push_token
                From user_messages
                Where push_token = :push_token
                    AND push = :push
                    AND sended = :sended
                    AND show = :show
                ORDER BY created desc
                LIMIT 1
SQL;
                $res_messages = db::query(1, $sql)->parameters([
                            ':push_token' => $subscriber_token,
                            ':push' => 1,
                            ':sended' => 1,
                            ':show' => 1,
                        ])->execute()->as_array();

                foreach ($res_messages as $m)
                {
                    $message = [
                        'newMessage' => 1,
                        'title' => $m['title'],
                        'message' => $m['text'],
                        'icon' => '/assets/img/pushmessage.png',
                        'notification' => [
                            'data' => $m['push_link'],
                        ],
                    ];

                    $mess_model = new Model_User_Message($m['id']);
                    $mess_model->show = 0;
                    $mess_model->time_read = time();
                    $mess_model->save();
                }
            }
            $this->response->body(json_encode($message));
        }





        public function action_spam()
        {

            $this->auto_render = false;

            $ans = ['error' => 1, 'text' => __('Ошибка при сохранении')];

            if ($this->request->method() == 'POST')
            {
                $enable = intval(arr::get($_POST, 'enable', 1));

                auth::parent_acc()->getspam = $enable;
                auth::parent_acc()->save();

                $ans = ['error' => 0, 'text' => __('Вы отписаны от всех рассылок')];
            }

            $this->response->body(json_encode($ans));
        }

        public function action_passport()
        {

            $this->auto_render = false;

            $ans = ['error' => 1, 'text' => __('Ошибка при сохранении')];

            if ($this->request->method() == 'POST')
            {
                $file = arr::get($_FILES, 'passport');

                if (isset($file) AND $file['size'] > 0)
                {
                    $folder = '/upasp/' . auth::parent_acc()->id . '/';
                    $format = explode('.', $file['name']);
                    $image = $folder . time() . '.' . $format[count($format) - 1];
                    $name = DOCROOT . $image;

                    if (!is_dir(DOCROOT . $folder))
                    {
                        mkdir(DOCROOT . $folder, 02777);
                        chmod(DOCROOT . $folder, 02777);
                    }

                    if (move_uploaded_file($file['tmp_name'], $name))
                    {
                        $profile = new Model_User_Profile(['user_id' => auth::user()->parent_id]);
                        $profile->passport = $image;
                        $profile->save();

                        $ans = ['error' => 0, 'text' => __('Фото паспорта сохранено')];
                    }
                }
            }

            $this->response->body(json_encode($ans));
        }

        public function action_registration()
        {
            $view = new View('login/registration');
            $view->offices = orm::factory('office')->where('id', 'in', kohana::$config->load('static.offices'))->find_all();

            $this->template->content = $view;
        }

    }
