<?php
class Vidget_Codelink extends Vidget{

	
function _list($model){
	$code = $model->__get($this->name);
	return "<a href=\"#\">{$model->get_domain()}{$this->param['link']}{$code}</a>";
}	

function _item($model){
	$code = $model->__get($this->name);
	return "<a href=\"#\">{$model->get_domain()}{$this->param['link']}{$code}</a>";
}	

function _search($vars){
	return 'Нельзя использовать Vidget Link в поиске';
}	





function handler_save($data,$old_data,$model){
	return $model;
}




function handler_search($model,$vars){
	
	return $model;	
	
}



}



