<?php

class Slot_Igrosoft_Fruitcocktail2 extends Slot_Igrosoft {

	public function __construct() {
		parent::__construct('fruitcocktail2');
	}

	//TODO проверить, нужно ли так делать в игрософте
	public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
	}

    public function calcbonus()
    {
        $c=$this->getCounter('igrosoft', $this->name);

        $pay = $this->config['bonus_pay'];
        $count_chance = $this->config['bonus_countchance'];

        $canPay=$this->bonusZ*$c->in-$c->bonus;
        $canPay=$canPay>0 ? $canPay : 0;

        $count=count($this->bonus_chance);

        $this->bonusdata=array_fill(0,$count, 0);
        $win=0;

        for($i=0;$i<$count-1;$i++){
            $cnt=0;
            for($y=1;$y<=3;$y++) {
                $cnt+=$this->IfChance($count_chance[$i],1,0);
            }

            $t=$this->IfChance($this->bonus_chance[$i],$cnt,0);

            if($canPay>$win+$pay[$i]*$t*$this->amount){
                $win+=$pay[$i]*$t*$this->amount;
                $this->bonusdata[$i]=$t;
            }
            else {
                //echo Debug::vars($canPay,$win+$pay[$i]*$t*$this->amount,$win,$i,$t,'=======================================');
            }
        }

        $this->bonusPay=$win;
        return $this->bonusPay;
    }

}
