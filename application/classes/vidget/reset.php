<?php

class Vidget_Reset extends Vidget_Echo
{

    function _list($model)
    {
        if($model instanceof Model_Status AND $model->id == 'autopay') {
            return ' - ';
        }
        
		$value= $this->param['v'][0];
        $text = '';
        $js='';
        if($model->__get($this->name))
        {
            $key = $model->primary_key();
            $url = str_replace('_','',$model->object_name());
            $url = "/" . ADMINR . "/$url/reset/{$model->$key}";
            $b_id=$model->$key;
            $js = <<<JS
        <script>
            $('#reset$b_id').click(function(event){
                event.stopPropagation();
                $.ajax({
                    method: 'post',
                    url: '$url',
                    data: {
                        field: '{$value}',
                        value: 0,
                    },
                    success: function(response) {
                        if(response.error==1) {
                            alert('Ошибка при сохранении');
                        } else {
                            alert('Изменение успешно сохранено');
                        }
                    }
                });
            });
        </script>
JS;
            if(time() - $model->__get($this->name) > 10*60 && $model->__get($value) > 0)
            {
                $text = '<br><div style="padding: 10px 0; color: #FF0000;">Завис</div><button id="reset'.$b_id.'">Сбросить</button>';
            }
            else
            {
                $text = '<br><div style="padding: 10px 0; color: #00FF00;">Работает</div>';
            }
        }
        return !is_null($model->__get($this->name)) ? date($format='d.m.y H:i:s',$model->__get($this->name)).$text.$js: '';
    }

}
