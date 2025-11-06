<?php

class Vidget_Nlstatus extends Vidget_Echo
{

    function _list($model)
    {
        $a=$model->__get($this->name);
        if($a=='newsletter'){
            $a='Статус почтовой рассылки';
        } elseif($a=='autopay') {
            $a='Автовыплата';
        }
        return $a;
    }

}
