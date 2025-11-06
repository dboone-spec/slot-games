<?php

class Vidget_Isfreespin extends Vidget_Echo
{

    function _list($model)
    {
        $a=$model->__get($this->name);
        if($a=='1'){
            $a='FSback';
        } elseif($a=='2') {
            $a='API';
        }
        return $a;
    }

}
