<?php

class Vidget_ClearCounter extends Vidget {

    function _list($model) {

        $html = Form::button('clear['.$model->__get($this->name).']', 'Сбросить', array('type' => 'button','counter_id'=>$model->__get($this->name),'id'=>'clear'.$model->__get($this->name)));
        $html.= '<script>';
        $ajax = <<<AJAX
                $.ajax({
                    url: 'countergame/clear/'+counter_id,
                    type: 'POST',
                    success: function() {
                        window.location.reload();
                    }
                })
AJAX;
        $html.= '$(\'#clear'.$model->__get($this->name).'\').click(function() { var counter_id=$(this).attr(\'counter_id\'); '.$ajax.' })';
        $html.= '</script>';
        return $html;
    }

    function _item($model) {
        return;
    }

    public function _search($vars)
    {
        return;
    }

}
