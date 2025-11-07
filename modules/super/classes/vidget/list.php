<?php
class Vidget_List extends Vidget_Input{




function _list($model){

	if (isset($this->param['list'][$model->__get($this->name)])){
	    return HTML::chars($this->param['list'][$model->__get($this->name)]);
	}

	return '';
}

function _item($model){
    $can_edit=arr::get($this->param,'can_edit',true);
    if(!$can_edit) {
        return HTML::chars($this->param['list'][$model->__get($this->name)]).'<input name="'.$this->name.'" type="hidden" value="'.$model->__get($this->name).'" />';
    }
	return form::select($this->name($model),$this->param['list'],$model->__get($this->name));
}

function _search($vars){

	$list=array('alldata'=>'All')+$this->param['list'];
	return form::select($this->name,$list,$vars[$this->name]);

}




function handler_search($model,$vars){

	$value='alldata';

	if (isset($vars[$this->name]) and !empty($vars[$this->name]) and $vars[$this->name]!='alldata'){
		$value=$vars[$this->name];
		$model->where($this->model->object_name().'.'.$this->name,'=',$value);
	}

	$this->search_vars[$this->name]=$value;
	return $model;

}

function handler_save($data,$old_data,$model){

	$model->set($this->name,$data[$this->name]);
	return $model;
}


}
