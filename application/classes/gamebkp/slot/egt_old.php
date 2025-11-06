<?php

//логика игры

class game_slot_egt extends game_slot
{

    protected $_config_defaults = [];
    protected $_config          = [];
    protected $_game;
    protected $_calc;

    public function __construct($game)
    {
        $this->_game = $game;
        $class_name  = 'Slot_Egt_' . ucfirst($this->_game);
        if(class_exists($class_name))
        {
            $this->_calc = new $class_name($this->_game);
        }
        else
        {
            $this->_calc = new Slot_Egt($this->_game);
        }

        $this->_config_defaults = Kohana::$config->load('egt');
        $this->_config          = Kohana::$config->load('egt/' . $game);

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

        if(!$this->_calc->isBonusMode && ($this->_calc->freeCountAll - $this->_calc->freeCountCurrent <= 0))
        {
            throw new Exception('freerun is disabled '.$this->_game.' '.auth::$user_id);
        }

        $use_freespins = auth::user()->use_freespins_now($this->_game);

        if($use_freespins) {
            $this->_calc->SetFreeSpinMode();
        }

        $extra_ans = [];

        $r = $this->_calc->bet();

        if($r != 0)
        {
            return [
                    'error_code' => $r,
            ];
        }

        $lineWin = $this->_calc->win;
        foreach($lineWin as &$l)
        {
            $l *= 100;
        }

        $balance = $this->amount() * 100 - $this->_calc->win_all * 100;

        if($use_freespins) {
            $fs = auth::user()->freespins_info($this->_game);
            $fs->total_win_freespin += $this->_calc->win_all;
            $fs->win_balance += $this->_calc->win_all;
            $fs->save();
        }

        if($this->_config['bonus_double'] == 'all')
        {
            $balance = $this->amount() * 100 - $this->_calc->total_win_free * 100;
        }

        $freespin_balance=false;

        $use_freespins_now = auth::user()->use_freespins_now($this->_game);
        if ($use_freespins_now) {
            $freespin_balance  = ($fs->total_win_freespin - $this->_calc->win_all) * 100;
            if($this->_config['bonus_double'] == 'all')
            {
                $freespin_balance  = ($fs->total_win_freespin - $this->_calc->total_win_free) * 100;
            }
        }

        $this->reload_session();

        $this->_session['lm']  = array_values($this->_calc->lightingLine());
        $this->_session['lv']  = $lineWin;

        //TODO наверн не нужен тут save
        $this->save();

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "win"                 => $this->_calc->win_all * 100,
                "linesMask"           => array_values($this->_calc->lightingLine()),
                "linesValue"          => $lineWin,
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
                'last_win_sum'        => $this->_calc->total_win_free * 100,//TODO. Не совсем уместный параметр, но хотя бы работает. Надо смотреть в сторону lastWinSumBlur из клиента
                'bonus'               => game::data('freeCountAll') - game::data('freeCountCurrent'),
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => game::data('freeCountAll',0),
                "multiplier"          => game::data('multiplier',1),
                "trader"              => bet::$trader_transaction,
        ];

        $ans = array_merge($ans,$extra_ans);

        if($freespin_balance!==false) {
            $ans['freespin_balance'] = $freespin_balance;
        }

