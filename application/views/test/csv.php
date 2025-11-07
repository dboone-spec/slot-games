<?php $i = 1;
foreach ($config as $game => $c) : ?>
<?php echo $games[$game]['visible_name'] ?>

Sr.No;Description;Comment
1;Symbols; <?php echo  $info[$game][5] ?>

2;Wild;<?php echo count($c['wild']) > 0 ? 'yes' : 'no'; ?>

3;Scatter;<?php echo count($c['scatter']) > 0 ? 'yes' : 'no'; ?>

4;Paylines;<?php echo $c['linesCount'] > 0 ? 'yes' : 'no'; ?>

5;Number of paylines;<?php echo $c['linesCount'] ?> Paylines
6;Background brightness
7;Background dynamics
8;Win animation;Yes
9;Big Win animation;Yes
10;Sound effects during spins;Yes
11;Sound effects during Big Win;Yes
12;Slot sound effects;Yes
13;Free spins;<?php echo $c['FG'] ?>

14;Risk Game;Yes
15;Bonus games;No
16;Autospins;Yes
17;Autospins with spin quantity selection;No
18;Instant spin;Yes
19;Current balance;in Bottom
20;Demo; <?php echo  $info[$game][4] ?>

21;Spin with spacebar;Yes
22;Interactive post-win;No
23;3D symbols;No
24;3D animations on win;No
25;3D animations in bonus game;No
26;3D animations on all devices;No
27;RTP;<?php echo  $games[$game]['rtp'] ?>

28;Min bet;<?php echo  $info[$game][1] ?> EUR
29;Max bet;<?php echo  $info[$game][2] ?> EUR
30;Default bet;<?php echo  $info[$game][0] ?> EUR
31;Default number of paylines;<?php echo $c['linesCount'] ?? 'No' ?> Paylines
32;Game description
33;Reels;<?php echo $c['heigth'] ?> Reels
34;min win;Not Specified
35;max win;x<?php echo $c['maxMult'] ?>

36;Volatility;<?php echo  $info[$game][3] ?>

37;Supported language    Azerbaijani, Chinese, German, Spanish, Vietnamese, French, English, Georgian, Indian, Russian, Korean, Portuguese, Turkish, Ukrainian, Bengali, Thai, Greek, Indonesian, Italian, Kazakh, Kyrgyz, Marathi, Polish, Swahili, Telugu, Tajik, Uzbek


<?php $i++; endforeach; ?>