<?php

class Slot_Novomatic_Columbusd extends Slot_Novomatic{


    public function __construct () {
        parent::__construct ('columbusd');
       }


    public function SetFreeRunMode () {
		parent::SetFreeRunMode ();
		$this->wild = [8,9];
    }

}

