<?php

abstract class Slot_CalcOLD extends math{
	
	public $barcount=5;
	protected $heigth=3;
	public $bars=[];
	
	protected $name;
	protected $group;
	protected $config;
	public $pos=[];
	protected $lines;
        protected $lineCount;
	protected $pay;
	protected $pay_rule;
	protected $anypay;
	public $wild;
	protected $wild_multiplier;
	protected $wild_except;
	public $scatter;
	protected $free_games;
	protected $free_multiplier;
        protected $bonus;
	protected $bonus_param;
	protected $free_mode;
	protected $multiplier;
	protected $free_multiplier_mode;

        //Комбинациия в лини на барабане
        protected $lineComb;

        //общая ставка
	public $amount=0;
	public $amount_line=0;
	public $cline=0;
	public $win=[];
	public $win_all=0;
	//сколько будет выиграно в бонус игре (не входит в win_all)
	public $bonus_win=0;
	public $win_scatter=0;
	public $freerun=0;
	public $is_freerun;
	public $bonusrun=0;
	public $bonusdata;
	
	//всего выиграно на фри спинах
	public $total_win_free=0;
	// Начать халявные вращения
	public $mode_free_begin=false;
	// закончить халявные вращения
	public $mode_free_end=false;
	//осталось халявных вращений
	public $free_count_play=0;
	//сыграно халявных вращений
	public $free_count_played=0;
	//выигрыш получен путем вращения можно запускать бонус игру
	public $simple_win=false;
	
	//выигрыш по линиям
	public $LineSymbol=[];
	//выигрыш по линиям c wild 0/1
	public $LineUseWild=[];
	//длина комбинации в линии
	public $LineWinLen=[];
	
        //true если игра запущена в режиме фриспинов, пока ни где не используется
        //TODO возможно нужно переделать обработку фриспинов внутри класса, а может и так заебись
        public $modeFreeRun=false;
	
