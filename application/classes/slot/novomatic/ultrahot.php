<?php

class Slot_Novomatic_Ultrahot extends Slot_Novomatic{


    public function __construct() {
        parent::__construct('ultrahot');
        }


    public function win(){

        $r=parent::win();

        $s = $this->sym();

        unset($s[4]);
        unset($s[5]);
        unset($s[9]);
        unset($s[10]);
        unset($s[14]);
        unset($s[15]);

        $c=array_count_values($s);
        $count=reset($c);
        $sym=key($c);
        if ($count==9 and in_array($sym,[1,2,3,4])){
            foreach($this->win as $line=>$win){
                $this->win[$line]*=2;
            }
            $this->win_all=array_sum($this->win);
        }


        return $r;
    }


    public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}





}

