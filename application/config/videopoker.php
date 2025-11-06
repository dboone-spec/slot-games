<?php

$pay[1]=[ 10=>['name'=>'Royal Flush', 'pay'=>500 ],
					9=>['name'=>'Straight Flush', 'pay'=>50 ],
					8=>['name'=>'Four of a Kind', 'pay'=>25 ],
					7=>['name'=>'Full House', 'pay'=>6 ],
					6=>['name'=>'Flush', 'pay'=>5 ],
					5=>['name'=>'Straight', 'pay'=>4 ],
					4=>['name'=>'Three of a Kind', 'pay'=>3 ],
					3=>['name'=>'Two pairs', 'pay'=>2 ],
					2=>['name'=>'Tens or Better', 'pay'=>1 ],
					1=>['name'=>'Hi card','pay'=>0 ],
					0=>['name'=>'No', 'pay'=>0 ]
				];

$pay[5]=$pay[4]=$pay[3]=$pay[2]=$pay[1];

$pay[5][10]['pay']=4000/5;
$pay[4][10]['pay']=3000/4;
$pay[3][10]['pay']=2000/3;
$pay[2][10]['pay']=1000/2;
		




$a['tensorbetter']=[
			'pay'=>$pay,
		];
	
			
			
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


$a['jacksorbetter']=[
						'pay'=>$pay,
					];
		
		

$pay[1]=[ 12=>['name'=>'Royal Flush', 'pay'=>500 ],
			11=>['name'=>'Four Aces', 'pay'=>80 ],
			10=>['name'=>'Straight Flush', 'pay'=>50 ],
			9=>['name'=>'Four faces', 'pay'=>40 ],
			8=>['name'=>'Four 2s through 10s', 'pay'=>25 ],
			7=>['name'=>'Full House', 'pay'=>6 ],
			6=>['name'=>'Flush', 'pay'=>5 ],
			5=>['name'=>'Straight', 'pay'=>4 ],
			4=>['name'=>'Three of a Kind', 'pay'=>3 ],
			3=>['name'=>'Two pairs', 'pay'=>2 ],
			2=>['name'=>'Jacks or Better', 'pay'=>1 ],
			1=>['name'=>'Hi card','pay'=>0 ],
			0=>['name'=>'No', 'pay'=>0 ]
				];

$pay[5]=$pay[4]=$pay[3]=$pay[2]=$pay[1];

$pay[5][12]['pay']=4000/5;
$pay[4][12]['pay']=3000/4;
$pay[3][12]['pay']=2000/3;
$pay[2][12]['pay']=1000/2;


$a['acesandfaces']=[
						'pay'=>$pay,
					];

		
		/*
	'blackjacksilver'=>[
			
		],
	
	
	'blackjackgold'=>
		[
			
		],
	
	
	'blackjackdiamond'=>
		[
			
		],
],
*/
	
return $a;
