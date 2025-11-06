<?php

//логика игры

class Game_Demo_Agt extends Game_Slot_Agt
{

   public function gamble_stats() {

        $c = dbredis::instance()->get('demoSuitStat');

        if(!$c) {
            $cc['red'] = 0.45;
            $cc['black'] = 0.55;
            $cc['suit0'] = 0.22;
            $cc['suit1'] = 0.24;
            $cc['suit2'] = 0.26;
            $cc['suit3'] = 0.28;
        }
        else{
            $c=json_decode($c);
            $c=th::ObjectToArray($c);

            //TODO BC
            $cc['red']=round($c['red']/($c['red']+$c['black']),2);
            $cc['black']=round($c['black']/($c['red']+$c['black']),2);

            $cc['suit0']=round($c['suit0']/($c['suit0']+$c['suit1']+$c['suit2']+$c['suit3']),2);
            $cc['suit1']=round($c['suit1']/($c['suit0']+$c['suit1']+$c['suit2']+$c['suit3']),2);
            $cc['suit2']=round($c['suit2']/($c['suit0']+$c['suit1']+$c['suit2']+$c['suit3']),2);
            $cc['suit3']=round($c['suit3']/($c['suit0']+$c['suit1']+$c['suit2']+$c['suit3']),2);

        }

        $cc['red']+=1-($cc['red']+$cc['black']);
        $cc['suit3']+=1-($cc['suit0']+$cc['suit1']+$cc['suit2']+$cc['suit3']);

        $cc['red']=round($cc['red'],2);
        $cc['suit3']=round($cc['suit3'],2);

        return $cc;
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

        dbredis::instance()->set('demoAmount'.auth::$user_id,auth::user()->amount);
        dbredis::instance()->expire('demoAmount'.auth::$user_id, 60*60);

        $redis = dbredis::instance();
        $redis->select(1);

        $jps=(bool) auth::user()->office->enable_jp;

        $currency=auth::user()->office->currency;

        $a= ["balance"   => 100 * ($this->amount() - $this->_session['win']),
                "gamename"  => auth::user()->last_game ?? -1,
                "dentab"    => isset($this->_config['dentab']) ? $this->_config['dentab'] : $this->_config_defaults['dentab'],
                "bets"      => isset($this->_config['bets']) ? $this->_config['bets'] : $this->_config_defaults['bets'],
                "lines"     => (isset($this->_config['staticlines'])) ? $this->_config['staticlines'] : array_values($this->_config['lines']),
                "linesMask" => array_keys($this->_config['lines']),
                "user_id" => auth::$user_id,
                "langs"    => $langs,
                "lang"    => $l,
                "gui"    => 1,
                "k_list"    => Kohana::$config->load('agt')['k_list'],
                'jpa'   => $jps,
                "currency" => $currency->code,
                "currency_code" => $currency->icon,
        ];

        return $a;
    }


    public function spin($li=0,$amount=0,$didx=0)
    {

        $fs = auth::user()->getFreespins(auth::$user_id);
        $fs=false;

        if($fs && $fs->loaded() && $this->_calc->gameId()!=$fs->game_id) {
            throw new Exception('bad game fs');
        }

        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api');
            $amount = $fs->amount;
        }


        $this->_calc->amount_line = $amount/$li;
        $this->_calc->cline       = $li;
        $this->_calc->amount      = $this->_calc->amount_line * $this->_calc->cline;

        $r = $this->_calc->spin();

        $this->reload_session();



        $lineWin = $this->_calc->win;

        $this->_session['amount'] = $amount;
        $this->_session['li'] = $li;
        $this->_session['di'] = $didx;

        $this->_session['lm']  = $this->_calc->lightingLine();
        $this->_session['lv']  = $lineWin;
        $this->_session['win'] = $this->_calc->win_all;

//        array_shift($this->_session['lm']);
//        array_shift($this->_session['lv']);

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
            $this->_session['comb_before_fg'] = array_values($this->_calc->sym());
            $this->_session['comb_scatter_position'] = -1;

            $ch_arr=[];

            foreach($this->_session['comb_before_fg'] as $i=>$scatter) {
                if(in_array($scatter,$this->_config['scatter'])) {
                    $ch_arr[]=$i;
                }
            }


