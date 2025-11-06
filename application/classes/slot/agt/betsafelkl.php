<?php

class Slot_Agt_Betsafelkl extends Slot_Agt
{


    public function spin($mode = null)
    {


        if (auth::$user_id == 9203790) {


            
            for ($i = 1; $i <= $this->barcount; $i++) {
                $this->pos[$i] = math::random_int(0, count($this->bars[$i]) - 1);
            }

            if (true) {

                $spins = Kohana::$config->load('script');
                $num = dbredis::instance()->get('BetsafelklNum');
                $num++;
                if (!isset($spins[$num])) {
                    $num = 0;
                }
                dbredis::instance()->set('BetsafelklNum', $num);

                $spin = $spins[$num];
                $poses = [];


                $add = array_fill(1, $this->barcount, 0);

                $line = $this->config['lines'][$spin['line']];
                for ($x = 0; $x < $this->barcount; $x++) {
                    for ($y = 0; $y < $this->heigth; $y++) {
                        if ($line[$y][$x] > 0) {
                            $add[$x + 1] -= $y;
                        }
                    }
                }

                foreach ($this->bars as $num => $bar) {
                    $poses = array_keys($bar, $spin['sym']);

                    if ($num <= $spin['count']) {
                        $this->pos[$num] = $poses[array_rand($poses)] + $add[$num];
                    } else {

                        do {
                            $pos = $this->pos[$num] = mt_rand(0, count($bar)) + $add[$num];
                        } while (in_array($pos, $poses));

                    }
                }

            }


            $this->correct_pos();
            $this->win();
        }
        else{
            parent::spin();
        }
    }


}
