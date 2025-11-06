<?php

//логика игры

class game_slot_agt extends game_slot
{

    protected $_config_defaults = [];
    protected $_config          = [];
    protected $_game;
    protected $_calc;
    protected $_name='agt';

    protected function _commonInit() {

        //load all langs
        $langs = [];
        $d=I18n::$lang;
        foreach(array_keys(Kohana::$config->load('languages.lang')) as $l) {
            I18n::$lang=$l;
            foreach(Kohana::$config->load($this->_name.'.langs') as $e=>$k) {
                $langs[$l][$e]=__($k);
            }
        }
        I18n::$lang=$d;

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

        $redis = dbredis::instance();
        $redis->select(1);

        $jps=(bool) auth::user()->office->enable_jp;
        $currency=auth::user()->office->currency;
        $k_list = auth::user()->office->get_k_list();

        $last_game = auth::user()->last_game ?? -1;
        if(auth::user()->office->apitype==4 && $last_game!=-1 && $this->_game!=$last_game) {
            $last_game=-1;
        }

        $a= [
            "gamename" => $last_game,
            "dentab"    => isset($this->_config['dentab']) ? $this->_config['dentab'] : $this->_config_defaults['dentab'],
            "bets"      => isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'],
            "lines"     => [],
            "linesMask" => [],
            "user_id" => auth::$user_id,
            "langs"    => $langs,
            "lang"    => $l,
            'jpa'   => $jps,
            'gui'   => auth::user()->office->gameui ?? 1,
            'k_list'   => $k_list,
            "currency" => $currency->code,
            "need_convert_int" => in_array(auth::user()->office_id,[1040,1046]),
            "currency_code" => $currency->icon,
        ];

        return $a;
    }


    public function __construct($game)
    {
        $this->_game = $game;
        $class_name  = "Slot_{$this->_name}_" . ucfirst($this->_game);



        if(class_exists($class_name))
        {
            $this->_calc = new $class_name($this->_game);
        }
        else
        {
            $slotClass='Slot_'.$this->_name;
            $this->_calc = new $slotClass($this->_game);
        }

        $this->_config_defaults = Kohana::$config->load($this->_name);
        $this->_config          = Kohana::$config->load($this->_name.'/' . $game);

        $o = office::instance()->office();
        if($o->games_rtp && !empty($o->games_rtp)) {
            $this->_calc->forceBars($o->games_rtp);
        }


        if(!empty(auth::$user_id) && $bets_arr = auth::user()->bets_arr) {
            $bets_arr = explode(',',auth::user()->bets_arr);

            if(!empty($bets_arr)) {
                foreach($bets_arr as $k=>$v) {
                    $bets_arr[$k]= floatval($v);
                }
                $this->_config['bets']=$bets_arr;
            }
        }

        parent::__construct();
        $this->_check_keys();
    }

