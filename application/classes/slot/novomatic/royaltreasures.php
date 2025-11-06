<?php

class Slot_Novomatic_Royaltreasures extends Slot_Novomatic {

	public function __construct() {
		parent::__construct('royaltreasures');
	}

	public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}

}
