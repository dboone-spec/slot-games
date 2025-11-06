<?php

class Slot_Agt_Keno extends Math
{

    public $betcoin   = 1;
    public $win   = 0;
    public $come   = [];
    public $nums   = [];
    public $amount    = 1;
    public $numsCount = 20;
    public $comeCount = 80;
    public $game_id;

    public function forceBars(){

    }

    public function __construct($name) {
        $group = 'keno';
        $this->group = $group;
        $this->name = $name;
        $this->config = Kohana::$config->load("$group/$name");
        $this->gameId();
    }

    public function spin()
    {
        return $this->gennums();
    }
	
	public function getTotalFreeCount() {
        return 0;
    }

    public function double() {

        if (game::data('can_double') != 1) {
            throw new Exception('cant double');
        }

        $this->doubleclass = new Double_Agt($this->gameId());

        $this->bettype = 'double';
        $this->amount = game::data('first_bet');
        $this->step = game::data('step',0);
        if($this->step>=5) {
                throw new Exception('cant double. max steps limit');
        }
        $this->first_bet = game::data('first_bet');

        $this->doubleclass->select = $this->select;
        $this->doubleclass->amount = game::data('win');


        $i = 0;
        $exit = false;
        $min = PHP_INT_MAX;
        $method = '';

        do {

            $this->doubleclass->clear();
            $this->doubleclass->select();
            $win = $this->doubleclass->win();
            //сколько можем выиграть?
            if (bet::HaveBankAmount($win)) {
                $exit = true;
                $method = 'bank';
            }

            //минимально возможный выигрыш
            if ($win < $min) {
                $min = $this->win;
                $state = $this->doubleclass->state;
            }

            //нет вариантов
            if ($i >= 50) {
                //закат солнца вручную
                $this->doubleclass->state = $state;
                $exit = true;
                $method = 'hand';
                continue;
            }

            $i++;
        } while (!$exit);

        if ($i == 1) {
            $method = 'random';
        }

        $this->win_all = $this->doubleclass->win();
        $this->double_result = $this->doubleclass->state;

        $data = [];
        if ($this->win_all > 0) {
            $data['can_double'] = 1;
            $data['step']=$this->step+1; //TODO. проверить, чтобы в случае ошибки, не попадало в сессию
            $data['first_bet']=$this->win_all;
        }
        else {
            $data['can_double'] = 0;
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }


        $data['win'] = $this->win_all;
        //TODO поправить из другого конфига
        if($this->step==5) {
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }

        $bet['amount'] = $this->amount;
        $bet['come'] = $this->doubleclass->come();
        $bet['result'] = $this->doubleclass->result();
        $bet['win'] = $this->win_all;
        $bet['game_id'] = $this->game_id;
        $bet['game'] = game::session()->game . ' double';
        $bet['method'] = $method;


        bet::make($bet,  $this->bettype, $data);
    }

    public function gennums()
    {
        $nums=[];

        $a = range(1,$this->comeCount);

        while (count($nums)<$this->numsCount) {
            $r=$a[$this->array_rand($a)];
            if (!in_array($r,$nums)){
                $nums[]=$r;
            }
        }
        $this->nums=$nums;
        return $nums;
    }

    public function win() {


        array_walk($this->come,function (&$item,$key){
            $item=(int) trim($item);
            if ($item<1 || $item>80){
                $item=-1;
            }
        });
        $this->come=array_unique($this->come);

        foreach ($this->come as $key=>$c){
            if ($c==-1){
                unset($this->come[$key]);
            }
        }


        $cnt = count(array_intersect($this->nums,$this->come));
        $win = $this->config['pay'][count($this->come)][$cnt];

        if (count($this->come)<1 || count($this->come)>10 ){
            $win=0;
        }


        $win_all=$win*$this->amount;
        $this->correctMaxWin($win_all);

        return $win_all;
    }



    public function bet()
    {
        $data = game::data();

        if(!isset($data['history'])) {
            $data['history']=[];
        }

        $method = 'random';


        $i = 0;
        $pos=[];
        $firstWin=-1;
        $exit = false;
        $min = PHP_INT_MAX;
        $needZero=bet::needZero();

        do {
            $nums = $this->gennums();
            $win_all = $this->win();

            if ($i==0){
                $firstWin=$win_all;
            }

            if($needZero){
                if ($win_all==0){
                    $exit = true;
                }
            }
            else{
                if (bet::HaveBankAmount($win_all)) {
                    $exit = true;
                }
            }


            //минимально возможный выигрыш
            if ($win_all < $min) {
                $min = $win_all;
                $pos = $nums;
                $method = $needZero ? 'zero' : 'bank';
            }

            //нет вариантов
            if ($i >= 50) {
                //закат солнца вручную
                $this->nums = $pos;
                $win_all = $this->win();
                $exit = true;
                $method = $needZero ? 'zero' : 'bank';
            }
            $i++;
        } while (!$exit);


        $method = $i > 1 ? $method : 'random';

        $this->win      = $win_all;
        $this->win_all      = $win_all;

        $data['amount']     = $this->amount;
        $data['win']        = $win_all;
        $data['step']        = 0;
        $data['comb']       = $nums;
        $data['history'][]  = $nums;

        $data['history']=array_slice($data['history'],-10,10);

        $data['can_double'] = (int) ($win_all > 0);
        $data['first_bet']  = $win_all;

        $bet['amount'] = $this->amount;
        $bet['come']   = $this->come;
        $bet['result'] = json_encode($nums);
        $bet['win']    = $win_all;
        $bet['method'] = $method;
        $bet['game_id']=$this->game_id;
        $bet['firstWin'] = $firstWin;

        bet::make($bet,'normal',$data);


        return 0;
    }

    public function sym()
    {
        return $this->nums;
    }

    public function extrasym()
    {
        return [];
    }

    public function gameId()
    {
        if(!$this->game_id)
        {
            $this->game_model = ORM::factory('Game')
                ->where('provider','=','our')
                ->where('brand','=','agt')
                ->where('name','=',$this->name)
                ->find();
            $this->game_id = $this->game_model->id;
        }

        return $this->game_id;
    }

}
