<?php

class Slot_Agt_Doubleice extends Slot_Agt{

    public $barcount = 3;

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
            case 1: return 0b100;
            case 2: return 0b110;
            case 3: return 0b111;
        }

        return 0;
    }



    	public function win(){

            $r=parent::win();

            $s =[ $this->sym(1), $this->sym(2), $this->sym(3),
                 $this->sym(4), $this->sym(5), $this->sym(6),
                 $this->sym(7), $this->sym(8), $this->sym(9),] ;


            $c=array_count_values($s);
            $count=reset($c);
            $sym=key($c);


            if ($count==9 and in_array($sym,[0,1,2,3])){

                    foreach($this->win as $line=>$win){
                            $this->win[$line]*=2;
                    }
                    $this->win_all=array_sum($this->win);

                    $this->correctMaxWin($this->win_all);
            }


            return $r;
    }

}

