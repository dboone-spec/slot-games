<?php

class bet {

    public static $trader_transaction=['error'=>true];
    protected static $small_bank_amount = null;
    public static $last_bet_id;
    public static $is_first_bet=false;
    public static $api_request_id;
    public static $jpwin=0;
    public static $jpdata=[
            'values'=>[]
    ];


    public static function needZero(){
        $o = office::instance()->office();
        $oRTP=(int) $o->rtp;
        if ($oRTP<80){
            $oRTP=80;
        }
        if ($oRTP>96){
            $oRTP=96;
        }

        $uRTP=96;
        if (auth::$user_id){

            $uRTP=(int) auth::user()->rtp;
            if ($uRTP<80){
                $uRTP=80;
            }
            if ($uRTP>96){
                $uRTP=96;
            }

        }
        $RTP=min($oRTP,$uRTP);
        if ($RTP==96){
            return false;
        }

        if (mt_rand(1,10000)/10000>$RTP/96){
            return true;
        }

        return false;


    }

    public static function needZeroPoker(){

        return false;

        //На второй этап 36% RTP приходится
        //Если нужно срезать от всего RTP %
        $srez=5;
        //то нужно срезать
        //берем 45, а не 36 хз почему, но при 45 получаются самые правильные результаты
        $srez=$srez/45;

        if (mt_rand(1,10000)/10000<$srez){
            return true;
        }

        return false;
    }



    //есть ли сума достаточчная для выигрыша в банке
    public static function HaveBankAmount($win,$gameId=0) {
/*
        if(DEMO_DOMAIN) {
            return true;
        }
*/
        //выход если выиграл меньше минимального порога
        if ($win <= Kohana::$config->load('static.zero_win')) {
                return true;
        }

        //информация может быть уже не актульная, да и хуй с ней
        $st = office::instance()->office();

        /* removed from server
        if ($gameId==762 and $st->id==1038){
            if ($win>49749){
                return false;
            }

        }*/

        if($st->use_bank==0) {
            return true;
        }


        if ($st->apienable){
            $bank = $st->bank;
        }
        else{
            $bank = $st->bank - $st->users;
        }

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
        if (!$freespins && bccomp($user->amount(),$sum)<0) {
            return 4;
        }

        //нет денег на балансе
        if(PROJECT==2 && !$office->is_test && $office->postpay && bccomp($office->amount,$sum)<0) {
            return 5;
        }

        if(!$freespins) {
            //ставка маленькая или высокая
            foreach ($amount as $bet) {
                if (!in_array(6, $noerror) && ($bet < $office->bet_min || $bet > $office->bet_max)) {
                    return 6;
                }
            }
        }

        //TODO добавить ставка маленькая или слишком высокая для игры


        return 0;
    }

    public static function pokerstart($amount, $data = null) {

        $data['bet_started']=time();


        $game_type = game::session()->type;
        $game = game::session()->game;

        $sql_nextval = <<<SQL
            Select nextval('bets_id_seq')
SQL;
        $bet_id = key(db::query(1, $sql_nextval)->execute()->as_array('nextval'));

        $bet = new Model_Pokerbet();
        $bet->id = $bet_id;
        $bet->user_id = auth::$user_id;
        $bet->amount = $data['amount'];
        $bet->game_type = $game_type;
        $bet->game = $game;
        $bet->type = 'normal'; //normal, free game, bonus game, double
        $bet->come = arr::get($data,'comb');
        $bet->result = arr::get($data,'result');
        $bet->office_id = auth::user()->office_id;
        $bet->win = 0;
        $bet->game_id = arr::get($data, 'game_id', 0);
        $bet->method = arr::get($data,'method');
        $bet->balance = auth::user()->amount(true)-$data['amount'];

        $come = $bet->come;
        $bet->come = card::print_card($bet->come);
        $bet->created = $data['bet_started'];

        $api = self::sendapi($bet);
        $bet->come = $come;

        database::instance()->begin();
        $bet->save();

        $data['poker_bet_id']=$bet->id;
        $data['initial_id']=$bet->id;

        bet::$last_bet_id = $bet->id;

        if ($bet->method=='bank'){
            $firstWin=$data['firstWin'] ?? -1000;
            th::ceoAlert("Poker deal {$bet->office_id} b {$firstWin} {$bet->id}");
        }

        if(!$api) {
            $sql = 'update users
                set amount=amount-:amount,
                sum_amount=sum_amount+:amount
                where id=:uid';

            db::query(1, $sql)->param(':amount', (float) $amount)
                    ->param(':uid', auth::$user_id)
                    ->execute();
        }

        if(PROJECT==1) {
            bet::donateJP($data['amount']/10,auth::user()->office_id,$bet->id);
        }

        database::instance()->commit();

        $data['balance']=auth::user(true)->amount();
        dbredis::instance()->select(0);
        game::session()->flash($data,true);

    }

