<?php

class Slot_Novomatic_Alwayshot extends Slot_Novomatic{
    
    
    public function __construct() {
        parent::__construct('alwayshot');
        }
    

    
    public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}
    
    
    
    
    
}

