<?php

class Controller_Cron extends Controller{

    public function action_1003report() {
        $sql="select b.id, b.user_id, b.office_id,c.code,  b.info,  b.amount as rate, b. win, b.balance-b.win+b.amount as balance_before, b.balance as balance_after, b.come as lines,g.visible_name,
vdate(b.created+o.zone_time*60*60)
from bets b
join offices o on b.office_id=o.id
join currencies c on o.currency_id=c.id
join games g on g.id=b.game_id
where b.office_id=1003
and b.created>= EXTRACT( epoch from date_trunc('day', now() at time zone 'utc' ) )-24*60*60-o.zone_time*60*60
and b.created< EXTRACT( epoch from date_trunc('day', now() at time zone 'utc' ) )-o.zone_time*60*60";

        $data=db::query(1,$sql)
            ->execute()->as_array();

        $fields=[
            'id',
            'user_id',
            'office_id',
            'info',
            'rate',
            'win',
            'balance_before',
            'balance_after',
            'lines',
            'visible_name',
            'vdate',
        ];

        $csv='';

        $csv.=implode(';',$fields).PHP_EOL;

        foreach($data as $row) {
            $csv.=implode(';',$row).PHP_EOL;
        }

        Email::send('forrroger@yandex.ru',['no-reply@site-domain.com','site-domain.com'],'report','report '.date('Y-m-d'),false,$csv);

        logfile::create(date('Y-m-d H:i:s') . PHP_EOL,$csv, '1003csv');
    }

    public function action_updatecurrencies() {


        $keyProcess='updatecurrencies';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("updatecurrencies LOCKED!");
            return null;
        }

        Service::updateCurrencies();
        //Service::updateNullCurrencies();

