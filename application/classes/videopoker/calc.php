<?php

class Videopoker_Calc extends Videopoker_Root{

	public $paycard=10;
	public $wincomb='';
        protected $config=[];
        protected $cardCount=5;


        public function __construct($name) {
            parent::__construct();
            $this->config=Kohana::$config->load('videopoker/'.$name);
            $this->paycard=$this->config['paycard'] ?? $this->paycard;

            $this->game_model = ORM::factory('Game')
                ->where('provider','=','our')
                ->where('brand', '=', 'agt')
                ->where('name', '=', $name)
                ->find();

            $this->game_id = $this->game_model->id;

        }



	public function forceBars(){

        }


        public function win($card){
		$level=$this->level($card);
                $pay=$this->config['pay'][$this->betcoin][$level];
		return $pay*$this->amount;

	}



	public static function tonumber($a){
		//TODO сделать как нравится, текущий формат был привязан к флешке
                return $a;
		//1 вини  1- т
		//2 крести 2-т
		//3	буби 2-т
		//4 черви

		if (is_array($a)){
			foreach ($a as $key=>$value){
				$a[$key]=self::tonumber($value);
			}
			return $a;
		}

		$num=card::num($a);
		$suit=card::suit($a);
		return $num.$suit;


	}

	protected $deck=null;

	public function deck(){

		if (empty($this->deck)){

			$this->deck=[];
			for($i=1;$i<=52;$i++){
				$this->deck[$i]=1;
			}
		}

		return $this->deck;

	}

    public $cards=[];
    public $win_all=0;


    public function getNewCard(){

        //return math::getRandWeight($this->deck());
        return $this->array_rand($this->deck());

    }

    public function gencards() {
        $this->cards=[];
        $j=1;

        while (count($this->cards)<$this->cardCount) {
            $r=$this->getNewCard();
            if (!in_array($r,$this->cards)){
                $this->cards[$j]=$r;
                $j++;
            }
        }


        $this->win_all = $this->win($this->cards);

        return $this->cards;
    }


    public function deal(){

		$error=bet::error($this->amount);
		if ($error>0){
			return $error;
		}

		$i=0;
		$exit=false;
		$min=PHP_INT_MAX;
                $method='random';
                $firstWin=-1;

                $needZero=bet::needZeroPoker();
		do {
		//генерим карты

                        $card=$this->gencards();

			//сколько можем выиграть?
			$win_all=$this->win_all;

                        if($needZero){
                            if ($this->win_all==0){
                                $exit = true;
                            }
                        }
                        else{
                            if (bet::HaveBankAmount($win_all,$this->amount)){
				$exit=true;
                            }
                        }
                        
                        if ($i==0){
                            $firstWin=$win_all;
                        }



			//минимально возможный выигрыш
			if ($win_all<$min){
				$min=$win_all;
				$card1=$card;
                                $method = $needZero ? 'zero' : 'bank';    
			}

			//нет вариантов
			if ($i>=50){
				//закат солнца вручную
				$win_all=$min;
				$card=$card1;
				$exit=true;
				$method = $needZero ? 'zero' : 'bank';
				continue;
			}

			$i++;
		}	while (!$exit);

                if ($i==1){
                    $method='random';
		}
                
		$this->cardon=$card;
		//что оставим
		$a=$this->base($card);
		$this->hold=card::light($card,$a);

		$data['comb']=$card;
		$data['method']=$method;
		$data['hold']=$this->hold;
		$data['holdcomb']=$this->config['level'][$this->level($card)];
		$data['amount']=$this->amount;
		$data['pokerStep']=2;
		$data['li']=$this->betcoin;
		$data['game_id']=$this->game_id;
                $data['firstWin'] = $firstWin;

//		game::session()->flash($data,true);
                bet::pokerstart($this->amount,$data);
		return 0;

	}



	public function draw($hold){


		$data=game::data();


		$begin_cards=(array) arr::get($data,'comb',[]);



		if (count($begin_cards)!=$this->cardCount){
			return 5;
		}

		$this->amount=arr::get($data,'amount',0);
		$this->betcoin=arr::get($data,'li',0);


		$i=0;
		$exit=false;
		$min=PHP_INT_MAX;
		$method='';
                $firstWin=-1;


                $needZero=bet::needZeroPoker();

		do {

			$card=[];
			$j=1;
			while (count($card)<$this->cardCount) {
				if (in_array($j,$hold)){
					$card[$j]=$begin_cards[$j];
					$j++;
				}
				else{
					$r=$this->getNewCard();
					if (!in_array($r,$card) and !in_array($r,$begin_cards)){
						$card[$j]=$r;
						$j++;
					}
				}
			}

			//сколько выиграли?
			$win_all=$this->win($card);
                        $this->win_all = $win_all;
                        $this->cards = $card;

			if($needZero){
                            if ($this->win_all==0){
                                $exit = true;
                            }
                        }
                        else{
                            if (bet::HaveBankAmount($win_all,$this->amount)){
				$exit=true;
                            }
                        }
                        
                        if ($i==0){
                            $firstWin=$win_all;
                        }

			//минимально возможный выигрыш
			if ($win_all<$min){
				$min=$win_all;
				$card1=$card;
                                $method = $needZero ? 'zero' : 'bank';
			}

			//нет вариантов
			if ($i>=50){
				//закат солнца вручную
				$win_all=$min;
				$card=$card1;
				$exit=true;
				$method = $needZero ? 'zero' : 'bank';
			}

			$i++;
		}	while (!$exit);

		if ($i==1){
                    $method='random';
		}

		$this->cardon=$card;
		$this->win=$win_all;
		$this->winlight=true;
                $this->level=$this->level($card);
		$this->wincomb=$this->config['level'][$this->level];

		$data['amount']=$this->amount;
		$data['pokerStep']=1;
		$data['win']=$win_all;
		$data['wincard']=$this->wincard;
		$data['comb']=$card;
                $data['can_double'] = (int) ($win_all>0);
                $data['first_bet'] = $win_all;
               

		$bet['amount']=$this->amount;
		$bet['come']=$this->wincomb;
		$bet['result']=card::print_card($card);
		$bet['win']=$win_all;
		$bet['method']=$method;
                $bet['game_id']=$this->game_id;
                $bet['firstWin'] = $firstWin;

		bet::make($bet,'normal',$data);

		bet::prepareToHistory([
            's1'=>$begin_cards,
        ]);
		
		bet::$arrToHistory['li']=$this->betcoin;

		return 0;

	}

	protected $doubleclass;
    public $double_result;
    public $first_bet;

    public function double() {

        if (game::data('can_double') != 1) {
            throw new Exception('cant double');
        }


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
                $min = $this->win_all; //todo check. was win
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







}

