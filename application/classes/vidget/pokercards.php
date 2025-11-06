<?php

class vidget_pokercards extends Vidget_Input {

     public function _list($model)
     {
         $cards = $model->__get($this->name);
         return '<span style="font-family: sans-serif;">'.card::print_card($cards).'</span>';
     }

}
