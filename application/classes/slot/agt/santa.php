<?php

class Slot_Agt_Santa extends Slot_Agt{

    public $barcount = 4;

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
            case 1: return 0b1000;
            case 2: return 0b1100;
            case 3: return 0b1110;
            case 4: return 0b1111;
            case -1: return 0b0001;
            case -2: return 0b0011;
            case -3: return 0b0111;
            case -4: return 0b1111;
        }

        return 0;
    }
}

