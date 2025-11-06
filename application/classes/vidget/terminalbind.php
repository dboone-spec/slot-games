<?php

    class Vidget_Terminalbind extends Vidget_Echo
    {
        
        public function _list($model)
        {
            if(!$model->__get('office_id')) {
                return '<a id="enter_name_before" href="/enter/terminal/bind/'.$model->__get($this->name).'">'.__('Привязать').'</a>';
            }
            return __('Привязан');
        }
        
    }
    