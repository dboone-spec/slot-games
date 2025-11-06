<?php

    class Vidget_Jsondyn extends Vidget_Json
    {

        public function _list($model)
        {
            $url = str_replace('_','',$model->object_name());

            $key = $model->primary_key();

            $dir = 'enter';
            if(defined('ADMINR')) {
                $dir = ADMINR;
            }

            $url = "/" . $dir . "/$url/input/{$model->$key}";

            $del_text = __('Удалить');
            $save_text = __('Сохранено');

            $js = <<<JS
            <script>
                $('.add_new{$model->__get($key)}').click(function() {
                    $(this).parent().prepend('<div><input name="{$this->name}[{$model->__get($key)}][]"><div style="cursor:pointer;display: inline-block;margin-left: 10px;" onclick="$(this).parent().remove();">{$del_text}</div></div>');
                });
                $('.save_ips{$model->__get($key)}').click(function() {
                    var ips = []
                    $('[name="white_ips['+$(this).attr('office_id')+'][]"]').each(function() {
                        ips.push($(this).val())
                    });
                    $.ajax({
                        url: '{$url}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            field: '{$this->name}',
                            value: ips
                        },
                        success: function() {
                            alert('{$save_text}');
                        }
                    });
                });
            </script>
JS;
            $form = '';

            $can_edit=arr::get($this->param,'can_edit',true);

            $vals = $model->__get($this->name);

            if($vals===null && !empty($this->param['default'])) {
                $vals = $this->param['default'];
            }

            if ($vals !== NULL)
            {
                foreach ($vals as $value)
                {
                    if($can_edit) {
                            $form .= "<div><input name='{$this->name}[{$model->__get($key)}][]' value='{$value}'><div style='cursor:pointer;display: inline-block;margin-left: 10px;' onclick='$(this).parent().remove();'>{$del_text}</div></div>";
                    }
                    else {
                            $form .= "<div>{$value}</div>";
                    }
                }
            }

            $form .= '<div class="add_new'.$model->__get($key).'" style="margin-bottom:5px;width:80px;cursor:pointer;">'.__('Добавить').'</div>';
            $form .= '<button type="button" class="save_ips'.$model->__get($key).'" office_id="'.$model->__get($key).'">'.__('Сохранить').'</button>';
            return $form.$js;
        }

    }
