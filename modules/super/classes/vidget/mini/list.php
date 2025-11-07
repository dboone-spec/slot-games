<?php

class Vidget_Mini_List extends Vidget_Mini_Input{
	
	public function render($value,$number,$attr=array()){
		
		$this->attr['id']="{$this->name}_{$number}_{$this->column}";
		return form::select("$this->name[$number][{$this->column}]",$this->options['list'],$value,$this->attr);		
	}
	
}

