<?php

class Videopoker_Game_Pairplus extends Videopoker_Calc{
	
	public $paycard=0;
        protected $cardCount=3;
	
        
        public function __construct() {
            return parent::__construct('pairplus');
            
        }
	
	
/**
 * уровень карточной комбинации 
 * @param array $card - индексы карт в колоде
 */
public function level(array $card){
	
    if (count($card)<$this->cardCount){
        print_r($card);
            throw new Kohana_Exception(__("Меньше {$this->cardCount} карт быть не может"));
    }

    $this->clear($card);
/*
    print_r($this->value);
    print_r($this->c_suit);
    print_r($this->c_value);
echo "c_value_raw";
    print_r($this->c_value_raw);
    print_r($this->card);
*/
    if ($this->StraightFlush()) return 6;
    if ($this->Set()) return 5;  
    if ($this->Straight()) return 4;
    if ($this->Flash()) return 3; 
    if ($this->OnePair()) return 2;
    if ($this->HighCard()) return 1;

    return 0;

}


public function StraightFlush(){
    
    if ($this->Flash() and $this->Straight()){
        $this->wincard=$this->hand;
        return true;        
    }
    
    return false;
    
    
}



public function Set(){
    
    if ($this->c_value[0]==3){
        if ($this->winlight){
            $this->wincard=$this->hand;
        }
        return true;
    }
    
    return false;
}

public function Flash(){
    
    if ($this->c_suit[0]==3){
        
        if ($this->winlight){
            $this->wincard=$this->hand;
        }
        return true;
    }
    
    return false;
}

public function OnePair($hi=true){
    
    if ($this->c_value[0]==2){
        if ($this->winlight){
            $key=array_keys($this->c_value_raw,2);
            foreach($this->card as $c){
                if (in_array($c['num'],$key)){
                        $this->wincard[]=$c['value'];
                }
            }
            
        }
        
        return true;
    }    
    return false;
}



/** 
Стрейт или Стрит (англ. straight — «порядок»): пять карт по порядку любых мастей, 
например: 5♦ 4♥ 3♠ 2♦ Т♦. Туз может как начинать порядок, так и заканчивать его. В данном примере Т♦ начинает комбинацию и его достоинство оценивается в единицу, а 5♦ считается старшей картой.
*/

public function Straight(){
	
    if ($this->value[0]==$this->value[1]-1  
    	and $this->value[1]==$this->value[2]-1){
			
        $this->wincard=$this->hand;
        return true;
			
    }
		
	if ($this->value[0]==2 
            and $this->value[1]==3 
            and $this->value[2]==14){
			
            $this->wincard=$this->hand;
            return true;

    }
		
	return false;

}


public function base($card){
	
	$level=$this->level($card);
	
	//готоввая комбинация из 3 карт
	if ($level>=3){
		return $card;
	}

        //пара
        if ($this->c_value[0]==2){
            $key=array_keys($this->c_value_raw,2);
            $nc=[];
            foreach($this->card as $c){
                if (in_array($c['num'],$key)){
                        $nc[]=$c['value'];
                }
            }
            
            return $nc;
        }
        
	//2 карты для внешнего стрит 
	$nc=[$this->card[0]['value']];
	$l=0;
	for($i=1;$i<=2;$i++){
		
		if ($this->card[$i]['num']==13){
			$l=0;
			continue;
		}
		
		if ($this->card[$i]['num']==$this->card[$i-1]['num']+1){
			$l++;
			$nc[]=$this->card[$i]['value'];
		}
		else{
			$nc=[];
			$l=0;
		}
		
		if ($l==2){
			return $nc;
		}
		
		
		
	}
	
	
	//2 карты для флеш
	if ($this->c_suit[0]==2){
		$v=array_keys($this->c_suit_raw,2);
		$nc=[];
		foreach ($card as $c){
			if (card::suit($c)==$v[0] ){
				$nc[]=$c;
			}
		}
		return $nc;
	}
	
	
	
	/*
	//echo 'Одна старшая карта<br>';
        if ($this->card[2]['num']>=$this->paycard){
		return [$this->card[2]['value']];
	}
        */
	//echo 'Сбросить все.<br>';
	return [];
				
}


}

