<?php

class Vidget_Image extends Vidget_Echo
{

    public function _list($model)
    {
        $link = parent::_list($model);
        $r = '<img style="border: 1px solid;" src="'.$link.'" />';
        return $r;
    }

    function _item($model){
        $text = '';
        if($name = $model->__get($this->name)) {
           $link = $_SERVER['HTTP_HOST'].$name;

           $text .= "<span>Текущая картинка <img style='border: 1px solid;' src='//$link'  /></span>";
        }

        return form::input($this->name($model),null,array('type' => 'file','class'=>"field text medium", 'maxlength'>="255", "accept"=>"image/*"))
                .form::input($this->name($model),null,array('type' => 'url','class'=>"field text medium", 'maxlength'>="255"))
                .$text;
    }

	function handler_save($data,$old_data,$model){
        $file = $_FILES[$this->name];



        if(isset($file) AND $file['size'] > 0) {
            $folder = $this->param['folder'];
            $image = $folder.time().$file['name'];
            $name = DOCROOT . $image;

            if (move_uploaded_file($file['tmp_name'], $name)) {
                $model->set($this->name, $image);
            }
        }
        else if(!empty($data['brand']) && $data[$this->name] && strpos($data[$this->name],'http')===0) {
            $file = file_get_contents($data[$this->name]);
            if(!$file) {
                return $model;
            }
            $ex = explode('/',$data[$this->name]);
            $folder = $this->param['folder'];
            $folder = str_replace('<brand>',$data['brand'],$folder);
            $name = DOCROOT . $folder. DIRECTORY_SEPARATOR . $ex[count($ex)-1];
            file_put_contents($name,$file);
            $model->set($this->name, '/'. str_replace(DIRECTORY_SEPARATOR,'/',$folder. DIRECTORY_SEPARATOR . $ex[count($ex)-1]));
        }

        return $model;
    }
}
