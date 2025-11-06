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
    2=>[
        ['draw'=>1,'win'=>1.9,'absolute'=>0],
        ['draw'=>0,'win'=>2.9,'absolute'=>0],
        ['draw'=>0,'win'=>0,'absolute'=>8.7],
    ],
    3=>[
        ['draw'=>0,'win'=>3.7,'absolute'=>0],
        ['draw'=>1,'win'=>1.85,'absolute'=>0],
        ['draw'=>2,'win'=>0,'absolute'=>0],
        ['draw'=>0,'win'=>0,'absolute'=>26],
    ],
    4=>[
        ['draw'=>0,'win'=>5.2,'absolute'=>0],
        ['draw'=>1,'win'=>1.8,'absolute'=>0],
        ['draw'=>1.53,'win'=>0,'absolute'=>0],
        ['draw'=>0.5,'win'=>3.5,'absolute'=>0],
        ['draw'=>0,'win'=>0,'absolute'=>78],
    ],
    5=>[
        ['draw'=>0,'win'=>7.6,'absolute'=>0],
        ['draw'=>1,'win'=>1.7,'absolute'=>0],
        ['draw'=>1.3,'win'=>0,'absolute'=>0],
        ['draw'=>0.5,'win'=>4.6,'absolute'=>0],
        ['draw'=>0.3,'win'=>5.8,'absolute'=>0],
        ['draw'=>0,'win'=>0,'absolute'=>234],
    ],
];

$l['lines_choose']=[1,2,3,4,5];
$l['bets']=[1,2,3,4,5];
return $l;