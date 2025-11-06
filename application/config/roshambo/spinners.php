<?php

//[lose,draw,win]

$l['rules']=[
    0=>[1],
    1=>[2],
    2=>[0],
];

$l['pay']=[
    1=>[
        ['draw'=>1,'win'=>1.9,'absolute'=>0],
        ['draw'=>0,'win'=>2.9,'absolute'=>0],
    ],
];


return $l;