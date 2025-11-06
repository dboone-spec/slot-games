<?php

class Vidget_fsprocessstatus extends Vidget_Echo
{

    function _list($model)
    {
        return $model->getStatusText();
    }

}
