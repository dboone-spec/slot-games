<?php

class Vidget_Amount_Person extends Vidget_Echo {
    
     function _item($model) {
        $s=(string) $model->__get($this->name);
        $s=HTML::chars($s);
        
        if($model->loaded() and in_array($model->role,['gameman','client','sa'])) {

            if(in_array(Person::$role,['sa','client'])){
                $s.=<<<TEXT
<br> <input type="input" name="amountForOffice" id="amountForOffice" />

<a id="amountReplenish" class="btn btn-default btn-sm" href="/enter/person/balance/{$model->id}">Replenish</a>
<a id="amountTakeoff" class="btn btn-default btn-sm" href="/enter/person/balance/{$model->id}">Take off</a>


                    
<script>
 $(document).ready(function() {
        $('#amountForOffice').change(function(){
            $('#amountReplenish').attr('href','/enter/person/balance/{$model->id}?amount='+$(this).val()+'&mode=replenish');
            $('#amountTakeoff').attr('href','/enter/person/balance/{$model->id}?amount='+$(this).val()+'&mode=takeoff');
        })
    
   });

</script>
TEXT;
                
                
            }
            else{
                $s.=<<<TEXT
                         
<br> <input type="input" name="amountForOffice" id="amountForOffice" />

<a id="amountReplenish" class="btn btn-default btn-sm" href="/enter/person/balance/{$model->id}">Replenish</a>



                    
<script>
 $(document).ready(function() {
        $('#amountForOffice').change(function(){
            $('#amountReplenish').attr('href','/enter/person/balance/{$model->id}?amount='+$(this).val()+'&mode=replenish');
        })
    
   });

</script>                         
                         
TEXT;
                
            }
        
        }
        return $s;
    }
    
    
    
}
