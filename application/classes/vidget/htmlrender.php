<?php

class Vidget_HTMLrender extends Vidget_Echo
{

    function _list($model)
    {
        $html = base64_encode($model->__get($this->name));

        $js = <<<JS
                <script>
                function render_html{$model->__get('id')}() {
                    var w = window.open('', '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=800,width=800');
                    var html = decodeURIComponent(escape(atob('{$html}')));
                    $(w.document.body).html(html);
                }
                </script>
JS;
        return '<a href="javascript:render_html'.$model->__get('id').'()">Показать</a>'.$js;
    }

    function handler_save($data,$old_data,$model)
    {
        return $model;
    }

}
