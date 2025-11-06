<?php


class Bj_Calc extends Bj_Root{
	
	
	public function tonumber($a){
		
		if (is_array($a)){
			foreach ($a as $key=>$value){
				$a[$key]=self::tonumber($value);
			}
			return $a;
		}
		
		
		//0-туз 12 король
		//              сервер клиент
		//0-12 черви     4       1
		//13-25 буби     3       2
		//26-38 крести   2       3
		//39-51 вини     1       4
		$suit=5-card::suit($a);
		$num=card::num($a)-1;
		if ($num %13 ==0){
			$num-=13;
		}

		return ($suit-1)*13+$num;

		
	}
	
	

	public $player=[];
	public $dealer=[];
	public $state;
	
	
	
	public function bet(){
		
		$error=bet::error($this->amount);
		if ($error>0){
			return $error;
		}
		
		
		$this->player=[];
		//карты игрока
		$this->player[]=$this->getCard();
		$this->player[]=$this->getCard();

		$c=$this->getCard();
		$this->dealer=[];
		$this->dealer[]=$c;
		

		//обычное состояние
		$this->state = "0|7|0|0|0|0";

		//Если у дилера туз
		if(card::num($c)==14 ){
			$this->state = "0|16|0|0|0|0";
		}
		
		
		
		//записываем данные в сессию
		$data['player']=$this->player;
		$data['dealer']=$this->dealer;
		$data['amount']=$this->amount;
		$data['double']=0;
		$data['status']=1;
		
		bet::start($this->amount,$data);
		
		return 0;
		
	}
	
	
	
	
	public function savebet(){
		
		$bet['amount']=$this->amount+$this->insur_bet;
		$bet['come']=card::print_card($this->dealer).' ('.$this->ScoreDealer().')';
		$bet['result']=card::print_card($this->player).' ('.$this->ScorePlayer().')';
		if ($this->insur_bet>0){
			$bet['result'].=' insur';
		}
		$bet['win']=$this->win+$this->insur_win;
		$bet['game_id']=0;
		//писать номер колоды
		$bet['method']='random';

		bet::make($bet,$this->win+$this->insur_win);
		
	}
	
	
	//хуй страховой
	public function noinsur(){
		
		$data=game::data();
		
		if ($data['status']!=1){
			return 5;
		}
		
		$this->player=$data['player'];
		$this->dealer=$data['dealer'];
		$this->amount=$data['amount'];
		$this->state = "0|7|0|0|0|0";
				
		
		
		
	}
	
	//ЗастраХУЙ
	public function insur(){
		
		$data=game::data();
		
		if ($data['status']!=1){
			return 5;
		}
		
		$this->player=$data['player'];
		$this->dealer=$data['dealer'];
		$this->amount=$data['amount'];
		
		
		$error=bet::error($this->amount);
		if ($error>0){
			return $error;
		}		
		
		//записываем данные в сессию
		$data['insur']=$this->amount;
		bet::start($this->amount,$data);
		$this->state="0|7|0|0|0|0";
				
		
	}
	
	public function double(){
		
		$data=game::data();
		
		if ($data['status']!=1){
			return 5;
		}
		
		$this->player=$data['player'];
		$this->dealer=$data['dealer'];
		$this->amount=$data['amount'];
		
		
		$error=bet::error($this->amount);
		if ($error>0){
			return $error;
		}		
		
		
		//записываем данные в сессию
		$data['player']=$this->player;
		$data['dealer']=$this->dealer;
		$data['amount']=$this->amount*2;
		$data['double']=1;
		$data['status']=1;
		
		bet::start($this->amount,$data);

		
	}




	public function hit(){
		
		$data=game::data();
		
		if ($data['status']!=1){
			return 5;
		}
		
		$this->player=$data['player'];
		$this->dealer=$data['dealer'];
		$this->amount=$data['amount'];
		
		
		$this->player[]=$this->getCard();
		
		
		//перебор у игрока
		if ($this->ScorePlayer()>21){
			
			$this->insur_bet=game::data('insur',0);
			$this->insur_win=0;
			
			$this->win=0;
			$this->savebet();
			
			$this->state = "1|0|1|0|1|0";
			$this->payout = "0.0|0.0|0.0|0.0";
		}
		else{
			$data['player']=$this->player;
			$data['dealer']=$this->dealer;
			$data['amount']=$this->amount;
			$data['double']=$data['double'];
			$data['status']=1;
			game::session()->flash($data);
			$this->state='0|3|0|0|0|0';
		}
		
		return 0;
		
		
	}
	
	
	
	
	public function stand(){
		
		$data=game::data();
		
		if ($data['status']!=1){
			return 5;
		}
		
		$this->player=$data['player'];
		$this->dealer=$data['dealer'];
		$this->amount=$data['amount'];
		$this->double=$data['double'];
		$this->insur_bet=game::data('insur',0);
		$this->insur_win=0;
		
		$i=1;
		$this->scoresdealer="0|0";
		$s=10;
		while ($s<17 ){
			$this->dealer[]=$this->getCard();
			$s=$this->ScoreDealer();
			$this->scoresdealer.=",$s|$s";
			$i++;
		}
		
		
		//страховка сыграла
		if ($this->insur_bet>0 and $this->insur_win()){
			$this->insur_win=$this->insur_bet*2;
		}
		
	
		$r=$this->win();
		$this->win=$this->amount*$r;
		

		
		//проиграл без удвоения
		if ($r==0 and $this->double==0){
			$this->state = "1|0|1|0|1|0";
		}
		
		//проиграл и было удвоение
		if ($r==0 and $this->double==1){
			$this->state = "1|0|1|0|1|2";
		}
		
		//выиграл без удвоения
		if ($r>1 and $this->double==0){
			$this->state = "1|0|1|0|1|4";
		}
		

		//выиграл с удвоением
		if ($r>1 and $this->double==1){
			$this->state = "1|0|1|0|1|6";
		}

		
		//равенство без удвоения, возвращаем бабки
		if ($r==1 and $this->double==0){
			$this->state = "1|0|1|0|1|8";
		}

		//равенство с удвоением, возвращаем бабки
		if ($r==1 and $this->double==1){
			$this->state = "1|0|1|0|1|8";
		}

		$this->payout = "0.0|{$this->win}|{$this->insur_win}.0|{$this->win}";
		
		
		
		$this->savebet();
		
		return 0;
		
		
	}
	
	
	
}