    public static function sendjp($params=[]) {

        $api = gameapi::instance(auth::user()->office->apitype);

        if(auth::user()->office->apienable AND $api->isNeedSend(auth::user()->office->gameapiurl)) {
            $resapi = $api->jp(auth::user()->name, auth::user()->office->gameapiurl, $params);
        }
    }
    public static function sendapi(Model_Bet $bet,$poker_bet_id=0,$session_data=[],$extra_data=[]) {

        $api = gameapi::instance(auth::user()->office->apitype);

        if(auth::user()->office->apienable AND $api->isNeedSend(auth::user()->office->gameapiurl)) {

            //strict egt if need
            if(false && $game_model->brand=='egt') {
                throw new Exception('work on regulation');
            }

            $amount = $bet->amount;


            switch($bet->type) {
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
                case 'prize':
                    $bettype = 'free';
                    break;
                default:
                    $bettype = $bet->type;
            }

            if(isset($bet->is_freespin) && $bet->is_freespin && $bettype=='normal') {
                $amount=0;
            }

            $params = [
                    'amount'=>$amount,
                    'fs_amount'=>(float) $bet->amount,
                    'win'=>(float) $bet->win,
                    'game'=>$bet->game,
                    'game_id'=>$bet->game_id,
                    'game_type'=>$bet->game_type,
                    'bettype'=>$bettype,
                    'bet_id'=>$bet->id,
                    'come'=>$bet->come,
                    'fg_first_bet_id'=>$session_data['fg_first_bet_id']??false,
                    'result'=>$bet->result,
                    'is_freespin'=>isset($bet->is_freespin) && (bool) $bet->is_freespin,
                    'base_amount' => (float) $bet->amount,
                    'created' => $bet->created,
                    'is_cashback' => isset($bet->is_freespin) && ((int) $bet->is_freespin===1),
					'is_luckyspin' => isset($bet->is_freespin) && ((int) $bet->is_freespin===3),
            ];

            $params+=$extra_data;

            if($poker_bet_id>0) {
                $params['poker_bet_id']=$poker_bet_id;
            }

            if(!empty($bet->initial_id)) {
                $params['initial_id']=$bet->initial_id;
            }

            $params['slot_win_lines']=game::data('win_per_line',[]);
            $url = $api->getUrl(auth::user()->office->gameapiurl);

            try {
                $resapi = $api->bet(auth::user()->name, $url, $params);
            }
            catch (Exception_ApiResponse $e) {
                $resapi=false;
            }

            if($api->bet_request_id) {
                self::$api_request_id=$api->bet_request_id;
                $params['bet_request_id']=$api->bet_request_id;
            }

            if(!$resapi) {
                $er = $api->last_error;
                $er = substr($er,0,100);
				
				Game_Session::clearFG($bet->game);
                $u=auth::user();
                $u->last_game=null;
                $u->save();

                if(auth::user()->office->seamlesstype==1) {
                    $api->saveWrongBet($bet,$params,$poker_bet_id);

                    if($api->canRestoreSession && !empty($session_data)) {
                        Game_Session::savebackup($bet->id,$session_data);
                    }
                }

                if(!empty($er)) {
                    header('CustomError: '.$er);
                }
                throw new Exception_ApiResponse('wrong bet. '.$er.print_r($params,1));
            }
            return true;
        }
        return false;
    }

    protected static $_jp;

    public static function getJP() {
        return self::$_jp;
    }

    public static function setJP(Model_JackpotHistory $jpModel) {
        self::$_jp = $jpModel;
    }

    //Model_Bet for mango
    public static function saveJP($bet) {
        $jpdata = [
                'values' => [],
        ];

        $jps=auth::user()->office->activeJackpots();

        foreach($jps as $jp) {
            $jpdata['values'][$jp->type]=bcdiv($jp->current,1,2);

            if($jp->last_win_time+$jp->jp_show_time() > time()) {
                $jpdata['last_win_time'] = time();
                $jpdata['win'] = th::number_format($jp->prev_value);
                $jpdata['is_win'] = (int) arr::get($jps,'win',0);
                $jpdata['user_id'] = $jp->user_id;
            }
        }

        bet::$jpdata=$jpdata;
        $j=new Model_Jackpot();
        $j->toFile($jpdata,auth::user()->office_id);

        if(bet::$jpwin>0) {
            self::sendjp([
                    'win'=>(float) bet::$jpwin,
                    'game'=>$bet->game,
                    'game_id'=>$bet->game_id,
                    'bet_id'=>$bet->id,
                    'user_id'=>$jp->user_id,
            ]);
        }
    }

    public static function donateJP($amount,$office_id,$bet_id) {

        if(auth::user()->office->enable_jp) {
            $j = new model_jackpot();
            $jpwin = $j->donate($amount,$office_id,$bet_id);
            bet::$jpwin=$jpwin['win'];
            bet::$jpdata=[
                    'values'=>$jpwin['values']
            ];
        }
    }

