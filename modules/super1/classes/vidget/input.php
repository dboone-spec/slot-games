<?php

class Vidget_Input extends Vidget {

    function _list($model) {
        $can_edit=arr::get($this->param,'can_edit',false);
        return HTML::chars($model->__get($this->name));
    }

    function _item($model) {
        return form::input($this->name($model), $model->__get($this->name), array('class' => "field text medium", 'maxlength' >= "255"));
    }

    function _search($vars) {

        return form::input($this->name, $vars[$this->name]);
    }

    function handler_search($model, $vars) {
        if (isset($vars[$this->name]) and ! empty($vars[$this->name])) {
            $val = trim($vars[$this->name]);
            $this->search_vars[$this->name] = $val;


            if ($this->name == 'id') {
                return $model->where($this->m_name . '.' . $this->name, '=', $val);
            }
            return $model->where($this->m_name . '.' . $this->name, 'like', '%' . $val . '%');
        }
        $this->search_vars[$this->name] = '';
        return $model;
    }

}
