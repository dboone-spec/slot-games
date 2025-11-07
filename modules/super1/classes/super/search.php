<?php

class Super_Search {
	
	
protected $search;

protected $search_vars;
protected $vars;

public function __construct($search,$array){

	$this->vars=$array;
	$this->search=$search;
}
	


public  function make($model){

	
	$this->search_vars=array();	
	foreach($this->search as $name=>$func){
		
		if (!method_exists($this,$func)){
			throw Kohana_Exception::handler(new Exception("Method  search::$func don't exist"));
		}

		$param=array($name,$model);
		call_user_func_array(array($this,$func),$param);
		
	}
	return $model;	

}
	
public function search_vars(){
	return $this->search_vars;
}


/******************
	Обработчики
*********/
public function like($name,$model){
	
	
	if (isset($this->vars[$name])){
		$this->search_vars[$name]=$this->vars[$name];
		return $model->where($name,'like','%'.$this->vars[$name].'%');
	}
	$this->search_vars[$name]='';
	return $model;
}


public function equally($name,$model){
	
	
	if (isset($this->vars[$name]) and (!empty($this->vars[$name])) ){
		$this->search_vars[$name]=$this->vars[$name];
		return $model->where($name,'=',$this->vars[$name]);
	}
	$this->search_vars[$name]='';
	return $model;
}



}













