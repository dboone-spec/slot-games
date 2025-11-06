<?php

//версия без автоматической обрабоки фриспинов. В режим фриспинов вводить вручную
abstract class Slot_Calc extends math {

    public $barcount = 5;
    //TODO это не работает, считается что $heigth=3 всегда
    protected $heigth = 3;
    public $bars = [];
    protected $barFree=[];
    protected $name;
    protected $group;
    protected $config;
    public $pos = [];
    protected $lines;
    protected $lineCount;
    protected $pay;
    protected $pay_rule;
    protected $anypay;
    public $wild;
    protected $wild_multiplier;
    protected $wild_except;
    public $scatter;
    protected $free_games;
    protected $free_multiplier;
    protected $bonus;
    protected $bonus_param;
    protected $free_mode;
    protected $multiplier;
    protected $free_multiplier_mode;
    //общая ставка
    public $amount = 0;
    public $amount_line = 0;
    public $cline = 0;
    public $win = [];
    public $win_all = 0;
    //сколько будет выиграно в бонус игре (не входит в win_all)
    public $bonus_win = 0;
    public $freerun = 0;
    public $isFreerun;
    public $bonusrun = 0;
    public $bonusdata;
    //всего выиграно на фри спинах
    public $total_win_free = 0;
    //осталось халявных вращений
    public $freeCountAll = 0;
    //сыграно халявных вращений
    public $freeCountCurrent = 0;
    //выигрыш получен путем вращения можно запускать бонус игру
    public $canDouble = false;
    //выигрыш по линиям
    public $LineSymbol = [];
    //выигрыш по линиям c wild 0/1
    public $LineUseWild = [];
    //длина комбинации в линии
    public $LineWinLen = [];
    //закончилась серия фриспинов
    public $mode_free_end=false;

    public $bettype = 'normal';

    public function __construct($group, $name) {

        $this->group = $group;
        $this->name = $name;
        $this->config = Kohana::$config->load("$group/$name");



        foreach ($this->config['lines'] as $num => $line) {
            for ($x = 0; $x < $this->barcount; $x++) {
                for ($y = 0; $y < 3; $y++) {
                    if ($line[$y][$x] > 0) {
                        $this->lines[$num][] = $x + $y * $this->barcount + 1;
                    }
                }
            }
        }

        $this->config['defaults'] = Kohana::$config->load($group);

        $this->lineCount = count($this->lines);
        $this->pay = $this->config['pay'];
        $this->pay_rule = arr::get($this->config, 'pay_rule', 'left');
        if (!is_array($this->pay_rule)) {
            $this->pay_rule = [$this->pay_rule];
        }

        $this->anypay = arr::get($this->config, 'anypay', []);
        $this->wild = arr::get($this->config, 'wild', []);
        if (!is_array($this->wild)) {
            $this->wild = [$this->wild];
        }
        $this->wild_multiplier = arr::get($this->config, 'wild_multiplier', 1);
        $this->wild_except = arr::get($this->config, 'wild_except', []);
        if (!is_array($this->wild_except)) {
            $this->wild_except = [$this->wild_except];
        }

        $this->scatter = arr::get($this->config, 'scatter', -1);
        if (!is_array($this->scatter)) {
            $this->scatter = [$this->scatter];
        }

        $this->bonus = arr::get($this->config, 'bonus', -1);
        if (!is_array($this->bonus)) {
            $this->bonus = [$this->bonus];
        }
        $this->bonus_param = arr::get($this->config, 'bonus_param', [0, 0, 0, 0, 0, 0]);

        $this->free_games = arr::get($this->config, 'free_games', [0, 0, 0, 0, 0, 0]);
        $this->free_multiplier = arr::get($this->config, 'free_multiplier', 1);
        $this->free_mode = arr::get($this->config, 'free_mode', 'sum');
        $this->free_multiplier_mode = arr::get($this->config, 'free_multiplier_mode', 'simple');
        $this->multiplier = 1;
    }

    //корректирует позицию барабана, если позиция находится вне допустимого диапазона
    public function correct_pos() {

        foreach ($this->pos as $num => $pos) {
            $c = count($this->bars[$num]);
            if ($pos > $c - 1) {
                $this->pos[$num] -= $c;
            }

            if ($pos < 0) {
                $this->pos[$num] += $c;
            }
        }
    }

