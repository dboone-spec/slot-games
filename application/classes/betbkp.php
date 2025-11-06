<?php

class bet {

    public static $trader_transaction=['error'=>true];
    protected static $small_bank_amount = null;
    public static $last_bet_id;
    public static $jpwin=0;


    public static function needZero(){
        $o = office::instance()->office();
        $RTP=(int) $o->rtp;
        if ($RTP<80 or $RTP>=96){
            return false;
        }

        if (mt_rand(1,10000)/10000>$RTP/96){
            return true;
        }

        return false;


    }



    //есть ли сума достаточчная для выигрыша в банке
    public static function HaveBankAmount($win) {

        if(DEMO_DOMAIN) {
            return true;
        }

		//выход если выиграл меньше минимального порога
		if ($win <= Kohana::$config->load('static.zero_win')) {
			return true;
		}

		//информация может быть уже не актульная, да и хуй с ней
		$st = office::instance()->office();
        if($st->use_bank==0) {
            return true;
        }
		$bank = $st->bank - $st->users;

        $res = $win <= Kohana::$config->load('static.bank_percent_win') * $bank;

        if(!$res) {
            self::$small_bank_amount = $win;
        }

		return $res;
    }

    /** проверка ставки на ошибки
     * @param type $amount сумма ставки или массив с суммами ставок
     *
     *
     * @return int//1 не авторизован
      //2 не завершенная игра
      //3 закртыто на хуй
      //4 нет денег
      //5 не верный формат данных
      //6 не верный формат ставки
      //7 сервер не доступен
      //8 разрыв связи
      //9 jackpot
     *
     */
    public static function error($amount, $noerror = [], $freespins=false) {

        //TODO Добавить казино закрыто на хуй
        //TODO Игра закрыта на хуй
        //не авторизован
        if (!Auth::$user_id) {
            return 1;
        }

        //сессия не загружена (играть нельзя)
        if (!game::session()->loaded()) {
            return 3;
        }

        if (!is_array($amount)) {
            $amount = [$amount];
        }
        $sum = array_sum($amount);
        $user = auth::user(true);
        $office = orm::factory('office')->where('id', '=', $user->office_id)->find();

        //нет денег
        if (bccomp($user->amount($freespins),$sum)<0) {
            return 4;
        }

        //на офисе нет денег
        

        //ставка маленькая или высокая
        foreach ($amount as $bet) {
            if (($bet < $office->bet_min)) {
                if (!in_array(6, $noerror)) {
                    return 6;
                }
            }

            if (($bet > $office->bet_max)) {
                return 6;
            }
        }

        //TODO добавить ставка маленькая или слишком высокая для игры


        return 0;
    }

    public static function start($amount, $data = null) {

        database::instance()->begin();

        game::session()->flash($data);

        $sql = 'update users
			set amount=amount-:amount
			where id=:uid';

        db::query(1, $sql)->param(':amount', (float) $amount)
                ->param(':uid', auth::$user_id)
                ->execute();

        database::instance()->commit();
    }
	
	public static function sendapi($in,$out,$game,$game_model,$bettype,$bet_id,$data) {
        if(GAMECONTENT AND auth::user()->office->apienable AND auth::user()->office->gameapiurl AND auth::user()->office->apitype==0) {

            //strict egt if need
            if(false && $game_model->brand=='egt') {
                throw new Exception('work on regulation');
            }

            $n = new gameapi();
            $resapi = $n->bet(auth::user()->name, auth::user()->office->gameapiurl, [
                    'amount'=>(float) $in,
                    'win'=>(float) $out,
                    'game'=>$game,
                    'game_id'=>$game_model->id,
                    'bettype'=>$bettype,
                    'bet_id'=>$bet_id,
                    'come'=>$data['come'],
                    'result'=>$data['result'],
            ]);

            if(!$resapi) {
                $er = $n->last_error;
                $er = substr($er,0,100);

                if(auth::user()->office->seamlesstype==1) {
                    $b = new Model_Wrongbet();
		    $b->bet_id=$bet_id;
                    $b->office_id=auth::user()->office_id;
                    $b->save();
                }

                if(!empty($er)) {
                    header('CustomError: '.$er);
                }
                throw new Exception('wrong bet. '.$er);
            }
        }
    }

