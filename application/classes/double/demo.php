<?php

class Double_Demo extends math
{

    public $select;
	public $amount;
	public $state;
	protected $multiplier=2;
	protected $game_id;
        protected $percent=0.96;

    public function __construct()
    {
        
    }

    public function select(){
	

        $rnd=$this->random_float(0,1);

        if(is_numeric($this->select)) {
            $this->select = (int) $this->select;


            if($rnd<=$this->percent/4) {
                $this->state=$this->select;
            }
            else {  //lose
                if($this->select==3){
                    $this->state=math::array_rand_value([0,2,1]);
                }
                elseif($this->select==2){
                    $this->state=math::array_rand_value([0,1,3]);
                }
                elseif($this->select==1){
                    $this->state=math::array_rand_value([0,2,3]);
                }
                elseif($this->select==0){
                    $this->state=math::array_rand_value([1,2,3]);
                }
            }
            return $this->state;
        }

        if ($rnd<=$this->percent/2){ //win
            if($this->select=='black'){
                $this->state=math::array_rand_value([2,3]);
            }
            elseif($this->select=='red'){
                $this->state=math::array_rand_value([0,1]);
            }
        }
        else{ //lose
            if($this->select=='red'){
                $this->state=math::array_rand_value([2,3]);
            }
            elseif($this->select=='black'){
                $this->state=math::array_rand_value([0,1]);
            }
        }


	return $this->state;
        
}

    public function win()
    {

        //вход select
        //1 black
        //0 red
        //выход state
        //3 - black
        //2 - black
        //1 - red
        //0 - red
        $r = 0;
        if(in_array($this->state,[2,3]) and $this->select === 'black')
        {
            $r = 1;
        }
        if(in_array($this->state,[0,1]) and $this->select === 'red')
        {
            $r = 1;
        }

        //x4
        if($this->state===$this->select) {
            $r = 2;
        }

//        $r=2;

        $c = dbredis::instance()->get('demoSuitStat');

        if(!$c) {
            $c['red'] = 0;
            $c['black'] = 0;
            $c['suit0'] = 0;
            $c['suit1'] = 0;
            $c['suit2'] = 0;
            $c['suit3'] = 0;
        }
        else{
            $c=json_decode($c);
            $c=th::ObjectToArray($c);
        }

        if($this->state==0) {
            $c['red']++;
            $c['suit0']++;
        }

        if($this->state==1) {
            $c['red']++;
            $c['suit1']++;
        }

        if($this->state==2) {
            $c['black']++;
            $c['suit2']++;
        }

        if($this->state==3) {
            $c['black']++;
            $c['suit3']++;
        }
        
        dbredis::instance()->set('demoSuitStat', json_encode($c));
        

        return $r * $this->amount * $this->multiplier;
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
