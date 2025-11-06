<?php

class Slot_Igrosoft_Luckyhaunter extends Slot_Igrosoft {

	public function __construct() {
		parent::__construct('luckyhaunter');
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
