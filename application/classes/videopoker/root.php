<?php
/**
 *
 * @author var
 * Содержит методы по расчету комбинации
 */
class Videopoker_Root extends math{

public $paycard=10;
public $winlight=false;
protected $newdeck=null; //не менять
public $wincard=[];


public function __construct(){

	$this->newdeck=$this->newdeck();

}


public static function  visible_suit($i){

	return card::visible_suit($i);
}

public static function  visible_num($i){

	return card::visible_num($i);
}

public static function suit($i){

	return card::suit($i);
}

public static function num($i){
	return card::num($i);
}


public static function print_card($a){
	return card::print_card($a);
}


//новая колода
public function newdeck(){

	return card::newdeck();

}





/**
Роял-флаш или Роял-флэш (англ. royal flush — «королевская масть»): старшие (туз, король, дама, валет, десять) пять карт одной масти,
например: Т♥ К♥ Д♥ В♥ 10♥.
*/
public function RoyalFlush(){


	if ($this->c_suit[0]==5){
		if ($this->value[0]==10 and $this->value[1]==11 and $this->value[2]==12 and $this->value[3]==13 and $this->value[4]==14 ){
			$this->wincard=$this->hand;
			return true;
		}
	}
	return false;

}



/**
Стрейт-флаш или Стрит-флэш (англ. straight flush — «масть по порядку»): любые пять карт одной масти по порядку,
например: 9♠ 8♠ 7♠ 6♠ 5♠. Туз может как начинать порядок, так и заканчивать его.
*/
public function StraightFlush(){

	if ($this->c_suit[0]==5 and $this->Straight()){
		$this->wincard=$this->hand;
		return true;
	}

	return false;


}


/**
Каре/Четвёрка (англ. four of a kind, quads — «четыре одинаковых»): четыре карты одного достоинства,
например: 3♥ 3♦ 3♣ 3♠ 10♦.
*/
public function Quads(){

	if ($this->c_value[0]==4){


		if ($this->winlight){


			$key=array_keys($this->c_value_raw,4);
			$key=$key[0];
			foreach($this->card as $c){
				if ($c['num']==$key){
					$this->wincard[]=$c['value'];
				}
			}
		}
		return true;
	}

	return false;



}


/**
Фул-хаус/Полный сбор/Три плюс два (англ. full house, full boat — «полный дом», «полная лодка»): три карты одного достоинства и одна пара,
 например: 10♥ 10♦ 10♠ 8♣ 8♥.
*/
public function FullHouse(){

	if ($this->c_value[0]==3 and $this->c_value[1]==2){
		$this->wincard=$this->hand;
		return true;
	}

	return false;

}



/**
Флаш или Флэш (англ. flush — «масть»): пять карт одной масти,
например: К♠ В♠ 8♠ 4♠ 3♠.
*/
public function Flash(){

	if ($this->c_suit[0]==5){
		$this->wincard=$this->hand;
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
		and $this->value[1]==$this->value[2]-1
		and $this->value[2]==$this->value[3]-1
		and $this->value[3]==$this->value[4]-1	){

			$this->wincard=$this->hand;
			return true;

		}

	if ($this->value[0]==2 and
		$this->value[1]==3 and
		$this->value[2]==4 and
		$this->value[3]==5 and
		$this->value[4]==14){

			$this->wincard=$this->hand;
			return true;

		}

	return false;

}


/**
Сет/Трипс/Тройка (англ. three of a kind, set — «три одинаковых», «набор»): три карты одного достоинства,
например: 7♣ 7♥ 7♠ K♦ 2♠.
*/
public function Set(){


	if ($this->c_value[0]==3){


		if ($this->winlight){


			$key=array_keys($this->c_value_raw,3);
			$key=$key[0];
			foreach($this->card as $c){
				if ($c['num']==$key){
					$this->wincard[]=$c['value'];
				}
			}
		}
		return true;
	}

	return false;




}

/**
Две пары/Две двойки/Два плюс два (англ. two pairs): две пары карт,
например: 8♣ 8♠ 4♥ 4♣ 2♠.
*/


public function TwoPair(){

	if ($this->c_value[0]==2 and $this->c_value[1]==2){


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
Одна пара/Двойка (англ. one pair): две карты одного достоинства,
например: 9♥ 9♠ Т♣ В♠ 4♥.
*/

public function OnePair($hi=true){

	if ($this->c_value[0]==2){

		$key=array_keys($this->c_value_raw,2);

		if ($hi){

			if ( $key[0]>=$this->paycard){

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
Старшая карта/Кикер (англ. high card): ни одна из вышеописанных комбинаций,
например (комбинация называется «старший туз»): Т♦ 10♦ 9♠ 5♣ 4♣.
*/
public function HighCard(){

	return true;
}


//по порядку
public function hasOrder(){


	$values=$this->value;

	if (in_array(14,$values)){
		$values[]=1;
	}

	$values=array_unique($values);
	sort($values);
	$l=0;
	$max_line=0;
	$ch=-1;
	$count=count($values);

	for($i=0;$i<$count;$i++){
		$ch++;
		if ($ch==$values[$i]){
			$l++;
		}
		else{
			$l=0;
		}
		$ch=$values[$i];
		$max_line=max($max_line,$l);
	}

	return $max_line>=4 ? true : false;

}




protected $suit;
protected $value;
protected $c_suit;
protected $c_value;
//not sorted;
protected $c_suit_raw;
protected $c_value_raw;
protected $card;

protected $hand;

/**
 * Очитска класса для следующиего подсчета
 */
public function clear($card){


	$this->hand=$card;
	$this->wincard=[];
	$this->suit=array();
	$this->value=array();
	$this->c_suit=array();
	$this->c_value=array();
	$this->suit_raw=array();
	$this->c_value_raw=array();

	$this->_LongSuitValues=null;
	$this->card=[];
	$this->card1=[];


	foreach ($card as $c){
		$num=$this->num($c);
		$m=$this->suit($c);
		$this->value[]=$num;
		$this->suit[]=$m;
		$idx=$num*10;
		while (isset($this->card1[$idx])){
			$idx++;
		}
		$this->card1[$idx]=['value'=>$c, 'num'=>$num, 'suit'=>$m];

		if(!isset($this->c_value[$num])){
			$this->c_value[$num]=0;
		}
		$this->c_value[$num]++;

		if(!isset($this->c_suit[$m])){
			$this->c_suit[$m]=0;
		}
		$this->c_suit[$m]++;

	}




	$this->c_suit_raw=$this->c_suit;
	$this->c_value_raw=$this->c_value;
	$this->value_raw=$this->value;
	sort($this->value);
	rsort($this->c_suit);
	rsort($this->c_value);

	ksort($this->card1);

	foreach($this->card1 as $c){
		$this->card[]=$c;
	}

}

/**
 * уровень карточной комбинации
 * @param array $card - индексы карт в колоде
 */
public function level(array $card){

	if (count($card)<5){
		throw new Kohana_Exception(__('Меньше 5 карт быть не может'));
	}

	$this->clear($card);

	if ($this->RoyalFlush()) return 10;
	if ($this->StraightFlush()) return 9;
	if ($this->Quads()) return 8;
	if ($this->FullHouse()) return 7;
	if ($this->Flash()) return 6;
	if ($this->Straight()) return 5;
	if ($this->Set()) return 4;
	if ($this->TwoPair()) return 3;
	if ($this->OnePair()) return 2;
	if ( $this->HighCard()) return 1;

return 0;



}



//сильно урезанная базовая стратегия
//оставляем только готовые комбинации
public function userbase($card){


	$level=$this->level($card);


	//Фул-хаус и лучше
	//Стрит, или флеш
	if ($level>=5){
		return $card;
	}


	//тройка
	if ($level>=4){
		$nc=[];
		$key=array_keys($this->c_value_raw,3);
		foreach ($card as $c){
			if (card::num($c)==$key[0]){
				$nc[]=$c;
			}
		}
		return $nc;
	}

	//Две пары
	if ($level>=3){

		$v=array_keys($this->c_value_raw,2);
		$nc=[];

		foreach ($card as $c){
			if (card::num($c)==$v[0] or card::num($c)==$v[1] ){
				$nc[]=$c;
			}
		}
		return $nc;
	}

	//Старшая пара (валеты и выше)
	if ($level>=2){
		$v=array_keys($this->c_value_raw,2);
		$nc=[];

		foreach ($card as $c){
			if (card::num($c)==$v[0] ){
				$nc[]=$c;
			}
		}
		return $nc;

	}

	//Младшая пара (десятки и младше)
	//echo '<br>Младшая пара (десятки и младше)<br>';
	if ($this->OnePair(false)){
		$v=array_keys($this->c_value_raw,2);
		$nc=[];
		foreach ($card as $c){
			if (card::num($c)==$v[0] ){
				$nc[]=$c;
			}
		}
		return $nc;

	}

	//echo 'Сбросить все.<br>';
	return [];



}



//базовая стратегия
//
public function base($card){

	$level=$this->level($card);
	//echo card::print_card($card);
	//echo "<br>level $level";
	//print_r($this->card);

	//Фул-хаус и лучше
	if ($level>=7){
		return $card;
	}

	//4 карты для роял флеша
	if ($this->c_suit[0]==4){
		$nc=[];
		foreach ($card as $c){
			if ($this->c_suit_raw[card::suit($c)]==4 and in_array(card::num($c),[10,11,12,13,14])){
				$nc[]=$c;
			}
		}
		if (count($nc)==4){
			return $nc;
		}

	}


	//Стрит, или флеш
	if ($level>=5){
		return $card;
	}


	//тройка
	if ($level>=4){
		$nc=[];
		$key=array_keys($this->c_value_raw,3);
		foreach ($card as $c){
			if (card::num($c)==$key[0]){
				$nc[]=$c;
			}
		}
		return $nc;
	}


	//4 карты для стрит флеша
	//5 карт одной масти быть не может - это флеш
	if ($this->c_suit[0]==4){
		$nc=[];
		$v=[];
		$v1=[];

		foreach ($card as $c){

			if ($this->c_suit_raw[card::suit($c)]==4 ){
				$nc[]=$c;
				$v[]=card::num($c);
				$v1[]=card::num($c)==14 ? 1 : card::num($c);
			}
		}
		$min=min($v);
		$max=max($v);
		if ($max-$min<=4){
			return $nc;
		}

		$min=min($v1);
		$max=max($v1);
		if ($max-$min<=4){
			return $nc;
		}


	}

	//Две пары
	if ($level>=3){

		$v=array_keys($this->c_value_raw,2);
		$nc=[];

		foreach ($card as $c){
			if (card::num($c)==$v[0] or card::num($c)==$v[1] ){
				$nc[]=$c;
			}
		}
		return $nc;
	}



	//Старшая пара (валеты и выше)
	if ($level>=2){
		$v=array_keys($this->c_value_raw,2);
		$nc=[];

		foreach ($card as $c){
			if (card::num($c)==$v[0] ){
				$nc[]=$c;
			}
		}
		return $nc;

	}


	//3 карты для роял флеша
	if ($this->c_suit[0]>=3){
		$nc=[];
		foreach ($card as $c){
			if ($this->c_suit_raw[card::suit($c)]==3 and in_array(card::num($c),[10,11,12,13,14])){
				$nc[]=$c;
			}
		}
		if (count($nc)==3){
			return $nc;
		}

	}

	//4 карты для флеша
	if ($this->c_suit[0]==4){
		$nc=[];
		foreach ($card as $c){
			if ($this->c_suit_raw[card::suit($c)]==4 ){
				$nc[]=$c;
			}
		}

		if (count($nc)!=4){
			throw new Exception;
		}
		return $nc;

	}


	//Младшая пара (десятки и младше)
	//echo '<br>Младшая пара (десятки и младше)<br>';
	if ($this->OnePair(false)){
		$v=array_keys($this->c_value_raw,2);
		$nc=[];
		foreach ($card as $c){
			if (card::num($c)==$v[0] ){
				$nc[]=$c;
			}
		}
		return $nc;

	}


	//4 карты к внешнему стриту
	//echo '4 карты к внешнему стриту<br>';
	$nc=[$this->card[0]['value']];
	$l=0;
	for($i=1;$i<=4;$i++){

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

		if ($l==3){
			return $nc;
		}



	}

	//echo '2 старшие карты одной масти<br>';
	if ($this->value[3]>=$this->paycard){

		$v=[];
		foreach ($this->card as $c){
			if ($c['num']>=$this->paycard){
				$v[]=$c;
			}
		}

		foreach ($v as $age){
			foreach ($this->card as $c){
				if ($c['num']>=$this->paycard ){
					if ($c['suit']==$age['suit'] and $age['value']!=$c['value']){
						return [$age['value'],$c['value']];
					}
				}
			}
		}


	}


	//echo '3 карты для стрит флеша<br>';
	if ($this->c_suit[0]==3){
		$nc=[];
		$v=[];
		$v1=[];

		foreach ($card as $c){

			if ($this->c_suit_raw[card::suit($c)]==3 ){
				$nc[]=$c;
				$v[]=card::num($c);
				$v1[]=card::num($c)==14 ? 1 : card::num($c);
			}
		}
		$min=min($v);
		$max=max($v);
		if ($max-$min<=4){
			return $nc;
		}

		$min=min($v1);
		$max=max($v1);
		if ($max-$min<=4){
			return $nc;
		}


	}


	//echo '2 старшие карты разных мастей (если больше чем 2 старшие карты, то оставить младшие из них)<br>';
	if ($this->value[3]>=$this->paycard){

		$v=[];
		foreach ($this->card as $c){
			if ($c['num']>=$this->paycard){
				$v[]=$c;
			}
		}

		foreach ($v as $age){
			foreach ($this->card as $c){
				if ($c['num']>=$this->paycard ){
					if ($age['value']!=$c['value']){
						return [$age['value'],$c['value']];
					}
				}
			}
		}


	}




	//echo '10/валет, 10/дама, 10/король одной масти<br>';
	if (in_array('10',$this->value)){
		$v=[];
		foreach ($this->card as $c){
			if ($c['num']==10){
				$v[]=$c;
			}
		}
		foreach ($v as $ten){
			foreach ($this->card as $c){
				if ($c['num']==11 or $c['num']==12 or $c['num']==13 ){
					if ($c['suit']==$ten['suit']){
						return [$ten['value'],$c['value']];
					}
				}
			}
		}

	}



	//echo 'Одна старшая карта<br>';
	if ($this->card[4]['num']>=$this->paycard){
		return [$this->card[4]['value']];
	}


	//echo 'Сбросить все.<br>';
	return [];

}


}
