            $this->_session['comb_scatter_position'] = $ch_arr[math::array_rand($ch_arr)];

            $this->_session['fg_level'] = 0;
            $this->_session['freeCountAll']=$this->_calc->freerun;
            $this->_session['freeCountCurrent'] = 0;
            $this->_session['total_win_free']   = 0;

        }


        $this->_session['extracomb'] = $this->_calc->extrasym();
        $this->_session['step'] = 0; //при каждом спине обнуляем удвоение.
        $this->_session['can_double']= ($this->_calc->win_all >0);



        $this->_session['lines']=$this->_calc->cline;
        $this->_session['amount']=$this->_calc->amount;

        $this->save();

        dbredis::instance()->incrByFloat('demoAmount'.auth::$user_id,$this->_calc->win_all-$amount);

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "extracomb"                => $this->_calc->extrasym(), //prev and next reels
                "win"                 => "".$this->_calc->win_all, //flags JSON_NUMERIC_CHECK and JSON_PRESERVE_ZERO_FRACTION are broken in php 7+ — json_encode((float)8.8) returns "8.8000000000000007", and json_encode((float)8.8, JSON_NUMERIC_CHECK) and json_encode((float)8.8, JSON_PRESERVE_ZERO_FRACTION) return "8.8000000000000007" too.
                                                                    // the only way to fix this is setting "serialize_precision = -1" in php.ini
                "linesMask"           => $this->_session['lm'],
                "linesValue"          => $this->_session['lv'],

                'gamble_max_steps'    => $this->_config_defaults['max_double'],
                "gamble_suit_history" => [1,2,3,4],// $this->_session['gamble_history'],
                "jackpots"            => array_values(bet::$jpdata['values']),
                'step'                => 0,
                "balance"             => bcdiv($this->amount() - $this->_calc->win_all,1,2),
                "bonus"               => $this->_calc->freerun,
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => $this->_session['freeCountAll'],
                'jpwin' => bet::$jpwin,
                "last_bet_id"          => mt_rand(1000000,9999999),
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
        }

        return $ans;
    }


    public function amount() {
        $amount = dbredis::instance()->get('demoAmount'.auth::$user_id);
        dbredis::instance()->expire('demoAmount'.auth::$user_id, 30*60);
        return (float) $amount;
    }


    public function double($select)
    {


        //---------------------
        $d=new Double_Demo();


        if (game::data('can_double') != 1) {
            throw new Exception('cant double');
        }
        $bettype = 'double';
        $amount = game::data('win');
        $step = game::data('step',0);
        if($step>=5) {
                throw new Exception('cant double. max steps limit');
        }
        $first_bet = game::data('first_bet');

        $d->select = $select;
        $d->amount = game::data('win');



        $total_win_free=0;

        if(game::data('freeCountCurrent',0)>0 ) {
            $total_win_free = game::data('total_win_free',0) - game::data('win'); //удвоение бонусов
        }



        $d->clear();
        $d->select();
        $win = $d->win();


        $win_all = $d->win();
        $double_result = $d->state;

        $data = null;
        if ($win_all > 0) {
            $data['can_double'] = 1;
            $data['step']=$step+1; //TODO. проверить, чтобы в случае ошибки, не попадало в сессию
            $data['first_bet']=$win_all;
        }
        else {
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }

        if(game::data('freeCountCurrent',0)>0) {
            $total_win_free += $win_all;
        }
        $data['total_win_free'] = $total_win_free;


        $data['win'] = $win_all;
        //TODO поправить из другого конфига
        if($step>=5) {
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }

        /*
        $bet['amount'] = $this->amount;
        $bet['come'] = $this->doubleclass->come();
        $bet['result'] = $this->doubleclass->result();
        $bet['win'] = $this->win_all;
        $bet['game_id'] = null;
        $bet['game'] = game::session()->game . ' double';
        $bet['method'] = $method;
*/


        game::session()->flash($data);
        //---------------------



        $this->reload_session();

        $gamble_history                   = $this->_session['gamble_history'];
        array_unshift($gamble_history,$double_result);
        $gamble_history                   = array_slice($gamble_history,0,5);
        $this->_session['gamble_history'] = $gamble_history;


        dbredis::instance()->incrByFloat('demoAmount'.auth::$user_id,$win_all-$amount);
        $ans                              = [
                "bonus"               => $this->_session['freeCountAll'] - $this->_session['freeCountCurrent'],
                "bonus_all"           => $this->_session['freeCountAll'],
                "step"                => $this->_session['step'],
                'gamble_max_steps'    => $this->_config_defaults['max_double'] + 1,
                "win"                 => "".$win_all,
                "suite"               => $double_result,
                "gamble_suit_history" => $this->_session['gamble_history'],
                "jackpots"            => [
                        "jps"       => [0,0,0,0],
                        "jpmin"     => [0,0,0,0],
                        "jpmax"     => [0,0,0,0],
                        "jplim"     => [0,0,0,0],
                        "jpenabled" => "0"
                ],
                "balance"             => ($this->amount() - $win_all),
                "last_bet_id"          => mt_rand(1000000,9999999),
        ];

        $ans['gamble_stats'] = $this->gamble_stats();


        if($this->_session['freeCountAll'] > 0 && ($this->_session['freeCountAll'] - $this->_session['freeCountCurrent'] == 0))
        { //последний фриспин
            $ans['balance']      = $this->amount() - $total_win_free;
            $ans['last_win_sum'] = $total_win_free;
        }

        $this->save();

        return $ans;
    }





    public function bonus_game()
    {

        $this->_calc->SetFreeRunMode();

        $fs = auth::user()->getFreespins(auth::$user_id);
        if($fs && $fs->loaded() && $fs->active!=0) {
            $this->_calc->setFreeSpinMode($fs->src=='api');
        }

        if(!$this->_calc->isBonusMode && ($this->_calc->freeCountAll - $this->_calc->freeCountCurrent <= 0))
        {
            throw new Exception('freerun is disabled '.$this->_game.' '.auth::$user_id);
        }

        $extra_ans = [];

        $r = $this->_calc->spin();

        if($r != 0)
        {
            return [
                    'error_code' => $r,
            ];
        }

        $lineWin = $this->_calc->win;

        $balance = $this->amount() - $this->_calc->win_all;

        if($this->_config['bonus_double'] == 'all')
        {
            $balance = $this->amount() - $this->_calc->total_win_free;
        }

        $this->reload_session();

        $this->_session['lm']  = array_values($this->_calc->lightingLine());
        $this->_session['lv']  = $lineWin;
        $freeCountCurrent=game::data('freeCountCurrent',0);
        $freeCountCurrent++;
        $this->_session['freeCountCurrent']=$freeCountCurrent;
        $this->_session['total_win_free']+=$this->_calc->win_all;

        if($this->_calc->freerun>0)
        {
            //clear bonus game
            $this->_session['fg_level']++;
            $this->_session['freeCountAll']+=$this->_calc->freerun;

        }

        //TODO наверн не нужен тут save
        $this->save();
        dbredis::instance()->incrByFloat('demoAmount'.auth::$user_id,$this->_calc->win_all);

        $ans = [
                "comb"                => array_values($this->_calc->sym()),
                "win"                 => $this->_calc->win_all,
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
                "balance"             => $this->amount()-$this->_calc->win_all,
                'last_win_sum'        => $this->_calc->total_win_free,//TODO. Не совсем уместный параметр, но хотя бы работает. Надо смотреть в сторону lastWinSumBlur из клиента
                'bonus'               => game::data('freeCountAll') - game::data('freeCountCurrent'),
                "bonus_win"           => $this->_calc->freerun,
                "bonus_all"           => game::data('freeCountAll',0),
                "multiplier"          => game::data('multiplier',1),
                "last_bet_id"          => mt_rand(1000000,9999999),
        ];

        $ans['extracomb'] = $this->_calc->extrasym();

        if($this->_session['freeCountAll'] > 0 && $this->_session['freeCountAll'] == $this->_session['freeCountCurrent'])
        {
            $ans['last_win_sum'] = $this->_session['total_win_free'];
        }

        $ans['session_total_win_free'] = $this->_session['total_win_free'];
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





}