    public function bonus_game()
    {

        $this->_calc->SetFreeRunMode();

        $checkGame=false;

        if(auth::user()->office->apitype==4) {
            $checkGame=$this->_calc->game_id;
        }

        $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);
        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api');
        }

        if(!$this->_calc->isBonusMode && ($this->_calc->freeCountAll - $this->_calc->freeCountCurrent <= 0))
        {
            throw new Exception('freerun is disabled '.$this->_game.' '.auth::$user_id);
        }

        $extra_ans = [];

        $r = $this->_calc->bet('morefrees');

        if($r != 0)
        {
            throw new Exception('Error');

            return [
                    'error_code' => $r,
            ];
        }


        $balance = $this->amount() - $this->_calc->win_all;

        if($this->_config['bonus_double'] == 'all')
        {
            $balance = $this->amount() - $this->_calc->total_win_free;
        }

        $this->reload_session();

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "win"                 => $this->_calc->win_all,
                "linesMask"           => array_values($this->_calc->lightingLine()),
                "linesValue"          => $this->_session['lv'],
                "step"                => 0,
                'gamble_max_steps'    => $this->_config_defaults['max_double'] + 1,
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"             => $balance,
                'last_win_sum'        => $this->_calc->total_win_free,//TODO. Не совсем уместный параметр, но хотя бы работает. Надо смотреть в сторону lastWinSumBlur из клиента
                'bonus'               => game::data('freeCountAll') - game::data('freeCountCurrent'),
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => game::data('freeCountAll',0),
                "multiplier"          => game::data('multiplier',1),
                "last_bet_id"          => bet::$last_bet_id,
        ];

        $ans['extracomb'] = $this->_calc->extrasym();

        if($this->_session['freeCountAll'] > 0 && $this->_session['freeCountAll'] == $this->_session['freeCountCurrent'])
        {
            $ans['last_win_sum'] = $this->_session['total_win_free'];
        }

        $ans['session_total_win_free'] = $this->_session['total_win_free'];

        if($fs && $fs->loaded() && $fs->active!=0) {
            $ans['total_fs_win']=bcdiv($fs->sum_win,1,2);
            $ans['balance']=bcdiv($this->amount()-$fs->sum_win-$ans['win'],1,2);
        }

        $ans['gamble_stats'] = $this->gamble_stats();

        $ans = array_merge($ans,$extra_ans);
        if(auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] <= 0)
            {
                auth::user()->last_game = null;
                auth::user()->save();
            }
        }

        //bookofra

        if(in_array($this->_game,['pharaoh','pharaoh2'])) {
            $ans['replace_sym']=0;

            if($this->_calc->freerun) {
                $ans['replace_sym']=$this->_session['extra_param'];
            }
        }



        return $ans;
    }


    public function double($select)
    {

		if($this->_state()==3) {
            $this->_calc->SetFreeRunMode();
        }

        $checkGame=false;

        if(auth::user()->office->apitype==4) {
            $checkGame=$this->_calc->game_id;
        }

        $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);
        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api');
        }

        $this->_calc->select = !is_null($select) ? $select : math::random_int(0, 1);
        $this->_calc->double();

        $this->reload_session();

        $gamble_history                   = $this->_session['gamble_history'];
        array_unshift($gamble_history,$this->_calc->double_result);
        $gamble_history                   = array_slice($gamble_history,0,5);
        $this->_session['gamble_history'] = $gamble_history;

        $this->save();
        $ans                              = [
                "bonus"               => $this->_session['freeCountAll'] - $this->_session['freeCountCurrent'],
                "bonus_all"           => $this->_session['freeCountAll'],
                "step"                => $this->_session['step'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'] + 1,
                "win"                 => "".$this->_calc->win_all,
                "suite"               => $this->_calc->double_result,
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"             => ($this->amount() - $this->_calc->win_all),
                "last_bet_id"          => bet::$last_bet_id,
        ];

        $ans['gamble_stats'] = $this->gamble_stats();


        if($this->_session['freeCountAll'] > 0 && ($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] == 0))
        { //последний фриспин
            $ans['balance']      = $this->amount() - $this->_calc->total_win_free;
            $ans['last_win_sum'] = $this->_calc->total_win_free;
        }

        if(auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] <= $this->_session['freeCountCurrent'])
            {
                auth::user()->last_game = null;
                auth::user()->save();
            }
        }


        return $ans;
    }

    protected function _check_keys()
    {
        $save = false;

        $office=auth::user()->office;

        foreach(['win','is_bonus_game','is_gamble_game','bonus_win',
                'bonus_all','bonus_current','total_win_free','step',
                'li','bi','di','freerun','freeCountAll','freeCountCurrent',
                'freegames_start_from','extra_param','bonusdata'] as $key)
        {
            if(!isset($this->_session[$key]))
            {
                $this->_session[$key] = 0;
                $save                 = true;
            }
            if($key=='di') {
                $this->_session[$key] = (int) $office->default_dentab;

                if(!isset($office->get_k_list()[$this->_session[$key]])) {
                    $this->_session[$key]=0;
                    $save=true;
                }
            }
        }

        foreach(['comb','lm','lv','bonusdata'] as $key)
        {
            if(!isset($this->_session[$key]))
            {
                $this->_session[$key] = [];
                $save                 = true;
            }
        }

        foreach(['gamble_history'] as $key)
        {
            if(!isset($this->_session[$key]))
            {
                $this->_session[$key] = th::mixedRange([0,1,2,3],5);
                $save                 = true;
            }
        }


        if($save)
        {
            $this->save();
        }
    }

    public function init()
    {

        if(in_array(auth::user()->office_id,OFFICES_TEST_MODE) && (OFFICES_TEST_MODE_GAMES[0]=='*' || in_array($this->_game,OFFICES_TEST_MODE_GAMES))) {
            $this->_session['_dev_symbol']=0;
            $this->_session['_dev_symbol_count']=0;
            $this->_session['freeCountAll']=0;
            $this->save();
        }

        //for new users
        if(!isset($this->_session['comb']) || empty($this->_session['comb']))
        {
            $this->_calc->amount      = 0;
            $this->_calc->cline       = 0;
            $this->_calc->amount_line = 0;
            $this->_calc->spin();
            $this->_session['comb']   = array_values($this->_calc->sym());
            $this->_session['extracomb'] = $this->_calc->extrasym();
            $this->save();
        }
        else {
            $this->_session['total_win_free']   = 0;
            $this->save();
        }

        $a=$this->_commonInit();

        $a["lines"]=(isset($this->_config['staticlines'])) ? $this->_config['staticlines'] : array_values($this->_config['lines']);
        $a["linesMask"]=array_keys($this->_config['lines']);

        return $a;
    }

    protected function _state()
    {
        $state = 0;

        if($this->_session['freerun'] > 0 || ($this->_session['freeCountAll'] != $this->_session['freeCountCurrent']) || arr::get($this->_session,'bonusrun',0)==1)
        {
            $state = 3;
        }
        elseif($this->_session['win'] > 0 && $this->_session['step'] > 0)
        {
            $state = 2;
        }
        elseif($this->_session['win'] > 0 || $this->_session['total_win_free'] > 0)
        {
            $state = 1;
        }

        return $state;
    }

    public function restore()
    {

        //todo
        //баг в старых играх. если ласт гейм одна из старых игр, то она виснет. исправить!!!! down under при mode=free

        $state = $this->_state();
        //lucky lady bug
        //{"win":0,"amount":5,"lines":5,"comb":[3,3,2,6,0,9,8,1,5,11,4,1,3,3,6],"can_double":1,"can_bonus":0,"freeCountAll":15,"first_bet":0,"fg_level":1,"total_win_free":4578,"freeCountCurrent":15,"multiplier":3,"extra_param":7,"freegames_start_from":"amount","step":0,"is_bonus_game":0,"is_gamble_game":0,"bonus_win":0,"bonus_all":0,"bonus_current":0,"li":"4","bi":"0","di":"0","freerun":0,"bonusdata":0,"lm":[8260,0,0,0,0,0],"lv":[2500,0,0,0,0,0,0,0,0,0,0],"gamble_history":[3,3,0,1,0]}
        //dolphins bug
        //{"win":0,"amount":1,"lines":10,"comb":[2,7,2,11,11,8,0,6,3,4,5,8,5,9,8],"can_double":0,"can_bonus":0,"freeCountAll":0,"first_bet":0,"fg_level":0,"total_win_free":0,"freeCountCurrent":0,"multiplier":3,"step":0,"is_bonus_game":0,"is_gamble_game":0,"bonus_win":0,"bonus_all":0,"bonus_current":0,"li":"9","bi":"7","di":"0","freerun":0,"freegames_start_from":0,"extra_param":0,"bonusdata":0,"lm":[0,0,0,0,0,0,0,0,0,0,0],"lv":[0,0,0,0,0,0,0,0,0,0,0],"gamble_history":[3,2,1,2,3]}
        //coldspell bug
        //{"win":0,"amount":0.2,"lines":1,"comb":[6,7,0,4,3,1,4,10,6,7,11,11,2,2,2],"can_double":0,"can_bonus":0,"freeCountAll":0,"first_bet":0,"fg_level":0,"total_win_free":0,"freeCountCurrent":0,"multiplier":3,"extra_param":0,"freegames_start_from":"amount","is_bonus_game":0,"is_gamble_game":0,"bonus_win":0,"bonus_all":0,"bonus_current":0,"step":0,"li":"0","bi":"8","di":"0","freerun":0,"bonusdata":0,"lm":[0,0],"lv":[0,0,0,0,0,0,0,0,0,0,0],"gamble_history":[3,3,0,3,1]}
        //todo поставить чек на измененные bets_arr

        $bonus_win = 0;
        if($state == 3) {
            if($this->_session['freerun']==0 && $this->_session['freeCountAll']>0) {
                $bonus_win=$this->_session['freeCountAll'] - $this->_session['freeCountCurrent'];
            }
            if($this->_session['freerun']>0) {
                $bonus_win=$this->_session['freerun'];
            }
        }

        $ans = ["balance"             => $this->amount(),
                "comb"                => $this->_session['comb'],
                "extracomb"           => $this->_session['extracomb']??[],
                "state"               => $state,
                "win"                 => 0,
                'bonus'               => game::data('freeCountAll') - game::data('freeCountCurrent'),
                "bonus_win"           => 0,
                "bonus_all"           => game::data('freeCountAll',0),
                "last_win_sum"        => $state == 3 ? $this->_session['total_win_free'] : $this->_session['win'],
                "jackpots"            => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                'gamble_max_steps'    => $this->_session['freeCountAll'] > 0 ? 0 : $this->_config_defaults['max_double'],
                'step'                => $this->_session['step'],
                "gamble_suit_history" => $this->_session['gamble_history'],
                "user_id" => auth::$user_id,
        ];

        $ans['pay_table'] = $this->_config['pay'];
        $ans['amount']= (float) ($this->_session['amount'] ?? 0);
        $ans['gamble_stats'] = $this->gamble_stats();

        if($this->_game=='superhot201') {
            $ans['extracomb']=[
                [1,2,3,4,5,6],
                [1,2,3,4,5,6],
                [1,2,3,4,5,6],
                [1,2,3,4,5,6],
                [1,2,3,4,5,6],
            ];

            $ans['comb']= array_fill(0, 15, 7);
        }
//        if(!in_array($this->_game,['bookofrad','dolphinsd','luckyladycharmd','coldspell','columbusd','pharaohsgold3','wonderfulflute'])) {
//            $ans['bonus']=0;
//        }

        if(true || $this->_session['win'] || $state == 3)
        {
            $ans['li']         = $this->_session['li'];
            $ans['bi']         = $this->_session['bi'];
            $ans['di']         = $this->_session['di'];
            $ans['linesMask']  = $this->_session['lm'];
            $ans['linesValue'] = $this->_session['lv'];
            $ans['linecnt']    = count($this->_session['lm']);
        }

        $checkGame=false;

        if(auth::user()->office->apitype==4) {
            $checkGame=$this->_calc->game_id;
        }

        if(auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] <= 0)
            {
                auth::user()->last_game = null;
                auth::user()->save();
            }
        }

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
            $ans['fs_gamename'] = $fs->game->visible_name;
            $ans['fs_game'] = $fs->game->name;
            $ans['fs_id'] = $fs->id;

            $ans['balance']=bcdiv($this->amount()-$fs->sum_win,1,2);
