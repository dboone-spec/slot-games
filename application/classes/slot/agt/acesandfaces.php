<?php

class Slot_Agt_Acesandfaces extends Videopoker_Game_Acesandfaces{

    public $betcoin=1;
    public $amount=1;
    public $game_id;

    public function spin() {
        return $this->gencards();
    }
	public function forceBars(){

    }
    public function double() {
        $this->doubleclass = new Double_Agt($this->gameId());
        parent::double();
    }

    public function bet($step,$hold) {
        if($step=='1') {
            $this->deal();
        }
        elseif($step=='2') {
            $this->draw($hold);
        }
    }

    public function sym() {
        return $this->cards;
    }

    public function extrasym() {
        return [];
    }

    public function gameId() {
        if(!$this->game_id) {
            $this->game_id = ORM::factory('Game')
                ->where('provider','=','our')
                ->where('brand', '=', 'agt')
                ->where('name', '=', 'acesandfaces')
                ->find()->id;
        }

        return $this->game_id;
    }



}

