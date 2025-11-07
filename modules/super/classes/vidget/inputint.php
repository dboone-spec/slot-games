<?php
class Vidget_Inputint extends Vidget_Input{

	
function handler_search($model,$vars){
	
	if (isset($vars[$this->name]) and !empty($vars[$this->name])){
		$this->search_vars[$this->name]=$vars[$this->name];
		return $model->where($this->name,'=',$vars[$this->name]);
	}
	$this->search_vars[$this->name]='';
	return $model;	
	
}



}