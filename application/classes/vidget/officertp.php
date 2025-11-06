<?php

class Vidget_Officertp extends Vidget_Selectdyn
{

    function _list($model)
    {
        $id = $this->name . '_' . $model->id;

        $url = str_replace('_','',$model->object_name());

        $key = $model->primary_key();

        $dir = 'enter';
        if(defined('ADMINR'))
        {
            $dir = ADMINR;
        }

        $url = "/" . $dir . "/$url/input/{$model->$key}";

        $o_id  = $model->__get($model->primary_key());
        $value = Status::instance($o_id)->rtp;

        $js = <<<JS
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
                                window.location = window.location;
                            }
                        }
                    });
                }
                if(!result){
                    $('#$id').val({$value});
                    alert('Изменение отменено');
                }

            });
        </script>
JS;

        $select = form::select($this->name,$this->param['fields'],$value,array('class' => "field text medium",'id' => $id));

        return $select . "&nbsp;<button type='button' id='b{$id}'>".__('Сохранить')."</button>" . $js;
    }

}
