<?php

class Slot_Novomatic_Marcopolo extends Slot_Novomatic {

	public function __construct() {
		parent::__construct('marcopolo');
	}

	public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}

}