        th::unlockProcess($keyProcess);
    }

    public function action_updatevertbetcurrencies() {

        $keyProcess='updatevertbetcurrencies';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("updatevertbetcurrencies LOCKED!");
            return null;
        }

        $api=new Api_Vertbet();
        $api->setURL('https://dev.vertbet.com/casinoapi/api/v1');

        logfile::create(date('Y-m-d H:i:s') . ' [UPDATE VERTBET CURRENCIES] Request', 'vertbet');

        $currencies=$api->getCurrencies();

        logfile::create(date('Y-m-d H:i:s') . ' [UPDATE VERTBET CURRENCIES] Response' . PHP_EOL . json_encode($currencies, 1), 'vertbet');

        $new_currencies=[];

        if($currencies) {

            foreach (json_decode($currencies, 1) as $curr) {
                $c = new Model_Currency(['external_id' => $curr['iso'],'source'=>'vertbet']);

                if (!$c->loaded()) {

                    $c->code = $curr['iso'];
                    $c->external_id = $curr['iso'];
                    $c->source = 'vertbet';
                    $c->name = $curr['iso'];
                    $c->icon = substr(implode(unpack('H*', iconv("UTF-8", "UCS-4BE", $curr['symbol']))), -4, 4);
                    $c->disable = 1;
                    $c->val = 0;

                    $new_currencies[]=$curr['iso'];
                }

                $c->updated = time();
                $c->mult = substr_count($curr['multiplier'], '0');

                $c->save();
            }

            if(count($new_currencies)) {

                Service::updateNullCurrencies();
                Service::updateCurrencies('vertbet');

                th::techAlert('NEW vb currencies! '.implode(', ',$new_currencies));
            }
        }

        th::unlockProcess($keyProcess);
    }

    public function action_jackpotsfinish() {

        $keyProcess='jpFinish';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("jpFinish LOCKED!");
            return null;
        }

        $redis = dbredis::instance();
        $jpModel = new Model_JackpotHistory();

        foreach((new Model_Office)->where('enable_jp','=','1')->find_all() as $o) {

            //время на расчет ДП
            $redis->select(1);

            $time = $redis->get('jpStartTime-'.$o->id);


            if(!$time) {
                $time=0;
            }

            if($redis->get('jpa-'.$o->id)==='0' && !$time) {

                $allCards = json_decode($redis->get('alljpcards-'.$o->id));

                if(!is_array($allCards)) {
                    th::critAlert("jpFinish ERROR! [".$o->id."]");
                }

                //авторозыгрыш
                if (!th::lockProcess($keyProcess.'-'.$o->id)){
                    continue;
                }

                $game = $redis->get('jpGame-'.$o->id);

                auth::$user_id=$redis->get('jpUser-'.$o->id);
                game::session('agt',$game);


                $jc = new jpcard();
                $level = $jc->level($allCards);

                $jpLevel = $jc->getJPNum($level);

                $jps = $o->activeJackpots()->as_array();

                $win=0;

                foreach($jps as $k=>$jp) {
                    if($jpLevel>3) {
                        $win+=$jp->current;
                        break;
                    }
                    else if($k==$jpLevel) {
                        $win=$jp->current;
                        break;
                    }
                }

                $redis->select(1);

                $jpModel->user_id = auth::$user_id;
                $jpModel->office_id = $o->id;
                $jpModel->game = $game;
                $jpModel->cards = $allCards;
                $jpModel->level = $jpLevel;
                $jpModel->win = $win;
                $jpModel->triggernum = $redis->get('jpTriggerNum-'.$jpModel->office_id);
                $jpModel->triggersum = $redis->get('jpTriggerSum-'.$jpModel->office_id.'-'.$jpModel->triggernum);
                $jpModel->hotstartsum = $redis->get('jpHotStartSum-'.$jpModel->office_id.'-'.$jpModel->triggernum);
                $jpModel->triggertime = $redis->get('jpTriggerTime-'.$jpModel->office_id);
                $jpModel->hotstart=$redis->get('jpHotStart-'.$jpModel->office_id.'-'.$jpModel->triggernum);
				$jpModel->trigger_bet_id = $redis->get('jpTriggerBetId-'.$jpModel->office_id);

                $redis->delete('jpcards-'.$o->id);
                $redis->delete('currjpcards-'.$o->id);
                $redis->delete('alljpcards-'.$o->id);
                $redis->delete('jpTime-'.$o->id);

                foreach($jps as $numjp) {
                    $redis->delete('jpHotStart-'.$o->id.'-'.$numjp);
                    $redis->delete('jpHotStartSum-'.$o->id.'-'.$numjp);
                }

                $redis->delete('jpBlock-'.$o->id);
				$redis->delete('jpTriggerBetId-'.$o->id);
                $redis->set('jpa-'.$o->id,1);

                $bet=[];

                $bet['amount']=0;
                $bet['come']=$jpLevel;
                $bet['result']=json_encode($allCards);
                $bet['win']=$win;
                $bet['game_id']=0;
                $bet['can_jp']=false;
                $bet['game_name']='jp';
                $bet['game_type']='jp';
                $bet['method']='jp';
				$bet['trigger_bet_id']=$jpModel->trigger_bet_id;

                bet::setJP($jpModel);

                bet::make($bet,'jp',null,true);
                th::unlockProcess($keyProcess.'-'.$o->id);

            }
        }

        $redis->select(0);

        th::unlockProcess($keyProcess);
    }

    public function action_jackpots() {

        $keyProcess='jpProcess';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("jpProcess LOCKED!");
            return null;
        }

        $redis = dbredis::instance();
        $redis->select(1);

        $j = new Model_Jackpot();

        foreach((new Model_Office)->where('enable_jp','=','1')->find_all() as $o) {
            if((bool) $redis->get('jpa-'.$o->id)) {
                $vals = [];
                for($i=0;$i<4;$i++) {
                    $vals[]=$redis->get('jps-'.$o->id.'-'.$i);
                    //for new offices
                    $redis->setNx('jpHotPercent-'.$o->id.'-'.$i,0.05);
                }
                $j->toFile([
                    'values'=>$vals
                ],$o->id);

            }
        }

        $redis->select(0);

        th::unlockProcess($keyProcess);
    }

    public function action_onlineplayers() {
        db::query(Database::UPDATE,'update games set online = b.cnt
from (select count(user_id) as cnt, game_id from bets where created>=:period group by user_id,game_id) as b
where id = b.game_id')
            ->param(':period',time()-Date::MINUTE)
            ->execute();
    }

	public function action_moonrestartnew()
    {
        $games_ids = [
            863 => 'app',
            903 => 'appmulti',
        ];

        if(defined('LOCAL') && LOCAL) {
            $games_ids = [
                83 => 'app',
                863 => 'app',
                53 => 'appmulti',
            ];
        }

        $last_rounds = db::query(1, 'select game_id,max(finished) as finished,max(created) as created
                                                from moon_results group by game_id limit :limit')
            ->param(':limit', count($games_ids))
            ->execute()
            ->as_array('game_id');

        $max_time = Date::MINUTE * 15;

        $rdb=dbredis::instance();

        $moonFile = $rdb->get('moon_apps');

        $json = json_decode($moonFile, 1);

        if (!empty($json)) {
            foreach ($games_ids as $id=>$appname) {

                if ($json[$id] ?? 0 > 0) {
                    $json[$id] = 0;
                    $rdb->set('moon_apps',json_encode($json));

                    $last_rounds[$id] = [
                        'game_id' => $id,
                        'finished' => null,
                        'created'  => time() - $max_time - 1,
                    ];
                }
            }
        }

        if(count($last_rounds)>0) {
            $isLA=false;

            if(!Kohana::$is_windows) {
                $la=sys_getloadavg();
                $la=$la[0];

                if($la>40) {
                    $isLA=true;
                }
            }

            foreach ($last_rounds as $last_round) {

                $need_restart=$isLA;

                if(empty($last_round['finished']) && $last_round['created'] + $max_time < time()) {
                    $need_restart=true;
                }

                if(!empty($last_round['finished']) && $last_round['finished'] + $max_time + Date::MINUTE*5 < time()) {
                    $need_restart=true;
                }

                if ($need_restart && !th::isBackupRunning()) {

                    th::techAlert('Moon was restarted![' . $last_round['game_id'] . ']');

                    shell_exec('forever stop /var/www/moon/' . $games_ids[$last_round['game_id']] . '.js');
                    shell_exec('forever start /var/www/moon/' . $games_ids[$last_round['game_id']] . '.js');
                }
            }
        }
    }

    public function action_moonrestart() {
		
		//old run from nginx
        return;
		
        $last_round=db::query(1,'select created,finished from moon_results where game_id=863 order by id desc limit 1')
            ->execute()
            ->as_array();

        $max_time=Date::MINUTE*15;

            if(empty($last_round[0]['finished']) && $last_round[0]['created']+$max_time<time()) {

            th::techAlert('Moon was restarted!');

            $pids=explode('-',file('/var/www/moon/moon.pid')[0]);

            //parent
            shell_exec('kill '.$pids[0]);
            //child
            shell_exec('kill '.$pids[1]);

            shell_exec('forever start /var/www/moon/app.js');

            exit;

            shell_exec('forever restart /var/www/moon/app.js');
        }
    }

	public function action_moonloosebetssoftswiss() {
        $keyProcess='moonloosebetssoftswissFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("moonloosebetssoftswiss LOCKED!");
            return null;
        }

        $games=[];

        $om = new Model_Office();

        $offices = $om
            ->where('seamlesstype','=',1)
            ->where('enable_moon_dispatch','=',1)
            ->where('apienable','=',1)
            ->where('apitype','=',12)
            ->find_all();


        $all_bets=[];

        foreach($offices as $o) {
            //отправлять по нарастающей. try=1 -> минута, try=2 -> 4 минуты и тд
            $bets = db::query(1,'select m.*,u.name as uname from moon_dispatch_bets m join users u on u.id=m.user_id where m.sended=0 and m.try<6 and m.office_id=:o_id')
                ->param(':o_id',$o->id)
                ->execute()
                ->as_array();

            if(!count($bets)) {
                continue;
            }

            $api = gameapi::instance(12);
            $url = $api->setUpEnv($o->is_test);

            foreach($bets as $bet_one) {

                if(!isset($games[$bet_one['game']])) {
                    $games[$bet_one['game']]=new Model_Game(['name'=>$bet_one['game']]);
                }

                $a = explode('-',$bet_one['uname']);

                array_pop($a);

                $params = [
                    'amount' => 0,
                    'fs_amount' => 0,
                    'win' => 0,
                    'game' => $games[$bet_one['game']]->name,
                    'game_id' => $games[$bet_one['game']]->id,
                    'game_type' => $games[$bet_one['game']]->type,
                    'bet_type' => 'normal',
                    'bettype' => 'normal',
                    'bet_id' => 0,
                    'initial_id' => $bet_one['initial_id'],
                    'come' => 0,
                    'result' => $bet_one['rate'],
                    'is_freespin' => false,
                    'base_amount' => 0,
                    'created' => $bet_one['created'],
                    'is_cashback' => false,
                    'slot_win_lines' => [],
                    'login' => implode('-', $a),
                    'office_id' => $bet_one['office_id'],
                    'user_id' => $bet_one['user_id'],
                    'time' => time(),
                    'action' => 'bet',
                ];

                $all_bets += $bets;

                try {
                    $result = $api->bet($bet_one['uname'], $url, $params, true); //already repeat
                } catch (Exception $e) {
                    $result=false;
                    Kohana::$log->writeException($e);
                }

                if($result) {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set sended = 1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }
                else {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set try = try+1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }

            }
        }

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

    public function action_moonloosebetssoftgamings() {
        $keyProcess='moonloosebetssoftgamingsFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("moonloosebetssoftgamings LOCKED!");
            return null;
        }

        $games=[];

        $om = new Model_Office();

        $offices = $om
            ->where('seamlesstype','=',1)
            ->where('enable_moon_dispatch','=',1)
            ->where('apienable','=',1)
            ->where('apitype','=',8)
            ->find_all();


        $all_bets=[];

        foreach($offices as $o) {
            //отправлять по нарастающей. try=1 -> минута, try=2 -> 4 минуты и тд
            $bets = db::query(1,'select m.*,u.name as uname from moon_dispatch_bets m join users u on u.id=m.user_id where m.sended=0 and m.try<6 and m.office_id=:o_id')
                ->param(':o_id',$o->id)
                ->execute()
                ->as_array();

            if(!count($bets)) {
                continue;
            }

            $api = gameapi::instance(8);
            $url = $api->setUpEnv($o->is_test);

            foreach($bets as $bet_one) {

                if(!isset($games[$bet_one['game']])) {
                    $games[$bet_one['game']]=new Model_Game(['name'=>$bet_one['game']]);
                }

                $a = explode('-',$bet_one['uname']);

                array_pop($a);

                $params = [
                    'amount' => 0,
                    'fs_amount' => 0,
                    'win' => 0,
                    'game' => $games[$bet_one['game']]->name,
                    'game_id' => $games[$bet_one['game']]->id,
                    'game_type' => $games[$bet_one['game']]->type,
                    'bet_type' => 'normal',
                    'bettype' => 'normal',
                    'bet_id' => 0,
                    'initial_id' => $bet_one['initial_id'],
                    'come' => 0,
                    'result' => $bet_one['rate'],
                    'is_freespin' => false,
                    'base_amount' => 0,
                    'created' => $bet_one['created'],
                    'is_cashback' => false,
                    'slot_win_lines' => [],
                    'login' => implode('-', $a),
                    'office_id' => $bet_one['office_id'],
                    'user_id' => $bet_one['user_id'],
                    'time' => time(),
                    'action' => 'bet',
                ];

                $all_bets += $bets;

                try {
                    $result = $api->bet($bet_one['uname'], $url, $params, true); //already repeat
                } catch (Exception $e) {
                    $result=false;
                    Kohana::$log->writeException($e);
                }

                if($result) {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set sended = 1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }
                else {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set try = try+1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }

            }
        }

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

	public function action_moonloosebetstvbet() {
        $keyProcess='moonloosebetstvbetFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("moonloosebetstvbet LOCKED!");
            return null;
        }

        $games=[];

        $om = new Model_Office();

        $offices = $om
            ->where('seamlesstype','=',1)
            ->where('enable_moon_dispatch','=',1)
            ->where('apienable','=',1)
            ->where('apitype','=',11)
            ->find_all();


        $all_bets=[];

        foreach($offices as $o) {
            //отправлять по нарастающей. try=1 -> минута, try=2 -> 4 минуты и тд
            $bets = db::query(1,'select m.*,u.name as uname from moon_dispatch_bets m join users u on u.id=m.user_id where m.sended=0 and m.try<6 and m.office_id=:o_id')
                ->param(':o_id',$o->id)
                ->execute()
                ->as_array();

            if(!count($bets)) {
                continue;
            }

            $api = gameapi::instance(11);
            $url = $api->setUpEnv($o->is_test);

            foreach($bets as $bet_one) {

                if(!isset($games[$bet_one['game']])) {
                    $games[$bet_one['game']]=new Model_Game(['name'=>$bet_one['game']]);
                }

                $a = explode('-',$bet_one['uname']);

                array_pop($a);

                $params = [
                    'amount' => 0,
                    'fs_amount' => 0,
                    'win' => 0,
                    'game' => $games[$bet_one['game']]->name,
                    'game_id' => $games[$bet_one['game']]->id,
                    'game_type' => $games[$bet_one['game']]->type,
                    'bet_type' => 'normal',
                    'bettype' => 'normal',
                    'bet_id' => 0,
                    'initial_id' => $bet_one['initial_id'],
                    'come' => 0,
                    'result' => $bet_one['rate'],
                    'is_freespin' => false,
                    'base_amount' => 0,
                    'created' => $bet_one['created'],
                    'is_cashback' => false,
                    'slot_win_lines' => [],
                    'login' => implode('-', $a),
                    'office_id' => $bet_one['office_id'],
                    'user_id' => $bet_one['user_id'],
                    'time' => time(),
                    'action' => 'bet',
                ];

                $all_bets += $bets;

                try {
                    $result = $api->bet($bet_one['uname'], $url, $params, true); //already repeat
                } catch (Exception $e) {
                    $result=false;
                    Kohana::$log->writeException($e);
                }

                if($result) {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set sended = 1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }
                else {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set try = try+1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }

            }
        }

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

    public function action_moonloosebetsevenbet() {
        $keyProcess='moonloosebetsevenbetFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("moonloosebetsevenbet LOCKED!");
            return null;
        }

        $games=[];

        $om = new Model_Office();

        $offices = $om
            ->where('seamlesstype','=',1)
            ->where('enable_moon_dispatch','=',1)
            ->where('apienable','=',1)
            ->where('apitype','=',7)
            ->find_all();


        $all_bets=[];

        foreach($offices as $o) {
            //отправлять по нарастающей. try=1 -> минута, try=2 -> 4 минуты и тд
            $bets = db::query(1,'select m.*,u.name as uname from moon_dispatch_bets m join users u on u.id=m.user_id where m.sended=0 and m.try<6 and m.office_id=:o_id')
                ->param(':o_id',$o->id)
                ->execute()
                ->as_array();

            if(!count($bets)) {
                continue;
            }

            $api = gameapi::instance(7);
            $url = $api->setUpEnv($o->is_test);
			
			$url=$o->gameapiurl;
            $api->forceURL($url);

            foreach($bets as $bet_one) {

                if(!isset($games[$bet_one['game']])) {
                    $games[$bet_one['game']]=new Model_Game(['name'=>$bet_one['game']]);
                }

                $a = explode('-',$bet_one['uname']);

                array_pop($a);

                $params = [
                    'amount' => 0,
                    'fs_amount' => 0,
                    'win' => 0,
                    'game' => $games[$bet_one['game']]->name,
                    'game_id' => $games[$bet_one['game']]->id,
                    'game_type' => $games[$bet_one['game']]->type,
                    'bet_type' => 'normal',
                    'bet_id' => 0,
                    'initial_id' => $bet_one['initial_id'],
                    'come' => 0,
                    'result' => $bet_one['rate'],
                    'is_freespin' => false,
                    'base_amount' => 0,
                    'created' => $bet_one['created'],
                    'is_cashback' => false,
                    'slot_win_lines' => [],
                    'login' => implode('-', $a),
                    'office_id' => $bet_one['office_id'],
                    'user_id' => $bet_one['user_id'],
                    'time' => time(),
                    'action' => 'bet',
                ];

                $all_bets += $bets;

                try {
                    $result = $api->bet($bet_one['uname'], $url, $params, true); //already repeat
                } catch (Exception $e) {
                    $result=false;
                    Kohana::$log->writeException($e);
                }

                if($result) {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set sended = 1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }
                else {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set try = try+1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }

            }
        }

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

    public function action_moonloosebetsematrix() {
        $keyProcess='moonloosebetsematrixFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("moonloosebetsematrix LOCKED!");
            return null;
        }

        $games=[];

        $om = new Model_Office();

        $offices = $om
            ->where('seamlesstype','=',1)
            ->where('enable_moon_dispatch','=',1)
            ->where('apienable','=',1)
            ->where('apitype','=',6)
            ->find_all();


        $all_bets=[];

        foreach($offices as $o) {
            //отправлять по нарастающей. try=1 -> минута, try=2 -> 4 минуты и тд
            $bets = db::query(1,'select m.*,u.name as uname from moon_dispatch_bets m join users u on u.id=m.user_id where m.sended=0 and m.try<6 and m.office_id=:o_id')
                ->param(':o_id',$o->id)
                ->execute()
                ->as_array();

            if(!count($bets)) {
                continue;
            }

            $api = gameapi::instance(6);
            $url = $api->getUrl($o->gameapiurl);

            foreach($bets as $bet_one) {

                if(!isset($games[$bet_one['game']])) {
                    $games[$bet_one['game']]=new Model_Game(['name'=>$bet_one['game']]);
                }

                $a = explode('-',$bet_one['uname']);

                array_pop($a);

                $params = [
                    'amount' => 0,
                    'fs_amount' => 0,
                    'win' => 0,
                    'game' => $games[$bet_one['game']]->name,
                    'game_id' => $games[$bet_one['game']]->id,
                    'game_type' => $games[$bet_one['game']]->type,
                    'bet_type' => 'normal',
                    'bet_id' => 0,
                    'initial_id' => $bet_one['initial_id'],
                    'come' => 0,
                    'result' => $bet_one['rate'],
                    'is_freespin' => false,
                    'base_amount' => 0,
                    'created' => $bet_one['created'],
                    'is_cashback' => false,
                    'slot_win_lines' => [],
                    'login' => implode('-', $a),
                    'office_id' => $bet_one['office_id'],
                    'user_id' => $bet_one['user_id'],
                    'time' => time(),
                    'action' => 'bet',
                ];

                $all_bets += $bets;

                try {
                    $result = $api->bet($bet_one['uname'], $url, $params, true); //already repeat
                } catch (Exception $e) {
                    $result=false;
                    Kohana::$log->writeException($e);
                }

                if($result) {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set sended = 1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }
                else {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set try = try+1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }

            }
        }

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

    public function action_moonloosebetsinfin() {



        $keyProcess='moonloosebetsinfinFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("moonloosebetsinfin LOCKED!");
            return null;
        }

        $games=[];

        $om = new Model_Office();

        $offices = $om
            ->where('seamlesstype','=',1)
            ->where('enable_moon_dispatch','=',1)
            ->where('apienable','=',1)
            ->where('apitype','=',4)
            ->find_all();


        $all_bets=[];

        foreach($offices as $o) {
            //отправлять по нарастающей. try=1 -> минута, try=2 -> 4 минуты и тд
            $bets = db::query(1,'select m.*,u.name as uname from moon_dispatch_bets m join users u on u.id=m.user_id where m.sended=0 and m.try<6 and m.office_id=:o_id')
                ->param(':o_id',$o->id)
                ->execute()
                ->as_array();

            if(!count($bets)) {
                continue;
            }

            $api = gameapi::instance(4);
            $url = $api->getUrl($o->gameapiurl);

            foreach($bets as $bet_one) {

                if(!isset($games[$bet_one['game']])) {
                    $games[$bet_one['game']]=new Model_Game(['name'=>$bet_one['game']]);
                }

                $a = explode('-',$bet_one['uname']);

                array_pop($a);

                $params = [
                    'amount' => 0,
                    'fs_amount' => 0,
                    'win' => 0,
                    'game' => $games[$bet_one['game']]->name,
                    'game_id' => $games[$bet_one['game']]->id,
                    'game_type' => $games[$bet_one['game']]->type,
                    'bet_type' => 'normal',
                    'bet_id' => 0,
                    'initial_id' => $bet_one['initial_id'],
                    'come' => 0,
                    'result' => $bet_one['rate'],
                    'is_freespin' => false,
                    'base_amount' => 0,
                    'created' => $bet_one['created'],
                    'is_cashback' => false,
                    'slot_win_lines' => [],
                    'login' => implode('-', $a),
                    'office_id' => $bet_one['office_id'],
                    'user_id' => $bet_one['user_id'],
                    'time' => time(),
                    'action' => 'bet',
                ];

                $all_bets += $bets;

                try {
                    $result = $api->bet($bet_one['uname'], $url, $params, true); //already repeat
                } catch (Exception $e) {
                    $result=false;
                    Kohana::$log->writeException($e);
                }

                if($result) {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set sended = 1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }
                else {
                    db::query(Database::UPDATE,'update moon_dispatch_bets set try = try+1 where id = :id')
                        ->param(':id',$bet_one['id'])
                        ->execute();
                }
            }
        }

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

    public function action_moonrecheckbets() {

		$keyProcess='moonrecheckbetsFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("moonrecheckbets LOCKED!");
            return null;
        }

        $notresultedrounds=db::query(1,'select * from moon_results where finished is not null and finished > 0 and dispatched=0 and finished<:finished order by id desc limit 10')
            ->param(':finished',time()-Date::MINUTE*5)
            ->execute()
            ->as_array('id');

        foreach($notresultedrounds as $round_id=>$arr) {

            $made_bets=[];
            $missed_bets=[];

            $allbets=db::query(1,'select * from bets where game in :game and created>=:time and come::int4 = '.$round_id.' limit 200')
                ->param(':game',th::getMoonGames())
                ->param(':time',$arr['created'])
                ->execute()
                ->as_array('id');

            if(!empty($allbets)) {
                $dispatched_bets=db::query(1,'select * from moon_dispatch_bets where initial_id in :ids')
                    ->param(':ids',array_keys($allbets))
                    ->execute()->as_array('initial_id');

                foreach($allbets as $betin) {
                    $was_win=false;
                    foreach($allbets as $bet) {
                        if($bet['initial_id']==$betin['id']) {
                            $was_win=true;
                        }
                    }

                    if(empty($betin['initial_id'])) {
                        $made_bets[$betin['id']]=[
                            'id'=>$betin['id'],
                            'user_id'=>$betin['user_id'],
                            'office_id'=>$betin['office_id'],
                        ];
                    }

                    if(!$was_win && office::instance($betin['office_id'])->office()->enable_moon_dispatch==1) {
                        if(empty($betin['initial_id']) && !isset($dispatched_bets[$betin['id']])) {
                            $missed_bets[$betin['id']]=[
                                'id'=>$betin['id'],
                                'user_id'=>$betin['user_id'],
                                'office_id'=>$betin['office_id'],
                                'game'=>$betin['game'],
                            ];
                        }
                    }
                }

                if(!empty($missed_bets)) {
                    Kohana::$log->add(Log::INFO,'moon missed bets: '.print_r($missed_bets,1));
                    $values=[];
                    foreach($missed_bets as $bet) {
                        $values[]='('.implode(',',[$bet['id'],$bet['office_id'],$bet['user_id'],$arr['rate'],$arr['finished']]).',\''.$bet['game'].'\')';
                    }

                    db::query(Database::INSERT,
                        'insert into moon_dispatch_bets(initial_id,office_id,user_id,rate,created,game) values '.implode(',',$values))
                        ->execute();
                }
            }

            db::query(Database::UPDATE,'update moon_results set dispatched=:time where id=:id')
                ->param(':time',time())
                ->param(':id',$round_id)
                ->execute();
        }
		
		echo 'ok';
        th::unlockProcess($keyProcess);
    }
    public function action_moonloosebets() {

        $keyProcess='moonloosebetsFlag';
        if (!th::lockProcess($keyProcess)){
            if(time()-dbredis::instance()->get('lastExecProcess-'.$keyProcess)>Date::MINUTE*5) {
                th::critAlert("moonloosebets LOCKED!");
            }
            return null;
        }

        $time=time();

        do {

            $bets = db::query(1, 'select m.* from moon_dispatch_bets m 
                                join offices o on m.office_id=o.id
                                where 
                                o.seamlesstype=1 and o.enable_moon_dispatch=1 and o.apienable=1 and o.apitype=0 and 
                                m.created<=:time+m.try*20*60 and m.sended=0 and m.try<1 limit 20')
                ->param(':time',$time)
                ->execute()
                ->as_array('id');

            if(!count($bets)) {
                break;
            }

            $betDispatcher = new betDispatcher();

            $betDispatcher->prepareChannels($bets);

            $betDispatcher->processChannels();

            if (!empty($betDispatcher->good_bets)) {
                db::query(Database::UPDATE, 'update moon_dispatch_bets set sended = 1 where id in :ids')
                    ->param(':ids', $betDispatcher->good_bets)
                    ->execute();
            }

            if (!empty($betDispatcher->bad_bets)) {
                //wrong bets send?
                db::query(Database::UPDATE, 'update moon_dispatch_bets set try = try+1 where id in :ids')
                    ->param(':ids', $betDispatcher->bad_bets)
                    ->execute();
            }
        }
        while(count($bets)>0);

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

	public function action_moonloosebetsretry() {

		echo 'start';

        $keyProcess='moonloosebetsretryFlag';
        if (!th::lockProcess($keyProcess)){
            if(time()-dbredis::instance()->get('lastExecProcess-'.$keyProcess)>Date::MINUTE*5) {
                th::critAlert("moonloosebetsretry LOCKED!");
            }
            return null;
        }

        $time=time();

		echo $time;

        do {

            $bets = db::query(1, 'select m.* from moon_dispatch_bets m 
                                join offices o on m.office_id=o.id
                                where 
                                o.seamlesstype=1 and o.enable_moon_dispatch=1 and o.apienable=1 and o.apitype=0 and is_test=0 and 
                                m.created<=:time+m.try*20*60 and m.sended=0 and m.try>=1 and m.try<4 order by try limit 20')
                ->param(':time',$time)
                ->execute()
                ->as_array('id');

            if(!count($bets)) {
                break;
            }

            $betDispatcher = new betDispatcher();

            $betDispatcher->prepareChannels($bets);

            $betDispatcher->processChannels();

            if (!empty($betDispatcher->good_bets)) {
                db::query(Database::UPDATE, 'update moon_dispatch_bets set sended = 1 where id in :ids')
                    ->param(':ids', $betDispatcher->good_bets)
                    ->execute();
            }

            if (!empty($betDispatcher->bad_bets)) {
                //wrong bets send?
                db::query(Database::UPDATE, 'update moon_dispatch_bets set try = try+1 where id in :ids')
                    ->param(':ids', $betDispatcher->bad_bets)
                    ->execute();
            }
        }
        while(count($bets)>0);

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

    public function action_updateeventsgames() {
        $eventModel=new Model_Event();
        $events=$eventModel
            ->where('is_auto_gen','=',1)
            ->where('type','=','progressive')
            ->find_all();

        foreach($events as $event) {
            $event->games_ids=$event->randomGames($event->office_id);
            $event->save();
        }
    }

    public function action_wrongbets() {

        $keyProcess='wrongbetsFlag';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("wrongbets LOCKED!");
            return null;
        }

        $multi = curl_multi_init();
        $channels = array();

        $om = new Model_Office();

        $offices = $om
            ->where('seamlesstype','=',1)
            ->where('apienable','=',1)
            ->where('is_test','=',0) //temp
            ->where('apitype','=',0)
			->where('owner','not in',[
                1089, //NUX
                1154, //POINT PLACE				
                1177, //NOAH
            ])
            ->where('id','not in',[
                5498, //BMP
                5499, //BMP
                5494, //BMP
            ])
            ->find_all();

        $bets = [];
        $bets_to_api = [];

        $time_started=time();

        foreach($offices as $o) {

            if(!isset($bets[$o->id])) {
                $bets[$o->id] = [];
                $bets_to_api[$o->id] = [];
            }

            $g = new gameapi();

            $time=time();

            $bets_one = db::query(1,'select * from wrongbets where office_id=:o_id and processed = 0  limit 50')
                ->param(':o_id',$o->id)
                ->execute()
                ->as_array('bet_id');

            if(!count($bets_one)) {
                continue;
            }

            $bets_one_api = $bets_one;

            foreach($bets_one_api as $bta_id=>$bta_arr) {
                unset($bets_one_api[$bta_id]['method']);
                unset($bets_one_api[$bta_id]['processed']);

                if(isset($bets_one_api[$bta_id]['try'])) {
                    unset($bets_one_api[$bta_id]['try']);
                }

                if(office::instance($bets_one_api[$bta_id]['office_id'])->office()->owner==1023) {
                    unset($bets_one_api[$bta_id]['login']);
                }

                switch($bta_arr['type']) {
                    case 'normal':
                    case 'norcfs':
                    case 'norlfs':
                    case 'norafs':
                    case 'normfs':
                        $bettype = 'normal';
                        break;
                    case 'double':
                    case 'doucfs':
                    case 'doulfs':
                    case 'douafs':
                    case 'doumfs':
                        $bettype = 'double';
                        break;
                    case 'free':
                    case 'frecfs':
                    case 'frelfs':
                    case 'freafs':
                    case 'fremfs':
                        $bettype = 'free';
                        break;
                    default:
                        $bettype=$bta_arr['type'];
                }

                $fin=1;
                $round_num=$bta_arr['bet_id'];

                if(isset($bta_arr['poker_bet_id']) && $bta_arr['poker_bet_id']>0) {
                    $fin=1;
                    $round_num=$bta_arr['poker_bet_id'];
                }
                elseif(isset($params['initial_id']) && $bta_arr['initial_id']>0) {
                    $fin=1;
                    $round_num=$bta_arr['initial_id'];
                }
                elseif(th::isMoonGame($bta_arr['game'])) {
                    $fin=0;
                }
                elseif(in_array($bta_arr['game'],['tensorbetter','jacksorbetter','acesandfaces']) && $bettype=='normal') {
                    $fin=0;
                }

                $bets_one_api[$bta_id]['finished']=$fin;
                $bets_one_api[$bta_id]['is_cashback']=($bets_one_api[$bta_id]['is_freespin']=='1');
                $bets_one_api[$bta_id]['is_freespin']=(bool) $bets_one_api[$bta_id]['is_freespin'];
                $bets_one_api[$bta_id]['round_num']=$round_num;
                $bets_one_api[$bta_id]['base_amount']=$bets_one_api[$bta_id]['amount']??0;
                $bets_one_api[$bta_id]['fs_amount']=($bets_one_api[$bta_id]['amount']??0);

                $bets_one_api[$bta_id]['type']=$bettype;
            }

            $bets_to_api[$o->id] = $bets[$o->id] + $bets_one_api;
            $bets[$o->id] = $bets[$o->id] + $bets_one;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_ENCODING, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36');
            curl_setopt($ch, CURLOPT_COOKIEFILE, DOCROOT."cookie.txt");
            curl_setopt($ch, CURLOPT_COOKIEJAR, DOCROOT."cookie.txt");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

            $data=[];
            $data['bets']=array_keys($bets[$o->id]);
            $data['full_bets']= json_encode($bets_to_api[$o->id],JSON_FORCE_OBJECT);
            $data['time']=$time;
            $data['action']='wrongbets';
            $data['sign']=$o->sign([
                'time'=>$time,
                'office_id'=>$o->id,
            ]);

            curl_setopt($ch, CURLOPT_URL,$o->gameapiurl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            curl_multi_add_handle($multi, $ch);

            $channels[$o->id] = $ch;

            logfile::create(date('Y-m-d H:i:s')." REQUEST CRON[$time_started]: ". "\n" . $o->id . "\n".'DATA: '. "\n". json_encode($data). "\n",'wronbets');

        }

        //running the requests
        $running = null;
        do {
            curl_multi_exec($multi, $running);
        } while ($running);

        //getting the responses
        foreach(array_keys($channels) as $key){
            $error = curl_error($channels[$key]);
            $last_effective_URL = curl_getinfo($channels[$key], CURLINFO_EFFECTIVE_URL);
            $time = curl_getinfo($channels[$key], CURLINFO_TOTAL_TIME);
            $response = curl_multi_getcontent($channels[$key]);  // get results
            if (!empty($error)) {
                echo "The request $key return a error: $error" . "\n";
            }
            else {

                $res=false;
                logfile::create(date('Y-m-d H:i:s')." REQUEST CRON[$time_started]: ". "\n" . $last_effective_URL . "\n".'RESPONSE: '. "\n".$response. "\n",'wronbets');
                $jr = json_decode($response,1);
                if(!$jr) {
                    //todo log
                }
                else if(!isset($jr['error']) || $jr['error']!='0') {
                    //todo log
                }
                else if(!isset($jr['bets']) || empty($jr['bets'])) {
                    //todo log
                }
                else {
                    $res=true;
                }

                if($res) {
                    $good_bets=[];
                    $all_bets=[];
                    foreach($jr['bets'] as $b_id=>$status) {

                        if(!isset($bets[$key][$b_id])) {
                            //what todo?
                            continue;
                        }

                        //если у них нет, то и нам не надо
                        if(((int) $status)==0) {
                            $all_bets[]=$b_id;
                            continue;
                        }

                        auth::$user_id = $bets[$key][$b_id]['user_id'];
                        $bets[$key][$b_id]['game_type']='agt';
                        $bets[$key][$b_id]['game_name']=$bets[$key][$b_id]['game'];
                        $bets[$key][$b_id]['can_jp']=false;
                        $bets[$key][$b_id]['send_api']=true;

                        /*
                            So "wrongbets" status 1 is returned when bet has been resolved (bet updated with winnings and event finished).
                            status 0 is returned when we cannot find provided bet,
                        */

                        if($status==1) {
                            $good_bets[]=$b_id;
                            $bets[$key][$b_id]['send_api']=false;
                        }

                        $ready=true;

                        try
                        {
                            bet::make($bets[$key][$b_id],$bets[$key][$b_id]['type'],[],true);
                            if(th::isMoonGame($bets[$key][$b_id]['game']) && $bets[$key][$b_id]['initial_id']>0) {
                                game_moon_agt::updateUserBetHistory($bets[$key][$b_id]['user_id'],$bets[$key][$b_id]);
                            }
                        }
                        catch(Exception $ex)
                        {
                            logfile::create(date('Y-m-d H:i:s')." ERROR BET!!!!!: ".$ex->getMessage(). "\n".$ex->getTraceAsString(),'wrongbets');
                            $ready=false;
                        }

                        if($ready) {
                            $all_bets[]=$b_id;
                        }
                    }

                    if(!empty($all_bets)) {
                        db::query(Database::UPDATE,'update wrongbets set processed = 1 where office_id=:o_id and bet_id in :b_id')
                            ->param(':o_id',$key)
                            ->param(':b_id',$all_bets)
                            ->execute();
                    }
                }

                echo "The request to '$last_effective_URL' returned '$response' in $time seconds." . "\n";
            }

            curl_multi_remove_handle($multi, $channels[$key]);
        }

        curl_multi_close($multi);

        echo 'ok';
        th::unlockProcess($keyProcess);
    }

    public function action_agtgames() {

        if(PROJECT==2) {

            $api = new api_agt(1);
            $games = $api->gamelist();

            /*db::query(database::DELETE,"delete from games where brand='agt'")
                    ->execute();*/

            foreach($games as $s) {
                $g = new Model_Game(['external_id'=>$s['game_id']]);

                if(!$g->loaded()) {

                }

                $g->name = $s['name'];
                $g->visible_name = $s['visible_name'];
                $g->provider = 'agt';
                $g->type = 'slots';
                $g->brand = 'agt';
                $g->image = $s['image'];
                $g->external_id = $s['game_id'];
                $g->show = '1';
                $g->tech_type = 'h';
                $g->category = $s['category'];
                $g->demo = 0;
                $g->mobile = 0;

                $g->save();
            }
        }
    }
    public function action_checkbets() {

        $date = date('Y').'_'.date('m').'_'.date('d');

        $t = Database::instance()->list_tables('bets%'.$date);

        if(empty($t)) {
            $date = date('Y',strtotime("-1 day")).'_'.date('m',strtotime("-1 day")).'_'.date('d',strtotime("-1 day"));
            $table_name = 'bets_'.$date;
        }
        else {
            $table_name = $t[0];
        }

        $sql='select b.created as created from :name b join offices o on o.id = b.office_id where o.is_test=0 order by b.created desc limit 1';


        $b = db::query(1,$sql)
            ->param(':name',DB::expr($table_name))
            ->execute()
            ->as_array();


        if(empty($b) || $b[0]['created']==null) {
            th::ceoAlert('!no bets');
            return;
        }

        $curr = Cache::instance()->get('checkbets_level_agt',0);
        $last = Cache::instance()->get('checkbets_level_tg_agt',0);


        if(time()<=($b[0]['created']+(10*Date::MINUTE))) {
            $curr=0;
        }
        elseif($curr<3 && (time()>($b[0]['created']+($curr*10*Date::MINUTE)))) {
            $curr++;
        }
        elseif($curr<6 && ((time()-Date::HOUR)>$b[0]['created'])) {
            $curr++;
        }
        else {

        }

        Cache::instance()->set('checkbets_level_agt',$curr);

        if($curr>0) {
            $k=$curr*10*Date::MINUTE;
            if($curr>=3) {
                $k=Date::HOUR;
            }

            if($curr>=6) {
                $k=Date::DAY;
            }

            if($last<=time()-$k) {
                th::ceoAlert('no movement');
                Cache::instance()->set('checkbets_level_tg_agt',time());
            }
        }
    }

    public function action_checkbets1075() {

        $date = date('Y').'_'.date('m').'_'.date('d');

        $t = Database::instance()->list_tables('bets%'.$date);

        if(empty($t)) {
            $date = date('Y',strtotime("-1 day")).'_'.date('m',strtotime("-1 day")).'_'.date('d',strtotime("-1 day"));
            $table_name = 'bets_'.$date;
        }
        else {
            $table_name = $t[0];
        }

        $sql='select b.created as created from :name b where b.office_id in (6623,6637,6625) order by b.created desc limit 1';

        $b = db::query(1,$sql)
            ->param(':name',DB::expr($table_name))
            ->execute()
            ->as_array();


        if(empty($b) || $b[0]['created']==null) {
            th::ceoAlert('! no bets 6623,6637,6625');
            return;
        }

        $curr = Cache::instance()->get('checkbets_level_agt1075',0);
        $last = Cache::instance()->get('checkbets_level_tg_agt1075',0);


        if(time()<=($b[0]['created']+(10*Date::MINUTE))) {
            $curr=0;
        }
        elseif($curr<3 && (time()>($b[0]['created']+($curr*10*Date::MINUTE)))) {
            $curr++;
        }
        elseif($curr<6 && ((time()-Date::HOUR)>$b[0]['created'])) {
            $curr++;
        }
        else {

        }

        Cache::instance()->set('checkbets_level_agt1075',$curr);

        if($curr>0) {
            $k=$curr*10*Date::MINUTE;
            if($curr>=3) {
                $k=Date::HOUR;
            }

            if($curr>=6) {
                $k=Date::DAY;
            }

            if($last<=time()-$k) {
                th::ceoAlert('no movement 6623,6637,6625');
                Cache::instance()->set('checkbets_level_tg_agt1075',time());
            }
        }
    }

    public function action_checkbets1038() {

        $date = date('Y').'_'.date('m').'_'.date('d');

        $t = Database::instance()->list_tables('bets%'.$date);

        if(empty($t)) {
            $date = date('Y',strtotime("-1 day")).'_'.date('m',strtotime("-1 day")).'_'.date('d',strtotime("-1 day"));
            $table_name = 'bets_'.$date;
        }
        else {
            $table_name = $t[0];
        }

        $sql='select b.created as created from :name b where b.office_id=1038 order by b.created desc limit 1';

        $b = db::query(1,$sql)
            ->param(':name',DB::expr($table_name))
            ->execute()
            ->as_array();


        if(empty($b) || $b[0]['created']==null) {
            th::ceoAlert('! no bets 1038');
            return;
        }

        $curr = Cache::instance()->get('checkbets_level_agt1038',0);
        $last = Cache::instance()->get('checkbets_level_tg_agt1038',0);


        if(time()<=($b[0]['created']+(10*Date::MINUTE))) {
            $curr=0;
        }
        elseif($curr<3 && (time()>($b[0]['created']+($curr*10*Date::MINUTE)))) {
            $curr++;
        }
        elseif($curr<6 && ((time()-Date::HOUR)>$b[0]['created'])) {
            $curr++;
        }
        else {

        }

        Cache::instance()->set('checkbets_level_agt1038',$curr);

        if($curr>0) {
            $k=$curr*10*Date::MINUTE;
            if($curr>=3) {
                $k=Date::HOUR;
            }

            if($curr>=6) {
                $k=Date::DAY;
            }

            if($last<=time()-$k) {
                th::ceoAlert('no movement 1038');
                Cache::instance()->set('checkbets_level_tg_agt1038',time());
            }
        }
    }

    public function action_payeerout(){

        //Блокируем параллельные процессы
        $payments=new Model_Payment;
        $payments=$payments->where('status','=',PAY_APPROVED)
            ->where('gateway','=','payeer')
            ->find_all();
        $log=new logfile();
        $payeer = new CPayeer();
        $fields=new Model_Payment_Field;
        foreach($payments as $p){
            $log->payeer="\r\n".th::date()." start payment with id {$p->id} amount {$p->amount}";
            //меняем статус на 1 - в процессе
            $p->status=PAY_BEGIN;
            $p->save();

            //дополнительные данные платежа
            $data=json_decode($p->data);
            $data=th::ObjectToArray($data);

            //пытаемся выводить деньги
            if (!$payeer->isAuth()){
                $log->payeer="Cannot authorise in payeer";
                $log->payeer=$payeer->getErrors();
                return null;
            }

            $user = new Model_User($p->user_id);

            $a=[
                'ps'=>$p->payment_system_id,
                'curIn' => $user->office->currency->code,
                'sumOut' => abs($p->amount),
                'curOut' => $user->office->currency->code,
            ];

            foreach ($fields->where('payment_system_id','=',$p->payment_system_id)->find_all() as $f){
                if (!isset($data[$f->name])){
                    $log->payeer="Not found data {$f->name} in payment data";
                    continue;
                }
                $a["param_{$f->name}"]=$data[$f->name];
            }



            if (!$payeer->initOutput($a)){
                $log->payeer="Cannot registered payment in payeer";
                $log->payeer=$payeer->getErrors();
                continue;
            }

            $id=$payeer->output();

            if (!($id>0)){
                $log->payeer="Cannot pay payment in payeer";
                $log->payeer=$payeer->getErrors();
            }

            $p->external_id=$id;
            $p->payed=time();
            $p->status=PAY_SUCCES;
            $p->save();
            $log->payeer='Payment pay successfully';

        }


    }


    public function action_interkassaout(){

        //Блокируем параллельные процессы
        $payments=new Model_Payment;
        $payments=$payments->where('status','=',10)
            ->where('provider','=','interkassa')
            ->find_all();
        $log=new logfile();
        $api = new interkassa();
        $fields=new Model_Payment_Field;


        foreach($payments as $p){
            $u = new Model_User($p->user_id);
            $log->interkassa="\r\n".th::date()." start payment with id {$p->id} amount " . $p->amount/$u->office->currency_coeff;
            //меняем статус на 1 - в процессе
            $p->status=PAY_BEGIN;
            $p->save();

            //дополнительные данные платежа
            $data=json_decode($p->data);
            $data=th::ObjectToArray($data);

            $a=[
                'action'=> interkassa::ACTION_CALC, //только расчет. для выплаты надо "process"
                'calcKey'=> interkassa::CALC_PAYEE_AMOUNT,
                'amount' => abs($p->amount/$u->office->currency_coeff),
                'paywayId'=>$p->payment_system_id,
                'purseId' => (string) Kohana::$config->load('secret.interkassa.purse_id'), //TODO непонятно с кошельками. 1 будет или нет. список кошельков с их балансами - $api->getPurseList()
                'paymentNo'=>$p->id,
                'details'=>[],
            ];

            foreach ($fields->where('payment_system_id','=',$p->payment_system_id)->find_all() as $f){
                if (!isset($data[$f->name])){
                    $log->interkassa="Not found data {$f->name} in payment data";
                    continue;
                }
                $a['details'][$f->name]=$data[$f->name];
            }

            if(!$api->createWithdraw($a)) {
                $log->interkassa="Cannot registered payment in interkassa";
                $log->interkassa=print_r($api->getErrors(), TRUE);
                continue;
            }

            $a['action'] = interkassa::ACTION_PROCESS;

            $result=$api->createWithdraw($a);

            if (!$result || !isset($result['id'])){
                $log->interkassa="Cannot pay payment in interkassa";
                $log->interkassa=print_r($api->getErrors(), TRUE);
                continue;
            }

            $p->external_id=$result['id'];
            $p->payed=time();
            $p->status=PAY_SUCCES;
            $p->save();
            $log->interkassa='Payment pay successfully';

        }
    }

    public function action_piastrixout(){

        $status = new Model_Status('piastrixout');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = time();
            $status->id='piastrixout';
            $status->type='second';
            $status->save()->reload();
        }

        if(time() - $status->last < 2) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        //Блокируем параллельные процессы
        $payments=new Model_Payment;
        $payments=$payments->where('status','=',PAY_APPROVED)
            ->where('gateway','=','piastrix')
            ->limit(1)
            ->find_all();
        $log=new logfile();
        $api = new piastrix();
        $fields=new Model_Payment_Field;


        foreach($payments as $p){
            $u = new Model_User($p->user_id);
            $log->piastrix="\r\n".th::date()." start payment with id {$p->id} amount ". $p->amount;
            //меняем статус на 1 - в процессе
            $p->status=PAY_BEGIN;
            $p->save();

            //дополнительные данные платежа
            $data=json_decode($p->data);
            $data=th::ObjectToArray($data);

            $api->addParam('amount',abs($p->amount))
                ->addParam('amount_type','ps_amount')
                ->addParam('payway',$p->ps);

            $api->addParam('shop_currency',$u->office->currency->iso_4217);


            if(!$result = $api->prewithdraw()) {
                $log->piastrix="Cannot prewithdraw ".$p->id;
                $log->piastrix=print_r($api->getErrors(), TRUE);
                continue;
            }

            $api->addParam('amount',abs($p->amount))
                ->addParam('amount_type','ps_amount')
                ->addParam('shop_payment_id',$p->id)
                ->addParam('payway',$p->ps);

            $api->addParam('shop_currency',$u->office->currency->iso_4217);

            foreach ($fields->where('payment_system_id','=',$p->payment_system_id)->find_all() as $f){
                if (!isset($data[$f->name])){
                    $log->piastrix="Not found data {$f->name} in payment data";
                    continue;
                }
                $api->addParam($f->name,$data[$f->name]);
            }

            $result = $api->withdraw();

            if(!$result) {
                $log->piastrix="Cannot registered payment in piastrix. ".$p->id;
                $log->piastrix=print_r($api->getErrors(), TRUE);
                continue;
            }

            $p->external_id=$result['id'];
            //комиссия списанная платежкой
            $p->total_commission = $result['shop_write_off'] - $result['payee_receive'];
            $p->payed=time();
            $p->save();
            $log->piastrix='Payment pay successfully';

        }

        $status->last = time();
        $status->value = 0;
        $status->save();

    }

    public function action_freeobmoneyout(){

        $status = new Model_Status('freeobmoneyout');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = time();
            $status->id='freeobmoneyout';
            $status->type='second';
            $status->save()->reload();
        }

        if(time() - $status->last < 2) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        //Блокируем параллельные процессы
        $payments=new Model_Payment;
        $payments=$payments->where('status','=',PAY_APPROVED)
            ->where('gateway','=','freeob')
            ->limit(1)
            ->find_all();
        $log=new logfile();
        $api = new freeobmen();
        $fields=new Model_Payment_Field;

        $fff=[];

        foreach($payments as $p){

            $u = new Model_User($p->user_id);
            $log->freeob="\r\n".th::date()." start payment with id {$p->id} amount ". $p->amount;
            //меняем статус на 1 - в процессе
            $p->status=PAY_BEGIN;
            $p->save();

            //дополнительные данные платежа
            $data=json_decode($p->data);
            $data=th::ObjectToArray($data);

            foreach ($fields->where('payment_system_id','=',$p->payment_system_id)->find_all() as $f){
                if (!isset($data[$f->name])){
                    $log->freeob="Not found data {$f->name} in payment data";
                    continue;
                }
                $fff[$f->name]=$data[$f->name];
            }

            $result = $api->out(abs($p->amount),$p->ps,$p->currency);


            if(!$result) {
                $log->freeob="Cannot registered payment in freeob. ".$p->id;
                $log->freeob=print_r($api->getErrors(), TRUE);
                continue;
            }

            $p->external_id=$result['paymentId'];
            //комиссия списанная платежкой
            $p->payed=time();
            $p->save();
            $log->freeob='Payment pay successfully';

        }

        $status->last = time();
        $status->value = 0;
        $status->save();

    }
    public function action_ex4moneyout(){

        $status = new Model_Status('ex4moneyout');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = time();
            $status->id='ex4moneyout';
            $status->type='second';
            $status->save()->reload();
        }

        if(time() - $status->last < 2) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        //Блокируем параллельные процессы
        $payments=new Model_Payment;
        $payments=$payments->where('status','=',PAY_APPROVED)
            ->where('gateway','=','ex4money')
            ->limit(1)
            ->find_all();
        $log=new logfile();
        $api = new ex4money();
        $fields=new Model_Payment_Field;

        $fff=[];

        foreach($payments as $p){

            $u = new Model_User($p->user_id);
            $log->ex4money="\r\n".th::date()." start payment with id {$p->id} amount ". $p->amount;
            //меняем статус на 1 - в процессе
            $p->status=PAY_BEGIN;
            $p->save();

            //дополнительные данные платежа
            $data=json_decode($p->data);
            $data=th::ObjectToArray($data);

            foreach ($fields->where('payment_system_id','=',$p->payment_system_id)->find_all() as $f){
                if (!isset($data[$f->name])){
                    $log->ex4money="Not found data {$f->name} in payment data";
                    continue;
                }
                $fff[$f->name]=$data[$f->name];
            }

            $result = $api->out(abs($p->amount),$p->id,$p->ps,$fff);


            if(!$result) {
                $log->ex4money="Cannot registered payment in ex4money. ".$p->id;
                $log->ex4money=print_r($api->getErrors(), TRUE);
                continue;
            }

            $p->external_id=$result['payment_id'];
            //комиссия списанная платежкой
            $p->payed=time();
            $p->save();
            $log->ex4money='Payment pay successfully';

        }

        $status->last = time();
        $status->value = 0;
        $status->save();

    }

    public function action_trioout(){

        $status = new Model_Status('trioout');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = time();
            $status->id='trioout';
            $status->type='second';
            $status->save()->reload();
        }

        if(time() - $status->last < 2) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        //Блокируем параллельные процессы
        $payments=new Model_Payment;
        $payments=$payments->where('status','=',PAY_APPROVED)
            ->where('gateway','=','trio')
            ->limit(1)
            ->find_all();
        $log=new logfile();
        $api = new trio();
        $fields=new Model_Payment_Field;


        foreach($payments as $p){
            $u = new Model_User($p->user_id);
            $log->trio="\r\n".th::date()." start payment with id {$p->id} amount ". $p->amount/$u->office->currency_coeff;
            //меняем статус на 1 - в процессе
            $p->status=PAY_BEGIN;
            $p->save();

            //дополнительные данные платежа
            $data=json_decode($p->data);
            $data=th::ObjectToArray($data);

            $api->addParam('amount',abs($p->amount/$u->office->currency_coeff))
                ->addParam('amount_type','ps_amount')
                ->addParam('payway',$p->ps);

            if(!$result = $api->prewithdraw()) {
                $log->trio="Cannot prewithdraw ".$p->id;
                $log->trio=print_r($api->getErrors(), TRUE);
                continue;
            }

            $api->addParam('amount',abs($p->amount/$u->office->currency_coeff))
                ->addParam('amount_type','ps_amount')
                ->addParam('payment_id',$p->id)
                ->addParam('payway',$p->ps);

            foreach ($fields->where('payment_system_id','=',$p->payment_system_id)->find_all() as $f){
                if (!isset($data[$f->name])){
                    $log->trio="Not found data {$f->name} in payment data";
                    continue;
                }
                $api->addParam($f->name,$data[$f->name]);
            }

            $result = $api->withdraw();

            if(!$result) {
                $log->trio="Cannot registered payment in trio. ".$p->id;
                $log->trio=print_r($api->getErrors(), TRUE);
                continue;
            }

            $p->external_id=$result['id'];
            $p->payed=time();
            $p->save();
            $log->trio='Payment pay successfully';

        }

        $status->last = time();
        $status->value = 0;
        $status->save();

    }

    public function action_freekassaout(){

        //Блокируем параллельные процессы
        $payments=new Model_Payment;
        $payments=$payments->where('status','=',PAY_APPROVED)
            ->where('gateway','=','freekassa')
            ->find_all();
        $log=new logfile();
        $api = new freekassa();

        $config = Kohana::$config->load('secret.freekassa');

        foreach($payments as $p){
            $u = new Model_User($p->user_id);
            $log->freekassa="\r\n".th::date()." start payment with id {$p->id} amount " . $p->amount/$u->office->currency_coeff;
            //меняем статус на 1 - в процессе
            $p->status=PAY_BEGIN;
            $p->save();

            //дополнительные данные платежа
            $data=json_decode($p->data);
            $data=th::ObjectToArray($data);

            $a=[];

            $a['wallet_id'] = $config['wallet_id'];
            $a['purse'] = $data['client']??$data['phone']; //TODO только для карт. если понадобится для другого - надо переделать
            $a['amount'] = round(abs($p->amount/$u->office->currency_coeff),2);
            $a['desc'] = $p->id;
            $a['currency'] = $p->currency;
            $a['sign'] = md5($a['wallet_id'].$a['currency'].$a['amount'].$a['purse'].$config['api_key']);
            $a['action'] = 'cashout';

            $result = $api->createWithdraw($a);


            if (!$result || !isset($result['payment_id']) || !$result['payment_id']){
                $log->freekassa="Cannot pay payment in freekassa";
                $log->freekassa=json_encode($result);
                continue;
            }

            $p->external_id=$result['payment_id'];
            $p->save();
            $log->freekassa='Payment pay successfully';

        }


    }

    public function action_updateImperium() {
        $api = new Api_Imperiumgames();
        $gameList = $api->gameList();

        $games = [];

        foreach ($gameList as $value) {
            foreach ($value as $game) {
                $games[] = $game['id'];
            }
        }

        database::instance()->begin();
        try {
            $sql_update =<<<SQL
                    update imperium_games set enabled = 0 where enabled = 1;
                    update imperium_games set enabled = 1 where game_id in :ids;
SQL;
            db::query(database::UPDATE, $sql_update)->param(':ids', $games)->execute();
            database::instance()->commit();
        } catch (Database_Exception $e) {
            database::instance()->rollback();
        }
    }

    public function action_calcBonuses() {
        /*
         * выходим если бонусы выключены
         */
        $status = new Model_Status('enable_bia');
        if($status->value == 0) {
            return;
        }
        /*
         * получаем пользователей у которых
         * всключены бонусы
         */

        /*
         * проверяем когда была посл ставка у игрока
         */
        $sql_bets = <<<SQL
            SELECT
                u. ID AS user_id,
                u.last_bet_time AS last_bet,
                u.getspam,
                u.email,
                u.dsrc
            FROM
                users u
            WHERE
                u.blocked = 0
            and
            u.last_game is null
            and u.office_id not in :o_ids
            and
            (extract('epoch' from CURRENT_TIMESTAMP)-u.last_bet_time) > :diff
SQL;

        $diff_last_bet = kohana::$config->load('bonus.diff_last_bet');

        $res_bets = db::query(database::SELECT, $sql_bets)
            ->param(':diff',$diff_last_bet)
            ->param(':o_ids',[777])
            ->execute(null,'Model_User');

        /*
        * время для расчета которое прошло
        * после последней ставки игрока
        */

        foreach ($res_bets as $user) {
            $bonus = new Bonus_Calc($user->user_id);
            $current_bonus = $bonus->go();

            echo $user->user_id.': '.$current_bonus['bonus'].'<br>';

            if($current_bonus['bonus'] > 0 AND $user->getspam) {
                $message = new View('email/activity');
                $message->bonus = round($current_bonus['bonus'],2);
                $message->user = $user;
                $message->is_referal = true;

                Email::stack($user->email, Email::from($user->dsrc), __('Вам начислен бонус'), $message->render(), true, $user->dsrc,1);

                if(isset($current_bonus['invited']['bonus'])) {
                    $user_invited = new Model_User($current_bonus['invited']['user_id']);
                    $message->bonus = round($current_bonus['invited']['bonus'], 2);
                    $message->user = $user_invited;
                    $message->is_referal = false;

                    Email::stack($user_invited->email, Email::from($user_invited->dsrc), __('Вам начислен бонус'), $message->render(), true, $user_invited->dsrc, 1);
                }
            }
        }
    }

    /*
     * запускать 1-го числа каждого месяца
     * рассчитывает текущий уровень игрока
     * с учетом ставок за предыдущий месяц
     */
    public function action_calcUsersLevel() {
        /*
         * проверка запуска в текущем месяце
         */
        $status = new Model_Status('user_lvl');
        if($status->value >= mktime(0, 0, 0, date("n"), 1, date("Y"))) {
            echo 'Рассчет уровней игроков в текущем месяце уже производился';
            return;
        }

        $status->value = time();
        $status->save();

        $sql_user_compoints = <<<SQL
            Select c.user_id, sum(c.comp_accrued)
            From compoints c JOIN users u ON c.user_id = u.id
            Where u.comp_level < 10 AND c.created >= :time_from AND c.created < :time_to
            GROUP BY user_id
SQL;
        $params = [
            ':time_from' => mktime(0, 0, 0, date("n")-1, 1, date("Y")),
            ':time_to' => mktime(0, 0, 0, date("n"), 1, date("Y")),
        ];

        $users_compoints = db::query(database::SELECT, $sql_user_compoints)->parameters($params)->execute()->as_array('user_id', 'sum');

        $sql_users = <<<SQL
            Select id, comp_level
            From users
            Where comp_level < 10
SQL;
        $users = db::query(database::SELECT, $sql_users)->execute()->as_array('id', 'comp_level');

        foreach ($users as $user_id => $user_comp_level) {
            $user = new Model_User($user_id);
            $users_compoints_prev_month = isset($users_compoints[$user_id]) ? $users_compoints[$user_id] : 0;
            /*
             * понижаем уровень игрока, если месячный счетчик компойнтов игрока
             * меньше половины значения порога компоинтов текущего статуса игрока
             */
            if($users_compoints_prev_month < $user->get_compoint_param('levels')/2 AND $user_comp_level != 1) {
                $user->comp_level = $user_comp_level - 1;
                $user->comp_process = $user->get_compoint_param('levels', $user_comp_level - 1);
                $user->save();

                echo 'Уровень игрока '.$user_id . ' понижен до "' . $user->get_compoint_param('names') . '"';
            }
        }

    }

    /*
     * запускать 1-го января
     * рассчитывает текущий уровень игрока
     * с учетом ставок за предыдущий год
     */
    public function action_calcUsersLevelVip() {
        /*
         * проверка запуска в текущем месяце
         */
        $status = new Model_Status('user_lvl_vip');
        if($status->value >= mktime(0, 0, 0, 1, 1, date("Y"))) {
            echo 'Рассчет уровней игроков в текущем году уже производился';
            return;
        }

        $status->value = time();
        $status->save();

        $sql_user_compoints = <<<SQL
            Select c.user_id, sum(c.comp_accrued)
            From compoints c JOIN users u ON c.user_id = u.id
            Where u.comp_level >= 10 AND c.created >= :time_from AND c.created < :time_to
            GROUP BY user_id
SQL;
        $params = [
            ':time_from' => mktime(0, 0, 0, 1, 1, date("Y")-1),
            ':time_to' => mktime(0, 0, 0, 1, 1, date("Y")),
        ];

        $users_compoints = db::query(database::SELECT, $sql_user_compoints)->parameters($params)->execute()->as_array('user_id', 'sum');

        $sql_users = <<<SQL
            Select id, comp_level
            From users
            Where comp_level >= 10
SQL;
        $users = db::query(database::SELECT, $sql_users)->execute()->as_array('id', 'comp_level');

        foreach ($users as $user_id => $user_comp_level) {
            $user = new Model_User($user_id);
            $users_compoints_prev_year = isset($users_compoints[$user_id]) ? $users_compoints[$user_id] : 0;
            /*
             * понижаем уровень игрока, если годовой счетчик компойнтов игрока
             * меньше половины значения порога компоинтов текущего статуса игрока
             */
            if($users_compoints_prev_year < $user->get_compoint_param('levels')/2) {
                $user->comp_level = 9;
                $user->comp_process = $user->get_compoint_param('levels', 9);
                $user->save();

                echo 'Уровень игрока '.$user_id . ' понижен до "' . $user->get_compoint_param('names') . '"';
            }
        }

    }

    //TODO сделать логирование по дням (папки)
    public function action_telegramUpdate() {

        $l = new logfile();
        $start = microtime(1);

        $l->telegramcron = 'start: '.$start.' ['.md5($start).']';

        //TODO сделать в отдельную таблицу
        $status = new Model_Status('telegram_send');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = time();
            $status->id='telegram_send';
            $status->type='second';
            $status->save()->reload();
        }

        if(time() - $status->last < 2) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        $tgbot = new tgbot();
        $tgbot->getUpdates();


        $status->last = time();
        $status->value = 0;
        $status->save();

        $end = microtime(1);

        $l->telegramcron = 'end: '.$end.' ['.md5($start).'] execution time: '.($end - $start);
    }



    public function action_piastrixb() {

        //не запускаем раньше 9
        if(time() < mktime(4,0,0)) {
            return;
        }

        //TODO сделать в отдельную таблицу

        $status = new Model_Status('piastrixb_rep');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = 0;
            $status->id='piastrixb_rep';
            $status->type='dayly';
            $status->save()->reload();
        }

        if(time() - $status->last < 24*60*60) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        report::getPiastrixBalance();

        $status->last = time();
        $status->value = 0;
        $status->save();
    }


    //TODO сделать логирование по дням (папки)
    public function action_forecastreport() {

        //не запускаем раньше 9
        if(time() < mktime(4,0,0)) {
            return;
        }

        //TODO сделать в отдельную таблицу

        $status = new Model_Status('forecast_rep');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = 0;
            $status->id='forecast_rep';
            $status->type='dayly';
            $status->save()->reload();
        }

        if(time() - $status->last < 24*60*60) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        report::daylyForcast();
        report::getPiastrixBalance();

        $status->last = time();
        $status->value = 0;
        $status->save();
    }

    public function action_newsletter() {

        $status = new Model_Status('newsletter');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = 0;
            $status->id='newsletter';
            $status->type='minute';
            $status->save()->reload();
        }

        if(time() - $status->last < 0) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        $letters = ORM::factory('newsletter')
            ->where('need_to_send','<=',time())
            ->where('sended','=',0)
            ->limit(1)
            ->order_by('level','desc')
            ->find_all();

        foreach($letters as $letter) {
            $message = $letter->message;

            $domain = $letter->domain??Dd::get_domain('default', 'short');

            if($letter->html==1) {
                $message = str_replace('</body>',Email::px($letter->id, $domain).'</body>',$message);
            }

            $domain = substr($domain,0,count($domain)-4) .'.com';

            if(Email::send($letter->to,[$letter->from,$domain],$letter->title,$message,(bool) $letter->html)) {
                $letter->sended=time();
            }
            else {
                $letter->sended=-1;
            }
            $letter->save();
        }

        $status->last = time();
        $status->value = 0;
        $status->save();

    }

    public function action_nousedbonuses() {
        $sql_no_payed = <<<SQL
            Select id, user_id, bonus, created, last_notification, accrual_days_ago
            From bonuses
            Where type = 'activity'
                AND payed = 0
SQL;
        $res_no_payed = db::query(1, $sql_no_payed)->execute()->as_array();

        $message = new View('email/activity_remind');

        foreach ($res_no_payed as $no_payed) {
            $days_with_accrual = floor((time() - $no_payed['created'])/(24*60*60));//days

            echo $days_with_accrual . ' - ' . $no_payed['accrual_days_ago'] . '<br>';

            $u = new Model_User($no_payed['user_id']);
            $parent = $u->parent_acc();

            I18n::lang($parent->lang);

            $message->bonus = $no_payed['bonus'];
            $message->days = $days_with_accrual;
            $message->u = $parent;

            if($days_with_accrual == 1 AND $no_payed['accrual_days_ago'] != 1
                OR
                ($days_with_accrual == 2 AND $no_payed['accrual_days_ago'] != 2)
                OR
                ($days_with_accrual == 3 AND $no_payed['accrual_days_ago'] != 3)
                OR
                ($days_with_accrual == 7 AND $no_payed['accrual_days_ago'] != 7)
                OR
                ($days_with_accrual == 30 AND $no_payed['accrual_days_ago'] != 30)
            ) {
                $bonus = new Model_Bonus($no_payed['id']);
                $bonus->last_notification = time();
                $bonus->accrual_days_ago = $days_with_accrual;
                $bonus->save();

                if($tokens = $parent->pushtokens->find_all()) {
                    foreach ($tokens as $data) {
                        $parent->new_message([
                            "user_id" => $parent->id,
                            "push" => 1,
                            "title" => __("ДОРОГОЙ ИГРОК!"),
                            "text" => __('Вам начислен бонус за игровую активность'),
                            "push_link" => '/',
                            "browser" => $data->browser,
                            "push_token" => $data->token,
                        ]);
                    }
                }

                Email::stack($parent->email, Email::from($parent->dsrc), __('Вам начислен бонус'), $message->render(), true, $parent->dsrc, 1);
            }
        }
    }

    public function action_calcshares() {
        $count_tickets = [
            1000 => 1,
            3000 => 4,
            7000 => 10,
            10000 => 16,
            20000 => 40,
            50000 => 150
        ];
        $sql_shares = <<<SQL
            Select id, time_from, time_to
            From shares
            Where enabled = 1
                AND time_to >= :time_to
                AND type = 'lottery'
SQL;
        $res_shares = db::query(1, $sql_shares)->param(':time_to', time())->execute();

        $sql_payments = <<<SQL
            Select id as payment_id, amount, user_id
            From payments
            Where
                status = 30
                AND amount > 0
                AND created >= :time_from
                AND created <= :time_to
SQL;
        foreach ($res_shares as $share) {

            $res_payments_for_share = db::query(1, $sql_payments)->parameters([
                ':time_from' => $share['time_from'],
                ':time_to' => $share['time_to'],
            ])->execute();

            foreach ($res_payments_for_share as $p) {
                $payment_tickets = new Model_Sharetickets([
                    "user_id" => $p['user_id'],
                    "share_id" => $share['id'],
                    "payment_id" => $p['payment_id']
                ]);

                if($payment_tickets->loaded()) {
                    continue;
                }

                $u = new Model_User($p['user_id']);
                $user_count_tickets = 0;
                foreach ($count_tickets as $sum_payment => $count) {
                    if($p['amount'] >= $sum_payment) {
                        $user_count_tickets = $count;
                        if($u->partner == 49) {
                            $user_count_tickets *= 2;
                        }
                    }
                }

                for($i = 1; $i <= $user_count_tickets; $i++) {
                    db::query(2, "insert into share_tickets(user_id, share_id, payment_id) values (:user_id, :share_id, :payment_id)")->parameters([
                        ':user_id' => $p['user_id'],
                        ':share_id' => $share['id'],
                        ':payment_id' => $p['payment_id'],
                    ])->execute();
                }
            }

        }

    }

    //раз в 5 минут

    public function action_checkpayout() {
        $status = new Model_Status('checkpayout');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = time();
            $status->id='checkpayout';
            $status->type='second';
            $status->save()->reload();
        }

        if(time() - $status->last < 60) {
            return;
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();


        $payments=new Model_Payment;
        $payments=$payments->where('status','=',20)
            ->find_all();


        foreach($payments as $p) {
            if($p->provider == 'interkassa') {
//                $i = new interkassa();
//
//                $st = $i->getWithdrawList(['paymentNo'=>$p->id]);

                if(!$p->ext_id) { continue; }

                $u='https://api.interkassa.com/v1/withdraw/'.$p->ext_id;
                $pars=new Parser();
                $g = $pars->get($u,array(), $auth='5dd40ac51ae1bd12008b4568:f3HgvF5LscQ4addgir2CHJQ2NI79RrUh');

                $st = json_decode($g,1);

                if(!$st || empty($st)) {
                    continue;
                }

                //success
                if($st['data']['state']=='8') {

                    if(!$p->commision) {
                        $p->commision=$st['data']['psFeeOut'];
                    }

                    $p->paid=time();
                    $p->status=30;
                    $p->save();
                }

                //fail
                if(in_array($st['data']['state'],['3','9','11'])) {
                    $p->cancel();
                }
            }

            if($p->provider == 'freekassa') {

                if(!$p->external_id) {
                    continue;
                }

                $api = new freekassa();
                $config = Kohana::$config->load('secret.freekassa');

                $a=[];
                $a['wallet_id'] = $config['wallet_id'];
                $a['payment_id'] = $p->external_id;
                $a['sign'] = md5($a['wallet_id'].$a['payment_id'].$config['api_key']);
                $a['action'] = 'get_payment_status';

                $result = $api->outCheck($a);

                if (!$result || !isset($result['payment_id']) || !$result['payment_id']){
                    continue;
                }

                if($result['status']=='Completed') {
                    $p->payed=time();
                    $p->status=PAY_SUCCES;
                    $p->save();
                }

                if($result['status']=='Canceled') {
                    $p->cancel();
                }
            }

            if($p->provider == 'trio') {

                if(!$p->external_id) {
                    continue;
                }

                $config = Kohana::$config->load('secret.trio');
                $api = new trio();
                $api->addParam('withdraw_id',$p->external_id);
                $api->addParam('now',date('Y-m-d H:i:s.s'));
                $result = $api->checkOut();

                if (!$result || !isset($result['processed']) || !$result['processed']){
                    continue;
                }

                if($result['status']==5) {
                    $p->payed=time();
                    $p->status=PAY_SUCCES;
                    $p->save();
                }

                if(in_array($result['status'],[6,8,10])) {
                    $p->cancel();
                }
            }

            if($p->provider == 'piastrix') {

                if(!$p->external_id) {
                    continue;
                }

                $config = Kohana::$config->load('secret.piastrix');
                $api = new piastrix();
                $api->addParam('withdraw_id',$p->external_id);
                $api->addParam('now',date('Y-m-d H:i:s.s'));
                $result = $api->checkOut();

                if (!$result || !isset($result['status']) || !$result['status']){
                    continue;
                }

                if($result['status']==5) {
                    $p->payed=time();
                    $p->status=PAY_SUCCES;
                    $p->save();
                }

                if(in_array($result['status'],[6,10,11])) {
                    $p->cancel();
                }
            }
            if($p->provider == 'freeob') {

                if(!$p->external_id) {
                    continue;
                }

                $config = Kohana::$config->load('secret.freeobmen');
                $api = new freeobmen();
                $result = $api->checkOut($p->external_id);

                if (!$result || !isset($result['status']) || !$result['status']){
                    continue;
                }

                if($result['status']=='success') {
                    $p->payed=time();
                    $p->status=PAY_SUCCES;
                    $p->save();
                }

                if(in_array($result['status'],['fail','wrong-number','process'])) {
                    $p->cancel();
                }
            }

            if($p->provider == 'payeer') {

                $config = Kohana::$config->load('secret.payeer');
                $api = new Cpayeer();

                $result = $api->getHistoryInfo($p->external_id);

                if($result['info']['status']=='execute') {
                    $p->payed=time();
                    $p->status=PAY_SUCCES;
                    $p->save();
                }

                if($result['info']['status']=='cancel') {
                    $p->cancel();
                }
            }
        }

        $status->last = time();
        $status->value = 0;
        $status->save();
    }

    public function action_sendpush() {
        $time_to_live = 3000;
        $status = new Model_Status('pushmessages');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = 0;
            $status->id='pushmessages';
            $status->type='minute';
            $status->save()->reload();
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        $push_messages = ORM::factory('user_message')
            ->where('sended','=',0)
            ->and_where('push', '=', 1)
            ->limit(1)
            ->find_all();

        foreach($push_messages as $message) {
            $subscriber_id = $message->push_token;

            $ch = curl_init();

            switch($message->browser) {
                case 'chrome':
                    $my_key = kohana::$config->load('static.chrome');
                    var_dump($my_key);
                    curl_setopt($ch, CURLOPT_URL, 'https://gcm-http.googleapis.com/gcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: key='.$my_key, 'Content-Type: application/json']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                        json_encode([
                            'registration_ids' => [$subscriber_id],
                            'data' => ["message" => "send"],
                            'time_to_live' => $time_to_live,

                        ])
                    );
                    break;

                case 'firefox':
                    curl_setopt($ch, CURLOPT_URL, 'https://updates.push.services.mozilla.com/wpush/v5/'.$subscriber_id);
                    curl_setopt($ch, CURLOPT_PUT, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['TTL: '.$time_to_live]);
                    break;

            }
            $result = curl_exec($ch);
            curl_close($ch);
            var_dump($result);
            $message->sended = 1;
            $message->save();
        }

        $status->last = time();
        $status->value = 0;
        $status->save();
    }

    public function action_profitTP() {
        $percent_profit = 0.01;

        $days = range(mktime(0, 0, 0, date("n"), date("j")-6), mktime(0, 0, 0), (24*60*60));

        foreach ($days as $d) {

            $time_start = mktime(9, 0, 0, date("n", $d), date("j", $d), date("Y", $d));
            $time_end = $time_start + 24*60*60;

            $data = [];

            $sql_payments = <<<SQL
                Select user_id, sum(amount+bonus), sum(amount) as amount
                From payments
                Where status = 30
                    AND payed >= :time_start
                    AND payed < :time_end
                GROUP BY user_id
SQL;
            $payments = db::query(1, $sql_payments)->parameters([
                ':time_start' => $time_start,
                ':time_end' => $time_end,
            ])->execute()->as_array();

            $users = [];

            foreach ($payments as $p) {
                $users[] = $p['user_id'];

                if(!isset($data[$p['user_id']])){
                    $data[$p['user_id']] = [
                        'payments' => $p['sum'],
                        'amount' => $p['amount'],
                        'bets' => 0
                    ];
                }
            }

            $config_dates = kohana::$config->load('tech.dates');

            $person_id = 0;

            foreach ($config_dates as $id => $dt) {
                $dat = date('d-m-Y', $time_start);
                if(in_array($dat, $dt)) {
                    $person_id = $id;
                }
            }

            if($users) {
                $sql_bets = <<<SQL
                    Select user_id, sum(amount-win)
                    From bets
                    Where user_id in :users
                        AND created >= :time_start
                        AND created < :time_end
                    GROUP BY user_id
SQL;
                $res_bets = db::query(1, $sql_bets)->parameters([
                    ':users' => $users,
                    ':time_start' => $time_start,
                    ':time_end' => $time_end,
                ])->execute()->as_array();

                $user_profit = new Model_User_Profit(['date' => $time_start]);

                if(!$user_profit->loaded()) {
                    $user_profit->date = $time_start;
                    $user_profit->person_id = $person_id;
                }

                $user_profit->amount = 0;
                $user_profit->profit = 0;

                foreach ($res_bets as $v) {
                    $data[$v['user_id']]['bets'] += $v['sum'];

                    $diff = $data[$v['user_id']]['payments'] - ($v['sum'] + 30);

                    if($diff <= 0) {
                        $user_profit->amount += $data[$v['user_id']]['amount'];
                        $user_profit->profit += $data[$v['user_id']]['amount'] * $percent_profit;
                        $user_profit->save();
                    }
                }
            }

            $user_profit = new Model_User_Profit(['date' => $time_start]);

            if(!$user_profit->loaded()) {
                $user_profit->date = $time_start;
                $user_profit->amount = 0;
                $user_profit->profit = 0;
                $user_profit->person_id = $person_id;
            }

            $user_profit->save();
        }
    }

    public function action_sharesend() {


        $status = new Model_Status('sharesend');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = 0;
            $status->id='sharesend';
            $status->type='minute';
            $status->save()->reload();
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        $shares = orm::factory('share')->where('notification', '=', 0)->and_where('ready', '=', 1)->and_where('time_from', '<=', time())->limit(1)->find_all();

        foreach ($shares as $s) {
            if($s->type=='unreg') {
                $s->notification_unreg();
            }
            else {
                $s->notification();
            }
        }


        $status->last = time();
        $status->value = 0;
        $status->save();
    }

    public function action_monitoringcounters() {
        exit;
        $status = new Model_Status('monitoring');

        if(!$status->loaded()) {
            $status->value=0;
            $status->last = 0;
            $status->id='monitoring';
            $status->type='minute';
            $status->save()->reload();
        }

        if($status->value != 0) {
            return;
        }

        $status->last = time();
        $status->value = mt_rand(1000000,9000000);
        $status->save();

        report::counters();

        $status->last = time();
        $status->value = 0;
        $status->save();
    }

    public function action_newImperium() {
        $api = new Api_Imperiumgames();
        $gameList = $api->gameList();
        $games = [];
        $gamesTable = [];
        $oldImperium = db::query(1, 'select game_id from imperium_games')->execute()->as_array('game_id');

        $sql = <<<SQL
            insert into imperium_games(game_id, name, type_system, img, enabled, domain) values
SQL;
        $sqlTable = <<<SQL
            insert into games(name, visible_name, provider, brand, external_id, image, show) values
SQL;

        foreach ($gameList as $value) {
            foreach ($value as $g) {
                if(!isset($oldImperium[$g['id']])) {
                    $label = strtolower($g['label']);
                    $games[] = "({$g['id']},'{$g['name']}','$label','{$g['img']}',1,1)";

                    $short_name = strtolower(str_replace(' ', '', $g['name']));
                    $brand = strtolower(str_replace(['_deluxe','_html5'], '', $g['label']));

//                    $image = file_get_contents($g['img']);
                    $image_name = $g['id'] . '.png';

                    $src = DOCROOT . 'games/imperium/' . $image_name;

//                    file_put_contents($src, $image);

                    $src = '/games/imperium/' . $image_name;

                    $gamesTable[] = "('$short_name', '{$g['name']}', 'imperium', '$brand', {$g['id']}, '$src', 0)";
                }
            }
        }

        if($games) {
//            database::instance()->begin();

            $sql .= implode(',', $games);
            Db::query(3, $sql)->execute();

            $sqlTable .= implode(',', $gamesTable);
            Db::query(3, $sqlTable)->execute();

//            database::instance()->commit();
        }
    }

    public function action_checkdeliveryemails() {
        $dir = DOCROOT.'undelivered';

        $files_names = scandir($dir);

        $emails = [];

        foreach ($files_names as $v) {
            $file = $dir.DIRECTORY_SEPARATOR.$v;

            if(!in_array($v,['.','..']) AND file_exists($file)) {
                $fh = fopen($file, "r");

                while (($row = fgets($fh)) !== false) {
                    if(strpos($row, 'Original-Recipient') !== false) {
                        $pattern = "/[-a-z0-9!#$%&'*_`{|}~]+[-a-z0-9!#$%&'*_`{|}~\.=?]*@[a-zA-Z0-9_-]+[a-zA-Z0-9\._-]+/i";
                        preg_match_all($pattern, $row, $matches);

                        $email = $matches[0][0]??false;

                        if($email) {
                            $emails[] = $email;
                        }

                        break;
                    }
                }

                fclose($fh);
            }
        }

        if($emails){
            $sql_users = <<<SQL
                Update users set email_valid = 0
                Where email in :emails
SQL;
            db::query(3, $sql_users)->param(':emails', $emails)->execute();

            $sql_unreg = <<<SQL
                Update users_unreg set email_valid = 0
                Where email in :emails
SQL;
            db::query(3, $sql_unreg)->param(':emails', $emails)->execute();
        }

    }


    public function action_smssend(){
        $keyProcess='smsSend';
        if (!th::lockProcess($keyProcess)){
            return null;
        }

        $sql='select id, text, "to", bot
                from sms
                where status=0 order by created asc';

        $data=db::query(1, $sql)->execute()->as_array();
        if (!$data){
            th::unlockProcess($keyProcess);
            return null;
        }

        $to=[];
        $ids=[];
        foreach($data as $sms){
            $to[$sms['to'].'-'.$sms['bot']][]=$sms['text'];
            $ids[]=$sms['id'];
        }



        foreach ($to as $dest=>$text){
            $dest=explode('-',$dest);
            tg::send($dest[0],implode("\r\n",$text),$dest[1]);
        }

        $sql='update sms set status=100 where id in :id';
        db::query(Database::UPDATE, $sql)->param(':id',$ids)->execute();

        th::unlockProcess($keyProcess);

    }



    public function action_daymoney(){

        $date=date('Y-m-d',time()-60*60*24);

        $sql="Select c.code,c.val,
            sum(
                case when bettype = 'norafs' or bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in end
            ) as in,
            sum(amount_out) as out,
            sum(count) as cnt
            From statistics s
            join offices o on o.id=s.office_id
            join currencies c on o.currency_id=c.id
            Where
		date = :date
		AND bettype in ('normal', 'norafs', 'norcfs', 'norlfs', 'normfs', 'double', 'douafs', 'doucfs', 'doulfs', 'doumfs', 'free', 'freafs', 'frecfs', 'frelfs', 'fremfs','jp','prize')

		AND o.is_test=0
		GROUP BY 1,2";

        $data=db::query(1,$sql)
            ->param(':date',$date)
            ->param(':types',['normal','double','free'])
            ->execute()->as_array();

        if (!$data){
            th::ceoAlert($date.' zero');
            return null;
        }

        $sqlF="Select (sum(c.val*((case when bettype = 'norafs' or bettype = 'norcfs' or bettype = 'norlfs' then 0 else amount_in end)-amount_out))/
                    (date_part('days',current_date) - 1)*
                    (date_part('days',date_trunc('month', current_timestamp at time zone 'UTC' + interval '1 month') - date_trunc('month', current_timestamp))))::NUMERIC(14,2) as forecast_profit
                From statistics s
                    join offices o on o.id=s.office_id
                    JOIN currencies c ON o.currency_id = c.id
                Where
                    date >= to_char(date_trunc('month', current_timestamp at time zone 'UTC'),'YYYY-MM-DD')
                    AND date <= :date
                    AND bettype in ('normal', 'norafs', 'norcfs', 'norlfs', 'normfs', 'double', 'douafs', 'doulfs', 'doumfs', 'doucfs', 'free', 'freafs', 'frelfs', 'fremfs', 'frecfs','jp','prize')
                    and o.is_test=0";

        $dataF=db::query(1,$sqlF)
            ->param(':date',$date)
            ->execute()->as_array();

        $forecast=round($dataF[0]['forecast_profit']/1000);

        $txt=[$date];
        $total=['in'=>0,'win'=>0,'cnt'=>0];
        $totalA=['in'=>0,'win'=>0,'cnt'=>0];
        foreach($data as $cur){

            if ($cur!='FUN'){
                $total['in']+=$cur['in']*$cur['val'];
                $total['win']+=($cur['in']-$cur['out'])*$cur['val'];
                $total['cnt']+=$cur['cnt'];
                $totalA['in']+=$cur['in'];
                $totalA['win']+=($cur['in']-$cur['out']);
                $totalA['cnt']+=$cur['cnt'];
            }

            $win=round( ($cur['in']-$cur['out'])/1000 );
            $cur['in']=round($cur['in']/1000);

            $txt[] = "{$cur['code']} i: {$cur['in']} w:{$win}";
        }

        $txt=implode("\r\n",$txt);
        $total['in']=round($total['in']/1000);
        $total['win']=round($total['win']/1000);
        $totalA['in']=floor($totalA['in']/100)/10;
        $totalA['win']=floor($totalA['win']/100)/10;

        //$txt.="\r\nTotal i:{$total['in']} w:{$total['win']}";

        $txt="T {$totalA['in']} , {$totalA['win']} , {$totalA['cnt']}";
        $txt.="\r\n"."{$total['in']} , {$total['win']} , {$forecast}";

        th::ceoAlert($txt);

    }


    public function action_clearfreespins() {

        $keyProcess = 'clearfsapi';
        if(!th::lockProcess($keyProcess))
        {
            th::critAlert("clearfsapi LOCKED!");
            return null;
        }

        db::query(Database::DELETE,'delete from freespins where expirtime<extract(\'epoch\' from CURRENT_TIMESTAMP)::int4')
            ->execute();

        //todo
        //обновлять здесь fs_history (или в админке)

        th::unlockProcess($keyProcess);
    }

    public function action_processfsapi()
    {

        $keyProcess = 'processfsapi';
        if(!th::lockProcess($keyProcess))
        {
            th::critAlert("processfsapi LOCKED!");
            return null;
        }

        $set_to_process = db::query(1,'select * from freespins_stack as stack '
            . 'where status=0 order by created asc limit 1')
            ->execute()
            ->as_array('id');


        if(!empty($set_to_process))
        {

            $process_id = key($set_to_process);

            $set_to_process = array_values($set_to_process)[0];

            db::query(Database::UPDATE,'update freespins_stack set status=1,updated=:upd where id=:id')
                ->param(':id',$process_id)
                ->param(':upd',time())
                ->execute();

            $sql_users = 'select id,name,office_id from users where blocked=0 ';

            $sql_params = [];

            if(!empty($set_to_process['login']))
            {
                $sql_users           .= ' and id=:name';
                $sql_params[':name'] = $set_to_process['login'];
            }
            else {
                //dont use mass
                th::unlockProcess($keyProcess);
                return;
            }

            if(((int) $set_to_process['office_id']) > 0)
            {
                $sql_users                .= ' and office_id=:office_id';
                $sql_params[':office_id'] = $set_to_process['office_id'];
            }

            if(!empty($set_to_process['params']))
            {

                $params = json_decode($set_to_process['params']);

                foreach($params as $param_types)
                {
                    foreach($param_types as $type=>$param)
                    {

                        $c = new $type('user',explode('_',$type)[1]);

                        foreach($param as $i => $v)
                        {

                            $v                                    = (array) $v;

                            $sql_users                            .= ' and ' . $v['field'] . ' ' . $v['op'] . ' :' . ($v['field'] . $type.$i);


                            $sql_params[':' . ($v['field'] . $type. $i)] = $c->sqlval($v['val']);
                        }
                    }
                }
            }


            $sql_users .= ' and id>:user_id order by id limit 100';

            do
            {

                $sql_params[':user_id'] = (int) ($last_user_id ?? $set_to_process['last_user_id']);


                $res_users = db::query(1,$sql_users)
                    ->parameters($sql_params)
                    ->execute()
                    ->as_array('id');

                $user_ids = array_keys($res_users);

                $last_user_id = null;
                $status       = 2;

                Database::instance()->begin();

                try {

                    if(!empty($user_ids))
                    {
                        $last_user_id = max($user_ids);
                        $status       = 1;

                        foreach($res_users as $user_id => $u)
                        {

                            $expire = ($set_to_process['expirtime']??null);

                            if(!empty($expire)) {
                                $expire = (int)$expire;
                            }

                            $f = new Model_Freespin();
                            $f->fs_offer_id=$set_to_process['set_id'];
                            $f->fs_offer_type='fsset';

                            $f->giveFreespins($user_id,$u['office_id'],$set_to_process['game_id'],$set_to_process['fs_count'],$set_to_process['amount']/$set_to_process['fs_count'],$set_to_process['lines'],$set_to_process['dentab_index'],'api',true,[],false,$set_to_process['visible_name'],$expire);
                        }
                    }

                    db::query(Database::UPDATE,'update freespins_stack set last_user_id=:last_user_id,status=:status,updated=:upd where id=:id')
                        ->param(':last_user_id',$last_user_id)
                        ->param(':status',$status)
                        ->param(':upd',time())
                        ->param(':id',$process_id)
                        ->execute();

                    Database::instance()->commit();
                }
                catch(Database_Exception $e) {
                    Database::instance()->rollback();
                }


            }
            while($status != 2);
        }

        th::unlockProcess($keyProcess);
    }

