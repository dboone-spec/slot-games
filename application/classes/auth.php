<?php

    class Auth
    {

        public static $_table = 'users';
        public static $prefix = 'user_';
        protected static $_instance = NULL;
        public static $user_id = false;
        public static $force_user_id = false;
        public static $role = null;

        public static function instance()
        {
            if (self::$_instance == NULL)
                self::$_instance = new Auth();
            return self::$_instance;
        }

        public function get_user()
        {

            if(PROJECT==1 && !empty(self::$token)) {
                return NULL;
            }

            self::$user_id = Session::instance()->get(self::$prefix . 'user_id');

            if(self::$force_user_id) {
                self::$user_id=self::$force_user_id;
            }

            $md5 = false; //was coockie md5 key

            if($md5 && empty(self::$user_id)) {


                $sql = 'select id,office_id from ' . self::$_table . ' where name=:name';
                $up = db::query(1, $sql)->param(':name', 'frln*'.$md5)->execute()->as_array();

                if ($up)
                {

                    $sql = 'select id from ' . self::$_table . ' where parent_id=:pid and office_id = :o_id';
                    $u = db::query(1, $sql)->param(':pid', $up[0]['id'])->param(':o_id',$up[0]['office_id'])->execute()->as_array();

                    if($u) {

                        Session::instance()->set(self::$prefix . 'user_id', self::$user_id);
                        self::$user_id = $u[0]['id'];
                        return NULL;
                    }
                }
            }

            if (empty(self::$user_id))
            {
                $code = Cookie::get(self::$prefix . 'auth');


                if (!empty($code))
                {

                    $sql = 'select id from ' . self::$_table . ' where remember=:code';
                    $u = db::query(1, $sql)->param(':code', $code)->execute()->as_array();

                    if ($u)
                    {
                        Session::instance()->set(self::$prefix . 'user_id', self::$user_id);
                        self::$user_id = $u[0]['id'];
                    }
                }
            }


            return NULL;
        }

        public function __construct()
        {
            $this->get_user();
        }

        protected static $user;
        public static $token;

        /**
         *
         * @param bool $update
         * @return Model_User
         */
        public static function user($update = false)
        {
            if (self::$user instanceof Model_DemoUser){
                return  self::$user;
            }

            if (empty(self::$user) or $update)
            {
                self::$user = new Model_User(self::$user_id);
            }
            return self::$user;
        }

        public static function role()
        {
            return self::user()->role;
        }

        public static function setCustomSessionId($user_id, $val) {
            dbredis::instance()->set('CustomSessionId'.$user_id,$val);
            dbredis::instance()->expire('CustomSessionId'.$user_id, 365*24*60*60);
        }

        public static function getCustomSessionId($user_id, $default) {
            $redis = dbredis::instance();
            $currdb = $redis->getDBNum();
            $redis->select(0);
            $s = dbredis::instance()->get('CustomSessionId'.$user_id);
            if(!$s) {
                $s=$default;
            }
            $redis->select($currdb);
            return $s;
        }

		public static function setCustomGameSessionId($user_id, $game, $val) {
            dbredis::instance()->set('CustomGameSessionId'.$user_id.$game,$val);
            dbredis::instance()->expire('CustomGameSessionId'.$user_id.$game, 365*24*60*60);
        }

        public static function getCustomGameSessionId($user_id, $game, $default) {
            $redis = dbredis::instance();
            $currdb = $redis->getDBNum();
            $redis->select(0);
            $s = dbredis::instance()->get('CustomGameSessionId'.$user_id.$game);
            if(!$s) {
                $s=$default;
            }
            $redis->select($currdb);
            return $s;
        }

        public static function from_token($token, $user_id,$game=null) {

            if($token=='demo') {
                $u = new Model_DemoUser($user_id);

                self::$user_id = $u->id;
                self::$user = $u;
                self::$token = $token;

                return;
            }

            $key='token'.$user_id;

            if(!empty($game)) {
                $key.=$game;
            }

            $t = dbredis::instance()->get($key);
            if($t==$token) {
                $u = new Model_User($user_id);
                if ($u->loaded() AND ! $u->blocked)
                {
                    self::$user_id = $u->id;
                    self::$user = $u;
                    self::$token = $token;
                    return;
                }
            }
            throw new HTTP_Exception_403;
        }

        public static function game_login(Model_User $curr_user,$game=null,$allow_multiple=true) {
            if ($curr_user->loaded() AND ! $curr_user->blocked)
            {
                self::$user_id = $curr_user->id;

                $curr_user->last_login = time();
                $curr_user->save();

                self::$user = $curr_user;

                $key='token'.self::$user_id;

                if(!empty($game)) {
                    $key.=$game;
                }

                if($allow_multiple) {
                    self::$token = dbredis::instance()->get($key);
                }
                if(empty(self::$token)) {
                    self::$token = guid::create();
                    dbredis::instance()->set($key, self::$token);
                }
                dbredis::instance()->expire($key, 24*60*60);
            }
        }

        public static function force_login($name, $fingerprint = null,$rem=false)
        {
            $curr_user = new Model_User(array('name' => $name));

            if ($curr_user->loaded() AND ! $curr_user->blocked)
            {
                self::$user_id = $curr_user->id;
                $curr_user->last_login = time();

                if($rem) {
                    $code = guid::create();
                    $time = 365 * 24 * 60 * 60;
                    $curr_user->remember = $code;
                    Cookie::set(self::$prefix . 'auth', $code, $time);
                }

                $curr_user->last_login = time();
                $curr_user->save();

                $l = new Model_Login;
                $l->ip = $_SERVER['REMOTE_ADDR'] ?? null;
                $l->user_id = $curr_user->id;
                $l->fingerprint = $fingerprint;
                $l->save();

                self::$user = $curr_user;
                Session::instance()->set(self::$prefix . 'user_id', self::$user_id);
                cookie::delete('regfs');
            }
        }

        public static function force_login_model($user, $fingerprint = null)
        {
            $curr_user = new Model_User(['id' => $user->id, 'office_id' => $user->office_id]);

            if ($curr_user->loaded() AND ! $curr_user->blocked)
            {
                self::$user_id = $curr_user->id;
                self::$force_user_id = self::$user_id;
                $curr_user->last_login = time();
                $curr_user->save();

                if(!in_array((int) $curr_user->office_id,[111,444,777,999,456])) {
                    $l = new Model_Login;
                    $l->ip = $_SERVER['REMOTE_ADDR']??'local';
                    $l->user_id = $curr_user->id;
                    $l->fingerprint = $fingerprint;
                    $l->save();
                }

                Session::instance()->set(self::$prefix . 'user_id', self::$user_id);
                cookie::delete('regfs');
                return true;
            }

            return false;
        }

        public static function create_office_account($user, $office_id = null)
        {
            if ($user->loaded() AND ! $user->parent_id)
            {
                $office_id = $office_id ?? $user->office_id;
                $office_account = new Model_User(['parent_id' => $user->id, 'office_id' => $office_id]);

                if (!$office_account->loaded())
                {
                    $reset_balance = false;

                    $office_account->name = $user->name . '_' . $office_id;
                    $office_account->parent_id = $user->id;
                    $office_account->office_id = $office_id;
                    $office_account->invited_by = $user->invited_by;
                    $office_account->partner = $user->partner;
                    $office_account->project = $user->project;
                    $office_account->msrc = $user->msrc;
                    $office_account->getspam = $user->getspam;
                    $office_account->dsrc = $user->dsrc;
                    $office_account->created = $user->created;

                    database::instance()->begin();

                    $office_account->save();

                    $fields = [
                        "amount", "bonus", "bonusbreak",
                        "bonuscurrent", "sum_win", "sum_amount",
                        "sum_in", "sum_out", "sum_bonus",
                        "last_drop", "compoints", "comp_level",
                        "comp_process", "comp_current",
                    ];

                    if ($office_id == 1)
                    {
                        foreach ($fields as $field)
                        {
                            $office_account->$field = $user->$field;
                            $user->$field = 0;
                        }

                        $reset_balance = true;
                    }

                    try
                    {
                        if ($reset_balance)
                        {
                            $user->save();
                        }
                        $office_account->save()->reload();

                        database::instance()->commit();
                    }
                    catch (Exception $exc)
                    {
                        database::instance()->rollback();
                        throw $exc;
                    }

                    return $office_account;
                }

                return $office_account;
            }
        }
		
		public static function add_sbc_account($visible_name,$name)
        {
            $u = new Model_User(['name'=>$name]);

            if($u->loaded()) {
                self::force_login_model($u);
                return;
            }

            $u->salt = rand(1, 10000000);
            $u->api = 0;
            $u->amount = 5000;

            $u->office_id = DEMO_OFFICE_ID;

            $u->email_valid = 0;

			$u->lang = 'en-no';
            $u->name = $name . $u->id;
            $u->visible_name = $visible_name;
            $u->comment = $visible_name.'('.$name.')';
            $u->email = $name . $u->id . '@loc.loc';

            $u->save()->reload();

            $code = guid::create();
            $time = 24 * 60 * 60;
            $u->remember = $code;
            Cookie::set(self::$prefix . 'auth', $code, $time);

            self::force_login_model($u);
        }

        public static function add_demo_account()
        {
            $name = microtime(1);
            $u = new Model_User();

            $u->salt = rand(1, 10000000);
            $u->api = 0;
            $u->amount = 5000;

            if(defined('RELEASE_DOMAIN') && RELEASE_DOMAIN) {
                $u->office_id = 111;
            }
            else {
                $u->office_id = OFFICE;
            }

            $u->email_valid = 0;

            //$u->dsrc = isset($_SERVER['HTTP_HOST']) ? str_replace(['.'], '', $_SERVER['HTTP_HOST']) : null;

            $u->name = $name . $u->id;
            $u->email = $name . $u->id . '@loc.loc';

            $u->save()->reload();

            $code = guid::create();
            $time = 24 * 60 * 60;
            $u->remember = $code;
            Cookie::set(self::$prefix . 'auth', $code, $time);

            self::force_login_model($u);
        }

        public static function force_register($name, $password, $rem = false)
        {
            $name = UTF8::strtolower($name);

            $type = !th::checkphone($name)?'phone':'email';
            if($type=='email' && !valid::email($name)) {
                $type='login';
            }

            $u = new Model_User();;
            $u->name = ($type=='phone')?th::nic().substr($name, -2, 2):$name;
            $u->phone = ($type=='phone')?th::clearphone($name):null;
            $u->getspam = 1;
            $u->phone_confirm = 0;
            $u->email = ($type=='email')?$name:null;
            $u->office_id = 1;

            try
            {

                if ($rem === true)
                {
                    $code = guid::create();
                    $u->remember = $code;
                    Cookie::set(self::$prefix . 'auth', $code);
                }

                $u->salt = rand(1, 10000000);
                $u->password = auth::pass($password, $u->salt);
                $u->api = 0;
                $u->amount = 0;
                $u->msrc = Cookie::get('msrc');
                $u->dsrc = isset($_SERVER['HTTP_HOST']) ? str_replace(['.'], '', $_SERVER['HTTP_HOST']) : null;
                $u->partner = intval(Cookie::get('partner')) ? Cookie::get('partner') : null;
                $u->project = Cookie::get('project');
                $u->save()->reload();

                auth::create_office_account($u);
            }
            catch (Exception $e)
            {

            }

            return $u;
        }

        public static function login($name, $password, $rem = false, $fingerprint = null)
        {
            $name = UTF8::strtolower($name);
            $user = new Model_User(array('name' => $name));

            if ($user->loaded() AND ! $user->blocked)
            {
                //запрет авторизации оффлайна
                if (false && OFFLINE && in_array($user->office_id, [1, 4]))
                {
                    return false;
                }

                if ($user->password == self::pass($password, $user->salt))
                {
                    self::$user_id = $user->id;
                    Session::instance()->set(self::$prefix . 'user_id', self::$user_id);

                    $user->last_login = time();

                    $l = new Model_Login;
                    $l->ip = $_SERVER['REMOTE_ADDR'];
                    $l->fingerprint = $fingerprint;
                    $l->user_id = $user->id;
                    $l->save();


                    if ($rem === true)
                    {
                        if ($user->last_login < time() - 60 * 60 * 24 * 8)
                        {
                            $code = guid::create();
                        }
                        else
                        {
                            $code = $user->remember;
                        }

                        $user->remember = $code;
                        Cookie::set(self::$prefix . 'auth', $code);
                    }
                    $user->save();
                    cookie::delete('regfs');

                    $bonus = $user->pay_bia();

                    if((float) $bonus>0) {
                        Flash::info('<img src="/assets/img/bonus.jpg" />'.__('На Ваш счет начислено ').th::number_format($bonus).__(' бонусов').'!!!');
                    }

                    self::game_login($user);

                    return true;
                }
            }
            return false;
        }

        public static function logout()
        {
            self::$user_id = NULL;
            Session::instance()->destroy();
            Cookie::delete(self::$prefix . 'auth');
        }

        public static function pass($pass, $salt)
        {

            return md5(md5($pass) . $salt);
        }

    }
