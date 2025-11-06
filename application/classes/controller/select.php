<?php

class Controller_Select extends Controller_Base{

    protected $show_search = true;

    public function action_index(){
        if(THEME=='robot') {
            Session::instance()->set('show_start_terminal',0);
        }

        $this->request->redirect('/');
    }

    public function action_all(){

        $this->request->redirect('/interactive');
        
        $this->auto_render=false;

        $sql='select g.visible_name,g.image,g.brand,g.name
            from office_games og
            join games g on g.id=og.game_id

            where og.enable=1
            and og.office_id=:oid
            order by g.sort';

        $view=new View('site/select/all');

        $vg = new vipgameapi();
        $games = $vg->gamelist();

        if (count($games)<12){
            for($i=count($games); $i<12;$i++){
                $games[]=['visible_name'=>'Coming soon', 'image'=>'/games/agt/thumb/coming.png','brand'=>'none'];
            }
        }



        $view->games=$games;

        $this->response->body($view->render());


    }


	public function action_popular(){

		$view=new View('site/select/index');
		$games_search_pop=$games=th::our_games();
		$cat=array_keys($games);

        //Процент возврата для сортировки
        $retpay=th::retpay();
        $maxp=th::maxpayin();
        $view->retpay=$retpay;
        $view->mp=$maxp;

        $mainp=th::mainpage($cat);
        $imperium_games=$mainp[0];
        $cat=$mainp[1];

        $imperium_games_s=th::filPopImpGames($imperium_games, $retpay, $maxp);
        $games_s=th::filPopGames($games, $retpay, $maxp);

        $view->imperium_games = $imperium_games_s;
//        $a=new Model_Userfavourite();
//        $a->emptygamestmp();
        //Избранное для сортировки
        $fg=th::favegame();
        if(!empty($fg)){
            $decfg= json_decode($fg[0]['games'],true);
            $view->fgames=$decfg;
        }
        //
        ////
        $view->games_search_pop=$games_search_pop;
        $new_games=Kohana::$config->load('newgames')->as_array();
        $view->new_games= $new_games;
        $percent_return=Kohana::$config->load('percentreturn')->as_array();
        $view->percent_return=$percent_return;
        $view->page_type='pop';
		$view->cats=$cat;
		$view->games=$games_s;
        $this->template->page_type='pop';
		$this->template->content=$view;

	}

    public function action_new(){

		$view=new View('site/select/index');
		$games_search_pop=$games=th::our_games();
		$cat=array_keys($games);
        $new_games=Kohana::$config->load('newgames')->as_array();
        $view->new_games= $new_games;
        $percent_return=Kohana::$config->load('percentreturn')->as_array();
        $view->percent_return=$percent_return;

        //Процент возврата для сортировки
        $retpay=th::retpay();
        $maxp=th::maxpayin();
        $view->retpay=$retpay;
        $view->mp=$maxp;

        $mainp=th::mainpage($cat);
        $imperium_games=$mainp[0];
        $cat=$mainp[1];

        $imperium_games_s=th::filNewImpGames($imperium_games, $new_games);
        $games_s=th::filNewGames($games, $new_games);

        $view->imperium_games = $imperium_games_s;

//        $a=new Model_Userfavourite();
//        $a->emptygamestmp();

        //Избранное для сортировки
        $fg=th::favegame();
        if(!empty($fg)){
            $decfg= json_decode($fg[0]['games'],true);
            $view->fgames=$decfg;
        }
        //
        $view->games_search_pop=$games_search_pop;
        $view->percent_return=$percent_return;
        $view->new_games = $new_games;
		$view->page_type='new';
        $this->template->page_type = 'new';
		$view->games=$games_s;
		$view->cats=$cat;
		$this->template->content=$view;

	}

	public function action_slots(){

		$view=new View('site/select/index');
		$games_search_pop=$games=th::our_games();
		$cat=array_keys($games);

        $new_games=Kohana::$config->load('newgames')->as_array();
        $view->new_games= $new_games;
        $percent_return=Kohana::$config->load('percentreturn')->as_array();
        $view->percent_return=$percent_return;

        //Процент возврата для сортировки
        $retpay=th::retpay();
        $maxp=th::maxpayin();
        $view->retpay=$retpay;
        $view->mp=$maxp;

        $mainp=th::mainpage($cat);
        $imperium_games=$mainp[0];
        $cat=$mainp[1];
        //Избранное для сортировки
        $fg=th::favegame();
        if(empty($fg) or !isset(auth::parent_acc()->id)){

//            $a=new Model_Userfavourite();
//
//            $c = $a->getgamestmp();//array games from db получаем массив игр local
//            $d = $c[0]['games'];//string games from db json с играми
//            $b = json_decode($d,true);//array массив с играми

            unset($imperium_games['roulette']);
            $view->imperium_games = $imperium_games;

            $view->games=$games;
//            $a->emptygamestmp();

        } else {

//            $a=new Model_Userfavourite();
//            $a->emptygamestmp();

            $decfg= json_decode($fg[0]['games'],true);
            $view->fgames=$decfg;

            unset($imperium_games['roulette']);
            $view->imperium_games = $imperium_games;

            $view->games=$games;
        }

        //

        //
        $view->games_search_pop=$games_search_pop;
        $view->page_type='slot';
		$view->cats=$cat;
        $this->template->page_type='slot';
		$this->template->content=$view;

	}

