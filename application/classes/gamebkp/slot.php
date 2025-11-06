<?php

//для поддержания структуры в будущем

abstract class game_slot {
	
	protected $_session = [];

	public function __construct() {
		$this->reload_session();
	}
	
	abstract public function spin();
	abstract public function save_win();
	abstract public function double($select);
	abstract public function bonus_game();
	abstract public function init();
	abstract public function restore();
	abstract public function get_balance();
    abstract public function amount();

    public function save() {
		game::save($this->_session);
		return $this;
	}
	
	public function reload_session() {
		game::session()->reload();
		$this->_session = game::data();
		return $this;
	}
	
}
