<?php

class Slot_Agt_Besthottest100 extends Slot_Agt{

    public $bonus_win_mask;
    public $bonus_win_lines=[];
    // модификатор бонус игры
    public $mud;
    public $replacenum;
    protected $_calc_extra=false;
    protected $bonus;

    public function __construct($name) {
        parent::__construct($name);
    }

    public function calcbonus()
    {
        return 0;
    }

    public function calcbonusgames($count)
    {
        return 0;
    }

    public function symreplace($num) {

        $bar = $num % $this->barcount;
        if ($bar == 0) {
            $bar = $this->barcount;
        }

        $pos = $this->pos[$bar] + floor(($num - 0.01) / $this->barcount);

        if ($pos >= count($this->bars[$bar])) {
            $pos -= count($this->bars[$bar]);
        }

        for($i=0;$i<$this->heigth;$i++) {

            $posi = $this->pos[$bar] + $i;

            if ($posi >= count($this->bars[$bar])) {
                $posi -= count($this->bars[$bar]);
            }

            if(in_array($this->bars[$bar][$posi],$this->wild)) {
                return $this->bars[$bar][$posi];
            }
        }

        return $this->bars[$bar][$pos];
    }

    function GetElLine($num) {
        $comb = [];
        foreach ($this->lines[$num] as $pos) {
            $comb[] = $this->symreplace($pos);
        }

        return $comb;
    }

    public function lightingLine($num = null) {

        if (is_null($num)) {
            for ($i = 1-count($this->anypay); $i <= $this->cline; $i++) {
                $a[$i] = $this->lightingLine($i);
            }
            ksort($a);
            return $a;
        }

        //scatter
        if ($num<=0){
            $light=0;
            if ($this->win[$num] > 0) {
                foreach ($this->sym() as $sym) {
                    $light = $light << 1;
                    if ($sym==$this->anypay[$num*-1]) {
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

    /*public function win() {

        $this->win_all = 0;
        $this->bonus_win = 0;

        $this->LineSymbol = array_fill(1, $this->lineCount, -1);
        $this->LineUseWild = array_fill(1, $this->lineCount, false);
        $this->LineWinLen = array_fill(1, $this->lineCount, 0);

        $this->win = array_fill(0, $this->lineCount+1, 0);
        //выигрыш по линиям
        for ($i = 1; $i <= $this->cline; $i++) {
            $this->win[$i] = $this->payLine($i) * $this->amount_line * $this->multiplier;
        }

        $count = array_count_values($this->sym());

        //anypay

        for($i=0;$i<count($this->anypay);$i++) {
            $this->win[-1*$i]=0;
            if (isset($count[$this->anypay[$i]])) {
                $this->win[-1*$i] = $this->pay($this->anypay[$i], $count[$this->anypay[$i]]) * $this->amount * $this->multiplier;
            }
        }

        $this->win_all = array_sum($this->win);

        $this->calcfreegames($count);
        $this->calcbonusgames($count);

        if ($this->bonusrun > 0) {
            $this->bonus_win=$this->calcbonus();
        }
    }*/
}

