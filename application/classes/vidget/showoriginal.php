<?php

class Vidget_Showoriginal extends Vidget_Echo
{
    public $ya;
    public function __construct($name,$model)
    {
        parent::__construct($name,$model);

        $this->ya = new Api_Yandex_Translate();
    }

    function _item($model)
    {
        //$model = Model_Sharelangs
        //$this->name = 'share_id'
        //$this = Vidget_Showoriginal

        $transl   = $model->__get($this->name);
        $id       = $model->__get('share_id');
        $m        = ORM::factory('share',$id);
        $original = $m->__get($this->name);

        $type = $this->param['type'] ?? '';

        $html = '';

        switch($type)
        {
            case $type == 'editor':
                if(empty($transl)) {
                    $transl = $this->ya->yandexTranslate('ru', 'en', $original);
                }
                $html = '<link href="/admin/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" /><link href="/admin/css/froala_style.min.css" rel="stylesheet" type="text/css" />'
                        . '<textarea id="editor' . $this->name . '">' . $transl . '</textarea><input id="hideditor' . $this->name . '" name="' . $this->name . '" type="hidden" value="">' . '<div class="orig" style="padding: 5px 10px; border: 1px solid #e2e2e2;">' . $original . '</div>';
                $js   = <<<JS
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
                break;
            case $type == 'rules':


                if($original !== NULL)
                {
                    $html = '<div style="margin-bottom: 20px;">';
                    foreach($original as $value)
                    {
                        $html .= "<div style='border: 1px solid #e2e2e2;'>{$value}</div>";
                    }
                    $html .= '</div>';
                }
                $js = <<<JS
            <script>
                $('#add_new').click(function() {
                    $(this).parent().append('<div><input name="{$this->name}[]"><div style="border-radius: 5px; border: 1px solid #eac2c2;padding: 3px;cursor:pointer;display: inline-block;margin-left: 10px;" onclick="$(this).parent().remove();">Удалить</div></div>');
                });
            </script>
JS;

                $html .= '<div id="add_new" style="margin-bottom:5px;width:200px;cursor:pointer;border-radius: 5px; border: 1px solid #bee6be;padding: 3px;">Добавить правило в перевод</div>' . $js;

                if($transl !== NULL && count($transl))
                {
                    foreach($transl as $value)
                    {
                        $html .= "<div><input size=\"150\" name='{$this->name}[]' value='{$value}'><div style='border-radius: 5px; border: 1px solid #eac2c2;padding: 3px; cursor:pointer;display: inline-block;margin-left: 10px;' onclick='$(this).parent().remove();'>Удалить</div></div>";
                    }
                }
                else {
                    if($original !== NULL)
                    {
                        foreach($original as $value)
                        {
                            $html .= "<div><input size=\"150\" name='{$this->name}[]' value='{$this->ya->yandexTranslate('ru', 'en', $value)}'><div style='border-radius: 5px; border: 1px solid #eac2c2;padding: 3px; cursor:pointer;display: inline-block;margin-left: 10px;' onclick='$(this).parent().remove();'>Удалить</div></div>";
                        }
                    }
                }
                break;

            case $type == 'image':
                $html = '';
                $js   = '';

                $text = '';
                if($transl)
                {
                    $link = $_SERVER['HTTP_HOST'] . $transl;
                    $text .= "<span>Текущая картинка <a href='//$link' target='_blank'>просмотр</a></span>";
                }
                if($original)
                {
                    $olink = $_SERVER['HTTP_HOST'] . $original;
                    $text  .= "<span>Картинка оригинала <a href='//$olink' target='_blank'>просмотр</a></span>";
                }

                $html = form::input($this->name($model),null,array('type' => 'file','class' => "field text medium",'maxlength' >= "255","accept" => "image/*")) . $text;

                break;

            default :
                if(empty($transl)) {
                    $transl = $this->ya->yandexTranslate('ru', 'en', $original);
                }
                $html = form::input($this->name,$transl,array('class' => "field text medium",'maxlength' >= "255")) . '<span class="orig" style="margin-left: 10px; padding: 5px 10px; border: 1px solid #e2e2e2;">' . $original . '</span>';
                $js   = '';
        }
        return $js . $html;
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

//    function handler_save($data,$old_data,$model)
//    {
////        kohana::$log->add(LOG::NOTICE,debug::vars($this->name));
//        if(isset($_FILES[$this->name])){
//            $file = $_FILES[$this->name];
//        }
//
//        if(isset($file) AND $file['size'] > 0)
//        {
//
//            $folder = $this->param['folder'];
//            $image  = $folder . time() . $file['name'];
//            $name   = DOCROOT . $image;
//
//            if(move_uploaded_file($file['tmp_name'],$name))
//            {
//                $model->set($this->name,$image);
//            }
//        }
//
//        if(isset($data[$this->name]))
//        {
//
//            $model->set($this->name,$data[$this->name]);
//            return $model;
//        }
//
//        $model->set($this->name,[]);
//        return $model;
//    }

}
