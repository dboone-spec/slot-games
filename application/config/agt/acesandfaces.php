<?php


/*

	if ($this->RoyalFlush()) return 12;
	if ($this->FourAces()) return 11;
	if ($this->StraightFlush()) return 10;
	if ($this->FourFaces()) return 9;
	if ($this->Four210()) return 8;
	if ($this->FullHouse()) return 7;
	if ($this->Flash()) return 6;
	if ($this->Straight()) return 5;
	if ($this->Set()) return 4;
	if ($this->TwoPair()) return 3;
	if ($this->OnePair()) return 2;
	if ( $this->HighCard()) return 1;
*/


$l['level']=[ 12=>'Royal Flush',
            11=>'Four Aces',
            10=>'Straight Flush',
            9=>'Four faces',
            8=>'Four 2s through 10s',
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
    1=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>25,9=>40,10=>50,11=>80,12=>500],
    2=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>25,9=>40,10=>50,11=>80,12=>500],
    3=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>25,9=>40,10=>50,11=>80,12=>2001/3],
    4=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>25,9=>40,10=>50,11=>80,12=>750],
    5=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>25,9=>40,10=>50,11=>80,12=>800],

];

$l['paycard']=11;


//это bets
$l['lines']=[
        1=>1,
        2=>2,
        3=>3,
        4=>4,
        5=>5,
];

$l['lines_choose']= [20, 15, 10, 5, 1];
return $l;