<?php
class Vidget_Text extends Vidget{

	
function _list($model){
	return HTML::chars($model->__get($this->name));
}	

function _item($model){
	return form::textarea($this->name($model),$model->__get($this->name));
}	

function _search($vars){
	return form::input($this->element_name,$vars[$this->name]);
}	





}