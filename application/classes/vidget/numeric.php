<?php

class Vidget_Numeric extends Vidget_Input {

    function _list($model) {
        $can_edit=arr::get($this->param,'can_edit',false);

        if(isset($model->office)) {
            $mult = $model->office->currency->mult??2;
        }
        else {
            $mult=$model->user->office->currency->mult??2;
        }
	if($mult==0) {
            return th::number_format($model->__get($this->name));
        }
        return th::float_format($model->__get($this->name),$mult);
    }

}
