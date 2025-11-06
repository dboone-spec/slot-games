<?php




class card {
	
public static $visible_num_suit=array(1=>'♠',2=>'♣',3=>'♦',4=>'♥');

public static function  visible_suit($i){
	return self::$visible_num_suit[self::suit($i)];
}

public static function  visible_num($i){
	$v=array(2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>'J',12=>'Q',13=>'K',14=>'A');
	return $v[self::num($i)];
}

public static function suit($i){

	return ceil(($i-0.01)/13);
}

public static function num($i){
	return $i-(self::suit($i)-1)*13+1;
}


public static function print_card($a){
	if (!is_array($a)){
		$a=[$a];
	}
	
	$r='';
	foreach($a as $b)
	{
		$r.=self::visible_num($b).self::visible_suit($b).' ';
	}

	return $r;
}


protected static $newdeck=null;
//новая колода
public static function newdeck(){

	if (empty(self::$newdeck)){
		$deck=array();

		for($i=1;$i<=52;$i++){
			$deck[$i]=$i;
		}
		self::$newdeck=$deck;
	}


	return self::$newdeck;

}

public static function light($a1,$a){
	
	$r=[];
	foreach ($a1 as $key=>$value){
		if (in_array($value,$a)){
			$r[]=$key;
		}
	}
	
	return $r;
	
}


public static function makecard($num,$suit){
	return ($suit-1)*13+$num-1;
	
}


public static function color($card){
	if ($card<=0){
		throw new Exception;
	}
	
	if ($card<=26){
		return 1;
	}
	if ($card<=52){
		return 2; 
	}
	
	throw new Exception;
	
}




public static function sample($name){
    

    if ($name=='Royal Flush'){
        return 'A♠ K♠ Q♠ J♠ 10♠';
    }
    if ($name=='Straight Flush'){
        return 'K♠ Q♠ J♠ 10♠ 9♠';
    }
    if ($name=='Four of a Kind'){
        return 'K♠ K♣ K♦ K♥ 10♥';
    }
    if ($name=='Full House'){
         return 'K♠ K♣ K♦ Q♥ Q♦';
    }
    if ($name=='Flush'){
        return 'K♠ 10♠ 7♠ 5♠ 2♠';
    }
    if ($name=='Straight'){
        return 'K♠ Q♦ J♠ 10♣ 9♠';
    }
    if ($name=='Three of a Kind'){
        return 'K♠ K♣ K♦ Q♥ 10♥';   
    }
    if ($name=='Two pairs'){
        return 'K♠ K♣ Q♦ Q♥ 10♥';       
    }
    if ($name=='Jacks or Better'){
        return 'J♠ J♣ Q♦ 9♥ 10♥';       
    }

    if ($name=='Tens or Better'){
        return '10♠ 10♣ Q♦ 9♥ 8♥';       
    }


    if ($name=='Four Aces'){
        return 'A♠ A♣ A♦ A♥ 10♥';
    }
    
    if ($name=='Four faces'){
        return 'K♠ K♣ K♦ K♥ 10♥';
    }
    
    if ($name=='Four 2s through 10s'){
        return '2♠ 2♣ 2♦ 2♥ 10♥';
    }

    
}




}



