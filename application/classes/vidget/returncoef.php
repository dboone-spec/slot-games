<?php

class Vidget_Returncoef extends Vidget
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
                var result = confirm('Счетчики будут сброшены. Продолжить?');
                if(result){
                    $.ajax({
                        method: 'post',
                        url: '$url',
                        data: {
                            field: '{$this->name}',
                            value: v,
                            og_id: '{$model->id}',
                        },
                        dataType: 'json',
                        success: function(response) {
                            if(response.error==1) {
                                alert('Ошибка при сохранении');
                            } else if(response.error==2){
                                alert('Изменение сохранено. Ошибка сброса счетчиков.');
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

        $conf = kohana::$config->load($model->game->brand."/".$model->game->name);
        $select="<select id='{$id}'>";
        $custom=false;

        foreach ($conf as $k=>$v) {
            //novomatic
            if(strpos($k, 'bars_') !== false) {
                $percent = str_replace('bars_', '', $k);
                $cur_coef=$model->__get($this->name)*100;
                $selected='';
                if($cur_coef==$percent){
                    $selected='selected';
                    $custom=true;
                }
                $select.='<option '.$selected.' value="'.($percent/100).'">'.$percent.'%</option>';
            }
        }
        //igrosoft
        if(isset($conf['sets'])) {
            foreach($conf['sets'] as $percent=>$vals) {
                $cur_coef=$model->__get($this->name)*100;
                $selected='';
                if($cur_coef==$percent){
                    $selected='selected';
                    $custom=true;
                }
                $select.='<option '.$selected.' value="'.($percent/100).'">'.$percent.'%</option>';
            }
        }

        $default=(!$custom)?'selected':'';
        $select.='<option '.$default.' value="">Default</option></select>';

        return $select."<button type='button' id='b{$id}'>".__('Сохранить')."</button>". $js;
    }

    function _item($model)
    {
    }

    function _search($vars)
    {
        return form::select($this->name,['our'=>'Наши','all'=>'Все'],$vars[$this->name]??'all');
    }

    function handler_search($model, $vars) {
        if (isset($vars[$this->name]) and ! empty($vars[$this->name])) {
            if($vars[$this->name] != 'all') {
                return $model->where('provider', '=', $vars[$this->name]);
            }
        }
        $this->search_vars[$this->name] = 'all';
        return $model;
    }

}
