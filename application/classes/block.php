<?php


class block {
    //время хранения кеша 10мин
    protected static $cache_time = 60*10;

    protected static $instances = [];
    protected static $_instance = 'topwins';

    public static function instance($param) {
        self::$_instance = $param;
        if(!isset(self::$instances[$param])) {
            self::set_params();
        }
        return self::$instances[$param];
	}

    public static function set_params() {
        $cache_time = 24*60*60;

        $status_instance='main';

        if(OFFLINE) {
            $status_instance = ''.OFFICE;
        }

        $t=new Model_Topwin();

        $currency = Office::instance()->office()->currency->code;

        if (Status::instance($status_instance)->topwin+$cache_time<time()){

            $min = 50000/(Office::instance()->office()->currency_coeff??1);
            $max = 250000/(Office::instance()->office()->currency_coeff??1);

            $rand = round(th::randValue($min, $max)/100)*100;
            $games = kohana::$config->load('static.gamestopwins');
            $rand_game = mt_rand(1, count($games));

            $t->amount= $rand;
            $t->name=nic::randomName();
            $t->game_id= $games[$rand_game];
            $t->currency=$currency;

            $t->save();

            Status::instance($status_instance)->topwin=time();
        }

        $wins=$t->maxWins(5, $currency ,$cache_time);
        self::$instances[self::$_instance] = $wins;
    }

    public static function winners() {
        $t=new Model_Topwin();
        $config = kohana::$config->load('wins.win_now');

        $min = $config['min'];
        $max = $config['max'];
        $limit = $config['count_row'];

        if (Status::instance()->winners+static::$cache_time<time()){


            $rand = mt_rand($min, $max);
            $games = kohana::$config->load('static.gamestopwins');
            $rand_game = mt_rand(1, count($games));

            $t->amount= $rand;
            $t->name=nic::randomName();
            $t->game_id= $games[$rand_game];
            $t->currency='RUB';
            $t->save();


            Status::instance()->winners=time();
        }

        $wins=$t->winners($limit, $min, $max, static::$cache_time);

        return $wins;
    }

	public static function topwin() {
        $view=new View('block/topwin');
        $view->winners = self::winners();
        $view->data=self::instance('topwins');
		return $view->render();
	}

    public static function topwin_slider() {
        $view = new View('block/topwinslider');
        $view->data = self::winners();
        return $view->render();
    }

    public static function payed(){

		if(time() - Status::instance()->payedlast > 60){
			Status::instance()->payed+= mt_rand(5000,10000);
            Status::instance()->payedlast=time();
		}

		$view=new View('block/payed');
		$view->payed=(Status::instance()->payed)/10;
		$j=$view->render();

		return $j;
	}

    public static function jackpot(){

		if(mt_rand(0,1000)!=1000){
			Status::instance()->jackpot+=(time()-Status::instance()->jackpotlast)*mt_rand(650,850);
		}
		else{
			Status::instance()->jackpot=100000000;
		}
		Status::instance()->jackpotlast=time();

		$view=new View('block/jackpot');
		$view->jackpot=(Status::instance()->jackpot)/10;
		$j=$view->render();

		return $j;


	}

