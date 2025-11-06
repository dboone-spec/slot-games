<?php

class Slot_Agt_Christmas extends Slot_Agt
{

    public function spin($mode = null)
    {
        if($this->is_buy) {

            $exit = false;

            //todo do random if need
            $collections=[];

            $infinity=50;

            do {

                for($i=1;$i<=count($this->bars);$i++) {
                    foreach($this->bars[$i] as $y=>$k) {
                        if(in_array($k,$this->scatter)) {
                            break;
                        }
                    }
                    $this->pos[$i] = $y-mt_rand(0,$this->heigth-1);
                }

                $this->correct_pos();
                $this->win();
                $this->win_all = 0;
                $this->win=[0=>0];

                //минимально возможный выигрыш
                if ($this->win_all == 0) {
                    $exit = true;
                }

                $infinity--;

                if($infinity==0) {
                    $exit=true;
                }

            } while (!$exit);

            $count = array_count_values($this->sym());
            $this->calcfreegames($count);
            return;
        }

        return parent::spin($mode);
    }

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

}

