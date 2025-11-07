<?php

class Vidget_Input extends Vidget {

    function _list($model) {
        $fields = ['bets_arr','external_id', 'id', 'created','payment_system_id', 'user_id','fingerprint','person_id','updated_id'];

        if(!in_array($this->name, $fields) AND is_numeric($model->__get($this->name))) {
            return th::number_format($model->__get($this->name));
        }

        $can_edit=arr::get($this->param,'can_edit',false);

        if($can_edit) {

            $url = str_replace('_','',$model->object_name());

            $key = $model->primary_key();

            $dir = 'enter';
            if(defined('ADMINR')) {
                $dir = ADMINR;
            }

            $url = "/" . $dir . "/$url/input/{$model->$key}";

            $vals = $model->__get($this->name);

            if($vals===null && !empty($this->param['default'])) {
                $vals = $this->param['default'];
            }

            $html = Form::input($this->name,$vals,['model_id'=>$key]);
            $html.=Form::button($this->name.'btn',__('Сохранить'),['class'=>'save_'.$this->name.'_'.$model->__get($key)]);

            $alert = __('Сохранено');

            $js = <<<JS
                    <script>
                    $('.save_{$this->name}_{$model->__get($key)}').click(function() {
                    console.log($('[name={$this->name}][model_id={$key}]').val());
                        $.ajax({
                            url: '{$url}',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                field: '{$this->name}',
                                value: $('[name={$this->name}][model_id={$key}]').val()
                            },
                            success: function() {
                                alert('$alert');
                            }
                        });
                    });
                    </script>
JS;

            return $html.$js;
        }
        return HTML::chars($model->__get($this->name));
    }

    function _item($model) {
        return form::input($this->name($model), $model->__get($this->name), array('class' => "field text medium", 'maxlength' >= "255"));
    }

    function _search($vars) {

        return form::input($this->name, $vars[$this->name]);
    }

    function handler_search($model, $vars) {
        if (isset($vars[$this->name]) and ! empty($vars[$this->name])) {
            $val = trim($vars[$this->name]);
            $this->search_vars[$this->name] = $val;

            if ($this->name == 'id' AND $model instanceof Model_User) {
                $u = new Model_User($val);
                if ($u->loaded() AND $u->parent_id) {
                    return $model->where($this->m_name . '.' . $this->name, '=', $u->parent_id)->or_where($this->m_name . '.' . $this->name, '=', $u->id);
                }
            }

            if ($this->name == 'id') {
                return $model->where($this->m_name . '.' . $this->name, '=', $val);
            }
            return $model->where($this->m_name . '.' . $this->name, 'like', '%' . $val . '%');
        }
        $this->search_vars[$this->name] = '';
        return $model;
    }

}
