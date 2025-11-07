<?php
class Vidget_Echo_List extends Vidget_List{


	
	
function _item($model){
	
	if (isset($this->param['list'][$model->__get($this->name)])){
	    return HTML::chars($this->param['list'][$model->__get($this->name)]);
	}
    
	return '';
}	

function handler_save($data, $old_data, $model) {
    return $model;
}

}
