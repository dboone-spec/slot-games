<?php

class Slot_Egt_Likeadiamond extends Slot_Egt{



    public function symreplace($num) {

        $bar = $num % $this->barcount;
        if ($bar == 0) {
            $bar = $this->barcount;
        }

        $pos = $this->pos[$bar] + floor(($num - 0.01) / $this->barcount);

        if ($pos >= count($this->bars[$bar])) {
            $pos -= count($this->bars[$bar]);
        }


        $startBar=$bar-1;
        $startBar=$startBar>0 ? $startBar : 1;

        $endBar=$bar+1;
        $endBar=$endBar>$this->barcount ? $this->barcount : $endBar;


        $row=floor(($num - 0.01) / $this->barcount);

        $startRow=$row;
        $startRow=$startRow>0 ? $startRow : 0;

        $endRow=$row;
        $endRow=$endRow>$this->heigth-1 ? $this->heigth-1 : $endRow;




        for($iBar=$startBar; $iBar<=$endBar; $iBar++){
            for($iRow=$startRow; $iRow<=$endRow; $iRow++){

                $posSym=$this->pos[$iBar]+$iRow;
                $c = count($this->bars[$iBar]);
                if ($posSym>$c-1 ){
                    $posSym-=$c;
                }

                if(in_array($this->bars[$iBar][$posSym], $this->replaceBar)){
                    $v = implode(',',[$bar-1,floor(($num - 0.01) / $this->barcount)]);
                    if(!in_array($v,$this->replaced_symbols_in_bar)) {
                        $this->replaced_symbols_in_bar[]=$v;
                    }
                    return $this->bars[$iBar][$posSym];
                }
            }
        }





        return $this->bars[$bar][$pos];
    }

}

