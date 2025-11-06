<?php

class Bj_Root extends math{
	
public $deck;
public $dealer=[];
public $player=[];
	
	
public function deck(){

	if (empty($this->deck)){

		$this->deck=[];
		for($i=1;$i<=52;$i++){
			//при таких вероятностях z=0.9403145 кеф в BJ 2 - много не выиграешь, z нормальный
			$this->deck[$i]=1;//math::random_float(0.8,1.2);
		}
	}

	return $this->deck;

}



public function weight($card){
	
	$num=card::num($card);
	if ($num>=10 and $num<=13){
		return 10;		
	}
	if ($num==14){
		return 11;
	}
	
	return $num;
	
}


public $ace=false;
public function comb_weight($cards){
	
	$this->ace=false;
	$s=0;
	$ace=0;
	foreach($cards as $c){
		$s+=$this->weight($c);
		if (card::num($c)==14){
			$ace++;
		}
	}
	
	while ($ace>0 and $s>21){
		$s-=10;
		$ace--;
	}
	
	if ($ace>0){
		$this->ace=true;
	}
	
	return $s;
	
}


public function getCard(){
		
	do {
		$r=$this->getRandWeight($this->deck());
	} while(in_array($r,$this->player) or in_array($r,$this->dealer));

	return $r;

}


public function ScoreDealer(){
	return $this->comb_weight($this->dealer);
}

public function ScorePlayer(){
	return $this->comb_weight($this->player);
}

public function calc(){
	
	$this->deck();
	$this->dealer=[];
	$this->player=[];
	
	$this->dealer();
	$this->player();
	$this->dealer2();
	
	
}


public function dealer(){
	
	while (count($this->dealer)<2){
		$this->dealer[]=$this->getCard();
	}
	
}


public function dealer2(){
	
	while ($this->ScoreDealer()<17 ){
		$this->dealer[]=$this->getCard();
	}
	
}


public function player(){
	
	
	while ($this->base()){
		$this->player[]=$this->getCard();
	}
	
}


public function base(){
	

	
	$num=card::num($this->dealer[0]);
	$sum=$this->comb_weight($this->player);
	
	if ($this->ace){
		
		if ($sum>=19){
			return false;
		}
		if($num<=6 and $sum>=18){
			return false;
		}
		
	}
	else{
	
		if ($sum>=17){
			return false;
		}


		if ($num<=6){
			if ($sum>=13){
				return false;
			}
			if ($num>=4 and $sum>=12){
				return false;
			}

		}
	
	}
	
	
	return true;		
	
	
}


public function insur_win(){
	
	$p=$this->comb_weight($this->player);
	$d=$this->comb_weight($this->dealer);
	$bjp=($p==21 and count($this->player)==2);
	$bjd=($d==21 and count($this->dealer)==2);
	
	return $bjd and !$bjp;
	
}


public function win(){
	
		
	$p=$this->comb_weight($this->player);
	$d=$this->comb_weight($this->dealer);
	$bjp=($p==21 and count($this->player)==2);
	$bjd=($d==21 and count($this->dealer)==2);
	
	//перебор игрока
	if ($p>21){
		 return 0;
	}
	//перебор у дилера
	elseif($d>21){
		return 2;
	}
	//bj у всех
	elseif($bjp and $bjd){
		return 1;
	}
	//bj у игрока
	elseif($bjp){
		return 1.5;
	}
	//bj у дилера
	elseif($bjd){
		return 0;
	}
	elseif($p>$d){
		return 2;
	}
	elseif($p==$d){
		return 1;
	}
	
	
	return 0;
	
}

	
}

