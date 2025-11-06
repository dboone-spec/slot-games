<?php

//логика игры

class game_slot_agt extends game_slot
{

    protected $_config_defaults = [];
    protected $_config          = [];
    protected $_game;
    /**
     * @var  Slot_Calc
     */
    protected $_calc;
    protected $_name='agt';

    protected function _commonInit() {

        //load all langs
        $langs = [];
        $d=I18n::$lang;

        $lkeys=array_keys(Kohana::$config->load('languages.lang'));


        $o=auth::user()->office;

        foreach($lkeys as &$l) {
            if(in_array($o->id,[5320,1629]) && !in_array($l,['ru','en','lt'])) {
                unset($l);
                continue;
            }
            I18n::$lang=$l;
            foreach(Kohana::$config->load($this->_name.'.langs') as $e=>$k) {
                $langs[$l][$e]=__($k);
            }
        }
        I18n::$lang=$d;

        $office_lang = $o->lang;
		
        $l = $office_lang;
        if(!empty(auth::user()->lang)) {
            $la = explode('-',auth::user()->lang);
            if(!empty($la[0])) {
                $l = $la[0];
            }

            if($l=='socen') {
                I18n::$lang=$l;
                $l='en';
                foreach(Kohana::$config->load($this->_name.'.langs') as $e=>$k) {
                    $langs[$l][$e]=__($k);
                }
            }

            if(isset($la[1]) && $la[1]=='no') {
                $langs = [$l=>$langs[$l]];
            }
        }

        if($this->_game=='slotscity') {
            unset($langs['ru']);
            $l='ua';
        }
		
		if($this->_game=='betsafelkl') {
            $l='lt';
        }
		
		if(empty($l) && th::isB2B($o->owner)) {
            $l='en';
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

        $display_currency=$currency->code;
        if(auth::user()->office->apitype==4 && $currency->code=='SS1') {
            $display_currency='SC';
        }
        if(auth::user()->office->apitype==4 && $currency->code=='GLD') {
            $display_currency='GC';
        }
        if(auth::user()->office->apitype==4 && $currency->code=='YOH') {
            $display_currency='YottaCash';
        }
        if(auth::user()->office->apitype==4 && $currency->code=='TOK') {
            $display_currency='Tokens';
        }

		$full_bets=[];
        foreach($this->_config['lines_choose'] as $lines_count) {
            $full_bets[$lines_count]=$this->_calc->game_model->getAllBets(auth::user()->office,$currency,$lines_count);
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
            "currency" => $display_currency,
            "mult" => $currency->mult,
            "need_convert_int" => in_array(auth::user()->office_id,[1040,1046]) || $currency->mult==0,
            "currency_code" => $currency->icon,
            "min_bet" => auth::user()->office->bet_min,
            "max_bet" => auth::user()->office->bet_max,
            "strict_double" => auth::user()->office->strict_double,
			'full_bets'=>$full_bets,
		'k_max_lvl'=>empty($o->k_max_lvl)?1:$o->k_max_lvl,
        ];

		if(th::isB2B($o->owner)) {
            $a['not_use_navigator_lang']=1;
        }

        return $a;
    }

    public function isUserActivityIsOK() {

        $cnt=6;

        if(auth::user()->office->owner==1042) {
            $cnt=3;
        }

        $key='__userbets_newlock__'.auth::$user_id.$this->_game;

        dbredis::instance()->set($key, 0, array ('nx', 'ex' => 2));

        $incr=dbredis::instance()->incr($key);

        if($incr>$cnt) {
            if($incr>$cnt*1.5) {
                dbredis::instance()->expire($key, 1);
            }
            return false;
        }

        return true;
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

        $max_win=(int) $o->max_win_limit;
        $this->_calc->setMaxWin($max_win);
    }

    public function bonus_game()
    {

        if(!$this->isUserActivityIsOK()) {
            throw new Exception('Error.');
        }

        $this->_calc->SetFreeRunMode();

        $checkGame=$this->_calc->game_id;

        $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);
        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api',$fs->src=='lucky');
        }

