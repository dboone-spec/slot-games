<?php

class Controller_Index extends Controller_Base{

    protected $show_search = true;

    public function before() {
        $r=parent::before();

        if (th::flag('noAuthIndex') and empty(auth::$user_id)){
            throw new HTTP_Exception_404();
        }

        return $r;
    }


	public function action_index(){

		if(INFINSOC_DOMAIN) {
            exit;
        }
		
		if(API_DOMAIN) {
            exit;
        }

        if (TERMINAL){
            $this->request->redirect('/terminal');
        }

        if (TELEGRAM){
            $this->request->redirect('/bot/games');
        }

		if(SBC_DOMAIN && empty(auth::$user_id)) {
            $this->request->redirect('/register');
        }

        if (PROJECT==1){
            $this->request->redirect('/interactive');
        }

        if (PROJECT==2){
            $this->request->redirect('/online');
        }
        throw new HTTP_Exception_404();


        $vg = new vipgameapi();
        $list = $vg->gamelist();
        $this->template->gamelist = $list;
	}



	public function action_inituser(){


            $this->auto_render=false;


            $ans=["token"=>"123123123",
                "view"=>0,
                "desktop"=>0,
                "full"=>1,
                "game_logo"=>"DC",
                "gcat"=>"/",
                "lang"=>"ru",
                "rolls_smoothing"=>0,//
                "server_type"=>2,//
                "speed"=>0,
                "success"=>1,
                "url_math"=>"/novomatic/init.php",
                "currency"=>"",
                "volume"=>100,
                "game_continue"=>1,
                "url_logo"=>0];

            $this->response->body(json_encode($ans));


	}


        public function action_math(){

            $this->auto_render=false;
            $id=arr::get($_GET,'id');

            //init
            if ($id==100){

                $ans=["balance"=>269650,
                      "game_id"=>-1,
                      "dentab"=>[100,200,10,20,50],
                      "bets"=>[1,2,3,4,5,10,15,20,30,40,50,100],
                      "lines"=>[1,2,3,4,5,6,7,8,9,10]
                    ];
            }
            //init comb
            if ($id==0){

                $ans=["balance"=>269650,
                        "comb"=>[5,1,5,8,0,6,12,9,3,10,1,0,1,10,4],
                    "state"=>0,
                    "jackpots"=>[
                            "jps"=>[0,0,0,0],
                            "jpmin"=>[0,0,0,0],
                            "jpmax"=>[0,0,0,0],
                            "jplim"=>[0,0,0,0],
                            "jpenabled"=>"0"
                                ],
                    ];


            }

            //balance update
            if ($id==5){

                $ans=["balance"=>269650,
                        "jackpots"=>[
                            "jps"=>[0,0,0,0],
                            "jpmin"=>[0,0,0,0],
                            "jpmax"=>[0,0,0,0],
                            "jplim"=>[0,0,0,0],
                            "jpenabled"=>"0"
                                ],
                    ];


            }

            //spin
            if ($id==1){

                $ans=[
                    "comb"=>[5,2,3,11,2,6,7,8,3,1,1,4,5,9,10],
                    "win"=>0,
                    "lines"=>[0,0,0,0,0,0,0,0,0,0],
                    "linesValue"=>[0,0,0,0,0,0,0,0,0,0,0],
                    "bonus"=>0,
                    "bonus_win"=>0,
                    "gamble_suit_history"=>[1,2,2,3,1],
                    "jackpots"=>[
                            "jps"=>[0,0,0,0],
                            "jpmin"=>[0,0,0,0],
                            "jpmax"=>[0,0,0,0],
                            "jplim"=>[0,0,0,0],
                            "jpenabled"=>"0"
                                ],
                    "balance"=>270410

                ];

            }



            $this->response->body(json_encode($ans));
        }




/*

	public function action_history(){

		$math=new math();

		$this->auto_render=false;

		$sql='select amount, win, come, result
				from bets
				where user_id=:uid
				order by id desc
				limit 1';

		$data=db::query(1,$sql)->param(':uid',auth::$user_id)->execute()->as_array();

		$s='';
		foreach ($data as $d){
			$r=str_replace("\r\n",'<br>', $d['result']);
			$s.="Ставка {$d['amount']}<br>";
			$s.="Выигрыш {$d['win']}<br>";
			$s.="Баланс ".auth::user(true)->amount.'<br>';
			//$s.="Исход {$d['come']}<br>";
			$s.="Результат <br> $r <br><br>";
		}

		$this->response->body($s);


	}

	*/


    public function action_search() {
        $this->auto_render = false;
        $view=new View('site/select/search');
		$sql_games = <<<SQL
            Select g.*
            From games g JOIN office_games og ON g.id = og.game_id
            Where office_id = :office_id
                AND g.show in :show
                AND og.enable = 1
SQL;

        $show = [1,3];

        if(th::isMobile()) {
            $show = [1,2];
        }

        $res_games  = db::query(1, $sql_games)->parameters([
            ':office_id' => OFFICE,
            ':show' => $show
        ])->execute()->as_array();

        $games = [];
        $imperium_games = [];

        $static_domain = kohana::$config->load('static.static_domain');

        foreach ($res_games as $g) {

            if($static_domain) {
                $g['image'] = $static_domain.$g['image'];
            }

            if($g['provider'] == 'our') {
                $games[$g['brand']][$g['name']] = [
                    'image' => $g['image'],
                    'visible_name' => $g['visible_name'],
                ];
            } else {
                $imperium_games[$g['brand']][] = [
                    'game_id' => $g['external_id'],
                    'name' => $g['visible_name'],
                    'type_system' => $g['brand']
                ];
            }
        }

        $view->imperium_games = $imperium_games;
		$view->games=$games;

        $this->response->body($view->render());
    }

    public function action_antiblock() {
        $url = '/';

        if(!THEME OR THEME=='default') {
            $url = '';
        }

        $this->request->redirect($url);
    }

    public function action_redirect() {
        $redir_with = $this->request->param('id');

        if(!auth::$user_id AND $redir_with=='register') {
            Flash::warning('/popup/register');
        }

        $this->request->redirect('/');
    }
}

