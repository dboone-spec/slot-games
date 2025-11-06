<?php

class vidget_number extends Vidget_Input {

     public function _item($model)
     {

         $val = !is_null($model->__get($this->name))?$model->__get($this->name):arr::get($this->param,'default');

         $c = arr::get($this->param,'c',1);

         $val = $val/$c;

         if(arr::get($this->param,'onlyshow',false)) {
             return HTML::chars($val);
         }

         $txt = form::input($this->name($model), $val, array(
                 'class' => "field text medium",
                 'maxlength' >= "255",
                 'type'=>'number',
                 'min'=>arr::get($this->param,'min',1),
                 'max'=>arr::get($this->param,'max',100),
                ));

         $txt.=$this->param['text'] ?? '';

         return $txt;
     }

     public function handler_save($data,$old_data,$model)
     {
         if(arr::get($this->param,'onlyshow',false)) {
             return $model;
         }
         $c = arr::get($this->param,'c',1);

         $min=arr::get($this->param,'min');
         if($min && $data[$this->name]<$min) {
             return $model;
         }

         $max=arr::get($this->param,'max');
         if($max && $data[$this->name]>$max) {
             return $model;
         }

         $model->set($this->name,$data[$this->name]*$c);
         return $model;

     }

}
