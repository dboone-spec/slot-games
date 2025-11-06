<?php

class Vidget_BalanceWithAmount extends Vidget_Input
{
    public function _list($model) {
        return th::number_format($model->__get($this->name)+$model->win-$model->amount);
    }
}