	public static function ticker(){

        $cache_name = 'ticker';

		$top=Cache::instance()->get($cache_name);

		if (!$top OR Status::instance()->$cache_name+static::$cache_time<time()){
			$sql='select name, amount, currency
				from topwins
				where created>:time
				order by created desc
				limit 5';

			$wins=db::query(1,$sql)->param(':time',time()-60*60*24*30)
								->execute()
								->as_array();

			$top="&nbsp;".__('Последние выигрыши')."&nbsp;&nbsp;";
			$i=1;
			foreach ($wins as $win){
				$top.="&nbsp;$i.&nbsp;".th::hidename($win['name'])." - " . $win['amount'] . " {$win['currency']}&nbsp;";
				$i++;
			}

            $sql_tournament = <<<SQL
                Select tr.user_name, tr.count_points, s.name, s.prize
                From shares s JOIN share_winners tr ON s.id = tr.share_id
                Where s.time_from < :time
                    AND s.time_to > :time
                    AND theme = :theme
                ORDER BY tr.count_points desc
                LIMIT 5
SQL;
            $res_tournament = db::query(1, $sql_tournament)->param(':time', time())->param(':theme', THEME==false?'0':THEME)->execute()->as_array();

            if(count($res_tournament)) {
                $top .= "&nbsp;||&nbsp;".__('Турнир')."&nbsp;{$res_tournament[0]['name']}&nbsp;".__('Призовой фонд').":&nbsp;{$res_tournament[0]['prize']}&nbsp;".__('Лидеры турнира').":";
                $i = 1;
                foreach ($res_tournament as $tournament) {
                    $top .= "&nbsp;$i.&nbsp;".th::hidename($tournament['user_name'])."&nbsp;-&nbsp;".$tournament['count_points'];
                    $i++;
                }
            }

            $sql_games = <<<SQL
                Select visible_name
                From games
                Where provider = 'our'
SQL;

            $res_games = db::query(1, $sql_games)->execute('games')->as_array();
            shuffle($res_games);

            $top .= "&nbsp;||&nbsp;".__('Самые горячие игры сейчас').":";
            foreach ($res_games as $k => $game) {
                ++$k;
                if($k>5) {
                    break;
                }

                $top .= "&nbsp;$k.&nbsp;{$game['visible_name']}";
            }

            shuffle($res_games);

            $top .= "&nbsp;||&nbsp;".__('Самые щедрые игры сейчас').":";
            foreach ($res_games as $k => $game) {
                ++$k;
                if($k>5) {
                    break;
                }

                $top .= "&nbsp;$k.&nbsp;{$game['visible_name']}&nbsp;(payout " . mt_rand(100, 120) . "%)&nbsp;";
            }

            $top .= "&nbsp;||&nbsp;";

			Cache::instance()->set($cache_name,$top,static::$cache_time);
            Status::instance()->$cache_name=time();
		}

		return $top;


	}

	public static function gamelist($count=4){

		$list=[];
		$i=1;
		$games=Kohana::$config->load('games');
		$games=th::ObjectToArray($games);

		while ($i<=$count) {

			$cat=array_rand($games);
			$name=array_rand($games[$cat]);
			if (!isset($list[$cat][$name])){
				$list[$cat][$name]=$games[$cat][$name];
				$i++;
			}

		}
		$view=new View('block/gamelist');
		$view->games=$list;
		return $view->render();


	}

    public static function share(){

        $t=new Model_Share();

		$share=$t->lastShare();
        $data=[];
        if(!empty($share[0])){
            $s=new Model_Share($share[0]['id']);
            $data=[
                    0=>[
                            'id'=>$share[0]['id'] ,
                            'name'=>!empty($sname=$s->share_langs->where('lang', '=',Cookie::get('lang'))->find()->name) ? $sname: $s->name,
                            'image'=>!empty($simage=$s->share_langs->where('lang', '=',Cookie::get('lang'))->find()->image) ? $simage: $s->image,
                    ]
            ];
        }


        $view=new View('block/share');

        $view->data=$data;

        return $view->render();
    }

    public static function chest() {
        $view = new View('block/chest');
        return $view->render();
    }

    public static function bonus() {
        $view=new View('block/bonus');

        return $view->render();
    }

    public static function flashcheck() {
        $view=new View('block/flash');
        return $view->render();
    }

    public static function paydouble() {
        $view=new View('block/paydouble');
        return $view->render();
    }

