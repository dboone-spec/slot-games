<?php

class Vidget_Betamount extends Vidget_Echo
{

    function _list($model)
    {
        if(!empty($model->external_id)) {
            return $model->pokerbets->amount;
        }
        return parent::_list($model);
    }


}
