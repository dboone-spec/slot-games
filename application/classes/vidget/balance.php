<?php

class Vidget_Balance extends Vidget_Input
{
    public function _list($model) {
        return th::number_format($model->__get($this->name));
    }
}
