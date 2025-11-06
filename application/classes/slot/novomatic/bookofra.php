<?php

class Slot_Novomatic_Bookofra extends Slot_Novomatic_Replace{

    public function __construct() {
        parent::__construct('bookofra');
    }

    public function win()
    {
        parent::win();

        if($this->isBonusMode) {

            $this->win[0]=0;

            for ($i = 1; $i <= $this->cline; $i++) {
                $comb = [];
                foreach ($this->lines[$i] as $pos) {
                    $comb[] = $this->bonusdata[$pos];
                }

                $cnt = array_count_values($comb);

                if(isset($cnt[$this->extra_param])) {
                    $this->win[$i] = $this->pay($this->extra_param,$cnt[$this->extra_param]) * $this->amount_line * $this->multiplier;
                }
            }

            $this->win_all = array_sum($this->win);
        }
    }
}