    public static function make($data, $bettype = 'normal', $ses_data = null, $no_session=false, $restore_session=false) {

        //проверяем есть ли пользователь, также перезагружаем модель
        if(!auth::user(true)->loaded()) {
            throw new Exception('no user for bet');
        }

        self::$is_first_bet=empty(auth::user()->last_bet_time);

        $mult=auth::user()->office->currency->mult ?? 2;


        if(!isset($data['can_jp'])) {
            $data['can_jp']=true;
            if(PROJECT==1) {
                $data['can_jp']=(bool) auth::user()->office->enable_jp;
            }
        }


        if(!isset($data['send_api'])) {
            $data['send_api']=true;
        }

        if(isset($data['bet_id']) && $data['bet_id']) {
            $bet_id=$data['bet_id'];
        }
        else {

            $sql_nextval = <<<SQL
            Select nextval('bets_id_seq')
SQL;
            $bet_id = key(db::query(1, $sql_nextval)->execute()->as_array('nextval'));
        }

        $bet_time = time();

        if(isset($ses_data['freegames_start_from'])) {
            //$ses_data['start_bet_id'] = $bet_id; //TODO непомню зачем вообще это надо. убираю, т.к. нет бонусов
        }

        $poker_bet_id=0;
        if(isset($ses_data['poker_bet_id'])) {
            $poker_bet_id=$ses_data['poker_bet_id'];
            $ses_data['poker_bet_id']=0;
        }

		//обязательно. в пг не уходят числа с экспонентой. партнерам уходят не округлённые данные
        $data['amount']=rtrim(sprintf('%.'.($mult+2).'F',$data['amount']),'0');
        $data['win']=rtrim(sprintf('%.'.($mult).'F',$data['win']),'0');

        $in = $data['amount'];
        $out = $data['win'];
        $winAll=$out-$in;

        $game_type = $data['game_type']??game::session()->type;
        $game = $data['game_name']??game::session()->game;

        $betamount = $data['amount'];
        $isfs = $data['is_freespin']??false;

        if($isfs && $bettype=='normal') {
            $betamount=0;
        }

        if($isfs && $bettype=='double' && $data['win']==0) {
            $betamount+=game::data('first_bet');
        }

        if($bettype=='double'){
            $data['can_jp']=false;
        }

        $new_bettype = $bettype;

        if($isfs) {
            $short_fs_type='c';
            if($data['is_freespin']==2) {
                $short_fs_type='a';
            }
            if($data['is_freespin']==3) {
                $short_fs_type='l';
            }
            if($data['is_freespin']==4) {
                $short_fs_type='m';
            }

            $data['can_jp']=false;
            $new_bettype = substr($new_bettype,0,3);
            $new_bettype.= $short_fs_type; //api or cashback
            $new_bettype.='fs';
        }

        $bet = new Model_Bet();
        $bet->id = $bet_id;
        $bet->user_id = auth::$user_id;
        $bet->game_type = $game_type;
        $bet->game = $game;
        $bet->type = $new_bettype; //normal, free game, bonus game, double
        $bet->come = $data['come'];
        $bet->result = $data['result'];
        $bet->office_id = auth::user()->office_id;
        $bet->win = $out;
        $bet->game_id = arr::get($data, 'game_id', 0);
        $bet->method = $data['method'];
        $bet->is_freespin = (int) $isfs;
        $bet->created = $bet_time;
//        $bet->balance = auth::user()->amount()-$betamount+$data['win'];//amount + bonus
        $bet->balance = floor((auth::user()->amount()-$betamount+$data['win'])*pow(10,$mult))/pow(10,$mult);
        $bet->amount = $in;
        $bet->info = $data['info']??null;

        $bet->real_amount = $bet->amount;
        $bet->real_win = $bet->win;
        $bet->session_id = auth::getCustomSessionId(auth::$user_id, md5(auth::$token));
		$bet->game_session_id = auth::getCustomGameSessionId(auth::$user_id, $game, md5(auth::$token.$game));
        $bet->fs_uuid = $data['fs_uuid']??null;

        if(isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $bet->country = strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
        }

        $lastFS=$data['is_last_freespin'] ?? false;
        $lastFSID=$data['last_freespin_id'] ?? -1;

        $external_params=[
            'is_last_freespin'=>$lastFS,
            'last_freespin_id'=>$lastFSID,
            'free_games_started'=>$data['free_games_started']??false,
            'free_games_end'=>$data['free_games_end']??false,
            'fg_first_bet_id'=>$ses_data['fg_first_bet_id']??false,
			'is_buy'=>$data['is_buy']??0,
            'event_id'=>$data['event_id']??false,
        ];
		
		if($bettype=='jp') {
            $external_params['trigger_bet_id']=$data['trigger_bet_id']??0;
        }

        //очищаем.
        if($bet->type=='normal') {
            $ses_data['fg_first_bet_id']=false;
        }

        //сохраняем в сессию id ставки
        if($external_params['free_games_started']) {
            $ses_data['fg_first_bet_id']=$bet->id;
        }

        if(!empty($bet->fs_uuid)) {
            $external_params['fs_uuid']=$bet->fs_uuid;
        }

        if(isset($data['is_freespin']) && $data['is_freespin']==2 && auth::user()->office->apitype==4) {

            if(!$lastFS) {
                $bet->amount = 0;
                $bet->win = 0;
            }
            else {
                $fs = new Model_Freespinhistory(['freespin_id'=>$lastFSID]);

                if(!$fs->loaded()) {
                    throw Exception('error gift spins');
                }

                if($fs->fs_offer_type!='infingift') {
                    throw Exception('wrong gift spins');
                }

                $infinOffer = new Model_InfinOffer($fs->fs_offer_id);

                if(!$infinOffer->loaded()) {
                    throw Exception('error offer gift');
                }

                $bet->amount = $fs->fs_count*$fs->amount;
                $bet->win = $fs->sum_win;

                $external_params['infin_guid']=$infinOffer->guid;
            }

        }

        if(isset($data['is_freespin']) && $data['is_freespin']==2 && auth::user()->office->apitype==8) {

            $fs = new Model_Freespinhistory(['freespin_id'=>$data['last_freespin_id']]);

            if(!$fs->loaded()) {
                throw new Exception('error gift spins');
            }

            if($fs->fs_offer_type!='softgaming') {
                throw new Exception('wrong gift spins');
            }

            $softgamingFreeround = new Model_SoftGameFreeround($fs->fs_offer_id);

            if(!$softgamingFreeround->loaded()) {
                throw Exception('error offer gift');
            }

            //$bet->amount = $fs->fs_count*$fs->amount;
            //$bet->win = $fs->sum_win;

            $external_params['freeroundID']=$softgamingFreeround->freeround_id;
        }
		
		if(isset($data['is_freespin']) && $data['is_freespin']==2 && auth::user()->office->apitype==12) {
            if(!$lastFS) {
                $bet->amount = 0;
                $bet->win = 0;
                $data['send_api']=false;
            }
            else {

                $fs = new Model_Freespinhistory(['freespin_id' => $data['last_freespin_id']]);

                if (!$fs->loaded()) {
                    throw new Exception('error gift spins');
                }

                if ($fs->fs_offer_type != 'softswiss') {
                    throw new Exception('wrong gift spins');
                }

                $softswFreeround = new Model_SoftSwissAward($fs->fs_offer_id);

                if (!$softswFreeround->loaded()) {
                    throw Exception('error offer gift');
                }

                if ($softswFreeround->bonus_type!='freespins') {
                    throw Exception('error offer gift2');
                }

                $bet->amount = $fs->fs_count * $fs->amount;
                $bet->win = $fs->sum_win;

                $external_params['freeroundID'] = $softswFreeround->issue_id;
            }
        }
		
		if(isset($data['is_freespin']) && $data['is_freespin']==2 && auth::user()->office->apitype==9) {

            $fs = new Model_Freespinhistory(['freespin_id'=>$lastFSID]);

            if(!$fs->loaded()) {
                throw new Exception('error gift spins');
            }


            if($fs->fs_offer_type!='betconst') {
                throw new Exception('wrong gift spins');
            }

            $betconsFreeround = new Model_BetconstructFreespin($fs->fs_offer_id);

            if(!$betconsFreeround->loaded()) {
                throw Exception('error offer gift');
            }

            $bet->amount = 0;
            //$bet->win = $fs->sum_win;

            $external_params['freeroundID']=$betconsFreeround->id;
        }
		
		if(isset($data['is_freespin']) && $data['is_freespin']==2 && auth::user()->office->apitype==10) {

            if(!$lastFS) {
                $bet->amount = 0;
                $bet->win = 0;
				$data['send_api']=false;
            }
            else {

                $bet->amount = 0;

                $fs = new Model_Freespinhistory(['freespin_id' => $lastFSID]);

                if (!$fs->loaded()) {
                    throw new Exception('error gift spins: ' . $lastFSID);
                }

                if ($fs->fs_offer_type != 'pinup') {
                    throw new Exception('wrong gift spins');
                }

                $pinupFreespin = new Model_PinupFreespin($fs->fs_offer_id);

                if (!$pinupFreespin->loaded()) {
                    throw Exception('error offer gift');
                }

                if (!$lastFS) {
                    $bet->win = 0;
					
                } else {
                    $bet->win = $fs->sum_win;
                }

                $external_params['freeroundID'] = $pinupFreespin->freespinId;
            }
        }

        //далее важен порядок действий: сохранение в сессию и отправку по апи, прежде чем начать транзакцию

        if(isset($data['initial_id']) && !empty($data['initial_id'])) {
            $bet->initial_id=$data['initial_id'];
        }

        if($poker_bet_id>0) {
            $data['can_jp']=false;
            if(!$no_session) {
                if (empty($ses_data)) {
                    game::session()->save();
                } else {
                    game::session()->flash($ses_data);
                }
            }

            if($data['send_api']) {
                $bet->amount = 0;
                self::sendapi($bet,$poker_bet_id,$ses_data);
                $bet->amount = $data['amount'];
            }
        }
        else {

            if($data['send_api']) {
                self::sendapi($bet,$poker_bet_id,$ses_data,$external_params);
            }

            if(!$no_session) {
                if (empty($ses_data)) {
                    game::session()->save();
                } else {
                    game::session()->flash($ses_data);
                }
            }

            if($restore_session) {
                Game_Session::restorebackup($bet->id,$bet->user_id,$bet->game);
            }
        }

        if(self::$api_request_id) {
            $bet->request_id=self::$api_request_id;
        }

        $bet->balance = floor((auth::user(true)->amount())*pow(10,$mult))/pow(10,$mult);

        database::instance()->begin();

        if($data['can_jp']) {
            bet::donateJP($data['amount'],auth::user()->office_id,$bet_id);
        }

        $sqlWinAll=(float) $winAll;
        $sqlIn=(float) $in;


        if($poker_bet_id) {
            $sqlWinAll+=$in;
            $sqlIn=0;
            $bet->balance+=$in;
            $bet->external_id=$poker_bet_id;
        }


        $bet->save();

        if(self::$_jp) {
            self::$_jp->save();
        }

        //шлем SMS
        if ($bet->method=='bank'){
            $firstWin=$data['firstWin'] ?? -1000;
            //$firstWin=round($firstWin/1000);
            th::ceoAlert("{$bet->office_id} b {$firstWin} {$bet->id}");
        }

        if($isfs && $bettype=='normal') {
            //добавляем к инам только для обычного фриспина. не удвоение
            $sqlWinAll+=$sqlIn;
        }

        $ds_info=json_decode(auth::user()->ds_info,1);

        $sqlUsers='update users
                set last_bet_time=:last_bet_time,ds_in_out=ds_in_out+:ds_in_out,ls_wager=ls_wager+:ls_wager,ds_info=:ds_info ';

        if(!auth::user()->office->apienable OR auth::user()->office->apitype==1) {
            $sqlUsers.=', amount=amount+:win,
                sum_amount=sum_amount+:in,
                sum_win=sum_win+:out
                ';
        }


        //обновляем на агт время последней ставки только если потрачены деньги
        $bettime = (PROJECT==2 || (in_array($bettype,['normal','double']) && !$isfs))?time():DB::expr('last_bet_time');


        $gamestrict = ['jp'];

        $gamestrict = array_merge($gamestrict,array_keys((array) Kohana::$config->load('videopoker')));

        $ds_inout=0;
        $ls_wager=0;

        if($bet->is_freespin==0) {
            $ls_wager=$bet->amount;
        }

        if($bet->is_freespin==0 && !in_array($game,$gamestrict) && !th::cantFSback($game)) {

            $ds_inout=$bet->amount-$bet->win;

            if(!isset($ds_info[$game])) {
                $ds_info[$game]=[
                    'cnt'=>0,
                    'avg'=>0,
                    'in'=>0,
                    'out'=>0,
                    'game_id'=>$bet->game_id,
                    'game'=>$game,
                ];
            }
            $ds_info[$game]['cnt']++;
            $ds_info[$game]['in']+=$bet->amount;
            $ds_info[$game]['out']+=$bet->win;
            $ds_info[$game]['avg']=$ds_info[$game]['in']/$ds_info[$game]['cnt'];
        }

        //если это выплата выигрыша по турниру
        if(isset($data['promo_prize']) && $data['promo_prize']) {
            $sqlUsers.=',promo_inout=0,promo_started=null,promo_end_time=null ';
        }

        $ds_info=json_encode($ds_info);

        $sqlUsers.='where id=:uid';

        db::query(1, $sqlUsers)->param(':win', $sqlWinAll)
                ->param(':in', $sqlIn)
                ->param(':out', (float) $out)
                ->param(':uid', auth::$user_id)
                ->param(':last_bet_time', $bettime)
                ->param(':ds_inout',$ds_inout)
                ->param(':ds_in_out',$ds_inout)
                ->param(':ls_wager',$ls_wager)
                ->param(':ds_info',$ds_info)
                ->execute();

        //для счетчиков

        $count=1;

        $bonus=0;
        $bonusCount=0;

        $free=0;
        $freeCount=0;

        $double_in = 0;
        $double_out = 0;
        $doubleCount = 0;

        $fs_api_in = 0;
        $fs_api_out = 0;
        $fs_api_count = 0;

        $fs_cash_in = 0;
        $fs_cash_out = 0;
        $fs_cash_count = 0;

        $fs_lucky_in = 0;
        $fs_lucky_out = 0;
        $fs_lucky_count = 0;

        $fs_moon_in = 0;
        $fs_moon_out = 0;
        $fs_moon_count = 0;


        if ($bettype=='bonus'){
                $bonus=$winAll;
                $bonusCount=1;
                $count=0;
                $out=0;
        }
        if (in_array($new_bettype,['free','frecfs','freafs','frelfs','fremfs'])){
                $free=$winAll;
                $freeCount=1;
                $count=0;
                $out=0;
        }


        if(in_array($new_bettype,['double','doucfs','douafs','doulfs','doumfs'])) {
            $double_in = $in;
            $double_out = $out;
            $doubleCount = 1;

            $in = 0;
            $out = 0;
            $count=0;
        }

        if($new_bettype=='norafs') {
            $fs_api_in = $in;
            $fs_api_out = $out;
            $fs_api_count = 1;

            $in = 0;
            $out = 0;
            $count=0;
        }

        if($new_bettype=='normfs') {
            $fs_moon_in = $in;
            $fs_moon_out = $out;
            $fs_moon_count = 1;

            $in = 0;
            $out = 0;
            $count=0;
        }

        if($new_bettype=='norcfs') {
            $fs_cash_in = $in;
            $fs_cash_out = $out;
            $fs_cash_count = 1;

            $in = 0;
            $out = 0;
            $count=0;
        }

        if($new_bettype=='norlfs') {
            $fs_lucky_in = $in;
            $fs_lucky_out = $out;
            $fs_lucky_count = 1;

            $in = 0;
            $out = 0;
            $count=0;
        }


        $sql='update counters
                set "in"="in"+:in,
                "out"="out"+:out,
                "double_in"="double_in"+:double_in,
                "double_out"="double_out"+:double_out,
                "fs_cash_in"="fs_cash_in"+:fs_cash_in,
                "fs_cash_out"="fs_cash_out"+:fs_cash_out,
                "fs_api_in"="fs_api_in"+:fs_api_in,
                "fs_api_out"="fs_api_out"+:fs_api_out,
                "fs_lucky_in"="fs_lucky_in"+:fs_lucky_in,
                "fs_lucky_out"="fs_lucky_out"+:fs_lucky_out,
                "fs_moon_in"="fs_moon_in"+:fs_moon_in,
                "fs_moon_out"="fs_moon_out"+:fs_moon_out,
                "bonus"="bonus"+:bonus,
                "free"="free"+:free,
                "count"="count"+:count,
                double_count=double_count+:doubleCount,
                free_count=free_count+:freeCount,
                fs_api_count=fs_api_count+:fs_api_count,
                fs_cash_count=fs_cash_count+:fs_cash_count,
                fs_lucky_count=fs_lucky_count+:fs_lucky_count,
                fs_moon_count=fs_moon_count+:fs_moon_count,
                bonus_count=bonus_count+:bonusCount
                where game_id=:game
                AND office_id = :oid';

        $affected = db::query(Database::UPDATE,$sql)->param(':in',(float) $in)
                        ->param(':out',(float) $out)
                        ->param(':bonus',(float) $bonus)
                        ->param(':double_in',(float) $double_in)
                        ->param(':double_out',(float) $double_out)
                        ->param(':game',(int) $bet->game_id)
                        ->param(':oid', auth::user()->office_id)
                        ->param(':free', $free)
                        ->param(':count', $count)
                        ->param(':doubleCount', $doubleCount)
                        ->param(':bonusCount', $bonusCount)
                        ->param(':freeCount', $freeCount)
                        ->param(':fs_api_count', $fs_api_count)
                        ->param(':fs_cash_count', $fs_cash_count)
                        ->param(':fs_lucky_count', $fs_lucky_count)
                        ->param(':fs_moon_count', $fs_moon_count)
                        ->param(':fs_api_in', $fs_api_in)
                        ->param(':fs_cash_in', $fs_cash_in)
                        ->param(':fs_lucky_in', $fs_lucky_in)
                        ->param(':fs_moon_in', $fs_moon_in)
                        ->param(':fs_api_out', $fs_api_out)
                        ->param(':fs_cash_out', $fs_cash_out)
                        ->param(':fs_lucky_out', $fs_lucky_out)
                        ->param(':fs_moon_out', $fs_moon_out)
                          ->execute();

        if ($affected == 0) {
            $sql = 'insert into counters ( "in", "out", "double_in",  "double_out", "bonus",  "game_id",   "office_id",  "free",count,double_count,free_count,bonus_count,
                                        fs_api_count,fs_cash_count,fs_lucky_count,fs_moon_count,fs_api_in,fs_cash_in,fs_lucky_in,fs_moon_in,fs_api_out,fs_cash_out,fs_lucky_out,fs_moon_out)
									values(:in, :out, :double_in, :double_out, :bonus, :game,  :oid, :free,:count,:doubleCount,:freeCount,:bonusCount,
                                        :fs_api_count,:fs_cash_count,:fs_lucky_count,:fs_moon_count,:fs_api_in,:fs_cash_in,:fs_lucky_in,:fs_moon_in,:fs_api_out,:fs_cash_out,:fs_lucky_out,:fs_moon_out)';

            db::query(Database::INSERT, $sql)->param(':in', (float) $in)
                    ->param(':out',(float) $out)
                    ->param(':bonus',(float) $bonus)
                    ->param(':double_in',(float) $double_in)
                    ->param(':double_out',(float) $double_out)
                    ->param(':game',(int) $bet->game_id)
                    ->param(':oid', auth::user()->office_id)
                    ->param(':free', $free)
                    ->param(':count', $count)
                    ->param(':doubleCount', $doubleCount)
                    ->param(':bonusCount', $bonusCount)
                    ->param(':freeCount', $freeCount)
                    ->param(':fs_api_count', $fs_api_count)
                    ->param(':fs_cash_count', $fs_cash_count)
                    ->param(':fs_lucky_count', $fs_lucky_count)
                    ->param(':fs_moon_count', $fs_moon_count)
                    ->param(':fs_api_in', $fs_api_in)
                    ->param(':fs_cash_in', $fs_cash_in)
                    ->param(':fs_lucky_in', $fs_lucky_in)
                    ->param(':fs_moon_in', $fs_moon_in)
                    ->param(':fs_api_out', $fs_api_out)
                    ->param(':fs_cash_out', $fs_cash_out)
                    ->param(':fs_lucky_out', $fs_lucky_out)
                    ->param(':fs_moon_out', $fs_moon_out)
                    ->execute();
        }

