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

/*

$pay[1]=[ 10=>['name'=>'Royal Flush', 'pay'=>250 ],
					9=>['name'=>'Straight Flush', 'pay'=>50 ],
					8=>['name'=>'Four of a Kind', 'pay'=>25 ],
					7=>['name'=>'Full House', 'pay'=>9 ],
					6=>['name'=>'Flush', 'pay'=>6 ],
					5=>['name'=>'Straight', 'pay'=>4 ],
					4=>['name'=>'Three of a Kind', 'pay'=>3 ],
					3=>['name'=>'Two pairs', 'pay'=>2 ],
					2=>['name'=>'Jacks or Better', 'pay'=>1 ],
					1=>['name'=>'Hi card','pay'=>0 ],
					0=>['name'=>'No', 'pay'=>0 ]
				];

$pay[5]=$pay[4]=$pay[3]=$pay[2]=$pay[1];

$pay[5][10]['pay']=4000/5;
$pay[4][10]['pay']=1000/4;
$pay[3][10]['pay']=750/3;
$pay[2][10]['pay']=500/2;
 */

$l['pay']=[
    1=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>20,9=>50,10=>250],
    2=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>20,9=>50,10=>250],
    3=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>20,9=>50,10=>250],
    4=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>20,9=>50,10=>250],
    5=>[0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>20,9=>50,10=>800],
];

$l['paycard']=10;

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