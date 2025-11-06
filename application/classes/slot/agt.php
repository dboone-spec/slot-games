<?php

class Slot_Agt extends Slot_Calc {

    public $mud = 0;
    protected $_countrolls=10; //количество реальных символов барабанов передаваемые на клиент


    //need to call after initBars
    public function forceBars($rtp='965'){
        $rtp = (float) $rtp;
        $rtp = str_replace('.','',$rtp);

        if($rtp=='99') {
            $rtp='965';
        }

        $this->bars = $this->config['bars'.$rtp] ?? $this->bars;

        $this->applyCertification();

    }

    public function applyCertification() {
        if(!auth::$user_id) {
            return;
        }

        if(!in_array(auth::user()->office_id,[1027,1028,1029,1670])) {
            return;
        }

        $this->bars = $this->config['barsCert'] ?? $this->bars;
        $this->barFree = $this->config['barsFreeCert'] ?? $this->barFree;
    }

    protected function initBars(){

        $this->bars = $this->config['bars965'] ?? $this->config['bars'];
        $this->barcount = count($this->bars);
        $this->barFree = arr::get($this->config, 'barFree', $this->bars);

        $this->applyCertification();

    }


    public function __construct($name) {
        parent::__construct('agt', $name);

    }

    public function canDouble() {
        $this->canDouble = false;
        if ($this->win_all > 0) {
            $this->canDouble = true;
        }
    }

    public function double() {

        $this->doubleclass = new Double_Agt($this->game_id);
        $this->doubleclass->setMaxWin($this->_max_win);
        parent::double();
    }

    public function extrasym() {

        $a=[];
        for ($i = 1; $i <= $this->barcount; $i++) {

            $a[]= array_slice($this->bars[$i],$this->pos[$i],$this->_countrolls);

            /*$bnum2=$this->pos[$i]+$this->barcount;
            if ($bnum2 >= count($this->bars[$i])) {
                $bnum2 -= count($this->bars[$i]);
            }

            $a[1][]=$this->bars[$i][$bnum2];*/
        }

        foreach($a as $i => &$b) {
            if(count($b)<$this->_countrolls) {
                foreach(array_slice($this->bars[$i+1],0,$this->_countrolls-count($b)) as $k) {
                    $b[]=$k;
                }

            }
        }

        return $a;
    }

    public function lightingLine($num = null) {

        if (is_null($num)) {
            for ($i = 1-count($this->anypay); $i <= $this->cline; $i++) {
                $a[$i] = $this->lightingLine($i);
            }
            ksort($a);
            return $a;
        }

        //scatter
        if ($num<=0){
            $light=0;
            if ($this->win[$num] > 0) {
                foreach ($this->sym() as $sym) {
                    $light = $light << 1;
                    if ($sym==$this->anypay[$num*-1]) {
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

    //for two scatters

    public function win() {

        $this->win_all = 0;
        $this->bonus_win = 0;

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

        for($i=0;$i<count($this->anypay);$i++) {
            $this->win[-1*$i]=0;
            if (isset($count[$this->anypay[$i]])) {
                $this->win[-1*$i] = $this->pay($this->anypay[$i], $count[$this->anypay[$i]]) * $this->amount * $this->multiplier;
            }
        }

        $this->win_all = array_sum($this->win);

        $this->correctMaxWin($this->win_all);

        $this->calcfreegames($count);
        $this->calcbonusgames($count);

        if ($this->bonusrun > 0) {
            $this->bonus_win=$this->calcbonus();
        }
    }

}
