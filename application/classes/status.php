<?php

class Status {

	
	protected $type = '';
	private $param = array();
	public static $instances=array();
	protected $numeric=['bank','users'];


	private function __construct($type) {
		$this->type=$type;
		$this->load();
	}
	
	public static function instance($type='main')
	{
		if (isset(Status::$instances[$type])){
			return Status::$instances[$type];
		}
		Status::$instances[$type] = new Status($type);
		return Status::$instances[$type];
	}
	
	
	public function __get($name) {
		return $this->get($name);
	}
	
	public function get($name,$default=null){
		return arr::get($this->param,$name,$default);
	}
	
	public function set($name, $value){
		
		$this->__set($name, $value);
	}


	public function __set($name, $value) {
		
		
		if (in_array($name,$this->numeric)){
			throw new Exception("Cann't set numeric value '$name'. Use sql update");
		}
		
		if (isset($this->param[$name])){
			
			$sql='update status set value=:value where type=:type and id=:name';
			db::query(Database::UPDATE,$sql)->param(':type',$this->type)
											->param(':name',$name)
											->param(':value',$value)
											->execute();
			
		}
		else{
			$sql='insert into status (type,id,value) values (:type,:name,:value)';
			db::query(Database::UPDATE,$sql)->param(':type',$this->type)
											->param(':name',$name)
											->param(':value',$value)
											->execute();
											
			
		}
		
		$this->param[$name]=$value;

	}

	
	private function load() {
		
		$sql='select id,value,value_numeric from status where type=:type';
		$data=db::query(1,$sql)->param(':type',$this->type)->execute()->as_array();
		
        /*
         * для создания банка для новый валюты, если его нет
         */
        if(!count($data)) {
            $status = new Model_Status([
                'id' => 'bank',
                'type' => $this->type,
            ]);
            
            if(!$status->loaded()) {
                $status->value=0;
                $status->value_numeric=300000;
                $status->last = 0;
                $status->id='bank';
                $status->type=$this->type;
                $status->save()->reload();
            }
            
            $this->param[$status->id]=$status->value_numeric;
            
            $s = new Model_Status([
                'id' => 'users',
                'type' => $this->type,
            ]);
            
            if(!$s->loaded()) {
                $s->value=0;
                $s->value_numeric=0;
                $s->last = 0;
                $s->id='users';
                $s->type=$this->type;
                $s->save()->reload();
            }
            
            $this->param[$s->id] = $s->value_numeric;
        }
        
		foreach ($data as $param) {
			$key='value';
			if(in_array($param['id'],$this->numeric)){
				$key='value_numeric';
			}
			$this->param[$param['id']]=$param[$key];
		}

	}
	

	

}


