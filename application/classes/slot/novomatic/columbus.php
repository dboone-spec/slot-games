<?php

class Slot_Novomatic_Columbus extends Slot_Novomatic {

	public function __construct() {
		parent::__construct('columbus');
	}

	public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}

    public function SetFreeRunMode () {
		parent::SetFreeRunMode ();
		$this->wild = [8,9];
    }

}
