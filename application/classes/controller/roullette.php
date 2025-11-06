<?php

class Controller_Roullette extends Controller_Base {
	
	public $template='layout/game';	

	
	public function action_file(){
		
		$id=$this->request->param('id');	

		$config=Kohana::$config->load('games.roullette');
		if (!isset($config[$id])){
			throw new HTTP_Exception_404;
		}
		
		$name=$this->request->param('name');	
		$this->request->redirect("/resources/roullette/casino/{$name}.swf",301);	
		
		$this->request->redirect();
		
	}
	
	
	public function action_table(){
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games.roullette');
		if (!isset($games[$id])){
			throw new HTTP_Exception_404;
		}
		
		$view=new View('site/roullette/table');
		$view->game=$games[$id];
		$view->name=$id;
		$this->template->content=$view;
		
	}

	
	public function action_game(){
	
		
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games.roullette');
		$table=$this->request->param('table',0);
		if (!isset($games[$id])){
			throw new HTTP_Exception_404;
		}
		
		
		//не выбран стол
		if ($table==0){
			$this->request->redirect("/roullette/{$id}/table");
		}
		
		//играет на другом столе
		if (!game::session('roullette',$id,$table)->start()){
			$view=new View('site/message/notable');			
			$this->template->content=$view;
			return null;
		}
		
		$view=new View('site/roullette/game');
		//запускаем игру
		
		$view->game=$games[$id];
		$view->table=$games[$id]['table'][$table];
		$view->name=$id;
		
		$left=new View('site/roullette/left');
		$left->tables=$games[$id]['table'];
		$left->name=$id;
		$left->active=$table;
		
		$this->template->content=$view;
		$this->template->left=$left;
		
		
	}
	
	
	public function  action_init(){
		$this->auto_render=false;
		$id=$this->request->param('id');
		$games=Kohana::$config->load('games.roullette');
		$table=$this->request->param('table');
		if (!isset($games[$id]) or (!isset($games[$id]['table'][$table])) ){
			throw new HTTP_Exception_404;
		}
		
		//сессия обновлять будем потом при ставкке
		game::session('roullette',$id,$table);
		
		
		$game=$games[$id];
		
		
		$action=$this->request->post('action');
		
		//init
		if ($action=='state'){
			$ans['result']='ok';
			$ans['state']=0;
			$ans['min']=$game['table'][$table]['min'];
			$ans['jack']=1;
			$ans['id']=auth::parent_acc()->visible_name;
			$ans['balance']=auth::user()->amount;
			
		}
		
		if ($action=='spin'){
			$calc=new Roullete_Calc();
			$calc->parsebet($this->request->post('bets'));
			$r=$calc->bet();
			if ($r==0){
				
				$num=$calc->num;
				$ans['id']=auth::parent_acc()->visible_name;
				$ans['result']='ok';
				//номер
				$ans['num']=$num;
				$ans['balance']=auth::user(true)->amount;
				//хз
				$ans['end']=160;
				
			}	
			else{
				$ans['error']=$r;
			}
			
		}
		
		
		
		
		
		foreach($ans as $key=>$value){
			$ans[$key]="$key=$value";
		}
		
		$this->response->body(implode('&',$ans));
		
		
	}
	
	
}

