<?php

class Slot_Agt_6megaice40 extends Slot_Agt{

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
            case 1: return 0b100000;
            case 2: return 0b110000;
            case 3: return 0b111000;
            case 4: return 0b111100;
            case 5: return 0b111110;
            case 6: return 0b111111;
            case -1: return 0b000001;
            case -2: return 0b000011;
            case -3: return 0b000111;
            case -4: return 0b001111;
            case -5: return 0b011111;
            case -6: return 0b111111;
        }

        return 0;
    }





}

