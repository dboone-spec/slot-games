<?php

class game_keno_agt extends game_slot_agt
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
            $this->_session['extracomb'] = [];
            $this->save();

            if(!Kohana::$is_cli) {
                //new user
                $stats=arr::get($_GET,'stats',[]);
                $stats['user_id']=auth::$user_id;
                $stats['ip']=$_SERVER['REMOTE_ADDR'] ?? null;

                th::saveUserDevice($this->_game,$stats);
            }
        }

        return $this->_commonInit();
    }

    public function restore()
    {
        $a = parent::restore();
        $pay=(array) Kohana::$config->load('keno/'.$this->_game)['pay'];
        $a['pay_table']=$pay;
        return $a;
    }

    public function spin($li=0,$amount=0,$didx=0)
    {

        if(!$this->isUserActivityIsOK()) {
            throw new Exception('Error.');
        }

        $this->_calc->betcoin       = $li;
        $this->_calc->amount      = $amount;
        $this->_calc->come      = arr::get($_GET,'nums',[]);
		
		if(!is_array($this->_calc->come)) {
            throw new Exception('wrong param');
        }


        $r = $this->_calc->bet();

        $this->reload_session();

        if($r != 0)
        {
            throw new Exception('Error');

            return [
                    'error_code' => $r,
            ];
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
        ];

        if(bet::$jpwin) {
            $j = $this->nextjpcard(true);
            $ans['jpcard'] = $j['jpcard'];
        }

        $ans['gamble_stats'] = $this->gamble_stats();

        return $ans;
    }

}
