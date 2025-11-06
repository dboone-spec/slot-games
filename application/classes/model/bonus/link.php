<?php


class Model_Bonus_Link extends ORM {


	protected $_primary_key = 'hash';
	protected $_created_column = array('column' => 'created', 'format' => true);


	public function url() {
		return URL::site('/',true).URL::query(['bonuslink'=>$this->hash]);
	}
}