//bets to archive
    public function action_archive(){

        Service::betArchive();
        Service::statisticLocal();
        Service::statisticUsers();
        Service::statisticDynamics();
        Service::bets1003(); //office
        Service::betsOwner1062(); //owner
        Service::betsAvg();

    }


//collect users' statistic users_month_history


    public function action_usermh(){

        Service::usersMH();

    }


public function action_ba(){

    Service::betsAvg();

}

public function action_collectrates(){
    Service::collectRatesYesterday();
}

    public function action_monthstat(){

        Service::monthStat();

    }

    public function action_calcpromoevents() {

        $keyProcess='calcpromoeventsProcess';
        if (!th::lockProcess($keyProcess)){
            return null;
        }

        $gamestrict = ['jp'];
        $gamestrict = array_merge($gamestrict,array_keys((array) Kohana::$config->load('videopoker')));

        database::instance()->begin();

        $events=db::query(1,'update events set calc=extract(epoch from now() at time zone \'UTC\')::int 
                                            from (select e.* from events e
                                            join offices o on o.id=e.office_id
                                            where 
                                            e.calc < extract(epoch from date_trunc(\'day\', now() at time zone \'UTC\') at time zone \'UTC\')::int4 
                                            AND e.TYPE = \'promo\' 
                                            AND e.starts < EXTRACT ( epoch FROM now( ) AT TIME ZONE \'UTC\' ) :: INT 
                                            AND ends > EXTRACT ( epoch FROM now( ) AT TIME ZONE \'UTC\' ) :: INT
                                            and (extract(epoch from date_trunc(\'day\', now() at time zone \'UTC\') at time zone \'UTC\')::int4
                                                +e.h*60*60
                                                +e.m*60
                                                +e.duration)<EXTRACT ( epoch FROM now( ) AT TIME ZONE \'UTC\' ) :: INT
                                            and 
                                                (extract(epoch from date_trunc(\'day\', now() at time zone \'UTC\') at time zone \'UTC\')::int4
                                                +e.h*60*60
                                                +e.m*60
                                                +e.duration
                                                +e.time_to_collect) > EXTRACT ( epoch FROM now( ) AT TIME ZONE \'UTC\' ) :: INT) as s 
                                        where events.id = s.id
                                        RETURNING s.*')
            ->execute(null,'Model_Event')
            ->as_array();

        $data=[];

        foreach($events as $e) {
            $games=$e->games_ids;


            $moon_last_rounds=db::query(1,'select * from moon_results where created<:end order by created desc limit 2')
                ->param(':end',$e->ends)
                ->execute()
                ->as_array('id');

            $sql='update users set promo_inout=(case when s.sum>:max then :max else s.sum end)
                            from (
                            select sum(amount-win),user_id from bets 
                            where created>=:start and created<=:end and office_id=:o_id
                            and (game in :moon_games and come in :moonrounds) IS NOT TRUE
                            and game_id in :game_ids
                            and game not in :strict_games
                            and is_freespin=0
							and type != \'prize\'
                            group by user_id
                            ) as s
                            where id=s.user_id and promo_started is not null and promo_end_time!=promo_started
                            returning id,promo_inout,office_id';

            $res=db::query(Database::SELECT,$sql)
                ->param(':start',$e->startTime())
                ->param(':o_id',$e->office_id)
                ->param(':game_ids',$games)
                ->param(':strict_games',$gamestrict)
				->param(':moon_games',th::getMoonGames())
                ->param(':max',$e->max_payout)
                ->param(':moonrounds',array_map('strval', array_keys($moon_last_rounds)))
                ->param(':end',$e->startTime()+$e->duration)
                ->execute()->as_array();

            

            $data[$e->id]=[];

            $date=date('Y-m-d');

            foreach($res as $row) {
                $text=$row['id'].';'.$row['promo_inout'].PHP_EOL;

                logfile::create(date('Y-m-d H:i:s').PHP_EOL . $text.PHP_EOL,'promocalc');

                if(!isset($data[$e->id][$date])) {
                    $data[$e->id][$date]=[];
                }

                if(!isset($data[$e->id][$date][$row['office_id']])) {
                    $data[$e->id][$date][$row['office_id']]=[
                        'event_id'=>$e->id,
                        'office_id'=>$row['office_id'],
                        'max_promo_out'=>0,
                        'max_promo_count'=>0,
                        'created'=>time(),
                        'date'=>date('Y-m-d'),
                    ];
                }

                if($row['promo_inout']>0) {
                    $data[$e->id][$date][$row['office_id']]['max_promo_out']+=$row['promo_inout'];
                    $data[$e->id][$date][$row['office_id']]['max_promo_count']++;
                }
            }

            th::ceoAlert(count($res).' users promo calc ['.$e->office_id.']');
        }

        foreach ($data as $e_id=>$dates) {
            foreach ($dates as $date=>$offices) {
                foreach ($offices as $office_id=>$vals) {
                    $text2='summary: e_id: '.$e_id.'; date: '.$date.'; office_id: '.$office_id.'; all: '.print_r($vals,1);
                    logfile::create(date('Y-m-d H:i:s').PHP_EOL . $text2 .PHP_EOL,'promocalc');
                    Model_Event::updateStats($vals);
                }
            }
        }

        database::instance()->commit();
		
		foreach ($events as $event) {

            $time=$e->startTime();

            $d = date('d', $time);
            $m = date('m', $time);
            $y = date('Y', $time);

            $start = $time;
            $end = $start + $event->duration;
            $end = min($end, $event->ends);


            $sql = 'select sum(b.amount) as in , sum(b.win) as out, count(b.id) as cnt,count( DISTINCT user_id) as users
                from bets b
                join users u on b.user_id = u.id
                where b.office_id=:oid
                    and u.test =0
                and b.created>=	:start
                and b.created<= 	:end';

            $data = db::query(1, $sql)->param(':start', $start)
                ->param(':end', $end)
                ->param(':oid',  $event->office_id)
                ->execute()
                ->as_array();

            if (count($data) == 0) {
                continue;
            }

            $data = $data[0];

            $date = "$y-$m-$d";
            echo $date;


            $sql = 'insert into statistic_events (
                  "event_id" ,  "created" ,  "date" ,  "office_id" ,  "in" ,  "out" ,  "count" ,  "users" ,  "calc", cancel_count, max_promo_count, max_promo_out)
                  VALUES (:eventId, :created,  :date,  :oid,  :in,  :out,  :count,  :users,  1, 0,0,0)
                    ON CONFLICT On CONSTRAINT statistic_events_pkey do UPDATE
                        SET  "in" = EXCLUDED.in ,
                                        "out" = EXCLUDED.out ,
                                    "count" = EXCLUDED.count ,
                                    users = EXCLUDED.users ,
                                    calc = 1
                    ';

            db::query(Database::UPDATE, $sql)
                ->parameters([
                    ':eventId' => $event->id,
                    ':created' => time(),
                    ':date' => $date,
                    ':oid' => $event->office_id,
                    ':in' => $data['in'],
                    ':out' => $data['out'],
                    ':count' => $data['cnt'],
                    ':users' => $data['users']
                ])
                ->execute();


        }

        th::unlockProcess($keyProcess);
    }

    public function action_calcdsbets() {

        $keyProcess='calcdsbetsProcess';
        if (!th::lockProcess($keyProcess)){
            return null;
        }

        $gamestrict = ['jp']+th::getMoonGames();

        $gamestrict = array_merge($gamestrict,array_keys((array) Kohana::$config->load('videopoker')));

        $last_moon_round=db::query(1,'select id from moon_results order by 1 desc limit 1')->execute()->as_array();

        db::query(database::UPDATE,'UPDATE users 
                SET ds_inout = s.SUM 
                FROM
                    (
                    SELECT SUM
                        ( b.amount - b.win ),b.user_id
                    FROM
                        bets b
                        JOIN users u ON u.ID = b.user_id 
                        AND b.created >= u.last_bonus_calc
                        JOIN offices o ON o.ID = b.office_id 
                    WHERE
                        o.enable_bia > 0 
                        AND b.is_freespin = 0 
                        AND ( ( b.game NOT IN :gamestrict ) OR ( b.game in :moongame AND b.come != :moon_round ) ) 
                    GROUP BY
                        b.user_id 
                    ) 
                AS s where s.user_id=id;')
            ->parameters([
                ':gamestrict'=>$gamestrict,
                ':moongame'=>th::getMoonGames(),
                ':moon_round'=>$last_moon_round[0]['id'],
            ])
            ->execute();

        th::unlockProcess($keyProcess);
    }


    public function action_minus(){
        Service::userMinus();

    }
	
	public function action_updatelsamount() {
        $keyProcess='updatelsamount';
        if (!th::lockProcess($keyProcess)){
            th::critAlert("updatelsamount LOCKED!");
            return null;
        }

        $time = strtotime("-1 days");

        $data = Service::parseCurrencyRateByDate($time);

        foreach(db::query(1,'select c.code,c.val,c.mult,
                e.* from events e 
                join offices o on o.id=e.office_id 
                join currencies c on c.id=o.currency_id
                where e.type=\'progressive\' and is_auto_gen=1')->execute(null,'Model_Event') as $event) {

            $val=$data[$event->code]??$event->val;

            $event->fs_amount=$event->getValueForLS($val,$event->mult);
            $event->save();
        }

        th::unlockProcess($keyProcess);
    }
	
	public function action_clearbrokenlock() {
        foreach(dbredis::instance()->keys('__userbets_newlock__*') as $key) {
            if(dbredis::instance()->ttl($key)===-1) {
                dbredis::instance()->del($key);
                echo 'deleted: '.$key.PHP_EOL;
            }
        }
    }
	
	public function action_promocalc(){

        Service::promoCalc();
    }
	
	 public function action_sort(){

        Service::sort();
    }
	
	public function action_su(){
		Service::statisticUsers();
	}

}


