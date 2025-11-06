<?php

class Slot_Igrosoft extends Slot_Igrosofts{

	protected $extralife;
	protected $bonus_chance;
    protected $bonus_last;


	public function __construct( $name) {


		parent::__construct($name);

		$this->extralife=arr::get($this->config,'extralife',-1);
		$this->bonus_chance=arr::get($this->config,'bonus_chance',[0,0,0,0,0,0]);
        $this->bonus_last=arr::get($this->config,'bonus_last',0);

	}


	public function correct_pos(){
	    return false;
	}




    public function sym($num = null) {
        if (empty($num)) {
            $r = [];
            for ($i = 1; $i <= $this->barcount * $this->heigth; $i++) {
                $r[$i] = $this->sym($i);
            }
            return $r;
        }

        return $this->pos[$num];

    }



    protected function payLeftRight($comb){

	$win1=$this->payleft($comb);
        $win2=$this->payRight($comb);

        return $win1['pay']>=$win2['pay'] ? $win1 : $win2;
    }


    protected function payRight($comb){
        $win=['pay'=>0,'sym'=>-1,'useWild'=>false,'len'=>0];
        //ищем wild
        $wildPos=[];
        foreach($this->wild as $w){
            foreach(array_keys($comb,$w) as $pos){
                $wildPos[]=$pos;
            }
        }



        $simple=[0,1,2,3,4];
        foreach ($wildPos as $pos){
            unset($simple[$pos]);
        }

        //первый в линии символ справа, который не wild
        $posSym=count($simple)>0 ? max($simple) : -1;
        //если комбинация состоит не из одних wild
        if ($posSym>=0){

            $sym=$comb[$posSym];
            if (!in_array($sym,$this->anypay)){
                $comb1=$comb;
                //если wild действует на текущий символ
                if (!in_array($sym,$this->wild_except)){
                    foreach($wildPos as $pos){
                        $comb1[$pos]=$sym;
                    }
                }



                $len=-1;
                $m=1;
                $useWild=false;
                if ($comb1[4]==$comb1[3]){
                    $len=-2;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(4,$wildPos) or in_array(3,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[4]==$comb1[3] and $comb1[3]==$comb1[2]){
                    $len=-3;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(4,$wildPos) or in_array(3,$wildPos) or in_array(2,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[4]==$comb1[3] and $comb1[3]==$comb1[2] and $comb1[2]==$comb1[1]){
                    $len=-4;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(4,$wildPos) or in_array(3,$wildPos) or in_array(2,$wildPos) or in_array(1,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if ($comb1[4]==$comb1[3] and $comb1[3]==$comb1[2] and $comb1[2]==$comb1[1] and $comb1[1]==$comb1[0]){
                    $len=-5;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) or in_array(4,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if($this->pay($sym,$len)>$win['pay']){
                    $win=['pay'=>$this->pay($sym,$len)*$m,'sym'=>$sym,'useWild'=>$useWild,'len'=>$len];
                }

            }
        }

        //комбинация состоит из одних wild
        $len=0;
        $wild=[];
        if (in_array(4,$wildPos) ){
            $len=-1;
            $wild=[$comb[4]];
        }

        if (in_array(4,$wildPos) and in_array(3,$wildPos)){
            $len=-2;
            $wild=[$comb[4],$comb[3]];
        }

        if (in_array(4,$wildPos) and in_array(3,$wildPos) and in_array(2,$wildPos)){
            $len=-3;
            $wild=[$comb[4],$comb[3],$comb[2]];
        }

        if (in_array(4,$wildPos) and in_array(3,$wildPos) and in_array(2,$wildPos) and in_array(1,$wildPos)){
            $len=-4;
            $wild=[$comb[4],$comb[3],$comb[2],$comb[1]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos) and in_array(4,$wildPos)){
            $len=-5;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3],$comb[4]];
        }


        foreach(array_unique($wild) as $sym){
            if ($this->pay($sym,$len)>0 and $this->pay($sym,$len)>=$win['pay'] and ! in_array($sym, $this->anypay)){
                $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>false,'len'=>$len];
            }
        }

        return $win;

    }





    //************************************************
	public function extralife(){

		return $this->extralife>=0 ? "|&extralife=$this->extralife" : "";
	}



	public function bonuscalcmath(){

		$win=[0,0,0,0,0,0];

		if ($this->bonus_param[3]>0){

			$v=$this->bonus_chance[0];
			$win[3]=5*$v;

			$v*=$this->bonus_chance[1];
			$win[3]+=5*$v;

			$v*=$this->bonus_chance[2];
			$win[3]+=5*$v;

			$v*=$this->bonus_chance[3];
			$win[3]+=5*$v;

			$v*=$this->bonus_chance[4];
			$win[3]+=5*$v;

			$v*=$this->bonus_chance[5];
			$win[3]+=50*$v;

			//Лень считать бонусы при extralife
			if ($this->extralife>=0){
				$win[3]*=2;
			}
		}


		if ($this->bonus_param[4]>0){
			$win[4]=$win[3];
		}

		if ($this->bonus_param[5]>0){
			$win[5]=$win[3];
		}


		return $win;

	}




	public function rope(){

		if($this->name=='fruitcocktail'){


		//urn '&reel=4|4|3|8|0|reel=5|0|6|7|0)
			$reel=[];
			for($i=1;$i<=$this->bonusrun;$i++){
				$a=$this->bonusdata[$i];
				$reel[]='reel='.implode('|',$a);
			}

			return '|&'.  implode('|',$reel);

		}



		$life= $this->extralife<=$this->amount ? 2 : 1;
		if ($this->extralife==-1){
			$life=1;
		}
		$a=[];



		for($i=1;$i<=5;$i++){
			$multiplier=$this->bonusdata[$i];
			$a[]="rope1={$multiplier}";
			if($multiplier==0){
				$life--;
				if ($life==0){
					break;
				}
			}
		}
		$str='|&'.implode('|', $a);

		if ($life>0){
			$str.="|l_bonus={$this->bonusdata[6]}";
		}


		return $str;

	}



	public function GetDoubleCard(){

		if ($this->name=='luckyhaunter'){
			$this->doubleclass=new Double_Luckyhaunter();
		}
		else{
			$this->doubleclass=new Double_Igrosoft();
		}

		$card=$this->doubleclass->GetCardDealer();
		$data=['card'=>$card];
		game::session()->flash($data);
		return $this->CardToSlot($card);
	}


	public function double(){

		if ($this->name=='luckyhaunter'){
			$this->doubleclass=new Double_Luckyhaunter();
		}
		else{
			$this->doubleclass=new Double_Igrosoft();
		}

        $this->doubleclass->game_id = $this->game_id;
        $this->doubleclass->name = $this->name;

		$this->doubleclass->dealer=game::data('card',51);

		parent::double();

	}


	public function CardToSlot($card){

		if ($this->name=='luckyhaunter'){
			return $card;
		}


		$num=card::num($card);
		$suit=card::suit($card);
		$num-=2;
		if($suit==1){
			$suitslot=4;
		}
		elseif($suit==2){
			$suitslot=3;
		}
		elseif($suit==3){
			$suitslot=1;
		}
		elseif($suit==4){
			$suitslot=2;
		}


		return ($num)+($suitslot-1)*13;
	}


		//Ускоренная версия calcmath
	public function calcmath1($show=false){



		$start=time();
		//ставка на линию
		$this->amount_line=1;
		//выбрано линий
		$this->cline=1;
		//ставка всего
		$this->amount=$this->cline*$this->amount_line;


		$this->bars=[];
		$kf=[];

		foreach($this->pay as $sym=>$pay){
			for($i=1;$i<=5;$i++){
				$kf[$i][$sym]=$pay[$i];
			}
		}

		for($i=1;$i<=5;$i++){
			arsort($kf[$i]);
		}

		foreach($kf as $num=>$ks){
			$c=0;
			foreach($ks as $sym=>$mn){

				if ($this instanceof Slot_Amarok){
					//amarok без бонус игры
					if ($this->bonus_param[5]==0){
						$c=mt_rand(ceil(sqrt($sym)), ceil(sqrt(24*$sym+2)) );
						if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
							$c=mt_rand(1,round(((6-$num)*1.5)));
						}
					}
					//amarok c бонус игрой
					else{
						$c=mt_rand(ceil(sqrt($sym)), ceil(sqrt(48*$sym+2)) );
						if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
							$c=mt_rand(1,round($num*1.5));
						}
					}
				}
				elseif ($this instanceof Slot_Igrosoft){
					if (in_array($this->name,['keks','luckyhaunter','luckydrink','rockclimber','gnome','resident'])){

						$call=count($this->pay)+1;
						$c=$call-mt_rand(ceil($sym), ceil(sqrt(20*$sym+2)) );
						$c=$c>0 ? $c :1;
						if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
							$c=mt_rand(1,4);
						}
					}
					// crazymonkey fruitcocktail fairyland island
					else{
						$call=count($this->pay)+1;
						$c=2*$call-mt_rand(ceil($sym), 2*ceil(sqrt(20*$sym+2)) );

						if ($sym==0){
							$c=$call-mt_rand(ceil($sym), ceil(sqrt(20*6+2)) );
						}
						$c=$c>0 ? $c :1;

						if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
							$c=mt_rand(1,4);
						}

					}
				}
				else{
					$call=count($this->pay)+1;
					$c=$call-mt_rand(1,$sym==0? 1: $sym);
					if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
						$c=mt_rand(1,round($num*1.5));
					}
				}




