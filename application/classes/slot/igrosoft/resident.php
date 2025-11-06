<?php

class Slot_Igrosoft_Resident extends Slot_Igrosoft {

	public function __construct() {
		parent::__construct('resident');
	}

	//TODO проверить, нужно ли так делать в игрософте
	public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}



}