    public static function tournament() {
        $view = new View('block/tournament');

        $tournaments = orm::factory('share')->where('type', '=', 'tournament')->and_where('time_from', '<', time())
                        ->and_where('theme', '=', THEME==false?'0':THEME)->and_where('time_to', '>', time())->and_where('enabled', '=', 1)->find_all();

        $cache_name = 'tour';

        if (Status::instance()->$cache_name+static::$cache_time<time()) {

            $sql_results = <<<SQL
                Select s.id, count(*)
                From shares s LEFT JOIN share_winners tr ON s.id = tr.share_id
                Where s.time_from < :time
                    AND s.time_to > :time
                    AND type = 'tournament'
                    AND theme = :theme
                GROUP BY s.id
SQL;

            $results = db::query(1, $sql_results)->param(':time', time())->param(':theme', THEME==false?'0':THEME)->execute()->as_array();

            foreach ($results as $tourn) {
                if($tourn['count'] < 20) {
                    self::gener_tourn_users($tourn['id']);
                } else {
                    self::update_tourn_users($tourn['id']);
                }
            }

            Status::instance()->$cache_name=time();
        }

        $view->tournaments = $tournaments;

        return $view->render();
    }

    private static function gener_tourn_users($share_id, $count = 20) {
        for($i=1;$i<=$count;$i++) {
            $fake_user_res = new Model_Sharewinners();
            $fake_user_res->count_points = th::randValue(1000, 25000);
            $fake_user_res->user_name=nic::randomName();
            $fake_user_res->share_id = $share_id;
            $fake_user_res->save();
        }
    }

    private static function update_tourn_users($share_id, $count = 15) {
        $sql_fake_users = <<<SQL
            Select id, user_id
            From share_winners
            Where user_id is null
                AND share_id = :share_id
SQL;
        $fake_users = db::query(1, $sql_fake_users)->param(':share_id', $share_id)->execute()->as_array();
        shuffle($fake_users);

        $s = new Model_Share($share_id);
        $time_end = $s->time_to;
        $leader=self::fake_winners($share_id, 15);//real user top win
        for($i=1;$i<=$count;$i++) { //5 случайных ботов
            $fake_user_res = new Model_Sharewinners($fake_users[$i]['id']);
            $sleep_time = th::randValue(14400, 28800);//Время сна бота
            if(!$fake_user_res->play_to ){//Если нет времени конца игры, то назначать время конца игры, начиная с текущего
                $fake_user_res->play_to = time()+th::randValue(7200, 21600);
            }
            $curr_points=$fake_user_res->count_points;//текущий счет бота
            $time_last=$fake_user_res->last_upd;// время послднего обновления текущ бота
            $d = (($time_end-time())/self::$cache_time);
            $a = (time()-$time_last)/self::$cache_time;
            if($d==0){
                $d=1;
            }
            $plus = $a*($leader['count_points']-$curr_points)/$d;
            if($plus<0){
                $plus=th::randValue(0,100);
            }else if($plus>$leader['count_points']){
                $plus=th::randValue($leader['count_points']/4,$leader['count_points']/2);
            }
            if($fake_user_res->play_to<= time() && $fake_user_res->play_to+$sleep_time>time()){
                $plus=0;
            }else{
                $fake_user_res->play_to = time()+th::randValue(1, 100);
                $fake_user_res->last_upd = time();
            }
            $fake_user_res->count_points += $plus;
            $fake_user_res->save();
            self::check_danger($share_id, $time_end, $count, $leader);//Проверка есть ли в топе люди
        }
    }

