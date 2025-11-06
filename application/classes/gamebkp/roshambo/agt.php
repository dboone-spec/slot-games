<?php

class game_roshambo_agt extends game_slot_agt
{

    public function init()
    {
        //for new users
        if(!isset($this->_session['comb']) || empty($this->_session['comb']))
        {
            $this->_calc->amount      = 0;
            $this->_calc->betcoin      = max(array_keys($this->_config['pay']));
            $this->_calc->cline       = 0;
            $this->_calc->come       = 0;
            $this->_calc->amount_line = 0;
            $this->_calc->spin();
            $this->_session['comb']   = $this->_calc->sym();
            $this->_session['extracomb'] = [];
            $this->save();
        }

        $a=$this->_commonInit();

        return $a;
    }

    public function restore()
    {
        $a = parent::restore();
        $pay=(array) Kohana::$config->load('roshambo/'.$this->_game)['pay'];
        $a['pay_table']=$pay;
        $a['last5_history']=$this->_session['history'];
        $a['li']=0; //if not - no random hands
        return $a;
    }

    protected function _check_keys()
    {

        foreach(['history'] as $key)
        {
            if(!isset($this->_session[$key]))
            {
                $this->_session[$key] = [];
            }
        }

        parent::_check_keys();
    }

    public function spin($li=0,$amount=0,$didx=0)
    {


        $this->_calc->betcoin       = $li-1;
        $this->_calc->come       = arr::get($_GET,'hand',[]);
        $this->_calc->amount      = $amount;


        $r = $this->_calc->bet();

        $this->reload_session();

        $comb=array_values($this->_calc->sym());

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

        $this->_session['win'] = $this->_calc->win_all;

        $this->save();

        $win_all = $this->_calc->win_all;

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
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => array_values(bet::$jpdata['values']),
                'step'                => 0,
                "balance"             => $this->amount() - $win_all,
                'jpwin' => bet::$jpwin,
                "last_bet_id"          => bet::$last_bet_id,
                "last5_history"          => $this->_session['history'],
        ];

        if(bet::$jpwin) {
            $j = $this->nextjpcard(true);
            $ans['jpcard'] = $j['jpcard'];
        }

        $ans['gamble_stats'] = $this->gamble_stats();

        return $ans;
    }

}
