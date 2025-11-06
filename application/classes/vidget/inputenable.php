<?php

class Vidget_Inputenable extends Vidget_Input
{

    function _item($model)
    {
        $id=$this->name($model);
        $idCh=$this->name($model).'_checkbox';
        $str=form::input($this->name($model), $model->__get($this->name), [ 'class' => "field text medium", 'maxlength' >= "255",'disabled'=>'disabled', 'id'=>$id]);
        $str.=' '.form::checkbox($idCh, 'on', false,['id'=>$idCh]);
        $str.=' <label for="'.$idCh.'">edit</label>';
        $str.=<<<ACC
	<script>
		$(function() {
                    $('#$idCh').change(function(){
                        if ($(this).is(':checked')){
                            $('#$id').removeAttr('disabled');
                        }
                        else{
                            $('#$id').attr('disabled','disabled');
                        }
                    
                    })


		})
        </script>
ACC
;

        
        
        return $str;
    }

    function handler_save($data,$old_data,$model)
    {
        if (isset($data[$this->name])){
            return parent::handler_save($data, $old_data, $model);
        }
        
        return $model;
    }

}


