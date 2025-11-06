<?php

class Controller_Amarok extends Controller_Base{
	
	public $template='layout/game';
	public $tables=[1=>1, 2=>5, 3=>10, 4=>20];
	
	public function action_game(){
		
		$id=$this->request->param('id');
		
		$games=Kohana::$config->load('games');
		if (!isset($games['amarok'][$id])){
			throw new HTTP_Exception_404;
		}
		
		$table=$this->request->param('table',0);
		if (!isset($this->tables[$table])){
			throw new HTTP_Exception_404;
		}
		
		
		$view=new View('site/amarok/game');
		$view->game=$games['amarok'][$id];
		$view->name=$id;
		
		$left=new View('site/amarok/left');
		$left->tables=$this->tables;
		$left->name=$id;
		$left->active=$table;
		
		$this->template->content=$view;
		$this->template->left=$left;
		
	}
	
	
	public function action_sound(){
		$id=$this->request->param('id');	

		$config=Kohana::$config->load('games.amarok');
		if (!isset($config[$id])){
			throw new HTTP_Exception_404;
		}
		
		$name=$this->request->param('name');	
		$this->request->redirect('/resources/amarok/'.$name.'.swf',301);	
	}
	
	
	public function action_init(){
		
		$this->auto_render=false;
		
		$id=$this->request->param('id');
		$name=$this->request->param('name');
		$table=$this->request->param('table',0);
		
		$config=Kohana::$config->load('games.amarok');
		if (!isset($config[$id])){
			throw new HTTP_Exception_404;
		}
		
		$game=$config[$id];
		
		if (!isset($this->tables[$table])){
			throw new HTTP_Exception_404;
		}
		
		
		if (in_array($name,array('bonus_game','double_up'))){
			$action=$name;
		}
		else{
			$p=explode('_',$name);
			$action=$p[count($p)-1];
		}
		
		
		
		if ($action=='init'){
			$str=$this->init($id,$game,$table);
		}
		elseif($action =='spin'){
			$str=$this->spin($id,$game,$table);
		}
		elseif($action =='double_up'){
			$str=$this->up($id,$game,$table);
		}
		elseif($action =='bonus_game'){
			$str=$this->bonus($id,$game,$table);
		}
		else{
			throw new HTTP_Exception_404;
		}
		
		$this->response->body($str);
		
	}
	
	
	public function init($id,$game,$table){
		
		
		
		$coinsize=$this->tables[$table];
		game::session('amarok', $id, $coinsize);
		
		$calc=new Slot_Amarok($id);
		
		$ans=$calc->ans_init();

		
		$ans["credit"]=auth::user()->amount;
		$ans["coinsize"]=$coinsize;

		
		$calc->spin();
		
		if (arr::get($game,'type')==1){


				//расположение элементов на барабанах
				for ($i=1; $i<=$calc->barcount; $i++) {
					//размер барабана
					$size=count($calc->bars[$i]);
					$ans["wheel{$i}size"]=$size;
					
					//барабан
					for ($j=1; $j<=$size; $j++) {
						$ans["wheel{$i}_{$j}"]=$calc->bars[$i][$j-1];
					}
					
					
						//позиции барабанов
					$ans["wheel1Pos"]=$calc->pos[1]+2;
					$ans["wheel2Pos"]=$calc->pos[2]+2;
					$ans["wheel3Pos"]=$calc->pos[3]+2;
					$ans["wheel4Pos"]=$calc->pos[4]+2;
					$ans["wheel5Pos"]=$calc->pos[5]+2;

					
				}
			
		}
		else{
	

			$ans["symb1"]=$calc->sym(1);
			$ans["symb2"]=$calc->sym(2);
			$ans["symb3"]=$calc->sym(3);
			$ans["symb4"]=$calc->sym(4);
			$ans["symb5"]=$calc->sym(5);
			$ans["symb6"]=$calc->sym(6);
			$ans["symb7"]=$calc->sym(7);
			$ans["symb8"]=$calc->sym(8);
			$ans["symb9"]=$calc->sym(9);
			$ans["symb10"]=$calc->sym(10);
			$ans["symb11"]=$calc->sym(11);
			$ans["symb12"]=$calc->sym(12);
			$ans["symb13"]=$calc->sym(13);
			$ans["symb14"]=$calc->sym(14);
			$ans["symb15"]=$calc->sym(15);
			
			
		}
		
		foreach($ans as $key=>$value){
			$ans[$key]="$key=$value";
		}
		
		return implode('&',$ans);
		
	}
	
	
	public function spin($id,$game,$table){
		
		//стоимость одной монеты
		//брать из настроек?
		$coinsize=$this->tables[$table];
		game::session('amarok', $id, $coinsize);
		
		$calc=new Slot_Amarok($id);
		//ставка на линию
		$calc->amount_line=$this->request->post('nb_coins')*$coinsize;
		//выбрано линий
		$calc->cline=$lines=$this->request->post('nb_lines');
		//ставка всего
		$calc->amount=$calc->cline*$calc->amount_line;

		
		
		$err=$calc->bet();


		$ans=[];
		$ans["error"]=$err;
		
		if ($err==0){
		
			$ans["betamount"]=$calc->amount;

			$ans["credit"]=auth::user(true)->amount;
			$ans["coinsize"]=$coinsize;

			$ans["nb_coins"]=$calc->amount_line;
			$ans["nb_lines"]=$calc->cline;
			//выиграно на скаттер символе
			$ans["scatter_win"]=$calc->win_scatter;


			if (arr::get($game,'type')==1){
				//выиграно всего
				$ans["money_win_total"]=$calc->win_all;
				//выиграно монет
				$ans["nb_coins_win_total"]=$calc->win_all/$coinsize;

				//выигрышные линии
				//если $winall и  $win_coins =0 ни хрена отображаться не будет
				for ($i=1;$i<=9;$i++) {
					$ans["line_{$i}_wincoins"]=isset($calc->win[$i]) ? $calc->win[$i]/$coinsize : 0;
				}


				//позиции барабанов
				$ans["wheel1Pos"]=$calc->pos[1]+2;
				$ans["wheel2Pos"]=$calc->pos[2]+2;
				$ans["wheel3Pos"]=$calc->pos[3]+2;
				$ans["wheel4Pos"]=$calc->pos[4]+2;
				$ans["wheel5Pos"]=$calc->pos[5]+2;


				//активный wild символ, 
				for ($i=1; $i<=15; $i++) {
					//1 активный 0- нет
					if (in_array($calc->sym($i),$calc->wild))
					  $ans["active_wild{$i}"]=1;
				}

				//Разрешить игру на удвоение
				$ans['double_up_ok']=(int) $calc->simple_win;

				//1 запускает бонус игру
				$ans["bonus_game_ok"]=$calc->bonusrun>0 ? 1 : 0;
				//бросков на бонус игру
				$ans['nb_dice_to_play']=$calc->bonusrun;


			}
			else{

				$ans["total_win"]=$calc->win_all;
				$ans["total_coins_win"]=$calc->win_all/$coinsize;


				//выигрышные линии
				//если $winall и  $win_coins =0 ни хрена отображаться не будет
				for ($i=1;$i<=9;$i++) {
					//сумма выигрыша или 0 если проиграл
					$ans["line_win$i"]=isset($calc->win[$i]) ? $calc->win[$i] : 0;
				}

				//активный wild символ, 
				for ($i=1; $i<=15; $i++) {
					//1 активный 0- нет
					if (in_array($calc->sym($i),$calc->wild))
					  $ans["active_wild{$i}"]=1;
				}


				//символы на экране
				$ans["symb1"]=$calc->sym(1);
				$ans["symb2"]=$calc->sym(2);
				$ans["symb3"]=$calc->sym(3);
				$ans["symb4"]=$calc->sym(4);
				$ans["symb5"]=$calc->sym(5);
				$ans["symb6"]=$calc->sym(6);
				$ans["symb7"]=$calc->sym(7);
				$ans["symb8"]=$calc->sym(8);
				$ans["symb9"]=$calc->sym(9);
				$ans["symb10"]=$calc->sym(10);
				$ans["symb11"]=$calc->sym(11);
				$ans["symb12"]=$calc->sym(12);
				$ans["symb13"]=$calc->sym(13);
				$ans["symb14"]=$calc->sym(14);
				$ans["symb15"]=$calc->sym(15);



				//если запущено фриспин, то обрабатывается это сервере, клиент не в курсе про фриспин
				//все выиграно на фри спинах
				$ans['total_win_free']=$calc->total_win_free;
				//1- Начать хаялвные вращения
				$ans['mode_free_begin']=$calc->mode_free_begin ? 1 : 0;
				//0 - закончить халявные вращения
				$ans['mode_free']=$calc->mode_free_end ? 1 : 0;

				//осталось халявных вращений
				$ans['nb_gratuit_to_play']=$calc->free_count_play;
				//сыгроано халявных вращений
				$ans['nb_gratuit_played']=$calc->free_count_played;
			}
		}
		
		foreach($ans as $key=>$value){
			$ans[$key]="$key=$value";
		}
		
		return implode('&',$ans);
		
	}
	
	
	
