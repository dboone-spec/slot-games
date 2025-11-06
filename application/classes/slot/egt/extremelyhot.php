<?php

class Slot_Egt_Extremelyhot extends Slot_Egt{

    

    public function win(){

            $r=parent::win();

            $s =[ $this->sym(1), $this->sym(2), $this->sym(3),$this->sym(4), $this->sym(5),
                  $this->sym(6), $this->sym(7),$this->sym(8),$this->sym(9), $this->sym(10),
                 $this->sym(11), $this->sym(12), $this->sym(13),$this->sym(14), $this->sym(15),] ;
            

            $c=array_count_values($s);
            $count=reset($c);
            $sym=key($c);

            if ($count==15 and in_array($sym,[1,2,3,4])){

                    foreach($this->win as $line=>$win){
                            $this->win[$line]*=5;
                    }
                    $this->win_all=array_sum($this->win);
                    
                    return $r;
            }
            
            $s =[ $this->sym(1), $this->sym(2), $this->sym(3),$this->sym(4), 
                  $this->sym(6), $this->sym(7),$this->sym(8),$this->sym(9), 
                 $this->sym(11), $this->sym(12), $this->sym(13),$this->sym(14), ] ;
            

            $c=array_count_values($s);
            $count=reset($c);
            $sym=key($c);

            if ($count==12 and in_array($sym,[1,2,3,4])){

                    foreach($this->win as $line=>$win){
                            $this->win[$line]*=4;
                    }
                    $this->win_all=array_sum($this->win);
                    
                    return $r;
            }
            
            $s =[ $this->sym(1), $this->sym(2), $this->sym(3),
                  $this->sym(6), $this->sym(7),$this->sym(8),
                 $this->sym(11), $this->sym(12), $this->sym(13), ] ;
            

            $c=array_count_values($s);
            $count=reset($c);
            $sym=key($c);

            if ($count==9 and in_array($sym,[1,2,3,4])){

                    foreach($this->win as $line=>$win){
                            $this->win[$line]*=3;
                    }
                    $this->win_all=array_sum($this->win);
                    
                    return $r;
            }
            
            
            return $r;
            
    }
}

