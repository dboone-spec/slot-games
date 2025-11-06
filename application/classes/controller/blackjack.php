<?php

class Controller_Blackjack extends Controller_Base{
	
	public $template='layout/game';

	public function action_game(){
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games.cards');
		if (!isset($games[$id])){
			throw new HTTP_Exception_404;
		}
		
		
		$view=new View('site/blackjack/game');
		$view->game=$games[$id];
		$view->name=$id;
		$this->template->content=$view;
		
	}
	
	
	
	public function action_init(){
		
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games.cards');
		if (!isset($games[$id])){
			throw new HTTP_Exception_404;
		}
		
		
		$this->auto_render=false;
		

		$action =$this->request->post('ACTION');
		$type = $this->request->post('TYPE');
		//ставка
		$amount = $this->request->post('BET');

		$ans=[];
		
		//вход
		if ( $action == "ENTER" ){
			$ans["RESULT"]="OK";
			$ans["BALANCE"]=auth::user()->amount;
		}
		
		game::session('bj',$id);
		
		//ставка
		if ( $action == "MAKEBET" ){
			
			$calc=new Bj_Calc();
			$calc->amount=$amount;
			$m=$calc->bet();
			if ($m==0){
				//ставка хз что 
				$bet3="{$amount}.0|{$amount}.0|0.0|{$amount}.0|0.0";
				$ans["STATE"]=$calc->state;
				$ans["CARDSDEALER"]= implode('|',$calc->tonumber($calc->dealer)).'|52';
				$ans["SCORESPLAYER"]=$calc->ScorePlayer();
				$ans["CARDSPLAYER"]=implode('|',$calc->tonumber($calc->player));
				$ans["BET"]=$bet3;
				$ans["RESULT"]="OK";
				$ans["BALANCE"]=auth::user(true)->amount;
			}
			else{
				$ans["RESULT"]="ERROR";
				$ans["BALANCE"]=auth::user(true)->amount;
			}
			
			
		}


		if ( $action == "MOVE" ){
			
			//не страхуемся
			if($type=="INSURANCENO"){
				$calc=new Bj_Calc();
				$calc->noinsur();
				
				
				$bet3 = $calc->amount.".0|{$calc->amount}.0|0.0|{$calc->amount}.0|0.0";
				$state = "0|7|0|0|0|0";
				$ans["STATE"]=$calc->state;
				$ans["CARDSDEALER"]= implode('|',$calc->tonumber($calc->dealer)).'|52';
				$ans["SCORESPLAYER"]=$calc->ScorePlayer();
				$ans["CARDSPLAYER"]=implode('|',$calc->tonumber($calc->player));
				$ans["BET"]=$bet3;
				$ans["RESULT"]="OK";
				$ans["BALANCE"]=auth::user()->amount;
				
				
			}
			if($type=="INSURANCEYES"){
				
				$calc=new Bj_Calc();
				$calc->insur();
				$bet3 = $calc->amount.".0|{$calc->amount}.0|{$calc->amount}.0|{$calc->amount}.0|0.0";
				$ans["STATE"]=$calc->state;
				$ans["CARDSDEALER"]= implode('|',$calc->tonumber($calc->dealer)).'|52';
				$ans["SCORESPLAYER"]=$calc->ScorePlayer();
				$ans["CARDSPLAYER"]=implode('|',$calc->tonumber($calc->player));
				$ans["BET"]=$bet3;
				$ans["RESULT"]="OK";
				$ans["BALANCE"]=auth::user(true)->amount;

			}

			if ( $type == "DOUBLE" ){
				
				$calc=new Bj_Calc();
				$calc->double();
				$calc->hit();
				$type='STAND';


			}

			//Берем ещё карту
			if ( $type == "HIT" )
			{
				
				$calc=new Bj_Calc();
				$m=$calc->hit();
				if ($m!=0){
					$ans["RESULT"]="ERROR";
					$ans["BALANCE"]=auth::user()->amount;
				}
				else{
					$bet3 = $calc->amount.".0|{$calc->amount}.0|0.0|{$calc->amount}.0|0.0";
					//Перебор у игрока
					if ($calc->ScorePlayer()>21 )
					{
						$ans["STATE"]=$calc->state;
						$ans["CARDSDEALER"]=implode('|',$calc->tonumber($calc->dealer));
						$ans["SCORESPLAYER"]=$calc->ScorePlayer();
						$ans["SCORESDEALER"]=$calc->ScoreDealer();
						$ans["CARDSPLAYER"]=implode('|',$calc->tonumber($calc->player));
						$ans["BET"]=$bet3;
						$ans["RESULT"]="OK";
						$ans["BALANCE"]=auth::user(true)->amount;
						$ans["PAYOUT"]=$calc->payout;
					}
					else
					{
						$ans["STATE"]=$calc->state;
						$ans["CARDSDEALER"]=implode('|',$calc->tonumber($calc->dealer)).'|52';
						$ans["SCORESPLAYER"]=$calc->ScorePlayer();
						$ans["CARDSPLAYER"]=implode('|',$calc->tonumber($calc->player));
						$ans["BET"]=$bet3;
						$ans["RESULT"]="OK";
						$ans["BALANCE"]=auth::user(true)->amount;
					}
				}
			}

			//Себе (хватит)
			if ( $type == "STAND" ){

				
				$calc=new Bj_Calc();
				$m=$calc->stand();
				if ($m!=0){
					$ans["RESULT"]="ERROR";
					$ans["BALANCE"]=auth::user()->amount;
				}
				else{
					
					$bet3 = $calc->amount.".0|{$calc->amount}.0|0.0|{$calc->amount}.0|0.0";
					if ($calc->double){
						$h=$calc->amount/2;
						$bet3 = "$h.0|{$h}.0|0.0|{$h}.0|0.0";						
					}
					
					
					$ans["STATE"]=$calc->state;
					$ans["CARDSDEALER"]=implode('|',$calc->tonumber($calc->dealer));
					$ans["SCORESPLAYER"]=$calc->ScorePlayer();
					$ans["SCORESDEALER"]=$calc->scoresdealer;
					$ans["CARDSPLAYER"]=implode('|',$calc->tonumber($calc->player));
					$ans["BET"]=$bet3;
					$ans["RESULT"]="OK";
					$ans["BALANCE"]=auth::user(true)->amount;
					$ans["PAYOUT"]=$calc->payout;
				}
				
			}
			
		}

		foreach($ans as $key=>$value){
				$ans[$key]="$key=$value";
			}

		echo implode('&',$ans);

}
	
	
	
	
	
	
	public function init($game){
		
		
		
	}

}