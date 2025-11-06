<?php

class Slot_Igrosofts extends Slot_Calc{


    protected $percent=[];
    protected $mainZ;
    protected $bonusZ;
    protected $game_id;
    protected $bonus_chance;
    protected $bonus_last;
    protected $bonus_replace;
    public $bonusPay=0;

    public function __construct( $name) {

        parent::__construct('igrosoft', $name);

        $this->percent=$this->config['percent'];
        $this->mainZ=$this->config['mainz'];
        $this->bonusZ=$this->config['bonusz'];
        $this->bonus_chance=$this->config['bonus_chance'];
        $this->bonus_last=$this->config['bonus_last'];
        $this->bonus_replace=$this->config['bonus_replace'];

		if(isset($this->config['sets'])) {

            $game = new Model_Game(['provider'=>'our','name'=>$name]);
            $off_game =  new Model_Office_Game(['game_id'=>$game->id,'office_id'=>OFFICE]);

            $this->bonus_chance = $this->config['sets'][$off_game->z*100]['bonus_chance']??$this->bonus_chance;
            $this->bonusZ = $this->config['sets'][$off_game->z*100]['bonusz']??$this->bonusZ;

            $this->game_id = $game->id;
        }

	}




    public function bet($mode = null) {

        $this->bonusPay=0;
        $c=parent::bet($mode);

    }


    //вращаем
    //в pos храним тупо символы, для совместимости
    public function spin($mode = null) {

        $c=$this->getCounter('igrosoft', $this->name);
        $c->in=$c->in==0 ? 1 : $c->in;
        $z=$c->out/$c->in;
        $allz=($c->out+$c->bonus)/$c->in;

        $use=0;
        if ($z>$this->mainZ){
            $use=1;
        }

        if ($allz>$this->mainZ+$this->bonusZ){
            $use=1;
        }


        for ($i = 1; $i <= $this->barcount; $i++) {
			$pos=$this->getRandWeight($this->percent[$use],3);

            $this->pos[$i]=$pos[0];

            $this->pos[$i+$this->barcount]=$pos[1];
            $this->pos[$i+$this->barcount*2]=$pos[2];


        }


        $this->win();


    }

     public function calcbonus() {


        $c=$this->getCounter('igrosoft', $this->name);
        $bonusZ=$c->bonus/$c->in;
        $mainZ=($c->out)/$c->in;

        /*
        //если все норм
        $canPayZ=[$this->bonusZ];

        //превышение по всем счетчикам
        if ($mainZ+$bonusZ >$this->mainZ+$this->bonusZ){
            $canPayZ[]=0.01;
        }

        //если есть превышение по основным счетчикам,
        if ($mainZ >$this->mainZ){
            //уменьшаем общий процент бонуса до величины, которая не позволит превысить счетчики
            $canPayZ[]=$this->bonusZ+$this->mainZ-$mainZ;
        }

        //превышение по счетчикам бонусов
        if($bonusZ>$this->bonusZ){
            $canPayZ[]=0.01;
        }

        //сколько денег можем выплатить
        $canPayZ=min($canPayZ);


        $canPay=$canPayZ*$c->in-$c->bonus;
         */
        $canPay=$this->bonusZ*$c->in-$c->bonus;
        $canPay=$canPay>0 ? $canPay : 0;


        $count=count($this->bonus_chance);
        $this->bonusdata=array_fill(0,$count, 0);
        $replace=true;

        for($i=0;$i<$count-1;$i++){
            $t=$this->IfChance($this->bonus_chance[$i],mt_rand(1,10),0);
            if($canPay>(array_sum($this->bonusdata)+$t)*$this->amount){
                $this->bonusdata[$i]=$t;
            }

        }

        //последний выигрыш
        if($canPay>(array_sum($this->bonusdata)+$this->bonus_last)*$this->amount){
            $this->bonusdata[$count-1]=$this->IfChance($this->bonus_chance[$count-1],$this->bonus_last,0);
        }

        for($i=0;$i<$count-1;$i++){
            if($this->bonus_chance[$i]>0){
                $t=$this->IfChance($this->bonus_replace,mt_rand(10,50),0);
                if($t>0 and $canPay>(array_sum($this->bonusdata)+$t-$this->bonusdata[$i])*$this->amount){
                    $this->bonusdata[$i]=$t;
                }
            }
        }


        //считаем бонус
        $life= $this->amount>=$this->extralife ? 1 : 0;

        for($i=0;$i<$count;$i++){
            if ($this->bonusdata[$i]==0){
                $life--;
            }

            if ($life<0){
                $this->bonusdata[$i]=0;
            }

        }

        $this->bonusPay=array_sum($this->bonusdata)*$this->amount;

        return $this->bonusPay;


    }




