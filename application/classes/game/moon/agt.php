<?php

class game_moon_agt extends game_slot_agt
{

    public function init()
    {
        //for new users
        if(!isset($this->_session['user_id']) || empty($this->_session['user_id']))
        {
            $this->_calc->amount      = 0;
            $this->_calc->cline       = 0;
            $this->_calc->amount_line = 0;
            $this->_session['total_win']   = 0;
            $this->_session['user_id']   = auth::$user_id;
            $this->_session['extracomb'] = [];
            $this->_session['betcoin'] = 0;
            $this->save();
        }

        //load all langs
        $langs=th::getLangsTranslate();

        $office_lang = auth::user()->office->lang;
        $l = $office_lang;
        if(!empty(auth::user()->lang)) {
            $la = explode('-',auth::user()->lang);
            if(!empty($la[0])) {
                $l = $la[0];
            }
            if(isset($la[1]) && $la[1]=='no') {
                $langs = [$l=>$langs[$l]];
            }
        }

        $k_list = auth::user()->office->get_k_list();

        $ans = ["balance"   => $this->amount(),
                "gamename"  => auth::user()->last_game ?? -1,
                "dentab"    => isset($this->_config['dentab']) ? $this->_config['dentab'] : $this->_config_defaults['dentab'],
                "langs"    => $langs,
                "bets"      => isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'],
                "lines"     => [],
                "linesMask" => [],
                "user_id" => auth::$user_id,
                "lang"    => $l,
                'jpa'   => 0,
                'gui'   => auth::user()->office->gameui ?? 1,
                'k_list'   => $k_list,
                "currency" => auth::user()->office->currency->code,
                "need_convert_int" => in_array(auth::user()->office_id,[1040,1046]),
                "currency_code" => auth::user()->office->currency->icon,
				'full_bets'=>$this->_calc->game_model->getAllBets(auth::user()->office,auth::user()->office->currency,1)
        ];


        $checkGame=$this->_calc->game_id;

        $fs = auth::user()->getFreespins(auth::$user_id,false,false,$checkGame);

        if($fs->loaded()) {
            $ans['fs_count']=(int) $fs->fs_count;
            $ans['fs_type']=$fs->src;
            $ans['fs_played']=(int) $fs->fs_played;
            $ans['total_fs_win']=$fs->sum_win;
            $ans['fs_active']=(int) $fs->active;
            $ans['fs_created']=(int) $fs->created;
            $ans['li'] = (int) $fs->lines;
            $ans['amount'] = $fs->amount;
            if(!empty($fs->gameids)) {

                $fsgame=in_array($this->_calc->game_id,$fs->gameids)?
                    $this->_calc->game_id:
                    $fs->gameids[array_rand($fs->gameids)];

                $game=new Model_Game($fsgame);
                if(!$game->loaded()) {
                    throw new Exception('wtf game');
                }
                if($game->show!=1) {
                    throw new Exception('game not available: '.$fsgame);
                }
                $og = new Model_Office_Game([
                    'office_id' => auth::user()->office_id,
                    'game_id' => $game->id,
                ]);

                if(!$og->loaded()) {
                    throw new Exception('game not available: '.$fsgame.' '.auth::user()->office_id);
                }

                if($og->enable == 0) {
                    throw new Exception('game not available');
                }

                $ans['li']=(int) Kohana::$config->load('agt/' . $game->name.'.lines_choose')[0];

                $ans['fs_gamename']=$game->visible_name;
                $ans['fs_game']=$game->name;
            }
            else {
                $ans['fs_gamename'] = $fs->game->visible_name;
                $ans['fs_game'] = $fs->game->name;
            }
            $ans['fs_id'] = $fs->id;

            $ans['balance']=bcdiv($this->amount()-$fs->sum_win,1,2);
        }

        return $ans;
    }

    public function restore()
    {
        $a = parent::restore();
        $pay=(array) Kohana::$config->load('agt/'.$this->_game)['pay'];
        $a['pay_table']=$pay;
        $a['li']=$this->_session['betcoin']??0;
        $a['last5_history']=$this->_session['history']??[];
        $a['win']=$this->_session['win']??0;
        $a['current_win_level']=$this->_session['current_win_level']??0;
        return $a;
    }

    public function save_win()
    {

        $this->clear(true);

        $a=[];
        $a['current_win_level']=$this->_session['current_win_level'];

        return $a;
    }

    public function clear($save = false)
    {
        $this->_session['current_win_level']=0;
        $this->_session['total_win']=0;
        $this->_session['betcoin']=0;
        $this->_session['history']=[];

        parent::clear($save);
    }

    public function isUserActivityIsOK() {

		
        $key='__userbets_lock__'.auth::$user_id.$this->_game;

        if (dbredis::instance()->setNx($key, 1)){
            dbredis::instance()->expire($key, 4);
        }

        $limit=13;
        if(auth::user()->office->owner==1042) {
            $limit=4;
        }

        if(dbredis::instance()->incr($key)>$limit) {
            return false;
        }

        return true;
    }

