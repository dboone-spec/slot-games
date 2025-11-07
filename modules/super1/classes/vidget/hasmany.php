<?php

/**
 * Параметры
 * related - связь, должна быть прописана в _has_many модели
 * list (array) - имя столбцов для вывода
 * vidgets (array ) - имена виджетов  для вывода
 * vidgets_option (array ) - данные виджетов для вывода
 */


class Vidget_Hasmany extends Vidget{
	


public function _list($model){
	return '';	
}

public function _search($vars){
	return '';
}

public function _item($model){
	
	if ($this->many){
		return get_class($this).' Not support listedit';
	}
	
	if (!$model->loaded()){
		return 'Для добавления элементов коллекции сохраните модель';
	}
	
	$id=$model->pk();
	$class=get_class($model->__get($this->param['related']));
	$class=explode('_',$class);
	$m=ORM::factory($class[1]);
	$data=$model->__get($this->param['related'])->find_all();
	
	//primary key
	if (!in_array($m->primary_key(),$this->param['show'])){
		$this->param['show'][]=$m->primary_key();
		$this->param['vidgets'][$m->primary_key()]='hidden';
	}
	//delete
	$this->param['show'][]='_delete_checkbox';
	$this->param['vidgets']['_delete_checkbox']='delete';
	
	$vidgets=array();
	$attr=arr::get($this->param,'attr',array());
	
	foreach( $this->param['show'] as $column){
		if ( isset($this->param['vidgets'][$column]) ){
			$vidgets[$column]=Vidget_Mini::factory($this->name,$this->param['vidgets'][$column],$column);
			$vidgets[$column]->options=arr::get($this->param['vidgets_option'],$column,array());
		}
		else{
			$vidgets[$column]=Vidget_Mini::factory($this->name,'input',$column);
			$vidgets[$column]->attr=arr::get($attr,$column,array());
		}
	}
	
	
	
	$new_el='<tr>';
	foreach ($this->param['show'] as $s){
		$new_el.='<td>'.$vidgets[$s]->render('','::n::').'</td>';
	}
	$new_el.='</tr>';
	$new_el=  str_replace("\n",'',$new_el);
	$new_el=  str_replace("\r",'',$new_el);
	
	$labels=$m->labels();
	$labels['_delete_checkbox']='Удалить';
	
	$view=new view('super/hasmany');
	$view->show=$this->param['show'];
	$view->label=$labels;
	$view->data=$data;
	$view->vidgets=$vidgets;
	$view->new_el=$new_el;
	$view->name=$this->name;
	
	return $view->render();
}


	


function handler_save($data,$old_data,$model){
	
	
	if (!isset($data[$this->name])) {
		return $model;		
	}
	
	$class=get_class($model->__get($this->param['related']));
	$class=explode('_',$class);
	$m=ORM::factory($class[1]);
	
	foreach($data[$this->name] as $row ){
		$m->where($m->primary_key(),'=',$row[$m->primary_key()])->find();
		foreach( $this->param['show'] as $column){
			//если удалить то не сохраняем
			$m->set($column,$row[$column]);
		}
		
		
		if (!$m->loaded()){
			if (isset($row['delete'])){
				continue;
			};
			$m->set($model->object_name().'_id',$model->pk());
		}
		
		//если загружено и нужно удалить
		if (isset($row['delete'])){
			$m->delete();
			continue;
		}
		
		$m->save();
		$m->clear();
	}
	
	return $model;
}
	



}

