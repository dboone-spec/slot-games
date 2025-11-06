<?php

$l=[];

//1-rock
//2-paper
//3-scissors


$l['lines']=[];


//[lose,draw,win]

$l['pay']=[
    1=>[
        ['draw'=>1,'win'=>1.9,'absolute'=>0],
        ['draw'=>0,'win'=>2.9,'absolute'=>0],
    ],
];

$l['lines_choose']=[1,2,3,4,5];
$l['bets']=[1,2,3,4,5];
return $l;