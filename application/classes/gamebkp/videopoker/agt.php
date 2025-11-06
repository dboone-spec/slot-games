<?php

class game_videopoker_agt extends game_slot_agt
{

    //copypast because drunk
    public function init()
    {
        //for new users
        if(!isset($this->_session['comb']) || empty($this->_session['comb']))
        {
            $this->_calc->amount      = 0;
            $this->_calc->cline       = 0;
            $this->_calc->amount_line = 0;
            $this->_calc->spin();
            $this->_session['comb']   = ($this->_calc->sym());
            $this->_session['extracomb'] = $this->_calc->extrasym();
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
                "lines"     => (isset($this->_config['staticlines'])) ? $this->_config['staticlines'] : array_keys($this->_config['lines']),
                "linesMask" => array_keys($this->_config['lines']),
                "user_id" => auth::$user_id,
                "lang"    => $l,
                'jpa'   => $jps,
                'gui'   => auth::user()->office->gameui ?? 1,
                'k_list'   => $k_list,
                "need_convert_int" => in_array(auth::user()->office_id,[1040,1046]),
                "currency" => auth::user()->office->currency->code,
                "currency_code" => auth::user()->office->currency->icon,
        ];
    }

    public function restore()
    {
        $a = parent::restore();
        $ab=$a['comb'];
        $a['comb']=[];
        foreach($ab as $kb=>$bb) {
            $a['comb'][$kb-1]=$bb;
        }
        $pay=Kohana::$config->load('videopoker.'.$this->_game);

        $combNames = Arr::extract(array_values($pay['pay'][1]),['*.name']);

        $a['combNames']=$combNames['*']['name'];
        unset($a['combNames'][count($a['combNames'])-1]);
        unset($a['combNames'][count($a['combNames'])-1]);
        $a['pokerStep']=$this->_session['pokerStep']??1;
        $a["wincard"] = array_values($this->_session['wincard']??[]);
        $a["hold"] = array_values($this->_session['hold']??[]);
        return $a;
    }

    public function spin($li=0,$amount=0,$didx=0)
    {


        $this->_calc->betcoin       = $li;
        $this->_calc->amount      = $amount;

        $step = $this->_session['pokerStep']??1;


        $hold=[];
        $h=[];

        if($step==2) {
            $hold = arr::get($_GET,'hold',[]);
            foreach($hold as $hh) {
                $h[]=$hh+1;
            }
        }

        $r = $this->_calc->bet($step,$h);

        $this->reload_session();

        if($r != 0)
        {
            throw new Exception('Error');

            return [
                    'error_code' => $r,
            ];
        }

//        $lineWin = $this->_calc->win;
        if(!empty($h)) {
            $this->_session['hold'] = $h;
        }
        $this->_session['amount'] = $amount;
        $this->_session['li'] = $li;
        $this->_session['di'] = $didx;

//        $this->_session['lm']  = $this->_calc->lightingLine();
//        $this->_session['lv']  = $lineWin;
        $this->_session['win'] = $this->_calc->win_all;


//        array_shift($this->_session['lm']);
//        array_shift($this->_session['lv']);


        if(auth::user(true)->last_game && $this->_game != auth::user()->last_game)
        {
            //если есть недоигранные бонусы и пытаются играть в другую игру
            //todo решить что делать с этой проверкой
            //throw new HTTP_Exception_404;
        }

        $this->_session['step'] = 0;
        $this->save();

        $win_all = 0;
        if($step==2) {
            $win_all = $this->_calc->win_all;
        }

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "extracomb"                => $this->_calc->extrasym(), //prev and next reels
                "win"                 => "".$win_all, //flags JSON_NUMERIC_CHECK and JSON_PRESERVE_ZERO_FRACTION are broken in php 7+ — json_encode((float)8.8) returns "8.8000000000000007", and json_encode((float)8.8, JSON_NUMERIC_CHECK) and json_encode((float)8.8, JSON_PRESERVE_ZERO_FRACTION) return "8.8000000000000007" too.
                                                                    // the only way to fix this is setting "serialize_precision = -1" in php.ini
                "linesMask"           => $this->_session['lm'],
                "linesValue"          => $this->_session['lv'],
                "wincard"          => array_values($this->_session['wincard']??[]),
                "hold"          => array_values($this->_session['hold']??[]),
                'holdcomb' => $this->_session['holdcomb']??'',
//		    'gamble_max_steps' => $this->_calc->freerun>0?0:$this->_config_defaults['max_double'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'],
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => array_values(bet::$jpdata['values']),
                'step'                => 0,
                "balance"             => $this->amount() - $win_all,
                'pokerStep'=>$this->_session['pokerStep'],
                'wincomb'=>$this->_calc->wincomb,
                'jpwin' => bet::$jpwin,
                "last_bet_id"          => bet::$last_bet_id,
        ];

        if(bet::$jpwin) {
            $j = $this->nextjpcard(true);
            $ans['jpcard'] = $j['jpcard'];
        }

        $ans['gamble_stats'] = $this->gamble_stats();

        return $ans;
    }

}
