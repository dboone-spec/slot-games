<?php

class Vidget_Mini_Delete extends Vidget_Mini_Input{
	
	public function render($value,$number,$attr=array()){
		$html='<div style="float:left">';
		$html.=form::checkbox("$this->name[$number][delete]",null,false,array('id'=>"{$this->name}_{$number}_delete"));
		$html.='</div><div style="float:left">';
		$html.="<label for=\"{$this->name}_{$number}_delete\">удалить</label>";
		$html.='</div>';
		return $html;
	}
	
}

