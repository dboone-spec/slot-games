<?php

class Vidget_SelectDenTab extends Vidget_Input
{

    function handler_save($data,$old_data,$model){
        if(Person::$role=='sa') {
            return parent::handler_save($data,$old_data,$model);
        }
        return $model;
    }

    function _item($model){
        $params = array(
                'class'=>"field text medium",
                'style'=>'overflow-x: hidden;width:100px;padding:0;text-align:center',
                'size'=>count($this->param['list'])
            );

        if(Person::$role!='sa') {
            $params['disabled']='disabled';
        }
	
	$this->param['list'] = $model->get_k_list() ?? $this->param['list'];

        return form::select($this->name($model), $this->param['list'],(int) $model->__get($this->name),$params);
    }
}