	public function __construct($group,$name) {
		
            $this->group=$group;
            $this->name=$name;
            $this->config=Kohana::$config->load("$group/$name");

            $this->bars=$this->config['bars'];
            $this->barcount=count($this->bars);
            $this->barFree=arr::get($this->config,'barFree',$this->bars); 

            $this->lines=$this->config['lines'];
            $this->lineCount=count($this->lines);
            $this->pay=$this->config['pay'];
            $this->pay_rule=arr::get($this->config,'pay_rule','left');
            if (!is_array($this->pay_rule)){
                    $this->pay_rule=[$this->pay_rule];
            }

            $this->anypay=arr::get($this->config,'anypay',[]);
            $this->wild=arr::get($this->config,'wild',[]);
            if (!is_array($this->wild)){
                    $this->wild=[$this->wild];
            }
            $this->wild_multiplier=arr::get($this->config,'wild_multiplier',1);
            $this->wild_except=arr::get($this->config,'wild_except',[]);
            if (!is_array($this->wild)){
                    $this->wild=[$this->wild];
            }

            $this->scatter=arr::get($this->config,'scatter',-1);
            if (!is_array($this->scatter)){
                    $this->scatter=[$this->scatter];
            }

            $this->bonus=arr::get($this->config,'bonus',-1);
            if (!is_array($this->bonus)){
                    $this->bonus=[$this->bonus];
            }
            $this->bonus_param=arr::get($this->config,'bonus_param',[0,0,0,0,0,0]);

            $this->free_games=arr::get($this->config,'free_games',[0,0,0,0,0,0]);
            $this->free_multiplier=arr::get($this->config,'free_multiplier',1);
            $this->free_mode=arr::get($this->config,'free_mode','sum');
            $this->free_multiplier_mode=arr::get($this->config,'free_multiplier_mode','simple');
            $this->multiplier=1;
            //барабаны

            /*
            $syms=array_keys($this->pay);
            $start=empty($start) ? min($syms) : $start;
            $end=empty($end) ? max($syms) : $end;

            $bar=[];

            for($i=1;$i<=5;$i++){
                    $this->bars[$i]=[];
            }

            for($i=$start;$i<=$end;$i++){

                    $this->bars[1][]=$i;
                    $this->bars[2][]=$i;
                    $this->bars[3][]=$i;
                    $this->bars[4][]=$i;
                    $this->bars[5][]=$i;
            }
            */	

		
		
	}
	
	
	//корректирует позицию барабана, есил позиция находится вне допустимого диапазона
	public function correct_pos(){
		
		foreach ($this->pos as $num=>$pos){
			$c=count($this->bars[$num]);
			if ($pos>$c-1){
				$this->pos[$num]-=$c;
			}
			
			if ($pos<0){
				$this->pos[$num]+=$c;
			}
			
		}
		
	}
	
	
	//выставляем символы на барабанах по порядку 
	public function test1($end=null,$start=null){
		
		$syms=array_keys($this->pay);
		$start=empty($start) ? min($syms) : $end;
		$end=empty($end) ? max($syms) : $end;
		
		
		for($i=1;$i<=5;$i++){
			$this->bars[$i]=[];
		}
		
		for($i=$start;$i<=$end;$i++){
			
			$this->bars[1][]=$i;
			$this->bars[2][]=$i;
			$this->bars[3][]=$i;
			$this->bars[4][]=$i;
			$this->bars[5][]=$i;
			$bar[$i]=$i;
		}
		$this->barcount==count($this->bars);
		
		
		for($i=1;$i<=$this->barcount;$i++){
			$this->pos[$i]=($i-1)*$this->heigth;
			
			
		}
		
		$this->correct_pos();
		
		
		
	}
	
	
	public function test($end=null,$start=null,$wild=false){
		
		$syms=array_keys($this->pay);
		$start=empty($start) ? min($syms) : $start;
		$end=empty($end) ? max($syms) : $end;
		
		$sym=game::data('sym','1');
		$count=game::data('count','0');
		$bar=[];
		
		for($i=1;$i<=5;$i++){
			$this->bars[$i]=[];
		}
		
		for($i=$start;$i<=$end;$i++){
			
			if (in_array($i,$this->scatter)){
				continue;
			}
			if (in_array($i,$this->wild)){
				continue;
			}
			
			if (in_array($i,$this->bonus)){
				continue;
			}

			if ($wild and isset($this->wild[0])){
				$this->bars[1][]=$this->wild[0];
			}
			else {
				$this->bars[1][]=$i;
			}
			$this->bars[2][]=$i;
			$this->bars[3][]=$i;
			$this->bars[4][]=$i;
			$this->bars[5][]=$i;
			$bar[$i]=$i;
		}
		
		
		
		$this->barcount==count($this->bars);
		unset($bar[$sym]);
		
		$nop=0;
		for($i=1;$i<=$this->barcount;$i++){
			if ($i<=$count){
				$nop=$this->pos[$i]=$sym-2;
			}
			else{
				do{
					$r=array_rand($bar)-1;
				} while ($r==$nop);
				$this->pos[$i]=$r;
			}
			
		}
		
		$this->correct_pos();
		
		$count++;
		if($wild){
			if ($count>3){
				$count=3;
				$sym++;
			}
		}
		else{
		
			if ($count>5){
				$count=0;
				$sym++;
			}
		}
		
			
		if ($sym>$end){
			$sym=1;
		}
		
		$data=['sym'=>$sym,'count'=>$count];
		
		$this->win();
		
		
		
		$bet['amount']=$this->amount;
		$bet['come']=$this->cline;
		$bet['result']=print_a1($this->visible,true);
		$bet['office_id']=OFFICE;
		$bet['win']=$this->win_all;
		$bet['game_id']=0;
		$bet['method']='test';
		
		
		
		bet::make($bet, $this->win_all-$this->amount, $data);
		
	}
	
	
	
	
	public function bet($mode=null){
		
		
		$this->total_win_free=game::data('free_total_win',0);
		$this->mode_free_begin=false;
		$this->mode_free_end=false;
		$this->free_count_play=game::data('freerun',0);
		$this->free_count_played=game::data('free_count_played',0);
		$this->is_freerun=false;
                $this->simple_win=false;
		$amount=$this->amount;
		$no=[];
		//freerspin mode все данные берем из сессии, а не то что прилетело
		if ($this->free_count_play>0){
			$this->is_freerun=true;
			$this->amount=game::data('bet');
			$this->cline=game::data('lines');
			$this->amount_line=$this->amount/$this->cline;
			$amount=0;
			$no[]=6;
			$this->multiplier=game::data('multiplier',$this->free_multiplier);
		}
		
		
		$error=bet::error($this->amount,$no);
		if ($error>0){
                    return $error;				
                }
		
		
		
		$i=0;
		$exit=false;
		$min=PHP_INT_MAX;  
		
		do {
		
			$this->spin($mode);
		
			if (bet::HaveBankAmount($this->win_all+$this->bonus_win,$amount)){
				$exit=true;
			};
			
			//минимально возможный выигрыш
			if ($this->win_all<$min){
				$min=$this->win_all;
				$pos=$this->pos;
				$method='bank';
				
			}
			
			//нет вариантов
			if ($i>=50){
				//закат солнца вручную
				$this->pos=$pos;
				$this->win();
				$exit=true;
				$method='hand';
				continue;
			}
			
			
			$i++;
		}	while (!$exit);
		
		
		
		  
		
		$bet['amount']=$amount;
		$bet['come']=$this->cline;
		$bet['result']=print_a1($this->visible,true);
		$bet['office_id']=OFFICE;
		$bet['win']=$this->win_all;
		$bet['game_id']=0;
		$bet['method']=$i>1 ? $method : 'random';
		
		if ($this->win_all>0 and !$this->is_freerun and $this->freerun==0 and $this->bonusrun==0){
			$this->simple_win=true;
		}
		
		$data=null;
		if ($this->win_all>0 or  $this->freerun>0 and $this->bonusrun>0 ){
			$data['amount']=$this->win_all;
			$data['bet']=$this->amount;
			$data['lines']=$this->cline;
			$data['can_double']=(int) $this->simple_win;
			$data['can_bonus']=0;
			$data['freerun']=0;
			$data['first_bet']=$this->win_all;
		}
		
		if ($this->is_freerun){
			$this->free_count_play--;
			$data['freerun']=$this->free_count_play;
			// закончить халявные вращения
			if ($this->free_count_play+$this->freerun==0){
				$this->mode_free_end=true;
			}
			
			$this->total_win_free+=$this->win_all;
			$this->free_count_played++;
			$data['free_total_win']=$this->total_win_free;
			$data['free_count_play']=$this->free_count_play;
			$data['free_count_played']=$this->free_count_played;
			
		}
		
		//нужно запускать бонус
		if ($this->bonusrun>0){
			$data['can_bonus']=1;
			$data['bonusdata']=$this->bonusdata;
			$data['bonusrun']=$this->bonusrun;
		}
		
		//выиграли freespin
		if ($this->freerun>0){
			if ($this->free_mode=='sum'){
				$this->free_count_play+=$this->freerun;
			}
			else {
				$this->free_count_play=$this->freerun;
			}
			$data['freerun']=$this->free_count_play;
			
			//freespin выиграли во время freespin
			if ($this->is_freerun){
				if ($this->free_multiplier_mode=='inc'){
					$data['multiplier']=game::data('multiplier')+1;
				}
				else{
					$data['multiplier']=$this->free_multiplier;
				}
			}
			else{
				$data['multiplier']=$this->free_multiplier;
			}
			
			// Начать халявные вращения
			if (!$this->is_freerun){
				$this->mode_free_begin=true;
				//Выиграно на фриспинах всего c учетом первого вращения, где выпал сам бонус
				$data['free_total_win']=$this->win_all;
			}
			$this->free_count_play=$data['freerun'];
			$data['free_count_play']=$this->free_count_play;
		}
                
		
		
		bet::make($bet, $this->win_all-$amount,$data);
		
	}
	
	
	//вращаем
	public function spin($mode=null){
		
		for($i=1;$i<=count($this->lines);$i++){
			$this->win[$i]=0;
		}
		
		for($i=1;$i<=$this->barcount;$i++){
			$this->pos[$i]=math::random_int(0,count($this->bars[$i])-1);
		}
		
		
		//крутим сами
		if($mode=='scatter' ){
			
			$j=0;
			$spos=[ 1=>[],
                                2=>[],
                                3=>[],
                                4=>[],
                                5=>[],
                              ];
                        
                        foreach ($this->bars as $num=>$bar){
                            foreach($bar as $pos=>$sym ){
                                    if(in_array($sym,$this->scatter)){
                                            $spos[$num][]=$pos;
                                    }
                            }
                        }
                        
                        for($i=1;$i<=$this->barcount;$i++){
                            if (count($spos[$i]>0)){
                                $this->pos[$i]=$spos[$i][array_rand($spos[$i])]-mt_rand(0,2);
                            }
                            else{
                                $this->pos=mt_rand(0,count($this->bars[$i])-1);
                            }
                            
                        }
                        
		

			$this->correct_pos();
			
		}
                
               
		
		
		//крутим сами
		if($mode=='bonus'){
			
			$j=0;
			$bar=[];
			
			foreach($this->bars[1] as $sym ){
				if(in_array($sym,$this->bonus)){
					$wpos=$j;
				}
				$bar[]=$j;
				$j++;
			}
			
			for($i=1;$i<=$this->barcount;$i++){
				if ($i<=3){
					$nop=$this->pos[$i]=$wpos+rand(0,2)-2;
				}
				else{
					do{
						$r=array_rand($bar)-1;
					} while ($r<=$wpos and $r>=$wpos-2);
					$this->pos[$i]=$r;
				}

			}

			$this->correct_pos();
			
		}
		

		//крутим сами
		if($mode=='win'){
			
			$this->pos[1]=0;
                        $this->pos[2]=0;
                        $this->pos[3]=0;
                        $this->pos[4]=3;
                        $this->pos[5]=3;
			$this->correct_pos();
			
		}
		
		
		$this->win();
		
		
		
	
	}
	
