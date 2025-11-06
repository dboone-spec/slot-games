<?php
//удвоение для выбора карты с открытой картой дилера
class Double_Calccard extends math {

	public $dealer;
	public $user=[];
	public $count;

    public $game_id;


	public function __construct($count=4) {
		$this->count=$count;
	}


	//карта дилера
	public function GetCardDealer(){

		$this->dealer=null;
		do {
			$r=math::random_int(1,52);
			if (!in_array($r,$this->user)){
				$this->dealer=$r;
			}
		}
		while (empty($this->dealer));


		return $this->dealer;

	}


	public function GetCardUser() {


		$this->user=[];
		$i=1;
		do {
			$r=math::random_int(1,52);
			if (!in_array($r,$this->user) and $r!=$this->dealer){
				$this->user[$i]=$r;
				$i++;
			}
		}
		while (count($this->user)<$this->count);


		return $this->user;

	}


	public function win(){

		$d=card::num($this->dealer);
		$u=card::num($this->user[$this->select]);

		//user win
		if ($d<$u){
			return 2;
		}
		//равенство
		elseif($u==$d){
			return 1;
		}

		return 0;

	}


	public function clear(){

		$this->dealer=null;
		$this->user=[];
	}


}

