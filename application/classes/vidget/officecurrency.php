<?php

class Vidget_Officecurrency extends Vidget_Input
{
    public function _item($model) {
        if($model->office) {
            return "<span>{$model->office->currency->code}</span>";
        }
        return "<span>{$model->user->office->currency->code}</span>";
    }

    public function _list($model) {
        if($model->office) {
            return "<span>{$model->office->currency->code}</span>";
        }
        return "<span>{$model->user->office->currency->code}</span>";
    }

    function handler_search($model,$vars){
        return $model;
    }

    function handler_save($data,$old_data,$model){
        return $model;
    }
}
