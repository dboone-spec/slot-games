<?php

class Slot_Igrosoft_Gnome extends Slot_Igrosoft {

	public function __construct() {
		parent::__construct('gnome');
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
