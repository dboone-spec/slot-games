<?php

class Vidget_Blocked extends Vidget_CheckBox{


function _list($model){

    $str= parent::_list($model);

    if($model instanceof Model_Office && $model->__get($this->name)>0) {
        $extra = '<br />'.date('m-d-Y H:i:s');
		return "<input type='checkbox' id='$id' style='cursor: pointer;' checked>".$extra.$js;
    }

	if ($model->__get($this->name)==1){
		return "<input type='checkbox' id='$id' style='cursor: pointer;' checked>".$js;
	}
    return "<input type='checkbox' id='$id' style='cursor: pointer;'>".$js;
}




}