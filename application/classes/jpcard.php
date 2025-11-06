<?php

class jpcard extends Videopoker_Calc
{

    protected $cardCount = 7;
    public $paycard      = 0;
    public $winlight     = true;

    public function __construct()
    {
        $this->betcoin = 1;
        return $this;
    }

    //контрольная сумма
    public function CheckSum(array $values,$sort = true)
    {

        if($sort)
        {
            rsort($values);
        }

        $values = array_slice($values,0,5);

        $sum = 0;
        foreach($values as $v)
        {
            $sum <<= 4;
            $sum += $v;
        }
        return $sum;
    }

    public function getJPNum($level)
    {
        $jpLevel = 0;

        if($level > 2)
        {
            $jpLevel++;
        }
        if($level > 3)
        {
            $jpLevel++;
        }
        if($level > 5)
        {
            $jpLevel++;
        }
        if($level > 9)
        {
            $jpLevel++;
        }

        return $jpLevel;
    }

    public function combName($comb)
    {

        $c = [
                0 => '',
                1 => 'High card',
                2 => 'One pair',
                3 => 'Two pairs',
                4 => 'Three of a kind',
                5 => 'Straight',
                6 => 'Flush',
                7 => 'Full House',
                8 => 'Four of a kind',
                9 => 'Straight Flush',
                10 => 'Royal Flush',
        ];

        return $c[$comb];
    }

    public function wincards()
    {
        $allcards = arr::pluck($this->card,'value');
        $wincards = $this->wincard;


        foreach($wincards as $wincard)
        {
            if(in_array($wincard,$allcards))
            {
                $i = array_search($wincard,$allcards);
                unset($allcards[$i]);
            }
        }


        while(count($wincards) < 5)
        {
            $wincards[] = array_pop($allcards);
        }

        foreach($wincards as $k => $v)
        {
            if(empty($v))
            {
                unset($wincards[$k]);
            }
        }

        return $wincards;
    }

    public function gencards()
    {
        $this->cards = [];
        $j           = 1;

        while(count($this->cards) < $this->cardCount)
        {
            $r = $this->getNewCard();
            if(!in_array($r,$this->cards))
            {
                $this->cards[$j] = $r;
                $j++;
            }
        }

//        return [17,14,11,25,20,21,41];
//        return [17,14,41,11,25,20,21];
//        return [30,44,29,2,40,20,48];
//        return [23,3,51,35,37,39,47];

//        return [15,28,42,27,33,44,1];
//        return [13,12,11,10,9,8,7]; //royal flush

        return $this->cards;
    }

    /**
      Флаш или Флэш (англ. flush — «масть»): пять карт одной масти,
      например: К♠ В♠ 8♠ 4♠ 3♠.
     */
    public function Flash()
    {


        if($this->c_suit[0] < 5)
        {
            return false;
        }


        $winSuit=array_keys($this->c_suit_raw,$this->c_suit[0])[0];
        $win=$this->LongSuitValues();
        $win= array_slice($win,0,5);

        $this->wincard=[];
        foreach($win as $card){
                $this->wincard[]=card::makecard($card, $winSuit);
        }

        return true;
    }

    /**
      Стрейт-флаш или Стрит-флэш (англ. straight flush — «масть по порядку»): любые пять карт одной масти по порядку,
      например: 9♠ 8♠ 7♠ 6♠ 5♠. Туз может как начинать порядок, так и заканчивать его.
     */
    public function StraightFlush()
    {
        if($this->c_suit[0] < 5)
        {
            return false;
        }

        if(!$this->HasOrder())
        {
            return false;
        }

        return $this->MakeStraight($this->LongSuitValues());
    }

    /**
      Роял-флаш или Роял-флэш (англ. royal flush — «королевская масть»): старшие (туз, король, дама, валет, десять) пять карт одной масти,
      например: Т♥ К♥ Д♥ В♥ 10♥.
     */
    public function RoyalFlush()
    {

        $v = $this->LongSuitValues();

        if($this->c_suit[0] < 5)
        {
            return false;
        }

        if(!$this->HasOrder())
        {
            return false;
        }

        if($v[0] == 14 and $v[1] == 13 and $v[2] == 12 and $v[3] == 11 and $v[4] == 10)
        {
            return 1;
        }

        return false;
    }

    protected $_LongSuitValues = null;

    public function LongSuitValues()
    {

        if(empty($this->_LongSuitValues))
        {

            //выбираем самую длинную масть:
            $suit                  = array_keys($this->c_suit_raw,$this->c_suit[0]);
            //их значения
            $keys                  = array_keys($this->suit,$suit[0]);



            $this->_LongSuitValues = array();
            foreach($keys as $k)
            {
                $this->_LongSuitValues[] = $this->value_raw[$k];
            }
            rsort($this->_LongSuitValues);
        }



        return $this->_LongSuitValues;
    }

//    public function Flash()
//    {
//        return $this->CheckSum($this->LongSuitValues());
//    }

    /**
     * СпецСортировка
     * @param 0 array для сортировки
     * @param 1 значения, которые ставить первыми
     */
    public function SpecSort(array $a,$value)
    {
        $ret = array();
        if(!is_array($value))
        {
            $value = array($value);
        }

        foreach($value as $v)
        {
            foreach(array_keys($a,$v) as $key)
            {
                $ret[] = $a[$key];
                unset($a[$key]);
            }
        }

        if(count($a) > 0)
        {
            rsort($a);
            foreach($a as $v)
            {
                $ret[] = $v;
            }
        }
        return $ret;
    }

