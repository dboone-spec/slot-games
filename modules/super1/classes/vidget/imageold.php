<?php
/**
 * параметры 
 * action - url куда загружать картинки
 * imgdir - web-каталог где лежат картинки, которые показывать в админке
 * imgdirlist - web-каталог где лежат картинки, которые показывать в админке в списке
 * type - имя каталога с картинками из static имя модели по умолчанию
 */
class Vidget_Imageold extends Vidget{


	
function _list($model){
	
	$var=$model->__get($this->name);
	
	
	if (!empty($var)){
		
		$dir=arr::get($this->param,'imgdirlist',arr::get($this->param,'imgdir','small'));
		$type=arr::get($this->param,'type',$this->m_name);
		return '<img src="'.th::imglink($var,$type,$dir).'" /><br>';;
		//return '<img src="'.kohana::$config->load('static.'.$this->param['action'].'_web_image').'/small/'.$var.'" /><br>';
	}
	return '';
}	

function _item($model){

	$this->element_id=$this->id($model);
	$this->element_name=$this->name($model);
	
	$dir=arr::get($this->param,'imgdir','small');
	$type=arr::get($this->param,'type',$this->m_name);
	
	$str='<div  id="admin_image_'.$this->element_id.'">';
	$var=$model->__get($this->name);
	if (!empty($var)){
		$str.='<img src="'.th::imglink($var,$type, $dir).'" /><br>';
	}
	$str.='</div>';
	$str.=form::hidden($this->element_name,$var,array('id'=>'admin_hidden_'.$this->element_id));
	
	
	$def=array('name'=>'','w'=>0, 'h'=>0, 'q'=>60);
	$sizes=arr::get($this->param,'size',array($def));

	
//	super_head::js('/super/js/jquery-ui.js');
//	super_head::css('/super/css/jquery-ui.css');
	super_head::js('/super/js/ajaxupload.js');
	
	
$js='';	
if (isset($this->param['action'])){
$js=<<<ACC
<input type="button" id="button_{$this->element_id}" value="Загрузить"> <br><br><br>
<script>
$(document).ready(function(){

new AjaxUpload('#button_{$this->element_id}', {
  action: '{$this->param['action']}',
  name: 'filedata',
  // авто submit
  autoSubmit: true,
  responseType: 'json',
  onComplete: function(file,json) {
		if(json.error==1){
			alert(json.name);
		}
		else{
			$('#admin_image_{$this->element_id}').html('<img src="'+imglink(json.name,'$type','$dir')+'"/>');
			$('#admin_hidden_{$this->element_id}').val(json.name);
		}
	}
});
	  
});
</script>

ACC
;	
}
	
	return $str.$js;
	
}	

function handler_save($data,$old_data,$model){
	$model->set($this->name,$data[$this->name]);
	return $model;
}

function _search($vars){
	return 'поиск по картинкам не ведется';
}	



function handler_search($model,$vars){
	
	return $model;	
	
}


}