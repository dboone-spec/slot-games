<?php

class Vidget_Select extends Vidget_Input
{

    function _item($model){
        $params = array('class'=>"field text medium");
        if(!($this->param['can_edit']??true)) {
            $params=['disabled'=>'disabled'];
        }
        return form::select($this->name($model), $this->param['fields'],$model->__get($this->name),$params);
    }

    public function handler_search($model, $vars) {
        if(in_array($this->name,['office_id','show','blocked'])) {
            if (isset($vars[$this->name]) and $vars[$this->name]!=''){

                if($this->name=='office_id' && $vars[$this->name]<=0) {
                    return parent::handler_search($model, $vars);
                }

                $this->search_vars[$this->name] = $vars[$this->name];
                return $model->where($this->name, '=', (int)$vars[$this->name]);
            }
        }
        return parent::handler_search($model, $vars);
    }

    function handler_save($data,$old_data,$model){
        $fields = array_keys($this->param['fields']);

        if(isset($data[$this->name]) AND in_array($data[$this->name], $fields)) {
            $model->set($this->name,$data[$this->name]);
        }

        return $model;
    }

    function _list($model) {
        $value = $model->__get($this->name);
        return HTML::chars(arr::get($this->param['fields'], $value, $value));
    }

    function _search($vars) {
        return form::select($this->name, $this->param['fields'],$vars[$this->name],array('class'=>"field text medium"));
    }
}
