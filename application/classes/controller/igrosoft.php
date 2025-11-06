<?php

class Controller_Igrosoft extends Controller_Base{
	
	public $template='layout/game';
	
	public function action_game(){
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games');
		if (!isset($games['igrosoft'][$id])){
			throw new HTTP_Exception_404;
		}
		
		
		$view=new View('site/igrosoft/game');
		$view->game=$games['igrosoft'][$id];
		$view->name=$id;
		$this->template->content=$view;
		
	}
	
	
	public function action_file(){
		
		$id=$this->request->param('id');	

		$config=Kohana::$config->load('games.igrosoft');
		if (!isset($config[$id])){
			throw new HTTP_Exception_404;
		}
		
		$name=$this->request->param('name');	
		$this->request->redirect("/resources/igrosoft/{$id}/{$name}.swf",301);	
		
		$this->request->redirect();
		
	}
	
	
	public function action_init(){
		
		$this->auto_render=false;
		$id=$this->request->param('id');	
		
		$config=Kohana::$config->load('games.igrosoft');
		if (!isset($config[$id])){
			throw new HTTP_Exception_404;
		}
	
		
		$action=$this->request->post('action');
		
		game::session('igrosoft',$id);		
		
		
		//разобрать говно запрос на дабл
		//action=double|3
		$g='action=double';
		//double
		if (substr($action,0,strlen($g))==$g){
			list($no,$select)=explode('|',$action);
			$action='double';
		}
		//init or spin
		else{
			foreach(explode('|',$action) as $a){
				list($key,$value)=explode('=',$a);
				$param[$key]=$value;
			}
			$action=substr($param['action'],0,4);
		}
		
		//init
		if ($action=='init'){
			$calc=new Slot_Igrosoft($id);
			$str="OK|".floor(auth::user()->amount).$calc->extralife();
			
		}
		
		/*
		OK|132|&extralife=10
		OK|28231.00&extralife=18
		*/
		//spin
		if ($action=='play' ){
			
			
			
			$calc=new Slot_Igrosoft($id);
			//ставка на линию
			$calc->amount_line=$param['bet'];
			//выбрано линий
			$calc->cline=$param['lines'];
			//ставка всего
			$calc->amount=$calc->cline*$calc->amount_line;
			
			
			
			$calc->bet('bonus');
			$balance=auth::user(true)->amount-$calc->win_all;
			
			$str="OK|".implode('|',$calc->sym())."|{$calc->win_all}|".$balance;
			
			//бонус
			
			if ($calc->bonusrun>0){
				$str.=$calc->rope();
			}
			else {
				//карта на дабл
				if ($calc->win_all>0){
					$str.='|'.$calc->GetDoubleCard();
				}
			}
			
			$str.=$calc->extralife();
			
			/*
			//ответ проиграл
			$str="OK|$sym1|$sym2|$sym3|$sym4|$sym5|$sym6|$sym7|$sym8|$sym9|$sym10|$sym11|$sym12|$sym13|$sym14|$sym15|$win|$user_balance|&extralife={$extralife}";
			        OK|8|4|0| 1|0|3| 5|2|7| 3|3|2 |5|7|2|                                                               0|6904|
			//ответ выиграл
			$str="OK|$sym1|$sym2|$sym3|$sym4|$sym5|$sym6|$sym7|$sym8|$sym9|$sym10|$sym11|$sym12|$sym13|$sym14|$sym15|$win|$user_balance|$card|&extralife={$extralife}";
			
			//ответ с бонус игрой
			$str="OK|$sym1|$sym2|$sym3|$sym4|$sym5|$sym6|$sym7|$sym8|$sym9|$sym10|$sym11|$sym12|$sym13|$sym14|$sym15|$win|$user_balance|&rope1=10|&extralife={$extralife}";
			
			//ответ с бонус игрой до конца
			$str="OK|$sym1|$sym2|$sym3|$sym4|$sym5|$sym6|$sym7|$sym8|$sym9|$sym10|$sym11|$sym12|$sym13|$sym14|$sym15|$win|$user_balance|&rope1=20|rope1=10|rope1=10|rope1=20|rope1=40|l_bonus=100|&extralife={$extralife}";
			
			//всего выиграно с учетом бонуса 1800
			//OK|6|8|4|3|2|7|6|7|2|8|3|8|6|1|3|1800|90297|&rope1=20|rope1=10|rope1=10|rope1=20|rope1=40|l_bonus=100|&extralife=18
			*/
		}
		if ($action=='double'){
			//post
			//action	action=double|3
			//ответ 
			
			$calc=new Slot_Igrosoft($id);
			$calc->select=$select;
			$calc->double();
			
			
			
			//выигрыш
			$win=$calc->win_all;
			
			$info=$calc->double_result;
			
			//карты на поле
			$card1=$calc->CardToSlot($info[0]);
			$card2=$calc->CardToSlot($info[1]);
			$card3=$calc->CardToSlot($info[2]);
			$card4=$calc->CardToSlot($info[3]);
			$card5=$calc->CardToSlot($info[4]);
			/*
			$card1=12;
			$card2=13;
			$card3=14;
			$card4=15;
			$card5=16;
			*/
			
			$str="OK|$card1|$card2|$card3|$card4|$card5|$win|".auth::user(true)->amount.'|';
			
			//если выиграл
			//карта на следующий дабл
			if ($calc->win_all>0){
				$str.=$calc->GetDoubleCard();
			}
			
			/*
			//проиграл
			$str="OK|$card1|$card2|$card3|$card4|$card5|$win|$user_balance|";
			//OK|21|42|16|8|25|0|90324|
			 * 
			 */
		}
		
		$this->response->body($str);
		
		
	}
	
	
	
}