//
            foreach(auth::user()->office->get_k_list() as $ki=>$k) {

                foreach($this->_config['bets'] as $bi=>$b) {
                    $r1 = ''.((float) $ans['amount']);

                    $vv = $k*$b;
                    if(!isset($this->_config['staticlines']) || empty($this->_config['staticlines'])) {
                        $vv*=$ans['li'];
                    }

                    $r2 = ''.$vv;

                    $f = explode('.',$r2);
                    if(count($f)==2 && strlen($f[1])==1) {
                        $r2.='0';
                    }

                    if($r1==$r2) {
                        $ans['di']=$ki;
                        $ans['bi']=$bi;
                        break;
                    }
                }
            }
        }

        return $ans;
    }

    public function get_balance()
    {
        $win = $this->_session['win'];
        $ans = [
                "jackpots" => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"  => 100 * ($this->amount() - $win),
        ];

        return $ans;
    }

    public function savechoose($chooser_btns)
    {
        $this->_session['chooser_btns']=$chooser_btns;
        $this->save();
    }

    public function finishjp()
    {
        $redis = dbredis::instance();
        $redis->select(1);
        $redis->delete('jpStartTime-'.auth::user()->office_id);
        $redis->delete('jpcards-'.auth::user()->office_id);
        $redis->delete('currjpcards-'.auth::user()->office_id);
        $redis->delete('alljpcards-'.auth::user()->office_id);
        $redis->delete('jpTime-'.auth::user()->office_id);

        for($numjp=0;$numjp<4;$numjp++) {
            $redis->delete('jpHotStart-'.auth::user()->office_id.'-'.$numjp);
            $redis->delete('jpHotStartSum-'.auth::user()->office_id.'-'.$numjp);
        }

        $redis->set('jpa-'.auth::user()->office_id,1);
	$redis->delete('jpBlock-'.auth::user()->office_id);

        $redis->select(0);
        return [];
    }

    public function lastjpcard(){
        $redis = dbredis::instance();
        $redis->select(1);
        $redis->set('currjpcards-'.auth::user()->office_id,$redis->get('alljpcards-'.auth::user()->office_id));
        $redis->select(0);
        return [];
    }

    public function nextjpcard($first=false)
    {
        $j = new jpcard();

        $office_id = auth::user()->office_id;

        $redis = dbredis::instance();
        $redis->select(1);

        if($first) {
            //выпал ДП
            //10 секунд монетки, 5 секунд показ руки, 2 минуты на розыгрыш, ? время на автоигру, 10 секунд на показ результата, 10 секунд монетки
            $t = 10+5+120+10+10+7;
            $redis->set('jpStartTime-'.auth::user()->office_id,1);
            $redis->expire('jpStartTime-'.auth::user()->office_id, $t);
        }

        $cards = json_decode($redis->get('jpcards-'.$office_id));
        $card=false;

        if($cards) {

            $card = array_shift($cards);
        }

        $currentCards = json_decode($redis->get('currjpcards-'.$office_id));


        $win = 0;

        $allCards=[];


        if(!$card || empty($cards)) {

            $jpModel = new Model_JackpotHistory();

            $allCards = json_decode($redis->get('alljpcards-'.$office_id));

            $level = $j->level($allCards);

            $jpLevel = $j->getJPNum($level);

            $jps = auth::user()->office->activeJackpots()->as_array();

            foreach($jps as $k=>$jp) {
                if($jpLevel>3) {
                    $win+=$jp->current;
                }
                else if($k==$jpLevel) {
                    $win=$jp->current;
                    break;
                }
            }



            $jpModel->user_id = auth::$user_id;
            $jpModel->office_id = auth::user()->office_id;
            $jpModel->game = $this->_game;
            $jpModel->cards = $allCards;
            $jpModel->level = $jpLevel;
            $jpModel->win = $win;
            $jpModel->triggernum = $redis->get('jpTriggerNum-'.$jpModel->office_id);
            $jpModel->triggersum = $redis->get('jpTriggerSum-'.$jpModel->office_id.'-'.$jpModel->triggernum);
            $jpModel->hotstartsum = $redis->get('jpHotStartSum-'.$jpModel->office_id.'-'.$jpModel->triggernum);
            $jpModel->triggertime = $redis->get('jpTriggerTime-'.$jpModel->office_id);
            $jpModel->hotstart=$redis->get('jpHotStart-'.$jpModel->office_id.'-'.$jpModel->triggernum);


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

            bet::setJP($jpModel);
            try {
                if (th::lockProcess('jpFinish'.'-'.auth::user()->office_id)){
                    bet::make($bet,'jp',null,true);
                    $this->finishjp();
                    //finishjp chages db to 0
                    $redis->select(1);
                    th::unlockProcess('jpFinish'.'-'.auth::user()->office_id);
                }
            } catch (Exception $ex) {
                logfile::create(date('Y-m-d H:i:s').' ['.auth::$user_id.'] '.' JPERROR: '.$ex->getTraceAsString(),'jpwin');
            }
        }
        else {
            $redis->set('jpcards-'.auth::user()->office_id,json_encode($cards));
            $currentCards[]=$card;
            $level = $j->level($currentCards);
            $redis->set('currjpcards-'.auth::user()->office_id,json_encode($currentCards));
        }


        $redis->select(0);

        $wincards = $j->wincards();

        return ['jpcard'=>$card,'winvalue'=>$win,'comb'=>$level, 'combname'=>$j->combName($level),
                'wincards'=>$wincards];
    }

    public function fsprocess($act,$fs_id)
    {
        $fs = new Model_Freespin($fs_id);


        if($fs->office_id<0) {
            $fs->updateAnonym(auth::user()->office_id);
        }

        $ans=[];

        if($fs->loaded()) {
            if($this->_calc->gameId()!=$fs->game_id) {
                throw new Exception('bad game fs');
            }
            if($act=='decline') {
                $fs->declineFreespins($fs->id);

                $ans['token']=auth::user()->api_key;
            }
            else {

                if($fs->amount>auth::user()->office->bet_max) {
                    throw new Exception('max bet fs amount');
                }

                $fs->activateFreespins($fs->id);
            }
            return $ans;
        }
        throw new Exception('bad fs');
    }

    public function save_win()
    {
        exit;
        $win = $this->_session['win'];
        //Обнуление скаттера в linesMask от зависания deluxe игр после restore
        $lm=$this->_session['lm'];
        if(count($lm)==11){
            $lm[0]=0;
            $this->_session['lm']=$lm;
        }

        //TODO поставить другое условие для бонусов
//		if($this->_session['total_win_free']==0) {
//			$this->clear(true);
//		}
//		else {
//			$win = $this->_session['total_win_free'];
//		}
        //TODO на пробу
        $this->clear(true);

        $ans = [
                "jackpots"         => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"          => $this->amount() * 100,
                "last_win_sum"     => $win * 100,
                'gamble_max_steps' => $win == 0 ? 0 : $this->_config_defaults['max_double'],
                'step'             => 0,
                //TODO параметр из документации. Не проверил, на что он влияет
                "real_win"         => 100 * ($this->amount() + $win),
        ];

        if(auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] <= $this->_session['freeCountCurrent'])
            {
                auth::user()->last_game = null;
                auth::user()->save();
            }
        }

        return $ans;
    }

    public function spin($li=0,$amount=0,$didx=0)
    {

        $checkGame=false;

        if(auth::user()->office->apitype==4) {
            $checkGame=$this->_calc->game_id;
        }

        $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);


        if($fs && $fs->loaded() && $this->_calc->gameId()!=$fs->game_id) {
            throw new Exception('bad game fs');
        }

        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api');
            $amount = $fs->amount;
            $li = $fs->lines;
        }


        $this->_calc->amount_line = $amount/$li;
        $this->_calc->cline       = $li;
        $this->_calc->amount      = $this->_calc->amount_line * $this->_calc->cline;

        $r = $this->_calc->bet();

        $this->reload_session();

        if($r != 0)
        {

            throw new Exception('Error');

            return [
                    'error_code' => $r,
            ];
        }

        if(!$this->_calc->freerun)
        {
        }
        else
        {
            auth::user()->last_game = $this->_game;
            auth::user()->save();
        }

        $this->save();

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "extracomb"                => $this->_calc->extrasym(), //prev and next reels
                "win"                 => "".$this->_calc->win_all, //flags JSON_NUMERIC_CHECK and JSON_PRESERVE_ZERO_FRACTION are broken in php 7+ — json_encode((float)8.8) returns "8.8000000000000007", and json_encode((float)8.8, JSON_NUMERIC_CHECK) and json_encode((float)8.8, JSON_PRESERVE_ZERO_FRACTION) return "8.8000000000000007" too.
                                                                    // the only way to fix this is setting "serialize_precision = -1" in php.ini
                "linesMask"           => $this->_session['lm'],
                "linesValue"          => $this->_session['lv'],
