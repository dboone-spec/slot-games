<?php
class Vidget_Relatedtable extends Vidget_Input{
/**
 * Параметры
 * related - related модель
 * name - имя столбца для вывода
 * fkey - столбец в текущей модели
  */


    

function _search($vars) {
    
    
    return form::input($this->name, $vars[$this->name] ?? '');
}

function handler_search($model,$vars){

    
    
    
	if (isset($vars[$this->name]) and !empty($vars[$this->name])){
		
            $this->search_vars[$this->name]=$vars[$this->name];
            
            $r=ORM::factory($this->param['related'],[$this->param['name']=>$vars[$this->name] ]);

            return $model->where($this->param['fkey'], '=', $r->pk() );
	}

	return $model;

}



}