    private static function fake_winners($share_id, $count = 15) { //$count Сколько ботов должно быть гарантированно в топ15
        $sql_who_to_win=<<<SQL
                SELECT  id, user_name, count_points FROM (SELECT id, user_id, user_name, count_points FROM share_winners WHERE share_id=:share_id ORDER BY count_points DESC LIMIT (:limit)) as s WHERE user_id is not NULL ORDER BY count_points DESC limit(1);
SQL;
        $params_who_to_win = [
            ':share_id' => $share_id,
            ':limit' => $count,
        ];
        $who_to_win = db::query(1, $sql_who_to_win)->parameters($params_who_to_win)->execute()->as_array();
        if (empty($who_to_win[0])){
            $who_to_win[0]['count_points']=0;
        }
        return $who_to_win[0];
    }
    private static function check_danger($share_id, $time_end, $count, $leader) { //$count Сколько ботов должно быть гарантированно в топ15

        $sql_dng=<<<SQL
                select count(user_name) from (select user_name, user_id from share_winners where share_id=:share_id ORDER BY count_points DESC LIMIT(:limit)) as s where user_id is not null;
SQL;
        $params_dng = [
            ':share_id' => $share_id,
            ':limit' => $count,
        ];
        $dng = db::query(1, $sql_dng)->parameters($params_dng)->execute()->as_array();
        $addcount=intval($dng[0]['count']);//Сколько ботов добавлять
        if(($time_end-time())/self::$cache_time<=3 && $addcount>0){//Если до конца акции осталось не более 3х запусков и нужны еще боты, то добавляем
            for($i=1;$i<=$addcount;$i++) {
                $fake_user_res = new Model_Sharewinners();
                $fake_user_res->count_points = th::randValue($leader['count_points'], $leader['count_points']+2000);
                $fake_user_res->user_name=nic::randomName();
                $fake_user_res->share_id = $share_id;
                $fake_user_res->save();
            }
        }
    }

    public static function fs_view($fs) { //freespins in game view
        $view = View::factory('block/fs/view');
        $view->fs = $fs;
        return $view->render();
    }

    //todo возможно потом вынести отдельно куда то

     public static function admin_active_users_count() {
        $sql = "select DISTINCT user_id from bets where created >= extract('epoch' from CURRENT_TIMESTAMP)-10*60 limit 100";
        $users = array_keys(db::query(1,$sql)->execute()->cached(5*60)->as_array('user_id'));
        $count = count($users);
        $route = Request::current()->route();
        $href = $route->uri(['controller'=>'user']).'?id_list='. urlencode(implode(',',$users));
        return '<a href="/'.$href.'">P: <strong>'.$count.'</strong></a>';
    }

    public static function admin_waiting_payouts() {
        return '';
        $p = new Model_Payment;
        $count = $p->where('amount','<','0')
          ->where('status','=',0)
          ->count_all();

        $route = Request::current()->route();
        $href = $route->uri(['controller'=>'payment']).'?status=0&paymentamount=2';
        return '<a href="/'.$href.'">W: <strong>'.$count.'</strong></a>';
    }

    public static function onlineplayers(){
        $status = new Model_Status('online');

        if(!$status->loaded()) {
            $status->value=850;
            $status->last = 0;
            $status->type = 'main';
            $status->id='online';
            $status->save();
        }

		if($status->last < time()-60*60){
			$status->value += mt_rand(-5, 5);
            $status->last = time();
            $status->save();
		}

		return $status->value;
	}

    public static function yaM(){
        if(THEME=='robot') {
            return '';
        }
        $view = View::factory('block/counter/yametrica');
        return $view->render();
    }


    public static function jackpot_win(){
        $view = View::factory('block/jackpotwin');
        return $view->render();
    }

    public static function ban() {
        $view = View::factory('block/ban');
        return $view->render();
    }

    public static function rfid_listen($person=false) {
        if(!KIOSK && !$person) {
            return '';
        }
        $view = View::factory('block/rfid');
        $view->is_person=$person;
        return $view->render();
    }

    public static function gamejp($jquery=false) {
        $view = View::factory('block/gamejp');
        $jpmodel = new Model_Jackpot();
        $jackpots = $jpmodel
            ->where('office_id','=',auth::user()->office_id??OFFICE)
            ->where('active','=',1)
            ->order_by('type')
            ->find_all();
        $view->jackpots = $jackpots;
        $view->jquery = $jquery;
        return $view->render();
    }

}
