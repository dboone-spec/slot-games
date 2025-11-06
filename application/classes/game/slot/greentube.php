<?php

//логика игры

class game_slot_greentube extends game_slot
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
        return [];

        $request = trim(json_encode(Request::current()->body()),'"');

        if(strpos($request,"\u0007")===0) {

            $ans=[];
//            array_push($ans,'S'.($this->amount() - $this->_session['win']));
            array_push($ans,'S10000');
            array_push($ans,'C,1.0,,0');
            array_push($ans,'A0');
            array_push($ans,'M,1,20000,1,-1,0');
            array_push($ans,'I11');
            array_push($ans,'e,10,1,10');
            array_push($ans,'s,1');
            array_push($ans,'r,1,6,46,23,24,4,45,3');
            array_push($ans,'W,0');
            array_push($ans,':n,LuckyLadysCharmDeluxe6');
            array_push($ans,':v,3');

            foreach($this->_config['lines'] as $l) {
                $lr='';
                foreach($l as $a=>$b) {
                    if($a==0) {
                        $s='^';
                    }
                    elseif($a==1) {
                        $s='-';
                    }
                    elseif($a==2) {
                        $s='_';
                    }

                    foreach($b as $c) {
                        if($c==1) {
                           $lr.=$s;
                        }
                    }
                }
                array_push($ans,':l'.$lr);
            }

            array_push($ans,':r,0,5TWQNKTA1T5KQ3AQSJQ2J4TKJT4J3A');
            array_push($ans,':r,0,4KWAKT1QJN5TA2JQ1AQ4KTSN3J4K52JN3KJ5KJ4N3QA');
            array_push($ans,':r,0,TQWT3A1N53J2N4TJ1QNSQT2A4Q53K1J4A2N3Q5AK4A5');
            array_push($ans,':r,0,KTWQ2AJ3Q1A5J4NJ5NK4TSK5J1A5N4KTWQ25N3J4NJ2');
            array_push($ans,':r,0,KAWK3Q4AQ5T2AK1Q4JT1NS4J3Q5A1KA1NQ2KJ3N4KN1');
            array_push($ans,':r,0,ZVMIGYUHOILXUZVILX#UZVRLXGZVUHOG');
            array_push($ans,':r,1,T4W23T1AJK23N1AJK23N15JK2SQN5J4AQN5T4AQN5T4AQK3');
            array_push($ans,':r,1,52WNT52J3QNTA2J4QSN1AJ4KN1AT4KN1QWT4KJ3QT5KJ3QT');
            array_push($ans,':r,1,1QWJN1QT3AJN1KT4AJ52KT4AJS52N4QJ52N4QT32N1QT3AN');
            array_push($ans,':r,1,2KW54QKN3T54QKN31S54QJ3154TJ21NATWJ21NATJ2KN3TJ');
            array_push($ans,':r,1,K1W2NK4TAJ2N5QKJ2S35QKJT35QK1T35WNK1T35NK1TA5N');
            array_push($ans,':r,1,14WJN35TKJQ2514JQ2S514AQ2N145TKN');
            array_push($ans,':r,2,5TWQN4TK1A5JN3AQSJQ2A4JQ1KTJ2KT3A');
            array_push($ans,':r,2,4KWAKJ1Q3T4KWAKTSN3JA2T5J35TSN4Q5TQJ');
            array_push($ans,':r,2,TQWT3AK15TQWT3KNSQK1NJ4N25ANSQJ42A4J');
            array_push($ans,':r,2,KTWQ2AJ3A1K5J34TSK5NT4AQJ14TSK5Q2NA3');
            array_push($ans,':r,2,KAWK3Q4NA5T2AK1NS4JAKQ5J3Q1NS4J3T52T');
            array_push($ans,':r,2,YRMZIYLGUVZHXL#IURGHXLIORG#HVLIORGHVI');
            array_push($ans,':r,3,1QWT5J24KNT5J24KNT5SJAKQ3KNA1Q3KNA');
            array_push($ans,':r,3,TKWJ3TKQA4JSAT5Q2NJAWT5Q2NJA4SQ1KQA43');
            array_push($ans,':r,3,JTW21JTQK52KJTQK54K3SANJ54K3ANJT4K51');
            array_push($ans,':r,3,KNW4T3NAJQ4ST3NAJQ4TW5NA1QATQS2KQAJQ2');
            array_push($ans,':r,3,KTW45QTNKJ41QT3KJN1AS23KJNKA25KTNKJ5');
            array_push($ans,':r,3,3AW1J3QN541T2QSJ5ANT2QJKANST4QJKANT4J');
            array_push($ans,':j,W,2,0,12345AKQJTN');
            array_push($ans,':w,#,6,-1500,0');
            array_push($ans,':w,#,5,-500,0');
            array_push($ans,':w,#,4,-20,0');
            array_push($ans,':w,#,3,-5,0');
            array_push($ans,':w,#,2,-2,0');
            array_push($ans,':w,1,6,10000,0');
            array_push($ans,':w,1,5,750,0');
            array_push($ans,':w,1,4,125,0');
            array_push($ans,':w,1,3,25,0');
            array_push($ans,':w,1,2,2,0');
            array_push($ans,':w,2,6,10000,0');
            array_push($ans,':w,2,5,750,0');
            array_push($ans,':w,2,4,125,0');
            array_push($ans,':w,2,3,25,0');
            array_push($ans,':w,2,2,2,0');
            array_push($ans,':w,3,6,1500,0');
            array_push($ans,':w,3,5,400,0');
            array_push($ans,':w,3,4,100,0');
            array_push($ans,':w,3,3,20,0');
            array_push($ans,':w,4,6,1000,0');
            array_push($ans,':w,4,5,250,0');
            array_push($ans,':w,4,4,75,0');
            array_push($ans,':w,4,3,15,0');
            array_push($ans,':w,5,6,1000,0');
            array_push($ans,':w,5,5,250,0');
            array_push($ans,':w,5,4,75,0');
            array_push($ans,':w,5,3,15,0');
            array_push($ans,':w,A,6,500,0');
            array_push($ans,':w,A,5,125,0');
            array_push($ans,':w,A,4,50,0');
            array_push($ans,':w,A,3,10,0');
            array_push($ans,':w,J,6,300,0');
            array_push($ans,':w,J,5,100,0');
            array_push($ans,':w,J,4,25,0');
            array_push($ans,':w,J,3,5,0');
            array_push($ans,':w,K,6,500,0');
            array_push($ans,':w,K,5,125,0');
            array_push($ans,':w,K,4,50,0');
            array_push($ans,':w,K,3,10,0');
            array_push($ans,':w,N,6,300,0');
            array_push($ans,':w,N,5,100,0');
            array_push($ans,':w,N,4,25,0');
            array_push($ans,':w,N,3,5,0');
            array_push($ans,':w,N,2,2,0');
            array_push($ans,':w,Q,6,300,0');
            array_push($ans,':w,Q,5,100,0');
            array_push($ans,':w,Q,4,25,0');
            array_push($ans,':w,Q,3,5,0');
            array_push($ans,':w,S,6,-1500,0');
            array_push($ans,':w,S,5,-500,0');
            array_push($ans,':w,S,4,-20,0');
            array_push($ans,':w,S,3,-5,0');
            array_push($ans,':w,S,2,-2,0');
            array_push($ans,':w,T,6,300,0');
            array_push($ans,':w,T,5,100,0');
            array_push($ans,':w,T,4,25,0');
            array_push($ans,':w,T,3,5,0');
            array_push($ans,':w,W,6,20000,0');
            array_push($ans,':w,W,5,9000,0');
            array_push($ans,':w,W,4,2500,0');
            array_push($ans,':w,W,3,250,0');
            array_push($ans,':w,W,2,10,0');
            array_push($ans,':s,0');
            array_push($ans,':i,1');
            array_push($ans,':i,2');
            array_push($ans,':i,3');
            array_push($ans,':i,4');
            array_push($ans,':i,5');
            array_push($ans,':i,6');
            array_push($ans,':i,7');
            array_push($ans,':i,8');
            array_push($ans,':i,9');
            array_push($ans,':i,10');
            array_push($ans,':i,10');
            array_push($ans,':m,1,2,3,4,5,6,7,8,9,10,20');
            array_push($ans,':b,6,1,2,3,4,5,10');
            array_push($ans,':a,0,0');
            array_push($ans,':g,5,1000000000,2147483646');


//            header("Content-Type: application/json");

            return ['S9926ÿC,1.0,,0ÿA0ÿR#8ÿM,1,20000,1,-1,0ÿI11ÿe,10,1,10ÿ:x,40ÿs,1ÿr,1,6,11,27,16,3,6,22ÿW,0ÿ:n,LuckyLadysCharmDeluxe6ÿ:v,3ÿ:l,------ÿ:l,^^^^^^ÿ:l,______ÿ:l,^-_-^^ÿ:l,_-^-__ÿ:l,-___--ÿ:l,-^^^--ÿ:l,__-^^^ÿ:l,^^-___ÿ:l,_---^^ÿ:r,0,5TWQNKTA1T5KQ3AQSJQ2J4TKJT4J3Aÿ:r,0,4KWAKT1QJN5TA2JQ1AQ4KTSN3J4K52JN3KJ5KJ4N3QAÿ:r,0,TQWT3A1N53J2N4TJ1QNSQT2A4Q53K1J4A2N3Q5AK4A5ÿ:r,0,KTWQ2AJ3Q1A5J4NJ5NK4TSK5J1A5N4KTWQ25N3J4NJ2ÿ:r,0,KAWK3Q4AQ5T2AK1Q4JT1NS4J3Q5A1KA1NQ2KJ3N4KN1ÿ:r,0,ZVMIGYUHOILXUZVILX#UZVRLXGZVUHOGÿ:r,1,T4W23T1AJK23N1AJK23N15JK2SQN5J4AQN5T4AQN5T4AQK3ÿ:r,1,52WNT52J3QNTA2J4QSN1AJ4KN1AT4KN1QWT4KJ3QT5KJ3QTÿ:r,1,1QWJN1QT3AJN1KT4AJ52KT4AJS52N4QJ52N4QT32N1QT3ANÿ:r,1,2KW54QKN3T54QKN31S54QJ3154TJ21NATWJ21NATJ2KN3TJÿ:r,1,K1W2NK4TAJ2N5QKJ2S35QKJT35QK1T35WNK1T35NK1TA5Nÿ:r,1,14WJN35TKJQ2514JQ2S514AQ2N145TKNÿ:r,2,5TWQN4TK1A5JN3AQSJQ2A4JQ1KTJ2KT3Aÿ:r,2,4KWAKJ1Q3T4KWAKTSN3JA2T5J35TSN4Q5TQJÿ:r,2,TQWT3AK15TQWT3KNSQK1NJ4N25ANSQJ42A4Jÿ:r,2,KTWQ2AJ3A1K5J34TSK5NT4AQJ14TSK5Q2NA3ÿ:r,2,KAWK3Q4NA5T2AK1NS4JAKQ5J3Q1NS4J3T52Tÿ:r,2,YRMZIYLGUVZHXL#IURGHXLIORG#HVLIORGHVIÿ:r,3,1QWT5J24KNT5J24KNT5SJAKQ3KNA1Q3KNAÿ:r,3,TKWJ3TKQA4JSAT5Q2NJAWT5Q2NJA4SQ1KQA43ÿ:r,3,JTW21JTQK52KJTQK54K3SANJ54K3ANJT4K51ÿ:r,3,KNW4T3NAJQ4ST3NAJQ4TW5NA1QATQS2KQAJQ2ÿ:r,3,KTW45QTNKJ41QT3KJN1AS23KJNKA25KTNKJ5ÿ:r,3,3AW1J3QN541T2QSJ5ANT2QJKANST4QJKANT4Jÿ:j,W,2,0,12345AKQJTNÿ:w,#,6,-1500,0ÿ:w,#,5,-500,0ÿ:w,#,4,-20,0ÿ:w,#,3,-5,0ÿ:w,#,2,-2,0ÿ:w,1,6,10000,0ÿ:w,1,5,750,0ÿ:w,1,4,125,0ÿ:w,1,3,25,0ÿ:w,1,2,2,0ÿ:w,2,6,10000,0ÿ:w,2,5,750,0ÿ:w,2,4,125,0ÿ:w,2,3,25,0ÿ:w,2,2,2,0ÿ:w,3,6,1500,0ÿ:w,3,5,400,0ÿ:w,3,4,100,0ÿ:w,3,3,20,0ÿ:w,4,6,1000,0ÿ:w,4,5,250,0ÿ:w,4,4,75,0ÿ:w,4,3,15,0ÿ:w,5,6,1000,0ÿ:w,5,5,250,0ÿ:w,5,4,75,0ÿ:w,5,3,15,0ÿ:w,A,6,500,0ÿ:w,A,5,125,0ÿ:w,A,4,50,0ÿ:w,A,3,10,0ÿ:w,J,6,300,0ÿ:w,J,5,100,0ÿ:w,J,4,25,0ÿ:w,J,3,5,0ÿ:w,K,6,500,0ÿ:w,K,5,125,0ÿ:w,K,4,50,0ÿ:w,K,3,10,0ÿ:w,N,6,300,0ÿ:w,N,5,100,0ÿ:w,N,4,25,0ÿ:w,N,3,5,0ÿ:w,N,2,2,0ÿ:w,Q,6,300,0ÿ:w,Q,5,100,0ÿ:w,Q,4,25,0ÿ:w,Q,3,5,0ÿ:w,S,6,-1500,0ÿ:w,S,5,-500,0ÿ:w,S,4,-20,0ÿ:w,S,3,-5,0ÿ:w,S,2,-2,0ÿ:w,T,6,300,0ÿ:w,T,5,100,0ÿ:w,T,4,25,0ÿ:w,T,3,5,0ÿ:w,W,6,20000,0ÿ:w,W,5,9000,0ÿ:w,W,4,2500,0ÿ:w,W,3,250,0ÿ:w,W,2,10,0ÿ:s,0ÿ:i,1ÿ:i,2ÿ:i,3ÿ:i,4ÿ:i,5ÿ:i,6ÿ:i,7ÿ:i,8ÿ:i,9ÿ:i,10ÿ:i,10ÿ:m,1,2,3,4,5,6,7,8,9,10,20ÿ:b,6,1,2,3,4,5,10ÿ:a,0,0ÿ:g,5,1000000000,2147483646ÿX'];

            return [(''.implode('ÿ',$ans).'X')];

        }

        if($request==='30') {
            return [
                    '9906ÿÿ1',
                    'S9906ÿC,1.0,,0ÿA0ÿR#9ÿM,1,20000,1,-1,0ÿI11ÿe,10,1,10ÿ:x,40ÿs,1ÿr,1,6,7,16,15,26,29,31ÿW,0ÿX'
                ];
        }

        return '';

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

        $state = $this->_state();

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

        if(true || $this->_session['win'] || $state == 3)
        {
            $ans['li']         = $this->_session['li'];
            $ans['bi']         = $this->_session['bi'];
            $ans['di']         = $this->_session['di'];
            $ans['linesMask']  = $this->_session['lm'];
            $ans['linesValue'] = $this->_session['lv'];
            $ans['linecnt']    = count($this->_session['lm']);
        }


        $ans = [];
        $ans[]='S'.(100*($this->amount() - $this->_session['win'])); //balance
        $ans[]='C,100.00,,0'; //1.0 - sessioncurrencyfactor. default 100; sessioncurrency; isDeepwalletSession ??; sessioncurrencycode ??
        $ans[]='A0'; //isPayinAllowed