	public function sym($num=null){
		
		if (empty($num)){
			$r=[];
			for($i=1;$i<=$this->barcount*$this->heigth;$i++){
				$r[$i]=$this->sym($i);
			}
			return $r;
			
		}
		
                
 
		$bar=$num % $this->barcount;
		if ($bar==0){
			$bar=$this->barcount;
		}
                
                
		$pos=$this->pos[$bar]+floor(($num-0.01)/$this->barcount);
                
                if ($pos>=count($this->bars[$bar])){
			$pos-=count($this->bars[$bar]);
		}
		return $this->bars[$bar][$pos];
		
                
		
	}
        
        //выигрыш по линиям.  0  эл-т массива - выигрыш по anypay
        public function lineWin($num=null){
            if ($num===null){
                $a=array_fill(0,$this->lineCount+1, 0);
                for($i=0;$i<=$this->cline;$i++){
                    $a[$i]=$this->lineWin($i);
                }
                return $a;
            }
            
            if ($num==0){
                return $this->win_scatter;
            }
            
            return $this->win[$num];
            
        }
        
        //битовая маска участвующих в выигрыше символов 
        //$num - номер линии
        //скаттеры anypay не участвуют тут
        //TODO пока поддерживается только pay_rule==left добавить остальные
        public function lightingLine($num=null){
            if (empty($num)){
                //scatter
                $a=array_fill(0,$this->lineCount,0);
                
                if ($this->win_scatter>0){
                    foreach($this->sym() as $sym){
                        $a[0]=$a[0]<<1;
                        if (in_array($sym,$this->anypay)){
                            $a[0]++;
                        }
                    }
                }
                //other
                for($i=1;$i<=$this->cline;$i++){
                    $a[$i]=$this->lightingLine($i);
                }
                return $a;
                
            }
            
            switch ($this->LineWinLen[$num]) {
                case 0: return 0;
                case 1: return 0b10000;
                case 2: return 0b11000;
                case 3: return 0b11100;
                case 4: return 0b11110;
                case 5: return 0b11111;
            }            
            
            return 0;
        }
        
	
	//текущий выигрыш
	public function win(){
		
		$this->win_all=0;
		$this->bonus_win=0;
		$this->bonusdata=null;
		
		$this->LineSymbol=[];
		$this->LineUseWild=[];
		$this->LineWinLen=[];
		
		$a1=[];
		$a2=[];
		$a3=[];
		
                
		for ($i=1;$i<=$this->barcount;$i++){
			$el=$this->getels($this->bars[$i],$this->pos[$i]);
			$a1[]=$el[0];
			$a2[]=$el[1];
			$a3[]=$el[2];
		}
		$this->visible=$a=[$a1,$a2,$a3];
		
		
		$this->lineComb=[];
		//выигрыш по линиям
		for($i=1;$i<=$this->cline;$i++){
			$keys=$this->linetokey($this->lines[$i]);
            $comb=$this->GetElLine($keys,$a);
			$this->lineComb[$i]=$comb;
			$this->win[$i]=$this->wincomb($comb,$i)*$this->amount_line*$this->multiplier;
		}
                
                //anypay
		$this->win_scatter=0;
		foreach($this->anypay as $sym){
			$c=0;
			foreach($a as $line){
				foreach($line as $symbar){
					if($sym==$symbar){
						$c++;
					}
				}
			}
			$this->win_scatter+=$this->pay($sym,$c)*$this->amount*$this->multiplier;
		}
		
		$this->win_all=array_sum($this->win)+$this->win_scatter;
		
		//free run
		$this->freerun=0;
		foreach($this->scatter as $sym){
			$c=0;
			foreach($a as $line){
				foreach($line as $symbar){
					if($sym==$symbar){
						$c++;
					}
				}
			}
			$this->freerun=$this->free_games[$c];
		}
		
		//bonus run
		$this->bonusrun=0;
		foreach($this->bonus as $sym){
			$c=0;
			foreach($a as $line){
				foreach($line as $symbar){
					if($sym==$symbar){
						$c++;
					}
				}
			}
			$this->bonusrun=$this->bonus_param[$c];
		}
		
		if ($this->bonusrun>0){
			$this->calcbonus();
		}
		
		
	}
	
	
	public function calcbonus(){
		return 0;
	}
	
	
	public function pay($sym,$c){
		
		if (!isset($this->pay[$sym][$c])){
			return 0;
		}
		
		return $this->pay[$sym][$c];
		
	}
	
