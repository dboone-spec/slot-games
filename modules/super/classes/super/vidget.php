<?php
abstract class super_vidget{
	
	public $element_id;
	public $element_name;
	public $element_id_prefix;
	public $element_name_prefix;
	protected $name;
	protected $model;
	protected $m_name;
	protected $param=array();
	protected $option=array();
	protected $many=false;
	
public function __construct($name,$model){

	//for form vidget
	if(Request::current()->is_initial()){
		$this->element_id=$name;
		$this->element_name=$name;
		$this->element_name_prefix='';
	}
	else{
		$id=arr::get(Request::current()->query(),'__super_id');
		$num=arr::get(Request::current()->query(),'__num',0);
		$this->element_id="{$id}_{$num}_$name";
		$this->element_name="{$id}[$num][$name]";
		$this->element_name_prefix="{$id}[$num]";
	}
	
	
	
	$this->name=$name;
	$this->model=$model;
	$this->m_name=$model->object_name();
	$this->m_name=strtolower($this->m_name);
}	



public static function factory($vidget_name,$name,$model)
{
	// Set class name
	$vidget='Vidget_'.ucfirst($vidget_name);

	return new $vidget($name,$model);
}

public function param($a,$value=null){

	if (is_array($a)){
		$this->param=array_merge($this->param,$a);	
	}
	else{
		$this->param[$a]=$value;
	}

}

public function option($a,$value=null){

	if (is_array($a)){
		$this->option=array_merge($this->param,$a);	
	}
	else{
		$this->option[$a]=$value;
	}

}
	
public function render($model,$type){
	
	if ($type=='list'){
		return $this->_list($model);
	}
	
	if ($type=='item'){
		return $this->_item($model);
	}
	
	if ($type=='search'){
		return $this->_search($model);
	}
	
	if ($type=='listedit'){
		$this->many=true;
		return $this->_item($model);
	}
	
	throw new Exception("Undefined vidget render method '$type'");
}	



public function name($model){
	if ($this->many){
		return $this->element_name.'['.$model->pk().']';
	}
	
	return $this->element_name;
}

public function id($model){
	if ($this->many){
		return $this->element_id.'_'.$model->pk();
	}
	
	return $this->element_id;
}



abstract function _list($model);

abstract function _item($model);

abstract function _search($vars);

	

function handler_save($data,$old_data,$model){
	$model->set($this->name,$data[$this->name]);
	return $model;
}


public $search_vars=array();

function handler_search($model,$vars){
	
	if (isset($vars[$this->name]) and !empty($vars[$this->name])){
		$this->search_vars[$this->name]=$vars[$this->name];
		return $model->where($this->m_name.'.'.$this->name,'like','%'.$vars[$this->name].'%');
	}
	$this->search_vars[$this->name]='';
	return $model;	
	
}

}



