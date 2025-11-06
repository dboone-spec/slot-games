<?php

abstract class Slot_Novomatic_Replace2 extends Slot_Novomatic_Replace{

    public $bonus_win_mask;
    public $bonus_win_lines=[];


    public function calcbonus()
    {
        return 0;
    }

    public function calcbonusgames($count)
    {
        return 0;
    }

    public function lightingLine($num = null) {

        if (is_null($num)) {
            for ($i = 0; $i <= $this->cline; $i++) {
                $a[$i] = $this->lightingLine($i);
            }
            return $a;
        }

        //scatter
        if ($num==0){
            $light=0;
            if ($this->win[0] > 0) {
                foreach ($this->sym() as $sym) {
                    $light = $light << 1;
                    if (in_array($sym, $this->anypay)) {
                        $light ++;
                    }
                }
            }
            return $light;
        }

        switch ($this->LineWinLen[$num]) {
            case 0: return 0;
            case 1: return 0b10000;
            case 2: return 0b11000;
            case 3: return 0b11100;
            case 4: return 0b11110;
            case 5: return 0b11111;
            case -1: return 0b00001;
            case -2: return 0b00011;
            case -3: return 0b00111;
            case -4: return 0b01111;
            case -5: return 0b11111;
        }

        return 0;
    }

    public function win()
    {
        $this->isBonusMode = false;
        
        parent::win();
        
        if($this->isFreerun) {
            
            $data = $this->sym();
            $this->bonusdata = $this->replaceSym($this->extra_param,$data);

            for ($i = 1; $i <= $this->cline; $i++) {
                $this->bonus_win_line[$i] = 0;
                $this->bonus_win_mask[$i]='';
            }

            $this->bonusdata = array_values($this->bonusdata);

            for ($i = 1; $i <= $this->cline; $i++) {
                $comb = [];

                foreach ($this->lines[$i] as $pos) {
                    $comb[] = $this->bonusdata[$pos-1];
                    $this->bonus_win_mask[$i]=''.$this->bonus_win_mask[$i].intval($this->bonusdata[$pos-1]==$this->extra_param);
                }
                $this->bonus_win_mask[$i]=bindec($this->bonus_win_mask[$i]);

                $cnt = array_count_values($comb);

                if(isset($cnt[$this->extra_param])) {

                    $bonus_win=$this->pay($this->extra_param,$cnt[$this->extra_param]) * $this->amount_line * $this->multiplier;
                    $this->bonus_win+=$bonus_win;
                    $this->bonus_win_line[$i]=$bonus_win*100;
//                    $this->win[$i] += $bonus_win;
                }
            }

            $this->win_all += $this->bonus_win;
        }
    }

}

