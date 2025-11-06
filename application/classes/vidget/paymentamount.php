<?php

class Vidget_Paymentamount extends Vidget_Echo
{

    //work with amount field only

    protected $_params_list = [
            -1=>'Все',
            1=>'Оплата',
            2=>'Выплата',
    ];

    function _item($model){

        return form::select($this->name($model), $this->_params_list,$model->__get($this->name),array('class'=>"field text medium"));
    }

    public function handler_search($model,$vars)
    {
        if (isset($vars[$this->name]) and $vars[$this->name] != '' and intval($vars[$this->name])>0){
            $this->search_vars[$this->name]=$vars[$this->name];
            $mod = intval($vars[$this->name])==1?'>':'<';
            return $model->where('amount',$mod,0);
        }
        return $model;
    }

    function _search($vars) {
        if(!isset($vars[$this->name])) {
            $vars[$this->name]=-1;
        }
        return form::select($this->name, $this->_params_list,$vars[$this->name],array('class'=>"field text medium"));
    }
}
