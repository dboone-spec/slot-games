<?php

    class Vidget_fsstatus extends Vidget_Select
    {

        public function __construct($name,$model)
        {
            $c = parent::__construct($name,$model);
            $this->param('fields',[
                '' => __('All'),
                -2 => __('Declined (Auto)'),
                -1 => __('Declined'),
                0 => __('New'),
                1 => __('Accepted'),
            ]);
            return $c;
        }

        public function _item($model)
        {
            $current_value = $model->__get($this->name);
            unset($this->param['fields']['']);
            unset($this->param['fields'][-1]);
            if($current_value==1) {
                //unset($this->param['fields'][0]) ??need??
            }

            return parent::_item($model);
        }

    }