        if($this->_calc->win_all == 0 && auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] <= 0)
            {
                auth::user()->last_game = null;
                auth::user()->save();
            }
        }

        //bookofra

        if(in_array($this->_game,['bookofra','downunder'])) {
            $ans['extra_param']=0;

            if($this->_calc->freerun) {
                $ans['extra_param']=$this->_session['extra_param'];
            }

            if($this->_calc->bonusrun) {
                $ans['extra_param']=1;
            }
        }



        return $ans;
    }


    public function double($select)
    {

		if($this->_state()==3) {
            $this->_calc->SetFreeRunMode();
        }


        $is_use_freespins = auth::user()->use_freespins_now($this->_game);

        if($is_use_freespins) {
            $this->_calc->SetFreeSpinMode();
        }

        $this->_calc->select = !is_null($select) ? $select : math::random_int(0, 1);
        $this->_calc->double();

        $this->reload_session();

        $gamble_history                   = $this->_session['gamble_history'];
        array_unshift($gamble_history,$this->_calc->double_result);
        $gamble_history                   = array_slice($gamble_history,0,5);
        $this->_session['gamble_history'] = $gamble_history;

        if($is_use_freespins) {
            $fs = auth::user()->freespins_info($this->_game);
            $fs->total_win_freespin += $this->_calc->win_all-$this->_calc->amount;
            $fs->win_balance += $this->_calc->win_all-$this->_calc->amount;
            $fs->save();
        }

        $this->save();
        $ans                              = [
                "bonus"               => $this->_session['freeCountAll'] - $this->_session['freeCountCurrent'],
                "bonus_all"           => $this->_session['freeCountAll'],
                "step"                => $this->_session['step'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'] + 1,
                "win"                 => $this->_calc->win_all * 100,
                "suite"               => $this->_calc->double_result,
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"             => 100 * ($this->amount() - $this->_calc->win_all),
                "trader"              => bet::$trader_transaction,
        ];

        $use_freespins_now = auth::user()->use_freespins_now($this->_game);

        if($this->_session['freeCountAll'] > 0 && ($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] == 0))
        { //последний фриспин
            $ans['balance']      = $this->amount() * 100 - $this->_calc->total_win_free * 100;
            $ans['last_win_sum'] = $this->_calc->total_win_free * 100;
            if ($use_freespins_now) {
                $ans["freespin_balance"]  = ($fs->total_win_freespin - $this->_calc->total_win_free) * 100;
            }
        }

        if ($use_freespins_now) {
            $ans["freespin_balance"]  = ($fs->total_win_freespin - $this->_calc->win_all) * 100;
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

        //freespin
        $use_freespins_now = auth::user()->use_freespins_now($this->_game);

        if($use_freespins_now) {

            $bets = isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'];
            $freespins_user = auth::user()->freespins_info($this->_game);

            $freespins = [
                "balance"   => $freespins_user->total_win_freespin,
                "gamename"  => auth::user()->last_game ?? -1,
                "freespin_balance"  => 100 * ($freespins_user->total_win_freespin - $this->_session['win']),
                "dentab"    => [100],
                "bets"      => array_fill(0,count($bets), $freespins_user->bet),
                "lines"     => array_fill(0,count($bets), (int)$freespins_user->lines),
                "linesMask" => array_keys($this->_config['lines']),
            ];

            return $freespins;
        }
        
        
        return ["balance"   => 100 * ($this->amount() - $this->_session['win']),
                "gamename"  => auth::user()->last_game ?? -1,
                "dentab"    => isset($this->_config['dentab']) ? $this->_config['dentab'] : $this->_config_defaults['dentab'],
                "bets"      => isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'],
                "lines"     => (isset($this->_config['staticlines'])) ? $this->_config['staticlines'] : array_keys($this->_config['lines']),
                "linesMask" => array_keys($this->_config['lines']),
        ];
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

        $ans = ["balance"             => 100 * ($this->amount() - $this->_session['win']),
                "comb"                => $this->_session['comb'],
                "extracomb"                => $this->_session['extracomb'],
                "state"               => $state,
                "win"                 => $state != 3 ? $this->_session['win'] * 100 : 0,
                "bonus_win"           => $bonus_win,
                "bonus_all"           => $this->_session['freeCountAll'],
                "bonus"               => $state == 3 ? ($this->_session['freeCountAll'] - $this->_session['freeCountCurrent']) : 0,
                "last_win_sum"        => $state == 3 ? $this->_session['total_win_free'] * 100 : $this->_session['win'],
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
        ];

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

        //freespin
        $use_freespins_now = auth::user()->use_freespins_now($this->_game);

        if($use_freespins_now) {
            $spin = auth::user()->get_freespins($this->_game);
            $freespins_user = auth::user()->freespins_info($this->_game);

            if($spin['current'] != $spin['break']) {
                $ans['freespin_spins_rest'] = $spin['break'] - $spin['current'];
                $ans['freespin_balance'] = 100 * ($freespins_user->total_win_freespin - $ans['win']);
                $ans['freespin_complete'] = false;
                $ans['message'] = __('Осталось '). $ans['freespin_spins_rest'].__(' бесплатных вращений');
            } else {
                $ans['freespin_spins_rest'] = 0;
                $ans['freespin_balance'] = 100 * ($freespins_user->total_win_freespin - $ans['win']);
                $ans['freespin_complete'] = true;
                $ans['message'] = __('Поздравляем! Выиграно ').($freespins_user->total_win_freespin).__(' на бесплатных вращениях');
            }

            $bets = isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'];

            $ans["balance"] = 0;
            $ans["gamename"]  = auth::user()->last_game ?? -1;
            $ans["dentab"]    = [100];
            $ans["bets"]      = array_fill(0,count($bets), (int)$freespins_user->bet);
            $ans["lines"]     = array_fill(0,count($bets), (int)$freespins_user->lines);
        }


        return $ans;
    }

    public function get_balance()
    {
        $fs = auth::user()->freespins_info($this->_game);
        $win = $fs->total_win_free?? $this->_session['win'];
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

        $use_freespins_now = auth::user()->use_freespins_now($this->_game);
        if($use_freespins_now) {
            $ans['freespin_balance'] = 100 * ($fs->total_win_freespin - $this->_session['win']);
        }

        return $ans;
    }

    public function save_win()
    {

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

        $use_freespins_now = auth::user()->use_freespins_now($this->_game);
        if ($use_freespins_now) {
            $fs = auth::user()->freespins_info($this->_game);
            $ans["freespin_balance"]  = $fs->total_win_freespin * 100;
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

    public function spin($lidx = -1,$bidx = -1,$didx = -1)
    {
        $dentab = isset($this->_config['dentab']) ? $this->_config['dentab'] : $this->_config_defaults['dentab'];
        $bets   = isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'];
        $lines  = (isset($this->_config['staticlines'])) ? $this->_config['staticlines'] : array_keys($this->_config['lines']);

        if(!isset($bets[$bidx])) {
            return [
                    'error_code' => 55,
            ];
        }

        $di = $dentab[$didx];
        $bi = $bets[$bidx];
        $li = $lines[$lidx];

        //freespin
        $use_freespins_now = auth::user()->use_freespins_now($this->_game);

        if ($use_freespins_now) {
            $this->_calc->SetFreeSpinMode();
            $freespins_user = auth::user()->freespins_info($this->_game);
            $di = 100;
            $bi = $freespins_user->bet;
            $li = $freespins_user->lines;
        }

        $this->_calc->amount_line = $bi * $di * 0.01;
        $this->_calc->cline       = $li;
        $this->_calc->amount      = $this->_calc->amount_line * $this->_calc->cline;

        $r = $this->_calc->bet();

        $this->reload_session();

        if($r != 0)
        {
            return [
                    'error_code' => $r,
            ];
        }

        $lineWin = $this->_calc->win;
        foreach($lineWin as &$l)
        {
            $l *= 100;
        }

        $this->_session['di'] = $didx;
        $this->_session['bi'] = $bidx;
        $this->_session['li'] = $lidx;

        $this->_session['lm']  = array_values($this->_calc->lightingLine());
        $this->_session['lv']  = $lineWin;
        $this->_session['win'] = $this->_calc->win_all;

        if(!$this->_calc->freerun)
        {
            //clear bonus game
            $this->_session['fg_level'] = 0;
            $this->_session['freeCountCurrent'] = 0;
            $this->_session['freeCountAll']     = 0;
            $this->_session['total_win_free']   = 0;
        }
        else
        {
            auth::user()->last_game = $this->_game;
            auth::user()->save();
        }

        if(auth::user(true)->last_game && $this->_game != auth::user()->last_game)
        {
            //если есть недоигранные бонусы и пытаются играть в другую игру
            throw new HTTP_Exception_404;
        }

        $this->_session['extracomb'] = $this->_calc->extrasym();
        $this->_session['step'] = 0; //при каждом спине обнуляем удвоение.

        if(auth::user()->use_freespins_now($this->_game)) {
            $fs = auth::user()->freespins_info($this->_game);
            $fs->total_win_freespin += $this->_calc->win_all;
            $fs->win_balance += $this->_calc->win_all;
            $fs->save();
        }
        $this->save();

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "extracomb"                => $this->_calc->extrasym(), //prev and next reels
                "win"                 => $this->_calc->win_all * 100,
                "linesMask"           => $this->_session['lm'],
                "linesValue"          => $this->_session['lv'],
//		    'gamble_max_steps' => $this->_calc->freerun>0?0:$this->_config_defaults['max_double'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'],
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                'step'                => 0,
                "balance"             => 100 * ($this->amount() - $this->_calc->win_all),
                "bonus"               => $this->_calc->freerun,
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => $this->_session['freeCountAll'],
                "trader"              => bet::$trader_transaction,
        ];

        if($this->_game=='superhot201') {
            $ans['extracomb']=[
                [8,2,3,4,5,6],
                [8,2,3,4,5,6],
                [8,2,3,4,5,6],
                [8,2,3,4,5,6],
                [8,2,3,4,5,6],
            ];

            $ans['comb']= array_fill(0, 15, 7);
        }

        //freespin
        $freespins = auth::user()->get_freespins($this->_game);

        if($use_freespins_now AND $freespins) {
            $spin = auth::user()->set_freespins($this->_game);

            if($spin['current'] != $spin['break']) {
                $ans['freespin_spins_rest'] = $spin['break'] - $spin['current'];
                $ans['freespin_balance'] = 100 * ($fs->total_win_freespin - $this->_calc->win_all);
                $ans['freespin_complete'] = false;
                $ans['message'] = __('Осталось ').$ans['freespin_spins_rest'].__(' бесплатных вращений');
            } else {
                $ans['freespin_spins_rest'] = 0;
                $ans['freespin_balance'] = 100 * ($fs->total_win_freespin - $this->_calc->win_all);
                $ans['freespin_complete'] = true;
                $ans['message'] = __('Поздравляем! Выиграно ').($fs->total_win_freespin).__(' на бесплатных вращениях');
            }

        }


        return $ans;
    }

    public function clear($save = false)
    {
        $this->_session['win']  = 0;
        $this->_session['step'] = 0;
//		$this->_session['is_gamble_game'] = 0;
//		$this->_session['is_bonus_game'] = 0;

        if($save)
        {
            $this->save();
        }
        return $this;
    }

    public function amount() {
        $amount = auth::user(true)->amount();

        $games = kohana::$config->load('static.reg_fs_games');

        if(!in_array($this->_game, $games)) {
            $amount -= auth::user()->fs_win_sum_current();
        }

        return $amount;
    }

}
