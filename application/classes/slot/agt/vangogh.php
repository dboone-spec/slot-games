<?php

class Slot_Agt_Vangogh extends Slot_Agt{

    
    public  $width;
    protected $probability=[];

    protected $currentState=[];
    protected $fgPercent=-1;
    protected $isShowFGSymbol=false;

    public function __construct($name) {
        
        parent::__construct($name);
        $this->width= arr::get($this->config,'width',5);
        $this->probability=arr::get($this->config,'probability',[]);
        $this->barcount=$this->width;
        $this->fgPercent=arr::get($this->config,'fg_percent',-1);

        $this->anypay=range(0,count($this->config['pay'])-1);

    }


    public function fillLines() {
        $this->lines=[];
    }

    public function spin($mode = NULL) {

        $this->bars=[];
        $this->pos=array_fill(1,$this->width,0);

        //Если режим FG заменяем вероятности на FG
        if ( $this->isFreerun && isset($this->config['probabilityFG']) ) {
            $this->probability=$this->config['probabilityFG'];
        }

        for($j=0;$j<$this->heigth;$j++){
            for($i=0;$i<$this->width;$i++){
                $this->bars[$i+1][]=$this->getRandWeight($this->probability);
            }
        }
        $this->win();

    }

    public function lightingLine($num = null) {


        if (is_null($num)) {
            for ($i = 1-count($this->anypay); $i <= 0; $i++) {
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
                        $light++;
                    }
                }
            }
            return $light;
        }

       throw new LogicException();
    }

    //текущий выигрыш
    public function win() {

        $this->win_all = 0;
        $this->bonus_win = 0;
        $this->replaced_symbols_in_bar=[];

        $count = array_count_values($this->sym());
        $this->calcfreegames($count);

        //если выпали FG переделываем барабаны до просчета.
        if ($this->isShowFGSymbol){
            //заменяем случайный символ на символ FG
            $bar=$this->random_int(1,$this->width);
            $pos=$this->random_int(0,$this->heigth-1);
            $this->bars[$bar][$pos]=$this->scatter[0];
        }


        if ($this->bonusrun > 0) {
            $this->bonus_win=$this->calcbonus();
        }

        for ($i = 1-count($this->anypay); $i <= 0; $i++) {
            $this->win[$i]=0;
        }

//        $this->win = [-3=>0,-2=>0,-1=>0,0=>0];

        $count = array_count_values($this->sym());

        foreach ($count as $sym=>$amount) {

            if ($this->pay($sym,$amount)>0){
                $this->win[-$sym]=$this->amount*$this->pay($sym,$amount);
                //echo "$sym,$amount\r\n";
            }
        }

        $this->win_all = array_sum($this->win);


    }
    
    


    public function calcfreegames($count) {

        $this->isShowFGSymbol=false;

        if ($this->random_gen()<$this->fgPercent){

            $this->isShowFGSymbol=true;
            $this->freerun = 0;
            //всегда берем 0 символ
            if ($this->isFreerun && isset($this->config['free_games_in_free_games'][0]) ){
                $this->freerun = $this->config['free_games_in_free_games'][0];
            }
            else{
                $this->freerun = $this->free_games[0];
            }

        }

    }


    public function inc(&$a,$num){

        if ($num>=$this->width*$this->heigth){
            return false;
        }

        $a[$num]++;

        if ($a[$num]>$this->maxS){
            $a[$num]=$this->minS;
            return $this->inc($a,$num+1);
        }

        return true;
    }

    protected $minS,$maxS;

    public function calc() {

        ob_end_clean();
        $start = time();

        $totalWeight=array_sum($this->probability);
        $percent=[];
        foreach($this->probability as $sym=>$weight){
            $percent[$sym]=$weight/$totalWeight;
        }



        $this->pos=array_fill(1,$this->width,0);

        $this->minS=min(array_keys($this->probability));
        $this->maxS=max(array_keys($this->probability));

        $syms=array_fill(0,$this->width*$this->heigth,$this->minS);
        $win=0;

        $all=pow(count($this->probability), $this->width*$this->heigth);
        $in=0;
        $out=0;
        $count=0;
        $this->amount=1;
        do{

            $this->bars=array_fill(0,$this->width,[]);
            for($i=1;$i<=$this->width*$this->heigth;$i++){

                $num=$i % $this->width;
                if ($num==0){
                    $num=$this->barcount;
                }
                $this->bars[$num][]=$syms[$i-1];
            }
            $this->win();
            $in+=$this->amount;
            $out+=array_sum($this->win);
            $count++;

            if(  ($count % 10000000 ==0 )|| ($count==50000 )|| ($count==500000 ) ){
                $time = floor((time() - $start) / $count * ($all - $count) );
                echo "$count/$all lost:$time сек\r\n";
            }

        } while ($this->inc($syms,0));


        $rtp=round($out/$in,10);

        echo "
               in:$in
               out:$out
               RTP:$rtp";

    }



    public function numberDrop($sets,$max){
        $a=$sets;
        $n = count($a);
        $sets=[];

        while (1)
        {
            /*Печать и выход. Print end exit.*/
            if (count($a)<=$max){
                $sets[]=$a;
            }

            if ($a[0] == $n) break;

            /*Элемент в нулевом индексе нашего динамического
            массива на текущий момент.
            First element of our dynamic array*/
            $first_elem = $a[0];

            /*Размер массива на текущий момент. Length of an array*/
            $c = count($a) - 1;
            $i = 0;
            while ($i != count($a) - 1)
            {
                /*Найдем элемент меньше первого. Here we search min. element.*/
                if ($a[$i] < $first_elem)
                {
                    $first_elem = $a[$i];
                    $min_elem = $i;
                }
                $i++;
            }
            if (empty($min_elem)) $min_elem = 0;

            /*Перенос элемента  "1". Here we transfer "1". */
            $a[$min_elem]+= 1;
            $a[$c]-= 1;

            /*Обрежем массив и найдем сумму его элементов. We cut the array
            * and count the sum of elements.*/
            array_splice($a, $min_elem + 1);
            $array_sum = array_sum($a);

            /*Добавим в массив единицы заново с учетом суммы.
            Here we add 1 (fill)  to the array
            ( taking into account the sum ).*/
            for ($j = 0; $j != $n - $array_sum; $j++) $a[] = 1;

            /*Обнулим переменную. Unset min_elem.*/
            unset($min_elem);
        }
        return $sets;

    }

    public function permutation($arr)
    {
        if (is_array($arr) && count($arr) > 1) {
            foreach ($arr as $k => $v) {
                $answer[][] = $v;
            }
            do {
                foreach ($arr as $k => $v) {
                    foreach ($answer as $key => $val) {
                        if (!in_array($v, $val)) {
                            $tmpArr[] = array_merge(array($v), $val);
                        }
                    }
                }
                $answer = $tmpArr;
                unset($tmpArr);
            } while (count($answer[0]) != count($arr));
            return $answer;
        } else
            $answer = $arr;
        return $answer;
    }


    protected $_symSets=[];
    public function symSets($size){
        if (!isset($this->symSets[$size])) {
            $syms=array_keys($this->probability);
            $result=[];
            foreach ($this->permutation($syms) as $el ){
                $r=array_slice($el,0,$size);
                //sort($r);
                $result[]=$r;
            }
            $this->symSets[$size]=array_unique($result,SORT_REGULAR);

        }

        return $this->symSets[$size];

    }


    public function calcFast() {


        echo "FG start 1 of ". ( (int) 1/$this->fgPercent ) ;

        $totalWeight=array_sum($this->probability);
        $percent=[];
        foreach($this->probability as $sym=>$weight){
            $percent[$sym]=$weight/$totalWeight;
        }


        //фригеймы
        $fg=0;

        $symsCount=count($this->probability);
        $sets=array_fill(0,$this->width*$this->heigth,1);
        $sets=$this->numberDrop($sets,$symsCount);

        $in=0;
        $out=0;
        $curSpin=0;
        $all=pow($symsCount,$this->width*$this->heigth);
        $amountOfBet=1;
        $allV=0;
        $crc2=0;

        $start=time();

        foreach ($sets as $set){
           // echo "\r\n".implode(' ',$set);
            $k=1;
            foreach (array_count_values($set) as $one){
                $k*=$this->f($one);
            }
            foreach ($this->symSets(count($set)) as $symSet){
             //   echo " | ".implode(' ',$symSet);


                $symsAmount=[];
                $i=0;
                foreach ($set as $amount){
                    $symsAmount[$symSet[$i]]=$amount;
                    $i++;
                }

                $count=array_sum($symsAmount);
                $spinCount=1;
                foreach($symsAmount as $amount){
                    $spinCount*=$this->c($amount,$count);
                    $count-=$amount;
                }
                $spinCount/=$k;


                $win=0;
                $v=1;
                foreach($symsAmount as $sym=>$amount){
                    $win+=$amountOfBet*$this->pay($sym,$amount);
            //        echo "$sym $amount $win\r\n";
                    $v*=pow($percent[$sym],$amount);
                }
                $v/=pow(1/$symsCount ,$this->width*$this->heigth);

            //    echo "win $win\r\n\r\n";
                //$v*=$spinCount;
                $allV+=$v;

                $in+=$amountOfBet*$spinCount*$v;
                $out+=$win*$spinCount*$v;
                $curSpin+=$spinCount;

            }

        }

        $crc=$curSpin-pow($symsCount,$this->width*$this->heigth);
        $rtp=round($out/$in,10);

        $fg=$curSpin*( max(0,$this->fgPercent) );

        echo "  
                in:$in
               out:$out
               RTP:$rtp
                FG:$fg
               CRC:$crc
         spinCount:$curSpin";


    }
    
    public function f(int $n){
        if ($n==1) {
            return 1;
        }

        return $this->f($n-1)*$n;
    }

    protected $_c=[];

    public function c($k,$n){
        if ($k==$n){
            return 1;
        }

        if (!isset($this->_c[$k][$n])){
            $this->_c[$k][$n]=($this->f($n))/($this->f($k)*$this->f($n-$k));
        }

        return $this->_c[$k][$n];

    }


    
    
    
    

}

