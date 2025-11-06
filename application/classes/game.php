<?php

class game {

/**
 *
 * @var Game_Session
 */
protected static $session=null;

public static function view_session($type,$game='game'){
    if(!auth::$user_id) {
        return;
    }
    //todo add one more instance?
    return th::ObjectToArray((new Game_Session(['user_id'=>auth::$user_id, 'type'=>$type, 'game'=>$game]))->data);
}

public static function session_start($type,$game='game'){

	if(!empty(self::$session)){
		throw new Exception('Session already started!');
	}

	if (!auth::$user_id){
		self::$session=new Game_Session();
		return self::$session;
	}

	self::$session=new Game_Session(['user_id'=>auth::$user_id, 'type'=>$type, 'game'=>$game]);
	if (!self::$session->loaded()){
		self::$session->user_id=auth::$user_id;
		self::$session->type=$type;
		self::$session->game=$game;
		self::$session->save();
	}

	return self::$session;

}


public static function session($type=null,$game='game'){

	if (!empty($type)){
		return self::session_start($type,$game);
	}

	if (empty (self::$session)){
		throw new Exception('Session not started!');
	}
	return self::$session;

}



public static function data($idx=null,$default=null){


	if(!self::session()->data) {
		self::session()->data = [];
	}

	$data=th::ObjectToArray(self::session()->data);

	if (empty($idx)){
		return $data;
	}

	return arr::get($data,$idx,$default);


}


public static function save($newdata = array()){
	if(!empty($newdata)) {
		game::session()->flash($newdata);
	}
}









}

