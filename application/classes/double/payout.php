<?php

class Double_Payout extends math {

    protected $_counter;
    protected $win_sum;
    protected $amount=0;


    public function __construct()
    {
        game::session('payout','double');
        $this->_counter = $this->getCounter('payout','double');
        return $this;
    }


    public function set_amount($amount) {
        $this->amount = $amount;
        return $this;
    }

    public function result() {
        return $this->win_sum;
    }

    public function win() {
        $rnd = math::random_int(1,100);

        if($rnd <= 48) {
            $c=$this->_counter;
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

    public function bet($come=null) {

        $error = bet::error($this->amount);
        if ($error > 0) {
            return $error;
        }

        $win = $this->win();
        $this->win_sum = $win;

        $bet['amount'] = $this->amount;
        $bet['result'] = $win>0?'win':'lose';
        $bet['office_id'] = OFFICE;
        $bet['come'] = $come;
        $bet['win'] = $this->win_sum;
        $bet['game_id'] = 0;
        $bet['method'] = 'calc_double';
        $bet['is_freespin'] = false;

        bet::make($bet, 'double');
    }
}