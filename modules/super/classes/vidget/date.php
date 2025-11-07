<?php

class Vidget_Date extends Vidget{


function _list($model){
	return HTML::chars(date('d-m-Y',$model->__get($this->name)));
}

function _item($model){
	$option=array('id'=>'super_'.$this->element_id);

	$js='<script>
			$(function(){
				$("#super_'.$this->element_id.'").datepicker({ dateFormat:"yy-mm-dd"});
			});
		</script>';

	return form::input($this->name($model),$model->__get($this->name),$option).$js;
}

function _search($vars){
	$option=array('id'=>'super_'.$this->name.'_start');
	$option1=array('id'=>'super_'.$this->name.'_end');

	$js='<script>
			$(function(){
				$("#super_'.$this->name.'_start").datepicker({ dateFormat:"yy-mm-dd"});
				$("#super_'.$this->name.'_end").datepicker({ dateFormat:"yy-mm-dd"});
			});
		</script>';

	return ' с '.form::input($this->name.'_start',$vars[$this->name.'_start'],$option).'&nbsp по '.form::input($this->name.'_end',$vars[$this->name.'_end'],$option1).$js;
}



function handler_search($model, $vars){

	if (isset($vars[$this->name.'_start']) and isset($vars[$this->name.'_end'])){
		$start=$vars[$this->name.'_start'];
		$end=$vars[$this->name.'_end'];
		$this->search_vars[$this->name.'_start']=$start;
		$this->search_vars[$this->name.'_end']=$end;
		return $model->where($this->name,'>=',$start)->where($this->name,'<=',$end);
	}

	$this->search_vars[$this->name.'_start']='';
	$this->search_vars[$this->name.'_end']='';

	return $model;

}


}








