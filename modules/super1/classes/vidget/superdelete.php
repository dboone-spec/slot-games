<?php

class Vidget_SuperDelete extends Vidget_CheckBox{

	
function _list($model){
	
	return 'Don\'t use vidget SuperDelete in list';
}	

function _item($model){
	
	
	return form::checkbox($this->name($model),null,false,array('id'=>$this->id($model))).'<label for="'.$this->id($model).'"> Удалить </label>';
	
}	

function _search($vars){
	return 'Don\'t use vidget SuperDelete in list';
}	


function handler_save($data,$old_data,$model){

	$r= isset($data[$this->name]) ? 1 : 0;
	$model->delete();
	return $model;
}





}