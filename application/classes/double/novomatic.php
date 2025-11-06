<?php


class Double_Novomatic extends math {

	public $select;
	public $amount;
	public $state;
	protected $multiplier=2;
    protected $percent=1;

    public function __construct($game_id)
    {
        $this->game_id = $game_id;
    }

	public function select(){

        $o = office::instance()->office();
        $this->percent=((int) $o->rtp)/100;


        $counter=new Model_Counter(['game_id'=>$this->game_id,'office_id'=>OFFICE]);
        $is2=true;

        if ($counter->double_out+$this->amount*2>$this->percent*($counter->double_in+$this->amount) ){
             $is2=false;
        }


        $rnd=$this->random_float(0,1);


        if ($rnd<=$this->percent/2 and $is2){ //win
            if($this->select==1){
                $this->state=math::array_rand_value([2,3]);
            }
            elseif($this->select==0){
                $this->state=math::array_rand_value([0,1]);
            }
        }
        else{ //lose
            if($this->select==0){
                $this->state=math::array_rand_value([2,3]);
            }
            elseif($this->select==1){
                $this->state=math::array_rand_value([0,1]);
            }
        }

		return $this->state;
	}


	public function win(){

		//вход select
		//1 black
		//0 red

		//выход state
		//3 - black
		//2 - black
		//1 - red
		//0 - red
		$r=0;
		if(in_array($this->state,[2,3]) and $this->select==1){
			$r=1;
		}
		if(in_array($this->state,[0,1]) and $this->select==0){
			$r=1;
		}


		return $r*$this->amount*$this->multiplier;
	}

	public function clear(){

		return true;
	}

	public function come(){
		return $this->select;
	}

	public function result(){
		return $this->state;
	}


}

