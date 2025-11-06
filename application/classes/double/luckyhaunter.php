<?php


class Double_Luckyhaunter extends Double_Igrosoft {




	//карта дилера
	public function GetCardDealer(){

		$this->dealer=math::random_int(0,15);
		return $this->dealer;

	}


	public function GetCardUser() {


		$this->user=[];
		$this->user[1]=math::random_int(0,15);
		$this->user[2]=math::random_int(0,15);
		$this->user[3]=math::random_int(0,15);
		$this->user[4]=math::random_int(0,15);

		return $this->user;

	}





	public function win(){

        if($this->IfChance(1/16,1,0)==1) {
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


		$d=$this->dealer;
		$u=$this->user[$this->select];

		//user win
		if ($d<$u){
			return 2*$this->amount;
		}
		//равенство
		elseif($u==$d){
			return $this->amount;
		}

		return 0;

	}


	public function come(){
        return '';
		$d=$this->dealer+1;
		$u=$this->user[$this->select]+1;
		return "$d $u";

	}

	public function result(){
        return $this->win()>0?'win':'lose';
		$a=$this->user;
		$a[0]=$this->dealer;
		ksort($a);
		$s='';
		foreach($a as $v){
			$s.=($v+1).' ';
		}
		return $s;
	}


}

