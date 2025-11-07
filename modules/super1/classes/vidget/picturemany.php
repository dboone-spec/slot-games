<?php

/**
 * imgdir - суб каталог каталог для отображения картинок small по умолчанию
 * 
 */

class Vidget_Picturemany extends Vidget_Image{
	
	
function _list($model){
	return 'Can not show vidget Picturemany in list mode.';
}	

function _item($model){
	
	if ($this->many){
		return get_class($this).' Not support listedit';
	}

	if (!$model->loaded()){
		return 'Для добавления картинок сохраните модель';
	}
	
	$view=new View('super/picturemany');
	
	$pmodel=isset($this->param['model']) ? $this->param['model'] : "picture_{$this->m_name}";
	
	$data=ORM::factory($pmodel)->where("{$this->m_name}_id",'=',$model->pk());
	if (isset($this->param['order'])){
		$data->order_by($this->param['order']);
	}
	$data=$data->find_all()->as_array();
    
	
	

	
//	super_head::js('/super/js/jquery-ui.js');
//	super_head::css('/super/css/jquery-ui.css');
	super_head::js('/super/js/ajaxupload.js');
	
	
	$view->element_id=$this->element_id;
	$view->element_name=$this->element_name;
	$view->imgdir=arr::get($this->param,'imgdir','small');
	$view->order=arr::get($this->param,'order',false);

	$view->m_name=$this->m_name;
	$view->data=$data;
	$view->num=1;
	
	return $view->render();
	
}	

function handler_save($data,$old_data,$model){
	
	
	if (!isset($data[$this->name])) {
		return $model;		
	}
	
	$pmodel=isset($this->param['model']) ? $this->param['model'] : "picture_{$this->m_name}";
	
	foreach($data[$this->name]['name'] as $num=>$name ){
		 

		$file=$data[$this->name]['file'][$num];
		if (empty($file)){
			continue;
		}
		
		$m=ORM::factory($pmodel,$file);
		if (!$m->loaded()){
			//если удалить то не сохраняем
			if (isset($data[$this->name]['del'][$num])){
				continue;
			};
			$m->file=$file;
		}
		
		//если загружено и нужно удалить
		if (isset($data[$this->name]['del'][$num])){
			$m->delete();
			continue;
		}
	
		$m->name=$name;
		$m->set("{$this->m_name}_id",$model->pk());
		if (isset($this->param['order'])){
			$m->set($this->param['order'],$data[$this->name]['order'][$num]);
			
		}
		
		$m->save();
		
	}
	
	return $model;
}

function _search($vars){
	return 'поиск по картинкам не ведется';
}	



function handler_search($model,$vars){
	
	return $model;	
	
}


}