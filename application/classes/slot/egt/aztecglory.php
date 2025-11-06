<?php

class Slot_Egt_Aztecglory extends Slot_Egt{


    public $bonus_win_mask;
    public $bonus_win_lines=[];
    // модификатор бонус игры
    public $mud;
    public $replacenum;
    protected $_calc_extra=false;
    protected $bonus;

    public function __construct($name) {

        parent::__construct($name);
        
        $this->extra_param = game::data('extra_param',-1);
    }

    public function bet($mode = null)
    {
        $data = $this->isBonusMode?game::data('bonusdata'):game::data('comb');
        $this->bonusdata = [];
        for($i=1;$i<=count($data);$i++) {
            $this->bonusdata[$i] = $data[$i-1];
        }
        parent::bet($mode);
    }

    public function sym($num = null)
    {
        if(!$this->isBonusMode) {
            return parent::sym($num);
        }

        if($this->isBonusMode) {
            if (empty($num)) {
                return parent::sym($num);
            }
            return $this->bonusdata[$num];
        }
    }

    protected function calcReplace($symbol,$countSym) {
        $o=0;

        if(!isset($countSym[$symbol])) {
            return 0;
        }

        for ($i = 1; $i <= $this->cline; $i++) {
            $a=0;
            foreach ($this->lines[$i] as $pos) {
                if($pos!=$symbol) {
                    continue;
                }
                $a++;
            }
            $o+=$this->pay($symbol,$a);
        }

        $this->bonusPay = $o;

        return $o;
    }

    public function bonus_mask($sym,$bonusdata) {

        $sss = '';
        $i=0;

        for ($x = 0; $x < $this->barcount; $x++) {
            for ($y = 0; $y < 3; $y++) {
                $sss=''.((int) ($bonusdata[$i]==$sym)).$sss;
                $i++;
            }
        }

        return bindec($sss);
    }

    //return mask of replacement
    public function replaceSym($r, $combs) {
        $a=0;
        for ($x = 1; $x <= $this->barcount; $x++) {
            for ($y = 1; $y <= 3; $y++) {
                $a++;
                if($combs[$a]==$r) {
                    for($z=0;$z<3;$z++) {
                        $combs[(($a-1)%$this->barcount)+($this->barcount*$z)+1]=$r;
                    }
                }
            }
        }
        return $combs;
    }

    public function calcbonus()
    {
        return 0;
    }

    public function calcbonusgames($count)
    {
        return 0;
    }

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

    public function win()
    {
        $this->isBonusMode = false;

        parent::win();

        if($this->isBonusMode) {

            $this->win[0]=0;

            for ($i = 1; $i <= $this->cline; $i++) {
                $comb = [];
                foreach ($this->lines[$i] as $pos) {
                    $comb[] = $this->bonusdata[$pos];
                }

                $cnt = array_count_values($comb);

                if(isset($cnt[$this->extra_param])) {
                    $this->win[$i] = $this->pay($this->extra_param,$cnt[$this->extra_param]) * $this->amount_line * $this->multiplier;
                }
            }

            $this->win_all = array_sum($this->win);
        }

        if($this->isFreerun) {

            $data = $this->sym();
            $this->bonusdata = $this->replaceSym($this->extra_param,$data);

            for ($i = 1; $i <= $this->cline; $i++) {
                $this->bonus_win_line[$i] = 0;
                $this->bonus_win_mask[$i]='';
            }

            $this->bonusdata = array_values($this->bonusdata);

            for ($i = 1; $i <= $this->cline; $i++) {
                $comb = [];

                foreach ($this->lines[$i] as $pos) {
                    $comb[] = $this->bonusdata[$pos-1];
                    $this->bonus_win_mask[$i]=''.$this->bonus_win_mask[$i].intval($this->bonusdata[$pos-1]==$this->extra_param);
                }
                $this->bonus_win_mask[$i]=bindec($this->bonus_win_mask[$i]);

                $cnt = array_count_values($comb);

                if(isset($cnt[$this->extra_param])) {

                    $bonus_win=$this->pay($this->extra_param,$cnt[$this->extra_param]) * $this->amount_line * $this->multiplier;
                    $this->bonus_win+=$bonus_win;
                    $this->bonus_win_line[$i]=$bonus_win;
//                    $this->win[$i] += $bonus_win;
                }
            }

            $this->win_all += $this->bonus_win;
        }
    }

    //выпадение бонуса
    //freeSpinsExpandSymbol
    /*
     * complex: {reels: [8, 4, 2, 3, 1, 1, 2, 9, 4, 0, 2, 5, 0, 4, 6, 4, 2, 1, 9, 0, 5, 4, 0, 9, 2],…}

     */

    //win
    /*
     * complex: {reels: [5, 2, 4, 1, 7, 0, 2, 6, 3, 0, 2, 0, 8, 3, 1, 1, 8, 4, 2, 6, 7, 2, 6, 3, 5], lines: [],…}
expand: []
freeSpinsExpandLines: [{line: 6, winAmount: 500, cells: [1, 2, 4, 0], card: 6},…]
freeSpinsExpandSymbol: 6
freeSpinsExpandWinAmount: 5000
freespins: 0
gambles: 0
gameCommand: "bet"
jackpot: false
lines: []
reels: [5, 2, 4, 1, 7, 0, 2, 6, 3, 0, 2, 0, 8, 3, 1, 1, 8, 4, 2, 6, 7, 2, 6, 3, 5]
scatters: []
     */
}

