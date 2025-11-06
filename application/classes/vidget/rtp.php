<?php

class Vidget_Rtp extends Vidget_List{

    function handler_save($data,$old_data,$model){

        if($data[$this->name]==$old_data[$this->name]){
            return $model;
        }


        $model->clearcounters();

        $model->set($this->name,$data[$this->name]);
        return $model;
    }

}

