<?php

class Slot_Agt_Egypt extends Slot_Agt{

    public $minipos;
    public $winMiniComb;
    public $minibarsRate=1;

    public function __construct($name) {
        parent::__construct($name);

        $this->miniBars = $this->config['miniBars'];
        $this->minipos = [];
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

    public function spin($mode = null)
    {

        //вращение минибарабанов
        for ($i = 1; $i <= count($this->miniBars); $i++) {
            $this->minipos[$i] = math::array_rand($this->miniBars[$i]);
        }

        //основное вращение
        parent::spin($mode);

    }

    public function correct_pos()
    {
        parent::correct_pos();

        foreach ($this->minipos as $num => $pos) {
            $c = count($this->miniBars[$num]);
            if ($pos > $c - 1) {
                $this->minipos[$num] -= $c;
            }

            if ($pos < 0) {
                $this->minipos[$num] += $c;
            }
        }

        $this->winMiniComb=[];

        for($i=1;$i<=count($this->minipos);$i++) {
            $pos = $this->minipos[$i];

            if ($pos >= count($this->minipos)) {
                $pos -= count($this->minipos);
            }

            $this->winMiniComb[]=$this->miniBars[$i][$pos];
        }
    }

    public function win()
    {
        parent::win();

        $count = array_count_values($this->winMiniComb);

        $rate=0;

        foreach($count as $sym=>$cnt) {
            if($cnt<count($this->miniBars)) {
                continue;
            }

            $rate+=$sym*$cnt;
        }

        if($rate>0) {
            $this->minibarsRate=$rate;
            $this->win_all*=$this->minibarsRate;
        }


        $this->correctMaxWin($this->win_all);
    }

}

