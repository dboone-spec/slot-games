<?php
class Vidget_Password extends Vidget{
	
	
	
function _list($model){
	return null;
}	

function _item($model){
    
        if (!$model->loaded()){
            return form::input($this->name($model),rand(10000,99999));
        }
    
	return form::input($this->name($model));
}	

function _search($vars){
	return null;
}	



function handler_save($data,$old_data,$model){
	$pass=$data[$this->name];
	
	if (!empty($pass)){
	
		if (isset($this->param['salt'])){
			$salt=rand(10000,1000000);
			$model->set($this->param['salt'],$salt);
			$model->set($this->name,call_user_func_array($this->param['func'],array($pass,$salt)));
		}
		else{
			$model->set($this->name,call_user_func($this->param['func'],$pass));
		}
	}
	
	return $model;
}

	
}