//		    'gamble_max_steps' => $this->_calc->freerun>0?0:$this->_config_defaults['max_double'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'],
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => array_values(bet::$jpdata['values']),
                'step'                => 0,
                "balance"             => bcdiv($this->amount() - $this->_calc->win_all,1,2),
                "bonus"               => $this->_calc->freerun,
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => $this->_session['freeCountAll'],
                'jpwin' => bet::$jpwin,
                "last_bet_id"          => bet::$last_bet_id,
        ];

        if(bet::$jpwin) {
            $j = $this->nextjpcard(true);
            $ans['jpcard'] = $j['jpcard'];
        }

        $ans['replace_sym']=$this->_calc->extra_param;
        $ans['comb_scatter_position']=$this->_session['comb_scatter_position']??-1;
        $ans['gamble_stats'] = $this->gamble_stats();

        if($fs && $fs->loaded() && $fs->active!=0) {
            $ans['fs_count']=(int) $fs->fs_count;
            $ans['fs_played']=(int) $fs->fs_played+1;
            $ans['total_fs_win']=bcdiv($fs->sum_win,1,2);
            $ans['balance']=bcdiv($this->amount()-$ans['win']-$fs->sum_win,1,2);
        }

        return $ans;
    }

    public function gamble_stats() {
        $c=new Model_Gamblestat(["game_id"=>$this->_calc->gameId(), "office_id"=>OFFICE]);
        if(!$c->loaded()) {
            return [
                    'red'=>'0',
                    'black'=>'0',
                    'suit0'=>'0',
                    'suit1'=>'0',
                    'suit2'=>'0',
                    'suit3'=>'0',
            ];
        }

        return [
                'red'=>$c->red/($c->red+$c->black),
                'black'=>$c->black/($c->red+$c->black),
                'suit0'=>$c->suit0/($c->red+$c->black),
                'suit1'=>$c->suit1/($c->red+$c->black),
                'suit2'=>$c->suit2/($c->red+$c->black),
                'suit3'=>$c->suit3/($c->red+$c->black),
        ];
    }

    public function clear($save = false)
    {
        $this->_session['win']  = 0;
        $this->_session['step'] = 0;

        if($save)
        {
            $this->save();
        }
        return $this;
    }

    public function amount() {
        $amount = auth::user(true)->amount();

        return $amount;
    }

}
