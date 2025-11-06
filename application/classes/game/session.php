<?php

class Game_Session {

    protected $_key;
    protected $_keys=[];
    protected $_redis;
    protected $_loaded=false;
    protected $_values=[];

    public function __set($name, $value) {
        $this->_values[$name]=$value;
    }

    public function __get($name) {
        if(isset($this->_values[$name])) {
            return $this->_values[$name];
        }
        if(isset($this->_keys[$name])) {
            return $this->_keys[$name];
        }
        return null;
    }

    public function save() {
        $this->_redis->set($this->_key, json_encode($this->_values));
        $this->_redis->expire($this->_key, static::$session_time);
    }

    public function load() {
        $data = $this->_redis->get($this->_key);
		if(!$data) {
            $this->_loaded=false;
            return $this;
        }

        $this->_values = json_decode($data,1);
        $this->_loaded=true;
        return $this;
    }

    public function __construct($keys=[]) {
        $this->_keys = $keys; //for model sync
        $this->_key = implode('-',$keys);
        $this->_redis = dbredis::instance();

        $dbi = 0;
        $this->_redis->select($dbi);


        if(!empty($keys)) {

//            $r->rpush("finderQueue", json_encode($find));
//            $r->set('finderResult-'.$find['id'],'queue');
//            $r->expire('finderResult-'.$find['id'], 60*20);

            $this->load();
        }
    }

    public function loaded() {
        return $this->_loaded;
    }

    public static $session_time=1*12*60*60;

    public static function restorebackup($bet_id,$user_id,$game,$brand='agt') {
        $redis=dbredis::instance();
        $redis->select(0);

        $session_key=implode('-',[$user_id,$brand,$game]);
        $session=json_decode($redis->get($session_key),1);

        $bkp=json_decode($redis->get('bkp_sess_'.$bet_id),1);
        $session['data']=$bkp;


        $redis->set($session_key, json_encode($session));
        $redis->expire($session_key, static::$session_time);

        $redis->delete('bkp_sess_'.$bet_id);
    }

    public static function savebackup($bet_id,$data) {
        $redis=dbredis::instance();
        $redis->select(0);

        $redis->set('bkp_sess_'.$bet_id, json_encode(th::ObjectToArray($data)));


        $redis->expire('bkp_sess_'.$bet_id, static::$session_time);
    }

	    public static function clearFG($game) {

        if(empty(auth::$user_id)) {
            throw new Exception('where user_id???');
        }

        $currentDb = dbredis::instance()->getDbNum();

        dbredis::instance()->select(0);

        //total to play FG count
        $key='freeCountTotal'.auth::$user_id.'-'.$game;
        dbredis::instance()->set($key, 0, array ('ex' => Game_Session::$session_time));

        //current played FG count
        $key2='freeCountAll'.auth::$user_id.'-'.$game;
        dbredis::instance()->set($key2, 0, array ('ex' => Game_Session::$session_time));

        dbredis::instance()->select($currentDb);

    }

    public function flash($data = null,$force=false) {


        if (!$force and !$this->loaded()) {
            return false;
        }

        if (is_array($data)) {
            $r = th::ObjectToArray($this->data);

            if (is_array($r)) {
                foreach ($r as $key => $value) {
                    if (!isset($data[$key])) {
                        $data[$key] = $value;
                    }
                }
            }
        }

        $this->last = time();
        $this->data = $data;
        $this->save();

        return true;
    }

    public function reload() {
        $this->load();
    }
}