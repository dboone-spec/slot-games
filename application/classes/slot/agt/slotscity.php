<?php
//TODO не удалять файл или функцию calcmathq1
// Если очень надо - то переименуйте
class Slot_Agt_Slotscity extends Slot_Agt{

    public $bonus_win_mask;
    public $bonus_win_lines=[];
    // модификатор бонус игры
    public $mud;
    public $replacenum;
    protected $_calc_extra=false;
    protected $bonus;

    public function __construct($name) {
        parent::__construct($name);
    }

    public function calcbonus()
    {
        return 0;
    }

    public function calcbonusgames($count)
    {
        return 0;
    }

    public function symreplace($num) {

        $bar = $num % $this->barcount;
        if ($bar == 0) {
            $bar = $this->barcount;
        }

        $pos = $this->pos[$bar] + floor(($num - 0.01) / $this->barcount);

        if ($pos >= count($this->bars[$bar])) {
            $pos -= count($this->bars[$bar]);
        }

        for($i=0;$i<$this->heigth;$i++) {

            $posi = $this->pos[$bar] + $i;

            if ($posi >= count($this->bars[$bar])) {
                $posi -= count($this->bars[$bar]);
            }

            if(in_array($this->bars[$bar][$posi],$this->wild)) {
                return $this->bars[$bar][$posi];
            }
        }

        return $this->bars[$bar][$pos];
    }

    function GetElLine($num) {
        $comb = [];
        foreach ($this->lines[$num] as $pos) {
            $comb[] = $this->symreplace($pos);
        }

        return $comb;
    }

    public function lightingLine($num = null) {

        if (is_null($num)) {
            for ($i = 1-count($this->anypay); $i <= $this->cline; $i++) {
                $a[$i] = $this->lightingLine($i);
            }
            ksort($a);
            return $a;
        }

        //scatter
        if ($num<=0){
            $light=0;
            if ($this->win[$num] > 0) {
                foreach ($this->sym() as $sym) {
                    $light = $light << 1;
                    if ($sym==$this->anypay[$num*-1]) {
                        $light ++;
                    }
                }
            }
            return $light;
        }

        switch ($this->LineWinLen[$num]) {
            case 0: return 0;
            case 1: return 0b10000;
            case 2: return 0b11000;
            case 3: return 0b11100;
            case 4: return 0b11110;
            case 5: return 0b11111;
            case -1: return 0b00001;
            case -2: return 0b00011;
            case -3: return 0b00111;
            case -4: return 0b01111;
            case -5: return 0b11111;
        }

        return 0;
    }

    public function win() {

        $this->win_all = 0;
        $this->bonus_win = 0;

        $this->LineSymbol = array_fill(1, $this->lineCount, -1);
        $this->LineUseWild = array_fill(1, $this->lineCount, false);
        $this->LineWinLen = array_fill(1, $this->lineCount, 0);

        $this->win = array_fill(0, $this->lineCount+1, 0);
        //выигрыш по линиям
        for ($i = 1; $i <= $this->cline; $i++) {
            $this->win[$i] = $this->payLine($i) * $this->amount_line * $this->multiplier;
        }

        $count = array_count_values($this->sym());

        //anypay

        for($i=0;$i<count($this->anypay);$i++) {
            $this->win[-1*$i]=0;
            if (isset($count[$this->anypay[$i]])) {
                $this->win[-1*$i] = $this->pay($this->anypay[$i], $count[$this->anypay[$i]]) * $this->amount * $this->multiplier;
            }
        }

        $this->win_all = array_sum($this->win);

        $this->calcfreegames($count);
        $this->calcbonusgames($count);

        if ($this->bonusrun > 0) {
            $this->bonus_win=$this->calcbonus();
        }
    }
    
    
    
    
    //быстрая версия
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


    
    
    
}

