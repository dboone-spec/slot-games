<?php

class Vidget_Selectmask extends Vidget_Select
{

    function _list($model)
    {
        $id = $this->name . '_' . $model->id;

        $url = str_replace('_','',$model->object_name());

        $key = $model->primary_key();

        $dir = 'enter';
        if(defined('ADMINR')) {
            $dir = ADMINR;
        }

        $url = "/" . $dir . "/$url/input/{$model->$key}";
        $js  = <<<JS
        <script>
            $('#b$id').click(function(event){
                event.preventDefault();
                event.stopPropagation();

                var input = $('#$id');

                var v = input.val();
                var result = 1 || confirm('Продолжить?');
                if(result){
                    $.ajax({
                        method: 'post',
                        url: '$url',
                        data: {
                            field: '{$this->name}',
                            value: v
                    },
                        dataType: 'json',
                        success: function(response) {
                            if(response.error==1) {
                                alert('Ошибка при сохранении');
                            }else {
                                alert('Изменение успешно сохранено');
                            }
                        }
                    });
                }
                if(!result){
                    $('#$id').val({$model->__get($this->name)});
                    alert('Изменение отменено');
                }

            });
        </script>
JS;

        $selected = [];
        $val = $model->__get($this->name);
        foreach($this->param['fields'] as $i=>$name) {
            if(th::getBit($val, $i)) {
                $selected[]=$i;
            }
        }

        $select = form::select($this->name, $this->param['fields'],$selected,array('class'=>"field text medium",'id'=>$id,'multiple'=>'multiple'));

        return $select . "&nbsp;<button type='button' id='b{$id}'>".__('Сохранить')."</button>" . $js;
    }

}
