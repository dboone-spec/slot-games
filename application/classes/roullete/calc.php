<?php

class Roullete_Calc extends math{

		public $bettonum=[
				0=>[0],
				1=>[1],
				2=>[2],
				3=>[3],
				4=>[4],
				5=>[5],
				6=>[6],
				7=>[7],
				8=>[8],
				9=>[9],
				10=>[10],
				11=>[11],
				12=>[12],
				13=>[13],
				14=>[14],
				15=>[15],
				16=>[16],
				17=>[17],
				18=>[18],
				19=>[19],
				20=>[20],
				21=>[21],
				22=>[22],
				23=>[23],
				24=>[24],
				25=>[25],
				26=>[26],
				27=>[27],
				28=>[28],
				29=>[29],
				30=>[30],
				31=>[31],
				32=>[32],
				33=>[33],
				34=>[34],
				35=>[35],
				36=>[36],

				37=>[0,1],
				38=>[0,2],
				39=>[0,3],
				40=>[1,4],
				41=>[2,5],
				42=>[3,6],
				43=>[4,7],
				44=>[5,8],
				45=>[6,9],
				46=>[7,10],
				47=>[8,11],
				48=>[9,12],
				49=>[10,13],
				50=>[11,14],
				51=>[12,15],
				52=>[13,16],
				53=>[14,17],
				54=>[15,18],
				55=>[16,19],
				56=>[17,20],
				57=>[18,21],
				58=>[19,22],
				59=>[20,23],
				60=>[21,24],
				61=>[22,25],
				62=>[23,26],
				63=>[24,27],
				64=>[25,28],
				65=>[26,29],
				66=>[27,30],
				67=>[28,31],
				68=>[29,32],
				69=>[30,33],
				70=>[31,34],
				71=>[32,35],
				72=>[33,36],
				73=>[1,2],
				74=>[2,3],
				75=>[4,5],
				76=>[5,6],
				77=>[7,8],
				78=>[8,9],
				79=>[10,11],
				80=>[11,12],
				81=>[13,14],
				82=>[14,15],
				83=>[16,17],
				84=>[17,18],
				85=>[19,20],
				86=>[20,21],
				87=>[22,23],
				88=>[23,24],
				89=>[25,26],
				90=>[26,27],
				91=>[28,29],
				92=>[29,30],
				93=>[31,32],
				94=>[32,33],
				95=>[34,35],
				96=>[35,36],
				97=>[0,1,2],
				98=>[0,2,3],
				99=>[1,2,3],
				100=>[4,5,6],
				101=>[7,8,9],
				102=>[10,11,12],
				103=>[13,14,15],
				104=>[16,17,18],
				105=>[19,20,21],
				106=>[22,23,24],
				107=>[25,26,27],
				108=>[28,29,30],
				109=>[31,32,33],
				110=>[34,35,36],
				111=>[0,1,2,3],
				112=>[1,2,4,5],
				113=>[2,3,5,6],
				114=>[4,5,7,8],
				115=>[5,6,8,9],
				116=>[7,8,10,11],
				117=>[8,9,11,12],
				118=>[10,11,13,14],
				119=>[11,12,14,15],
				120=>[13,14,16,17],
				121=>[14,15,17,18],
				122=>[16,17,19,20],
				123=>[17,18,20,21],
				124=>[19,20,22,23],
				125=>[20,21,23,24],
				126=>[22,23,25,26],
				127=>[23,24,26,27],
				128=>[25,26,28,29],
				129=>[26,27,29,30],
				130=>[28,29,31,32],
				131=>[29,30,32,33],
				132=>[31,32,34,35],
				133=>[32,33,35,36],
				134=>[1,2,3,4,5,6],
				135=>[4,5,6,7,8,9],
				136=>[7,8,9,10,11,12],
				137=>[10,11,12,13,14,15],
				138=>[13,14,15,16,17,18],
				139=>[16,17,18,19,20,21],
				140=>[19,20,21,22,23,24],
				141=>[22,23,24,25,26,27],
				142=>[25,26,27,28,29,30],
				143=>[28,29,30,31,32,33],
				144=>[31,32,33,34,35,36],
				145=>[3,6,9,12,15,18,21,24,27,30,33,36],  //3..
				146=>[2,5,8,11,14,17,20,23,26,29,32,35],  //2..
				147=>[1,4,7,10,13,16,19,22,25,28,31,34],  //1..
				148=>[1,2,3,4,5,6,7,8,9,10,11,12], //1-12
				149=>[13,14,15,16,17,18,19,20,21,22,23,24], //13-24
				150=>[25,26,27,28,29,30,31,32,33,34,35,36], //25-36
				151=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18], //1-18
				152=>[2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36],   //even
				153=>[1,3,5,7,9,12,14,16,18,19,21,23,25,27,30,32,34,36],  //red
				154=>[2,4,6,8,10,11,13,15,17,20,22,24,26,28,29,31,33,35],     //black
				155=>[1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33,35],  //odd
				156=>[19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36], //19-36


		];


	//сумма ставки
	public $amount;
	//сумма выигрыша
	public $win;

	//используемые поля
	public $sectors;

	public $data=[];

	//суммы ставок на сектора
	public $bets;
	//суммы выигрышей по сектора
	public $wins;


    public $win_bets=[];
    public $lose_bets=[];
    
    
    protected $game_id;
    public function __construct($group, $name) {
        $this->game_id = ORM::factory('Game')
                ->where('provider','=','our')
                ->where('brand', '=', $group)
                ->where('name', '=', $name)
                ->find()->id;
        
        
        
    }

	public function math_correct(){


		if ($this->amount<=10){
			return 0.94;
		}

		if ($this->amount<=15){
			return 0.96;
		}

		if ($this->amount<20){
			return 0.98;
		}

		return 1;


	}



	//count - количество секторов на которые сделаны ставки
	public function z(){

		return 0.98;

		$count=count($this->sectors);

		if ($count>34 or $count==0){
			return 1*$this->math_correct();
		}

		if ($count>=32){
			return 0.99*$this->math_correct();
		}

		if ($count>=28){
			return 0.98*$this->math_correct();
		}

		if ($count>=25){
			return 0.97*$this->math_correct();
		}

		if ($count>=20){
			return 0.965*$this->math_correct();
		}

		return 0.965*$this->math_correct();


	}




	//получаем случайное число в зависимости от количества заставленных секторов
	//$sectors - массив с секторами на которые сделаны ставки
	protected function getnum(){
		return parent::random_int(0,36);

		//количество секторов со ставками
		$counts=count($this->sectors);

		//реальный процент возврата
		$zreal=36/37;
		//коэффициент выплат
		$k=36;
		$v=[];


		//для чисел на которые поставили

		$all_spizdili=0;
		//новый шанс на выпадение числа
		$zn=$this->z();
		//реальный шанс на выпадение
		$vreal=$zreal/$k;
		//новый шанс на выпадение
		$vst=$vreal*$zn;



		//для чисел без ставок
		//спиздили вероятность
		$spizdili=$vreal-$vst;
		$all_spizdili=$spizdili*$counts;
		//перераспределенная вероятность
		if ($all_spizdili>0){
			$vno=$vreal+$all_spizdili/(37-$counts);
		}
		else{
			$vno=$vreal;
		}

		for($i=0;$i<=36;$i++){
			$v[$i]=in_array($i,$this->sectors) ?  $vst : $vno;
		}

		return $this->getRandWeight($v);

	}






	public function parsebet($str=null){

        if($str) {
            $row=explode('|',$str);
            unset($row[0]);
            foreach($row as $r){
                list($num,$bet)=explode(':',$r);
                $this->data[$num]=$bet;
            }
        }

		$this->amount=0;
		$this->bets=[];
		$this->sectors=[];

		//используемые номера и ставки на них
		foreach ($this->data as $num=>$bet){
			$this->amount+=$bet;

            if(!is_array($this->bettonum[$num])) {
                $this->bettonum[$num]=[$this->bettonum[$num]];
            }

			foreach ($this->bettonum[$num] as $number){

				if (!isset($this->bets[$number])){
					$this->bets[$number]=0;
				}
				$this->sectors[]=$number;
				$this->bets[$number]+=$bet/count($this->bettonum[$num]);
			}


		}

		$this->sectors=array_unique($this->sectors);

	}



	//1 не авторизован
	//2 не завершенная игра
	//3 закртыто на хуй
	//4 нет денег
	//5 не верный формат данных
	//6 не верный формат ставки
	//7 сервер не доступен
	//8 разрыв связи
	//9 jackpot
	public function bet(){

		$error=bet::error($this->data);
		if ($error>0){
			return $error;
		}


		$this->win=0;

		$wins=[];


		$i=0;
		$min=PHP_INT_MAX;
		$num=0;
		$wins1=[];
		$exit=false;


		while (!$exit){
			$i++;
			$win_all=0;
			$this->num=$this->getnum();
			//считаем выигрыш
			foreach ($this->data as $come=>$amount){

				$win=0;
				if (in_array($this->num,$this->bettonum[$come])){
					$win=$amount*36/count($this->bettonum[$come]);
					//$win=$win>$limit['maxwin'] ?  $limit['maxwin'] : $win;
					$win=round($win,2);
				}
                else {
                    $loses[$come]=$amount;
                }
				$wins[$come]=$win;
				$win_all+=$win;
			}



			//выиграл меньше чем возможно
			if (bet::HaveBankAmount($win_all,$this->amount)){
				$exit=true;
				$method='win_bank';
				continue;
			}

			//минимально возможный выигрыш
			if ($win_all<$min){
				$min=$win_all;
				$num=$this->num;
				$wins1=$wins;

			}

			//нет вариантов
			if ($i>=50){
				//закат солнца вручную
				$win_all=$min;
				$this->num=$num;
				$wins=$wins1;
				$exit=true;
				$method='hand';
				continue;
			}



		}

		if ($i==1){
			$method='random';
		}

        $all_amount=0;

		foreach ($this->data as $come=>$amount){

            $all_amount+=$amount;

            if($wins[$come]>0) {
                $this->win_bets[$come] = $amount;
            }
            else {
                $this->lose_bets[$come] = $amount;
            }
		}

        $result=[
            'num'=>$this->num,
            'winbets'=>$this->win_bets,
            'lose_bets'=>$this->lose_bets,
        ];

        bet::make([
            'amount'=>$all_amount,
            'come'=> json_encode($this->data),
            'result'=>json_encode($result),
            'win'=>$win_all,
            'game_id'=>$this->game_id,
            'method'=>$method,
            'can_jp'=>false,
        ],'normal');

		/*
		database::instance()->begin();

		try {

			foreach ($this->data as $come=>$amount){

				$bet=new Model_Bet();
				$bet->amount=$amount;
				$bet->come=$come;
				$bet->result=$this->num;
				$bet->win=$wins[$come];
				$bet->game_id=$id;
				$bet->method=$method;



			}


			/*
			$sql='update users
				set amount=amount-:amount+:win
				where id=:uid';

			db::query(1,$sql)->param(':amount',(float) $this->amount)
							->param(':win',(float) $win_all)
							->param(':uid',auth::$user_id)
							->execute();


			//для счетчиков

			$sql='update counters set "in"="in"+:in, "out"="out"+:out where office_id = :oid and game = :game and type=:type and "table"=:table RETURNING id';
			$rows=db::query(Database::UPDATE,$sql)->param(':in',(float) $this->amount)
							->param(':out',(float) $win_all)
							->param(':oid',OFFICE)
							->param(':type',game::session()->type)
							->param(':game',game::session()->game)
							->param(':table',game::session()->table)
							->execute();

			if ($rows==0){
				$sql='insert into counters ( "in", "out", office_id,  type, game,  "table")
									values(:in,    :out, :oid,       :type, :game, :table)  RETURNING id';

				db::query(Database::UPDATE,$sql)->param(':in',(float) $this->amount)
							->param(':out',(float) $win_all)
							->param(':oid',OFFICE)
							->param(':type',game::session()->type)
							->param(':game',game::session()->game)
							->param(':table',game::session()->table)
							->execute();
			}



			database::instance()->commit();

		}
		catch  (Exception $e){
			database::instance()->rollback();
			$win_all=0;
			return 8;
		}
		*/

		$this->win=$win_all;

        $this->winBets = $wins;

		return 0;

	}


}

