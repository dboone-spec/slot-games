<?php

class vidget_onlyme extends Vidget_CheckBox
{
    function _search($vars){
        return form::checkbox($this->name,Person::$user_id,$vars[$this->name]==Person::$user_id);
    }
}
