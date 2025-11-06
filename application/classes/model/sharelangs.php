<?php

class Model_Sharelangs extends ORM {

	protected $_table_name = 'share_langs';
    protected $_serialize_columns = ['rules'];

    protected $_belongs_to = [
		'share' => [
			'model'		 => 'share',
			'foreign_key'	 => 'share_id',
		],
	];
    
    public function labels() {
        return [
            'share_id' => 'Для какой акции',
            'name' => 'Название',
            'lang' => 'Язык перевода',
            'rules' => 'Правила',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'description' => 'Описание',
            'image' => 'Картинка (в акциях) 284*211',
            'prize' => 'Главный приз',
            'slider_img' => 'Картинка (в слайдер) 1000*275',
            'email_img' => 'Картинка (в письмо) 800*500',
            'email_text' => 'Текст письма',
            'slider_title' => 'Заголовок в слайдере',
            'slider_text' => 'Текст в слайдере',
            'subject' => 'Тема письма',
        ];
    }

}