    public function spin($li=0,$amount=0,$didx=0)
    {

        $win_amount=arr::get($_GET,'win',0);
        $round_id=arr::get($_GET,'round_id',0);

        $last_moon_round = db::query(1,'select id from moon_results where game_id=:game_id and finished is null order by 1 desc limit 1')->param(':game_id',$this->_calc->gameId())->execute()->as_array();

        $take_win_auto_play=arr::get($_GET,'autotake',-1)==='1' && $win_amount>0;

        if((empty($last_moon_round) || $last_moon_round[0]['id']!=$round_id)) {
            //если автоигра
            if($take_win_auto_play) {
                $last_moon_round = db::query(1,'select id,finished from moon_results where game_id=:game_id and finished is not null order by 1 desc limit 1')->param(':game_id',$this->_calc->gameId())->execute()->as_array();

                if(empty($last_moon_round) || $last_moon_round[0]['id']!=$round_id || time()+1<$last_moon_round[0]['finished']) {
                    throw new Exception('Error. Round not ok2. round_id: '.$round_id.'; autotake: '.arr::get($_GET,'autotake',-1));
                }
            }
            else {
                throw new Exception('Error. Round not ok. round_id: '.$round_id.'; game: '.$this->_game.'; game_id: '.$this->_calc->gameId().'; autotake: '.arr::get($_GET,'autotake',-1));
            }
        }

        if($win_amount==0 && !$this->isUserActivityIsOK()) {
            throw new Exception('Error.');
        }

        $checkGame=$this->_calc->game_id;

        $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);


        if($fs && $fs->loaded()) {
            if(!empty($fs->gameids) && !in_array($this->_calc->gameId(),$fs->gameids)) {
                throw new Exception('bad game fs');
            }
            if(empty($fs->gameids) && $this->_calc->gameId()!=$fs->game_id) {
                throw new Exception('bad game fs');
            }
        }

        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api',$fs->src=='lucky',$fs->src=='moon');
            if($win_amount==0) {
                $amount = $fs->amount;
            }
        }

        if($win_amount>0 && !!arr::get($_GET,'is_freespin',false)) {
            $this->_calc->setFreeSpinMode($fs->src=='api',$fs->src=='lucky',$fs->src=='moon');
        }

        $this->_calc->amount      = $amount;
        $this->_calc->come      = arr::get($_GET,'rate',0);
        $this->_calc->conn_lost      = !!arr::get($_GET,'force',0);
        $this->_calc->round_id      = $round_id;
        $this->_calc->bet_type      = arr::get($_GET,'type');
        $this->_calc->bet_id      = arr::get($_GET,'bet_id');

        $r = $this->_calc->bet($win_amount);

        $time=time();

        if($r != 0)
        {
            throw new Exception('Error: '.$r);

            return [
                    'error_code' => $r,
            ];
        }

        $mult=auth::user()->office->currency->mult ?? 2;

        $win_all = $this->_calc->win_all;

        $answin=$win_all;

        if($mult>0) {
            $answin=rtrim(sprintf('%.'.$mult.'F',$win_all),'0');
        }

        $ans = [
                "win"                => $answin,
                "rate"                => $this->_calc->come,
                "bet_id"                => bet::$last_bet_id,
                "user_id"                => auth::$user_id,
                "time"                => $time,
                "amount"                => $amount,
                "is_freespin"                => (int) $this->_calc->isFreespin,
                "session_id"                => auth::getCustomSessionId(auth::$user_id,md5(auth::$token)),
				"game_session_id"                => auth::getCustomGameSessionId(auth::$user_id,$this->_game,md5(auth::$token)),
                "balance" => bcdiv($this->amount(),1,2),
        ];

        $fs->reload();

        if($win_amount==0 && $fs && $fs->loaded() && $fs->active!=0) {
            $ans['fs_count']=(int) $fs->fs_count;
            $ans['fs_played']=(int) $fs->fs_played+1;
            $ans['total_fs_win']=bcdiv($fs->sum_win,1,2);
            $ans['balance']=bcdiv($this->amount()-$ans['win']-$fs->sum_win,1,2);
        }

        return $ans;
    }

    public static function updateUserBetHistory($user_id,$params) {
        if($params['win']<=0) {
            return;
        }
        //update only wins
        $redis=dbredis::instance();
        $redis->select(6);

        $bet=json_decode($redis->get("bH".$user_id.'-'.$params['initial_id']),1);
        if($bet) {
            $bet['win']=$params['win'];
            $bet['rate']=$params['result'];

            $redis->set("bH".$user_id.'-'.$params['initial_id'],json_encode($bet));
        }

        $redis->select(0);
    }

}
