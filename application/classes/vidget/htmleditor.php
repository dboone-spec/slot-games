<?php

class Vidget_Htmleditor extends Vidget_Echo
{

    function _item($model)
    {
        $text = $model->__get($this->name);
        $html = '<link href="/admin/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" /><link href="/admin/css/froala_style.min.css" rel="stylesheet" type="text/css" />'
                . '<textarea id="editor'.$this->name.'">'.$text.'</textarea><input id="hideditor'.$this->name.'" name="'.$this->name.'" type="hidden" value="">';

        $js = <<<JS
                <script type="text/javascript" src="/admin/js/froala_editor.min.js"></script>
                <script type="text/javascript" src="/admin/js/froala_editor.pkgd.min.js"></script>
                <script> 
                $(function() { 
                    $('#editor{$this->name}').froalaEditor({
                        iframe: true,
                        language: 'ru'
                });
                codes=$('#editor{$this->name}').froalaEditor('html.get', true);
                $('#hideditor{$this->name}').val(codes);
                $('#editor{$this->name}').on('froalaEditor.contentChanged', function (e, editor) {
                    codes=$('#editor{$this->name}').froalaEditor('html.get', true);
                    $('#hideditor{$this->name}').val(codes);
                    })
                }); 
                </script>
JS;
        return $html.$js;
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

}