	//line номер линии
	public function wincomb($comb,$line=null){
		
		
		$mask=[];
		
		if (in_array('left',$this->pay_rule)){
			$mask[]=[1,1,1,1,1];
			$mask[]=[1,1,1,1,0];
			$mask[]=[1,1,1,0,0];
			$mask[]=[1,1,0,0,0];
		}
		
		if (in_array('right',$this->pay_rule)){
			$mask[]=[1,1,1,1,1];
			$mask[]=[0,1,1,1,1];
			$mask[]=[0,0,1,1,1];
			$mask[]=[0,0,0,1,1];
		}
		
		if (in_array('any',$this->pay_rule)){
			$mask[]=[1,1,1,1,1];
			$mask[]=[1,1,1,1,0];
			$mask[]=[0,1,1,1,1];
			$mask[]=[1,1,1,0,0];
			$mask[]=[0,0,1,1,1];
			$mask[]=[0,1,1,1,0];
		}
		
		if (in_array('3',$this->pay_rule)){
			$mask[]=[1,1,1];
		}

		$win=[0];
		$winsymbol=[-1];
		$winbonus=[0];
		$winlen=[0];
		//any pay будет выплачен два раза
		foreach($mask as $m){
                    $a=$this->mask($comb,$m);
                    $a=array_unique($a);

                    //комбинации без wild;
                    if (count($a)==1 and !in_array($a[0],$this->anypay)){
                            $win[]=$this->pay($a[0],$this->masksize($m));
                            $winsymbol[]=$a[0];
                            $winbonus[]=0;
                            $winlen[]=$this->masksize($m);
                    };



                    $is_wild=false;
                    foreach ($this->wild as $wild){
                        if (in_array($wild,$a)){
                            $is_wild=true;
                        }
                    }

                    //комбинации c wild
                    if ($is_wild){
                        //делим на wild и не wild
                        $wild=[];
                        $nowild=[];
                        foreach($a as $key=>$value){
                            if (in_array($value,$this->wild)){
                                    $wild[]=$a[$key];
                            }
                            else {
                                $nowild[]=$a[$key];
                            }
                        }
                        
                        $noCount=count(array_count_values($nowild));
                        
                        if ($noCount>1){
                            continue;
                        }
                        
                        if ($noCount==1){
                            $sym=reset($nowild);
                            //выигрыш с wild настройки wild_except и wild_multiplier едины для всех wild
                            if (!in_array($sym,$this->wild_except) and !in_array($sym,$this->anypay) ){
                                //выигрыш
                                $win[]=$this->pay[$sym][$this->masksize($m)]*$this->wild_multiplier;
                                $winsymbol[]=$sym;
                                $winbonus[]=1;
                                $winlen[]=$this->masksize($m);
                            };
                        }
                        //в комбинации только wild
                        if ($noCount==0){
                            foreach($wild as $sym){
                                if (!in_array($sym,$this->wild_except) and !in_array($sym,$this->anypay) ){
                                    //выигрыш с wild настройки wild_except и wild_multiplier едины для всех wild
                                    $win[]=$this->pay[$sym][$this->masksize($m)];
                                    $winsymbol[]=$sym;
                                    $winbonus[]=0;
                                    $winlen[]=$this->masksize($m);
                                };
                            }
                        }
                        
                    }
		}
		
		$maxwin=max($win);
		
		if (!empty($line)){
			
			if ($maxwin>0){
				$idx=array_keys($win,$maxwin);

				$this->LineSymbol[$line]=$winsymbol[$idx[0]];
				$this->LineUseWild[$line]=$winbonus[$idx[0]];
				$this->LineWinLen[$line]=$winlen[$idx[0]];
			}
			else{
				$this->LineSymbol[$line]=-1;
				$this->LineUseWild[$line]=0;
				$this->LineWinLen[$line]=0;
				
			}
			
		}

		
		return $maxwin;
		
	}

	
	public function mask($comb,$mask){
		$r=[];
		foreach($mask as $key=>$m){
			if ($m>0){
				$r[]=$comb[$key];
			}
		}
		
		return $r;
		
	}
	