    public function SetFreeRunMode() {
        $this->bars=$this->barFree;
        $this->isFreerun=true;
        $this->bettype='free';
        if($this->isFreerun){
            $this->total_win_free = game::data('total_win_free', 0);
            $this->freeCountAll = game::data('freeCountAll', 0);
            $this->freeCountCurrent = game::data('freeCountCurrent', 0);
        }

    }


    public function bet($mode = null, callable $callback = null) {

        $this->canDouble = false;
        $amount = $this->amount;
        $no = [];
        //freerspin mode все данные берем из сессии, а не то что прилетело
        if ($this->isFreerun == true) {
            $this->amount = game::data('amount');
            $this->cline = game::data('lines');
            $this->amount_line = $this->amount / $this->cline;
            $amount = 0;
            $no[] = 6;
            $this->multiplier = game::data('multiplier', $this->free_multiplier);
        }


        $error = bet::error($this->amount, $no);
        if ($error > 0) {
            return $error;
        }



        $i = 0;
        $exit = false;
        $min = PHP_INT_MAX;

        do {

            $this->spin($mode);

            if (bet::HaveBankAmount($this->win_all + $this->bonus_win, $amount)) {
                $exit = true;
            };

            //минимально возможный выигрыш
            if ($this->win_all < $min) {
                $min = $this->win_all;
                $pos = $this->pos;
                $method = 'bank';
            }

            //нет вариантов
            if ($i >= 50) {
                //закат солнца вручную
                $this->pos = $pos;
                $this->win();
                $exit = true;
                $method = 'hand';
                continue;
            }


            $i++;
        } while (!$exit);





        $bet['amount'] = $amount;
        $bet['come'] = $this->cline;
        $bet['result'] = json_encode($this->sym());
        $bet['office_id'] = OFFICE;
        $bet['win'] = $this->win_all;
        $bet['game_id'] = 0;
        $bet['method'] = $i > 1 ? $method : 'random';

        //TODO в идеале удвоение должно быть при каждом выигрышном спинге в обычной игре и после прокрута всех фриспинов, можно сыграть на весь выигрыш
        if ($this->win_all > 0 and $this->bonusrun == 0) { //убрал условие на выпадение фригеймов, т.к. в этот момент еще горит кнопка "РИСК" и можно удвоить.
            $this->canDouble = true;
        }

        if(game::data('freeCountAll',0)>0 && game::data('freeCountCurrent',0)>0 && ($this->total_win_free+$this->win_all)>0) {
            $this->canDouble = true;
        }

        $data = null;

        $data['win'] = $this->win_all;
        $data['amount'] = $this->amount;
        $data['lines'] = $this->cline;
        $data['comb'] = array_values($this->sym());
        $data['can_double'] = (int) $this->canDouble;
        $data['can_bonus'] = 0;
        $data['freeCountAll'] = 0;
        //TODO убрать на хуй. UPD. не надо убирать. нужно в удвоении бонусов
        $data['first_bet'] = $this->win_all;



        if ($this->isFreerun){
            $this->total_win_free += $this->win_all;
            $this->freeCountCurrent++;
            $data['total_win_free'] = $this->total_win_free;
            $data['freeCountCurrent'] = $this->freeCountCurrent;
            $data['freeCountAll'] = $this->freeCountAll;
        }


        //выиграли freespin
        if ($this->freerun > 0) {

            if(!$this->isFreerun && $this->config['bonus_double']=='all') { //если старые игры и выиграли первые фри спины
                $this->total_win_free+=$this->win_all;
            }

            $data['total_win_free'] = $this->total_win_free;
            $data['freeCountCurrent'] = $this->freeCountCurrent;
            $data['freeCountAll'] = $this->freeCountAll;

            //freespin выиграли во время freespin
            if ($this->isFreerun) {

                if ($this->free_mode == 'sum') {
                    $this->freeCountAll+=$this->freerun;
                }
                else {
                    $this->freeCountAll=$this->freerun;
                    //TODO проверить что при этом отображается в клиенте
                    $this->freeCountCurrent=1;
                }

                if ($this->free_multiplier_mode == 'inc') {
                    $data['multiplier'] = game::data('multiplier') + 1;
                }
                else {
                    $data['multiplier'] = $this->free_multiplier;
                }

            }
            //freespin выиграли в обычном режиме
            else{
                $this->freeCountAll+=$this->freerun;
                $data['multiplier'] = $this->free_multiplier;
            }

            $data['freeCountAll'] = $this->freeCountAll;

        }

        if ($this->isFreerun and $this->freeCountCurrent>=$this->freeCountAll){
            $this->mode_free_end=true;
        }


        //нужно запускать бонус
        if ($this->bonusrun > 0) {
            $data['can_bonus'] = 1;
            $data['bonusdata'] = $this->bonusdata;
            $data['bonusrun'] = $this->bonusrun;
            $data['bonusPay'] = $this->bonusPay;
        }

        bet::make($bet, $this->bettype, $data, $callback);
    }

