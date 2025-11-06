<?php
class Person {
    public static $_table='persons';
    public static $prefix='person_';
    protected static $_instance = NULL;
    public static $user_id = false;

    public static $role=null;


    public static function instance(){
        if (self::$_instance == NULL)
            self::$_instance = new Person();
        return self::$_instance;
    }
    public function get_user(){
		self::$user_id = Session::instance()->get(self::$prefix.'user_id');

        if (empty(self::$user_id)){
        	$code=Cookie::get(self::$prefix.'auth');


        	if (!empty($code)) {

        		$sql='select id from '.self::$_table.' where remember=:code';
        		$u=db::query(1,$sql)->param(':code',$code)->execute()->as_array();

        		if ($u){
        			Session::instance()->set(self::$prefix.'user_id', self::$user_id);
        			self::$user_id=$u[0]['id'];
        		}
        	}
        }


        return NULL;
    }
    public function __construct(){
        $this->get_user();
        $this->set_role();
    }



	protected static $user;
	public static function  user($update=false): Model_Person{
		if (empty(self::$user) or $update){
			self::$user=new Model_Person(self::$user_id);
		}
		return self::$user;

	}

    protected function set_role(){
        self::$role=self::user()->role;
	}

	public static function role(){
		return self::user()->role;
	}

    public static function force_login($name){
        $user=new Model_Person(array('name'=>$name));
        if ($user->loaded()){
            $l = new Model_Person_Login;
            $l->ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $l->person_id = $user->id;
            $l->save();
            
            
            self::$user_id = $user->id;
            self::$role = $user->role;
			$user->last_login=time();
			$user->save();
			self::$user=$user;
            Session::instance()->set(self::$prefix.'user_id', self::$user_id);

        }
    }


	public static function force_login_model($user){

		if ($user->loaded()){
                    
                    $l = new Model_Person_Login;
                    $l->ip = $_SERVER['REMOTE_ADDR'] ?? null;
                    $l->person_id = $user->id;
                    $l->save();    
                    
                    self::$user_id = $user->id;
                    self::$role = $user->role;
                    Session::instance()->set(self::$prefix.'user_id', self::$user_id);
                    return true;
		}

		return false;


    }

    //return code of error. 0 - wrong login/pass; 1 - not have phone; 2

public static function login($name, $password,$rem=false){
    $user=new Model_Person(array('name'=>$name));
    if ($user->loaded() AND !$user->blocked){
        if ($user->password==self::pass($password,$user->salt)){

        

        self::$user_id = $user->id;
        Session::instance()->set(self::$prefix.'user_id', self::$user_id);
        $user->last_login=time();

        if ($rem===true){
            if ($user->last_login<time()-60*60*24*8){
                    $code=guid::create();
            }
            else{
                    $code=$user->remember;
            }

            $user->remember=$code;
            Cookie::set(self::$prefix.'auth',$code);
        }
        
        $l = new Model_Person_Login;
        $l->ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $l->person_id = $user->id;
        $l->save();    

        
        $user->save();
        

        return true;
        }
    }
    return false;
}


    public static function rolelist($role_name = null) {
        $roles = [
            "agent"=>__("Агент"),
            "kassa"=>__("Кассир"),
            "administrator"=>__("Администратор"),
            "manager"=>__("Менеджер"),
            "rmanager"=>__("Рег. менеджер"),
            "analitic"=>__("Аналитик"),
        ];

        if(!$role_name) {
            return $roles;
        }

        return arr::get($roles,$role_name,'');
    }


    public static function logout(){
        self::$user_id = NULL;
        Session::instance()->destroy();
        Cookie::delete(self::$prefix.'auth');
    }


    public static function pass($pass,$salt){

    	return md5(md5($pass).$salt);
    }

    public static function agent() {
        return new Model_Person(Person::user()->parent_id);
    }
}


