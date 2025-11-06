<?php

class Vidget_Timestampecho extends Vidget_Timestamp
{
    function _list($model)
    {
        if($model->__get($this->name)==0) {
            return 0;
        }
        return !is_null($model->__get($this->name)) ? date($format = 'd.m.y H:i:s',$model->__get($this->name)) : '';
    }

    function _item($model)
    {
        $value = $model->loaded() ? $model->__get($this->name) : time();
        return !is_null($value) ? date('d-m-y H:i:s',$value) : '';
    }

    function handler_save($data,$old_data,$model)
    {
        return $model;
    }
}
