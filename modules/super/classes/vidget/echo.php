<?php

class Vidget_Echo extends Vidget_Input {

    function _item($model) {

        try {
            $s = (string) $model->__get($this->name);
        } catch (Exception $exc) {
            $s = json_encode($model->__get($this->name));
        }

        return HTML::chars($s);
    }

    function handler_save($data, $old_data, $model) {
        return $model;
    }

}
