<?php

class Vidget_Mini_Input extends Vidget_Mini{
	
	
	public function render($value,$number){
		
		$this->attr['id']="{$this->name}_{$number}_{$this->column}";
		return form::input("$this->name[$number][{$this->column}]",$value,$this->attr);		
	}
	
}