    public function gen(){

	$w=[0=>0.2498,
		  1=>0.2132,
		  2=>0.1734,
		  3=>0.1274,
		  4=>0.0835,
		  5=>0.0620,
		  6=>0.0450,
		  7=>0.0189,
		  8=>0.0268];


	echo "start\r\n";

	for($i=1;$i<=100000;$i++){

	    $w[0]=mt_rand(15,25);
		$w[1]=mt_rand($w[0]-5,$w[0]);
		$w[2]=mt_rand($w[1]-3,$w[1]);
		$w[3]=mt_rand(6,$w[2]);
		$w[4]=mt_rand(5,$w[3]);
		$w[5]=mt_rand(2,$w[4]);
		$w[7]=mt_rand(1,10*$w[5])/10;
		//$w[7]=mt_rand(1,10*$w[6])/10;
        $w[6]=mt_rand(13,17)/10*$w[7];
		$w[8]=mt_rand(2,8)/2;

  /*
        $w[8]=mt_rand(15,25);
		$w[7]=mt_rand($w[8]-5,$w[8]);
		$w[6]=mt_rand($w[7]-3,$w[7]);
		$w[5]=mt_rand(6,$w[6]);
		$w[4]=mt_rand(5,$w[5]);
		$w[3]=mt_rand(2,$w[4]);
		$w[2]=mt_rand(1,10*$w[3])/10;
		//$w[1]=mt_rand(1,10*$w[2])/10;
        $w[1]=mt_rand(3,10)/10*$w[2];
		$w[0]=mt_rand(1,6)/2;
*/
        echo json_encode($w);
        echo "\r\n";

	    $w=$this->to100($w);


	    $this->genfile($w);
        echo "\r\n";
        echo "\r\n";





	}

    }

    //Перевод начальной вероятности в истинную
    public function trueChance($v1){

	$v2=[];
	foreach($v1 as $sym1=>$vs1){
	    foreach($v1 as $sym2=>$vs2){
		if($sym1==$sym2){
		    $v2[$sym1][$sym2]=0;
		}
		else{
		    $v2[$sym1][$sym2]=$vs2/(1-$vs1);
		}
	    }
	}

	$v3=[];
	foreach($v1 as $sym1=>$vs1){
	    foreach($v1 as $sym2=>$vs2){
		foreach($v1 as $sym3=>$vs3){
		    if($sym1==$sym2 or $sym1==$sym3 or $sym2==$sym3){
			$v3[$sym1][$sym2][$sym3]=0;
		    }
		    else{
			$v3[$sym1][$sym2][$sym3]=$vs3*$v2[$sym1][$sym2]*$v1[$sym1]/(1-$vs2-$vs1);
		    }
		}
	    }
	}




	$all=array_fill(0,count($v1),0);

	foreach ($v3 as $sym1=>$lvl2){
	    foreach($lvl2 as $sym2=>$lvl3){
		foreach($lvl3 as $sym3=>$v){
		    $all[$sym1]+=$v/3;
		    $all[$sym2]+=$v/3;
		    $all[$sym3]+=$v/3;
		}
	    }

	}

	return $all;
    }



    public function genfile($w){

	$percent=$this->trueChance($w);
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $count = [];
        $count[1] = count($this->pay);
        $count[2] = count($this->pay);
	$count[3] = count($this->pay);
	$count[4] = count($this->pay);
	$count[5] = count($this->pay);


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
	$bonusver=0;

        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[6] = $pos[1];
                            $this->pos[7] = $pos[2];
                            $this->pos[8] = $pos[3];
                            $this->pos[9] = $pos[4];
                            $this->pos[10] = $pos[5];
                            $this->correct_pos();
                            $this->win();

			    $ver=$percent[$pos[1]]*$percent[$pos[2]]*$percent[$pos[3]]*$percent[$pos[4]]*$percent[$pos[5]];


                            $spin_count++;
                            $in += $this->amount;
                            $out += $this->win_all*$ver;


                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;
                            $winScatter += $this->win[0];

                            $csym[$this->LineSymbol[1]][abs($this->LineWinLen[1])][$this->LineUseWild[1]] ++;


			    //считаем бонус
			    $m=1;
			    $countB=0;
			    foreach($pos as $bar=>$sym){
				if(in_array($sym,$this->bonus)){
				    $m*=3;
				    $countB++;
				}
			    }
			    $bonusver+=$ver*$m*$this->bonus_param[$countB];
                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            //echo "$spin_count/$allSpin lost:$time сек\r\n";
        }



/*
        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);
*/
        $s = "
			spin_count: $spin_count
			out: $out
			bonus: " . implode(' ', $bonus) . "
			time: $time
			begin percent: ".json_encode($w)."
			real percent: ".json_encode($percent)."
			bonus: ".$bonusver;


    echo "$out $bonusver\r\n";


	$name=round($out, 4);
	$name= str_replace('.',',', $name);

    if ($out<0.90 and $bonusver>0.020 and $bonusver<0.023){
        file_put_contents(DOCROOT.'z/'.$name, $s);

    }
 }

}