        if(!$this->_calc->isBonusMode && ($this->_calc->freeCountAll - $this->_calc->freeCountCurrent <= 0))
        {
            throw new Exception('freerun is disabled '.$this->_game.' '.auth::$user_id);
        }

        $extra_ans = [];

        $r = $this->_calc->bet();

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

        $mult=auth::user()->office->currency->mult ?? 2;

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "win"                 => bcdiv($this->_calc->win_all,1,$mult),
                "linesMask"           => $this->_calc->lightingLine(),
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
                "balance"             => bcdiv($balance,1,$mult),
                'last_win_sum'        => $this->_calc->total_win_free,//TODO. Не совсем уместный параметр, но хотя бы работает. Надо смотреть в сторону lastWinSumBlur из клиента
                'bonus'               => game::data('freeCountAll') - game::data('freeCountCurrent'),
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => $this->_calc->freeCountAll,
                "multiplier"          => game::data('multiplier',1),
                "last_bet_id"          => bet::$last_bet_id,
        ];

        foreach($ans['linesValue'] as &$lv) {
            $lv=rtrim(sprintf('%.'.$mult.'F',$lv),'0');
        }

        $ans['extracomb'] = $this->_calc->extrasym();

        if($this->_session['freeCountAll'] > 0 && $this->_session['freeCountAll'] == $this->_session['freeCountCurrent'])
        {
            $ans['last_win_sum'] = $this->_session['total_win_free'];
        }

        $ans['session_total_win_free'] = $this->_session['total_win_free'];

        //06.10.2023, в конце фригеймов показывало 0. исправлено как в исходном массиве
        $ans['session_total_win_free'] = $this->_calc->total_win_free;

        if($fs && $fs->loaded() && $fs->active!=0) {
            $ans['total_fs_win']=rtrim(sprintf('%.'.$mult.'F',$fs->sum_win),'0');
            $ans['balance']=bcdiv($this->amount()-$fs->sum_win-$ans['win'],1,$mult);
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

        if(!$this->isUserActivityIsOK()) {
            throw new Exception('Error.');
        }

		if($this->_state()==3) {
            $this->_calc->SetFreeRunMode();
        }

        $checkGame=$this->_calc->game_id;

        $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);
        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api',$fs->src=='lucky');
        }

        $this->_calc->select = !is_null($select) ? $select : math::random_int(0, 1);
        $this->_calc->double();

        $this->reload_session();

        $gamble_history                   = $this->_session['gamble_history'];
        array_unshift($gamble_history,$this->_calc->double_result);
        $gamble_history                   = array_slice($gamble_history,0,5);
        $this->_session['gamble_history'] = $gamble_history;
        $mult=auth::user()->office->currency->mult ?? 2;

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
                "balance"             => bcdiv(($this->amount() - $this->_calc->win_all),1,$mult),
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

            if(!Kohana::$is_cli) {
                //new user
                $stats=arr::get($_GET,'stats',[]);
                $stats['user_id']=auth::$user_id;
                $stats['ip']=$_SERVER['REMOTE_ADDR'] ?? null;

                th::saveUserDevice($this->_game,$stats);
            }
        }
        else {
            $this->_session['total_win_free']   = 0;
            $this->save();
        }

        $a=$this->_commonInit();

        $a["lines"]=array_values($this->_config['lines']);
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

        $mult=auth::user()->office->currency->mult ?? 2;

        $ans = ["balance"             => $this->amount(),
                "comb"                => $this->_session['comb'],
                "extracomb"           => $this->_session['extracomb']??[],
                "state"               => $state,
                "win"                 => 0,
                'bonus'               => $this->_calc->getTotalFreeCount() - game::data('freeCountCurrent'),
                "bonus_win"           => 0,
                "bonus_all"           => $this->_calc->getTotalFreeCount(),
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
        $ans['amount']= th::float_format($this->_session['amount'] ?? 0,$mult);
        $ans['gamble_stats'] = $this->gamble_stats();
        $ans['sess']=$this->_session;

        if(!empty($def_bet=auth::user()->office->default_bet)) {
            $ans['def_bet']=$def_bet;
        }
		
		if($this->_game=='betsafelkl') {
            $ans['def_bet']=1;
        }

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

        $checkGame=$this->_calc->game_id;

        if(auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] <= 0)
            {
                auth::user()->last_game = null;
                auth::user()->save();
            }
        }
		
		$u=auth::user();
        if($u->api=='9') {
            Api_BetConstruct::checkAndPayFS($u,$this->_game);
            $checkGame=false;
        }
		

        if($u->api=='10') {
            Api_Pinup::checkAndPayFS($u,$this->_game);
        }
		
		if($u->api=='12') {
            Api_SoftSwiss::checkAndPayAwards($u,$this->_calc->game_model);
            $checkGame=false;
        }

        $fs = auth::user()->getFreespins(auth::$user_id,false,false,$checkGame);

		if($u->api=='8' && $fs->fs_offer_type=='softgaming' && $fs->active==0) {
            $fs=Api_SoftGamings::checkAndActivateFS($u,$this->_calc->game_model,$fs);
        }

        if($fs && $fs->loaded() && in_array($u->api,['9','10','12']) && $fs->src=='api' && !empty($fs->gameids) && !in_array($this->_calc->game_id,$fs->gameids)) {
            $fs=false;
        }


        if($fs && $fs->loaded()) {
            $ans['fs_count']=(int) $fs->fs_count;
            $ans['fs_type']=$fs->src;
            $ans['fs_played']=(int) $fs->fs_played;
            $ans['total_fs_win']=th::float_format($fs->sum_win,$mult);
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
                    throw new Exception('game not available: '.$fsgame.' fsid: '.$fs->id);
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

                if(!in_array($fs->src,['cashback'])) {
                    $ans['li']=(int) Kohana::$config->load('agt/' . $game->name.'.lines_choose')[0];
                }


                $ans['fs_gamename']=$game->visible_name;
                $ans['fs_game']=$game->name;
            }
            else {
                $ans['fs_gamename'] = $fs->game->visible_name;
                $ans['fs_game'] = $fs->game->name;
            }
            $ans['fs_id'] = $fs->id;

            $ans['balance']=bcdiv($this->amount()-$fs->sum_win,1,$mult);
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

        $u=auth::user();

        //если игрок не отказывался от
        if($fs && !$fs->loaded() && $u->promo_started!==null) {

            $game=new Model_Game(['name'=>$this->_game]);

            $canJoinEvent=$u->checkEvents($game,true);

            //если есть промо, но уже закончилось
            if($canJoinEvent && $canJoinEvent->type=='promo' && $canJoinEvent->canPay($game,$u) && $u->promo_end_time) {
                if ($u->promo_inout > 0 && $u->promo_end_time + $canJoinEvent->time_to_collect >= time()) {
					
					logfile::create(date('Y-m-d H:i:s').' pay user '.$u->id.' '.$u->promo_inout.' win '.$u->promo_end_time.'; e_id: '.$canJoinEvent->id,'promocalc');
					
                    $ans['promo_win'] = bcdiv($u->promo_inout, 1, $mult);

                    $u->payForEvent($game,$canJoinEvent);

                    auth::user(true);
                    $ans['balance'] = bcdiv($this->amount(), 1, $mult);
                    $ans['promo_payed'] = 1;
                }
                //если прошло уже три часа, то нужно обнулить, также если отказались
                elseif($u->promo_end_time + $canJoinEvent->time_to_collect < time() || $u->promo_inout<=0) {
					
					logfile::create(date('Y-m-d H:i:s').' user '.$u->id.' promo end time of collecting '.$u->promo_end_time.'; e_id: '.$canJoinEvent->id,'promocalc');
					
                    $u->promo_inout = 0;
                    $u->promo_end_time = null;
                    $u->promo_started = null;
                    $u->save();
                }
            }
        }
		
		$refund_users=[
//            6025178=>2755.77,
//            5913640=>2992,
//            3472786=>472,
            //4600700=>90,

            //28.01

            //RUB

            //3801112=>695,
            //4600700=>300,
            //5461526=>1405,
//            5894204=>5000,

            //INR

//            3942580=>372.78,
//            5634088=>360,
            //6085148=>500,

            //27.01

//            3824178=>774,
            //5328398=>80,
//            5672286=>1200,

            //26.01

//            4240238=>964,
//            4654072=>4540,
            //5972380=>3090,
			
			//18.01 rub
            //5958220=>3000,
			
			//19.01 rub
            //3909840=>1995,
			
			//14.01
            //5461526=>1240,
            
            //25.01
            //5972156=>3000,
			
			//5639748=>10000,
			
			//возврат туземун
			//4216422=>4920+6009.6,
			//4216422=>5160,
			
			//5985424=>1500,
			
			//13.07.24
            //8678354=>17200
			//8553938=>19820,
			
			6014538=>2346,
			
			//возврат туземун 02.09.2024 за ставки 21.08
            //6355776=>700,
			
			//возврат туземун за бан по отменам
            6140736=>1500,
			
			//6082390=>205,
			//4600700=>4480,
			
			//18.09.24
            8317776=>5000,
            5985128=>4980,
        ];

		/*if(time()<(1712664121+Date::WEEK*2)) {
            foreach(file(APPPATH.'infinrefund.csv') as $row) {
                $csv=explode(';',$row);
                $sum=str_replace(' ','',$csv[4]);
                $sum=str_replace(',','.',$sum);
                $refund_users[$csv[1]]=floatval($sum);
            }
        }*/

        if(isset($refund_users[$u->id]) && dbredis::instance()->setNx('refundUserPromo3-'.$u->id,1)) {

            $game=new Model_Game(['name'=>$this->_game]);

            $promo_win = $refund_users[$u->id];

            if(isset($ans['promo_win'])) {
                $promo_win+=(float) $ans['promo_win'];
            }

            $ans['promo_win']=bcdiv($promo_win, 1, $mult);

            $u->promo_inout=$promo_win;
            $u->payForEvent($game);

            auth::user(true);
            $ans['balance'] = bcdiv($this->amount(), 1, $mult);
            $ans['promo_payed'] = 1;

            th::ceoAlert('user '.$u->id.' was refund for promo! delete it from code and redis');
        }

        return $ans;
    }

    public function get_balance()
    {
        $u=auth::user();
        if($u->api==6) {
            $balance=gameapi::instance(6)->checkBalance($u->id,$u->office_id,$u->office->gameapiurl);
            if(!$balance) {
                throw new Exception_ApiResponse('cant get balance');
            }
            $u->amount="".$balance;
            $u->save();
        }

        $ans = [
            "balance"  => $u->amount(),
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
		$redis->delete('jpTriggerBetId-'.auth::user()->office_id);

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
			$jpModel->trigger_bet_id = $redis->get('jpTriggerBetId-'.$jpModel->office_id);
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
			$bet['trigger_bet_id']=$jpModel->trigger_bet_id;

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

    public function promo()
    {
        $ans = [];

        $game = new Model_Game($this->_calc->gameId());
        $u = auth::user();
        $events = $u->futureAndNowEvents($u->office_id,['progressive','promo']);

		$timezone=$u->office->currency->timezone;

		$promo_event_id=0;

        $prepared_events = [];
        if ($events) {
            foreach ($events as $event) {

                if (!$event->isNotPlayed(auth::user(),true)) {
                    continue;
                }

				if(!!$u->office->check_new_ls && $event->isNewPlayer($u)) {
                    continue;
                }

                $e = new stdClass();
                $e->next_begining_time = $event->startTime();
                $e->fs_amount = $event->fs_amount;
                $e->fs_count = $event->fs_count;
                $e->type = $event->type;
                $e->banner = 'images/banners/luckyspins1.jpg';
                $e->duration = $event->duration;
                $e->time_to_collect = $event->time_to_collect;
                $e->max_win = $event->max_payout;
                $e->ends = (int) $event->ends;
                $e->starts = (int) $event->starts;
                $e->id = $event->id;
                $e->games = $event->gameList(true);
				$e->timezone=$timezone;
				$e->when='Every '.$event->getDowName();
				
				if($e->type=='promo') {
                    $promo_event_id=$e->id;
                }

                $prepared_events[] = $e;
            }
            usort($prepared_events, function ($a, $b) {
                if ($a->next_begining_time == $b->next_begining_time)
                    return 0;
                return (($a->next_begining_time < $b->next_begining_time) ? -1 : 1);
            });
        }

        $mult = $u->office->currency->mult ?? 2;

        if(auth::user()->office->enable_bia>0) {
            //DS
            $last_calc = $u->last_bonus_calc ?? $u->last_bet_time;
            $bonus_coeff = ($u->bonus_coeff ?? $u->office->bonus_coeff);

            $_cache_time=10*60;

            $cache_key = 'calc_dsback_betsjson_'.$u->id;
            dbredis::instance()->select(5);

            $jsonbets=dbredis::instance()->get($cache_key);

            if (!$jsonbets) {
                $bets = $u->getBetsForDS();

                dbredis::instance()->set($cache_key, json_encode($bets));
                dbredis::instance()->expire($cache_key, $_cache_time);
                dbredis::instance()->select(0);
            }
            else {
                $bets = json_decode($jsonbets,1);
            }

            $sum_all = $bets['sum'];
            $sumfsback = 0;
            $fs_count = 0;
            $fs_one_amount = 0;


            if ($sum_all > 0 && !empty($bets['ds_info'])) {
                
                $bets_values=array_values($bets['ds_info']);
                
                $c = $u->calc_fsback($sum_all * $bonus_coeff, $bets_values[0]['game'], $bets_values[0]['game_id']);
                if ($c) {
                    $sumfsback = rtrim(sprintf('%.' . $mult . 'F', $c['win']), '0');
                    $fs_count = floor($c['win'] / $c['zzz']);
                    $fs_one_amount = rtrim(sprintf('%.' . $mult . 'F', $c['zzz']), '0');
                }
            }
            $ans['ds'] = [
                'coeff' => $bonus_coeff,
                'sum_in' => $sum_all,
                'sumfsback' => $sumfsback,
                'fs_count' => $fs_count,
                'fs_one_amount' => $fs_one_amount,
                'next_time' => ($u->last_bet_time ?? time()) + $u->office->bonus_diff_last_bet * 60 * 60,
            ];
        }

        $fs = auth::user()->getFreespins(auth::$user_id,false,false);

        if($fs && $fs->loaded() && !empty($fs->gameids) && count($fs->gameids)>1) {
            $ans['availablefs']=[
                'games'=>$fs->games(),
                'fs_amount'=>$fs->amount,
                'fs_count'=>(int) ($fs->fs_count-$fs->fs_played),
            ];
        }
        elseif($promo_event_id) {
            $promo_popup_show=0;
            $u=auth::user();

            $k='promoStarted'.date('Ymd').'-'.$u->id.'-'.$promo_event_id;
            dbredis::instance()->select(0);

            $e=new Model_Event($promo_event_id);

            //показываем окно акции
            if($e->checkEventIfReady() && $u->promo_started!==null && $u->promo_started>0 && dbredis::instance()->set($k, 1, array ('nx', 'ex' => mktime(23,59,59)-time()))) {
                $promo_popup_show=1;
				logfile::create(date('Y-m-d H:i:s').' user '.$u->id.' show promo popup: '.$u->promo_started,'promocalc');
            }
            $ans['promo_popup_show']=$promo_popup_show;
        }

        $ans['events']=$prepared_events;
        $ans['topwins']=service::topWins(OFFICE);

        return $ans;
    }
    public function eventprocess() {
		exit;
        $u=auth::user();
		
        logfile::create(date('Y-m-d H:i:s').' user '.$u->id.' before declined promo: promo_started: '.$u->promo_started,'promocalc');

        $u->promo_inout = 0;
        $u->promo_end_time = $u->promo_started;
        $u->save();

        $e=new Model_Event(arr::get($_GET,'event_id'));

        if($e->loaded() && $e->checkEventIfReady()) {
            Model_Event::updateStats([
                'event_id'=>arr::get($_GET,'event_id'),
                'office_id'=>$u->office_id,
                'cancel_count'=>1,
                'created'=>time(),
                'date'=>date('Y-m-d'),
            ]);
        }
		
		Model_Event::updateStats([
            'event_id'=>arr::get($_GET,'event_id'),
            'office_id'=>$u->office_id,
            'cancel_count'=>1,
            'created'=>time(),
            'date'=>date('Y-m-d'),
        ]);
		
		logfile::create(date('Y-m-d H:i:s').' user '.$u->id.' declined promo','promocalc');
		
        return [
            'user_id'=>$u->id
        ];
    }
    public function fscheck() {

        $ans=[];

		$u=auth::user();

        $game=new Model_Game($this->_calc->gameId());
        $canJoinEvent=auth::user()->checkEvents($game);

        if($canJoinEvent) {
            auth::user()->joinEvent($game,$canJoinEvent);
        }
        elseif(auth::user()->office->apitype==4 && !in_array($game->name,['keno','acesandfaces','jacksorbetter','tensorbetter']+th::getMoonGames())) {
            $fs = auth::user()->getFreespins(auth::$user_id,false,false,$game->id);
            if(!$fs || !$fs->loaded()) {
                auth::user()->pay_bia(false,$game->name,$game->id);
            }
        }
        else {
            auth::user()->pay_bia(false,$game->name,$game->id);
        }


        $checkGame=false;

        if(auth::user()->office->apitype==4) {
//            $checkGame=$this->_calc->game_id;
        }

		//нужно убрать проверку, т.к. если есть наши фриспины и есть фс с апи - будет ошибка.
        //TODO проверить остальные интеграции, либо сделать по другому (как бетконстракт ниже)
        if($u->api=='8') {
            $checkGame=$this->_calc->game_id;
        }

		if($u->api=='12') {
            $checkGame=false;
        }
		
        $fs = auth::user()->getFreespins(auth::$user_id,false,false,$checkGame);
		
		if($fs && $fs->loaded() && in_array($u->api,['9','12']) && $fs->src=='api' && !empty($fs->gameids) && !in_array($this->_calc->game_id,$fs->gameids)) {
             $fs=false;
         }

        if($fs && $fs->loaded()) {
            $auto=false;
            $retry=false;

            if(empty($fs->starttime) || (in_array($fs->src,['cashback','lucky']) && $fs->active==0 && $fs->updated<=3)) {
                $fs->starttime=time();
                $fs->save();
            }

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

                $retry=true;
            }

            if($fs) {

                $mult=auth::user()->office->currency->mult ?? 2;

                $ans['fs_count']=(int) $fs->fs_count;
                $ans['fs_type']=$fs->src;
                $ans['fs_played']=(int) $fs->fs_played;
                $ans['total_fs_win']=th::float_format($fs->sum_win,$mult);
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

                $ans['balance']=bcdiv($this->amount()-$fs->sum_win,1,$mult);
            }

            $ans['recheck'] = $retry;
        }
        return $ans;
    }

    public function anothergame() {
        $u=auth::user();
        $o=$u->office;
        if($o->apitype==4) {
            $api=new Api_Infin();
            $api->guid=$api->getLastCustomSessionId($u->id,$u->api_session_id);
            $api->getUrl($o->gameapiurl);
            $api->reenter($u,$this->_game);
        }
        return ['token'=>$u->api_key];
    }

    public function fsprocess($act,$fs_id)
    {
        $fs = new Model_Freespin($fs_id);


        if($fs->office_id<0) {
            $fs->updateAnonym(auth::user()->office_id);
        }

        $ans=[];

        if($fs->loaded()) {
            if(!empty($fs->gameids)) {
                if(!in_array($this->_calc->gameId(),$fs->gameids)) {
                    throw new Exception('bad game fs');
                }
            }
            elseif($this->_calc->gameId()!=$fs->game_id) {
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
				
				$ans['token']=auth::user()->api_key;
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

        if(!$this->isUserActivityIsOK()) {
            throw new Exception('Error.');
        }

        $freeCountAll = $this->_calc->getTotalFreeCount();
        $freeCountCurrent = game::data('freeCountCurrent', 0);

        if($freeCountAll>0 && $freeCountCurrent != $freeCountAll)
        {
            throw new Exception('spin is disabled. freerun mode is active '.$this->_game.' '.auth::$user_id.'; freeCountCurrent: '.$freeCountCurrent.'; freeCountAll: '.$freeCountAll);
        }

        $checkGame=$this->_calc->game_id;
		
		$u=auth::user();

        if(in_array($u->api,['9','12'])) {
            $checkGame=false;
        }

        $fs = $u->getFreespins(auth::$user_id,false,true,$checkGame);

        $betconstructFSGame=!($u->api=='9' && $fs->src=='api' && !empty($fs->gameids) && !in_array($this->_calc->game_id,$fs->gameids));

        if($fs && $fs->loaded() && in_array($u->api,['9','12']) && $fs->src=='api' && !empty($fs->gameids) && !in_array($this->_calc->game_id,$fs->gameids)) {
            $fs=false;
        }

        if($fs && $fs->loaded()) {
            if(!empty($fs->gameids) && !in_array($this->_calc->gameId(),$fs->gameids)) {
                throw new Exception('bad game fs');
            }
            if(empty($fs->gameids) && $this->_calc->gameId()!=$fs->game_id) {
                throw new Exception('bad game fs');
            }
        }

        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api',$fs->src=='lucky');
            $amount = $fs->amount;
            $li = $fs->lines;

            if(!empty($fs->gameids) && count($fs->gameids)>1) {
                $li=Kohana::$config->load('agt/' . $this->_game.'.lines_choose')[0];
            }

            if($fs->src=='cashback') {
                $this->_calc->forceBars('98');
            }

            if($fs->src=='lucky') {
                $this->_calc->forceBars('98');
            }
        }

        $mult=auth::user()->office->currency->mult ?? 2;

        $this->_calc->amount_line = $amount/$li;
        $this->_calc->cline       = $li;
        $this->_calc->amount      = $this->_calc->amount_line * $this->_calc->cline;

        $r = $this->_calc->bet();

        $this->reload_session();

        if($r != 0)
        {

            throw new Exception('Error ['.$r.']');

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

        $win=$this->_calc->win_all;

        $answin=$win;

        if($mult>0) {
            $answin=rtrim(sprintf('%.'.$mult.'F',$win),'0');
        }

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "extracomb"                => $this->_calc->extrasym(), //prev and next reels
                "win"                 => $answin, //flags JSON_NUMERIC_CHECK and JSON_PRESERVE_ZERO_FRACTION are broken in php 7+ — json_encode((float)8.8) returns "8.8000000000000007", and json_encode((float)8.8, JSON_NUMERIC_CHECK) and json_encode((float)8.8, JSON_PRESERVE_ZERO_FRACTION) return "8.8000000000000007" too.
                "linesMask"           => $this->_session['lm'],
                "linesValue"          => $this->_session['lv'],
//		    'gamble_max_steps' => $this->_calc->freerun>0?0:$this->_config_defaults['max_double'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'],
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => array_values(bet::$jpdata['values']),
                'step'                => 0,
                "balance"             => bcdiv($this->amount() - (float) bcdiv($this->_calc->win_all,1,$mult),1,$mult),
                "bonus"               => $this->_calc->freerun,
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => $this->_session['freeCountAll'],
                'jpwin' => bet::$jpwin,
                "last_bet_id"          => bet::$last_bet_id,
        ];

        if($mult>0) {
            foreach($ans['linesValue'] as &$lv) {
                $lv=rtrim(sprintf('%.'.$mult.'F',$lv),'0');
            }
        }


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
            $ans['total_fs_win']=rtrim(sprintf('%.'.$mult.'F',$fs->sum_win),'0');
            $ans['balance']=bcdiv($this->amount()-$ans['win']-$fs->sum_win,1,$mult);
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
