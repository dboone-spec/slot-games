<?php


class Double_Igrosoft extends Double_Calccard {


	public $count=4;
	public $select;
	public $amount;
	public $state;
	protected $multiplier=2;

    public $name;

	public function select(){

		$this->GetCardUser();
		$this->state=$this->user;
		$this->state[0]=$this->dealer;
		return $this->state;
	}


	public function win(){

//		$r=parent::win(); //было раньше

        //ничья
        if($this->IfChance(3/51,1,0)==1) {
                return 1*$this->amount;
        }
        else {
            $rnd = math::random_int(1,100);

            if($rnd <= 48) {
                $c=$this->getCounter('igrosoft',$this->name);
                $r=(int) (($c->double_out+2*$this->amount)/($c->double_in+$this->amount)<0.96);
                if($r==1) {
                    return 2*$this->amount;
                }
                else {
                    return 0;
                }
            }
            else {
                return 0;
            }
        }

	}


	public function come(){
        return '';
		$a=[$this->dealer,$this->user[$this->select]];
		return card::print_card($a);
	}

	public function result(){
        return $this->win()>0?'win':'lose';
		$a=$this->user;
		$a[0]=$this->dealer;
		ksort($a);
		return card::print_card($a);
	}



	public function clear(){
		$this->user=[];
	}



}

