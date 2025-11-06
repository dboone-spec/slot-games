<?php

class dbredis {


	protected $con;
	protected function __construct() {
		$this->con=new Redis();
		$conf= Kohana::$config->load('redis');
		$this->con->connect($conf['connection']['hostname'],$conf['connection']['port']);
                if (isset($conf['connection']['password']) and !empty($conf['connection']['password'])){
                    $this->con->auth($conf['connection']['password']);
                }
    }

	protected static $instance;

	public static function instance()
	{
		if (!isset(static::$instance)){
			static::$instance=new static;
		}
		return static::$instance;
	}


	public function __call($name,$arguments){
        if($name=='select' && $arguments[0]==1 && Kohana::$environment==Kohana::DEVELOPMENT) {
            $arguments[0]++;
        }
		return  call_user_func_array([$this->con,$name],$arguments);
	}


}
