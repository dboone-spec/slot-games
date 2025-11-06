<?php

function print_a($a){

	if(!is_array($a)){
		$a=[$a];
	}
		echo '['.implode(',',$a)."]\r\n";

}



function print_a1($a,$return=false){

	$s='';
	$rz="\r\n";
	foreach($a as $a1){
		$s.='['.implode(',',$a1).']'.$rz;
	}

	if ($return){
		return $s;
	}

	echo $s;
}



function print_a1i($a,$return=false){



	$s='';
	$rz="\r\n";
	foreach($a as $a1){
		ksort($a1);
		$s.='[';
		foreach($a1 as $key=>$a2){
			$s.="$key=>$a2 ";
		}
		$s.="]$rz";
	}

	if ($return){
		return $s;
	}

	echo $s;
}


function print_b($a,$return=false){

	$s='';
	foreach($a as $sym=>$cc){
		$s.="$sym normal: ";
		foreach($cc as $count=>$ch){
			$s.="$count => {$ch[0]} ,";
		}
		$s.="\r\n$sym wild  : ";
		foreach($cc as $count=>$ch){
			$s.="$count => {$ch[1]} ,";
		}
		$s.="\r\n";
	}
	if ($return){
		return $s;
	}

	echo $s;
	return null;
}

function br($c=1){
	for($i=1;$i<=$c;$i++){
		echo '<br>';
	}
}


function a_php($a,$r=false){
	$s="[";
	foreach($a as $key=>$value){
		$s.="$key=>$value,\r\n";
	}
	$s.="];";

	if($r){
		return $s;
	}

	echo $s;
}




class math {

    protected $counter=null;
    protected $counter_type=null;
    protected function getCounter($type, $name){
        if (empty($this->counter)){
            $this->counter=new Model_Counter(["game_id"=>$this->game_id, "office_id"=>OFFICE]);
            if (!$this->counter->loaded()){

                //убрал exception
                $this->counter->game_id = $this->game_id;
                $this->counter->office_id = OFFICE;
                $this->counter->save()->reload();
            }

        }
        return $this->counter;
    }

	public function getTotalFreeCount() {
        return 0;
    }

    protected $_max_win=0; //лимит на максимальный выигрыш. если не 0, то идет корректировка выигрыша через correctMaxWin. используется в калькуляторах и удвоении
    //установка максимального выигрыша нужно делать извне через setMaxWin
    public function setMaxWin($max_win) {
        if($max_win>0) {
            $this->_max_win = $max_win;
        }
    }

    public function correctMaxWin(&$win) {
        if($this->_max_win>0 && $win>$this->_max_win) {
            $win=$this->_max_win;
        }
    }

public static function random_gen(){

    if (Kohana::$environment== Kohana::DEVELOPMENT){
        $val=mt_rand(0, PHP_INT_MAX);
    }
    else{
        try {
            if (!file_exists('/dev/urandom')){
                throw new Exception;
            }
            $fh = fopen('/dev/urandom', 'r');
            stream_set_read_buffer($fh, PHP_INT_SIZE);
            $bytes = fread($fh, PHP_INT_SIZE );
            if ($bytes === false || strlen($bytes) != PHP_INT_SIZE ) {
                    throw new RuntimeException('Unable to get ' . PHP_INT_SIZE . ' bytes');
            }
            fclose($fh); // Closing handle.

            if (PHP_INT_SIZE == 8) { // 64-bit versions
                    list($higher, $lower) = array_values(unpack('N2', $bytes));
                    $value = $higher << 32 | $lower;
            }
            else { // 32-bit versions
                    list($value) = array_values(unpack('Nint', $bytes));
            }
            $val = $value & PHP_INT_MAX;
        }
        catch (Exception $e) {
            $val=mt_rand(0, PHP_INT_MAX);
        }
    }
    return (float)$val / PHP_INT_MAX; // convert to [0,1]11


}




public static function random_int($min =0, $max = null, $z=100){

    if ($min==$max){
        return $min;
    }

	if (empty($max)){
		$max=PHP_INT_MAX-1;
	}

	$diff=$max-$min;

	if ($diff> PHP_INT_MAX-1) {
			throw new Exception('Bad Range');
	}

	if($z<100) {
        $o = (int)(floor(self::random_gen()*($diff+1)) + $min);
    }

	return (int)(floor(self::random_gen()*($diff+1)) + $min);
}

public static function random_float($min =0, $max = null){
	if (empty($max)){
		$max=PHP_INT_MAX-1;
	}

	$diff=$max-$min;

	if ($diff> PHP_INT_MAX-1) {
			throw new Exception('Bad Range');
	}



	return self::random_gen()*$diff + $min;
}




public static function array_rand($array){

    $count=count($array);
    $keys=array_keys($array);
    $key=self::random_int(0,$count-1);
    return $keys[$key];

}


public static function array_rand_value($array){

    $key=self::array_rand($array);
    return $array[$key];

}



//возвращает случайное значение, в зависимости от его веса.
// значение=>вес
public static function getRandWeight($weight,$count=1){

	if (count($weight)<$count){
		throw new Exception('Не достаточное количество элементов в массиве ');
	}

	$result=[];
	for($i=1;$i<=$count;$i++){

		$sum=array_sum($weight);
		$rand=self::random_float(0,$sum);

		$ts=0;
		foreach ($weight as $key=>$w){
			$ts+=$w;

			if ($ts>=$rand){
				$result[]=$key;
				unset($weight[$key]);
				break ;
			}
		}

	}

	if ($count==1){
		return $result[0];
	}


	shuffle($result);
	return $result;


}


public static function IfChance($chance,$win,$lose) {
	$r=mt_rand(1,1000);
	if ($r <= $chance * 1000){
		return $win;
	}
	return $lose;
}


public static function factorial($c){

	if ($c==1){
		return 1;
	}
	else return $c*self::factorial($c-1);

}

public static function binom($k,$n){

	return self::factorial($n)/(self::factorial($k)*self::factorial($n-$k));

}

public static function to100($a,$sto=1){
    $sum= array_sum($a);
    foreach($a as &$b){
	$b/=$sum*$sto;
    }

    return $a;


}



}

