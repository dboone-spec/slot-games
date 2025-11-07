<?php
class Vidget_Related extends Vidget{
/**
 * Параметры
 * related - связь, должна быть прописана в _belongs_to модели
 * name - имя столбца для вывода
 * empty - true если поле не обязательно
 */

function _list($model){
	$r=$model->__get($this->param['related']);

        
        //TODO не забудь это выпилить
        if(isset($r->parent_id)) {
            $r=new Model_User($r->parent_id);
        }
	return $r->__get($this->param['name']) ;
}

function _item($model){
	$class=get_class($model->__get($this->param['related']));
	$class=explode('_',$class);
	$m=ORM::factory($class[1]);
	$sel=array();

	if (arr::get($this->param,'empty',false)){
		$sel[0]='';
	}

	foreach ($m->find_all() as $c){
		$sel[$c->pk()]=$c->__get($this->param['name']);
	}

	$value=$model->__get($this->name);

	if (!$model->loaded() and is_null($value) ){
		$value=arr::get($this->param,'default');
	}

	return form::select($this->name($model),$sel,$value);
}

function _search($vars){

	$class=get_class($this->model->__get($this->param['related']));
	$class=explode('_',$class);
	$m=ORM::factory($class[1]);
	$sel=array();
	$sel[0]=__('Все');

    $where = arr::get($this->param,'where');
    if($where) {
        foreach($where as $condition) {
            $m->where($condition['name'],$condition['op'],$condition['val']);
        }
    }

	foreach ($m->find_all() as $c){
		$sel[$c->pk()]=$c->__get($this->param['name']);
	}

    if($this->param['name']=='msrc' || $this->param['name']=='visible_name') {
        unset($sel[0]);
        asort($sel);
        array_unshift($sel,__('Все'));
    }

	return form::select($this->name,$sel,$vars[$this->name]);

}


function handler_search($model,$vars){

	if (isset($vars[$this->name]) and $vars[$this->name]>0){
		$this->search_vars[$this->name]=$vars[$this->name];
		return $model->where($this->name,'=',$vars[$this->name]);
	}
	$this->search_vars[$this->name]='';
	return $model;

}



}