        $sql2='update counters_partners
                set "in"="in"+:in,
                "out"="out"+:out,
                "double_in"="double_in"+:double_in,
                "double_out"="double_out"+:double_out,
                "fs_cash_in"="fs_cash_in"+:fs_cash_in,
                "fs_cash_out"="fs_cash_out"+:fs_cash_out,
                "fs_lucky_in"="fs_lucky_in"+:fs_lucky_in,
                "fs_lucky_out"="fs_lucky_out"+:fs_lucky_out,
                "fs_moon_in"="fs_moon_in"+:fs_moon_in,
                "fs_moon_out"="fs_moon_out"+:fs_moon_out,
                "fs_api_in"="fs_api_in"+:fs_api_in,
                "fs_api_out"="fs_api_out"+:fs_api_out,
                "bonus"="bonus"+:bonus,
                "free"="free"+:free,
                "count"="count"+:count,
                double_count=double_count+:doubleCount,
                free_count=free_count+:freeCount,
                fs_api_count=fs_api_count+:fs_api_count,
                fs_cash_count=fs_cash_count+:fs_cash_count,
                fs_lucky_count=fs_lucky_count+:fs_lucky_count,
                fs_moon_count=fs_moon_count+:fs_moon_count,
                bonus_count=bonus_count+:bonusCount
                where game_id=:game
                AND office_id = :oid';

