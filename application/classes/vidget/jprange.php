<?php

class vidget_jprange extends Vidget_Input {

    public function _item($model)
    {
        $min = $this->param['min'];
        $max = $this->param['max'];

        $can_edit_min = arr::get($this->param,'edit_min',true);
        $can_edit_max = arr::get($this->param,'edit_max',true);

        $html = '';

        if($can_edit_min) {
            $html.=form::input($min, $model->__get($min), array('class' => "field text medium", 'maxlength' >= "255"));
        }
        else {
            $html.=HTML::chars($model->__get($min));
            $html.=form::input($min, $model->__get($min), array('class' => "field text medium", 'maxlength' >= "255",'type'=>'hidden'));
        }

        $html.='-';

        if($can_edit_max) {
            $html.=form::input($max, $model->__get($max), array('class' => "field text medium", 'maxlength' >= "255"));
        }
        else {
            $html.=HTML::chars($model->__get($max));
            $html.=form::input($min, $model->__get($max), array('class' => "field text medium", 'maxlength' >= "255",'type'=>'hidden'));
        }

        return $html;
    }

    function handler_save($data,$old_data,$model){
        $min = $this->param['min'];
        $max = $this->param['max'];
        $model->set($min,$data[$min]);
        $model->set($max,$data[$max]);
        return $model;
    }

}