	public function masksize($mask){

		
		return count(array_keys($mask,1));
		
	}
	

	//сравнение массивов 
	function array_diff1($comb, $win){

		if (count($comb)!=count($win)){
			throw new Exception('Несовпадение');
		}

		foreach($comb as $key=>$el){

			//массив
			if (is_array($comb[$key])){
				if (array_diff1($comb[$key],$win[$key])==1){
					return 1;
				}
			}
			//значение
			//0 - wild в комбинации comb
			elseif ($comb[$key]==0 ){
				continue;
			}
			//0 - любой символ во входе win
			elseif(($win[$key]>0 and $comb[$key]!=$win[$key])){
				return 1;
			}
		}

		return 0;

	}

	//получаем элемент и следующие за ним 
	function getels($bars,$key,$count=3){
                
		$el=[];
		$bc=count($bars)-1;
		for($i=1;$i<=$count;$i++){
			$el[]=$bars[$key];
			$key++;
			if ($key>$bc){
				$key=0;
			}
		}

		return $el;

	}

	//преобразование читаемых линий в ключи
	function linetokey($line){
		
		$r=[];
		
		foreach($line as $num_str=>$line2){
		
			foreach($line2 as $num_col=>$el){

				if ($el>0){
					if (isset($r[$num_col])){
						throw new Exception('двойное участие');
					}
					$r[$num_col]=$num_str;
				}

			}

		}
		ksort($r);
		return $r;


	}

	
	function GetElLine($line,$el){
		$comb=[];

		foreach($line as $x=>$y){
			$comb[]=$el[$y][$x];
		}

		return $comb;

	}
	
	protected $doubleclass;
	public $double_result;
	public $first_bet;
	public function double(){
	
		
		if (game::data('can_double')!=1){
			throw new Exception('cant double');
		}
		$this->amount=game::data('amount');
		$this->first_bet=game::data('first_bet');
				
		$this->doubleclass->select=$this->select;
		$this->doubleclass->amount=$this->amount;
		
		$i=0;
		$exit=false;
		$min=PHP_INT_MAX;
		$method='';
		
		do {
		
			$this->doubleclass->clear();
			$this->doubleclass->select();
			$win=$this->doubleclass->win();
			//сколько можем выиграть?
			if (bet::HaveBankAmount($win)){
				$exit=true;
				$method='bank';
			};
			
			//минимально возможный выигрыш
			if ($win<$min){
				$min=$this->win;
				$state=$this->doubleclass->state;
				
			}
			
			//нет вариантов
			if ($i>=50){
				//закат солнца вручную
				$this->doubleclass->state=$state;
				$exit=true;
				$method='hand';
				continue;
			}
			
			$i++;
		}	while (!$exit);
		
		if ($i==1){
			$method='random';
		}
		
		$this->win_all=$this->doubleclass->win();
		$this->double_result=$this->doubleclass->state;
		
		$data=null;
		if($this->win_all>0){
			$data['amount']=$this->win_all;
			$data['can_double']=1;
			$data['win']=$this->win_all;
		}
		
		
		$bet['amount']=$this->amount;
		$bet['come']=$this->doubleclass->come();
		$bet['result']=$this->doubleclass->result();
		$bet['win']=$this->win_all;
		$bet['game_id']=null;
		$bet['game']=game::session()->game.' double';
		$bet['method']=$method;
	
		
		bet::make($bet,$this->win_all-$this->amount,$data);
		
		
	}
	
	
	
	public function reMapBar(){
		
		
	foreach($this->bars as $numbar=>$bar){
            
            $count=array_count_values($bar);
            $len=count($bar);
            $a=[];
            $bonus=[];
            
            
            foreach($bar as $sym){
				
                if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
                    $bonus[]=$sym;
                }
            }
			
            
            foreach($this->bonus as $b ){
                unset($count[$b]);
            }
            
            foreach($this->scatter as $b ){
                if (isset($count[$b])){
					unset($count[$b]);
                }
            }
            foreach($this->anypay as $b ){
                if (isset($count[$b])){
					unset($count[$b]);
                }
            }
            
            
			$i=1;
            //основные элементы
            while (count($count)>0){
                $i++;
				if ($i>1000){
					throw new Exception('i exption');
				}
                $els=array_keys($count);
                $el=math::array_rand_value($els);
                $a[]=$el;
                $count[$el]--;
                if ($count[$el]==0){
                    unset($count[$el]);
                }
                
            }
            
			shuffle($a);
            //бонусы
			shuffle($bonus);
			
			foreach($bonus as $b){
				$bad=[];
				$ca=count($a);
				foreach($a as $num=>$sym){
					if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
						$bad[]=$num;
						$bad[]=$num+1;
						$bad[]=$num+2;
						$bad[]=$num-1;
						$bad[]=$num-2;
					}
				}
				
				$bad=array_unique($bad);
				
				foreach($bad as $num=>$val){
					if ($bad[$num]<0){
						$bad[$num]+=$ca;
					}
					
					if($bad[$num]>$ca-1){
						$bad[$num]-=$ca;
					}
					
				}
				
				$bad=array_unique($bad);
				$use=array_keys($a);
				foreach($bad as $k){
					unset($use[$k]);
				}
				
				if (count($use)==0){
					return false;
					throw new Exception('No uses values');
				}
				
				$key=math::array_rand_value($use);
				
				$sym=$a[$key];
				$a[$key]=$b;
				$a[]=$sym;

			}
            
            $this->bars[$numbar]=$a;
            
			
            
