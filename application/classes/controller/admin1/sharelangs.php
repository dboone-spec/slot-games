<?php

class Controller_Admin_Sharelangs extends Super
{

    public $mark       = 'Переводы акций'; //имя
    public $model_name = 'sharelangs'; //имя модели
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->search = [
            'name',
        ];
        $this->list = ['id','share_id','name','title'];

        $name = new Vidget_Showoriginal('name',$this->model);
        $name->param('type','input');
		$this->vidgets['name'] = $name;

        $title = new Vidget_Showoriginal('title',$this->model);
        $title->param('type','input');
		$this->vidgets['title'] = $title;

        $text = new Vidget_Showoriginal('text',$this->model);
        $text->param('type','input');
		$this->vidgets['text'] = $text;

        $prize = new Vidget_Showoriginal('prize',$this->model);
        $prize->param('type','input');
		$this->vidgets['prize'] = $prize;

        $slider_title = new Vidget_Showoriginal('slider_title',$this->model);
        $slider_title->param('type','input');
		$this->vidgets['slider_title'] = $slider_title;

        $slider_text = new Vidget_Showoriginal('slider_text',$this->model);
        $slider_text->param('type','input');
		$this->vidgets['slider_text'] = $slider_text;

        $rules = new Vidget_Showoriginal('rules',$this->model);
        $rules->param('type','rules');
		$this->vidgets['rules'] = $rules;

        $description = new Vidget_Showoriginal('description',$this->model);
        $description->param('type','editor');
		$this->vidgets['description'] = $description;

        $email_text = new Vidget_Showoriginal('email_text',$this->model);
        $email_text->param('type','editor');
		$this->vidgets['email_text'] = $email_text;

        $subject = new Vidget_Showoriginal('subject',$this->model);
        $subject->param('type','input');
		$this->vidgets['subject'] = $subject;

        $image = new Vidget_Image('image',$this->model);
        $image->param('folder', '/uploads/shares/');
//        $image->param('type','image');
        $this->vidgets['image'] = $image;

        $slider_img = new Vidget_Image('slider_img',$this->model);
        $slider_img->param('folder', '/uploads/shares/');
//        $slider_img->param('type','image');
        $this->vidgets['slider_img'] = $slider_img;

        $email_img = new Vidget_Image('email_img',$this->model);
        $email_img->param('folder', '/uploads/shares/');
//        $email_img->param('type','image');
        $this->vidgets['email_img'] = $email_img;

        $lang = new Vidget_Lang('lang',$this->model);
		$this->vidgets['lang'] = $lang;

        $share = new Vidget_Related('share_id',$this->model);
		$share->param('related','share');
		$share->param('name','name');
		$this->vidgets['share_id'] = $share;

    }

}
