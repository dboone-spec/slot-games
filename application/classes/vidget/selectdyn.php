<?php

class Vidget_Selectdyn extends Vidget_Select
{

    public function _search($vars)
    {
        $f = arr::get($this->param,'searchfields');
        if($f) {
            return form::select($this->name, $f,$vars[$this->name],array('class'=>"field text medium"));
        }
        return parent::_search($vars);
    }

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
                                window.location = window.location;
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

        $select = form::select($this->name, $this->param['fields'],$model->__get($this->name),array('class'=>"field text medium",'id'=>$id));

        return $select . "&nbsp;<button type='button' id='b{$id}'>".__('Сохранить')."</button>" . $js;
    }

}