	public function up($id,$game,$table){
		
		
		
		$coinsize=$this->tables[$table];
		game::session('amarok', $id, $coinsize);
		$calc=new Slot_Amarok($id);
		
		//2 черное
		//1 красное
		//0 выбор сделан на масть
		$color=$this->request->post('bet_2');
		
		
		//крести 1
		//буби 2
		//черви 3
		//вини 4
		$suit=$this->request->post('bet_4');

		if ($color==0 and $suit>0){
			$mode='suit';
			$select=$calc->SuitSlotToCard($suit);
		}
		elseif ($color>0 and $suit==0){
			$mode='color';
			$select=$calc->ColorSlotToCard($color);
		}
		else{
			throw HTTP_Exception_404;
		}
	
		
		$calc->double_mode=$mode;
		$calc->select=$select;
		$calc->double();

	
		$ans['error']=0;
		//Cумма на счету до нажатия кнопки double
		$ans['old_credit']=Auth::user(true)->amount+$calc->first_bet-$calc->win_all;
		//Cумма на счету после выбора
		$ans['credit']=Auth::user()->amount;
		//Выиграно
		$ans['du_win_money']=$calc->win_all;
		//карта
		//12 дама крестей
		//11 валет крестей
		$ans['card_res']=$calc->CardSlotFromCard($calc->double_result);
		//0 если просрал
		//1 выиграл
		$ans['double_up_ok']=$calc->win_all>0 ? 1 : 0;
		$ans['bonus_game_ok']=0;

		foreach($ans as $key=>$value){
			$ans[$key]="$key=$value";
		}
		return implode('&',$ans);
		
}


public function bonus($id,$game,$table){

		$coinsize=$this->tables[$table];
		game::session('amarok', $id, $coinsize);
		$calc=new Slot_Amarok($id);
		
		$calc->bonusgame();

		$ans["double_up_ok"]="0";
		$ans["error"]="0";

		// Всего бросков
		$ans["nb_dice_to_play"]=$calc->bonusrun;
		// Сделано бросков
		$ans["current_dice_played"]=$calc->bonusnum;

		// Число на кубике
		$ans["val_dice"]=$calc->digit;
		// Позиция на карте
		$ans["pos_dice"]=$calc->pos;
		// Выигрыш в монетах с позиции
		$ans["win_dice"]=10;

		// Всего выиграно денег за бонус
		$ans["coins_win_cumul_bonus"]=$calc->win_all;  // в монетах
		$ans["money_win_cumul_bonus"]=$calc->win_all;  // в лавешках

		$ans["credit"]=auth::user(true)->amount;
		$ans["coinsize"]=$coinsize;

		$ans["nb_coins"]=$calc->amount_line;
		$ans["nb_lines"]=$calc->cline;

		// Если 1=то играем. Если 0 то конец бонусу.
		$ans["bonus_game_ok"]=(int) $calc->bonusnum!=$calc->bonusnum;

	
		foreach($ans as $key=>$value){
			$ans[$key]="$key=$value";
		}
		return implode('&',$ans);
	
}



}