    //вращаем
    public function spin($mode = null) {

        for ($i = 1; $i <= $this->barcount; $i++) {
            $this->pos[$i] = math::random_int(0, count($this->bars[$i]) - 1);
        }

        if ($mode=='free'){
            $this->pos[1]=16;
            $this->pos[2]=20;
            $this->pos[3]=20;
            $this->pos[4]=20;
            $this->pos[5]=20;
        }

        if ($mode=='morefree'){
            $this->pos[1]=1;
            $this->pos[2]=2;
            $this->pos[3]=0;
            $this->pos[4]=0;
            $this->pos[5]=0;
        }

        $this->correct_pos();


        $this->win();
    }

    public function sym($num = null) {

        if (empty($num)) {
            $r = [];
            for ($i = 1; $i <= $this->barcount * $this->heigth; $i++) {
                $r[$i] = $this->sym($i);
            }
            return $r;
        }



        $bar = $num % $this->barcount;
        if ($bar == 0) {
            $bar = $this->barcount;
        }


        $pos = $this->pos[$bar] + floor(($num - 0.01) / $this->barcount);

        if ($pos >= count($this->bars[$bar])) {
            $pos -= count($this->bars[$bar]);
        }
        return $this->bars[$bar][$pos];
    }

    //битовая маска участвующих в выигрыше символов
    //$num - номер линии
    //скаттеры anypay не участвуют тут
    //TODO пока поддерживается только pay_rule==left добавить остальные
    public function lightingLine($num = null) {

        if (is_null($num)) {
            for ($i = 0; $i <= $this->cline; $i++) {
                $a[$i] = $this->lightingLine($i);
            }
            return $a;
        }

        //scatter
        if ($num==0){
            $light=0;
            if ($this->win[0] > 0) {
                foreach ($this->sym() as $sym) {
                    $light = $light << 1;
                    if (in_array($sym, $this->anypay)) {
                        $light ++;
                    }
                }
            }
            return $light;
        }

        switch ($this->LineWinLen[$num]) {
            case 0: return 0;
            case 1: return 0b10000;
            case 2: return 0b11000;
            case 3: return 0b11100;
            case 4: return 0b11110;
            case 5: return 0b11111;
            case -1: return 0b00001;
            case -2: return 0b00011;
            case -3: return 0b00111;
            case -4: return 0b01111;
            case -5: return 0b11111;
        }

        return 0;
    }

