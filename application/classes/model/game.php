<?php

class Model_Game extends ORM
{
    protected $_db_group = 'games';
    protected $_has_many = [
            'officegame' => [
                'model' => 'office_game',
                'foreign_key' => 'game_id',
            ],
        ];

    protected $_has_one = [
        'egtgame' => [
            'model' => 'egtgame',
            'foreign_key' => 'game_id',
        ],
    ];


    protected $_table_columns = [
            'id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'id',
                'column_default' => 'nextval("games_id_seq"::regclass)',
                'is_nullable' => '',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'name' =>
            [
                'type' => 'string',
                'column_name' => 'name',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'visible_name' =>
            [
                'type' => 'string',
                'column_name' => 'visible_name',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'provider' =>
            [
                'type' => 'string',
                'column_name' => 'provider',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '15',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'type' =>
            [
                'type' => 'string',
                'column_name' => 'type',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '15',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'brand' =>
            [
                'type' => 'string',
                'column_name' => 'brand',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '15',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'label' =>
            [
                'type' => 'string',
                'column_name' => 'label',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '15',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'external_id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'external_id',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'image' =>
            [
                'type' => 'string',
                'column_name' => 'image',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'show' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'show',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'mobile' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'mobile',
                'column_default' => '1',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'tech_type' =>
            [
                'type' => 'string',
                'column_name' => 'tech_type',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '2',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'demo' =>
            [
                'type' => 'string',
                'column_name' => 'brand',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'text' =>
            [
                'type' => 'string',
                'column_name' => 'text',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'category' =>
            [
                'type' => 'string',
                'column_name' => 'category',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
			'rtp' =>
            [
                'type' => 'string',
                'column_name' => 'rtp',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
			'branded' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'branded',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
			
			 'created' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'external_id',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
			
        ];

        public function labels()
        {
            return [
                'name' => 'name',
                'visible_name' => 'Название',
                'provider' => 'Провайдер',
                'brand' => 'Бренд',
                'show' => 'Показывать?',
                'tech_type' => 'Технология',
            ];
        }

    public function get_link($domain=null) {
        $link = '';
        switch ($this->provider) {
            case 'fsgames':
            case 'our':
                $link = '/games/' . $this->brand . '/' . $this->name;
                if($this->brand!='egt' && $this->brand!='agt') {
                    $link.='/';
                }

                if(!th::isMobile()) {
//                    $link .= 'play';
                }
                else {
//                    $link .= 'mobile';
                }
                if($this->brand=='live'){
                    $link = 'bglive/' . $this->name;
                }
                break;
            case 'imperium':
                $mobile = th::isMobile()?'mobile':'index';
                if(UTF8::strtolower($this->brand)=='netent') {
                    $mobile='mobile';
                }
                $link = '/i/' . $mobile . '/' . $this->external_id;
                break;
            default :
                $link = '/';
                break;
        }

        if(!is_null($domain)) {
            $link = $domain.$link;
        }

        return $link;
    }

    public function get_demo_link() {
        $demo_link = 'demo.' . dd::get_domain(THEME) . $this->get_link();

        return $demo_link;
    }
    
    
    public function embedDemo(){
        
        
        $pos= strpos($this->demo,'?v=');

        if ($pos!==false){
            return 'https://www.youtube.com/embed/'.substr($this->demo,$pos+3);
        }

        
        return null;
        
        
    }
	
	protected function _getBets($params)
    {
        extract($params);

        $defMinBet=$minBet;
        $defMaxBet=$maxBet;

		if($this->name=='foolsday') {
            $linesCount=100;
        }

        $minProvisionalWin = $minBet / $linesCount * $minMult;
        $maxProvisionalWin = $maxBet * $linesCount * $maxMult;

        if ($maxProvisionalWin > $maxWin) {
            $divisionRatio = $maxProvisionalWin / $maxWin;

            $minBet /= $divisionRatio;
            $maxBet /= $divisionRatio;
        }

        $minDisplayedBet = 1 / pow(10,$mult);
        if ($minBet < $minDisplayedBet / $linesCount) {
            $minBet = $minDisplayedBet / $linesCount;
        }

        if ($minProvisionalWin < $minDisplayedBet) {
            $minBet *= $minDisplayedBet / $minProvisionalWin;
        }

        $linesMult=$linesCount;

        $minBet *= $linesMult;
        $maxBet *= $linesMult;

        if($maxBet>$defMaxBet) {
			
            $maxBet=$defMaxBet;
			
			if($maxBet/$minBet<100) {
				//$minBet=$defMinBet;
				$minBet=$defMinBet/$minMult*$linesCount;
			}
        }
		
		if($minBet<$defMinBet) {
            $minBet=$defMinBet;
        }

        $MAX_ELEMS = 25;
        $initialSequence = [10, 5, 2, 2.5, 8, 1.5, 1.2, 4, 3, 6];
        $fullSequence = array_map(function($num) use($linesMult) {
            return round($num*$linesMult);
        },$initialSequence);

        $sortedSequence = array_merge($fullSequence);
        sort($sortedSequence);

        $betsDiff = round($maxBet / $minBet, $mult);
        $lastOrderData = $this->_getLastOrderData($betsDiff, $initialSequence);
        $mainDiff = $lastOrderData['lastOrderDiff'] ? round($betsDiff / $lastOrderData['lastOrderDiff']) : $betsDiff;
        $mainOrdersCount = strlen((string)$mainDiff) - 1;

        $ordersCount = $lastOrderData['lastOrderDiffPart'] + $mainOrdersCount;
		
		if($ordersCount==0) {
			$ordersCount=1;
		}
		
        $elemsPerOrder = floor(($MAX_ELEMS - 1) / $ordersCount);

        $sequence = $elemsPerOrder > count($fullSequence) ? $sortedSequence : array_slice($fullSequence, 0, $elemsPerOrder);
        sort($sequence);
        $result = array($minBet);

        $currentBet = $minBet;

        $limits=100;

        while ($limits>0) {
            $limits--;

            $currentBetMultiplier = $this->_calcBetMultiplier($currentBet,$linesMult);

            $nextSequenceNum;

            foreach ($sequence as $num) {
                if ($num > $currentBet * $currentBetMultiplier) {
                    $nextSequenceNum = $num;
                    break;
                }
            }

            $closestBiggerBet = $nextSequenceNum / $currentBetMultiplier;

            if ($closestBiggerBet > $maxBet) {
                if ($maxBet / $result[count($result) - 1] > (1 + 1 / count($sequence)) && count($sequence) < count($fullSequence)) {
                    $nextFullSequenceNum = array_reverse(array_filter(array_map('floatval', $fullSequence), function($sequenceNum) use ($maxBet, $currentBetMultiplier, $mult) {
                        return $sequenceNum < round($maxBet * $currentBetMultiplier, $mult);
                    }));
                    if ($nextFullSequenceNum) {
                        $result[] = $nextFullSequenceNum[0] / $currentBetMultiplier;
                    }
                }
                break;
            }

            $currentBet = $closestBiggerBet;

            if (($closestBiggerBet / $minBet) > (1 + 1 / count($sequence))) {
                $result[] = $closestBiggerBet;
            }
        }

        $result=array_unique($result);
        sort($result);

        $formattedNumbers = [];
        foreach ($result as $num) {
            $formattedNum = $num;
            if ($formattedNum !== 0) {
                $formattedNumbers[] = $formattedNum;
            }
        }

        $uniqueNumbers = [];
        $previousNum = null;
        foreach ($formattedNumbers as $idx => $num) {
            if ($idx === 0 || $num !== $previousNum) {
                $uniqueNumbers[] = $num;
            }
            $previousNum = $num;
        }

        $finalResult = $uniqueNumbers;

        $finalResult = array_filter($finalResult, function ($num) use ($mult) {
            $f = number_format($num, $mult, '.', '');
            return $f == $num;
        });

        if($fixedLines) {
            $finalResult = array_filter($finalResult, function ($num) use ($minMult, $mult,$linesMult) {
                $f = $num * $minMult * pow(10,$mult)  / $linesMult;
                return $f == intval($f);
            });
        }

        return array_values($finalResult);
    }

    protected function _getLastOrderData($betsDiff, $initialSequence)
    {

        $betsDiffStr = (string)$betsDiff;
        $isDegreeOf10 = $betsDiffStr[0] === '1' && preg_match('/[^0]/', substr($betsDiffStr, 1)) === 0;

        $initialSortedSequence=array_merge($initialSequence);
        sort($initialSortedSequence);

        if ($isDegreeOf10) {
            return array(
                'lastOrderDiff' => null,
                'lastOrderDiffPart' => 0,
            );
        }

        $lastOrderDiff = $betsDiff;
        while ($lastOrderDiff > 10) {
            $lastOrderDiff /= 10;
        }

        $resultIdx = array_search($lastOrderDiff, $initialSortedSequence, true);

        if ($resultIdx === false) {
            $resultIdx = 0;
            while ($initialSortedSequence[$resultIdx] <= $lastOrderDiff) {
                $resultIdx++;
            }
        }

        $lastOrderDiffPart = round(($resultIdx + 1) / count($initialSortedSequence), 2);

        return array(
            'lastOrderDiff' => $lastOrderDiff,
            'lastOrderDiffPart' => $lastOrderDiffPart
        );
    }

    protected function _calcBetMultiplier($bet,$linesMult)
    {
        $multiplier = 1;

        while ($bet * $multiplier < 1*$linesMult) {
            $multiplier *= 10;
        }

        while ($bet * $multiplier >= 10*$linesMult) {
            $multiplier /= 10;
        }

        return $multiplier;
    }

    public function needReduceMaxRate() {
        return in_array($this->name,['aislot','anonymous','bankofny','bigfive','leprechaun','infinitygems','bookofset','stalker','tesla','timemachine2','arabiannights','firefighters','bankofny','iceqween','piratesgold','bitcoin','egypt']);
    }

    public function getMinMaxRate($c, $maxrate,$max_lines)
    {
        $minrate = 9999999;
        $maxAnypayRate = 0;
        $minAnypayRate = 9999999;

        if($this->name=='foolsday') {
            return [4,409];
        }

        if (!in_array($this->type, ['moon'])) {
            if (isset($c['pay'])) {
                foreach ($c['pay'] as $n => $pt) {

                    if ($this->type=='slot' && (isset($c['anypay']) && in_array($n, $c['anypay'])) || (isset($c['scatter']) && in_array($n, $c['scatter']))) {

                        if($this->name=='pedrope' || $this->name=='monet') {
//                            var_dump($pt);
                        }

                        $pt = array_filter($pt, function ($a) {
                            return $a > 0;
                        });

                        if(empty($pt)) {
                            continue;
                        }

                        if (max($pt) > $maxAnypayRate) {
                            $maxAnypayRate = max($pt);
                        }

                        if (min($pt) < $minAnypayRate) {
                            $minAnypayRate = min($pt);
                        }

                        continue;
                    }

                    if ($this->type == 'roshambo') {

                        $min_rates = array_column($pt, 'draw');

                        $min_rates = array_filter($min_rates, function ($a) {
                            return $a > 0;
                        });

                        $max_rates = array_merge(array_column($pt, 'absolute'), array_column($pt, 'win'));

                        $max_rates = array_filter($max_rates, function ($a) {
                            return $a > 0;
                        });

                        if (max($max_rates) > $maxrate) {
                            $maxrate = max($max_rates);
                        }

                        if (min($min_rates) < $minrate) {
                            $minrate = min($min_rates);
                        }
                    } else {
                        if ($pt[count($pt) - 1] > $maxrate) {
                            $maxrate = $pt[count($pt) - 1];
                        }

                        $pt = array_filter($pt, function ($a) {
                            return $a > 0;
                        });

                        if (!count($pt)) {
                            continue;
                        }

                        if (min($pt) < $minrate) {
                            $minrate = min($pt);
                        }
                    }
                }
            }
        } else {
            $minrate = 1.2;
        }

        if(isset($c['wild_multiplier']) && $c['wild_multiplier']>1) {
//            $maxrate*=$c['wild_multiplier'];
        }

        //novomatic
        if($this->needReduceMaxRate() ) {

            $defmaxrate=$maxrate;
            $maxrate=1.2*$maxrate/$max_lines;

            if($maxrate>$defmaxrate) {
                $maxrate=$defmaxrate;
            }
        }

        if($minrate>$maxrate) {
            return [$minAnypayRate,$maxAnypayRate];
        }

        return [$minrate, $maxrate];
    }

    public function getAllBets(Model_Office $o, Model_Currency $currency,$max_lines)
    {

        if (!$this->loaded()) {
            return [];
        }

		if($this->name=='supabets') {
            return [0.5,1,2,5,10,20,50,100,150,200,250,500,1000,1500,2000,2500,5000];
        }

        if($this->type=='moon') {

            $bet_min = $currency->moon_min_bet ?? 0.1;
            $bet_max = $currency->moon_max_bet ?? 100;

            $bet_values=[$bet_min,10,20,50,100];
            if($this->name=='aerobet') {
                $bet_values=[10,50,100,200,500];
            }
            foreach ($bet_values as &$bv) {
                $bv=$currency->formatBet($bv,true,true);
            }

            if(!in_array($bet_max,$bet_values)) {
                $bet_values[]=$currency->formatBet($bet_max*10,true,true);
            }


            return $bet_values;
        }

        $maxWin = $o->getWinLimit();

        $config_name='agt/' . $this->name;

        if($this->type=='keno') {
            $config_name='keno/' . $this->name;
        }

        if($this->type=='roshambo') {
            $config_name='roshambo/' . $this->name;
        }

        if($this->type=='videopoker') {
            $config_name='videopoker/' . $this->name;
        }

        $c = Kohana::$config->load($config_name);

        $needcalc = true;

        if (isset($c['staticlines']) && !empty($c['staticlines'])) {
            $c['lines_choose'] = $c['staticlines'];
            $max_lines=$c['staticlines'][0];
        }

        if (in_array($this->type, ['videopoker', 'keno','roshambo'])) {
            $c['lines_choose'] = [1];
        }

        $bet_min = $o->bet_min > 0 ? $o->bet_min : 0.01/$currency->val;
        $bet_max = $o->bet_max > 0 ? $o->bet_max : 1000/$currency->val;

        if (in_array($this->type, ['roshambo'])) {
			if(!th::isB2B($o->owner)) {
				$bet_min = $currency->roshambo_min_bet ?? 5;
				$bet_max = $currency->roshambo_max_bet ?? 500;
			}
            $needcalc = false;
        }

        $maxrate=0;

        list($minrate,$maxrate)=$this->getMinMaxRate($c,$maxrate,$max_lines);


        if($minrate>$maxrate) {
            //одни скаттеры?
            $max_lines=1;
        }

        if (!$needcalc) {
            $max_lines = 1;
        }

        $fixedLines=isset($c['staticlines']) && !empty($c['staticlines']);

        if($this->type=='videopoker') {
            $bet_max=200/$currency->val;
        }

        $try = [
            'minBet' => $bet_min,
            'maxBet' => $bet_max,
            'linesCount' => $max_lines,
            'maxMult' => $maxrate > 0 ? $maxrate : 15000,
            'minMult' => $minrate,
            'maxWin' => $maxWin,
            'mult' => $currency->mult,
            'fixedLines' => $fixedLines,
        ];

        return $this->_getBets($try);
    }
}
