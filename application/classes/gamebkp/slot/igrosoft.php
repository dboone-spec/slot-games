<?php

//логика игры

class game_slot_igrosoft extends game_slot
{

    protected $_config_defaults = [];
    protected $_config          = [];
    protected $_game;
    protected $_calc;

    public function __construct($game)
    {
        $this->_game = $game;
        $class_name  = 'Slot_Igrosoft_' . ucfirst($this->_game);
        if(class_exists($class_name))
        {
            $this->_calc = new $class_name($this->_game);
        }
        else
        {
            $this->_calc = new Slot_Igrosoft($this->_game);
        }

        $this->_config_defaults = Kohana::$config->load('igrosoft');
        $this->_config          = Kohana::$config->load('igrosoft/' . $game);

        if(!empty(auth::$user_id) && $bets_arr = auth::user()->bets_arr) {
            $bets_arr = explode(',',auth::user()->bets_arr);
            if(!empty($bets_arr)) {
                $this->_config['bets']=$bets_arr;
            }
        }

        parent::__construct();
        $this->_check_keys();
    }

    public function bonus_game()
    {

        $r = $this->_calc->bonus();

        if($r != 0)
        {
            return [
                    'error_code' => $r,
            ];
        }

        $ans = [
                "comb"                => $this->_session['bonusdata'],
                "win"                 => $this->_calc->win_all * 100,
                "linesMask"           => $this->_session['bonusdata'],
                "linesValue"          => $this->_session['bonusdata'],
                'gamble_max_steps'    => 0,
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"             => $this->amount() * 100 - $this->_calc->win_all * 100,
//		    "balance" => (int) auth::user(true)->amount * 100 - $this->_calc->win_all * 100,
                'last_win_sum'        => $this->_calc->win_all * 100,//TODO. Не совсем уместный параметр, но хотя бы работает. Надо смотреть в сторону lastWinSumBlur из клиента
                'bonus'               => $this->_calc->bonusrun,
                "bonus_win"           => $this->_calc->bonusrun,
                "bonus_all"           => $this->_calc->bonusrun,
                "multiplier"          => game::data('multiplier',1),
                "trader"              => bet::$trader_transaction,
        ];


        return $ans;
    }

    public function double($select)
    {

        $this->_calc->select = !is_null($select) ? $select : math::random_int(1,4);
        $this->_calc->double();

        $this->reload_session();

        $gamble_history                   = $this->_session['gamble_history'];
        array_unshift($gamble_history,$this->_calc->double_result);
        $gamble_history                   = array_slice($gamble_history,0,5);
        $this->_session['gamble_history'] = $gamble_history;
        $this->save();
        $ans                              = [
                "bonus"               => 0,
                "bonus_all"           => 0,
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

        return $ans;
    }

    protected function _check_keys()
    {
        $save = false;
        foreach(['win','is_bonus_game','is_gamble_game','bonus_win','bonus_all','bonus_current','total_win_free','step','li','bi','di','freerun','freeCountAll','freeCountCurrent','bonusrun','bonusPay'] as $key)
        {
            if(!isset($this->_session[$key]))
            {
                $this->_session[$key] = 0;
                $save                 = true;
            }
        }

        foreach(['comb','lm','lv'] as $key)
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

        if($this->_session['bonusrun'] || ($this->_session['freeCountAll'] != $this->_session['freeCountCurrent']))
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

        $state = $this->_state();

        $ans = ["balance"             => 100 * ($this->amount() - $this->_session['win']),
                "comb"                => $this->_session['comb'],
                "state"               => $state,
                "win"                 => $this->_session['win'] * 100,
                "bonus_win"           => $state == 3 ? $this->_session['bonusrun'] : 0,
                "bonus_all"           => $state == 3 ? $this->_session['bonusrun'] : 0,
                "bonus"               => $state == 3 ? $this->_session['bonusrun'] : 0,
                "last_win_sum"        => ($this->_session['bonusPay']+$this->_session['win']) * 100,
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

        if($state == 3)
        {
            $ans['li']         = $this->_session['li'];
            $ans['bi']         = $this->_session['bi'];
            $ans['di']         = $this->_session['di'];
            $ans['linesMask']  = $this->_session['lm'];
            $ans['linesValue'] = $this->_session['lv'];
            $ans['linecnt']    = count($this->_session['lm']);
        }

        return $ans;
    }

    public function get_balance()
    {
        $ans = [
                "jackpots" => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"  => 100 * ($this->amount() - $this->_session['win']),
        ];
        return $ans;
    }

    public function save_win()
    {

        //TODO поставить другое условие для бонусов
        if($this->_session['bonusPay']==0) {
            $this->clear(true);
        }
        $win = $this->_session['bonusPay'];
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

        if($this->_session['bonusPay'] > 0)
        {
            //$ans['balance'] = (int) (auth::user()->amount - $this->_session['total_win_free'])*100;
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
                "bonus"               => $this->_calc->bonusrun,
                "bonus_win"           => $this->_calc->bonusrun,
                "bonus_all"           => $this->_calc->bonusrun,
                'jpwin' => bet::$jpwin,
        ];

        if($this->_calc->win_all > 0)
        {
            $ans['dealer_card'] = $this->_calc->GetDoubleCard();
        }

        return $ans;
    }

    public function clear($save = false)
    {
        $this->_session['fg_level'] = 0;
        $this->_session['win']      = 0;
        $this->_session['bonusrun'] = 0;
        $this->_session['bonusPay'] = 0;
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
