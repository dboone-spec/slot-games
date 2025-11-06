<?php

class Controller_Games_Agt extends Controller_Game{

    public $brand='agt';
    protected $_cache_time=10*60;

    public function before()
    {
        if(PROJECT!=1) {
            //throw new HTTP_Exception_404;
        }
        parent::before();
    }

    public function action_info()
    {
        $u = auth::user();

        if(empty(auth::$user_id)) {
            throw new HTTP_Exception_404;
        }

        if($u->office->enable_bia<=0) {
            throw new HTTP_Exception_404;
        }

        if(in_array($u->office_id,[1038,1043,1045,1047])) {
//            throw new HTTP_Exception_404;
        }

        $l=arr::get($_GET,'l','en');

        I18n::$lang=$l;

        $cache_key = 'calc_dsback_'.$u->id.$l;
        dbredis::instance()->select(5);

        if (!$v = dbredis::instance()->get($cache_key)) {

            $v = new View('site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . 'info');
            $v->u = $u;

            $mult=auth::user()->office->currency->mult ?? 2;
            $v->mult = $mult;

            $time = $u->last_bet_time+$u->office->bonus_diff_last_bet*60*60;

            $lst = floor((time()-$u->last_bet_time)/60/60/24);

            $last_calc = $u->last_bonus_calc??$u->last_bet_time;
            $bonus_coeff = ($u->bonus_coeff??$u->office->bonus_coeff);

            $bets=auth::user()->getBetsForDS();

            $sum_all = $bets['sum'];

            $history = new Model_Fsbackhistory();
            $history->where('user_id','=',$u->id);
            $history->order_by('created','desc');
            $history->limit(5);
            $history = $history->find_all()->as_array();


            sort($history);

            //первым должны быть кешбек
            $current_fs = new Model_Freespinhistory();
            $current_fs->where('user_id','=',$u->id);
            $current_fs->order_by(DB::expr('src desc, created desc, id desc'));
            $current_fs->limit(5);

            $current_fs = $current_fs->find_all()->as_array();

            sort($current_fs);

            $have_freespins = auth::user()->getFreespins(auth::$user_id)->loaded();

            $next_fs = new stdClass();
            $next_fs->created = ($u->last_bet_time??time())+$u->office->bonus_diff_last_bet*60*60;

            if($bets['ds_info'] && count($bets['ds_info'])) {

                $in=0;
                $out=0;
                $sum=0;

                usort($bets['ds_info'], function ($item1, $item2) {
                    return $item2['cnt'] <=> $item1['cnt'];
                });

                foreach($bets['ds_info'] as $b) {
                    $in+=$b['in'];
                    $out+=$b['out'];
                }

                $sum=$bets['sum'];

                $bets_values=array_values($bets['ds_info']);

                $next_fs->in = rtrim(sprintf('%.'.$mult.'F',$in),'0');
                $next_fs->out = rtrim(sprintf('%.'.$mult.'F',$out),'0');
                $next_fs->coeff = $bonus_coeff;
                $c = auth::user()->calc_fsback($sum*$bonus_coeff,$bets_values[0]['game'],$bets_values[0]['game_id']);


                if($c) {
                    $next_fs->sumfsback = rtrim(sprintf('%.'.$mult.'F',$c['win']),'0');
                    $z = floor($c['win']/$c['zzz']);
                    $next_fs->fs_count = $z;

                    $bets = array_values(array_filter($bets['ds_info'],function($v) {
                        return !th::cantFSback($v['game']);
                    }));

                    $next_fs->visible_name = '-';

                    if(count($bets)) {
                        $g = new Model_Game($bets[0]['game_id']);
                        if($g->loaded()) {
                            $next_fs->visible_name = $g->visible_name;
                        }
                    }

                    $next_fs->amount = rtrim(sprintf('%.'.$mult.'F',$c['zzz']),'0');
                    $next_fs->lines = explode('-',$c['near'])[0];
                }
            }


            $v->last_calc=$last_calc;
            $v->have_freespins=$have_freespins;
            $v->history=$history;
            $v->sum_all=$sum_all;
            $v->time=$time;
            $v->timenext=time()+$this->_cache_time;
            $v->current_fs=$current_fs;
            $v->next_fs=$next_fs;
            $v->bonus_coeff=$bonus_coeff;

            $v=$v->render();

            dbredis::instance()->set($cache_key, $v);
            dbredis::instance()->expire($cache_key, $this->_cache_time);
            dbredis::instance()->select(0);
        }

        $u->ds_notify=0;
        $u->save();

        $this->auto_render=false;
        $this->response->body($v);
    }

    public function action_promo()
    {
        $u = auth::user();

        if(empty(auth::$user_id)) {
            throw new HTTP_Exception_404;
        }

        $l=arr::get($_GET,'l','en');

        I18n::$lang=$l;

        $cache_key = 'promo_'.$u->id.$l;

        $v = new View('site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . 'promo');
        $v->u = $u;
        $events = $u->futureAndNowEvents($u->office_id,['promo']);

        $prepared_events = [];
        if ($events) {
            foreach ($events as $event) {

                $played = $event->isNotPlayed(auth::user(),true);

                if (!$played) {
                    continue;
                }

                $e = $event;

                $prepared_events[] = $e;

            }
        }

        $v->events = $prepared_events;

        $v=$v->render();

        $this->auto_render=false;
        $this->response->body($v);
    }

    public function action_ls()
    {
        $u = auth::user();

        if(empty(auth::$user_id)) {
            throw new HTTP_Exception_404;
        }

        $l=arr::get($_GET,'l','en');

        I18n::$lang=$l;

        $cache_key = 'ls_'.$u->id.$l;

        $v = new View('site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . 'luckyspins');
        $v->u = $u;
        $events = $u->futureAndNowEvents($u->office_id,['progressive']);

        $prepared_events = [];
        if ($events) {
            foreach ($events as $event) {

                $played = $event->isNotPlayed(auth::user(),true);

                if (!$played) {
                    continue;
                }

                $e = new stdClass();
                $e->next_begining_time = $event->startTime();
                $e->fs_amount = $event->fs_amount;
                $e->fs_count = $event->fs_count;
                $e->extra_params = $event->extra_params;
                $e->duration = $event->duration;
                $e->playeddays = $event->getPlayedDaysCount($e->fs_count);
                $e->days_of_week = $event->days_of_week;
                $e->id = $event->id;
                $e->games = $event->gameList(true);
                $e->sumbets = $event->madeBetsSum(auth::$user_id);
                $e->wager = $event->wager();

                if($e->playeddays==0) {
                    $e->wager=0;
                }

                if($e->next_begining_time>time() && date('w')==0) {
                    $e->playeddays++;
                }

                $prepared_events[] = $e;

            }
            usort($prepared_events, function ($a, $b) {
                if ($a->next_begining_time == $b->next_begining_time)
                    return 0;
                return (($a->next_begining_time < $b->next_begining_time) ? -1 : 1);
            });
        }

        $v->events = $prepared_events;

        $v=$v->render();

        $this->auto_render=false;
        $this->response->body($v);
    }

    public function action_jserror()
    {
        $post = $this->request->post();
        Kohana::$log->add(Log::ALERT,'JS ERROR!' . json_encode($post));
    }

    public function action_game ()
    {
        $id = $this->request->param ('id');
        if (!$id)
        {
            throw new HTTP_Exception_404;
        }

        $game = new Model_Game(['name'=>$id,'brand'=>'agt']);
        if (!$game->loaded() || $game->show==0)
        {
            throw new HTTP_Exception_404;
        }

        $canJoinEvent=auth::user()->checkEvents($game);

        if($canJoinEvent && $canJoinEvent->type!='promo') {
            auth::user()->joinEvent($game,$canJoinEvent);
        }
        elseif(auth::user()->office->apitype==4 && !in_array($game->name,['keno','acesandfaces','jacksorbetter','tensorbetter']+th::getMoonGames())) {
            $fs = auth::user()->getFreespins(auth::$user_id,false,false,$game->id);
            if(!$fs || !$fs->loaded()) {
                auth::user()->pay_bia(false,$game->name,$game->id);
            }
        }
        else {
            auth::user()->pay_bia();
        }

        $checkGame=$game->id;

        $fs = auth::user()->getFreespins(auth::$user_id,false,false,$checkGame);

        if($fs && $fs->loaded()) {
            $auto=false;

            if(empty($fs->starttime) || (in_array($fs->src,['cashback','lucky']) && $fs->active==0 && $fs->updated<=3)) {
                $fs->starttime=time();
                $fs->save();
            }

            //если положен кэшбек, и зайти например в покер, то кешбек обнуляется и игроки ничего не получают. почему так было сделано?
            if(th::cantFSback($game->name) ||
                    (in_array($fs->src,['cashback','lucky']) && $fs->active==0 && (($fs->updated>3 || $fs->starttime+Date::MINUTE*20<=time()) && $auto=true)) ||
                    ($fs->active==1 && $fs->starttime+Date::MINUTE*20<=time() && $auto=true)) {

                if($fs->fs_offer_type=='infingift' && $auto && $fs->sum_win>0) {
                    //отправляем в инфин что успели отыграть

                }

                if($fs->fs_offer_type!='betconst') {
                    $fs->declineFreespins($fs->id,$auto);
                    $fs=null;
                }
            }
        }
        elseif($canJoinEvent && $canJoinEvent->type=='promo') {
            auth::user()->joinEvent($game,$canJoinEvent);
        }

        //убрано 23.11.23
        /*if($fs && $fs->loaded() && !empty($fs->gameids) && count($fs->gameids)>1) {
            if(!in_array($this->game_id,$fs->gameids)) {
                $game = new Model_Game($fs->gameids[array_rand($fs->gameids)]);

                $query = $this->request->query();
                $query['back']=$this->request->param('id');
                $query['token']=auth::user()->api_key;
                $link = $game->get_link().'?'. http_build_query($query);


                $this->request->redirect($link);
            }
        }
        elseif(!auth::user(true)->last_game && $fs && $fs->loaded() && $this->request->param('id') != $fs->game->name) {
            $game = new Model_Game(['name' => $fs->game->name,'brand'=>'agt']);

            $query = $this->request->query();
            $query['back']=$this->request->param('id');
            $query['token']=auth::user()->api_key;
            $link = $game->get_link().'?'. http_build_query($query);


            $this->request->redirect($link);
        }*/

        $view           = new View ('site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . $game->type);
        $view->game     = $game;
        $view->name     = $id;
		
		if($game->type=='moon') {

            $o=auth::user()->office;
            $currency=$o->currency;

            $moonLimits=th::getMoonLimits($o,$currency);


            $minBet=$moonLimits['moon_min_bet'];
            $maxBet=$moonLimits['moon_max_bet'];
            $maxWin=$moonLimits['moon_max_win'];

            if($game->name=='aerobet') {
                $minBet=arr::get($this->request->query(), 'minbet',$minBet);
                $maxBet=arr::get($this->request->query(), 'maxbet',$maxBet);
            }

            $view->minBet=$minBet;
            $view->maxBet=$maxBet;
			$view->maxWin=$maxWin;


            $view->bets = $this->getMoonBets($game,auth::user()->office,$minBet,$maxBet);
        }
		
        $view->theme     = (string) Kohana::$config->load('agtthemes.'.$id);
        $this->template = $view;
    }

	protected function getMoonBets(Model_Game $game,Model_Office $office,$minBet,$maxBet) {
        $bets=$defBets=[10,20,50,100];

        if($game->name=='aerobet') {
            $bets=$defBets=[10,50,100,200,500];

            $qBets=$this->request->query('bets');
            if(!empty($qBets)) {

                $bets=explode(',',$qBets);

                $bets=array_filter($bets,function($a) use($minBet,$maxBet) {
                    return is_numeric($a) && ($minBet<=$a || $maxBet>=$a);
                });

                $bets=array_map(function($a) {
                    return $a*10;
                },$bets);

                if(empty($bets)) {
                    $bets=$defBets;
                }
            }
        }
        
        return $bets;
    }

    public function action_init ()
    {

        if (Request::$initial !== NULL && !Request::$initial->is_ajax()){
            $_GET=$this->request->query();
        }
        $gameId = arr::get ($_GET, 'gamename');

        $gameM = new Model_Game(['name'=>$gameId,'brand'=>'agt']);
        if (!$gameM->loaded() || $gameM->show==0)
        {
            throw new HTTP_Exception_404;
        }

        game::session ($this->brand, $gameId);

        $this->auto_render = false;
        $action            = arr::get ($_GET, 'action');

        $logic_class='game_'.$gameM->type.'_'.$this->brand;

        $game = new $logic_class($gameId);

        $logic_game_class='game_'.$gameM->type.'_'.$this->brand.'_'.$gameId;

        if(class_exists($logic_game_class)) {
            $game= new $logic_game_class($gameId);
        }

        if ($action == 'info')
        {
            $ans = $game->info();
            echo $ans;
            exit;
        }
        //init
        if ($action == 'start')
        {
            $ans = $game->init ();

            if(auth::user()->office->enable_bia>0 && auth::user()->ds_notify) {
                $ans['ds_notify']=1;
            }

            if(in_array(auth::user()->office->currency->code,['ZAR','JMD'])){
                foreach($ans['langs'] as $l=>&$a) {
                    $a['jpinfo']=str_replace('jackpots','bonus prizes',$a['jpinfo']);
                }
            }
			
			$l = new Model_Login;
            $l->ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $l->user_id = auth::user()->id;
            $l->fingerprint = arr::get($_GET,'agtunique');
            $l->save();

        }

        if ($action == 'close')
        {

		$game->save_win();
		exit;
        }

        //init comb
        if ($action == 'restore')
        {
            $ans = $game->restore ();
        }

        if ($action == 'fscheck')
        {
            $ans = $game->fscheck();
        }

        if ($action == 'eventprocess')
        {
            $ans = $game->eventprocess();
        }

        if ($action == 'anothergame')
        {
            $ans = $game->anothergame();
        }

        if ($action == 'fsprocess')
        {
            $ans = $game->fsprocess(arr::get($_GET,'act','accept'),arr::get($_GET,'fs_id'));
        }

        //balance update
        if ($action == 'balance')
        {
            $ans = $game->get_balance ();
        }
        //promo request, such as lucky spins, top wins, etc.
        if ($action == 'promo')
        {
            $ans = $game->promo();
        }

        //spin
        if ($action == 'spin')
        {
            //li (line index)- индекс выбранного элемента массива линий
            $lidx = arr::get ($_GET, 'li', -1);
            $didx = arr::get ($_GET, 'di', -1);
            $amount = arr::get ($_GET, 'amount', 0);
            $ans  = $game->spin ($lidx, $amount,$didx);
        }
		
		//buyfg
        if ($action == 'buyfg')
        {
            //li (line index)- индекс выбранного элемента массива линий
            $lidx = arr::get ($_GET, 'li', -1);
            $didx = arr::get ($_GET, 'di', -1);
            $amount = arr::get ($_GET, 'amount', 0);
            $ans  = $game->buyfg($lidx, $amount,$didx);
        }

        //save win
        if ($action == 'save')
        {
            $ans = $game->save_win();
        }

        if ($action == 'finishjp')
        {
            $ans = $game->finishjp();
        }

        //save choose
        if ($action == 'savechoose')
        {
            $chooser_btns = arr::get ($_GET, 'chooser_btns', []);
            $ans = $game->savechoose($chooser_btns);
        }

        //nextjpcard
        if ($action == 'nextjpcard')
        {
            $ans = $game->nextjpcard();
        }

        //lastjpcard
        if ($action == 'lastjpcard')
        {
            $ans = $game->lastjpcard();
        }

        //freerun
        if ($action == 'freespin')
        {
            $ans = $game->bonus_game ();
			
			bet::prepareToHistory([
                's2'=>arr::get($ans,'session_total_win_free',0),
            ]);
        }

        //double
        if ($action == 'double')
        {
            $ans = $game->double (arr::get($_GET, 'color'));
        }

		if(!in_array($action,['start','restore','savechoose','promo','balance','fsprocess'])) {

            bet::prepareToHistory([
                'linesMask'=>arr::get($ans,'linesMask',[]),
                'linesValue'=>arr::get($ans,'linesValue',[]),
                'lang'=>I18n::$lang,
                'strict_double'=>office::instance()->office()->strict_double,
                'chooser_btns'=>arr::get($ans,'chooser_btns',[]),
                'bonus'=>arr::get($ans,'bonus',0),
                'bonus_all'=>arr::get($ans,'bonus_all',0),
                'bonus_win'=>arr::get($ans,'bonus_win',0),
                'gamble_suit_history'=>arr::get($ans,'gamble_suit_history',[]),
                'hold'=>arr::get($ans,'hold',''),
                'wincard'=>arr::get($ans,'wincard',[]),
                'last5_history'=>arr::get($ans,'last5_history',[]),
                'pokerStep'=>arr::get($ans,'pokerStep',''),
                'replace_sym'=>arr::get($ans,'replace_sym',''),
                'suite'=>arr::get($ans,'suite',''),
                'currency_id'=>office::instance()->office()->currency_id,
                'owner'=>office::instance()->office()->owner,
            ]);

            try {
                bet::putToHistory();
            }
            catch (Exception $ex) {
                Kohana::$log->add(Log::ALERT,$ex->getMessage().PHP_EOL.print_r(bet::$arrToHistory,1).PHP_EOL);
                th::critAlert('error put to history');
            }
        }

        $this->response->body (json_encode ($ans));
    }
}