//        $ans[]='R#8'; round number
        $ans[]='M,1,2000000,1,-1,0'; //minbet, maxbet, minbuyin, maxbuyintotal, totalbuyin
        $ans[]='I11'; //hasopenround ??
        $ans[]='e,10,1,10'; //tmpallowedcreditsmultiplier ??
//        $ans[]=':x,40';
        //L - last tickets
        $ans[]=':x,'.$this->_session['win']; //lastwin?
        $ans[]='s,1'; //percentagePayoutAsGames ??
        $ans[]='r,1,6,11,27,16,3,6,22'; //ReelPositions
        $ans[]='W,0'; //estimatedWin ??
        $ans[]=':n,LuckyLadysCharmDeluxe6'; //gamename
        $ans[]=':v,3'; //visiblerows

        foreach($this->_config['lines'] as $l) {
            $lr='';
            foreach($l as $a=>$b) {
                if($a==0) {
                    $s='^';
                }
                elseif($a==1) {
                    $s='-';
                }
                elseif($a==2) {
                    $s='_';
                }

                foreach($b as $c) {
                    if($c==1) {
                       $lr.=$s;
                    }
                }
            }
            $ans[]=':l'.$lr;
        }

        $ans[]=':r,0,5TWQNKTA1T5KQ3AQSJQ2J4TKJT4J3A'; //tmpreels
        $ans[]=':r,0,4KWAKT1QJN5TA2JQ1AQ4KTSN3J4K52JN3KJ5KJ4N3QA';
        $ans[]=':r,0,TQWT3A1N53J2N4TJ1QNSQT2A4Q53K1J4A2N3Q5AK4A5';
        $ans[]=':r,0,KTWQ2AJ3Q1A5J4NJ5NK4TSK5J1A5N4KTWQ25N3J4NJ2';
        $ans[]=':r,0,KAWK3Q4AQ5T2AK1Q4JT1NS4J3Q5A1KA1NQ2KJ3N4KN1';
        $ans[]=':r,0,ZVMIGYUHOILXUZVILX#UZVRLXGZVUHOG';
        $ans[]=':r,1,T4W23T1AJK23N1AJK23N15JK2SQN5J4AQN5T4AQN5T4AQK3'; //первый барабан
        $ans[]=':r,1,52WNT52J3QNTA2J4QSN1AJ4KN1AT4KN1QWT4KJ3QT5KJ3QT';
        $ans[]=':r,1,1QWJN1QT3AJN1KT4AJ52KT4AJS52N4QJ52N4QT32N1QT3AN';
        $ans[]=':r,1,2KW54QKN3T54QKN31S54QJ3154TJ21NATWJ21NATJ2KN3TJ';
        $ans[]=':r,1,K1W2NK4TAJ2N5QKJ2S35QKJT35QK1T35WNK1T35NK1TA5N';
        $ans[]=':r,1,14WJN35TKJQ2514JQ2S514AQ2N145TKN';
        $ans[]=':r,2,5TWQN4TK1A5JN3AQSJQ2A4JQ1KTJ2KT3A';
        $ans[]=':r,2,4KWAKJ1Q3T4KWAKTSN3JA2T5J35TSN4Q5TQJ';
        $ans[]=':r,2,TQWT3AK15TQWT3KNSQK1NJ4N25ANSQJ42A4J';
        $ans[]=':r,2,KTWQ2AJ3A1K5J34TSK5NT4AQJ14TSK5Q2NA3';
        $ans[]=':r,2,KAWK3Q4NA5T2AK1NS4JAKQ5J3Q1NS4J3T52T';
        $ans[]=':r,2,YRMZIYLGUVZHXL#IURGHXLIORG#HVLIORGHVI';
        $ans[]=':r,3,1QWT5J24KNT5J24KNT5SJAKQ3KNA1Q3KNA';
        $ans[]=':r,3,TKWJ3TKQA4JSAT5Q2NJAWT5Q2NJA4SQ1KQA43';
        $ans[]=':r,3,JTW21JTQK52KJTQK54K3SANJ54K3ANJT4K51';
        $ans[]=':r,3,KNW4T3NAJQ4ST3NAJQ4TW5NA1QATQS2KQAJQ2';
        $ans[]=':r,3,KTW45QTNKJ41QT3KJN1AS23KJNKA25KTNKJ5';
        $ans[]=':r,3,3AW1J3QN541T2QSJ5ANT2QJKANST4QJKANT4J';

        $ans[]=':j,W,2,0,12345AKQJTN'; //extendsLineWin ?? | W-symbol, multiplier, extendsLineWin, replaceList

        foreach($this->_config['pay'] as $i=>$p) {
            foreach($p as $o=>$d) {
                if($d>0) {
                    if(in_array($i,$this->_config['scatter'])) {
                        $ans[]=':w,'.$this->_config['shortsyms'][$i].','.$o.','.(-$d).',0';
                        $ans[]=':w,#,'.$o.','.-$d.',0';
                    }
                    else {
                        $ans[]=':w,'.$this->_config['shortsyms'][$i].','.$o.','.($d).',0';
                    }
                }
            }
        }


        $ans[]=':s,0';

        $ans[]=':i,1';
        $ans[]=':i,2';
        $ans[]=':i,3';
        $ans[]=':i,4';
        $ans[]=':i,5';
        $ans[]=':i,6';
        $ans[]=':i,7';
        $ans[]=':i,8';
        $ans[]=':i,9';
        $ans[]=':i,10';
        $ans[]=':i,10';

        $ans[]=':m,1,2,3,4,5,6,7,8,9,10,20';
        $ans[]=':b,'.count($this->_config['bets']).','.implode(',',$this->_config['bets']);
//        $ans[]=':b,6,1,2,3,4,5,10';
        $ans[]=':a,0,0'; //autoplay counts
        $ans[]=':g,'.$this->_config_defaults['max_double'].',1000000000,2147483646'; //gamble params
        $ans[]='X';


        return [(''.implode('ÿ',$ans))];
    }

    public function saveparams() {

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

        return [
            ''.($this->amount()*100).'ÿÿ1', //это проигрыш
            'S'.($this->amount()*100).'ÿC,1.0,,0ÿA0ÿR#9ÿM,1,20000,1,-1,0ÿI11ÿe,10,1,10ÿ:x,40ÿs,1ÿr,0,6,0,16,16,26,29,33ÿW,0ÿX'
        ];

        //первая цифра это какой набор испольуется
        //вторая скорее всего количество барабанов.
        //третья крутят первый барабан,
        //четвертая крутит второй барабан
        //пятая крутит третий барабан
        //шестая крутит четвертый барабан
        //седьмая крутит пятый барабан
        //восьмая крутит шестой барабан

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
