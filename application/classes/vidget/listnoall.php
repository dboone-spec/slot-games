<?php

class Vidget_Listnoall extends Vidget_Input
{



    function _search($vars)
    {

        $params = [];
        if ($this->name == 'office_id') {
            $params = ['class' => 'select2','required'=>'required','style'=>'min-width: 0; width: 100%;'];
        }
        $list =  [''=>'--']+$this->param['list'];
        return form::select($this->name, $list, $vars[$this->name], $params);

    }



}
