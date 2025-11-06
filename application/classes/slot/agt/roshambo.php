<?php

class Slot_Agt_Roshambo extends Math
{

    public $betcoin   = 1;
    public $win   = 0;
    public $win_sym   = 0;
    public $come   = [];
    public $amount    = 1;
    public $hands_count    = 2;
    protected $_hands    = [];
    public $win_hands    = [];
    public $game_id;

    public function forceBars(){

    }

    public function __construct($name) {
        $group = 'roshambo';
        $this->group = $group;
        $this->name = $name;
        $this->config = Kohana::$config->load("$group/$name");
        $this->gameId();

        if($this->hands_count<2) {
            throw LogicException('wrong hands count');
        }
    }

    public function spin()
    {
        $this->genhands();
        return $this->win();
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

    public function genhands()
    {

        $this->hands_count = count($this->config['pay'][$this->betcoin]);

        $this->_hands = [];
        $posible_hands=[1,2,3];

        for($i=0;$i<$this->hands_count;$i++) {
            $this->_hands[$i]=$this->array_rand_value($posible_hands);
        }

        return $this->_hands;
    }

    protected function _winSym($a){
        if (count($a)!=2){
            return 0;
        }

        sort($a);
        if ($a[0]==1 and $a[1]==3){
            return 1;
        }

        return max($a);
    }

    public function win($come=null) {

        $this->win=0;
        $setWinSym=false;
        if(is_null($come)) {
            $setWinSym=true;
            $come=$this->come;
        }

        $hands=$this->_hands;

        $absolute=false;
        if($this->hands_count>=3 && array_count_values($hands)[$this->_hands[$come]]==1){
            $absolute=true;
        }

        $hands=array_unique($hands);
        $winSym=$this->_winSym($hands);

        if($setWinSym) {
            $this->win_sym=$winSym;
        }

        $win=0;

        if($winSym==0) {
            $win=$this->config['pay'][$this->betcoin][$come]['draw'];
        }
        elseif($this->_hands[$come]==$winSym) {
            $win=$this->config['pay'][$this->betcoin][$come]['win'];
            if($absolute && $this->config['pay'][$this->betcoin][$come]['absolute']>0) {
                $win=$this->config['pay'][$this->betcoin][$come]['absolute'];
            }
        }

//        file_put_contents('rosh','absoulte: '.((int) $absolute).'; HANDS ALL: '.print_r($this->_hands,1).'; hands: '.print_r($hands,1).'; betcoin: '.$this->betcoin.'; come: '.$this->come.'; winSym: '.$winSym.'; win: '.$win.PHP_EOL, FILE_APPEND);

        $win_all=$win*$this->amount;
        $this->correctMaxWin($win_all);

        return $win_all;
    }



    public function bet()
    {

	$amount = $this->amount;
        $no = [];

        $error = bet::error($amount, $no, false);
        if ($error > 0) {
            return $error;
        }

        $data = game::data();

        if(!isset($data['history'])) {
            $data['history']=[];
        }

        if(!isset($data['history'][$this->betcoin])) {
            $data['history'][$this->betcoin]=[];
        }

        $method = 'random';


        $i = 0;
        $pos=[];
        $firstWin=-1;
        $exit = false;
        $min = PHP_INT_MAX;
        $needZero=bet::needZero();

        do {
            $win_all = $this->spin();
            $result = $this->_hands;

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
                $pos = $result;
                $method = $needZero ? 'zero' : 'bank';
            }

            //нет вариантов
            if ($i >= 50) {
                //закат солнца вручную
                $this->_hands = $pos;
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
        $data['comb']       = $result;
        $data['history'][$this->betcoin][]  = $result;
        $data['win_per_line']=$this->win;
        $data['history'][$this->betcoin]=array_slice($data['history'][$this->betcoin],-6,6);

        $data['can_double'] = (int) ($win_all > 0);
        $data['first_bet']  = $win_all;
        $data['step']  = 0;

        $bet['amount'] = $this->amount;
        $bet['come']   = $this->come;
        $bet['result'] = json_encode($result);
        $bet['win']    = $win_all;
        $bet['method'] = $method;
        $bet['game_id']=$this->game_id;
        $bet['firstWin'] = $firstWin;

        bet::make($bet,'normal',$data);


        return 0;
    }

    public function sym()
    {
        return $this->_hands;
    }

    public function allWins()
    {
        $a=[];
        for($i=0;$i<$this->hands_count;$i++) {
            $a[$i]=$this->win($i);
        }
        return $a;
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