				for($i=1;$i<=$c;$i++){
					$this->bars[$num][]=$sym;
				}
			}

		}

		/*
		$this->bars[1]=[0,0,0,0,1,1,2,2,3,3,4,4,5,6,6,7,7,8];
		$this->bars[2]=[0,0,0,0,0,0,1,1,2,2,3,4,4,5,6,6,7,8];
		$this->bars[3]=[0,0,0,1,1,2,2,3,4,4,5,5,5,6,7,7,7,8];
		$this->bars[4]=[0,0,0,0,1,1,2,3,3,4,4,5,6,6,7,7,8];
		$this->bars[5]=[0,0,0,1,1,2,2,3,4,4,4,5,6,6,6,7,8,8];
		*/


		$uses=[];
		foreach($this->bars as $bar){
			foreach($bar as $num){
				$uses[]=$num;
			}
		}
		$uses=array_unique($uses);

		$this->reMapBar();


		$this->barcount=count($this->bars);


		$pos=[1=>0,2=>0,3=>0,4=>0,5=>0];
		$count_all=[];
		$count_all[1]=count($this->bars[1]);
		$count_all[2]=count($this->bars[2]);
		$count_all[3]=count($this->bars[3]);
		$count_all[4]=count($this->bars[4]);
		$count_all[5]=count($this->bars[5]);

		$spin_count=0;
		$out=0;
		$bonus=[0,0,0,0,0,0];
		$freespin=0;
		$freespinwincount=0;

		$spin_count=$count_all[1]*$count_all[2]*$count_all[3]*$count_all[4]*$count_all[5];
		$in=$spin_count*$this->amount_line*$this->cline;

		$csym=[];
		$csym[-1][0]=[$spin_count,0];
		foreach (array_keys($this->pay) as $sym){
			$csym[$sym][1]=[0,0];
			$csym[$sym][2]=[0,0];
			$csym[$sym][3]=[0,0];
			$csym[$sym][4]=[0,0];
			$csym[$sym][5]=[0,0];
		}

		$count=[];
		foreach($this->bars as $num=>$bar){
			$count[$num]=array_count_values($bar);
		}

		for($i=1;$i<=$this->barcount;$i++){
			foreach($uses as $num){
				if (!isset($count[$i][$num])){
					$count[$i][$num]=0;
				}
			}
		}


		$count_wild=[];
		$count_scatter=[];
        $count_bonus[]=[];
		foreach($count as $num=>$bar){
			$count_wild[$num]=0;
			foreach($this->wild as $sym){
				$count_wild[$num]+=isset($count[$num][$sym])? $count[$num][$sym] : 0 ;
			}

			$count_scatter[$num]=0;
			foreach($this->scatter as $sym){
				$count_scatter[$num]+=isset($count[$num][$sym])? $count[$num][$sym] : 0 ;
			}

            $count_bonus[$num]=0;
			foreach($this->bonus as $sym){
				$count_bonus[$num]+=isset($count[$num][$sym])? $count[$num][$sym] : 0 ;
			}

		}

		foreach(array_keys($this->pay) as $sym){
			$wild_already_pay[$sym][1]=0;
			$wild_already_pay[$sym][2]=0;
			$wild_already_pay[$sym][3]=0;
			$wild_already_pay[$sym][4]=0;
			$wild_already_pay[$sym][5]=0;

		}
		if ($count_scatter[1]>0){
			//freerun
			//555555555555
			$f[5]=3*$count_scatter[1]*3*$count_scatter[2]*3*$count_scatter[3]*3*$count_scatter[4]*3*$count_scatter[5];

			$all=$f[5];
			//444444444444
			$f[4]=0;
			for($i=1;$i<=5;$i++){
				$f[4]+=$all/(3*$count_scatter[$i])*($count_all[$i]-3*$count_scatter[$i]);
			}

			//33333333333
			$f[3]=0;
			for($i=1;$i<=5;$i++){
				for($j=$i+1;$j<=5;$j++){
					$f[3]+=$all/(3*$count_scatter[$i]*3*$count_scatter[$j])*($count_all[$i]-3*$count_scatter[$i])*($count_all[$j]-3*$count_scatter[$j]);

				}
			}

			//22222222222
			$f[2]=0;
			for($i=1;$i<=5;$i++){
				for($j=$i+1;$j<=5;$j++){
					for($k=$j+1;$k<=5;$k++){
						$d=$all/(3*$count_scatter[$i]*3*$count_scatter[$j]*3*$count_scatter[$k]);
						$d*=($count_all[$i]-3*$count_scatter[$i])*($count_all[$j]-3*$count_scatter[$j])*($count_all[$k]-3*$count_scatter[$k]);
						$f[2]+=$d;
					}
				}
			}


			$freespin=$this->free_games[5]*$f[5]+$this->free_games[4]*$f[4]+$this->free_games[3]*$f[3]+$this->free_games[2]*$f[2];
			$freespinwincount=$f[5]+$f[4]+$f[3];

		}



        if ($count_bonus[1]>0){
			//freerun
			//555555555555
			$bonus[5]=3*$count_bonus[1]*3*$count_bonus[2]*3*$count_bonus[3]*3*$count_bonus[4]*3*$count_bonus[5];

			$all=$bonus[5];
			//444444444444
			$bonus[4]=0;
			for($i=1;$i<=5;$i++){
				$bonus[4]+=$all/(3*$count_bonus[$i])*($count_all[$i]-3*$count_bonus[$i]);
			}

			//33333333333
			$bonus[3]=0;
			for($i=1;$i<=5;$i++){
				for($j=$i+1;$j<=5;$j++){
					$bonus[3]+=$all/(3*$count_bonus[$i]*3*$count_bonus[$j])*($count_all[$i]-3*$count_bonus[$i])*($count_all[$j]-3*$count_bonus[$j]);

				}
			}

			//22222222222
			$bonus[2]=0;
			for($i=1;$i<=5;$i++){
				for($j=$i+1;$j<=5;$j++){
					for($k=$j+1;$k<=5;$k++){
						$d=$all/(3*$count_bonus[$i]*3*$count_bonus[$j]*3*$count_bonus[$k]);
						$d*=($count_all[$i]-3*$count_bonus[$i])*($count_all[$j]-3*$count_bonus[$j])*($count_all[$k]-3*$count_bonus[$k]);
						$bonus[2]+=$d;
					}
				}
			}


		}



		//anypay
		foreach ($this->anypay as $sym){
			if (!isset($count[1][$sym])){
				continue;
			}
			//555555555555
			$f[5]=3*$count[1][$sym]*3*$count[2][$sym]*3*$count[3][$sym]*3*$count[4][$sym]*3*$count[5][$sym];

			$all=$f[5];
			//444444444444
			$f[4]=0;
			for($i=1;$i<=5;$i++){
				$f[4]+=$all/(3*$count[$i][$sym])*($count_all[$i]-3*$count[$i][$sym]);
			}

			//33333333333
			$f[3]=0;
			for($i=1;$i<=5;$i++){
				for($j=$i+1;$j<=5;$j++){
					$f[3]+=$all/(3*$count[$i][$sym]*3*$count[$j][$sym])*($count_all[$i]-3*$count[$i][$sym])*($count_all[$j]-3*$count[$j][$sym]);

				}
			}

			//22222222222
			$f[2]=0;
			for($i=1;$i<=5;$i++){
				for($j=$i+1;$j<=5;$j++){
					for($k=$j+1;$k<=5;$k++){
						$d=$all/(3*$count[$i][$sym]*3*$count[$j][$sym]*3*$count[$k][$sym]);
						$d*=($count_all[$i]-3*$count[$i][$sym])*($count_all[$j]-3*$count[$j][$sym])*($count_all[$k]-3*$count[$k][$sym]);
						$f[2]+=$d;
					}
				}
			}


			$win=[];
			for($i=2;$i<=5;$i++){
				$win[$i]=$f[$i]*$this->pay[$sym][$i];
				$csym[$sym][$i][0]=$this->pay[$sym][$i]>0 ? $f[$i] : 0;
				$csym[-1][0][0]-=$csym[$sym][$i][0];

			}
			$out+=array_sum($win);

		}


		//wild
		foreach ($this->wild as $sym){
			$count[1][$sym]=isset($count[1][$sym]) ? $count[1][$sym] :0;
			$count[2][$sym]=isset($count[2][$sym]) ? $count[2][$sym] :0;
			$count[3][$sym]=isset($count[3][$sym]) ? $count[3][$sym] :0;
			$count[4][$sym]=isset($count[4][$sym]) ? $count[4][$sym] :0;
			$count[5][$sym]=isset($count[5][$sym]) ? $count[5][$sym] :0;

			$p[5]=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];
			$p[4]=0;
			$p[3]=0;
			$p[2]=0;
			$p[1]=0;

			foreach ($this->pay as $symbol=>$kf) {

				if ($sym==$symbol){
					continue;
				}

				if (!isset($count[1][$symbol])){
					continue;
				}

				if (in_array($symbol,$this->wild_except)){

					//44444
					//WWWWS
					$p4=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$symbol];
					$p[4]+=$p4;

					//SWWWW
					$p4=$count[1][$symbol]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];
					$p[4]+=$p4;


					//33333
					//WWWS*
					$p3=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$symbol]*$count_all[5];
					$p[3]+=$p3;



					//*SWWW
					$p3=$count_all[1]*$count[2][$symbol]*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];
					$p[3]+=$p3;


					//SWWWS
					$p3=$count[1][$symbol]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$symbol];
					$p[3]+=$p3;



					//SWWW*
					foreach ($this->pay as $symbol1=>$kf1){

						if (!in_array($symbol1,$uses)){
							continue;
						}

						if ($symbol1==$sym or $symbol1==$symbol){
							continue;
						}

						if ($this->pay[$sym][3]>$this->pay[$symbol1][4]*$this->wild_multiplier or in_array($symbol1,$this->wild_except)){
							$p3=$count[1][$symbol]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$symbol1];
							$p[3]+=$p3;
							$wild_already_pay[$symbol1][4]+=$p3;
						}
					}

				}
				else{

					//44444444444444444444444444444
					//WWWWS
					if ($this->pay[$sym][4]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p4=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$symbol];
						$wild_already_pay[$symbol][5]+=$p4;
						$p[4]+=$p4;

					}

					//SWWWW
					if ($this->pay[$sym][4]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p4=$count[1][$symbol]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];
						$wild_already_pay[$symbol][5]+=$p4;
						$p[4]+=$p4;

					}


					//33333333333333333333333333333
					//555
					//WWWSS
					if ($this->pay[$sym][3]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p3=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$symbol]*($count[5][$symbol]+$count[5][$sym]);
						$wild_already_pay[$symbol][5]+=$p3;
						$p[3]+=$p3;

					}

					//SSWWW
					if ($this->pay[$sym][3]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p3=($count[1][$symbol]+$count[1][$sym])*$count[2][$symbol]*$count[3][$sym]*$count[4][$sym]*($count[5][$sym]);
						$wild_already_pay[$symbol][5]+=$p3;
						$p[3]+=$p3;

					}

					//SWWWS
					if ($this->pay[$sym][3]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p3=$count[1][$symbol]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*($count[5][$symbol]);
						$wild_already_pay[$symbol][5]+=$p3;
						$p[3]+=$p3;

					}

					//4444
					//WWWS*
					if ($this->pay[$sym][3]>$this->pay[$symbol][4]*$this->wild_multiplier){
						$p3=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$symbol]*($count_all[5]-$count[5][$symbol]-$count[5][$sym]);
						$wild_already_pay[$symbol][4]+=$p3;
						$p[3]+=$p3;

					}


					//*SWWW
					if ($this->pay[$sym][3]>$this->pay[$symbol][4]*$this->wild_multiplier){
						$p3=($count_all[1]-$count[1][$symbol]-$count[1][$sym])*$count[2][$symbol]*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];
						$wild_already_pay[$symbol][4]+=$p3;
						$p[3]+=$p3;

					}

					//4W4
					//SWWW*
					foreach ($this->pay as $symbol1=>$kf1){

						if (!isset($count[1][$symbol1])){
							continue;
						}

						if ($symbol1==$sym or $symbol1==$symbol){
							continue;
						}

						$pay1=in_array($symbol1,$this->wild_except) ? 0 : $this->pay[$symbol1][4];
						$pay=$this->pay[$symbol][4];

						$maxpay=max($pay1,$pay);
						$p3=$count[1][$symbol]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$symbol1];

						if ($this->pay[$sym][3]>$maxpay*$this->wild_multiplier){
							$p[3]+=$p3;
							$wild_already_pay[$symbol1][4]+=$p3;
							$wild_already_pay[$symbol][4]+=$p3;
						}
						else{
							if ($pay1>$pay){
								$wild_already_pay[$symbol][4]+=$p3;
							}
							else{
								$wild_already_pay[$symbol1][4]+=$p3;
							}
						}
					}

				}

			}


			$p[4]= $p[4]>0 ? $p[4] :0;
			$p[3]= $p[3]>0 ? $p[3] :0;
			$p[2]= $p[2]>0 ? $p[2] :0;
			$p[1]= $p[1]>0 ? $p[1] :0;


			$win=[];
			for($i=1;$i<=5;$i++){
				$win[$i]=$p[$i]*$this->pay[$sym][$i]*$this->amount_line;
				$csym[$sym][$i][0]=$this->pay[$sym][$i]>0 ? $p[$i] : 0;
				$csym[-1][0][0]-=$csym[$sym][$i][0];

			}
			$out+=array_sum($win);


		}

		//коррекция
		foreach ($this->wild as $w){
			foreach ($this->pay as $s=>$kf){
				foreach ($this->pay as $s1=>$kf1){

					if (!in_array($s,$uses)){
						continue;
					}
					if (!in_array($s1,$uses)){
						continue;
					}
					if (!in_array($w,$uses)){
						continue;
					}

					if ($w==$s or $w==$s1 or $s==$s1 ){
						continue;
					}

					if (in_array($s,$this->wild_except) or in_array($s1,$this->wild_except) ){
						continue;
					}


					//3W4
					//S W W S1 S1W
					$c=$count[1][$s]*$count[2][$w]*$count[3][$w]*$count[4][$s1]*($count[5][$s1]+$count[5][$w]);


					if ($this->pay[$s][3]>$this->pay[$s1][4]){
						$wild_already_pay[$s1][4]+=$c;
					}
					else{
						$wild_already_pay[$s][3]+=$c;
					}

					//4W3
					//SW S W W S1
					$c=($count[1][$s]+$count[1][$w])*$count[2][$s]*$count[3][$w]*$count[4][$w]*$count[5][$s1];
					if ($this->pay[$s][4]>$this->pay[$s1][3]){
						$wild_already_pay[$s1][3]+=$c;
					}
					else{
						$wild_already_pay[$s][4]+=$c;
					}

					//3W3
					//SW S W S1 S1W
					$c=($count[1][$s]+$count[1][$w])*$count[2][$s]*$count[3][$w]*$count[4][$s1]*($count[5][$s1]+$count[5][$w]);
					if ($this->pay[$s][3]>$this->pay[$s1][3]){
						$wild_already_pay[$s1][3]+=$c;
					}
					else{
						$wild_already_pay[$s][3]+=$c;
					}

					//!SW S W W S1
					$c=($count_all[1]-$count[1][$s]-$count[1][$w])*$count[2][$s]*$count[3][$w]*$count[4][$w]*$count[5][$s1];
					if ($this->pay[$s][3]>$this->pay[$s1][3]){
						$wild_already_pay[$s1][3]+=$c;
					}
					else{
						$wild_already_pay[$s][3]+=$c;
					}

					//S W W S1 !S1W
					$c=$count[1][$s]*$count[2][$w]*$count[3][$w]*$count[4][$s1]*($count_all[5]-$count[5][$s1]-$count[5][$w]);
					if ($this->pay[$s][3]>$this->pay[$s1][3]){
						$wild_already_pay[$s1][3]+=$c;
					}
					else{
						$wild_already_pay[$s][3]+=$c;
					}

				}
			}
		}

		//not wild
		foreach(array_keys($this->pay) as $sym){

			if (in_array($sym,$this->wild) or in_array($sym,$this->anypay)){
				continue;
			}

			$count[1][$sym]=isset($count[1][$sym]) ? $count[1][$sym] :0;
			$count[2][$sym]=isset($count[2][$sym]) ? $count[2][$sym] :0;
			$count[3][$sym]=isset($count[3][$sym]) ? $count[3][$sym] :0;
			$count[4][$sym]=isset($count[4][$sym]) ? $count[4][$sym] :0;
			$count[5][$sym]=isset($count[5][$sym]) ? $count[5][$sym] :0;

			$w=[];
			$w[5]=0;
			$w[4]=0;
			$w[3]=0;
			$w[2]=0;
			$w[1]=0;
			//обычные символы использующие wild
			if (!in_array($sym,$this->wild_except)){
				$w[5]=$count_wild[5];
				$w[4]=$count_wild[4];
				$w[3]=$count_wild[3];
				$w[2]=$count_wild[2];
				$w[1]=$count_wild[1];
			}

			//left to right no wild
			$p[5]=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];

			//SSSS*
			$p[4]=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*($count_all[5]-$count[5][$sym]-$w[5]);
			//*SSSS
			$p[4]+=($count_all[1]-$count[1][$sym]-$w[1])*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];

			//SSS!*
			//*!SSS
			//!SSS!
			$p[3]=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*($count_all[4]-$count[4][$sym]-$w[4])*$count_all[5];
			$p[3]+=($count_all[1])*($count_all[2]-$count[2][$sym]-$w[2])*$count[3][$sym]*$count[4][$sym]*$count[5][$sym];
			$p[3]+=($count_all[1]-$count[1][$sym]-$w[1])*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*($count_all[5]-$count[5][$sym]-$w[5]);



			$p[4]= $p[4]>0 ? $p[4] :0;
			$p[3]= $p[3]>0 ? $p[3] :0;
			$p[2]= 0;
			$p[1]= 0;


			$win=[];
			for($i=1;$i<=5;$i++){
				$win[$i]=$p[$i]*$this->pay[$sym][$i]*$this->amount_line;
				$csym[$sym][$i][0]=$this->pay[$sym][$i]>0 ? $p[$i] : 0;
				$csym[-1][0][0]-=$csym[$sym][$i][0];

			}
			$out+=array_sum($win);


			//with wild

			if (in_array($sym,$this->wild_except)){
				continue;
			}


			//всего
			$pw[5]=($count[1][$sym]+$count_wild[1])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count[4][$sym]+$count_wild[4])*($count[5][$sym]+$count_wild[5]);
			$pw[5]-=$p[5];
			$pw[5]-=($count_wild[1]*$count_wild[2]*$count_wild[3]*$count_wild[4]*$count_wild[5]);
			$pw[5]-=$wild_already_pay[$sym][5];

			$pw[4]=($count[1][$sym]+$count_wild[1])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count[4][$sym]+$count_wild[4])*($count_all[5]-$count_wild[5]-$count[5][$sym]);
			$pw[4]+=($count_all[1]-$count_wild[1]-$count[1][$sym])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count[4][$sym]+$count_wild[4])*($count[5][$sym]+$count_wild[5]);
			$pw[4]-=$p[4];
			$pw[4]-=$count_wild[1]*$count_wild[2]*$count_wild[3]*$count_wild[4]*($count_all[5]-$count_wild[5]-$count[5][$sym]);
			$pw[4]-=($count_all[1]-$count_wild[1]-$count[1][$sym])*$count_wild[2]*$count_wild[3]*$count_wild[4]*$count_wild[5];
			$pw[4]-=$wild_already_pay[$sym][4];

			$pw[3]=($count[1][$sym]+$count_wild[1])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count_all[4]-$count_wild[4]-$count[4][$sym])*$count_all[5];
			$pw[3]+=$count_all[1]*($count_all[2]-$count_wild[2]-$count[2][$sym])*($count[3][$sym]+$count_wild[3])*($count[4][$sym]+$count_wild[4])*($count[5][$sym]+$count_wild[5]);
			$pw[3]+=($count_all[1]-$count_wild[1]-$count[1][$sym])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count[4][$sym]+$count_wild[4])*($count_all[5]-$count_wild[5]-$count[5][$sym]);
			$pw[3]-=$p[3];
			$pw[3]-=$count_wild[1]*$count_wild[2]*$count_wild[3]*($count_all[4]-$count_wild[4]-$count[4][$sym])*$count_all[5];
			$pw[3]-=$count_all[1]*($count_all[2]-$count_wild[2]-$count[2][$sym])*$count_wild[3]*$count_wild[4]*$count_wild[5];
			$pw[3]-=($count_all[1]-$count_wild[1]-$count[1][$sym])*$count_wild[2]*$count_wild[3]*$count_wild[4]*($count_all[5]-$count_wild[5]-$count[5][$sym]);
			$pw[3]-=$wild_already_pay[$sym][3];

			$win=[];
			for($i=3;$i<=5;$i++){
				$pw[$i]=$pw[$i]<0 ? 0 : $pw[$i];
				$win[$i]=$pw[$i]*$this->pay[$sym][$i]*$this->amount_line*$this->wild_multiplier;
				$csym[$sym][$i][1]=$this->pay[$sym][$i]>0 ? $pw[$i] : 0;
				$csym[-1][0][0]-=$csym[$sym][$i][1];
			}

			$out+=array_sum($win);

		}

		//считаем freerun
		$z=($out)/$in;
		$free_spin_percent=$freespinwincount/$spin_count;
		$fic=$freespin;
		$winfree=0;


		$freemultiplier=$this->free_multiplier;

		$n=0;
		$i=1;
		$freespin_all=0;
		while ($fic>1) {
			$freespin_all+=$fic*$freemultiplier;
			$fic*=$free_spin_percent;

			if ($this->free_multiplier_mode=='inc'){
				$freemultiplier++;
			}
			$i++;

			if ($i>1000){
				$freespin_all=PHP_INT_MAX;
				$in=1;
				break(1);
			}

		}
		$z=$out/($in-$freespin_all);
		$winfree=$freespin_all*$z;
		$out+=$winfree;

		$wb=$this->bonuscalcmath();
		$winbonus=0;
		$bonus_spin=0;
		for($i=0;$i<=5;$i++){
			$winbonus+=$bonus[$i]*$wb[$i];
			$bonus_spin+=$bonus[$i]* ($wb[$i]>0 ? 1 : 0);
		}


		$out+=$winbonus;

		$time=time()-$start;

		$lose=round($csym[-1][0][0]/$spin_count,2);

		$s="
			spin_count: $spin_count
			in: $in
			out: $out
			free win: $winfree
			z: ".round($out/$in,4)."
			bonus: ".implode(' ',$bonus)."(".round($bonus_spin/$spin_count*100,2)."% chance ".round($winbonus/$in*100,2)."% payout)
			freespin: $freespin  (".round($free_spin_percent*100,2)."% chance ".round($winfree/$in*100,2)."% payout)
			lose: $lose

			\r\nbars:\r\n".print_a1($this->bars,true)."
			\r\ncount:\r\n".print_a1i($count,true)."
			config:\r\n
\$l['bars']=[1=>[".implode(',',$this->bars[1])."],
			2=>[".implode(',',$this->bars[2])."],
			3=>[".implode(',',$this->bars[3])."],
			4=>[".implode(',',$this->bars[4])."],
			5=>[".implode(',',$this->bars[5])."]
];

";


		$s.=print_b($csym,true);
		$z=round($out/$in,4);


		if ($show){
			echo $s;
			return true;
		}

		if ($z>0.95 or $z<0.9){
			return false;
		}

		$dir=DOCROOT.'z'.DIRECTORY_SEPARATOR.$this->group.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR;
		th::force_dir($dir);
		$file=$dir.$z.'.z';
		file_put_contents($file,$s);



	}


}

