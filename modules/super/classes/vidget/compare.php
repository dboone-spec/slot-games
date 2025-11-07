<?php
class Vidget_Compare extends Vidget{
/**
 * Параметры
 * related - связь, должна быть прописана в _belongs_to модели
 * name - имя столбца для вывода
 * empty - true если поле не обязательно
 */
	
function _list($model){
	
	//выводим текст для сравнения
	if (arr::get($this->param,'mode')=='compareto'){
		//если сравниваемое в другой табличке
		if (isset($this->param['related'])){
			$r=$model->__get($this->param['related']);
			$data=$r->__get($this->param['compareto']);
		}
		//Все рядом
		else{
			$data=$model->__get($this->param['compareto']);
		}
		return HTML::chars($data) ;
	}
	
	return HTML::chars($model->__get($this->name));
	
}	

function _item($model){
	if ($this->many){
		return get_class($this).' Not support listedit';
	}
	
	
	//если сравниваемое в другой табличке
	if (isset($this->param['related'])){
		$r=$model->__get($this->param['related']);
		$compare=$r->__get($this->param['compareto']);
	}
	//Все рядом
	else{
		$compare=$model->__get($this->param['compareto']);
	}
	
	super_head::js('/super/js/jsdiff.js');
	
	$view=new View('super/compare');
	$view->compare=$compare;
	$view->text=$model->__get($this->name);
	$view->element_name=$this->element_name;
	
	return $view->render();	
	
}	

function _search($vars){

	
	return 'Поиск не производится';
	
}	






}
