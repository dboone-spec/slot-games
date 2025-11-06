<?php

class Vidget_Sharerules extends Vidget_Echo
{

    private function print_json($a = [])
    {
        $s = '';
        foreach($a as $k => $v)
        {
            if(!is_array($v))
            {
                $s .= $k . ': ' . $v . '<br />';
            }
            else
            {
                $s .= $this->print_json($v);
            }
        }
        return $s;
    }

    function handler_save($data,$old_data,$model)
    {
        if(isset($data[$this->name]))
        {
            $model->set($this->name,$data[$this->name]);
            return $model;
        }
        $model->set($this->name,[]);
        return $model;
    }

    public function _list($model)
    {
        if($model->__get($this->name) !== NULL)
        {
            return $this->print_json($model->__get($this->name));
        }
        else
        {
            return '';
        }
    }

    function _item($model)
    {
        $js   = <<<JS
            <script>
                $('#add_new').click(function() {
                    $(this).parent().append('<div><input name="{$this->name}[]"><div style="cursor:pointer;display: inline-block;margin-left: 10px;" onclick="$(this).parent().remove();">Удалить</div></div>');
                });
            </script>
JS;
        $form = '<div id="add_new" style="margin-bottom:5px;width:80px;cursor:pointer;">Добавить</div>' . $js;

        if($model->__get($this->name) !== NULL)
        {
            foreach($model->__get($this->name) as $value)
            {
                $form .= "<div><input name='{$this->name}[]' value='{$value}'><div style='cursor:pointer;display: inline-block;margin-left: 10px;' onclick='$(this).parent().remove();'>Удалить</div></div>";
            }
            return $form;
        }
        else
        {
            return $form;
        }
    }

}
