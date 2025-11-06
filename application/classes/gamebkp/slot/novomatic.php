<?php

//todo gamelist
/*
 * gryphons gold -> unicorn magic
 * faust -> bookofra deluxe
 * lord of the ocean -> bookofra deluxe
 * dynasty of ra -> bookofra deluxe + доработки
 * golden arc -> bookofra deluxe + доработки
 *
 * polarfox -> dolphinsd
 * silverfox -> dolphinsd
 */

//логика игры

class game_slot_novomatic extends game_slot
{

    protected $_config_defaults = [];
    protected $_config          = [];
    protected $_game;
    protected $_calc;

    public $restoreFg=false;

    public function __construct($game)
    {
        $this->_game = $game;
        $class_name  = 'Slot_Novomatic_' . ucfirst($this->_game);
        if(class_exists($class_name))
        {
            $this->_calc = new $class_name($this->_game);
        }
        else
        {
            $this->_calc = new Slot_Novomatic($this->_game);
        }

        $this->_config_defaults = Kohana::$config->load('novomatic');
        $this->_config          = Kohana::$config->load('novomatic/' . $game);

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

        $extra_ans = [];

        if(in_array($this->_game,['bookofra','downunder']) && arr::get($this->_session,'bonusrun',0)==1) {
            $this->_calc->SetBonusMode();

            $extra_ans['bonus_replace_mask'] = $this->_calc->bonus_mask($this->_session['extra_param'],array_values($this->_session['bonusdata']));
            $extra_ans['comb'] = $this->_session['comb'];

            $this->_session['bonusrun']=0;
            $this->save();
        }

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

        if($this->_config['bonus_double'] == 'all')
        {
            $balance = $this->amount() * 100 - $this->_calc->total_win_free * 100;
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
        ];

        if($this->_game=='richesofindia') {
            $ans['multiplier'] = $this->_calc->multiplier();
        }

        if(in_array($this->_game,['bookofra','downunder'])) {
            $ans['bonus_win'] += $this->_calc->bonusrun;
        }

        $ans = array_merge($ans,$extra_ans);

        if($this->_calc->win_all == 0 && auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] <= 0)
            {
                auth::user()->last_game = null;
                auth::user()->save();
            }
        }

        if(auth::user()->last_game == $this->_game)
        {
            if($this->_session['freeCountAll'] <= $this->_session['freeCountCurrent'])
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
        ];


        if($this->_session['freeCountAll'] > 0 && ($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] == 0))
        { //последний фриспин
            $ans['balance']      = $this->amount() * 100 - $this->_calc->total_win_free * 100;
            $ans['last_win_sum'] = $this->_calc->total_win_free * 100;
        }

        if((new Model_Game(['name'=>$this->_game,'show'=>1]))->provider == 'fsgames'  && auth::user()->last_game == $this->_game)
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
            $this->save();
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

//        if(!in_array($this->_game,['bookofrad','dolphinsd','luckyladycharmd','coldspell','columbusd','pharaohsgold3','wonderfulflute'])) {
//            $ans['bonus']=0;
//        }

        if($this->_game=='richesofindia' && $this->_session['freeCountAll']>0) {
            $ans['multiplier']=5;
            if($this->_session['freeCountAll']==20) {
                $ans['multiplier']=10;
            }
            if($this->_session['freeCountAll']==25) {
                $ans['multiplier']=25;
            }
        }

        if(true || $this->_session['win'] || $state == 3)
        {
            $ans['li']         = $this->_session['li'];
            $ans['bi']         = $this->_session['bi'];
            $ans['di']         = $this->_session['di'];
            $ans['linesMask']  = $this->_session['lm'];
            $ans['linesValue'] = $this->_session['lv'];
            $ans['linecnt']    = count($this->_session['lm']);
        }


        //для букофра нужно убирать здесь bonus и указывать символ замены nospin
        //проверить букофра все когда одна линия выигрывает! там косяк
        //второй косяк!!! когда выигрываешь бонус на ОДНОЙ линии, что будет дальше? замена идет или нет? - в делюкс нет косяка.
        //есть косяк в делюкс на последнем бонусе полный выигрыш не верный

//        if(in_array($this->_game,['bookofrad'])) {
//            $b = $this->_session['extra_param']??-1;
//            $ans['extra_param']=$b;
//        }

        if(in_array($this->_game,['bookofra','downunder'])) {
            $b = $this->_session['extra_param']??-1;

            $ans['extra_param']=$b;
            if($b>=0) {
                unset($ans['bonus']);
            }
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

        $this->_calc->amount_line = $bi * $di * 0.01;
        $this->_calc->cline       = $li;
        $this->_calc->amount      = $this->_calc->amount_line * $this->_calc->cline;

        if($this->restoreFg && $this->_session['freeCountAll']>0 && $this->_session['freeCountAll']-$this->_session['freeCountCurrent']>0) {
            $this->_calc->needRestoreFG=true;
        }

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

        $this->_session['step'] = 0; //при каждом спине обнуляем удвоение.

        $this->save();

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
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
                "session"              => $this->_session,
                'jpwin' => bet::$jpwin,
        ];


        $ans['restoreFG']=$this->_calc->needRestoreFG;
        if($ans['restoreFG']) {
            $ans['bonus_all'] = $this->_session['freeCountAll'];
            $ans['bonus'] = $this->_session['freeCountAll']-$this->_session['freeCountCurrent'];
        }

        //для букофра нужно убирать здесь bonus и указывать символ замены nospin
        if(in_array($this->_game,['bookofra','downunder'])) {
            $b = $this->_session['extra_param']??-1;

            $ans['extra_param']=$b;
            //if($b) {
                unset($ans['bonus']);
            //}
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
