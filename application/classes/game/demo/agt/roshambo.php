<?php

class game_demo_agt_roshambo extends game_demo_agt
{
    public function init()
    {
        $this->_calc->betcoin      = max(array_keys($this->_config['pay']));
        $this->_calc->come       = 0;
        return parent::init();
    }

    public function spin($li=0,$amount=0,$didx=0)
    {


        $this->_calc->betcoin       = $li-1;
        $this->_calc->come       = arr::get($_GET,'hand',[]);
        $this->_calc->amount      = $amount;


        $win_all = $this->_calc->spin();

        $history=[];
        for($i=1;$i<=$li;$i++) {
            $history[$i]=[];
        }

        $this->reload_session();


        $comb=array_values($this->_calc->sym());

        $this->_session['amount'] = $amount;
        $this->_session['li'] = $li;
        $this->_session['di'] = $didx;

        $this->_session['lm']  = [];
        $this->_session['lv']  = [];
        $this->_session['win'] = $win_all;

        $this->save();

        dbredis::instance()->incrByFloat('demoAmount'.auth::$user_id,$win_all-$amount);

        $ans = [
                "comb"                => $comb,
                "handwins"                => $this->_calc->allWins(),
                "extracomb"                => $this->_calc->extrasym(), //prev and next reels
                "winSym"                => $this->_calc->win_sym, //prev and next reels
                "win"                 => "".$win_all, //flags JSON_NUMERIC_CHECK and JSON_PRESERVE_ZERO_FRACTION are broken in php 7+ — json_encode((float)8.8) returns "8.8000000000000007", and json_encode((float)8.8, JSON_NUMERIC_CHECK) and json_encode((float)8.8, JSON_PRESERVE_ZERO_FRACTION) return "8.8000000000000007" too.
                                                                    // the only way to fix this is setting "serialize_precision = -1" in php.ini
                "linesMask"           => $this->_session['lm'],
                "linesValue"          => $this->_session['lv'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'],
                "gamble_suit_history" => [1,2,3,4],
                "jackpots"            => array_values(bet::$jpdata['values']),
                'step'                => 0,
                "balance"             => bcdiv($this->amount() - $win_all,1,2),
                'jpwin' => bet::$jpwin,
                "last_bet_id"          => mt_rand(1000000,9999999),
//                "last5_history"          => $history,
        ];

        if(bet::$jpwin) {
            $j = $this->nextjpcard(true);
            $ans['jpcard'] = $j['jpcard'];
        }

        $ans['gamble_stats'] = $this->gamble_stats();

        return $ans;
    }
}

