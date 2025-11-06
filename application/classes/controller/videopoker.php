<?php

class Controller_Videopoker extends Controller_Base {

	public $template='layout/game';
	
	public function action_table(){
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games.cards');
		if (!isset($games[$id])){
			throw new HTTP_Exception_404;
		}
		
		$view=new View('site/videopoker/table');
		$view->game=$games[$id];
		$view->name=$id;
		$this->template->content=$view;
		
	}
	
	
	public function action_game(){
	
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games');
		$table=$this->request->param('table');
		if (!isset($games['cards'][$id])){
			throw new HTTP_Exception_404;
		}
		
		//не выбран стол
		if ($table==0){
			$this->request->redirect("/videopoker/{$id}/table");
		}
		
		//играет на другом столе
		if (!game::session('videopoker',$id,$table)->start()){
			$view=new View('site/message/notable');			
			$this->template->content=$view;
			return null;
		}
		
		$view=new View('site/videopoker/game');
		$view->game=$games['cards'][$id];
		$view->name=$id;
		
		$left=new View('site/videopoker/left');
		$left->tables=$games['cards'][$id]['table'];
		$left->name=$id;
		$left->active=$table;
		
		$this->template->content=$view;
		$this->template->left=$left;
		
		
	}
	
	
	public function  action_init(){
		
		
		$this->auto_render=false;
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games.cards');
		$table=$this->request->param('table');
		if (!isset($games[$id]) or (!isset($games[$id]['table'][$table])) ){
			throw new HTTP_Exception_404;
		}
		
		$action=$this->request->param('name');
		if ($action!='init'){
			$action=$this->request->post('amp;stage');
			//ставка в монетах
			$bet=$this->request->post('amp;bet');
			//стоимость монеты
			$coinprice=$games[$id]['table'][$table]['min'];
		}
		
		//сессия обновлять будем потом при ставкке
		game::session('videopoker',$id,$table);
		
		
		//инициализация
		if ($action=='init'){
			$ans['status']='ok';
			$ans['md5_random_key']='';
			$ans['new_game']=0;
			$ans['credits']=(int) auth::user()->amount;
			$ans['credits_real']='';
			$ans['credits_demo']='';

		}
		//раздача карт
		if ($action=='getcards'){
	
			$class='Videopoker_Game_'.$id;
			
			$calc=new $class;
			$calc->betcoin=$bet;
			$calc->amount=$bet*$coinprice;
			
			
			$deal=$calc->deal();
			if ($deal==0){
				$ans['status']='ok';
				$ans['md5_random_key']='';
				$ans['win']=0;
				$ans['credits']=(int) Auth::user(true)->amount;
				
				$card=$calc->tonumber($calc->cardon);

				//Карты
				$ans['card1']=$card[1];
				$ans['card2']=$card[2];
				$ans['card3']=$card[3];
				$ans['card4']=$card[4];
				$ans['card5']=$card[5];

				//1 если карту рекомендуется оставить
				$ans['win1']= in_array(1,$calc->hold)? 1 : 0;
				$ans['win2']= in_array(2,$calc->hold)? 1 : 0;
				$ans['win3']= in_array(3,$calc->hold)? 1 : 0;
				$ans['win4']= in_array(4,$calc->hold)? 1 : 0;
				$ans['win5']= in_array(5,$calc->hold)? 1 : 0;
				
				
				
			}
			else{
				$ans['status']='error '.$deal;
			}
			
			
			$ans['wc']=0;
			$ans['credits_real']=0;
			$ans['credits_demo']=0;
			
		}
		//смена карт
		if ($action=='draw'){
			
			$hold=[];
			
			//заморозка по картам 0 - менять 1 - оставить
			for($i=1;$i<=5;$i++){
				if ($this->request->post('amp;held'.$i)>0){
					$hold[]=$i;
				}
			}
			
			$class='Videopoker_Game_'.$id;
			$calc=new $class;
			
			$draw=$calc->draw($hold);
			
			if ($draw==0){
			
				$ans['status']='ok';
				$ans['md5_random_key']='';

				//сколько выиграл, (отображается на экране)
				$ans['win']=$calc->win;
				$ans['credits']=(int) auth::user(true)->amount;

				$card=$calc->tonumber($calc->cardon);
				//Карты
				$ans['card1']=$card[1];
				$ans['card2']=$card[2];
				$ans['card3']=$card[3];
				$ans['card4']=$card[4];
				$ans['card5']=$card[5];
				
				
				//1 если карта участвует в выигрышной комбинации
				$ans['win1']= in_array($calc->cardon[1],$calc->wincard)? 1 : 0;
				$ans['win2']= in_array($calc->cardon[2],$calc->wincard)? 1 : 0;
				$ans['win3']= in_array($calc->cardon[3],$calc->wincard)? 1 : 0;
				$ans['win4']= in_array($calc->cardon[4],$calc->wincard)? 1 : 0;
				$ans['win5']= in_array($calc->cardon[5],$calc->wincard)? 1 : 0;
				
				

				// выигрышная комбинация 1 - валеты и выше 2 две пары ит.д.
				$ans['wc']=$calc->level-1;
			}
			else{
				$ans['status']='error '.$draw;
			}
			
			$ans['credits_real']=0;
			$ans['credits_demo']=0;
			
		}
		
		//забираем выигрыш
		if ($action=='collect'){
			
			game::session()->end();
			
			$ans['status']='ok';
			$ans['md5_random_key']='';
			$ans['win']=0;
			$ans['credits']=(int) auth::user(true)->amount;
			$ans['wc']=0;
			$ans['credits_real']=0;
			$ans['credits_demo']=0;
			
		}
		//дабл
		if ($action=='double'){
			
			$class='Videopoker_Game_'.$id;
			$calc=new $class;
			$double=$calc->double();
			
			
			if ($double==0){
			
				$ans['status']='ok';
				$ans['md5_random_key']='';
				//открытая карта
				$ans['card1']=$calc->tonumber($calc->card1);
				$ans['credits']=auth::user(true)->amount;
				
			}
			else{
				$ans['status']='error '.$double;
			}
			
			$ans['credits_real']='';
			$ans['credits_demo']='';
			
			
		}
		//выбор в дабле
		if ($action=='doubleSelect'){
			
			//номер выбранной карты (1 - 4)
			$num=$this->request->post('amp;card');
			
			$class='Videopoker_Game_'.$id;
			$calc=new $class;
			$double=$calc->DoubleSelect($num);
			
			
			if ($double==0){
			
				$ans['status']='ok';
				$ans['md5_random_key']='';

				//Карты
				$ans['card2']=$calc->tonumber($calc->carduser[1]);
				$ans['card3']=$calc->tonumber($calc->carduser[2]);
				$ans['card4']=$calc->tonumber($calc->carduser[3]);
				$ans['card5']=$calc->tonumber($calc->carduser[4]);
				$ans['credits']=(int) auth::user(true)->amount;
				//сколько выиграл, хз похоже в рублях
				$ans['win']=$calc->win;
				$ans['wс']=0;
				
			}
			else{
				$ans['status']='error '.$double;
			}
			

			
			$ans['credits_real']='';
			$ans['credits_demo']='';

		}
		
		$this->response->body($this->makexml($ans));		
		
	}
	
	public function makexml($a){
		
		$str="\r\n<root>\r\n";
		foreach ($a as $key=>$value){
			$str.="<$key>$value</$key>\r\n";
		}
		$str.="</root>\r\n";
		
		return $str;
		
	}
	
	
	public function action_exit(){
		$this->request->redirect('/select',301);
	}
	
}

