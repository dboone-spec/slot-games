<?php

class Slot_Novomatic_Threee extends Slot_Novomatic{


    public function __construct() {
        parent::__construct('threee');
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
		$this->wild = [5];
    }



}

