<?php

class Vidget_Tinymce extends Vidget{

	
function _list($model){
	return HTML::chars($model->__get($this->name));
}	

function _item($model){
	
	$option=array('id'=>'super_'.$this->element_id,'cols'=>100, 'rows'=>14);
	
	//super_head::js('/super/js/jquery.js');
	super_head::js('/super/js/tinymce/tinymce.min.js');
	
	
	

$userdir=auth::user()->name;
	
	$js=<<<ACC
	<script>
			//Да сдолжнут к хуям все ебаные дебилы разработчики moxiemanager 
			//исправлено BaseFile.php
			//исправвлен super\js\tinymce\plugins\image\plugin.min.js
		$(function() {
			
			tinymce.PluginManager.load('moxiecut', '/super/js/tinymce/plugins/moxiemanager/plugin.min.js');
			tinymce.init({
				selector: "#super_$this->element_id",
				plugins: [
					"advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code fullscreen",
					"insertdatetime media table contextmenu paste moxiemanager"
				],
				toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
				convert_urls : false,
				relative_urls : false,
				moxiemanager_path : '/uploads/$userdir',
				moxiemanager_rootpath: '/uploads/$userdir',
	
			
			});


		})
		</script>
ACC
;
	
	return form::textarea($this->name($model),$model->__get($this->name),$option).$js;
}	

function _search($vars){
	return form::input($this->name,$vars[$this->name]);
}	




}