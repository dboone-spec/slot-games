<?php

class Slot_Novomatic_Richesofindia extends Slot_Novomatic {

	public function __construct() {
		parent::__construct('richesofindia');
	}

	public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}

    // если выпадает мужик в любом месте на барабане то:
    // Если запуск фригеймов был с 3 скаттеров множитель случайный от 3 или 5
    //Если запуск фригеймов был с 4 скаттеров множитель случайный от 3 или 5 или 10
    //Если запуск фригеймов был с 5 скаттеров множитель случайный от 3 или 5 или 10 или 25
    //Если мужик не выпадает, то никакого множителя нет.

    public function win()
    {

        parent::win();

        if($this->isFreerun && $this->win_all>0) {
            $comb = array_values($this->sym());

            if(in_array($this->wild[0],$comb)) {
                $vals = [3,5];
                if($this->freeCountAll==20) {
                    $vals[]=10;
                }
                if($this->freeCountAll==25) {
                    $vals[]=25;
                }
                $this->multiplier = math::array_rand_value($vals);
                $this->win_all = $this->win_all*$this->multiplier;
            }
        }
    }

}
