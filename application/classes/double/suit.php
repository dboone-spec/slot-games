<?php


class Double_Suit extends Double_Color{
	
	protected $multiplier=4;
	
	
	public function win(){
		$r= card::suit($this->state)==$this->select;
		return $r*$this->amount*$this->multiplier;
	}
	

	
	public function result(){
		return card::print_card($this->state).' suit';
	}
	
}