			//$this->bonus;
            //$this->scatter;
            
            
            
		}

		
	}


	public function calcmath(){
		ob_end_clean();
		$start=time();
		//ставка на линию
		$this->amount_line=1;
		//выбрано линий
		$this->cline=1;
		//ставка всего
		$this->amount=$this->cline*$this->amount_line;
		
		
		$this->barcount=count($this->bars);
		
		
		$pos=[1=>0,2=>0,3=>0,4=>0,5=>0];
		$count=[];
		$count[1]=count($this->bars[1]);
		$count[2]=count($this->bars[2]);
		$count[3]=count($this->bars[3]);
		$count[4]=count($this->bars[4]);
		$count[5]=count($this->bars[5]);
		
		$spin_count=0;
		$in=0;
		$out=0;
		$bonus=[0,0,0,0,0,0];
		$freespin=0;
		
		
		$csym=[];
		$csym[-1][0]=[0,0];
		foreach (array_keys($this->pay) as $sym){
			$csym[$sym][1]=[0,0];
			$csym[$sym][2]=[0,0];
			$csym[$sym][3]=[0,0];
			$csym[$sym][4]=[0,0];
			$csym[$sym][5]=[0,0];
		}
		
		$allSpin=$count[1]*$count[2]*$count[3]*$count[4]*$count[5];
		$winScatter=0;
		echo "start\r\n";
		for($pos[1]=0;$pos[1]<=$count[1]-1;$pos[1]++){
			for($pos[2]=0;$pos[2]<=$count[2]-1;$pos[2]++){
				for($pos[3]=0;$pos[3]<=$count[3]-1;$pos[3]++){
					for($pos[4]=0;$pos[4]<=$count[4]-1;$pos[4]++){
						for($pos[5]=0;$pos[5]<=$count[5]-1;$pos[5]++){
							$this->pos[1]=$pos[1];
							$this->pos[2]=$pos[2];
							$this->pos[3]=$pos[3];
							$this->pos[4]=$pos[4];
							$this->pos[5]=$pos[5];
							$this->correct_pos();
							$this->win();
							$spin_count++;
							$in+=$this->amount;
							$out+=$this->win_all;
							
							if ($this->bonusrun>0){
								$bonus[$this->bonusrun]++;
							}
							$freespin+=$this->freerun;
							$winScatter+=$this->win_scatter;
							
							$csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]]++;
                                                        

				
							
						}
					}
				}
			}
            $time= floor((time()-$start)/$spin_count*($allSpin-$spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
                    
		}
		
		$time=time()-$start;
		
		echo "
			spin_count: $spin_count 
			in: $in 
			out: $out 
			z: ".round($out/$in,2)."
			bonus: ".implode(' ',$bonus)."
			freespin: $freespin 
                        winScatter: $winScatter    
			time: $time
			bars: ".print_a1($this->bars,true);
			
			print_b($csym);
		
		$s="
			spin_count: $spin_count 
			in: $in 
			out: $out 
			z: ".round($out/$in,2)."
			bonus: ".implode(' ',$bonus)."
			freespin: $freespin 
			time: $time
			bars: ".print_a1($this->bars,true)."\r\n\r\n ".print_b($csym);
                        
                file_put_contents('1',$s);
		
		
		
	}
	
        
        //версия с перебором ограниченного числа символов
        public function calcmath2(){
            ob_end_clean();
		$start=time();
		//ставка на линию
		$this->amount_line=1;
		//выбрано линий
		$this->cline=1;
		//ставка всего
		$this->amount=$this->cline*$this->amount_line;
		
		
		
		$this->barcount=count($this->bars);
		
		
		$pos=[1=>0,2=>0,3=>0,4=>0,5=>0];
		$count=[];
		$count[1]=count($this->bars[1]);
		$count[2]=count($this->bars[2]);
		$count[3]=count($this->bars[3]);
		$count[4]=count($this->bars[4]);
		$count[5]=count($this->bars[5]);
		
		$spin_count=0;
		$in=0;
		$out=0;
		$bonus=[0,0,0,0,0,0];
		$freespin=0;
		
		
		$csym=[];
		$csym[-1][0]=[0,0];
		foreach (array_keys($this->pay) as $sym){
			$csym[$sym][1]=[0,0];
			$csym[$sym][2]=[0,0];
			$csym[$sym][3]=[0,0];
			$csym[$sym][4]=[0,0];
			$csym[$sym][5]=[0,0];
		}
		
                
                $lowBar=[];
                $countSym=[];
                $lowCount=[];
                foreach ($this->bars as $num=>$bar){
                    $lowBar[$num]= array_values(array_unique($bar));
                    $lowCount[$num]=count($lowBar[$num]);
                    $countSym[$num]= array_count_values($bar);
                }
		
                //меняем барабан на маленький
                $this->bars=$lowBar;
                
               
                
                $allSpin=$lowCount[1]*$lowCount[2]*$lowCount[3]*$lowCount[4]*$lowCount[5];
                $curSpin=0;
                
		for($pos[1]=0;$pos[1]<=$lowCount[1]-1;$pos[1]++){
			for($pos[2]=0;$pos[2]<=$lowCount[2]-1;$pos[2]++){
				for($pos[3]=0;$pos[3]<=$lowCount[3]-1;$pos[3]++){
					for($pos[4]=0;$pos[4]<=$lowCount[4]-1;$pos[4]++){
						for($pos[5]=0;$pos[5]<=$lowCount[5]-1;$pos[5]++){
							$this->pos[1]=$pos[1];
							$this->pos[2]=$pos[2];
							$this->pos[3]=$pos[3];
							$this->pos[4]=$pos[4];
							$this->pos[5]=$pos[5];
							$this->correct_pos();
							$this->win();
                                                        
                                                        //увеличиваем все на общее количество таких комбинаций на барабане
                                                        $mn=1;
                                                        
                                                        for($i=1;$i<=5;$i++){
                                                            //-1 потому что кривые данные, лень переделывать
                                                            $mn*=$countSym[$i][$this->lineComb[1][$i-1]];
                                                        }
                                                        
							$spin_count+=$mn;
							$in+=$this->amount*$mn;
							$out+=$this->win_all*$mn;
							
							if ($this->bonusrun>0){
                                                            $bonus[$this->bonusrun]+=$mn;
							}
							$freespin+=$this->freerun*$mn;
							
							
							$csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]]+=$mn;
                                                        
                                                        $curSpin++;			
							
						}
					}
				}
                                $time= floor((time()-$start)/$curSpin*($allSpin-$curSpin));
			}
                    echo "$curSpin/$allSpin lost:$time сек\r\n";
		}
		
		$time=time()-$start;
		
		echo "
			spin_count: $spin_count 
			in: $in 
			out: $out 
			z: ".round($out/$in,2)."
			bonus: ".implode(' ',$bonus)."
			freespin: $freespin 
			time: $time
			bars: ".print_a1($this->bars,true);
			
			print_b($csym);
		
		
		
		
		
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
		
		
		$kf=[];
		
		foreach($this->pay as $sym=>$pay){
			for($i=1;$i<=5;$i++){
				$kf[$i][$sym]=$pay[$i];
			}
		}
		
		for($i=1;$i<=5;$i++){
			arsort($kf[$i]);
		}
		/*
		$this->bars=[];
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
				else{
					$call=count($this->pay)+1;
					$c=$call-mt_rand(1,$sym);
					if(in_array($sym,$this->bonus) or in_array($sym,$this->scatter)  or in_array($sym,$this->anypay) ){
						$c=mt_rand(1,round($num*1.5));
					}
				}
				
				
				
				
				for($i=1;$i<=$c;$i++){
					$this->bars[$num][]=$sym;
				}
			}
			
		}
		
		
		
		$this->reMapBar();
		*/
		
		
		
		
		/*
		$this->bars[1]=[1,8,7,2,7,12,8,4,12,4,10,11,9,5,2,12,10,3,6,1];
		$this->bars[2]=[11,10,12,5,11,3,7,2,2,4,12,8,1,10,9,12,7,6,4];
		$this->bars[3]=[9,10,6,2,8,7,5,9,12,4,1,12,6,3,1,5,2,4,12,11];
		$this->bars[4]=[6,12,11,8,3,7,11,4,9,2,7,12,10,4,5,12,3,6,10,1];
		$this->bars[5]=[7,11,12,8,10,7,1,5,10,12,2,9,9,8,5,1,11,3,4,6,12];
		*/		
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
			
			//left to right no wild
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
					
					
					//44444444444444444444444444444
					$p4=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$symbol];
					$wild_already_pay[$symbol][5]+=$p4;
					$p[4]+=$p4;
					


					//33333333333333333333333333333
					// wwws*
					
					$p3=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$symbol]*$count_all[5];
					$wild_already_pay[$symbol][4]+=$p3;
					$p[3]+=$p3;
					
					//22222222222222222222222222222
					//WWS**					

					$p2=$count[1][$sym]*$count[2][$sym]*$count[3][$symbol]*$count_all[4]*$count_all[5];
					$wild_already_pay[$symbol][3]+=$p2;
					$p[2]+=$p2;

					
				}
				else{	
				

					//44444444444444444444444444444
					if ($this->pay[$sym][4]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p4=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*$count[5][$symbol];
						$wild_already_pay[$symbol][5]+=$p4;
						$p[4]+=$p4;
					}


					//33333333333333333333333333333
					// wwws*
					if ($this->pay[$sym][3]>$this->pay[$symbol][4]*$this->wild_multiplier){
						$p3=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$symbol]*($count_all[5]-$count[5][$sym]-$count[5][$symbol]);
						$wild_already_pay[$symbol][4]+=$p3;
						$p[3]+=$p3;
					}

					// wwwss
					if ($this->pay[$sym][3]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p3=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$symbol]*($count[5][$sym]+$count[5][$symbol]);
						$wild_already_pay[$symbol][5]+=$p3;
						$p[3]+=$p3;
					}



					//22222222222222222222222222222
					//WWS**					
					if ($this->pay[$sym][2]>$this->pay[$symbol][3]*$this->wild_multiplier){
						$p2=$count[1][$sym]*$count[2][$sym]*$count[3][$symbol]*($count_all[4]-$count[4][$sym]-$count[4][$symbol])*$count_all[5];
						$wild_already_pay[$symbol][3]+=$p2;
						$p[2]+=$p2;
					}

					//WWSS*
					if ($this->pay[$sym][2]>$this->pay[$symbol][4]*$this->wild_multiplier){
						$p2=$count[1][$sym]*$count[2][$sym]*$count[3][$symbol]*$count[4][$symbol]*($count_all[5]-$count[5][$sym]-$count[5][$symbol]);
						$wild_already_pay[$symbol][4]+=$p2;
						$p[2]+=$p2;
					}

					//WWSSS
					if ($this->pay[$sym][2]>$this->pay[$symbol][5]*$this->wild_multiplier){
						$p2=$count[1][$sym]*$count[2][$sym]*$count[3][$symbol]*$count[4][$symbol]*$count[5][$symbol];
						$wild_already_pay[$symbol][5]+=$p2;
						$p[2]+=$p2;
					}



					//11111111111111111111111111111
					//обойдемся
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
			$p[4]=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*$count[4][$sym]*($count_all[5]-$count[5][$sym]-$w[5]);
			$p[3]=$count[1][$sym]*$count[2][$sym]*$count[3][$sym]*($count_all[4]-$count[4][$sym]-$w[4])*$count_all[5];
			$p[2]=$count[1][$sym]*$count[2][$sym]*($count_all[3]-$count[3][$sym]-$w[3])*$count_all[4]*$count_all[5];
			$p[1]=$count[1][$sym]*($count_all[2]-$count[2][$sym]-$w[2])*$count_all[3]*$count_all[4]*$count_all[5];
			

			
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
						

			//left to right with wild
			
			if (in_array($sym,$this->wild_except)){
				continue;
			}
			
			
			//всего
			$pw[5]=($count[1][$sym]+$count_wild[1])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count[4][$sym]+$count_wild[4])*($count[5][$sym]+$count_wild[5]);
			$pw[5]-=$p[5]+($count_wild[1]*$count_wild[2]*$count_wild[3]*$count_wild[4]*$count_wild[5]);
			$pw[5]-=$wild_already_pay[$sym][5];
			
			$pw[4]=($count[1][$sym]+$count_wild[1])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count[4][$sym]+$count_wild[4])*($count_all[5]-$count_wild[5]-$count[5][$sym]);
			$pw[4]-=$p[4]+($count_wild[1]*$count_wild[2]*$count_wild[3]*$count_wild[4]*($count_all[5]-$count_wild[5]-$count[5][$sym]));
			$pw[4]-=$wild_already_pay[$sym][4];
			
			$pw[3]=($count[1][$sym]+$count_wild[1])*($count[2][$sym]+$count_wild[2])*($count[3][$sym]+$count_wild[3])*($count_all[4]-$count_wild[4]-$count[4][$sym])*$count_all[5];
			$pw[3]-=$p[3]+($count_wild[1]*$count_wild[2]*$count_wild[3]*($count_all[4]-$count_wild[4]-$count[4][$sym])*$count_all[5]);
			$pw[3]-=$wild_already_pay[$sym][3];
			
			$pw[2]=($count[1][$sym]+$count_wild[1])*($count[2][$sym]+$count_wild[2])*($count_all[3]-$count_wild[3]-$count[3][$sym])*$count_all[4]*$count_all[5];
			$pw[2]-=$p[2]+$count_wild[1]*$count_wild[2]*($count_all[3]-$count_wild[3]-$count[3][$sym])*$count_all[4]*$count_all[5];
			$pw[2]-=$wild_already_pay[$sym][2];
			
			$win=[];
			for($i=2;$i<=5;$i++){
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
			\r\ncount:\r\n".print_a1i($count,true)
				/*."
				
			config:\r\n
\$l['bars']=[1=>[".implode(',',$this->bars[1])."],
			2=>[".implode(',',$this->bars[2])."],
			3=>[".implode(',',$this->bars[3])."],
			4=>[".implode(',',$this->bars[4])."],
			5=>[".implode(',',$this->bars[5])."]
];

"; */;
			
		
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


	//версия для расчета 3-х барабанных слотов
public function calcmath3(){
		ob_end_clean();
		$start=time();
		//ставка на линию
		$this->amount_line=1;
		//выбрано линий
		$this->cline=1;
		//ставка всего
		$this->amount=$this->cline*$this->amount_line;
		
		
		$this->barcount=count($this->bars);
		
		
		$pos=[1=>0,2=>0,3=>0];
		$count=[];
		$count[1]=count($this->bars[1]);
		$count[2]=count($this->bars[2]);
		$count[3]=count($this->bars[3]);

		
		$spin_count=0;
		$in=0;
		$out=0;
		$bonus=[0,0,0,0];
		$freespin=0;
		
		
		$csym=[];
		$csym[-1][0]=[0,0];
		foreach (array_keys($this->pay) as $sym){
			$csym[$sym][1]=[0,0];
			$csym[$sym][2]=[0,0];
			$csym[$sym][3]=[0,0];
		}
		
		$allSpin=$count[1]*$count[2]*$count[3];
		$winScatter=0;
		echo "start\r\n";
		for($pos[1]=0;$pos[1]<=$count[1]-1;$pos[1]++){
			for($pos[2]=0;$pos[2]<=$count[2]-1;$pos[2]++){
				for($pos[3]=0;$pos[3]<=$count[3]-1;$pos[3]++){
					
							$this->pos[1]=$pos[1];
							$this->pos[2]=$pos[2];
							$this->pos[3]=$pos[3];

							$this->correct_pos();
							$this->win();
							$spin_count++;
							$in+=$this->amount;
							$out+=$this->win_all;
							
							if ($this->bonusrun>0){
								$bonus[$this->bonusrun]++;
							}
							$freespin+=$this->freerun;
							$winScatter+=$this->win_scatter;
							
							$csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]]++;
                                                        
							
					
				}
                            $time= floor((time()-$start)/$spin_count*($allSpin-$spin_count));
                            
			}
                    
		}
		
		$time=time()-$start;
		
		echo "
			spin_count: $spin_count 
			in: $in 
			out: $out 
			z: ".round($out/$in,2)."
			bonus: ".implode(' ',$bonus)."
			freespin: $freespin 
                        winScatter: $winScatter    
			time: $time
			bars: ".print_a1($this->bars,true);
			
			print_b($csym);
		
		$s="
			spin_count: $spin_count 
			in: $in 
			out: $out 
			z: ".round($out/$in,2)."
			bonus: ".implode(' ',$bonus)."
			freespin: $freespin 
			time: $time
			bars: ".print_a1($this->bars,true)."\r\n\r\n ".print_b($csym);
                        
                file_put_contents('1',$s);
		
		
		
	}	
	
	
public function bonuscalcmath(){

	return [0,0,0,0,0,0];
	
}




	
}

