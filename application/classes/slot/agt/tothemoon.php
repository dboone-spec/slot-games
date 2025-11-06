<?php

class Slot_Agt_Tothemoon extends math{

    public $win_all=0;
    public $amount=1;
    public $game_id;
    public $round_id;
    public $conn_lost=false;
    public $bet_type;
    public $bet_id;
    public $isFreespin=false;
    public $isFreespinFromApi=false;
    public $isLuckyFreespin=false;
    public $isFreespinFromGame=false;

    public function __construct($name) {
        $group = 'moon';
        $this->group = $group;
        $this->name = $name;
        $this->gameId();
    }

    public function spin() {
        return $this->sym();
    }
	public function forceBars(){

    }
    public function double() {

    }

    public function SetFreeSpinMode($from_api=false,$from_lucky=false,$from_game=false) {
        if(((int) $from_api + (int) $from_lucky + (int) $from_game)>1) {
            throw new LogicException('can not be FS from diff sources');
        }
        $this->isFreespin=true;
        $this->isFreespinFromApi=$from_api;
        $this->isLuckyFreespin=$from_lucky;
        $this->isFreespinFromGame=$from_game;
    }

    public function bet($win=0)
    {
        //нельзя в инфине, т.к. ставка по таким фс идет одна, в конце (можно при желании реализовать)
        if($this->isFreespinFromApi) {
            throw new LogicException('API FS NOT ALLOWED');
        }

        $no = [];
        if($this->isFreespin || $win>0) {
            $no[]=6;
        }

        $error = bet::error($this->amount, $no, $this->isFreespin);
        if ($error > 0) {
            return $error;
        }

        $method = 'random';

        if($this->conn_lost) {
            $method='disconnect';
        }
        //todo need needZero or HaveBankAmount??

        $this->win      = $win;
        $this->win_all      = $win;

        $bet['amount'] = $this->amount;
        $bet['come']   = $this->round_id;
        $bet['info']   = $this->bet_id;
        $bet['initial_id']   = $this->bet_id;
        $bet['result'] = $this->come;
        $bet['win']    = $this->win_all;
        $bet['method'] = $method;
        $bet['game_id']=$this->game_id;
        $bet['can_jp']=false;
        $bet['is_freespin'] = (int) $this->isFreespin + (int) $this->isFreespinFromApi;
        if($this->isLuckyFreespin) {
            $bet['is_freespin']=3;
        }
        if($this->isFreespinFromGame) {
            $bet['is_freespin']=4;
        }


        if($this->isFreespin) {

            $checkGame=$this->game_id;

            $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);

            if($this->win_all==0) {
                $fs->spinOneFreespin($fs->id,$this->win_all);
                $bet['info']=$fs->getTypeName();
                $bet['info'].=';'.($fs->_fs_played).'/'.$fs->fs_count;
            }

        }

		if(!$this->isFreespin && auth::user()->promo_started!==0) {
            //если это не фриспины и игрок не отказался от турнира, учавствует в нем.
            $events=auth::user()->checkEvents($this->game_model);
            if($events && $events->type=='promo') {
                //если игрок не видел окна, активируем его участие в турнире
                auth::user()->joinEvent($this->game_model,$events);
            }
        }

        bet::make($bet,'normal',game::data());


        return 0;
    }

    public function sym() {
        return $this->come;
    }

    public function extrasym() {
        return [];
    }

    public function gameId() {
        if(!$this->game_id) {
            $this->game_model = ORM::factory('Game')
                ->where('provider','=','our')
                ->where('brand', '=', 'agt')
                ->where('name', '=', $this->name)
                ->find();

            $this->game_id = $this->game_model->id;
        }

        return $this->game_id;
    }



}