        $affected2 = db::query(Database::UPDATE,$sql2)->param(':in',(float) $in)
                        ->param(':out',(float) $out)
                        ->param(':bonus',(float) $bonus)
                        ->param(':double_in',(float) $double_in)
                        ->param(':double_out',(float) $double_out)
                        ->param(':game',(int) $bet->game_id)
                        ->param(':oid', auth::user()->office_id)
                        ->param(':free', $free)
                        ->param(':count', $count)
                        ->param(':doubleCount', $doubleCount)
                        ->param(':bonusCount', $bonusCount)
                        ->param(':freeCount', $freeCount)
                        ->param(':fs_api_count', $fs_api_count)
                        ->param(':fs_cash_count', $fs_cash_count)
                        ->param(':fs_lucky_count', $fs_lucky_count)
                        ->param(':fs_moon_count', $fs_moon_count)
                        ->param(':fs_api_in', $fs_api_in)
                        ->param(':fs_cash_in', $fs_cash_in)
                        ->param(':fs_lucky_in', $fs_lucky_in)
                        ->param(':fs_moon_in', $fs_moon_in)
                        ->param(':fs_api_out', $fs_api_out)
                        ->param(':fs_cash_out', $fs_cash_out)
                        ->param(':fs_lucky_out', $fs_lucky_out)
                        ->param(':fs_moon_out', $fs_moon_out)
                          ->execute();

