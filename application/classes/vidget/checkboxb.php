<?php


class Vidget_CheckBoxb extends Vidget_CheckBox{


    function _list($model)
    {

        if ($model->__get($this->name) > 0) {
            return 'Yes';
        }

        return 'No';


    }

}