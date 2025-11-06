<?php defined('SYSPATH') or die('No direct script access.');

class Database_PostgreSQL extends Kohana_Database_PostgreSQL
{

	public function __construct($name, $config){

		parent::__construct($name, $config);
		$this->get_connection();
	}

	public function get_connection()
	{
		$this->connect();
		return $this->_connection;
	}


	public function direct_prepare($name,$sql){

		pg_prepare($this->_connection, $name, $sql);

	}


	public function direct_query($sql,$return=true){

		$result=pg_query($this->_connection, $sql);

		if ($return){
			$data=array();

			if ($result){
				while ($row=pg_fetch_array($result, null, PGSQL_ASSOC)){
					$data[]=$row;
				}
			}

			return $data;
		}
	}

	public function direct_execute($name,$param=array(),$return=null){

		$result=pg_execute($this->_connection,$name,$param);




		if ($result){
			$data=pg_fetch_array($result, null, PGSQL_ASSOC);



			if (!$data){
				return false;
			}


			if (!empty($return)){
				return $data[$return];
			}

			return $data;
		}

		return false;



	}

	public function direct_send_execute($name, $param = array())
	{
		@pg_send_execute($this->_connection, $name, $param);
	}






}
