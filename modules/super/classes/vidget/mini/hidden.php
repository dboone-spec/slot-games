<?php

class Vidget_Mini_Hidden extends Vidget_Mini_Input{
	
		public $show=false;
	
	public function render($value,$number,$attr=array()){
		return form::hidden("$this->name[$number][{$this->column}]",$value,array('id'=>"{$this->name}_{$number}_{$this->column}"));		
	}
	
}

