<?php

class Vidget_Persent extends Vidget_Echo
{

    public function _item($model)
    {

        $reverse = arr::get($this->param,'all',false);

        $all = 0;
        foreach($this->param['all'] as $v)
        {
            $all += $model->__get($v);
        }

        if($reverse)
        {
            if($model->__get($this->name) == 0)
            {
                return '∞';
            }

            return number_format($all * 100 / $model->__get($this->name),2) . ' %';
        }

        if($all == 0)
        {
            return '∞';
        }
        return number_format($model->__get($this->name) * 100 / $all,2) . ' %';
    }

    public function _list($model)
    {
        $reverse = arr::get($this->param,'all',false);

        $all = 0;
        foreach($this->param['all'] as $v)
        {
            $all += $model->__get($v);
        }

        if($reverse)
        {
            if($model->__get($this->name) == 0)
            {
                return '∞';
            }

            return number_format($all * 100 / $model->__get($this->name),2) . ' %';
        }

        if($all == 0)
        {
            return '∞';
        }
        return number_format($model->__get($this->name) * 100 / $all,2) . ' %';
    }

}
