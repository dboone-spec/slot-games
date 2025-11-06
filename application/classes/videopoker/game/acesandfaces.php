<?php

class Videopoker_Game_Acesandfaces extends Videopoker_Calc{
	
	public $paycard=11;
	
        
        public function __construct() {
            return parent::__construct('acesandfaces');
            
        }
	
	
/**
 * уровень карточной комбинации 
 * @param array $card - индексы карт в колоде
 */
public function level(array $card){
	
	if (count($card)<5){
		throw new Kohana_Exception(__('Меньше 5 карт быть не может'));
	}
	
	$this->clear($card);
	
	if ($this->RoyalFlush()) return 12;
	if ($this->FourAces()) return 11;
	if ($this->StraightFlush()) return 10;
	if ($this->FourFaces()) return 9;
	if ($this->Four210()) return 8;
	if ($this->FullHouse()) return 7;
	if ($this->Flash()) return 6;
	if ($this->Straight()) return 5;
	if ($this->Set()) return 4;
	if ($this->TwoPair()) return 3;
	if ($this->OnePair()) return 2;
	if ( $this->HighCard()) return 1;

return 0;
	
	
}

	public function FourAces(){
		if ($this->Quads()){
			$key=array_keys($this->c_value_raw,4);
			$key=$key[0];
			if ($key==14){
				return true;
			}
			
		}
		return false;
	}




	public function FourFaces(){
		if ($this->Quads()){
			$key=array_keys($this->c_value_raw,4);
			$key=$key[0];
			if ($key>=11){
				return true;
			}
			
		}
		return false;
	}



	public function Four210(){
		return $this->Quads();
	}





}