        if ($affected2 == 0) {
            $sql2 = 'insert into counters_partners ( "in", "out", "double_in",  "double_out", "bonus",  "game_id",   "office_id",  "free",count,double_count,free_count,bonus_count,
                                        fs_api_count,fs_cash_count,fs_lucky_count,fs_api_in,fs_cash_in,fs_lucky_in,fs_api_out,fs_cash_out,fs_lucky_out)
									values(:in, :out, :double_in, :double_out, :bonus, :game,  :oid, :free,:count,:doubleCount,:freeCount,:bonusCount,
                                        :fs_api_count,:fs_cash_count,:fs_lucky_count,:fs_api_in,:fs_cash_in,:fs_lucky_in,:fs_api_out,:fs_cash_out,:fs_lucky_out)';

            db::query(Database::INSERT, $sql2)->param(':in', (float) $in)
                    ->param(':out',(float) $out)
                    ->param(':bonus',(float) $bonus)
                    ->param(':double_in',(float) $double_in)
                    ->param(':double_out',(float) $double_out)
                    ->param(':game',(int) $bet->game_id)
                    ->param(':oid', auth::user()->office_id)
                    ->param(':free', $free)
                    ->param(':count', $count)
                    ->param(':doubleCount', $doubleCount)
                    ->param(':bonusCount', $bonusCount)
                    ->param(':freeCount', $freeCount)
                    ->param(':fs_api_count', $fs_api_count)
                    ->param(':fs_cash_count', $fs_cash_count)
                    ->param(':fs_lucky_count', $fs_lucky_count)
                    ->param(':fs_moon_count', $fs_moon_count)
                    ->param(':fs_api_in', $fs_api_in)
                    ->param(':fs_cash_in', $fs_cash_in)
                    ->param(':fs_lucky_in', $fs_lucky_in)
                    ->param(':fs_moon_in', $fs_moon_in)
                    ->param(':fs_api_out', $fs_api_out)
                    ->param(':fs_cash_out', $fs_cash_out)
                    ->param(':fs_lucky_out', $fs_lucky_out)
                    ->param(':fs_moon_out', $fs_moon_out)
                    ->execute();
        }

        //apitype==0 - бесшовный , обновляем amount
        //apitype==1 - перенос баланса, amount обновляется в момент переноса
        if(auth::user()->office->apienable AND auth::user()->office->apitype==0) {
            db::query(Database::UPDATE,'update offices set amount=amount+:winall where id=:id')
                    ->param(':id',auth::user()->office_id)
                    ->param(':winall',$winAll)
                    ->execute();
        }

        database::instance()->commit();

        if($data['can_jp'] && PROJECT==2) {
            bet::saveJp($bet);
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
        if($poker_bet_id>0) {
            bet::$last_bet_id=$poker_bet_id;
        }

        if(!Kohana::$is_cli) {
            bet::tostat(arr::get($_GET,'stat',[]));
            if(th::isMoonGame($game)) {
                bet::tomoonstat(json_decode(arr::get($_GET,'stats','{}'),1));
            }
        }

        bet::topwin_collect($bet->game,$bet->office_id,$bet->win);

		bet::prepareToHistory([
            'id'=>$bet_id,
            'user_id'=>$bet->user_id,
            'last_bet_id'=>bet::$last_bet_id,
            'amount'=>$bet->amount,
            'win'=>$bet->win,
            'real_amount'=>$bet->real_amount,
            'real_win'=>$bet->real_win,
            'game'=>$bet->game,
            'info'=>$bet->info,
            'created'=>$bet->created,
            'come'=>$bet->come,
            'country'=>$bet->country,
            'result'=>$bet->result,
            'game_id'=>$bet->game_id,
            'type'=>$bet->type,
            'office_id'=>$bet->office_id,
            'balance'=>$bet->balance,
            'is_freespin'=>$bet->is_freespin,
            'initial_id'=>$bet->initial_id,
            'li'=>$bet->come,
            'di'=>0,
        ]);

        return $bet_id;
    }

    public static function topwin_collect($game,$office_id,$win) {
        if($win<=0) {
            return;
        }
        $time=time();
        $find=db::query(1,'select * from topwins where 
                            game=:game and 
                            office_id=:o_id and 
                            win>:win and 
                            time>=:time-60*60*24*30')
            ->param(':game',$game)
            ->param(':o_id',$office_id)
            ->param(':win',$win)
            ->param(':time',$time)
            ->execute()
            ->as_array();

        if(count($find)) {
            return;
        }

        $sql = "insert into topwins(time,game,office_id,win) values(:time,:game,:o_id,:win)";
        db::query(Database::INSERT,$sql)
            ->param(':game',$game)
            ->param(':o_id',$office_id)
            ->param(':win',$win)
            ->param(':time',$time)
            ->execute();
    }

    public static function tostat($params=[]) {

return;

        foreach($params as $k=>$vv) {
            if(!in_array($k,[
                    'date',
                    'office_id',
                    'sound_on',
                    'sound_off',
                    'mobile',
                    'pc',
                    'chrome_pc',
                    'chrome_ios',
                    'chrome_android',
                    'ios',
                    'android',
                    'safari_ios',
                    'mozilla_ios',
                    'mozilla_pc',
                    'mozilla_android',
                    'mac',
                    'total',
                    'other_browser_android',
                    'other_browser_ios',
                    'other_browser_pc',
                    'chrome_mac',
                    'mozilla_mac',
                    'safari_mac',
                    'opera_mac',
                    'vert_mobile',
                    'horizont_mobile',
                    'opera_pc',
                    'opera_win',
                    'chrome_win',
                    'mozilla_win',
                    'win',
                    'other_browser_mac',
                    'bettype',
                    'res1200',
                    'res991',
                    'res768',
                    'res480',
                    'res0',
                    'res1200v',
                    'res991v',
                    'res768v',
                    'res480v',
                    'res0v',
                    'other_os',
                    'safari_pc',
                    'safari_android',
                    'safari_win',
                    'other_browser_win',
            ])) {
                unset($params[$k]);
            }
        }

        if(empty($params)) {
            return;
        }

        $o_id = auth::user()->office_id;
        $date = date('Y-m-d');

//        $params['office_id']=auth::user()->office_id;
//        $params['date']=date('Y-m-d');
        $params['total']=1;

        $up_sql = [];

        foreach($params as $f=>$v) {
            if($f=='bettype') {

            }
            else {
                $up_sql[]=$f.'='.$f.'+'.$v;
            }
            $in_sql_vals[]='\''.$v.'\'';
        }

//        Database::instance()->escape($string);

        $in_sql_vals[]='\''.$date.'\'';
        $in_sql_vals[]='\''.$o_id.'\'';

        $up_sql = implode(',',$up_sql);
        $up_sql = 'update betstats set '.$up_sql;
        $up_sql.=' where office_id='.$o_id.' and date=\''.$date.'\' and bettype=\''.$params['bettype'].'\' returning office_id';

        $in_sql_keys = array_keys($params);
        $in_sql_keys[]='date';
        $in_sql_keys[]='office_id';

        $in_sql = 'insert into betstats('.implode(',',$in_sql_keys).') values ('.implode(',',$in_sql_vals).')';

        set_error_handler(function(int $number, string $message) {
            Kohana::$log->add(Log::ALERT,$message);
        });

        $r = Database::instance()->direct_query($up_sql);

        if(empty($r)) {
            Database::instance()->direct_query($in_sql);
        }

        restore_error_handler();

    }

    public static function tomoonstat($params=[]) {

        if(empty($params)) {
            return;
        }

        $an_type = $params['an_type'];
        $mu_type = $params['mu_type'];

        foreach($params as $k=>$vv) {
            if(!in_array($k,[
                'date',
                'office_id',
                'sound_on',
                'ios',
                'android',
                'pc',
                'mobile',
            ])) {
                unset($params[$k]);
            }
        }

        if(empty($params)) {
            return;
        }

        $o_id = auth::user()->office_id;
        $date = date('Y-m-d');

        $params['total']=1;

        $up_sql = [];

        foreach($params as $f=>$v) {
            $up_sql[]=$f.'='.$f.'+'.$v;
            $in_sql_vals[]='\''.$v.'\'';
        }

//        Database::instance()->escape($string);

        $in_sql_vals[]='\''.$date.'\'';
        $in_sql_vals[]='\''.$o_id.'\'';
        $in_sql_vals[]='\''.$an_type.'\'';
        $in_sql_vals[]='\''.$mu_type.'\'';

        $up_sql = implode(',',$up_sql);
        $up_sql = 'update moonstats set '.$up_sql;
        $up_sql.=' where office_id='.$o_id.' and date=\''.$date.'\' and an_type=\''.$an_type.'\' and mu_type=\''.$mu_type.'\' returning office_id';

        $in_sql_keys = array_keys($params);
        $in_sql_keys[]='date';
        $in_sql_keys[]='office_id';
        $in_sql_keys[]='an_type';
        $in_sql_keys[]='mu_type';

        $in_sql = 'insert into moonstats('.implode(',',$in_sql_keys).') values ('.implode(',',$in_sql_vals).')';

        set_error_handler(function(int $number, string $message) {
            Kohana::$log->add(Log::ALERT,$message);
        });

        $r = Database::instance()->direct_query($up_sql);

        if(empty($r)) {
            Database::instance()->direct_query($in_sql);
        }

        restore_error_handler();

    }
	
	public static $arrToHistory=[];
    public static function prepareToHistory(array $arr) {
        self::$arrToHistory+=$arr;
    }

    public static function putToHistory() {

        $arr=self::$arrToHistory;

        $arr['server']=arr::get($arr,'server',1);

        $params=[
            'id',
            'server',
            'user_id',
            'amount',
            'win',
            'real_amount',
            'real_win',
            'game',
            'info',
            'created',
            'come',
            'country',
            'result',
            'game_id',
            'type',
            'type_of_game',
            'owner',
            'office_id',
            'balance',
            'is_freespin',
            'initial_id',
            'lang',
            'linesMask',
            'strict_double',
            'chooser_btns',
            'gamble_suit_history',
            'hold',
            'last5_history',
            'linesValue',
            'pokerStep',
            'replace_sym',
            'wincard',
            's1',
            's2',
            's3',
            'currency_id',
            'li',
            'suite',
            'i1',
            'i2',
            'i3',
            'bonus',
            'bonus_all',
            'bonus_win',
            'd1',
            'd2',
            'd3',
            'last_bet_id',
            'ui1',
            'ui2',
            'ui3',
        ];

        $user_id = arr::get($arr,'user_id',auth::$user_id);

        if(empty($user_id)) {
            throw new Exception('no user_id');
        }

        $full_dir_path = [
            'history',
            date('Y'),
            date('m'),
			date('d'),
            date('H'),
            date('i'),
        ];

        $full_dir='';

        foreach ($full_dir_path as $dir) {

            if(empty($full_dir)) {
                $full_dir=APPPATH;
            }

            $full_dir.=$dir;

            if (!file_exists($full_dir) && !is_link($full_dir)) {
                mkdir($full_dir, 02777);
                chmod($full_dir, 02777);
            }

            $full_dir.=DIRECTORY_SEPARATOR;
        }

        $json_ready=[];

        foreach($arr as $p=>$v) {
            if(!in_array($p,$params)) {
                continue;
            }
            $json_ready[$p]=$v;
        }

        /*$not_found=array_diff($params,array_keys($json_ready));

        if(count($not_found)) {
            Kohana::$log->add(Log::ALERT,'not found param: ['.print_r($not_found,1).']');
        }*/

		th::atomicFileWrite($full_dir.DIRECTORY_SEPARATOR.date('Y').'_'.date('m').'_'.date('d').'_'.date('H').'_'.date('i').'_'.$user_id.'.json',json_encode($json_ready).PHP_EOL);

        //file_put_contents($full_dir.date('Y').'_'.date('m').'_'.date('d').'_'.date('H').'_'.date('i').'_'.$user_id.'.json',json_encode($json_ready).PHP_EOL,FILE_APPEND | LOCK_EX);

    }
}
