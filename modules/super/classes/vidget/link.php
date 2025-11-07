<?php
class Vidget_Link extends Vidget{

	
function _list($model){
	$id=$model->__get($this->param['id']);
        $query=arr::get($this->param,'query','');
	return "<a href=\"{$this->param['link']}{$id}?{$query}\">{$this->param['text']}</a>";
}	

function _item($model){
	
	$id=$model->__get($this->param['id']);
	return "<a href=\"{$this->param['link']}{$id}\">{$this->param['text']}</a>";
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



