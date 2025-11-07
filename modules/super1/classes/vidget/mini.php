<?php

abstract class Vidget_Mini {
	
	abstract function render($value,$number);
	
	public $show=true;
	public $attr=array();
	
	public function __construct($name,$column,$attr=array()){

		$this->name=$name;
		$this->column=$column;
	}	

	
	public static function factory($name,$type,$column)
		{
			// Set class name
			$vidget='Vidget_Mini_'.ucfirst($type);
			return new $vidget($name,$column);
		}
	
}