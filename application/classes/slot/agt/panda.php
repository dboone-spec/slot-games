<?php

class Slot_Agt_Panda extends Slot_Agt{

    public $barcount = 3;

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
            case 1: return 0b100;
            case 2: return 0b110;
            case 3: return 0b111;
            case -1: return 0b001;
            case -2: return 0b011;
            case -3: return 0b111;
        }

        return 0;
    }


}

