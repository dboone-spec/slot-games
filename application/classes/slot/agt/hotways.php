<?php

class Slot_Agt_Hotways extends Slot_Megaways {

    public function win()
    {
        parent::win();

        $oldwin=$this->win;

        $new=[];
        foreach($oldwin as $l=>$v) {
            $new[-$l]=$v;
        }

        $this->win=$new;
    }

    public function lightingLine($num=null) {

        $result = [];
        if (is_null($num)) {
            foreach ($this->win as $sym => $win) {
                $result[$sym] = $this->lightingLine($sym);
            }

            return $result;
        }

        if (($this->win[$num] ?? 0) > 0) {

            foreach ($this->screen as $numBar => $bar) {
                if ($this->LineWinLen[-$num] >= 0) {
                    $result[$numBar] = array_keys($bar, -$num);

                    if(!in_array(-$num,$this->anypay)) {
                        foreach ($this->wild as $wild) {
                            $result[$numBar] = array_merge($result[$numBar], array_keys($bar, $wild));
                        }

                        if(empty($result[$numBar])) {
                            break;
                        }
                    }


                    if(in_array(-$num,$this->anypay)) {
                        foreach ($this->anypay as $anypay){
                            $result[$numBar] = array_intersect($result[$numBar],array_keys($bar, $anypay));
                        }
                    }
                }
            }
            return array_values($result);
        }

        return [];
    }

    /**
     * стоит ли переделывать на маску?
     */
    /*public function lightingLine($num = null) {

        if (is_null($num)) {
            for ($i = 1-count($this->pay); $i <= 0; $i++) {
                $a[$i] = $this->lightingLine($i);
            }
            return $a;
        }

        $num=$num*-1;

        //like scatter
        $light=0;
        if ($this->win[$num]??0 > 0) {
            foreach ($this->screen as $barNum=>$bar) {
                foreach($bar as $sym) {
                    $light = $light << 1;
                    if ($sym==$num) {
                        $light++;
                    }
                }
            }
        }
        return $light;
    }*/
}