	public function action_table(){

		$view=new View('site/select/index');
		$games_search_pop=$games=th::our_games();
		$cat=array_keys($games);

        $new_games=Kohana::$config->load('newgames')->as_array();
        $view->new_games= $new_games;
        $percent_return=Kohana::$config->load('percentreturn')->as_array();
        $view->percent_return=$percent_return;

        //Процент возврата для сортировки
        $retpay=th::retpay();
        $maxp=th::maxpayin();
        $view->retpay=$retpay;
        $view->mp=$maxp;

        $mainp=th::mainpage($cat);
        $imperium_games=$mainp[0];
        $cat=$mainp[1];
        $fg=th::favegame();
        if(empty($fg) or !isset(auth::parent_acc()->id)){

//            $a=new Model_Userfavourite();
//
//            $c = $a->getgamestmp();//array games from db получаем массив игр local
//            $d = $c[0]['games'];//string games from db json с играми
//            $b = json_decode($d,true);//array массив с играми

            foreach($imperium_games as $categ => $list){
                if($categ!=="roulette"){
                   unset($imperium_games[$categ]);
                }
            }

            $view->imperium_games = $imperium_games;
            unset($imperium_games);
            unset($games['novomatic']);
            unset($games['igrosoft']);
//            $a->emptygamestmp();
            $view->games=$games;


        } else {

//            $a=new Model_Userfavourite();
//            $a->emptygamestmp();

            $decfg= json_decode($fg[0]['games'],true);
            $view->fgames=$decfg;

            foreach($imperium_games as $categ => $list){
                if($categ!=="roulette"){
                   unset($imperium_games[$categ]);
                }
            }


            $view->imperium_games = $imperium_games;

            unset($games['novomatic']);
            unset($games['igrosoft']);

            $view->games=$games;
        }

        $view->games_search_pop=$games_search_pop;
        $view->page_type='table';
		$view->cats=$cat;
        $this->template->page_type='table';
		$this->template->content=$view;

	}

    public function action_fave(){

		$view=new View('site/select/index');
		$games_search_pop=$games=th::our_games();
		$cat=array_keys($games);

        $new_games=Kohana::$config->load('newgames')->as_array();
        $view->new_games= $new_games;
        $percent_return=Kohana::$config->load('percentreturn')->as_array();
        $view->percent_return=$percent_return;

        //Процент возврата для сортировки
        $retpay=th::retpay();
        $maxp=th::maxpayin();
        $view->retpay=$retpay;
        $view->mp=$maxp;

        $mainp=th::mainpage($cat);
        $imperium_games=$mainp[0];
        $cat=$mainp[1];
        $fg=th::favegame();

        if(!isset(auth::parent_acc()->id)){//empty($fg) OR
//
//            $a=new Model_Userfavourite();
//
//            $c = $a->getgamestmp();//array games from db получаем массив игр local
//            $d='';
//            if(isset($c[0]['games']))
//            {
//                $d = $c[0]['games'];//string games from db json с играми
//            }
//            $b = json_decode($d,true);//array массив с играми
//
////
//            $imperium_games_s=th::filFavImpGames($imperium_games, $b);
//            $games_s=th::filFavGames($games, $b);
//
//            $view->games=$games;
//            $a->emptygamestmp();
            $imperium_games_s=$imperium_games;
            $games_s=$games;
        } else {
//            $a=new Model_Userfavourite();
//            $a->emptygamestmp();
            $decfg=[];
            if(isset($fg[0]['games'])) {
                $decfg= json_decode($fg[0]['games'],true);
            }
            $view->fgames=$decfg;

            $imperium_games_s=th::filFavImpGames($imperium_games, $decfg);
            $games_s=th::filFavGames($games, $decfg);
        }

        $view->mp=$maxp;

        $view->imperium_games = $imperium_games_s;
        $view->games=$games_s;
        $view->games_search_pop=$games_search_pop;
        $view->page_type='fav';
		$view->cats=$cat;
        $this->template->page_type='fav';
		$this->template->content=$view;
	}
}
