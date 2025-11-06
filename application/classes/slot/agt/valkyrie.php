<?php

class Slot_Agt_Valkyrie extends Slot_Agt
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

    public function correct_pos()
    {
        parent::correct_pos();

        if($this->isFreerun) {
            $this->pos[4]=$this->pos[3]=$this->pos[2];
            $this->replaceBar=[$this->bars[2][$this->pos[2]]];
        }
    }

    public function calcfreegames($count) { //free run, все scatter складываются и работают как одинаковые
        $this->freerun = 0;
        $cf=0;
        foreach ($this->scatter as $sym) {
            if (isset($count[$sym])) {
                $cf+=$count[$sym];
            }
        }

        if(!isset($this->free_games[$cf])) {
            $cf=count($this->free_games)-1;
        }

        $this->freerun = $this->free_games[$cf];
    }

    public function win()
    {
        parent::win();

        $count = array_count_values($this->sym());


        for($i=0;$i<count($this->anypay);$i++) {
            $this->win[-1*$i]=0;
            if (isset($count[$this->anypay[$i]])) {
                $this->win[-1*$i] = $this->pay($this->anypay[$i], $count[$this->anypay[$i]]) * $this->amount * $this->multiplier;
            }
        }

        $this->win_all = array_sum($this->win);
    }

    public function symreplace($num)
    {

        $bar = $num % $this->barcount;
        if ($bar == 0) {
            $bar = $this->barcount;
        }

        $pos = $this->pos[$bar] + floor(($num - 0.01) / $this->barcount);

        if ($pos >= count($this->bars[$bar])) {
            $pos -= count($this->bars[$bar]);
        }

        if(!$this->isFreerun) {
            return $this->bars[$bar][$pos];
        }

        if(!in_array($bar,[2,3,4])) {
            return $this->bars[$bar][$pos];
        }

        if(empty($this->replaceBar)) {
            throw new Exception('wrong game logic calc');
        }

        for($i=0;$i<$this->heigth;$i++) {
            $posi = $this->pos[$bar] + $i;

            if ($posi >= count($this->bars[$bar])) {
                $posi -= count($this->bars[$bar]);
            }

            $this->bars[$bar][$posi]=$this->replaceBar[0];
        }

        return $this->replaceBar[0];

    }

}