    /**
      Две пары/Две двойки/Два плюс два (англ. two pairs): две пары карт,
      например: 8♣ 8♠ 4♥ 4♣ 2♠.
     */
    public function TwoPair()
    {
        //?????
        //3+2+2 не рассматриваем

        if($this->c_value[0] != 2 or $this->c_value[1] != 2)
        {
            return false;
        }

        $k = array_keys($this->c_value_raw,2);
        rsort($k);
        $k = array($k[0],$k[1]);

        $v = $this->SpecSort($this->value,$k);

        $win = $this->CheckSum($v,false);

        if($win && $this->winlight)
        {

            $key = array_keys($this->c_value_raw,2);

            rsort($key);

            $key = array_slice($key,0,2);

            foreach($this->card as $c)
            {
                if(in_array($c['num'],$key))
                {
                    $this->wincard[] = $c['value'];
                }
            }
        }

        return $win;
    }

    /**
     * Делает стрейт из указанной комбинации (если может)
     * на входе уникальный, отсортированный в обратном порядке массив
     */
    public function MakeStraight($v)
    {

        if(in_array(14,$v))
        {
            $v[] = 1;
        }

        $idx      = -1;
        $l        = 0;
        $max_line = 0;
        $ch       = 1;
        $count    = count($v);

        for($i = 0; $i < $count; $i++)
        {
            $ch--;
            if($ch == $v[$i])
            {
                //записываем начальный элемент
                if($l == 0 and ( $max_line == 0 or $max_line < 4))
                {
                    $idx = $i - 1;
                }
                $l++;
            }
            else
            {
                $l = 0;
            }
            $ch       = $v[$i];
            $max_line = max($max_line,$l);
        }


        if($max_line < 4)
        {
            return false;
        }

        $hand = array();
        for($i = 0; $i < 5; $i++)
        {
            $hand[] = $v[$i + $idx];
        }

        $win = $this->CheckSum($hand);

        if($win)
        {

            sort($hand);

            foreach($hand as &$h)
            {
                if($h == 1)
                {
                    $h = 14;
                }
            }

            foreach($this->card as $c)
            {
                if(in_array($c['num'],$hand))
                {
                    $this->wincard[] = $c['value'];
                    $i               = array_search($c['num'],$hand);
                    unset($hand[$i]);
                }
            }
        }
        return $win;
    }

    /**
      Фул-хаус/Полный сбор/Три плюс два (англ. full house, full boat — «полный дом», «полная лодка»): три карты одного достоинства и одна пара,
      например: 10♥ 10♦ 10♠ 8♣ 8♥.
     */
    public function FullHouse()
    {

        if($this->c_value[0] < 3)
        {
            return false;
        }

        //тех что 3
        $k = array_keys($this->c_value_raw,3);

        if(count($k) < 2 and ! in_array(2,$this->c_value))
        {
            return false;
        }

        rsort($k);

        //добавляем 2

        $k2 = array_keys($this->c_value_raw,2);

        rsort($k2);

        $k = array_merge($k,$k2);

        $v = $this->SpecSort($this->value,$k);


        $win  = $this->CheckSum($v,false);
        $hand = array_slice($v,0,5);


        if($win && $this->winlight)
        {
            foreach($this->card as $c)
            {
                if(in_array($c['num'],$hand))
                {
                    $this->wincard[] = $c['value'];
                    $i               = array_search($c['num'],$hand);
                    unset($hand[$i]);
                }
            }
        }

        return $win;
    }

    /**
      Стрейт или Стрит (англ. straight — «порядок»): пять карт по порядку любых мастей,
      например: 5♦ 4♥ 3♠ 2♦ Т♦. Туз может как начинать порядок, так и заканчивать его. В данном примере Т♦ начинает комбинацию и его достоинство оценивается в единицу, а 5♦ считается старшей картой.
     */
    public function Straight()
    {

        $v = $this->value;
        $v = array_unique($v);
        rsort($v);

        if(!$this->HasOrder())
        {
            return false;
        }

        return $this->MakeStraight($v);
    }

    public function hasOrder()
    {

        $values = $this->value;

        if(in_array(14,$values))
        {
            $values[] = 1;
        }

        $values   = array_unique($values);
        sort($values);
        $l        = 0;
        $max_line = 0;
        $ch       = -1;
        $count    = count($values);

        for($i = 0; $i < $count; $i++)
        {
            $ch++;
            if($ch == $values[$i])
            {
                $l++;
            }
            else
            {
                $l = 0;
            }
            $ch       = $values[$i];
            $max_line = max($max_line,$l);
        }

        return $max_line >= 4 ? true : false;
    }

    /**
     * уровень карточной комбинации
     * @param array $card - индексы карт в колоде
     */
    public function level(array $card)
    {

//        return 10;

        $cnt = count($card);

        $this->clear($card);

        if($cnt >= 5 && $this->RoyalFlush())
            return 10;
        if($cnt >= 5 && $this->StraightFlush())
            return 9;
        if($cnt >= 5 && $this->Quads())
            return 8;
        if($cnt >= 5 && $this->FullHouse())
            return 7;
        if($cnt >= 5 && $this->Flash())
            return 6;
        if($cnt >= 5 && $this->Straight())
            return 5;


        if($cnt >= 3 && $this->Set())
            return 4;
        if($cnt >= 4 && $this->TwoPair())
            return 3;
        if($cnt >= 2 && $this->OnePair())
            return 2;
        if($cnt >= 1 && $this->HighCard())
            return 1;

        return 0;
    }

}
