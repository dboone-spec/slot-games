<?php


class Vidget_Lang extends Vidget_Echo
{

    protected $_lang_list = [];

    public function __construct($name,$model)
    {
        $this->_lang_list = Kohana::$config->load('languages.lang');
        parent::__construct($name,$model);
    }

	public function _item($model)
	{
        return form::select($this->name, $this->_lang_list, $model->__get($this->name), []);
	}

    public function handler_save($data,$old_data,$model)
    {
        if(isset($data[$this->name])) {
            $model->set($this->name, $data[$this->name]);
        }

        return $model;
    }
}
