<?php

class game_miner_agt extends game_slot_agt
{

    public function init()
    {
        //for new users
        if(!isset($this->_session['comb']) || empty($this->_session['comb']))
        {
            $this->_calc->amount      = 0;
            $this->_calc->cline       = 0;
            $this->_calc->amount_line = 0;
            $this->_session['comb']   = [];
            $this->_session['current_win_level']   = 0;
            $this->_session['total_win']   = 0;
            $this->_session['extracomb'] = [];
            $this->_session['betcoin'] = 0;
            $this->save();
        }

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

        $k_list = auth::user()->office->get_k_list();

        return ["balance"   => 100 * ($this->amount() - $this->_session['win']),
                "gamename"  => auth::user()->last_game ?? -1,
                "dentab"    => isset($this->_config['dentab']) ? $this->_config['dentab'] : $this->_config_defaults['dentab'],
                "langs"    => $langs,
                "bets"      => isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'],
                "lines"     => [],
                "linesMask" => [],
                "user_id" => auth::$user_id,
                "lang"    => $l,
                'jpa'   => $jps,
                'gui'   => auth::user()->office->gameui ?? 1,
                'k_list'   => $k_list,
                "currency" => auth::user()->office->currency->code,
                "need_convert_int" => in_array(auth::user()->office_id,[1040,1046]),
                "currency_code" => auth::user()->office->currency->icon,
        ];
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

    public function spin($li=0,$amount=0,$didx=0)
    {

        $betcoin=(isset($this->_session['betcoin']) && $this->_session['betcoin']>0)?$this->_session['betcoin']:$li;

        $this->_calc->betcoin       = (int) $betcoin;
        $this->_calc->amount      = $this->_session['total_win']>0?$this->_session['total_win']:$amount;
        $this->_calc->come      = (int) arr::get($_GET,'num',-1);

        /*if($this->_session['current_win_level']>0) {
            $this->_calc->amount=$this->_session['first_bet'];
        }*/

        $r = $this->_calc->bet();

        $this->reload_session();

        if($r != 0)
        {
            throw new Exception('Error: '.$r);

            return [
                    'error_code' => $r,
            ];
        }

        $this->_session['amount'] = $amount;
        $this->_session['li'] = $li; //todo not work!!!
        $this->_session['di'] = $didx;

        $this->_session['win'] = $this->_calc->win_all;


        if(auth::user(true)->last_game && $this->_game != auth::user()->last_game)
        {
            //если есть недоигранные бонусы и пытаются играть в другую игру
            //todo решить что делать с этой проверкой
            //throw new HTTP_Exception_404;
        }

        $this->save();

        $win_all = $this->_calc->win_all;

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "extracomb"                => $this->_calc->extrasym(), //prev and next reels
                "win"                 => "".$win_all, //flags JSON_NUMERIC_CHECK and JSON_PRESERVE_ZERO_FRACTION are broken in php 7+ — json_encode((float)8.8) returns "8.8000000000000007", and json_encode((float)8.8, JSON_NUMERIC_CHECK) and json_encode((float)8.8, JSON_PRESERVE_ZERO_FRACTION) return "8.8000000000000007" too.
                                                                    // the only way to fix this is setting "serialize_precision = -1" in php.ini
                "linesMask"           => $this->_session['lm'],
                "linesValue"          => $this->_session['lv'],
//		    'gamble_max_steps' => $this->_calc->freerun>0?0:$this->_config_defaults['max_double'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'],
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => array_values(bet::$jpdata['values']),
                'step'                => 0,
                "balance"             => $this->amount() - $win_all,
                'jpwin' => bet::$jpwin,
                "last_bet_id"          => bet::$last_bet_id,
                "current_win_level"          => $this->_session['current_win_level']??0,
        ];

        if(bet::$jpwin) {
            $j = $this->nextjpcard(true);
            $ans['jpcard'] = $j['jpcard'];
        }

        $ans['gamble_stats'] = $this->gamble_stats();

        return $ans;
    }

}
