<?php

    class Vidget_terminalstatus extends Vidget_Select
    {

        public function __construct($name,$model)
        {
            $c = parent::__construct($name,$model);
            $this->param('fields',[
                '' => __('All'),
                -1 => __('New'),
                0 => __('Active'),
                1 => __('Block'),
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
