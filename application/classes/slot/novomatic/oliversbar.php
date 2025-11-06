<?php

class Slot_Novomatic_Oliversbar extends Slot_Novomatic {

	public function __construct() {
		parent::__construct('oliversbar');
	}

	public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}

}
