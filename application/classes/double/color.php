	<?php


class Double_Color extends math {

	public $select;
	public $amount;
	public $state;
	protected $multiplier=2;


    public function select(){

        $rnd=mt_rand(1,100);

        if ($rnd<=48){ //win
            if($this->select==1){
                $this->state=math::random_int(1,26);
            }
            else{
                $this->state=math::random_int(27,52);
            }
        }
        else{ //lose
            if($this->select==2){
                $this->state=math::random_int(1,26);
            }
            else{
                $this->state=math::random_int(27,52);
            }
        }

		return $this->state;
	}

	public function win(){
		$r= (int) card::color($this->state)==$this->select;
		return $r*$this->amount*$this->multiplier;
	}

	public function clear(){

		return true;
	}

	public function come(){
		return $this->select;
	}

	public function result(){
		return card::print_card($this->state).' color';
	}


}

