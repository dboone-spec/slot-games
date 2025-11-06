<?php defined('SYSPATH') or die('No direct script access.');


class Comm {
	
protected $table;
protected $id;
	
public function __construct($table,$id)
	{	
		$this->table=$table;
		$this->id=$id;
		
		$this->form=array('text'=>'','user'=>'');
		
	}
	
	
	
	
	public function get($id=0)
	{
		$sql="select u.visible_name as name, u.avatar, c.text, c.time 
			  from {$this->table}_comments c 
				  join users u on u.id=c.user_id
			  where c.{$this->table}_id=:id 
			  order by c.time";
		$result=DB::query(Database::SELECT,$sql)->param(':id',(int) $this->id)->execute()->as_array();
		
		foreach ($result as $key=>$comm){
			if(empty($result[$key]['avatar'])){
				$s=md5($comm['name']);
				$sum=0;
				for($i=0;$i<strlen($s);$i++){
					$d=$s[$i];
					switch ($d) {
						case 'a': $d=10;break;
						case 'b': $d=11;break;
						case 'c': $d=12;break;
						case 'd': $d=13;break;
						case 'e': $d=14;break;
						case 'f': $d=15;break;		
					} 

					$sum+=(int) $d;
				}

				$result[$key]['avatar']='/images/smile/'.(($sum % 42)+1).'.gif';
			}
		}
		
		return $result;
	}

 public function render($sh='comm/basic'){
		
		$result=View::factory($sh);
		
		if (count($this->errors)>0){
			$result->errors=$this->errors;		
		}
		
		$result->coms=$this->get();
		$result->form=$this->form;
		
		return $result;
	
	}

protected $_rules = array
	(
		'autor'			=> array
		(
			'not_empty'		=> NULL,
			'min_length'	=> array(2),
			'max_length'	=> array(25),
			'regex'		=>array('/^[a-zа-яё0-9_ !?<>@#$]*$/iu'),
		),
		'text'				=> array
		(
			'min_length'	=> array(10),
			'not_empty'		=> NULL,
		),
	);	
	

	
	
public $errors;	
protected $form;

protected function getip(){
	$ip=$_SERVER['REMOTE_ADDR'];
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){$ip.='|'.$_SERVER['HTTP_CLIENT_IP'];}
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip.='|'.$_SERVER['HTTP_X_FORWARDED_FOR'];}
	
	return $ip;
}


protected function clear($value){

	$value=(string) $value; 
	$value=strip_tags($value,$this->tags); 
	$value=str_replace("\r","",$value);
	$value=str_replace("\n","",$value);
	return $value;
}


public function validate_robot(Validate $array, $field){
	$dt=date('d-m-Y');
	
	list($d,$m,$y)=explode('-',$dt);
	
	if (!(($array[$field]==$m*$d*$y)or($array[$field]==$m*$d*($y-1900)))) {
		$array->error($field, 'Включите javascript!', array($array[$field]));
	}
	
}

public function addcom($array)
	{	
		$errors=array();
		
		if (!auth::$user_id){
			$errors[]='Писать комментарии может только зарегистрированный пользователь';
			return false;
		}
		

		$text=$array['text'];
		$text=strip_tags($text);
		$text=nl2br($text);
		$sql="insert into {$this->table}_comments
				set {$this->table}_id=:news_id,
					user_id=:user_id,
					text=:text,
  					time=:time,
  					ip=:ip";  
			  
		$result=DB::query(Database::INSERT,$sql)
		->param(':news_id',$this->id)
		->param(':user_id',auth::$user_id)
		->param(':text',$text)
		->param(':time',time())
		->param(':ip',$this->getip())
		->execute();
		
		$a=new Model_Action();
		$a->item=$this->table.'_comment';
		$a->item_id=$result[0];
		$a->action='new';
		$a->save();
		
		return true;
	}
	
}