    //текущий выигрыш
    public function win() {

        $this->win_all = 0;
        $this->bonus_win = 0;
        $this->bonusdata = null;

        $this->LineSymbol = array_fill(1, $this->lineCount, -1);
        $this->LineUseWild = array_fill(1, $this->lineCount, false);
        $this->LineWinLen = array_fill(1, $this->lineCount, 0);



        $this->win = array_fill(0, $this->lineCount+1, 0);
        //выигрыш по линиям
        for ($i = 1; $i <= $this->cline; $i++) {
            $this->win[$i] = $this->payLine($i) * $this->amount_line * $this->multiplier;
        }

        $count = array_count_values($this->sym());

        //anypay
        $this->win[0] = 0;
        foreach ($this->anypay as $sym) {
            if (isset($count[$sym])) {
                $this->win[0] += $this->pay($sym, $count[$sym]) * $this->amount * $this->multiplier;
            }
        }

        $this->win_all = array_sum($this->win);

        //free run, все scatter складываются и работают как одинаковые
        $this->freerun = 0;
        $cf=0;
        foreach ($this->scatter as $sym) {
            if (isset($count[$sym])) {
                $cf+=$count[$sym];
            }
        }
        $this->freerun += $this->free_games[$cf];

        //bonus run, каждый bonus считается вместе с другими
        $this->bonusrun = 0;
        $cb=0;
        foreach ($this->bonus as $sym) {
            if (isset($count[$sym])) {
                $cb+=$count[$sym];
            }
        }

        $this->bonusrun = $this->bonus_param[$cb];

        if ($this->bonusrun > 0) {
            $this->bonus_win=$this->calcbonus();
        }

        if(game::data('bonusPay')>0 && $this->amount == 0) {
            $this->win_all = game::data('bonusPay');
        }
    }

    public function calcbonus() {
        return 0;
    }

    public function pay($sym, $c) {
        $c=abs($c);
        return $this->pay[$sym][$c] ?? 0;
    }



    protected function pay3($comb){
        $win=['pay'=>0,'sym'=>-1,'useWild'=>false,'len'=>0];
        //ищем wild
        $wildPos=[];
        foreach($this->wild as $w){
            foreach(array_keys($comb,$w) as $pos){
                $wildPos[]=$pos;
            }
        }



        $simple=[0,1,2];
        foreach ($wildPos as $pos){
            unset($simple[$pos]);
        }

        //первый в линии символ, который не wild
        $posSym=count($simple)>0 ? min($simple) : -1;
        //если комбинация состоит не из одних wild
        if ($posSym>=0){

            $sym=$comb[$posSym];
            if (!in_array($sym,$this->anypay)){
                $comb1=$comb;
                //если wild действует на текущий символ
                if (!in_array($sym,$this->wild_except)){
                    foreach($wildPos as $pos){
                        $comb1[$pos]=$sym;
                    }
                }



                $len=0;
                $m=1;
                $useWild=false;

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2]){
                    $len=3;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

		if ($this->pay($sym,$len)>0){
		    $win=['pay'=>$this->pay($sym,$len)*$m,'sym'=>$sym,'useWild'=>$useWild,'len'=>$len];
		}

            }
        }

