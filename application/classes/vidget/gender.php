<?php

class vidget_gender extends Vidget_Echo
{

    protected function ret_g($g)
    {
        switch($g)
        {
            case '0':
                return 'Ж';
            case '1':
                return 'М';
            default: '?';
        }
        return '?';
    }

    function _list($model)
    {
        return $this->ret_g($model->__get($this->name));
    }

    function _item($model)
    {
        return $this->ret_g($model->__get($this->name));
    }

}
