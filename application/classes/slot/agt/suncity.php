<?php

class Slot_Agt_Suncity extends Slot_Agt{

    protected $_was_freerun=false;

    public function SetFreeRunMode() {
        $this->bars=$this->barFree;
        $this->isFreerun=true;
        $this->bettype='free';
        $this->total_win_free = game::data('total_win_free', 0);
        $this->freeCountAll = $this->getTotalFreeCount();
        $this->freeCountCurrent = $this->getCurrentFreeCount($this->freeCountAll);

        $this->amount = game::data('amount');
        $this->cline = game::data('lines');
        $this->amount_line = $this->amount / $this->cline;
        $this->multiplier = game::data('multiplier', $this->free_multiplier);
    }


    public function spin($mode = null)
    {

        if($this->is_buy) {

            $exit = false;

            //todo do random if need
            $collections=[];

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

                //минимально возможный выигрыш
                if ($this->win_all == 0) {
                    $exit = true;
                }

            } while (!$exit);

            $this->freerun=$this->genFGcount();
            return;
        }

        $end_freegames=$this->isFreerun && $this->freeCountAll>0 && $this->freeCountCurrent+1==$this->freeCountAll;

        if(!$end_freegames) {
            parent::spin($mode);
        }

        $start_freegames=!$this->isFreerun && $this->freerun;

        if($this->is_buy && $start_freegames) {
            throw new Exception('cant buy, it is fg mode already');
        }

        if($start_freegames) {
            $this->freerun=$this->genFGcount();
        }
        elseif($this->_was_freerun && !$end_freegames) {
            //если выпала бонусная игра внутри бонуса, но еще не время завершения - прокручиваем барабаны
            $this->spin();
        }
        elseif($end_freegames) {

            for ($i = 1; $i <= $this->barcount; $i++) {
                $this->pos[$i] = math::random_int(0, count($this->bars[$i]) - 1);
            }

            //подкручиваем барабаны для остановки
            for($i=1;$i<=count($this->barFree);$i++) {

                $scatters=$this->scatter;
                $scatters_count=count(array_filter($this->barFree[$i],function($v) use($scatters) {return in_array($v,$scatters);}));

                if($scatters_count<=0) {
                    continue;
                }

                $stop_pos=mt_rand(0,$scatters_count-1);

                $x=0;

                foreach($this->barFree[$i] as $y=>$k) {
                    if(in_array($k,$this->scatter) && ($x++)==$stop_pos) {
                        break;
                    }
                }
                $this->pos[$i] = $y-mt_rand(0,$this->heigth-1);
            }

            $this->correct_pos();
            $this->win();
        }
    }

    public function genFGcount() {
        if(empty($this->_weights)) {
            $this->_gen_weights();
        }

		logfile::create(date('Y-m-d H:i:s') . ' ['.auth::$user_id.']','genFGcount');
        logfile::create(date('Y-m-d H:i:s') . ' ['.auth::$user_id.']'.PHP_EOL.print_r(debug_backtrace(0,7),1),'genFGcountTrace');

        return math::getRandWeight($this->_weights)+1;
    }

    protected $_weights=[];

    protected function _gen_weights($i=0) {

        //вероятность выхода из фригеймов
        $d=0.0611413043;
		//$d=0.15;

        if($i==500) {
            return $this->_weights;
        }
        $this->_weights[$i]=$d*(1-array_sum($this->_weights));

        $i++;

        return $this->_gen_weights($i);
    }

    public function calcfreegames($count) {
        parent::calcfreegames($count);

        $this->_was_freerun=$this->freerun>0;

        if($this->isFreerun) {
            $this->freerun=0;
        }
    }

}