        //считаем wild
        $len=0;
        $wild=[];

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos)){
            $len=3;
            $wild=[$comb[0],$comb[1],$comb[2]];
        }


        foreach(array_unique($wild) as $sym){
            if ($this->pay($sym,$len)>$win['pay']){
                $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>false,'len'=>$len];
            }
        }

        return $win;

    }




     protected function payLeft($comb){
        $win=['pay'=>0,'sym'=>-1,'useWild'=>false,'len'=>0];
        //ищем wild
        $wildPos=[];
        foreach($this->wild as $w){
            foreach(array_keys($comb,$w) as $pos){
                $wildPos[]=$pos;
            }
        }



        $simple=[0,1,2,3,4];
        foreach ($wildPos as $pos){
            unset($simple[$pos]);
        }

        //первый в линии символ, который не wild
        $posSym=count($simple)>0 ? min($simple) : -1;
        //если комбинация состоит не из одних wild
        if ($posSym>=0){

            $sym=$comb[$posSym];
            if (!in_array($sym,$this->anypay)){
                $comb1=$comb;
                //если wild действует на текущий символ
                if (!in_array($sym,$this->wild_except)){
                    foreach($wildPos as $pos){
                        $comb1[$pos]=$sym;
                    }
                }



                $len=1;
                $m=1;
                $useWild=false;
                if ($comb1[0]==$comb1[1]){
                    $len=2;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2]){
                    $len=3;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2] and $comb1[2]==$comb1[3]){
                    $len=4;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2] and $comb1[2]==$comb1[3] and $comb1[3]==$comb1[4]){
                    $len=5;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) or in_array(4,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if($this->pay($sym,$len)>$win['pay']){
                    $win=['pay'=>$this->pay($sym,$len)*$m,'sym'=>$sym,'useWild'=>$useWild,'len'=>$len];
                }

            }
        }

        //считаем wild
        $len=0;
        $wild=[];
        if (in_array(0,$wildPos) ){
            $len=1;
            $wild=[$comb[0]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos)){
            $len=2;
            $wild=[$comb[0],$comb[1]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos)){
            $len=3;
            $wild=[$comb[0],$comb[1],$comb[2]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos)){
            $len=4;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos) and in_array(4,$wildPos)){
            $len=5;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3],$comb[4]];
        }


        foreach(array_unique($wild) as $sym){
            if ($this->pay($sym,$len)>0 and $this->pay($sym,$len)>=$win['pay'] and ! in_array($sym, $this->anypay)){
                $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>false,'len'=>$len];
            }
        }

        return $win;

    }

    //line номер линии
    public function payLine($line) {


        //TODO добавить поддержку any
        if (in_array('left', $this->pay_rule)) {
           $f='payLeft';
        }

        if (in_array('leftright', $this->pay_rule)) {
           $f='payLeftRight';
           //$f='payRight';
        }




        if (in_array('3', $this->pay_rule)) {
            $f='pay3';
        }


        $win=$this->{$f}($this->GetElLine($line));
        $this->LineSymbol[$line] = $win['sym'];
        $this->LineUseWild[$line] = $win['useWild'];
        $this->LineWinLen[$line] = $win['len'];

        return $win['pay'];
    }


    //сравнение массивов
    function array_diff1($comb, $win) {

        if (count($comb) != count($win)) {
            throw new Exception('Несовпадение');
        }

        foreach ($comb as $key => $el) {

            //массив
            if (is_array($comb[$key])) {
                if (array_diff1($comb[$key], $win[$key]) == 1) {
                    return 1;
                }
            }
            //значение
            //0 - wild в комбинации comb
            elseif ($comb[$key] == 0) {
                continue;
            }
            //0 - любой символ во входе win
            elseif (($win[$key] > 0 and $comb[$key] != $win[$key])) {
                return 1;
            }
        }

        return 0;
    }

    //получаем элемент и следующие за ним
    function getels($bars, $key, $count = 3) {

        $el = [];
        $bc = count($bars) - 1;
        for ($i = 1; $i <= $count; $i++) {
            $el[] = $bars[$key];
            $key++;
            if ($key > $bc) {
                $key = 0;
            }
        }

        return $el;
    }

    function GetElLine($num) {
        $comb = [];
        foreach ($this->lines[$num] as $pos) {
            $comb[] = $this->sym($pos);
        }

        return $comb;
    }

    protected $doubleclass;
    public $double_result;
    public $first_bet;

    public function double() {


        if (game::data('can_double') != 1) {
            throw new Exception('cant double');
        }
        $this->bettype = 'double';
        $this->amount = game::data('first_bet');
        $this->step = game::data('step',0);
        if($this->step>=$this->config['defaults']['max_double']) {
                throw new Exception('cant double. max steps limit');
        }
        $this->first_bet = game::data('first_bet');

        $this->doubleclass->select = $this->select;
        $this->doubleclass->amount = game::data('win');

        if(game::data('freeCountCurrent',0)>0 && $this->config['bonus_double']=='spin') {
            $this->total_win_free = game::data('total_win_free',0) - game::data('win'); //удвоение бонусов
        }

        if(game::data('freeCountCurrent',0)>0 && $this->config['bonus_double']=='all') {
            $this->amount = game::data('total_win_free',0);
            $this->doubleclass->amount = game::data('total_win_free',0);
        }

        $i = 0;
        $exit = false;
        $min = PHP_INT_MAX;
        $method = '';

        do {

            $this->doubleclass->clear();
            $this->doubleclass->select();
            $win = $this->doubleclass->win();
            //сколько можем выиграть?
            if (bet::HaveBankAmount($win)) {
                $exit = true;
                $method = 'bank';
            };

            //минимально возможный выигрыш
            if ($win < $min) {
                $min = $this->win;
                $state = $this->doubleclass->state;
            }

            //нет вариантов
            if ($i >= 50) {
                //закат солнца вручную
                $this->doubleclass->state = $state;
                $exit = true;
                $method = 'hand';
                continue;
            }

            $i++;
        } while (!$exit);

        if ($i == 1) {
            $method = 'random';
        }

        $this->win_all = $this->doubleclass->win();
        $this->double_result = $this->doubleclass->state;

        $data = null;
        if ($this->win_all > 0) {
            $data['can_double'] = 1;
            $data['step']=$this->step+1; //TODO. проверить, чтобы в случае ошибки, не попадало в сессию
            $data['first_bet']=$this->win_all;
        }
        else {
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }

        if(game::data('freeCountCurrent',0)>0) {
            $this->total_win_free += $this->win_all;
        }

        $data['total_win_free'] = $this->total_win_free;

        $data['win'] = $this->win_all;
        //TODO поправить из другого конфига
        if($this->step==$this->config['defaults']['max_double']) {
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }

        $bet['amount'] = $this->amount;
        $bet['come'] = $this->doubleclass->come();
        $bet['result'] = $this->doubleclass->result();
        $bet['win'] = $this->win_all;
        $bet['game_id'] = null;
        $bet['game'] = game::session()->game . ' double';
        $bet['method'] = $method;


        bet::make($bet,  $this->bettype, $data);
    }

    public function calcmath() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);

        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;
                            $in += $this->amount;
                            $out += $this->win_all;

                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;
                            $winScatter += $this->win[0];

                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] ++;
                            if ($this->LineWinLen[1]==1){
                                echo 'lineWinLen==1';
                                print_r($this->pos);
                                exit;
                            }

                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);

        $s = "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
			time: $time
			bars: " . print_a1($this->bars, true) . "\r\n\r\n " . print_b($csym);

        file_put_contents('1', $s);
    }

    //версия с перебором ограниченного числа символов
    public function calcmath2() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;



        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);

        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }


        $lowBar = [];
        $countSym = [];
        $lowCount = [];
        foreach ($this->bars as $num => $bar) {
            $lowBar[$num] = array_values(array_unique($bar));
            $lowCount[$num] = count($lowBar[$num]);
            $countSym[$num] = array_count_values($bar);
        }

        //меняем барабан на маленький
        $this->bars = $lowBar;



        $allSpin = $lowCount[1] * $lowCount[2] * $lowCount[3] * $lowCount[4] * $lowCount[5];
        $curSpin = 0;

        for ($pos[1] = 0; $pos[1] <= $lowCount[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $lowCount[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $lowCount[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $lowCount[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $lowCount[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();

                            //увеличиваем все на общее количество таких комбинаций на барабане
                            $mn = 1;

                            for ($i = 1; $i <= 5; $i++) {
                                //-1 потому что кривые данные, лень переделывать
                                //А потому и не работает, что ты ленивая жопа
                                //А в linecomb вообще говно лежит, и так считать нельзя
                                $mn *= $countSym[$i][$this->lineComb[1][$i - 1]];
                            }

                            $spin_count += $mn;
                            $in += $this->amount * $mn;
                            $out += $this->win_all * $mn;

                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] += $mn;
                            }
                            $freespin += $this->freerun * $mn;


                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] += $mn;

                            $curSpin++;
                        }
                    }
                }
                $time = floor((time() - $start) / $curSpin * ($allSpin - $curSpin));
            }
            echo "$curSpin/$allSpin lost:$time сек\r\n";
        }

        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);
    }

    //версия для расчета 3-х барабанных слотов
    public function calcmath3() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0];
        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];



        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
        }

        $allSpin = $count[1] * $count[2] * $count[3];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {

                    $this->pos[1] = $pos[1];
                    $this->pos[2] = $pos[2];
                    $this->pos[3] = $pos[3];

                    $this->correct_pos();
                    $this->win();
                    $spin_count++;
                    $in += $this->amount;
                    $out += $this->win_all;

                    if ($this->bonusrun > 0) {
                        $bonus[$this->bonusrun] ++;
                    }
                    $freespin += $this->freerun;
                    $winScatter += $this->win[0];

		    $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] ++;
                }
                $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            }
        }

        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);

        $s = "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
			time: $time
			bars: " . print_a1($this->bars, true) . "\r\n\r\n " . print_b($csym);

        file_put_contents('1', $s);
    }

    public function bonuscalcmath() {

        return [0, 0, 0, 0, 0, 0];
    }

}
