<?php

class Vidget_Amount_Office extends Vidget_Echo {

     function _item($model) {
        $s=(string) $model->__get($this->name);
        $s=HTML::chars($s);
        
        $replenish=__('Replenish');
        $takeOff=__('Take off');

        if($model->loaded()) {

            if(in_array(Person::$role,['sa','client'])){
                $s.=<<<TEXT
<br> <input type="input" name="amountForOffice" id="amountForOffice" />

<a id="amountReplenish" class="btn btn-default btn-sm" href="/enter/office/balance/{$model->id}">$replenish</a>
<a id="amountTakeoff" class="btn btn-default btn-sm" href="/enter/office/balance/{$model->id}">$takeOff</a>



<script>
 $(document).ready(function() {
        $('#amountForOffice').change(function(){
            $('#amountReplenish').attr('href','/enter/office/balance/{$model->id}?amount='+$(this).val()+'&mode=replenish');
            $('#amountTakeoff').attr('href','/enter/office/balance/{$model->id}?amount='+$(this).val()+'&mode=takeoff');
        })

   });

</script>
TEXT;


            }
            else{
                $s.=<<<TEXT

<br> <input type="input" name="amountForOffice" id="amountForOffice" />

<a id="amountReplenish" class="btn btn-default btn-sm" href="/enter/office/balance/{$model->id}">$replenish</a>




<script>
 $(document).ready(function() {
        $('#amountForOffice').change(function(){
            $('#amountReplenish').attr('href','/enter/office/balance/{$model->id}?amount='+$(this).val()+'&mode=replenish');
        })

   });

</script>

TEXT;

            }

        }
        return $s;
    }



}
