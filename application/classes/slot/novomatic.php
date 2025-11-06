<?php

class Slot_Novomatic extends Slot_Calc{

	public $mud=0;
	public $game_id;


    public function __construct( $name) {
		parent::__construct('novomatic', $name);

        $game = new Model_Game(['provider'=>'our','name'=>$name]);
        $off_game =  new Model_Office_Game(['game_id'=>$game->id,'office_id'=>OFFICE]);
        $bars='bars_'.$off_game->z*100;
        $this->bars=$this->config[$bars]??$this->config['bars'];

//        $this->bars = arr::get($this->config, 'bars');
        $this->barcount = count($this->bars);
        $this->barFree = arr::get($this->config, 'barFree', $this->bars);

        $this->game_id = $game->id;
	}

	public function double(){

		$this->doubleclass=new Double_Novomatic($this->game_id);

		parent::double();



	}



}