    public static function make($data, $bettype = 'normal', $ses_data = null) {

        $games = [];

        if(!isset($data['can_jp'])) {
            $data['can_jp']=true;
        }
	
	if(PROJECT==1) {
            $data['can_jp']=false;
        }

        foreach (th::gamelist() as $v) {
            if($v['provider']=='our') {
                $games[] = $v['name'];
            }
        }


        $sql_nextval = <<<SQL
            Select nextval('bets_id_seq')
SQL;
        $bet_id = key(db::query(1, $sql_nextval)->execute()->as_array('nextval'));

        if(isset($ses_data['freegames_start_from'])) {
            $ses_data['start_bet_id'] = $bet_id;
        }

        if (empty($ses_data)) {
            game::session()->save();
        } else {
            game::session()->flash($ses_data);
        }

        $in = $data['amount'];
        $out = $data['win'];
        $winAll=$out-$in;

        $game_type = game::session()->type;
        $game = game::session()->game;
        $bet_time = 0;

        $new_amount=0;

        $result = [];
        $game_model = new Model_Game(["name"=>$game,"provider"=>"our"]);

        $bet = new Model_Bet();
        $bet->id = $bet_id;
        $bet->user_id = auth::$user_id;
        $bet->amount = $data['amount'];
        $bet->game_type = $game_type;
        $bet->game = $game;
        $bet->type = $bettype; //normal, free game, bonus game, double
        $bet->come = $data['come'];
        $bet->result = $data['result'];
        $bet->office_id = OFFICE;
        $bet->win = $data['win'];
        $bet->game_id = arr::get($data, 'game_id', 0);
        $bet->method = $data['method'];
        $bet->is_freespin = $data['is_freespin']??false;
        $bet->balance = auth::user()->amount(true)-$data['amount']+$data['win'];//amount + bonus
        $bet->fg_level = game::data('fg_level',0);

        if(PROJECT==2 || auth::user()->office->seamlesstype==0) {
            self::sendapi($in,$out,$game,$game_model,$bettype,$bet_id,$data);
        }

        database::instance()->begin();

        if(auth::user()->office->apienable AND auth::user()->office->apitype==0) {
            db::query(Database::UPDATE,'update offices set amount=amount+:winall where id=:id')
                    ->param(':id',auth::user()->office_id)
                    ->param(':winall',$winAll)
                    ->execute();
        }

        $jps=auth::user()->office->activeJackpots();

        if($data['can_jp'] && count($jps)) {
            $j = new model_jackpot();
            $jpwin = $j->donate($data['amount'],auth::user()->office_id);
            bet::$jpwin=$jpwin['win'];
            $j->win($jpwin);
        }

        $bet->save();


        $bet_time = microtime(true);
        $result = json_decode($data['result'], true);

        auth::user()->save();

        $new_amount = auth::user()->amount();


        if(!auth::user()->office->apienable OR auth::user()->office->apitype==1) {
            if ($winAll>0){
                $sql='update users
                    set amount=amount+:win,
                    sum_amount=sum_amount+:in,
                    sum_win=sum_win+:out
                    ';
                if($bettype=='free') {
                    $sql = 'update users
                            set amount=amount + :win,
                            sum_amount=sum_amount+:in,
                            sum_win=sum_win+:out ';
                }
            }
            else{
                //Сначала списывается основной баланс, затем - бонусный.
                $sql='update users
                    set amount=amount+:win,
                    sum_amount=sum_amount+:in,
                    sum_win=sum_win+:out,
                    last_bet_time=:last_bet_time
                    ';
            }
            $sql.='where id=:uid RETURNING amount as am';

            $new_amount_res = db::query(1, $sql)->param(':win', (float) $winAll)
                    ->param(':in', (float) $in)
                    ->param(':out', (float) $out)
                    ->param(':uid', auth::$user_id)
                    ->param(':last_bet_time', time())
                    ->execute()
                    ->as_array('am');

            $new_amount = arr::get(current($new_amount_res),'am',0);
        }


        //для счетчиков
        $bettypes = [$bettype];

        if($bettype == 'free') {
            $fg_level = game::data('fg_level',0);
            $bettypes[] = $bettype . '_' . $fg_level;
        }




        $count=1;

        $bonus=0;
        $bonusCount=0;

        $free=0;
        $freeCount=0;

        $double_in = 0;
        $double_out = 0;
        $doubleCount = 0;




        if ($bettype=='bonus'){
                $bonus=$winAll;
                $bonusCount=1;
                $count=0;
                $out=0;
        }
        if ($bettype=='free'){
                $free=$winAll;
                $freeCount=1;
                $count=0;
                $out=0;
        }


        if($bettype=='double') {
            $double_in = $in;
            $double_out = $out;
            $doubleCount = 1;

            $in = 0;
            $out = 0;
            $count=0;
        }


        $sql='update counters
                set "in"="in"+:in,
                "out"="out"+:out,
                "double_in"="double_in"+:double_in,
                "double_out"="double_out"+:double_out,
                "bonus"="bonus"+:bonus,
                "free"="free"+:free,
                "count"="count"+:count,
                double_count=double_count+:doubleCount,
                free_count=free_count+:freeCount,
                bonus_count=bonus_count+:bonusCount
                where game_id=:game
                AND office_id = :oid';

        $affected = db::query(Database::UPDATE,$sql)->param(':in',(float) $in)
                          ->param(':out',(float) $out)
                        ->param(':bonus',(float) $bonus)
                        ->param(':double_in',(float) $double_in)
                        ->param(':double_out',(float) $double_out)
                        ->param(':game',(int) $bet->game_id)
                        ->param(':oid', OFFICE)
                        ->param(':free', $free)
                        ->param(':count', $count)
                        ->param(':doubleCount', $doubleCount)
                        ->param(':bonusCount', $bonusCount)
                        ->param(':freeCount', $freeCount)
                          ->execute();

        if ($affected == 0) {
            $sql = 'insert into counters ( "in", "out", "double_in",  "double_out", "bonus",  "game_id",   "office_id",  "free",count,double_count,free_count,bonus_count)
									values(:in, :out, :double_in, :double_out, :bonus, :game,  :oid, :free,:count,:doubleCount,:freeCount,:bonusCount)';

            db::query(Database::INSERT, $sql)->param(':in', (float) $in)
                    ->param(':out',(float) $out)
                    ->param(':bonus',(float) $bonus)
                    ->param(':double_in',(float) $double_in)
                    ->param(':double_out',(float) $double_out)
                    ->param(':game',(int) $bet->game_id)
                    ->param(':oid', OFFICE)
                    ->param(':free', $free)
                    ->param(':count', $count)
                    ->param(':doubleCount', $doubleCount)
                    ->param(':bonusCount', $bonusCount)
                    ->param(':freeCount', $freeCount)
                    ->execute();
        }


        //retry for partners

        $sql='update counters_partners
                set "in"="in"+:in,
                "out"="out"+:out,
                "double_in"="double_in"+:double_in,
                "double_out"="double_out"+:double_out,
                "bonus"="bonus"+:bonus,
                "free"="free"+:free,
                "count"="count"+:count,
                double_count=double_count+:doubleCount,
                free_count=free_count+:freeCount,
                bonus_count=bonus_count+:bonusCount
                where game_id=:game
                AND office_id = :oid';

        $affected = db::query(Database::UPDATE,$sql)->param(':in',(float) $in)
                          ->param(':out',(float) $out)
                        ->param(':bonus',(float) $bonus)
                        ->param(':double_in',(float) $double_in)
                        ->param(':double_out',(float) $double_out)
                        ->param(':game',(int) $bet->game_id)
                        ->param(':oid', OFFICE)
                        ->param(':free', $free)
                        ->param(':count', $count)
                        ->param(':doubleCount', $doubleCount)
                        ->param(':bonusCount', $bonusCount)
                        ->param(':freeCount', $freeCount)
                          ->execute();

        if ($affected == 0) {
            $sql = 'insert into counters_partners ( "in", "out", "double_in",  "double_out", "bonus",  "game_id",   "office_id",  "free",count,double_count,free_count,bonus_count)
									values(:in, :out, :double_in, :double_out, :bonus, :game,  :oid, :free,:count,:doubleCount,:freeCount,:bonusCount)';

            db::query(Database::INSERT, $sql)->param(':in', (float) $in)
                    ->param(':out',(float) $out)
                    ->param(':bonus',(float) $bonus)
                    ->param(':double_in',(float) $double_in)
                    ->param(':double_out',(float) $double_out)
                    ->param(':game',(int) $bet->game_id)
                    ->param(':oid', OFFICE)
                    ->param(':free', $free)
                    ->param(':count', $count)
                    ->param(':doubleCount', $doubleCount)
                    ->param(':bonusCount', $bonusCount)
                    ->param(':freeCount', $freeCount)
                    ->execute();
        }


        database::instance()->commit();
	
	if(PROJECT==1 && auth::user()->office->seamlesstype==1) {
            self::sendapi($in,$out,$game,$game_model,$bettype,$bet_id,$data);
        }

        if($data['can_jp'] && count($jps)) {
            $jpdata = [
                    'values' => [],
            ];

            foreach(auth::user()->office->activeJackpots() as $jp) {
                $jpdata['values'][$jp->type]=[
                        'value'=>th::number_format($jp->current),
                        'hot'=>$jp->hot(),
                ];

                if($jp->last_win_time+$jp->jp_show_time() > time()) {
                    $jpdata['last_win_time'] = time();
                    $jpdata['win'] = th::number_format($jp->prev_value);
                    $jpdata['user_id'] = $jp->user_id;
                }
            }
            $j->toFile($jpdata,auth::user()->office_id);
        }

        /* НЕ ВКЛЮЧАТЬ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
         * если очень надо делать это в отдельном потоке
        $phones = Kohana::$config->load('static.alertphones');
        $max=auth::user()->office->alert_max_win;
        if($winAll>=$max) {
            $win_sms = auth::user()->short_email() . ' ';
            $win_sms .= ($game_model?th::short_name_game($game_model->visible_name):$game) . ' ';

            if($bettype=='double') {
                $win_sms .= 'd ';
            }

            $win_sms .= $winAll/1000;

            foreach($phones as $phone) {
                th::tgsend($phone, $win_sms);
            }
        }

        if(self::$small_bank_amount) {
            $bank_sms = 'bank ';
            $bank_sms .= ($game_model?th::short_name_game($game_model->visible_name):$game) . ' ';

            if($bettype=='double') {
                $bank_sms .= 'd ' ;
            }

            $bank_sms .= ' ' . $bet_id . ' ';
            $bank_sms .= (self::$small_bank_amount/1000) . ' ';
            $bank_sms .= auth::user()->short_email();

            foreach($phones as $phone) {
                th::tgsend($phone, $bank_sms);
            }
        }
        */

        bet::$last_bet_id = $bet_id;

        return $bet_id;
    }

}
