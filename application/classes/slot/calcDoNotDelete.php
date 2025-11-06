<?php

class Calc_dontdelete {
    
	//быстрая версия
	public function calcmathq() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        

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
		
		
		$countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];
		
		$oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);
		
		foreach($this->bars as $bnum=>$bar){
			$countSym[$bnum]= array_count_values($bar);
			$this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
		}
		
		$count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);
		
		$sc=[0,0,0,0,0,0];

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;
							
                            $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*$countSym[3][$this->sym(8)]*$countSym[4][$this->sym(9)]*$countSym[5][$this->sym(10)];



                            $this->win[0]=0;	


                            $this->win_all = array_sum($this->win);
							
							
							
                            $in += $this->amount*$mn;
                            $out += $this->win_all*$mn;

							
                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;
                            $winScatter += $this->win[0];

                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;
                           

                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

		
		$sc3=0;
		$sc4=0;
		$sc5=0;
		
		
		foreach($this->anypay as $sym){
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*27*$this->pay($sym,3);
			
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*81*$this->pay($sym,4);
			
			$sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*243*$this->pay($sym,5);
		}
		
		
		echo "$sc3+$sc4+$sc5";
		
        $time = time() - $start;

		$winScatter=$sc3+$sc4+$sc5;
		
		$out+=$winScatter;
		
        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 8) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);

        
    }
	
	
	
	
	//быстрая версия
	public function calcmathq45() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        

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
		
		
		$countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];
		
		$oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);
		
        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
                $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
        }

        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);
		
		$sc=[0,0,0,0,0,0];

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;
							
                            $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*$countSym[3][$this->sym(8)]*$countSym[4][$this->sym(9)]*$countSym[5][$this->sym(10)];
                            $this->win[0]=0;	
                            $this->win_all = array_sum($this->win);
							
							
                            $in += $this->amount*$mn;
                            $out += $this->win_all*$mn;

							
                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;
                            $winScatter += $this->win[0];

                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;
                           

                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

		
		$sc3=0;
		$sc4=0;
		$sc5=0;
		
		
		foreach($this->anypay as $sym){
			
			$countSym[1][$sym]=$countSym[1][$sym] ?? 0;
			$countSym[2][$sym]=$countSym[2][$sym] ?? 0;
			$countSym[3][$sym]=$countSym[3][$sym] ?? 0;
			$countSym[4][$sym]=$countSym[4][$sym] ?? 0;
			$countSym[5][$sym]=$countSym[5][$sym] ?? 0;
			
			
			
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*64*$this->pay($sym,3);
			
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*256*$this->pay($sym,4);
			
			$sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*1024*$this->pay($sym,5);
		}
		
		
		echo "$sc3+$sc4+$sc5";
		
        $time = time() - $start;

		$winScatter=$sc3+$sc4+$sc5;
		
		$out+=$winScatter;
		
        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 4) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);

        
    }

    
    
    
    
	//быстрая версия для wild на весь барабан 3*5
        //You must to use slot_calc::sym and slot_calc::GetElLine
        //Function doesn't work correctly if there is wild on 1st or 5th reel
	public function calcmathq1() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        

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
		
		
        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);
		
        $a=$oc[1]*$oc[2]*$oc[3]*$oc[4]*$oc[5];



        $wildR1=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];	
        $wildR2=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];	

        foreach($this->bars as $bNum=>$bar){
                foreach($bar as $sNum=>$sym){
                        if (in_array($sym,$this->wild)){
                                $elNum=$sNum-1;
                                if ($elNum<0){
                                        $elNum+=$oc[$bNum];
                                }
                                $wildR1[$bNum][]=$this->bars[$bNum][$elNum];

                                $elNum=$sNum-2;
                                if ($elNum<0){
                                        $elNum+=$oc[$bNum];
                                }
                                $wildR2[$bNum][]=$this->bars[$bNum][$elNum];


                                $elNum=$sNum+1;
                                if ($elNum>$oc[$bNum]-1){
                                        $elNum-=$oc[$bNum];
                                }
                                $wildR1[$bNum][]=$this->bars[$bNum][$elNum];

                                $elNum=$sNum+2;
                                if ($elNum>$oc[$bNum]-1){
                                        $elNum-=$oc[$bNum];
                                }
                                $wildR2[$bNum][]=$this->bars[$bNum][$elNum];

                        }
                }
        }

        foreach($wildR1 as $bNum=>$R1){
                $WildR1Count[$bNum]= array_count_values($R1);


        }




        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
                $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
        }





        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);
		
        $sc=[0,0,0,0,0,0];

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;

                            $line=[1=>$this->sym(6), 2=>$this->sym(7), 3=>$this->sym(8), 4=>$this->sym(9), 5=>$this->sym(10),];
                            $mn=$countSym[1][$line[1]]*$countSym[2][$line[2]]*$countSym[3][$line[3]]*$countSym[4][$line[4]]*$countSym[5][$line[5]];

                            //magic. It doesn't work if there is wild in 1st or 5th reel
                            if ($this->LineWinLen[1]==3 and $this->LineUseWild[1]==0){
                                    $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*
                                            $countSym[3][$line[3]]*
                                            ($countSym[4][$line[4]] - ($WildR1Count[4][$line[4]] ?? 0) )*
                                                    $countSym[5][$this->sym(10)];
                                    
                            }

                            if ($this->LineWinLen[1]==2 and $this->LineUseWild[1]==0){
                                    $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*
                                            ($countSym[3][$line[3]]  - ($WildR1Count[3][$line[3]] ?? 0) )*
                                            $countSym[4][$line[4]]*
                                                    $countSym[5][$this->sym(10)];
                            }


                                            /*
                            $mn=($countSym[1][$line[1]] -($WildR1Count[1][$line[1]] ??0))*
                                    ($countSym[2][$line[2]] -($WildR1Count[2][$line[2]] ??0))*
                                    ($countSym[3][$line[3]] -($WildR1Count[3][$line[3]] ??0))*
                                    ($countSym[4][$line[4]] -($WildR1Count[4][$line[4]] ??0))*
                                    ($countSym[5][$line[5]] -($WildR1Count[5][$line[5]] ??0));
                            */
                            $this->win[0]=0;	
                            $this->win[-1]=0;
                            $this->win[-2]=0;


                            $this->win_all = array_sum($this->win);



                            $lineSym=[1=>'b', 2=>'b', 3=>'b', 4=>'b', 5=>'b',];



                            if ($this->LineUseWild[1]){
                                    foreach($line as $bNum1=>$ss){
                                            if ($ss==$this->LineSymbol[1]){
                                                    $lineSym[$bNum1]='s';
                                            }
                                            if (in_array($ss,$this->wild)){
                                                    $lineSym[$bNum1]='w';
                                            }

                                    }

                                    $mn=1;


                                    foreach ($lineSym as $bNum=>$label){
//									echo "  $bNum=>".$line[$bNum]."   ";
                                            if ($label=='s'){
                                                    $mn*=$countSym[$bNum][$this->LineSymbol[1]];
                                            }

                                            if ($label=='w'){

                                                    $dt=$countSym[$bNum][$this->wild[0]]*3;
                                                    $dt-=$WildR1Count[$bNum][$this->LineSymbol[1]] ?? 0;
                                                    $mn*=$dt;
                                            }

                                            if ($label=='b'){
                                                    $mn*=$countSym[$bNum][$line[$bNum]]-($WildR1Count[$bNum][$line[$bNum]] ?? 0);

                                            }
                                    }

                            //echo "$mn\r\n";
                            }


                            /*
                            if ($this->LineWinLen[1]==3 and $this->LineSymbol[1]==2 and $this->LineUseWild[1]==0){
                                    echo implode(' ',$line)." $mn \r\n";
                            }
                            */
                            /*
                            if ($this->win_all >0){
                                    echo "------------------------------\r\n$mn\r\n";
                                    echo implode(' ',$line)."\r\n";
                            }
                            */
                            $in += $this->amount*$mn;


                            $out += $this->win_all*$mn;
                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;
                            
                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

		
		$sc3=0;
		$sc4=0;
		$sc5=0;
		
		
		
		foreach($this->anypay as $sym){
			
			$countSym[1][$sym] = $countSym[1][$sym] ?? 0;
			$countSym[2][$sym] = $countSym[2][$sym] ?? 0;
			$countSym[3][$sym] = $countSym[3][$sym] ?? 0;
			$countSym[4][$sym] = $countSym[4][$sym] ?? 0;
			$countSym[5][$sym] = $countSym[5][$sym] ?? 0;
			
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*27*$this->pay($sym,3);
			
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*81*$this->pay($sym,4);
			
			$sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*243*$this->pay($sym,5);
		}
		
		

		
		$in=$oc[1]*$oc[2]*$oc[3]*$oc[4]*$oc[5] ;
		
		
		
		echo "$sc3+$sc4+$sc5";
		
        $time = time() - $start;

		$winScatter=$sc3+$sc4+$sc5;
		
		$out+=$winScatter;
		
        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 4) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);

        
    }


  
        	
	//быстрая версия для wild на весь барабан
        //for 5*4
        //You must to use slot_calc::sym and slot_calc::GetElLine
        //Function doesn't work correctly if there is wild on 1st or 5th reel
	public function  calcmathq45() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        

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
		
		
        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);
		
        $a=$oc[1]*$oc[2]*$oc[3]*$oc[4]*$oc[5];



        $wildR1=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];	
        $wildR2=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];	

        foreach($this->bars as $bNum=>$bar){
                foreach($bar as $sNum=>$sym){
                        if (in_array($sym,$this->wild)){
  
                                $elNum=$sNum+1;
                                if ($elNum>$oc[$bNum]-1){
                                        $elNum-=$oc[$bNum];
                                }
                                $wildR1[$bNum][]=$this->bars[$bNum][$elNum];
                                
                                $elNum=$sNum-1;
                                if ($elNum<0){
                                        $elNum+=$oc[$bNum];
                                }
                                $wildR1[$bNum][]=$this->bars[$bNum][$elNum];

 
                                $elNum=$sNum-2;
                                if ($elNum<0){
                                        $elNum+=$oc[$bNum];
                                }
                                $wildR1[$bNum][]=$this->bars[$bNum][$elNum];

                        }
                }
        }

        foreach($wildR1 as $bNum=>$R1){
                $WildR1Count[$bNum]= array_count_values($R1);
        }

        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
                $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
        }





        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);
		
        $sc=[0,0,0,0,0,0];

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;

                            $line=[1=>$this->sym(6), 2=>$this->sym(7), 3=>$this->sym(8), 4=>$this->sym(9), 5=>$this->sym(10),];
                            $mn=$countSym[1][$line[1]]*$countSym[2][$line[2]]*$countSym[3][$line[3]]*$countSym[4][$line[4]]*$countSym[5][$line[5]];

                            
                            if ($this->LineWinLen[1]==3 and $this->LineUseWild[1]==0){
                                    $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*
                                            $countSym[3][$line[3]]*
                                            ($countSym[4][$line[4]] - ($WildR1Count[4][$line[4]] ?? 0) )*
                                                    $countSym[5][$line[5]];
                                    
                            }



                            
                            $mn=($countSym[1][$line[1]] -($WildR1Count[1][$line[1]] ??0))*
                                ($countSym[2][$line[2]] -($WildR1Count[2][$line[2]] ??0))*
                                ($countSym[3][$line[3]] -($WildR1Count[3][$line[3]] ??0))*
                                ($countSym[4][$line[4]] -($WildR1Count[4][$line[4]] ??0))*
                                ($countSym[5][$line[5]] -($WildR1Count[5][$line[5]] ??0));
                            
                            
                            //magic. It doesn't work if there is wild in 1st or 5th reel
                            if ($this->LineWinLen[1]==2 and $this->LineUseWild[1]==0){
                                    $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*
                                            ($countSym[3][$line[3]]  - ($WildR1Count[3][$line[3]] ?? 0) )*
                                            $countSym[4][$line[4]]*
                                                    $countSym[5][$line[5]];
                            }
                            
                            
                            $this->win[0]=0;	
                            $this->win[-1]=0;
                            $this->win[-2]=0;


                            $this->win_all = array_sum($this->win);



                            $lineSym=[1=>'b', 2=>'b', 3=>'b', 4=>'b', 5=>'b',];



                            if ($this->LineUseWild[1]){
                                    foreach($line as $bNum1=>$ss){
                                            if ($ss==$this->LineSymbol[1]){
                                                    $lineSym[$bNum1]='s';
                                            }
                                            if (in_array($ss,$this->wild)){
                                                    $lineSym[$bNum1]='w';
                                            }

                                    }

                                    $mn=1;


                                    foreach ($lineSym as $bNum=>$label){
//									echo "  $bNum=>".$line[$bNum]."   ";
                                            if ($label=='s'){
                                                $dt=$countSym[$bNum][$this->LineSymbol[1]];
                                                $dt-=$WildR1Count[$bNum][$this->LineSymbol[1]] ?? 0;
                                                $mn*=$dt;
                                                    
                                                   
                                                    
                                            }

                                            if ($label=='w'){

                                                    $dt=$countSym[$bNum][$this->wild[0]]*4;
                                                    //$dt-=$WildR1Count[$bNum][$this->LineSymbol[1]] ?? 0;
                                                    $mn*=$dt;
                                                    
                                                   
                                            }

                                            if ($label=='b'){
                                                    $mn*=$countSym[$bNum][$line[$bNum]]-($WildR1Count[$bNum][$line[$bNum]] ?? 0);
                                            }
                                    }

                            //echo "$mn\r\n";
                            }
                            

                            
                            $in += $this->amount*$mn;


                            $out += $this->win_all*$mn;
                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;
                            
                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

		
		$sc3=0;
		$sc4=0;
		$sc5=0;
		
		
		
		foreach($this->anypay as $sym){
			
			$countSym[1][$sym] = $countSym[1][$sym] ?? 0;
			$countSym[2][$sym] = $countSym[2][$sym] ?? 0;
			$countSym[3][$sym] = $countSym[3][$sym] ?? 0;
			$countSym[4][$sym] = $countSym[4][$sym] ?? 0;
			$countSym[5][$sym] = $countSym[5][$sym] ?? 0;
			
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*64*$this->pay($sym,3);
			
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*256*$this->pay($sym,4);
			
			$sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*1024*$this->pay($sym,5);
		}
		
		

		
		$in=$oc[1]*$oc[2]*$oc[3]*$oc[4]*$oc[5] ;
		
		
		
		echo "$sc3+$sc4+$sc5";
		
        $time = time() - $start;

		$winScatter=$sc3+$sc4+$sc5;
		
		$out+=$winScatter;
		
        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 4) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);

        
    }
}