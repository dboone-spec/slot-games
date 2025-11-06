<?php

class Controller_Tournaments extends Controller_Base{

    
    //чтобы была норм ссылка
//    public function before() {
//        parent::before();
//
//        $gamelist = Kohana::$config->load('betgamestv.gamelist');
//        $game_id = array_search($this->request->action(), $gamelist)??$this->request->query('bggame');
//		$this->game_id = intval($game_id)??$this->game_id;
//
//        $this->request->action('index');
//    }
    
	public function action_index() {
        $view = new View('tournaments/list');
        
        $view->tournaments = th::get_shares_with_type('tournament');

        $this->template->content = $view;
    }
    
    public function action_info() {
        $id = intval($this->request->param('id'));
        
        $share = new Model_Share($id);
        
        if(!$share->loaded()) {
            throw new HTTP_Exception_404;
        }
        
        $view = new View('tournaments/item');
        
        $view->tournament = $share;
        $this->template->content = $view;
    }
    
}

