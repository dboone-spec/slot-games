<?php

class Vidget_AmountOperation extends Vidget_Input
{
    public function _list($model) {
        $amount = parent::_list($model);
        if($amount<0) {
            $amount = '<span style="color:red">'.th::number_format($amount).'</span>';
        }
        else {
            $amount = '<span style="color:green">'.th::number_format($amount).'</span>';
        }
        return $amount;
    }
}
