<?php

class Vidget_Currency extends Vidget_List
{

    function _item($model){


        $parent_item=parent::_item($model);

        $cur_jp_list = json_encode(arr::get($this->param,'jpk',[]));

        $script = <<<JS
            <script>
            
                let cur_jp_list=JSON.parse('$cur_jp_list');
            
                document.getElementsByName('currency_id')[0].onchange=function(e) { 
                    
                    let newval=parseFloat(cur_jp_list[e.target.value]);
                    
                    if(newval<=0 || isNaN(newval)) {
                        newval=1;
                    }
                    
                    newval*=20;
                    
                    document.getElementsByName('k_max_lvl')[0].value=newval;
                    document.getElementsByName('k_max_lvl')[0].dispatchEvent(new Event('change'));
                }
            </script>
JS;

        return $parent_item.$script;

        $can_edit=arr::get($this->param,'can_edit',true);

        $params=[];
        if(!$can_edit) {
            $params=['disabled'=>'disabled'];
        }

        return form::select($this->name($model),$this->param['list'],$model->__get($this->name),$params);
    }

}
