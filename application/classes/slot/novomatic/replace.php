<?php

abstract class Slot_Novomatic_Replace extends Slot_Novomatic{


    // модификатор бонус игры
    public $mud;
    public $replacenum;
    protected $_calc_extra=false;
    protected $bonus;


    public function __construct($name) {
        parent::__construct($name);

        $this->extra_param = game::data('extra_param',-1);
    }

    public function win()
    {
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

    public function calcbonus() {

        if (game::data('can_bonus') == 1 || $this->isBonusMode) {
            return 0;
        }

        $c = $this->extra_param;

        if($c<0) {
            return 0;
        }

        $this->bonusdata = $this->replaceSym($c,$this->sym());

        $bonus = 0;

        for ($i = 1; $i <= $this->cline; $i++) {

            $comb = [];
            foreach ($this->lines[$i] as $pos) {
                $comb[] = $this->bonusdata[$pos];
            }

            $cnt = array_count_values($comb);

            if(isset($cnt[$c])) {
                $bonus = $this->pay($c,$cnt[$c]) * $this->amount_line * $this->multiplier;
            }

            $this->bonusPay = $bonus;
        }
        return $this->bonusPay;
    }

    public function calcbonusgames($count)
    {

        if($this->isBonusMode) {
            return 0;
        }

        $this->extra_param = game::data('extra_param',-1);

        if(!$this->isFreerun) {
            return 0;
        }

        $this->bonusrun = 0;

        if($this->extra_param<0) {
            return 0;
        }

        if(!isset($count[$this->extra_param])) {
            return 0;
        }

        $this->bonusrun = (int) ($this->pay($this->extra_param, $count[$this->extra_param])>0);
    }

    public function lightingLine($num = null) {
		$a = parent::lightingLine($num);
		if (is_null($num)) {
			array_shift($a);
		}
		return $a;
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
}

