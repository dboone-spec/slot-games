<?php


class Slot_Maxline extends Slot_Agt
{


    protected $maxSyms;
    protected $minSyms = 2;
    public $barSymsCount = [];
    public $realBars = [];


    public function __construct($name)
    {
        parent::__construct($name);

        $this->maxSyms = arr::get($this->config, 'maxSyms', 7);
        $this->minSyms = arr::get($this->config, 'minSyms', 2);
    }


    //вращаем
    public function spin($mode = null)
    {

        for ($i = 1; $i <= $this->barcount; $i++) {
            $this->pos[$i] = math::random_int(0, count($this->bars[$i]) - 1);
            $this->barSymsCount[$i] = math::random_int($this->minSyms, $this->maxSyms);
        }


        $this->correct_pos();
        $this->win();
    }


    //текущий выигрыш
    public function win()
    {

        $this->win_all = 0;
        $this->win = [0 => 0, 1 => 0];
        $this->realBars = [];

        for ($i = 1; $i <= $this->barcount; $i++) {
            $this->realBars[$i] = array_slice($this->bars[$i], $this->pos[$i], $this->barSymsCount[$i]);
            if (count($this->realBars[$i]) < $this->barSymsCount[$i]) {
                $this->realBars[$i] = array_merge($this->realBars[$i], array_slice($this->bars[$i], 0, $this->barSymsCount[$i] - count($this->realBars[$i])));
            }
        }

        $syms = array_keys($this->pay);

        //$barsCountValues
        $bc = [];
        $allSyms = [];
        foreach ($this->realBars as $num => $bar) {
            $bc[$num] = array_count_values($bar);
            foreach ($syms as $sym) {
                $bc[$num][$sym] = $bc[$num][$sym] ?? 0;
            }
            $allSyms = array_merge($allSyms, $bar);

        }

        //$wildCount
        $wc = array_fill(1, $this->barcount, 0);

        foreach ($bc as $num => $bar) {
            foreach ($bar as $sym => $count) {
                if (in_array($sym, $this->wild)) {
                    $wc[$num] += $count;
                }
            }
        }

        $allCount = array_count_values($allSyms);


        $win = 0;
        foreach ($syms as $sym) {

            if (in_array($sym, $this->wild) || in_array($sym, $this->anypay)) {
                continue;
            }


            $win5 = $bc[1][$sym] * $bc[2][$sym] * $bc[3][$sym] * $bc[4][$sym] * $bc[5][$sym];
            $win4 = ($bc[1][$sym] * $bc[2][$sym] * $bc[3][$sym] * $bc[4][$sym]) * ($this->barSymsCount[5] - $wc[5]) - $win5 ;
            $win3 = ($bc[1][$sym] * $bc[2][$sym] * $bc[3][$sym]) * ($this->barSymsCount[4] - $wc[4] - $bc[4][$sym]) * $this->barSymsCount[5];
            $win2 = ($bc[1][$sym] * $bc[2][$sym]) * ( $this->barSymsCount[3] - $wc[3] - $bc[3][$sym] ) * $this->barSymsCount[4] * $this->barSymsCount[5];


            $win5w = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * ($bc[3][$sym] + $wc[3]) * ($bc[4][$sym] + $wc[4]) * ($bc[5][$sym] + $wc[5]) - $win5;
            $win4w = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * ($bc[3][$sym] + $wc[3]) * ($bc[4][$sym] + $wc[4]) * $this->barSymsCount[5] - $win4 - $win5w - $win5;
            $win3w = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * ($bc[3][$sym] + $wc[3]) * $this->barSymsCount[4]  * $this->barSymsCount[5] - $win3 - $win4 - $win4w - $win5w - $win5;
            $win2w = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * $this->barSymsCount[3] * $this->barSymsCount[4] * $this->barSymsCount[5] - $win2 - $win3w - $win3 - $win4w - $win4 - $win5w - $win5;


            $win5 *= $this->pay($sym, 5) * $this->amount_line * $this->multiplier;
            $win4 *= $this->pay($sym, 4) * $this->amount_line * $this->multiplier;
            $win3 *= $this->pay($sym, 3) * $this->amount_line * $this->multiplier;
            $win2 *= $this->pay($sym, 2) * $this->amount_line * $this->multiplier;


            $win5w *= $this->pay($sym, 5) * $this->amount_line * $this->multiplier * $this->wild_multiplier;
            $win4w *= $this->pay($sym, 4) * $this->amount_line * $this->multiplier * $this->wild_multiplier;
            $win3w *= $this->pay($sym, 3) * $this->amount_line * $this->multiplier * $this->wild_multiplier;
            $win2w *= $this->pay($sym, 2) * $this->amount_line * $this->multiplier * $this->wild_multiplier;

            $win += $win5 + $win4 + $win3 + $win2 + $win5w + $win4w + $win3w + $win2w;

        }


        $scatterWin = 0;
        foreach ($this->anypay as $sym) {
            if (isset($allCount[$sym])) {
                $scatterWin += $this->pay($sym, $allCount[$sym]) * $this->amount * $this->multiplier;
            }

        }

        $this->win = [0 => $scatterWin, 1 => $win];
        $this->win_all = $scatterWin + $win;


    }



}
