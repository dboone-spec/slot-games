<?php

class Vidget_Jpset extends Vidget_Input{




    function _item($model) {
        $k=$model->__get($this->name);
        if (!($k>0)){
            $k=1;
        }

        $txt="To change jackpot's set, change the lowest jackpot's value. Default:20";

        $params=array('class' => "field text medium", 'maxlength' >= "7", 'id'=>'jpset');
        if(!($this->param['can_edit']??true)) {
            $params=['disabled'=>'disabled'];
        }
        $txt.=form::input($this->name($model), $k*20, $params);

        $a=number_format($k*20,2);
        $b=number_format($k*50,2);
        $c=number_format($k*100,2);
        $d=number_format($k*250,2);

        $txt.="<span id='jpsetTxt'><b>$a</b> $b $c $d<span>";

        $mult=$model->currency->mult ?? 2;

        $txt.=<<<TXT

<script>
    let mult={$mult};
    $( document ).ready(function() {
        $('#jpset').bind('keyup change', function(e) {
                let k=$('#jpset').val();
                k=k/20;
                let a=k*20;
                let b=k*50;
                let c=k*100;
                let d=k*250;
                $('#jpsetTxt').html('<b>'+a.toString()+'</b> '+b.toString()+' '+c.toString()+' '+d.toString());




            });


    });


</script>


TXT;



        return $txt;
    }


    function handler_save($data,$old_data,$model){

        if(!arr::get($this->param,'can_edit',false)) {
            return $model;
        }

        if ($data[$this->name]>0){
            $data[$this->name]=$data[$this->name]/20;
            return parent::handler_save($data, $old_data, $model);
        }
        return $model;
    }


}
