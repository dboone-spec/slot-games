<?php

class Vidget_Listfloat extends Vidget_List {









function handler_save($data,$old_data,$model){

    if(!arr::get($this->param,'can_edit',false)) {
        return $model;
    }

    if (!isset($this->param['list'][$data[$this->name]])){
        throw  new HTTP_Exception_403();
    }

    $model->set($this->name, floatval ($data[$this->name]) );
    return $model;
}


function _item($model){

    $txt= parent::_item($model);

    $txt.=$this->param['text'] ?? '';

    return $txt;


}



}

