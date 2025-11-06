<?php


	/*
	if ($this->RoyalFlush()) return 10;
	if ($this->StraightFlush()) return 9;
	if ($this->Quads()) return 8;
	if ($this->FullHouse()) return 7;
	if ($this->Flash()) return 6;
	if ($this->Straight()) return 5;
	if ($this->Set()) return 4;
	if ($this->TwoPair()) return 3;
	if ($this->OnePair()) return 2;
	if ( $this->HighCard()) return 1;

*/


$l['level']=[ 10=>'Royal Flush',
            9=>'Straight Flush',
            8=>'Four of a Kind',
            7=>'Full House',
            6=>'Flush',
            5=>'Straight',
            4=>'Three of a Kind',
            3=>'Two pairs',
            2=>'Jacks or Better',
            1=>'Hi card',
            0=>'No',
    ];


			

$l['pay']=[
    1=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>9,8=>25,9=>50,10=>250],
    2=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>9,8=>25,9=>50,10=>250],
    3=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>9,8=>25,9=>50,10=>250],
    4=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>9,8=>25,9=>50,10=>250],
    5=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>9,8=>25,9=>50,10=>800],
];

$l['paycard']=11;


return $l;


