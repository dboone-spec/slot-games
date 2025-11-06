<?php

class Slot_Novomatics extends Slot_Calcs{
	
	public $mud=0;		
	
	
	public function __construct( $name) {
		parent::__construct('novomatic', $name);
                
	}
	
	public function double(){

		$this->doubleclass=new Double_Novomatic();
		
		parent::double();
		
		
		
	}
	

	
}

