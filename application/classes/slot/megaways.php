<?php

class Slot_Megaways extends Slot_Agt
{

    public $minLen = 2, $maxLen = 7;
    public $screen = [], $barSymsCount;
    public $winSymsPos;


    public function __construct($name)
    {
        parent::__construct($name);
        $this->maxLen = $this->heigth;

    }


    public function fillLines()
    {
        $this->lines = [1 => []];
    }


    public function sym($num = null)
    {
        return $this->screen;
    }


    public function spin($mode = null)
    {

        $this->screenSyms = [];

        for ($i = 1; $i <= $this->barcount; $i++) {
            $this->pos[$i] = math::random_int(0, count($this->bars[$i]) - 1);
            $min = $this->minLen;
            $this->barSymsCount[$i] = mt_rand($min, $this->maxLen);
        }


        for ($i = 1; $i <= $this->barcount; $i++) {
            $this->screen[$i] = array_slice($this->bars[$i], $this->pos[$i], $this->barSymsCount[$i]);
            if (count($this->screen[$i]) < $this->barSymsCount[$i]) {
                $this->screen[$i] = array_merge($this->screen[$i], array_slice($this->bars[$i], 0, $this->barSymsCount[$i] - count($this->screen[$i])));
            }
        }

        $this->correct_pos();
        $this->win();
    }


    public function printBar()
    {

        $result = [];

        for ($y = 0; $y < $this->heigth; $y++) {
            $one = [];
            for ($x = 1; $x <= $this->barcount; $x++) {
                $value = $this->screen[$x][$y] ?? ' ';
                $value = $value < 10 ? ' ' . $value : $value;
                $one[] = $value;
            }
            $result[] = implode(' ', $one);
        }

        return implode("\r\n", $result);

    }


    public function win()
    {

        $this->win_all = 0;
        $this->win = [];

        $this->LineWinLen = [];


        $syms = array_keys($this->pay);

        //$barsCountValues
        $bc = [];

        foreach ($this->screen as $num => $bar) {
            $bc[$num] = array_count_values($bar);
            foreach ($syms as $sym) {
                $bc[$num][$sym] = $bc[$num][$sym] ?? 0;
            }
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


        $win = 0;
        $len = [];


        foreach ($syms as $sym) {

            if (in_array($sym, $this->wild) || in_array($sym, $this->anypay) || in_array($sym, $this->scatter)) {
                continue;
            }


            //на первом барабане wild нет, поэтому можем считать так, халява
            $win6 = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * ($bc[3][$sym] + $wc[3]) * ($bc[4][$sym] + $wc[4]) * ($bc[5][$sym] + $wc[5]) * ($bc[6][$sym] + $wc[6]);
            $win5 = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * ($bc[3][$sym] + $wc[3]) * ($bc[4][$sym] + $wc[4]) * ($bc[5][$sym] + $wc[5]);
            $win4 = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * ($bc[3][$sym] + $wc[3]) * ($bc[4][$sym] + $wc[4]);
            $win3 = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]) * ($bc[3][$sym] + $wc[3]);
            $win2 = ($bc[1][$sym] + $wc[1]) * ($bc[2][$sym] + $wc[2]);


            if ($win6 > 0 && $this->pay($sym, 6) > 0) {
                $this->win[$sym] = $win6 * $this->amount * $this->multiplier * $this->pay($sym, 6);
                $this->LineWinLen[$sym] = 6;
            } elseif ($win5 > 0 && $this->pay($sym, 5) > 0) {
                $this->win[$sym] = $win5 * $this->amount * $this->multiplier * $this->pay($sym, 5);
                $this->LineWinLen[$sym] = 5;
            } elseif ($win4 > 0 && $this->pay($sym, 4) > 0) {
                $this->win[$sym] = $win4 * $this->amount * $this->multiplier * $this->pay($sym, 4);
                $this->LineWinLen[$sym] = 4;
            } elseif ($win3 > 0 && $this->pay($sym, 3) > 0) {
                $this->win[$sym] = $win3 * $this->amount * $this->multiplier * $this->pay($sym, 3);
                $this->LineWinLen[$sym] = 3;
            } elseif ($win2 > 0 && $this->pay($sym, 2) > 0) {
                $this->win[$sym] = $win2 * $this->amount * $this->multiplier * $this->pay($sym, 2);
                $this->LineWinLen[$sym] = 2;
            }


        }

        foreach ($this->anypay as $sym) {
            $winAny = 0;
            for ($i = 1; $i <= 6; $i++){
                $winAny += $bc[$i][$sym] ?? 0;
            }

            $win = $this->amount * $this->multiplier * $this->pay($sym, $winAny);

            if($win>0) {
                $this->win[$sym]=$win;
                $this->LineWinLen[$sym]=$winAny;
            }
        }


        $this->win_all = array_sum($this->win);

        $this->freerun = 0;
        $cf = 0;

        $count = array_fill(0, 20, 0);
        foreach ($this->screen as $bar) {
            foreach (array_count_values($bar) as $el => $countEl) {
                $count[$el] += $countEl;
            }
        }

        foreach ($this->scatter as $sym) {
            if (isset($count[$sym])) {
                $cf += $count[$sym];
            }
        }

        //выплат по скаттер не предусмотрено
        $this->freerun = $this->free_games[$cf];


    }

    /**
     * @param $num - символ, если указан параметр и выигрыша по линии нет возвращает пустой массив
     * @return array of array of array, индексы основоного массива - номера выигрышных символов, значения - двумерный массив
     * индексы первого вложенного массива барабаны начинаются с индекса 1,
     * значения второго вложенного массива - позиции символов, начинаются с 0
     * выплат по скаттер не предусмотрено
     *
     */
    public function lightingLine($num = null)
    {

        $result = [];
        if (is_null($num)) {
            foreach ($this->win as $sym => $win) {
                $result[$sym] = $this->lightingLine($sym);
            }

            return $result;
        }

        if (($this->win[$num] ?? 0) > 0) {

            foreach ($this->screen as $numBar => $bar) {
                if ($this->LineWinLen[$num] >= $numBar) {
                    $result[$numBar] = array_keys($bar, $num);
                    foreach ($this->wild as $wild) {
                        $result[$numBar] = array_merge($result[$numBar], array_keys($bar, $wild));
                    }
                }


            }
            return $result;
        }

        return [];


    }

}
