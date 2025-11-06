<?php

class Controller_Test extends Controller
{

	
    public function action_m()
    {
		echo 'try restart';
		var_dump(Cache::instance()->get('moon_apps','[]'));
		
		exit;
		
        $s = new Slot_Megaways('megatest');

        $s->amount= 1;
        $s->spin();

        print_r($s->win);
        echo $s->printBar();

        print_r($s->lightingLine());

    }


    protected function _getBets($params)
    {
        extract($params);
        $minProvisionalWin = $minBet / $linesCount * $minMult;
        $maxProvisionalWin = $maxBet * $linesCount * $maxMult;

        if ($maxProvisionalWin > $maxWin) {
            $minDivisionRatio = $maxProvisionalWin / $maxWin;

            $handyDividers = array(1, 2, 2.5, 5);

            $dividersOrder = 1;
            while ($minDivisionRatio > $dividersOrder * $handyDividers[count($handyDividers) - 1]) {
                $dividersOrder *= 10;
            }


            $divisionRatio = array_reduce(array_reverse($handyDividers), function ($previousValue, $currentFactor) use ($dividersOrder, $minDivisionRatio) {
                if ($currentFactor * $dividersOrder >= $minDivisionRatio) {
                    return $currentFactor * $dividersOrder;
                }

                return $previousValue;
            }, null);

            $minBet /= $divisionRatio;
            $maxBet /= $divisionRatio;
        }

        $minDisplayedBet = 1 / (10 ** 2);
        if ($minBet < $minDisplayedBet / $linesCount) $minBet = $minDisplayedBet / $linesCount;

        if ($minProvisionalWin < $minDisplayedBet) {
            $minBet *= $minDisplayedBet / $minProvisionalWin;
        }

        $MAX_ELEMS = 25;
        $fullSequence = array(10, 5, 2, 3, 8, 1.5, 1.2, 4, 2.5, 6);
        $sortedSequence = array_merge($fullSequence);
        sort($sortedSequence);

        $betsDiff = round($maxBet / $minBet, 2);
        $lastOrderData = $this->_getLastOrderData($betsDiff, $sortedSequence);
        $mainDiff = $lastOrderData['lastOrderDiff'] ? round($betsDiff / $lastOrderData['lastOrderDiff']) : $betsDiff;
        $mainOrdersCount = strlen((string)$mainDiff) - 1;

        $ordersCount = $lastOrderData['lastOrderDiffPart'] + $mainOrdersCount;
        $elemsPerOrder = floor(($MAX_ELEMS - 1) / $ordersCount);

        $sequence = $elemsPerOrder > count($fullSequence) ? $sortedSequence : array_slice($fullSequence, 0, $elemsPerOrder);
        sort($sequence);
        $result = array($minBet);

        $currentBet = $minBet;


        while (true) {
            $currentBetMultiplier = $this->_calcBetMultiplier($currentBet);

            $nextSequenceNum;

            foreach ($sequence as $num) {
                if ($num > $currentBet * $currentBetMultiplier) {
                    $nextSequenceNum = $num;
                    break;
                }
            }

            $closestBiggerBet = $nextSequenceNum / $currentBetMultiplier;

            if ($closestBiggerBet >= $maxBet) {
                $result[] = $maxBet;
                break;
            }

            $currentBet = $closestBiggerBet;

            if (($closestBiggerBet / $minBet) > (1 + 1 / count($sequence))) {
                $result[] = $closestBiggerBet;
            }
        }

        $map = array_map(function ($num) use ($linesCount) {
            return round($num * $linesCount, 2);
        }, $result);

        $result = array_unique(array_filter($map, function ($num, $idx) use ($result) {
            return $num != 0 && ($idx === 0 || $result[$idx - 1] !== $num);
        }, ARRAY_FILTER_USE_BOTH));

        $result = array_filter($result, function ($num) use ($linesCount, $minMult) {
            $f = ($num * $minMult * 100) / $linesCount;
            return $f == intval($f);
        });

        return $result;
    }

    protected function _getLastOrderData($betsDiff, $sortedSequence)
    {

        $betsDiffStr = (string)$betsDiff;
        $isDegreeOf10 = $betsDiffStr[0] === '1' && preg_match('/[^0]/', substr($betsDiffStr, 1)) === 0;

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

        $resultIdx = array_search($lastOrderDiff, $sortedSequence, true);

        if ($resultIdx === false) {
            $resultIdx = 0;
            while ($sortedSequence[$resultIdx] <= $lastOrderDiff) {
                $resultIdx++;
            }
        }

        $lastOrderDiffPart = round(($resultIdx + 1) / count($sortedSequence), 2);

        return array(
            'lastOrderDiff' => $lastOrderDiff,
            'lastOrderDiffPart' => $lastOrderDiffPart
        );
    }

    protected function _calcBetMultiplier($bet)
    {
        $multiplier = 1;

        while ($bet * $multiplier < 1) {
            $multiplier *= 10;
        }

        while ($bet * $multiplier >= 10) {
            $multiplier /= 10;
        }

        return $multiplier;
    }

    public function action_allbets()
    {

        echo '<script src="/js/getBets.js"></script>' . PHP_EOL;

        $games = db::query(1, 'select g.* from games g where g.branded=0 and g.show=1 order by g.visible_name')
            ->execute()
            ->as_array();

        $o = new Model_Office(777);
        $currency = $o->currency;

        $maxWin = arr::get($_GET, 'maxwin', PHP_INT_MAX);

        foreach ($games as $v) {
            $c = Kohana::$config->load('agt/' . $v['name']);

            $maxrate = 0;
            $minrate = 9999999;

            if (!in_array($v['type'], ['keno', 'moon'])) {

                if (!isset($c['pay'])) {
                    echo '<red>' . 'not found config pay: ' . $v['name'] . '; type: ' . $v['type'] . '</red><br>';
                } else {
                    foreach ($c['pay'] as $n => $pt) {

                        if ($v['type'] == 'slot' && (isset($c['anypay']) && in_array($n, $c['anypay'])) || (isset($c['scatter']) && in_array($n, $c['scatter']))) {
                            continue;
                        }

                        if ($v['type'] == 'roshambo') {
                            $pt = array_values($pt);
                            $pt = $pt[count($pt) - 1];
                            if (max($pt) > $maxrate) {
                                $maxrate = max($pt);
                            }

                            $pt = array_filter($pt, function ($a) {
                                return $a > 0;
                            });

                            $pt = $pt[count($pt) - 1];

                            if (min($pt) < $minrate) {
                                $minrate = min($pt);
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
            }

            $needcalc = true;

            if (isset($c['staticlines']) && !empty($c['staticlines'])) {
                $needcalc = false;
                $c['lines_choose'] = $c['staticlines'];
            }

            if (in_array($v['type'], ['videopoker', 'keno'])) {
                $c['lines_choose'] = [1];
            }

            $bet_min = $o->bet_min > 0 ? $o->bet_min : 0.01;
            $bet_max = $o->bet_max > 0 ? $o->bet_max : 50000;

            if (in_array($v['name'], ['roshambo', 'spinners'])) {
                $bet_min = $currency->roshambo_min_bet ?? 5;
                $bet_max = $currency->roshambo_max_bet ?? 500;

                $needcalc = false;
            }

            if (th::isMoonGame($v['name'])) {
                $bet_min = $currency->moon_min_bet ?? 0.1;
                $bet_max = $currency->moon_max_bet ?? 100;
            }

            if (in_array($v['type'], ['roshambo'])) {
                $s = array_map(function ($e) {
                    return count($e);
                }, $c['pay']);
                $c['lines_choose'] = array_reverse($s);
            }

            if (!isset($c['lines_choose'])) {
                var_dump($v);
            }

            $min_lines = $c['lines_choose'][count($c['lines_choose']) - 1];
            $max_lines = $c['lines_choose'][0];

            if (!$needcalc) {
                $max_lines = 1;
            }

            echo '<h1>' . $v['visible_name'] . '</h1>';

            $try = [
                'minBet' => $bet_min,
                'maxBet' => $bet_max,
                'linesCount' => $max_lines,
                'maxMult' => $maxrate > 0 ? $maxrate : 15000,
                'minMult' => $minrate,
                'maxWin' => $maxWin
            ];


            echo '<pre>' . PHP_EOL;
            echo 'MAX limits: ' . print_r($try, 1) . PHP_EOL;
            echo '</pre>';

            echo '<script>
            console.log(\'' . $v['visible_name'] . '\');
            console.log(\'MAX limits\',getBets({
                minBet: ' . $bet_min . ',
                maxBet: ' . $bet_max . ',
                linesCount: ' . $max_lines . ',
                maxMult: ' . ($maxrate > 0 ? $maxrate : 15000) . ',
                minMult: ' . ($minrate) . ',
                maxWin: ' . $maxWin . '
            }));
            </script>';

            $bets = $this->_getBets($try);

            if (empty($bets)) {
                echo '<h2 style="color:red">EMPTY</h2>';
            }

            echo '<b>' . implode(',', $bets) . '</b>';

            if ($max_lines != $min_lines) {

                $try['linesCount'] = $min_lines;

                echo '<pre>' . PHP_EOL;
                echo 'MIN limits: ' . print_r($try, 1) . PHP_EOL;
                echo '</pre>';

                echo '<script>
                console.log(\'' . $v['visible_name'] . '\');
                console.log(\'MIN limits\',getBets({
                    minBet: ' . $bet_min . ',
                    maxBet: ' . $bet_max . ',
                    linesCount: ' . $max_lines . ',
                    maxMult: ' . ($maxrate > 0 ? $maxrate : 15000) . ',
                    minMult: ' . ($minrate) . ',
                    maxWin: ' . $maxWin . '
                }));
                </script>';

                echo '<b>' . implode(',', $this->_getBets($try)) . '</b>';

            }
        }

    }

    public function action_megaconvert()
    {
        $conf = Kohana::$config->load('agt/hotways');

        echo '<pre>';

        $pay = $conf['pay'];

        sort($pay);

        $pay[11] = [0, 0, 0, 0, 0, 0, 0];
        $pay[12] = [0, 0, 0, 0, 0, 0, 0];

//        ksort($pay);


        foreach (($pay) as $i => $item) {
            echo '$l[\'pay\'][' . ($i) . ']=[' . implode(',', [0 => 0] + $item) . '];' . PHP_EOL;
        }

        $replaceSyms = function ($a) {
            if ($a == 12) return 12;
            if ($a == 11) return 0;
            if ($a == 10) return 1;
            if ($a == 9) return 2;
            if ($a == 8) return 3;
            if ($a == 7) return 4;
            if ($a == 6) return 5;
            if ($a == 5) return 6;
            if ($a == 4) return 7;
            if ($a == 3) return 8;
            if ($a == 2) return 9;
            if ($a == 1) return 10;
            if ($a == 0) return 11;
        };

        $bars = $conf['bars'];
        echo '$l[\'bars\']=[' . PHP_EOL;
        foreach ($bars as $num => $bar) {
            $newBar = array_map($replaceSyms, $bar);
            echo $num . '=>[' . implode(',', $newBar) . '],' . PHP_EOL;
        }
        echo '];' . PHP_EOL;

        $bars = $conf['barFree'];
        echo '$l[\'barFree\']=[' . PHP_EOL;
        foreach ($bars as $num => $bar) {
            $newBar = array_map($replaceSyms, $bar);
            echo $num . '=>[' . implode(',', $newBar) . '],' . PHP_EOL;
        }
        echo '];' . PHP_EOL;
    }

    public function action_mega()
    {

        $s = new Slot_Megaways('megatest');
        $s->amount = 1;
        $s->spin();
        echo $s->printBar();


        echo "\r\n Total win: {$s->win_all} \r\n";
        foreach ($s->win as $sym => $win) {
            echo "symNum: $sym   win: $win  Length:{$s->LineWinLen[$sym]}\r\n";
        }


    }


    public function action_ref()
    {

        echo '<pre>';

        $eurwin = 0;
        $eurin = 0;

        $users = [];

        $os = [];

        $owners_sql = db::query(1, 'select p.id,p.comment from persons p where comment is not null and comment !=\'\'')->execute()->as_array('id');
        $owner_offices_sql = db::query(1, 'select id,owner from offices where owner is not null')->execute()->as_array('id');

        $owners = [-1 => 'All'];
        $owner_offices = [];

        foreach ($owners_sql as $s) {
            $owners[$s['id']] = $s['comment'];
        }

        foreach ($owner_offices_sql as $s) {
            $owner_offices[$s['id']] = arr::get($owners_sql, $s['owner'], ['comment' => ''])['comment'];
        }

        $known_bets = [];
        $known_unbets = [];
        $unknown_rounds = [];
        $win_bets = [];

        $win_amount = 0;

        $byowners = [];
        $byownersusers = [];
        $byownersusersEUR = [];

        foreach (['03', '04', '05', '06'] as $day) {

            $pat = DOCROOT . 'del' . DIRECTORY_SEPARATOR . $day . '.php';
            $pat = APPPATH . 'logs' . DIRECTORY_SEPARATOR . '2024' . DIRECTORY_SEPARATOR . '04' . DIRECTORY_SEPARATOR . $day . '.php';

            $f = file($pat);


            foreach ($f as $i => $r) {
                if (strpos($r, 'Error. Round not ok. round_id:') !== false) {
                    $ratestr = $f[$i - 1];
                    $url = parse_url(explode('REQUEST_URI: ', $ratestr)[1] ?? 'notfound');

                    if ($url['path'] == 'notfound') {
                        var_dump($ratestr, $r);
                        continue;
                    }

                    parse_str($url['query'], $q);

                    $bet_id = $q['bet_id'];

                    if (empty($bet_id) && !in_array($q['round_id'], $unknown_rounds)) {
                        $unknown_rounds[] = $q['round_id'];
                        continue;
                    }

                    if (empty($bet_id)) {
                        continue;
                    }

                    if ($q['type'] == 'unbet') {
                        $known_unbets[] = $bet_id;
                    }

                    if (empty($q['win'])) {
                        var_dump($q);
                        continue;
                    }

                    $win_amount += $q['win'];

                    $win_bets[$bet_id] = $q['win'];

                    $known_bets[] = $bet_id;
                }
            }


            foreach (db::query(1, 'select * from bets_2024_04_' . $day . ' where id in :ids')
                         ->param(':ids', $known_bets)
                         ->execute()
                         ->as_array() as $b) {

                $o = office::instance($b['office_id']);

                if ($o->office()->is_test == '1') {
                    continue;
                }

                $owner = $owner_offices[$b['office_id']];

                $convert = $o->office()->currency->val;
                $currency = $o->office()->currency->code;

                if (!isset($os[$b['office_id']])) {
                    $os[$b['office_id']] = 0;
                }


                if (!isset($byowners[$owner])) {
                    $byowners[$owner] = 0;
                    $byownersusers[$owner] = [];
                    $byownersusersEUR[$owner] = [];
                }

                $ukey = $b['user_id'] . '!' . $b['office_id'] . '!' . $currency;

                if (!isset($byownersusers[$owner][$ukey])) {
                    $byownersusers[$owner][$ukey] = 0;
                    $byownersusersEUR[$owner][$ukey] = 0;
                }


                if (!isset($users[$b['user_id']])) {
                    $users[$b['user_id']] = 0;
                }

                $os[$b['office_id']] += $win_bets[$b['id']] * $convert;
                $byowners[$owner] += $win_bets[$b['id']] * $convert;
                $users[$b['user_id']] += $win_bets[$b['id']] * $convert;
                $byownersusers[$owner][$ukey] += $win_bets[$b['id']];
                $byownersusersEUR[$owner][$ukey] += $win_bets[$b['id']] * $convert;

                $eurwin += $win_bets[$b['id']] * $convert;
                $eurin += $b['amount'] * $convert;

            }

        }

        $susers = db::query(1, 'select external_id,id from users where id in :ids')->param(':ids', array_keys($users))->execute()->as_array('id');

        foreach ($byownersusers as $o => $usrs) {

            $filename = 'refundsApril' . $o . '.csv';

            foreach ($usrs as $u_key => $win_amount) {

                list($u_id, $office_id, $currency) = explode('!', $u_key);

                $eur = th::number_format($byownersusersEUR[$o][$u_id], ',');

                $row = $susers[$u_id]['external_id'] . ';' . $u_id . ';' . $office_id . ';' . $currency . ';' . th::number_format($win_amount, ',') . ';' . $eur . PHP_EOL;
                file_put_contents($filename, $row, FILE_APPEND);
            }
        }

        var_dump(count($susers), count($known_bets), $eurwin, $eurin, $byowners, count($users));
    }

    /**
     * генерирует цвета. придумать как можно доработать.
     * @return void
     */
    public function action_linescolors()
    {
        $game = 'foolsday';

        $max_lines = 256;
        $start_color = 2550000;
        $max_color = 16777215;

        $step = floor(($max_color - $start_color) / $max_lines);

        echo '<pre>';

        for ($i = $start_color; $i <= $start_color + ($max_lines * $step); $i += $step) {
            echo '["#' . dechex($i) . '","#' . dechex($i - ($step / 2)) . '"],' . PHP_EOL;
        }
    }

    /**
     * генерирует линии 4х4
     * @return void
     */
    public function action_maxlines()
    {
        $bars = 4;
        $bar_length = 4;

        $lines = [];

        $max_lines = pow($bars, $bar_length);

        echo '<pre>';

        for ($i1 = 1; $i1 <= 4; $i1++) {
            for ($i2 = 1; $i2 <= 4; $i2++) {
                for ($i3 = 1; $i3 <= 4; $i3++) {
                    for ($i4 = 1; $i4 <= 4; $i4++) {
                        $lines[$i1 . $i2 . $i3 . $i4] = [];
                    }
                }
            }
        }

        foreach ($lines as $l => &$line) {
            $l = (string)$l;
            for ($a = 0; $a < $bar_length; $a++) {
                for ($ll = 0; $ll < strlen($l); $ll++) {
                    $line[$a][$ll] = 0;
                    if ($l[$ll] - 1 == $a) {
                        $line[$a][$ll] = 1;
                    }
                }
            }
        }


        foreach (array_values($lines) as $n => $l) {
            echo ($n + 1) . ' => [' . PHP_EOL;
            for ($i = 0; $i < $bar_length; $i++) {
                echo '[' . implode(',', $l[$i]) . '],' . PHP_EOL;
            }
            echo '],' . PHP_EOL;
        }

    }

    public function action_testred()
    {

        ob_end_clean();

        auth::$user_id = 123;

        Game::session_start('agt', 'aliens');
        $g = new Game_Slot_Agt('aliens');


        for ($i = 0; $i < 200; $i++) {
            var_dump($g->isUserActivityIsOK());
            if (mt_rand(0, 3) == 3) {
                echo 'sleep';
                sleep(2);
            }
            var_dump($g->isUserActivityIsOK());
            if (mt_rand(0, 3) == 3) {
                echo 'sleep';
                sleep(2);
            }
        }
    }

    public function action_tra123()
    {

        //пол часа не играли
        $users = db::query(1, 'select id,last_game,last_bet_time from users where last_game is not null order by updated desc limit 100')->execute()->as_array();

        echo 'count: ' . count($users) . PHP_EOL;

        foreach ($users as $u) {
            auth::$user_id = $u['id'];
            $uM = auth::user(true);

            $session = game::view_session('agt', $u['last_game']);

            $key = 'freeCountTotal' . auth::$user_id . '-' . $u['last_game'];
            $key2 = 'freeCountAll' . auth::$user_id . '-' . $u['last_game'];


            $check = dbredis::instance()->get($key);
            $check2 = dbredis::instance()->get($key2);

            if ($check == false && $check2 === false) {
                continue;
            }


            if (!$session) {

                continue;

                dbredis::instance()->set($key, 0, array('ex' => Game_Session::$session_time));
                dbredis::instance()->set($key2, 0, array('ex' => Game_Session::$session_time));

                $uM->last_game = null;
                $uM->save();

                continue;
            }

            if (!($session['freeCountAll'] > 0 && $check2 > $session['freeCountAll'])) continue;

            echo auth::$user_id . PHP_EOL;

            if ($session['freeCountAll'] > 0 && $session['freeCountAll'] > $session['freeCountCurrent']) {

            }

            if ($session['freeCountAll'] > 0 && $check2 > $session['freeCountAll']) {
                var_dump('clear ' . auth::$user_id);

                $sess = new Game_Session(['user_id' => auth::$user_id, 'type' => 'agt', 'game' => $u['last_game']]);

                $sess_data = th::ObjectToArray($sess->data);
                $sess_data['freeCountAll'] = 0;

                $sess->flash($sess_data);

                dbredis::instance()->set($key, 0, array('ex' => Game_Session::$session_time));
                dbredis::instance()->set($key2, 0, array('ex' => Game_Session::$session_time));


                $uM->last_game = null;
                $uM->save();
            }

            var_dump($session['freeCountAll'], $session['freeCountCurrent'], $check, $check2);
            exit;
        }
    }

    public function action_ds()
    {
        $a = [];

        foreach (file(DOCROOT . 'temp' . DIRECTORY_SEPARATOR . '26.php') as $row) {
            if (strpos($row, 'ds_inout:') !== false) {
                preg_match('/last_bonus_calc\:\s([0-9]+).*ds_inout\:\s([-0-9.]+)\;\sds_in_out\:\s([-0-9.]+)/i', $row, $m);

                list($all, $time, $ds_inout, $ds_in_out) = $m;

                if ($ds_in_out != $ds_inout) {
                    if (!isset($a[$time])) {
                        $a[$time] = [];
                    }
                    $a[$time][] = [
                        'ds_inout' => $ds_inout,
                        'ds_in_out' => $ds_in_out,
                        'row' => $row,
                    ];
                }
            }
        }

        krsort($a);

        echo '<pre>';

        print_r($a);
    }

    public function action_roundls()
    {


        echo '<pre>';

        echo '<table><tbody>';

        foreach ((new Model_Currency())->where('source', '=', 'agt')->order_by('id')->find_all() as $a) {
            if ($a->val <= 0) {
                continue;
            }
            $v = 0.02 / $a->val;

            echo '<tr>';

            $min = round($v);

            if ($min <= 0) {
                $min = round($v * pow(10, $a->mult), -1) / pow(10, $a->mult);
            } else {
                $min = round($min, -strlen($min) + 1);
            }

            if ($min <= 0) {
                $min = round($v * pow(10, $a->mult)) / pow(10, $a->mult);
            }

            echo '<td>';
            echo $a->code;
            echo '</td>';


            echo '<td>';
            echo $v;
            echo '</td>';


            echo '<td>';
            echo $min;
            echo '</td>';


            echo '<td>';
            echo $a->mult;
            echo '</td>';

            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    public function action_stats()
    {
        Service::statisticDynamics();
        Service::betsAvg();
    }

    public function action_checkimages()
    {

        $static = kohana::$config->load('static');

        $sql = "select g.name, g.visible_name
                from games g
                    where g.show=1
                    order by g.sort";

        $result = db::query(1, $sql)
            ->execute()
            ->as_array();

        $sets = [
            '1x1' => [
                [250, 250],
                [450, 450],
                [600, 600],
            ],
            '1.5x1' => [600, 400],
            '1.4x1' => [420, 300],
            '1.33x1' => [400, 300],
            '1.566x1' => [752, 480],
            '1.666x1' => [400, 240],
            '1x1.5' => [400, 600],
            '1x1.33' => [420, 560],
        ];

        $mainfolder = DOCROOT;
//        $mainfolder='/var/www/agt/www/';

        $dest = $static['static_domain'] . '/files/promo/';

        //old=>new
        $renames = [
            'Pierre-Auguste Renoir' => 'Renoir',
            'Paul Gauguin' => 'Gauguin',
            'Peter Paul Rubens' => 'Rubens',
            'Claude Monet' => 'Monet',
            'Infinity gems' => 'Infinity Gems',
            'Grand Theft' => 'Grand theft',
            '40 Lucky Clover 6' => '40 Lucky Clover 6 reels',
            '20 Lucky Clover 6' => '20 Lucky Clover 6 reels',
        ];

        foreach ($result as $k => &$r) {

            if ($r['name'] == 'supabets' || $r['name'] == '1windoublehot') {
                continue;
            }

            foreach ($sets as $set => $sizes) {

                if (!is_array($sizes[0])) {
                    $sizes = [$sizes];
                }

                foreach ($sizes as $size) {

                    $dest_dir = $dest . 'agt' . $set . '_' . implode('-', $size);
                    $dest_path = $mainfolder . 'files' . DIRECTORY_SEPARATOR . 'promo' . DIRECTORY_SEPARATOR . 'agt' . $set . '_' . implode('-', $size) . DIRECTORY_SEPARATOR;


                    $fname = $dest_path . $r['visible_name'] . '.png';

                    if (!file_exists($fname)) {

                        $ex_path = explode(DIRECTORY_SEPARATOR, $fname);
                        list($new_name, $new_name_ext) = explode('.', $ex_path[count($ex_path) - 1]);

                        if (!isset($renames[$new_name])) {
                            echo 'rename not found for ' . $fname . PHP_EOL;
                            exit;
                        }

                        $oldname = $renames[$new_name];

                        $oldfname = str_replace($new_name, $oldname, $fname);

                        if (!file_exists($oldfname)) {
                            echo 'lobby not found for ' . $oldfname . PHP_EOL;
                            exit;
                        }

                        rename($oldfname, $fname);
                        var_dump($dest_path . $r['visible_name'] . '.png');
                        exit;
                    }


                    $r['lobby'][] = [
                        [
                            'src' => $dest_dir . '/' . $r['visible_name'] . '.png',
                            'type' => 'png',
                        ],
                        [
                            'src' => $dest_dir . 'cycle/' . $r['visible_name'] . '.webp',
                            'type' => 'webp',
                        ],
                        [
                            'src' => $dest_dir . 'anim/' . $r['visible_name'] . '.webp',
                            'type' => 'webp',
                        ],
                    ];
                }
            }
        }
    }

	public function before()
    {
        exit;
    }

    public function action_ttttta() {
		
		//phpinfo();
		
	}
	
    public function action_getrates() {

        $begin = new DateTime('2024-10-01');
        $end = new DateTime('2025-05-28');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $dateForDB = $dt->getTimestamp();
            $yesterday = $dateForDB - Date::DAY;
            service::updateRatesDay($yesterday, $dateForDB);
            sleep(mt_rand(2,3));
        }
    }

    public function action_jpres()
    {
        foreach (db::query(1, 'select * from offices where owner=1156 and enable_jp>0')->execute()->as_array() as $of) {

            $redis = dbredis::instance();
            $redis->select(1);

            $redis->set('jpa-' . $this->request->param('id'), 0);

            $r = db::query(database::UPDATE, 'update jackpots set active=:active where office_id=:o_id')
                ->param(':o_id', $of['id'])
                ->param(':active', 0)
                ->execute();
        }
    }

    public function action_events()
    {
		exit;
        foreach (db::query(1, 'select o.* from offices o left join events e on e.office_id=o.id where o.owner=1134 and e.is_auto_gen is null')->execute(null, 'Model_Office') as $o) {
            $o->createProgressiveEventForOffice();
        }
    }

    public function action_curr()
    {
        $currencies = array_keys(db::query(1, 'select code from currencies where source=\'agt\' and disable=0')->execute()->as_array('code'));

        echo '<pre>';

        foreach (file('temp' . DIRECTORY_SEPARATOR . 'curr.csv') as $c) {
            $c = explode(';', $c);

            if (!in_array($c[0], $currencies)) {
                echo $c[0] . PHP_EOL;
            }
        }
    }

    public function getFiles(string $directory, array $allFiles = []): array
    {
        $files = array_diff(scandir($directory), ['.', '..']);

        foreach ($files as $file) {
            $fullPath = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_dir($fullPath))
                $allFiles += $this->getFiles($fullPath, $allFiles);
            else
                $allFiles[] = $fullPath;
        }

        return $allFiles;
    }

    public function action_forupload()
    {
        $main_path = DOCROOT . 'games' . DIRECTORY_SEPARATOR . 'agt';
        $filter = ['images', 'audio'];

        $last_ex = mktime(0, 0, 0, date("m"), date("d") - 1);
        if (file_exists('last_export')) {
            $last_ex = (int)file_get_contents('last_export');
        }


        foreach ($this->getFiles($main_path) as $f) {
            if (in_array($f, ['.', '..'])) {
                continue;
            }

            $ext = explode('.', $f)[1] ?? false;

            if (!$ext) {
                continue;
            }

            if (!in_array($ext, ['jpg', 'png', 'webp', 'mp3'])) {
                continue;
            }

            foreach ($filter as $filt) {
                if (strpos($f, DIRECTORY_SEPARATOR . $filt . DIRECTORY_SEPARATOR) === FALSE) {
                    continue;
                }
            }

            $abs_path = str_replace(DOCROOT, '', $f);
            $dirs = explode(DIRECTORY_SEPARATOR, $abs_path);
            unset($dirs[count($dirs) - 1]);
            $dir = implode(DIRECTORY_SEPARATOR, $dirs);

            if (stat($f)['atime'] < $last_ex) {
                continue;
            }

            if (!is_dir(DOCROOT . 'for_upload' . DIRECTORY_SEPARATOR . $dir)) {
                mkdir(DOCROOT . 'for_upload' . DIRECTORY_SEPARATOR . $dir, 0777, true);
            }
            copy(DOCROOT . $abs_path, DOCROOT . 'for_upload' . DIRECTORY_SEPARATOR . $abs_path);
        }

        file_put_contents('last_export', time());
    }

    public function action_exp2()
    {

        $games = [
//                '6bluestar',
//                '6superhot5',
//                '6dreamcatcher',
//                'tesla',
//                'pharaoh2',
//                'leprechaun',
            'bookofset',
        ];


        foreach ($games as $g) {
            $path = 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . $g . DIRECTORY_SEPARATOR . 'ui' . DIRECTORY_SEPARATOR;

            mkdir(DOCROOT . 'upl2309' . DIRECTORY_SEPARATOR . $path, 0777, true);

            copy(DOCROOT . $path . 'page2.png', DOCROOT . 'upl2309' . DIRECTORY_SEPARATOR . $path . 'page2.png');
            copy(DOCROOT . $path . 'page2.webp', DOCROOT . 'upl2309' . DIRECTORY_SEPARATOR . $path . 'page2.webp');
        }
    }

    public function action_2r()
    {
        $m = new Model_User();
        var_dump($m->calc_fsback(0.9 * 515300, 'greenhot', 1089));
    }

    public function action_er()
    {

        echo '<pre>';

        var_dump(DateTime::createFromFormat('d/m/Y H:i', '15/09/2015 20:01')->getTimestamp());
        exit;

        try {

            $a = 'abc';
            try {
                $a *= 25;
            } catch (Exception $ex) {
                throw $ex;
            }
            $a = $a / 25;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function action_rt()
    {

        echo '<pre>';

        $xml = <<<XML
                <service session="site-domain" time="2021-09-06T18:05:58.362348">
                    <roundbetwin result="ok" id="321993794">
                        <balance currency="EUR" version="2" type="real" value="212"/>
                    </roundbetwin>
                </service>
XML;

        $data = simplexml_load_string($xml);

        var_dump((string)$data->roundbetwin->balance->attributes()->value);

    }

    public function action_q2()
    {

        $less = (floor((1628161561 - 1628098700) / 60 / 60 / 24));
        var_dump($less);

//        dbredis::instance()->FLUSHALL();
    }

    public function action_q()
    {

        dbredis::instance()->FLUSHALL();
    }

    public function action_p()
    {

        $j = new jpcard();
        //кривой флеш
        //$c = [24,11,1,2,33,7,3];
        //$c=[38,33,49,28,7,29,36];

        $c = [card::makecard(12, 1),
            card::makecard(12, 2),
            card::makecard(9, 3),
            card::makecard(7, 1),
            card::makecard(9, 2),
            card::makecard(4, 3),
            card::makecard(6, 3),
        ];
        $lvl = $j->level($c);

        echo card::print_card($c);
        $wincards = $j->wincards();


        echo $j->combName($lvl);
        echo "\r\n";

        echo card::print_card($wincards);
    }


    public function action_doubleline()
    {
        $c = Kohana::$config->load('agt/100_6')->as_array()['lines'];
        $use = [];
        $double = [];

        foreach ($c as $num => $line) {

            $comp = 0;
            for ($x = 0; $x < 6; $x++) {
                for ($y = 0; $y < 4; $y++) {
                    $comp = $comp << 1;
                    $comp += $line[$y][$x];
                }
            }
            if (in_array($comp, $use)) {
                $double[] = $num;
            } else {
                $use[] = $comp;
            }


        }

        echo implode(' ', $double);
    }


    public function action_t1()
    {
        $sql = "(
            select o.id,o.visible_name,b.created,vdate(b.created),vdate(b.created,o.zone_time) as ldate, b.user_id,

            case when b.type='double'
                    then b.amount
                    else (
                                    case when b.game in('tensorbetter','acesandfaces','jacksorbetter')
                                            THEN 0
                                            else (
                                                    case when b.is_freespin=0
                                                            Then  b.amount
                                                            else 0 END )
                                    end
                    ) END as in1,

            b.win as out1,b.game
            from bets_archive b
            join offices o on b.office_id=o.id

            where b.created>1598893200
            and b.created<1601596800

            and o.is_test=0
            --and namedate(b.created,o.zone_time)='2020-09-01'

            and o.id=1032

            union all

            select o.id,o.visible_name,b.created,vdate(b.created),vdate(b.created,o.zone_time), b.user_id, b.amount,0 ,b.game
            from pokerbets b
            join offices o on b.office_id=o.id

            where b.created>1598893200
            and b.created<1601596800

            and o.is_test=0
            --and namedate(b.created,o.zone_time)='2020-09-01'
            and o.id=1032
            )
            order by 1,3  ";

        $data = db::query(1, $sql)->execute()->as_array();
        $res = [];
        foreach ($data as $r) {
            if ($r['in1'] > 0) {
                $res[] = [
                    'date' => $r['ldate'],
                    'amount' => str_replace('.', ',', $r['in1']),
                    'type' => 'Bet'

                ];
            }

            if ($r['out1'] > 0) {
                $res[] = [
                    'date' => $r['ldate'],
                    'amount' => str_replace('.', ',', -$r['out1']),
                    'type' => 'Payout'
                ];
            }

        }

        foreach ($res as $rec) {
            echo implode(';', $rec) . "\r\n";
        }


    }

    public function action_newgames()
    {


        foreach (glob(APPPATH . 'config' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . '*.php') as $f) {
            $e = explode(DIRECTORY_SEPARATOR, $f);
            $name = explode('.', $e[count($e) - 1])[0];

            $g = new Model_Game(['name' => $name]);
            if ($g->loaded()) {
                continue;
            }

            $g->name = $name;

            $g->visible_name = $name;
            $g->provider = 'our';
            $g->type = 'slot';
            $g->brand = 'agt';
            $g->image = '/games/agt/thumb/csr.png';
            $g->show = 1;
            $g->tech_type = 'h';
            $g->category = 'hot';
            $g->mobile = 1;
            $id = $g->save();

            $og = new Model_Office_Game();
            $og->office_id = 777;
            $og->game_id = $id;
            $og->enable = 1;
            $og->save();
        }

    }

    public function action_lgg()
    {


exit;

        var_dump(Session::instance()->id());
        var_dump(Session::instance()->id());
        Session::instance()->regenerate();
        var_dump(Session::instance()->id());
        var_dump(Session::instance()->id());

        exit;

        $l = '';
        foreach (debug_backtrace() as $a) {
            if (isset($a['file'])) {
                $l .= $a['file'] . PHP_EOL . $a['line'] . PHP_EOL;
            }
        }
        echo $l;
        exit;

        Session::instance()->regenerate();
        var_dump(Session::instance(), Session::instance()->id());

//     Person::force_login('sa');
    }

    public function action_gggasd()
    {
        $c = (array)Kohana::$config->load('videopoker');

        $t = [];

        foreach ($c as $pname => $a) {
            foreach ($a['pay'][1] as $b) {
                $t[] = $b['name'];
            }
        }

        $t = array_unique($t);
        print_r(array_values($t));
    }

    public function action_log8()
    {
        $f = file('08.php');
        $a = [];
        $b = [];
        $sumin = 0;
        $sumout = 0;
        foreach ($f as $i => $r) {
            if (strpos($r, '[1134]') && strpos($f[$i + 1], '"action":"bet"')) {
                $a[] = [
                    $f[$i + 1],
                    $f[$i + 4],
                ];


                $r1 = json_decode($f[$i + 1], 1);
                $sumin += $r1['amount'];
                $sumout += $r1['win'];
                continue;
            }
        }
        echo '<pre>';
        echo $sumin . PHP_EOL;
        echo $sumout . PHP_EOL;
        echo $sumin - $sumout . PHP_EOL;
        print_r($a);
    }

    public function action_intersys()
    {
        $u = 'https://api.interkassa.com/v1/paysystem-output-payway?purseId=303525818142';
        $p = new Parser();
        $g = $p->get($u, array(), $auth = '5dd40ac51ae1bd12008b4568:f3HgvF5LscQ4addgir2CHJQ2NI79RrUh'); //вот так работает
        echo '<pre>';
        print_r(json_decode($g, 1));
        exit;
        $u = 'https://api.interkassa.com/v1/paysystem-input-payway?checkoutId=5dd40b9f1ae1bd0d008b4568';
        $p = new Parser();
        $g = $p->get($u, array(), $auth = '5dd40ac51ae1bd12008b4568:f3HgvF5LscQ4addgir2CHJQ2NI79RrUh'); //вот так работает
        echo '<pre>';
        print_r(json_decode($g, 1));
    }

    public function action_interout()
    {
        $u = 'https://api.interkassa.com/v1/account';
        $u = 'https://api.interkassa.com/v1/purse';

        //login - 5dd40ac51ae1bd12008b4568
        //account - 5dd40b6a1ae1bd10008b4569
        //kassa_id - 5dd40b9f1ae1bd0d008b4568
        //purse
        //uah - 303525818142
        //rub - 403691521372

//        $p=new Parser();
//        $headers = [
//                'Ik-Api-Account-Id: 5dd40b6a1ae1bd10008b4569',
//        ];
//        $g = $p->get($u,$headers, $auth='5dd40ac51ae1bd12008b4568:f3HgvF5LscQ4addgir2CHJQ2NI79RrUh'); //вот так работает
//        echo '<pre>';
//        print_r(json_decode($g));
//        exit;

        //вывод

        $u = 'https://api.interkassa.com/v1/withdraw';
        $p = new Parser();

        $data = [
            'action' => 'process',
            'calcKey' => 'psPayeeAmount',
            'amount' => 30,
//            'paywayId'=>'5ad5c30d3b1eafa45a8b4568', //mastercard_ecommpay_transfer_rub
            'purseId' => '403691521372',
            'paymentNo' => '10002',
            'method' => 'card',
            'currency' => 'rub',
            'useShortAlias' => true,
            'details' => [
                'phone' => '9253543498',
                'card' => '5331572295418732',
            ],
        ];

        $headers = [
            'Ik-Api-Account-Id: 5dd40b6a1ae1bd10008b4569',
        ];

        echo '<pre>';
//        print_r($data);

        $a = $p->post($u, $data, false, $headers, $auth = '5dd40ac51ae1bd12008b4568:f3HgvF5LscQ4addgir2CHJQ2NI79RrUh');

        print_r(json_decode($a, 1));
    }


    public function action_gasd()
    {
        $p = new Parser();
        $j = $p->get('https://www.cbr-xml-daily.ru/daily_json.js');
        $json = json_decode($j, 1);

        foreach ($json['Valute'] as $c => $s) {
            db::query(Database::UPDATE, 'update currencies set val=:v where code=:code')
                ->param(':code', $c)
                ->param(':v', $s['Value'])
                ->execute();
        }
    }

    public function action_asddd()
    {

        $file = DOCROOT . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . 'shiningstars' . DIRECTORY_SEPARATOR . 'animations' . DIRECTORY_SEPARATOR . 'anim_cell_tranform.png';
        $i = Image::factory($file);
        var_dump($i);
//        $i->background('#123123123');
//        $i->render('jpg');

//        $i->save(str_replace('.png','.jpg',$file),80);

        exit;

        $m = new Model_Apireq([
            'office_id' => 123,
            'request_id' => 20006,
        ]);

        var_dump($m->loaded());

        exit;

        echo '<pre>';


        $b = db::query(1, 'select bets.id as id from bets join offices on bets.office_id=offices.id where
offices.is_test=0 and
bets.created>=1575504000 and bets.created<1575504000+3600*24


 order by bets.created asc, bets.id asc')
            ->execute()
            ->as_array('id');

        $miss = [];
        $mbets = [];

        foreach (file(DOCROOT . 'mango_bet.csv') as $row) {


            $a = explode('","', $row);
            $a[0] = str_replace('"', '', $a[0]);

            $a[2] = (float)$a[2];
            $a[3] = (float)$a[3];

            $mbets[trim($a[5])] = trim($a[5]);

            $a[7] = date('Y-m-d H:i:s', trim(str_replace('"', '', $a[7])));
        }

        foreach ($b as $ab => $v) {
            if (!isset($mbets[$ab])) {
                $miss[] = $ab;
            }
        }
        print_r($miss);
    }

    public function action_asddd1()
    {
        echo '<pre>';

        $total = [0, 0, 0];


        foreach (file(DOCROOT . 'mango_bet.csv') as $row) {
//            $row = str_replace('"','',$row);


            $a = explode('","', $row);
            $a[0] = str_replace('"', '', $a[0]);

            $a[2] = (float)str_replace(',', '.', $a[2]);
            $a[3] = (float)str_replace(',', '.', $a[3]);


            $total[0] += $a[2];
            $total[1] += $a[3];
            $total[2]++;

            $a[7] = date('Y-m-d H:i:s', trim(str_replace('"', '', $a[7])));
            echo implode('  ', $a) . PHP_EOL;
        }
        ksort($total);
        print_r($total);
    }

    public function action_inter()
    {
        $p = new Parser();
        $d = array(
            'ik_co_id' => '5dd40b9f1ae1bd0d008b4568',
            'ik_co_prs_id' => '403691521372',
            'ik_inv_id' => '183369767',
            'ik_inv_st' => 'success',
            'ik_inv_crt' => '2019-11-25 13:00:54',
            'ik_inv_prc' => '2019-11-25 13:00:54',
            'ik_trn_id' => NULL,
            'ik_pm_no' => '_286H_19',
            'ik_pw_via' => 'test_interkassa_test_xts',
            'ik_am' => '22',
            'ik_co_rfn' => '22',
            'ik_ps_price' => '22.92',
            'ik_cur' => 'RUB',
            'ik_desc' => 'Order #9 Invoice',
            'ik_sign' => 'J4sI8YKZJDfzKo1rcm5S0w==',
        );

        echo($p->post('http://work/interkassa/process-invoice', $d));
    }

    public function action_gsad()
    {
        Auth::instance()->get_user();
        var_dump(auth::user()->id, auth::user()->office_id, count(auth::user()->office->activeJackpots()));
    }

    public function action_htr()
    {
        $c = kohana::$config->load('egt/virtualroulette');
        echo '<pre>';
        ksort($c['betnum']);
        print_r($c['betnum']);
    }

    public static $sortOrder;

    public function action_updegt()
    {
        $f = file(APPPATH . 'config' . DIRECTORY_SEPARATOR . 'egt' . DIRECTORY_SEPARATOR . 'work');

        db::query(database::UPDATE, 'update games set show=0 where brand=\'egt\'')->execute();

        $games = [];

        foreach ($f as $ii => $a) {
            if ($ii <= 14) {
                continue;
            }
            $n = explode(' ', $a);
            if (trim($n[count($n) - 1]) == '') {
                continue;
            }
            $games[] = trim($n[count($n) - 1]);
        }

        echo db::query(database::UPDATE, 'update games set show=1 where brand=\'egt\' and name in :names')
            ->param(':names', $games)
            ->compile(Database::instance());
        exit;

        db::query(database::UPDATE, 'update games set show=1 where brand=\'egt\' and name in :names')
            ->param(':names', $games)
            ->execute();
    }


    public function action_pos()
    {
        $s1 = '0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9';
        $s2 = '9,0,1';

        var_dump(strpos($s1, $s2));
    }

    function print_rr($a)
    {
        $s = '';
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $s .= '\'' . $k . '\' => [' . PHP_EOL . "\t" . $this->print_rr($v) . PHP_EOL . '],' . PHP_EOL;
            } else {
                if (!is_integer($k)) {
                    $s .= "\n\n\n\n" . '\'' . $k . '\' => \'' . $v . '\',' . PHP_EOL;
                } else {
                    if (!is_integer($v)) {
                        $s .= '\'' . $v . '\',';
                    } else {
                        $s .= $v . ',';
                    }
                }
            }
        }
        return $s;
    }

    function decode_code($code)
    {
        return preg_replace_callback(
            "@\\\(x)?([0-9a-f]{2,3})@",
            function ($m) {
                return chr($m[1] ? hexdec($m[2]) : octdec($m[2]));
            },
            $code
        );
    }

    public function action_htrad()
    {
        $comb = [2, 2, 4, 6, 8, 0, 0, 0, 4, 4, 4, 3, 6, 1, 3];
        foreach ($comb as $i => $n) {
            $bar = $i % 5;
            $y = floor($i / 5);
            var_dump($i, $y, $bar, ($i % 3), '<br>');
        }
    }

    public function action_dec()
    {
        $s = '\x66\x72\x65\x65\x2D\x73\x6C\x6F\x74\x73\x2E\x67\x61\x6D\x65\x73';
        $s = '0x238EF';

        $s = UTF8::strtolower($s);

        var_dump($this->decode_code($s));
    }


    function print_a($a, $return = true)
    {

        $s = '';
        $rz = ",\r\n";
        foreach ($a as $a1) {
            $s .= '[' . implode(',', $a1) . ']' . $rz;
        }

        if ($return) {
            return $s;
        }

        echo $s;
    }


    //parse games from fs.games
    public function action_chk()
    {

        $p = new Parser();

        $from_sitemap = true;

        $brand = 'novomatic';
        $category = 'greentube';

        $brand = 'amatic';
        $category = 'amatic';

        $b = 'https://free-slots.games';

        $all = '/' . $category;

        if ($from_sitemap) {
            $all = '/sitemap.xml';
        }

        $green = $p->get($b . $all);

        $list = [];

        if (!$from_sitemap) {
            $html = $p->html()->find('.play-icon a');
            foreach ($html as $h) {
                $list[] = $b . $h->href;
            }
        } else {
            $xml = simplexml_load_string($green);
            foreach ($xml->url as $u) {
                if (strpos($u->loc, '/' . $category . '/') === false) {
                    continue;
                }
                if (count(explode('/', $u->loc)) <= 5) {
                    continue;
                }
                $list[] = "" . $u->loc;
            }
        }

        $readygames = [];

        if (file_exists(APPPATH . $brand . DIRECTORY_SEPARATOR . 'work')) {
            $readygames = file(APPPATH . $brand . DIRECTORY_SEPARATOR . 'work');
        }

        $res = [];

        $defc = (array)Kohana::$config->load('novomatic/dolphinsd');

        foreach ($list as $a) {

            $game_p = new Parser();
            $g = $game_p->get($a);

            $index = $game_p->html()->find('#fsrc_div', 0)->plaintext;

            $server = UTF8::str_ireplace('index.php', 'server.php', $index);
            var_dump($server);
            exit;
            $post = new Parser();
            $ans = $post->post($server, [
                'slotEvent' => 'getSettings'
            ], true);

            $ans = json_decode($ans, 1);

            if (!$ans) {
                echo '==============' . $a . '==================';
                continue;
            }

            $lname = UTF8::strtolower($ans['serverResponse']['slotId']);

            if (!in_array($lname, $readygames)) {
                continue;
            }

            echo '!!!!!!!!!!!!!!!!!!!!! ' . $ans['serverResponse']['slotId'] . ' !!!!!!!!!!!!!!!' . PHP_EOL . PHP_EOL . PHP_EOL;

            $symbols = $ans['serverResponse']['SymbolGame'];

            //start

            $fc = '<?php' . PHP_EOL . PHP_EOL . PHP_EOL;

            //lines

            $fc .= '$l[\'lines\']=array(' . PHP_EOL;

            foreach ($ans['serverResponse']['gameLine'] as $l) {
                if (isset($defc['lines'][$l])) {
                    $fc .= $l . ' => [' . PHP_EOL . $this->print_a($defc['lines'][$l]) . PHP_EOL . '],' . PHP_EOL;
                } else {
                    $fc .= $l . ' => [' . PHP_EOL . $this->print_a($defc['lines'][1]) . PHP_EOL . '],' . PHP_EOL;
                }
            }

            $fc .= ');' . PHP_EOL . PHP_EOL . PHP_EOL;

            $fc .= '$psym=[];' . PHP_EOL;

            $paytable = $ans['serverResponse']['Paytable'];
            $count_symbols = 0;

            self::$sortOrder = [
                '9', '10', 'J', 'Q', 'K', 'A',
                'P_12', 'P_11', 'P_10', 'P_9', 'P_8', 'P_7', 'P_6', 'P_5', 'P_4', 'P_3', 'P_2',
                'P_1',
                'SCAT',
            ];

            uksort($paytable, function ($a, $b) {
                $cmpa = array_search($a, Controller_Test::$sortOrder);
                $cmpb = array_search($b, Controller_Test::$sortOrder);
                return ($cmpa > $cmpb) ? 1 : -1;
            });

//            if($lname=='treasurecastle') {
//
//
//                var_dump($paytable);
//                exit;
//            }

            $comp = [];

            $sym_index = 0;

            foreach ($paytable as $sym => $tbl) {
                $fc .= '$psym[' . $sym_index . '] = [' . implode(',', $tbl) . '];' . PHP_EOL;
                $comp[$sym_index] = $sym;
                $sym_index++;
            }

            //old version
            /*foreach($symbols as $sym_index => $sym) {

                if($ans['serverResponse']['slotDBId']=='150') {
                    $fc.=PHP_EOL.PHP_EOL.PHP_EOL.' !!!!НЕОБЫЧНАЯ ИГРА!!!! '.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
                    continue;
                }

                if(!isset($paytable[$sym])) {
                    //$fc.='$psym['.$sym_index.'] = ['. implode(',',array_fill(0,count($paytable['P_1']),0)).'];'.PHP_EOL;
                }
                else {
                    $fc.='$psym['.$sym_index.'] = ['.implode(',',$paytable[$sym]).'];'.PHP_EOL;
                    $count_symbols++;
                }
            }*/

            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;

            $fc .= '$compare=[];' . PHP_EOL;

            foreach ($comp as $a1 => $a2) {
                $fc .= '$compare[' . $a1 . '] = [\'' . $a2 . '\'];' . PHP_EOL;
            }

            $fc .= '$l[\'compare\']=$compare;' . PHP_EOL;

            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;


            $scat_index = array_search('SCAT', $comp);
            $wild_index = array_search('P_1', $comp); //не факт что всегда этот. нужно проверять

            if (isset($ans['serverResponse']['slotBonusType']) && $ans['serverResponse']['slotBonusType'] == '1') {
                $wild_index = $scat_index;
            }

            $fc .= '$l[\'pay\']=$psym;' . PHP_EOL;
            $fc .= '$l[\'scatter\']=[' . $scat_index . '];' . PHP_EOL;
            $fc .= '$l[\'wild\']=[' . $wild_index . '];' . PHP_EOL;
            $fc .= '$l[\'wild_multiplier\']=' . $ans['serverResponse']['slotWildMpl'] . ';' . PHP_EOL;
            $fc .= '$l[\'anypay\']=[' . $scat_index . '];' . PHP_EOL;

            if (is_array($ans['serverResponse']['slotFreeCount'])) {
                $fgarr = $ans['serverResponse']['slotFreeCount'];
            } else {
                $fgarr = [0, 0, 0, $ans['serverResponse']['slotFreeCount'], $ans['serverResponse']['slotFreeCount'], $ans['serverResponse']['slotFreeCount']];
            }

            $fc .= '$l[\'free_games\']=[' . implode(',', $fgarr) . '];' . PHP_EOL;
            $fc .= '$l[\'free_multiplier\']=' . $ans['serverResponse']['slotFreeMpl'] . ';' . PHP_EOL;
            $fc .= '$l[\'free_mode\']=\'sum\';' . PHP_EOL;
            $fc .= '$l[\'free_multiplier_mode\']=\'simple\';' . PHP_EOL;
            $fc .= '$l[\'pay_rule\']=\'left\';' . PHP_EOL;
            $fc .= '$l[\'bonus_double\']=\'spin\';' . PHP_EOL;

            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;


            $fc .= '$l[\'bars\']=[
                1=>[' . implode(',', range(0, $count_symbols - 1)) . '],
                2=>[' . implode(',', range(0, $count_symbols - 1)) . '],
                3=>[' . implode(',', range(0, $count_symbols - 1)) . '],
                4=>[' . implode(',', range(0, $count_symbols - 1)) . '],
                5=>[' . implode(',', range(0, $count_symbols - 1)) . ']
            ];';

            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;

            $fc .= '//для совместимости!! решил пихать все что они отдают' . PHP_EOL;

            $fc .= '$l[\'common\']=[' . PHP_EOL . $this->print_rr($ans['serverResponse']) . '];' . PHP_EOL;
            $fc .= '$l[\'common_lang\']=[' . PHP_EOL . $this->print_rr($ans['slotLanguage']) . '];' . PHP_EOL;

            //было так сначала
//            $fc .= '$l[\'SymbolGame\']=["'.implode('","',$symbols).'"];'.PHP_EOL;
//            $fc .= '$l[\'reelStrip1\']=["'.implode('","',$ans['serverResponse']['reelStrip1']).'"];'.PHP_EOL;
//            $fc .= '$l[\'reelStrip2\']=["'.implode('","',$ans['serverResponse']['reelStrip2']).'"];'.PHP_EOL;
//            $fc .= '$l[\'reelStrip3\']=["'.implode('","',$ans['serverResponse']['reelStrip3']).'"];'.PHP_EOL;
//            $fc .= '$l[\'reelStrip4\']=["'.implode('","',$ans['serverResponse']['reelStrip4']).'"];'.PHP_EOL;
//            $fc .= '$l[\'reelStrip5\']=["'.implode('","',$ans['serverResponse']['reelStrip5']).'"];'.PHP_EOL;
//            $fc .= '$l[\'slotId\']=\''.$ans['serverResponse']['slotId'].'\';'.PHP_EOL;
//            $fc .= '$l[\'slotDBId\']=\''.$ans['serverResponse']['slotDBId'].'\';'.PHP_EOL;

            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;
            $fc .= PHP_EOL;


            $fc .= 'return $l;' . PHP_EOL;

            //config save

            $f = APPPATH . 'config' . DIRECTORY_SEPARATOR . $brand . DIRECTORY_SEPARATOR . $lname . '.php';
            if (!file_exists($f)) {
                file_put_contents($f, $fc);
            }

            //database save

            $gm = new Model_Game(['name' => $lname, 'brand' => $brand, 'provider' => 'fsgames']);

            if (!$gm->loaded()) {
                $gm->name = $lname;
                $gm->visible_name = $ans['serverResponse']['slotId'];
                $gm->show = 1;
                $gm->provider = 'fsgames';
                $gm->brand = $brand;
                $gm->type = 'slots';
                $gm->tech_type = 'h';

                $gm->save();
            }
        }
    }

    public function action_azt()
    {

        define('OFFICE', 444);
        $n = new Slot_Egt('rollingdice');
        $n->cline = 10;
        $n->spin();
        $n->win();
        echo '<pre>';
        $syms = array_values($n->sym());
        foreach ($syms as $i => $s) {
            if (floor($i / 5) == $i / 5) {
                echo '' . PHP_EOL;
            }
            if ($s == 8) {
                echo '<b style="font-size:larger">' . $s . '</b> ';
            } else {
                echo $s . ' ';
            }
        }

        echo PHP_EOL;

        for ($z = 0; $z < count($n->replaced_symbols_in_bar); $z++) {
            echo $n->replaced_symbols_in_bar[$z] . ' : ' . $n->replaced_symbols_in_bar[$z + 1] . PHP_EOL;
            $z++;
        }
    }

    public function action_at()
    {
		//auth::force_login_model(new Model_User(12687539));
        //auth::force_login('asdasd111@loc.loc');

    }

    public function action_et()
    {
        $f = json_decode(file_get_contents(DOCROOT . 'games' . DIRECTORY_SEPARATOR . 'egt' . DIRECTORY_SEPARATOR . 'html5' . DIRECTORY_SEPARATOR . 'content.json'), 1);

        $normnames = db::query(1, 'select * from egtgames')->execute()->as_array('code');

        echo '<pre>';

        $a = [];
        $b = [];

        foreach ($f['loaders'][0]['children'] as $s) {

            if (strpos($s['url'], '/') === FALSE) {
                continue;
            }
            $g = explode('/', $s['url'])[0];

            if (!isset($a[$g])) {
                $a[$g] = [];
            }

            if (!isset($normnames[$s['vars']['name']])) {
                $b[] = $s;
                continue;
            }

            $a[$g][$s['vars']['name']] = $normnames[$s['vars']['name']]['gameName'];
        }

        ksort($a);

        print_r($a);
        echo PHP_EOL . '!!!!!!!!!!!!!!!!NOT FOUND !!!!!!!!!!!!!!!' . PHP_EOL . PHP_EOL;
        print_r($b);
    }

    public function action_ft()
    {
        $lines = [0];
        for ($i = 1; $i <= 81; $i++) {
            echo $i . '=>[' . PHP_EOL .
                '[0,0,0,0],
                    [1,1,1,1],
                    [0,0,0,0],' . PHP_EOL;
            echo '],' . PHP_EOL;
        }
    }

    public function action_makeegt()
    {
        $egt = db::query(1, 'select * from egtgames where game_id is null')->execute()->as_array();

        $conf_path = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'egt' . DIRECTORY_SEPARATOR;

        foreach ($egt as $e) {
            $name = UTF8::strtolower($e['gameName']);
            $name = UTF8::str_ireplace([' ', '&', "'", '`', '"'], '', $name);
            $file = $conf_path . $name . '.php';

            $db = database::instance();
            $db->begin();

            try {
                $g = db::query(Database::INSERT, 'INSERT INTO games(name, visible_name,provider,type,brand,show,tech_type,demo) '
                    . 'values (:name, :visible_name,:provider,:type,:brand,:show,:tech_type,:demo)')
                    ->parameters([
                        ':name' => $name,
                        ':visible_name' => $e['gameName'],
                        ':provider' => 'our',
                        ':type' => 'slots',
                        ':brand' => 'egt',
                        ':show' => '1',
                        ':tech_type' => 'h',
                        ':demo' => '0',
                    ])->execute();
                db::query(Database::INSERT, 'insert into office_games(game_id,office_id,enabled) values(' . $g[0] . ',777,1)')->execute();
                db::query(Database::UPDATE, 'update egtgames set game_id=:game_id where id=:id')->param(':game_id', $g[0])->param(':id', $e['id'])->execute();

                if (!file_exists($file)) {
                    file_put_contents($file, file_get_contents($conf_path . '100burninghot.php') . PHP_EOL . '//!!!AUTOGENERATED!!!');
                }

                $db->commit();
            } catch (Exception $ex) {
                $db->rollback();
            }
        }
    }

    public function action_abvgd()
    {
        require DOCROOT . 'login.php';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo 'TRUNCATE TABLE egtgames;<br>';
        foreach ($jayParsedAry['complex'] as $key => $a) {

            if ($key == 'Roulette') {
                continue;
            }

            $sql = strtolower('insert into egtgames (code,' . implode(',', array_keys($a[0])) . ')');
            $array_valuess = [];
            $array_valuess[] = '\'' . $key . '\'';
            foreach ($a[0] as $bk => $bv) {
                if (!is_array($bv)) {
                    if (is_bool($bv)) {
                        $array_valuess[] = (int)$bv;
                    } else {
                        $array_valuess[] = '\'' . str_replace("'", "''", $bv) . '\'';
                    }
                } else {
                    $array_valuess[] = '\'' . json_encode($bv) . '\'';
                }
            }
            $sql .= ' values (' . implode(',', $array_valuess) . ');<br>';
            echo $sql;
        }
    }

    public function action_aaaas()
    {

        auth::force_login('sadsad@test.loc');

        $api = new gameapi();

        $api->bet(auth::user()->parent_acc()->name, 'http://agtint.ru/agt.php', [
            'amount' => (float)100,
            'win' => (float)200,
            'balance_before' => 4000,
            'balance_after' => 4100,
            'game' => 'pharaoh',
            'game_id' => 737,
            'bettype' => 'free',
            'bet_id' => 32454674,
        ]);

//    $time=time();
//    $params=[
//        'action'=>'balance',
//        'time'=>$time
//    ];
//    $sign_params=[];
//    $api->bet('sadsad@test.loc','http:://agtint.ru/agt.php');

        echo hash('sha256', '123:abcdef:1402125022');
        exit;
        auth::force_login('sadsad@test.loc');
    }

    public function action_bars()
    {
        $bars = Kohana::$config->load('egt/superhot20.bars');
        echo '<pre>';
        $sets = [];
        foreach ($bars as $i => $bar) {
            for ($y = 0; $y < 4; $y++) {
                $sets[$y][$i - 1] = array_slice($bar, $y * 20, 20);
            }
        }
        foreach ($sets as $set) {
            echo '[' . PHP_EOL;
            foreach ($set as $b) {
                echo '[' . implode(',', $b) . ']' . PHP_EOL;
            }
            echo '],' . PHP_EOL;
        }
    }

    public function action_assa1()
    {

        $a = [1 => [
            -2 => 1,
            -1 => 1,
            0 => 1,
            1 => 1,
            2 => 1,
        ],
            2 => [
                -2 => 1,
                -1 => 1,
                0 => 1,
                1 => 1,
                2 => 1,
            ],
            3 => [
                -2 => 1,
                -1 => 1,
                0 => 1,
                1 => 1,
                2 => 1,
            ],
            4 => [
                -2 => 1,
                -1 => 1,
                0 => 1,
                1 => 1,
                2 => 1,
            ],
            5 => [
                -2 => 2,
                -1 => 2,
                0 => 2,
                1 => 2,
                2 => 2,
            ],
        ];


        foreach ($a as &$l) {
            ksort($l, SORT_DESC);
        }

        echo '<pre>';
        print_r($a);

        foreach ($a as $num => $l) {
            unset($l[-1], $l[-2]);
            echo "$num=>" . implode(',', $l) . "<br>";
        }
    }

    public function action_assa()
    {
        echo urldecode('https://api.vipgameapi.com/gameapi/getgame/621?login=test@2611&%E2%80%8Boffice_id=10002&%E2%80%8Bdemo=0&sign=1d91949b9e3f216851afe1de25121f51f221008903910b115182d4457f3c6c06');
        exit;
    }

    public function action_as()
    {

        $g = new Game_Session(['user_id' => 544299, 'type' => 'novomatic', 'game' => 'luckyladycharm']);
        $g->flash();
    }

    public function action_ab()
    {

        $a = [
            'Royal Lotus',
            'Tidal Riches',
            'Dragon Warrior',
            'Arctic Race',
            'Asian Fortunes',
            'Wild Country',
            'Tales of Darkness: Break of Dawn',
            'Tales of Darkness: Lunar Eclipse',
            'Tales of Darkness: Full Moon',
            'Jaguar Moon',
            'Stein Haus',
            'Buffalo Magic',
            'Legends of the Seas',
            'Eye of the Dragon',
            'Totally Wild',
            'Irish Coins',
            'Little Dragons',
            'Almighty Reels – Realm of Poseidon',
            'Book of Ra Magic',
            'From Dusk Till Dawn',
            'Asian Diamonds',
            'Book of Maya',
            'Crazy Birds',
            'King’s Treasure',
            'Cash Farm',
            'Lucky Rose',
            'Rumpel Wildspins',
            'Kingdom of Legends',
            'Book of Stars',
            'Grand Jester',
            'Gryphon’s Gold Deluxe',
            'Cops ‘n’ Robbers Vegas Vacation',
            'Dancing Dragon',
            'Jester’s Crown',
            'Apollo God of the Sun',
            'Ramses II',
            'Dragon’s Deep',
            'Wizard’s Ring',
            'Spectrum',
            'Rex',
            'Mystic Secrets',
            'Faust',
            'Hoffmania',
            'Top O’ The Money',
            'Freibier',
            'American Gangster',
            'Temple of Secrets',
            'Jungle Explorer',
            'Blazing Riches',
            'River Queen',
            'Wild Thing',
            'Spring Queen',
            'Royal Dynasty',
            'Helena',
            'Amazon’s Diamonds',
            '& Roll Online',
            'Snake Rattle & Roll',
            'Dynasty of Ra',
            'Dragon’s Pearl',
            'Reel Attraction',
            'Autumn Queen',
            'Book of Ra Deluxe 6',
            'Kingdom of Legend',
            'Volcanic Cash',
            'The Wild Wood',
            'Shooting Stars',
            'Rainbow Reels',
            'Queen of Hearts Deluxe',
            'Orca',
            'Mayan Moons',
            'Mermaid’s Pearl',
            'Jewels of the Sea',
            'King of the Pride',
            'Gorilla',
            'Frogs Fairy Tale',
            'Flamenco Roses',
            'Elven Princess',
            'Clockwork Oranges',
            'Dragon’s Wild Fire',
            'Cops ‘n’ Robbers',
            'Cleopatra Queen of Slots',
            'Cleopatra Last of the Pharaohs',
            'Bear Tracks',
            'Aztec Power',
            'African Simba',
            'Reel King Potty',
            'Red Lady',
            'Pharaoh’s Tomb',
            'Pharaoh’s Ring',
            'Mighty Trident',
            'Lava Loot',
            'Indian Spirit',
            'Haul of Hades',
            'Golden Cobras Deluxe',
            'Golden Ark',
            'Flame Dancer',
            'Fairy Queen',
            'Columbus Deluxe',
            'Big Catch',
            'Beetle Mania Deluxe',
            'Dolphin’s Pearl Deluxe',
            'Katana',
            'Rainbow King',
            'Reel King',
            'Secret Elixir',
            'Sharky',
            'Lord of the Ocean',
            'Book of Ra Deluxe',
        ];
        foreach ($a as $b) {
            $short_name = strtolower(str_replace([' ', '’', '&', ':', '–', '‘'], '', $b));
            $g = new Model_Game(['name' => $short_name]);
            if (!$g->loaded()) {
                echo 'not found: ' . $short_name . '<br>';
                continue;
            }
            $g = new Model_Game(['name' => $short_name, 'show' => 1]);
            if (!$g->loaded()) {
                echo 'not show: ' . $short_name . '<br>';
                continue;
            }
            echo 'exist: ' . $short_name . '; provider: ' . $g->provider . '<br>';
        }
    }

    public function action_cr3()
    {
        for ($i = 1; $i <= 99; $i++) {
            $pass = mt_rand(1000, 9999);
            $u = new Model_User();
            $u->name = 'agent' . $i;
            $salt = mt_rand(100000, 999999);
            $u->password = auth::pass($pass, $salt);
            $u->salt = $salt;
            $u->amount = 1000;
            $u->office_id = 444;

            $u->save()->reload();

            echo $u->name . '<br>' . $pass . '<br><br><br>';
        }
    }

    public function action_cr2()
    {
        $p = new Model_Person();
        $p->role = 'gameman';
        $p->name = 'gameman166';
        $salt = mt_rand(100000, 999999);
        $p->password = auth::pass('qw2kj3asd', $salt);
        $p->salt = $salt;
        $p->enable_telegram = 0;
        $p->save();
    }

    public function action_cr()
    {
        for ($i = 1; $i <= 99; $i++) {
            $u = new Model_User();
            $u->name = 'user' . $i;
            $salt = mt_rand(100000, 999999);
            $u->password = auth::pass('123', $salt);
            $u->salt = $salt;
            $u->amount = 5000;
            $u->office_id = 444;

            $u->save()->reload();
        }
    }

    public function action_free()
    {

        exit;

        $config = Kohana::$config->load('secret.freeobmen');

        $params = [
            'amount' => '2300.00',
            'currencyCode' => 'RUB',
            'paymentSystemCode' => 'Card',
            'number' => '4890494677236945',
        ];

        $signString = implode('', $params);
        $signString .= $config['secret'];
        $signString = md5($signString);


        $params['sign'] = $signString;

        $params['key'] = $config['public'];

        $url = 'https://www.freeobmen.com/api/withdrawal?' . http_build_query($params);

        $this->request->redirect($url);

        exit;

        /////////////////////////////////////
        /* $params = [
          'paymentId' => '15513447368876',
          ];

          $signString = implode('', $params);
          $signString.=$config['secret'];
          $signString =  md5($signString);


          $params['sign']=$signString;

          $params['key']=$config['public'];

          $url = 'https://www.freeobmen.com/api/withdrawal-status?'.http_build_query($params);
         */
        /* $this->request->redirect($url);
          exit; */

        $f = new freeobmen();
        th::vd($f->payways());
//    $url = $f->in('500',131,'Card','RUB');

        if (!count($f->getErrors())) {
            $this->request->redirect($url);
        }
        var_dump($f->getErrors());
    }

    public function action_getbalance()
    {
        $u = new Model_User(['name' => $this->request->post('login')]);
        echo json_encode(['error' => '0', 'balance' => $u->amount()]);
    }

    public function action_apigl()
    {
        $o = new Model_Office(109);

        $sign = $o->sign([
            'office_id' => '109',
        ]);

        $url = 'http://vipgameapi.com/gameapi/list?office_id=109&sign=' . $sign;

        $content = file_get_contents($url);
        $result = json_decode($content, 1);

        if ($result['error'] == '1') {
            echo '[' . $result['error_code'] . ']' . $result['error_message'];
            exit;
        }
        th::vd($result['data']);
    }

    public function action_apig()
    {


//    $api = new Api_Imperiumgames();
//    $gameList = $api->gameList();
//    th::vd($gameList);

        $o = new Model_Office(10001);

        $params = [
            'login' => 'user',
            'office_id' => '10001',
            'demo' => '0',
        ];

        $sign = $o->sign($params);

        $game_id = 506;

//    $url='http://192.168.0.105/gameapi/getgame/'.$game_id.'?'. http_build_query($params).'&sign='.$sign;
        $url = 'https://api.vipgameapi.com/gameapi/getgame/' . $game_id . '?' . http_build_query($params) . '&sign=' . $sign;
//    echo $url.'<br>';
//    echo $url;
//    exit;
        $p = new Parser();
        $content = $p->get($url);

//    var_dump($content);
//    $content = file_get_contents($url);
        $result = json_decode($content, 1);

        if ($result['error'] == '1') {
            echo '[' . $result['error_code'] . ']' . $result['error_message'];
            exit;
        }

        echo '<style>
    html,body,iframe {margin:0;padding:0;border:0;}
</style>';
        echo($result['data']);
    }

    public function action_bon()
    {

//    auth::force_login('frln*adaf969f7f15d0f4227421c1d832fb00');
        th::ub(600232, 154050.18);
        exit;

        $share = new Model_Share(9);
        $share->calc_tournament_winners();
    }

    public function action_pass()
    {
        /*
         * соль из таблицы
         */
        $salt = '1014100';
        /*
         * пасс который хочешь
         */
        $pass = 'ewq432';

        /*
         * вставить этот пасс в persons
         */
        echo md5(md5($pass) . $salt);
    }

    public function action_ggggg()
    {
        $b = new Model_Share(18);
        $b->notification();
    }

    public function action_t()
    {
        ob_end_clean();

        $calc = new Slot_Novomatic('test');
        $calcs = new Slot_Novomatic('test');


        $count[1] = count($calc->bars[1]);
        $count[2] = count($calc->bars[2]);
        $count[3] = count($calc->bars[3]);
        $count[4] = count($calc->bars[4]);
        $count[5] = count($calc->bars[5]);
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1]++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2]++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3]++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4]++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5]++) {


                            $calc->pos[1] = $pos[1];
                            $calc->pos[2] = $pos[2];
                            $calc->pos[3] = $pos[3];
                            $calc->pos[4] = $pos[4];
                            $calc->pos[5] = $pos[5];

                            $calcs->pos[1] = $pos[1];
                            $calcs->pos[2] = $pos[2];
                            $calcs->pos[3] = $pos[3];
                            $calcs->pos[4] = $pos[4];
                            $calcs->pos[5] = $pos[5];


                            /*
                              $calc->pos[1]=26;
                              $calc->pos[2]=7;
                              $calc->pos[3]=38;
                              $calc->pos[4]=13;
                              $calc->pos[5]=23;
                             */


                            $calc->correct_pos();
                            $calcs->correct_pos();
                            /*
                              echo $calc->sym(1).' '.$calc->sym(2).' '.$calc->sym(3).' '.$calc->sym(4).' '.$calc->sym(5)."<br>";
                              echo $calc->sym(6).' '.$calc->sym(7).' '.$calc->sym(8).' '.$calc->sym(9).' '.$calc->sym(10)."<br>";
                              echo $calc->sym(11).' '.$calc->sym(12).' '.$calc->sym(13).' '.$calc->sym(14).' '.$calc->sym(15)."<br>";
                             */

                            $calc->cline = 1;
                            $calc->amount_line = 1;
                            $calc->amount = 1;

                            $calcs->cline = 1;
                            $calcs->amount_line = 1;
                            $calcs->amount = 1;

                            $calc->win();
                            $calcs->win();

                            if ($calc->win_all != $calcs->win_all) {

                                echo json_encode($calc->pos);
                                echo ';';
                                echo json_encode($calc->win);
                                echo ';';
                                echo json_encode($calc->win_all);
                                echo ';';
                                echo json_encode($calcs->win);
                                echo ';';
                                echo json_encode($calcs->win_all);
                                echo "\r\n";
                            }
                        }
                    }
                }
            }
        }
    }

    public function action_r()
    {


        Auth::$user_id = 22;

        game::session('igrosoft', 'crazymonkey');
        $calc = new Slot_Igrosoft('crazymonkey');


        $calc->cline = 9;
        $calc->amount_line = 1;
        $calc->amount = 9;


        $calc->bet();

        echo $calc->sym(1) . ' ' . $calc->sym(2) . ' ' . $calc->sym(3) . ' ' . $calc->sym(4) . ' ' . $calc->sym(5) . "\r\n";
        echo $calc->sym(6) . ' ' . $calc->sym(7) . ' ' . $calc->sym(8) . ' ' . $calc->sym(9) . ' ' . $calc->sym(10) . "\r\n";
        echo $calc->sym(11) . ' ' . $calc->sym(12) . ' ' . $calc->sym(13) . ' ' . $calc->sym(14) . ' ' . $calc->sym(15) . "\r\n";

        echo $calc->LineSymbol[1] . ' ' . $calc->LineWinLen[1] . ' ' . $calc->LineUseWild[1] . "\r\n";


        echo json_encode($calc->pos);
        echo "\r\n";
        echo json_encode($calc->win);
        echo "\r\n";
        echo json_encode($calc->win_all);
        echo "\r\nbonus:";
        print_r($calc->bonusdata);
        echo "\r\n";
        echo $calc->bonusPay;
    }

    public function action_igrogen()
    {
        ob_end_clean();

        //game::session('novomatic','bananas');
        $calc = new Slot_Igrosoft('test');
        $calc->gen();
    }

    public function action_igro()
    {
        ob_end_clean();

        Auth::$user_id = 22;

        game::session('igrosoft', 'test');
        $calc = new Slot_Igrosoft('test');
        $calc->cline = 9;
        $calc->amount_line = 10;
        $calc->amount = 90;


        for ($i = 1; $i <= 10000; $i++) {
            $r = $calc->bet();
            if ($r > 0) {
                echo "error $r\r\n";
                exit;
            }
            if ($i % 500 == 0) {
                echo "$i\r\n";
            }
        }
    }

    public function action_novo()
    {
        ob_end_clean();

        Auth::$user_id = 22;

        game::session('novomatic', 'bananas');
        $calc = new Slot_Novomatic('bananas');
        $calc->cline = 9;
        $calc->amount_line = 10;
        $calc->amount = 90;


        for ($i = 1; $i <= 10000; $i++) {
            $r = $calc->bet();
            if ($r > 0) {
                echo "error $r\r\n";
                exit;
            }
            if ($i % 500 == 0) {
                echo "$i\r\n";
            }
        }
    }

    public function action_rtest()
    {


        $a = [0.3030303030303, 0.23232323232323, 0.14141414141414, 0.12121212121212, 0.080808080808081, 0.04040404040404, 0.02020202020202, 0.02020202020202, 0.04040404040404];
        $b = array_fill(0, 9, 0);
        for ($i = 1; $i <= 10000; $i++) {
            $c = math::getRandWeight($a, 3);
            $b[$c[0]]++;
            $b[$c[1]]++;
            $b[$c[2]]++;
        }

        print_r($b);
    }

    public function action_iphone()
    {
        $s = new Model_Share(1);
        $s->notification();
//        $u = new Model_User(19);
//        $view = new View('login/mailpass');
//        $view->user = $u;
//        $view->pas = 123;
//        $message = Email::render('login/mailpass',[
//                'user' => $u,
//                'pas'=>123
//        ]);
//
//        $message = Email::render('email/activity_remind',[
//                'bonus' => 100,
//                'days' => 5,
//                'u' => $u,
//        ]);
//
//        echo $message;
    }

    public function action_profile()
    {
        $user = new Model_User(19);
        echo debug::vars($user->profile->id);
    }

    public function recurseCopy($sourceDirectory, $destinationDirectory, $childFolder = '')
    {
        $directory = opendir($sourceDirectory);

        if (is_dir($destinationDirectory) === false) {
            mkdir($destinationDirectory);
        }

        if ($childFolder !== '') {
            if (is_dir("$destinationDirectory/$childFolder") === false) {
                mkdir("$destinationDirectory/$childFolder");
            }

            while (($file = readdir($directory)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                if (is_dir("$sourceDirectory/$file") === true) {
                    recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
                } else {
                    copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
                }
            }

            closedir($directory);

            return;
        }

        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (is_dir("$sourceDirectory/$file") === true) {
                $this->recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
            } else {
                copy("$sourceDirectory/$file", "$destinationDirectory/$file");
            }
        }

        closedir($directory);
    }

    public function action_newgame()
    {

        $games = db::query(1, 'select * from games where show=1 order by visible_name')->execute()->as_array('name');

        if ($this->request->method() == 'POST') {

            $g = $games[$_POST['game']];

            unset($g['id']);
            $g['name'] = $_POST['name'];
            $g['visible_name'] = $_POST['visible_name'];

            $sql = 'insert into games(' . implode(',', array_keys($g)) . ') values :vals on conflict(name) do update set visible_name=:v_name returning id';

            $r = db::query(1, $sql)
                ->param(':vals', array_values($g))
                ->param(':v_name', $_POST['visible_name'])
                ->execute()
                ->as_array('id');

            $id = key($r);

            db::query(1, 'insert into office_games(office_id,game_id,enable) 
                        values(777,:id,1) on conflict(office_id,game_id) do update set enable=1')
                ->param(':id', $id)
                ->execute();


            $staticImagesDIR = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'staticagt' .
                DIRECTORY_SEPARATOR . 'www';

            $gameImagesPath = $staticImagesDIR . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'images' .
                DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . $_POST['game'];

            $destImagesPath = $staticImagesDIR . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'images' .
                DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . $_POST['name'];

            if (is_dir($gameImagesPath) && !is_dir($destImagesPath)) {
                $this->recurseCopy($gameImagesPath, $destImagesPath);
            }

            $configPath = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . $_POST['game'] . '.php';
            $destConfigPath = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . $_POST['name'] . '.php';

            if (file_exists($configPath)) {
                copy($configPath, $destConfigPath);
            }

            $jsPath = DOCROOT . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR .
                'configs' . DIRECTORY_SEPARATOR . $_POST['game'] . '.js';
            $destJsPath = DOCROOT . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR .
                'configs' . DIRECTORY_SEPARATOR . $_POST['name'] . '.js';

            if (file_exists($jsPath)) {
                copy($jsPath, $destJsPath);
            }


            $this->request->redirect('/games/agt/' . $_POST['name']);
        }


        $form = Form::open(null, ['method' => 'POST']);

        $selectList = array_combine(arr::pluck($games, 'name'), arr::pluck($games, 'visible_name'));

        $form .= form::label('game', 'Choose game to clone');
        $form .= form::select('game', $selectList, null, ['required' => 'required']);

        $form .= '<br>';

        $form .= form::label('visible_name', 'new game visible name');
        $form .= form::input('visible_name', null, ['required' => 'required']);

        $form .= '<br>';

        $form .= form::label('name', 'new game short name');
        $form .= form::input('name', null, ['required' => 'required']);

        $form .= '<br>';

        $form .= form::button('go', 'go', ['type' => 'submit']);
        $form .= form::close();


        echo $form;
        exit;
    }

    public function action_translate()
    {

        $def = [
            'Please place your bet' => 'আপনার বাজি রাখুন',
            'Good luck' => 'শুভকামনা',
            'Line' => 'লাইন',
            'Scatter' => 'ছড়িয়ে দিন',
            'Please gamble or take win' => 'জুয়া বা জয় নিতে দয়া করে',
            'Select cards to hold' => 'রাখা কার্ড নির্বাচন করুন',
            'Please select symbol' => 'প্রতীক নির্বাচন করুন',
            'free games left' => 'বিনামূল্যে গেম বাকি',
            'not enough credits' => 'পর্যাপ্ত ক্রেডিট নেই',
            'Play sounds?' => 'শব্দ বাজানো?',
            'Yes' => 'হ্যাঁ',
            'No' => 'না',
            'BALANCE' => 'ভারসাম্য',
            'LAST WIN' => 'শেষ জয়',
            'Pay table' => 'বেতন টেবিল',
            'bet' => 'বাজি',
            'All pays are left to right on adjacent reels, on selected lines, beginning with the leftmost reel, except scatters. Scatter wins are added to the payline wins. Highest payline add/or scatter wins only paid. Line wins are multiplied by the bet value on the winning line. Scatter wins are multiplied by the total bet value. Malfunction voids all pays and plays.' => 'সমস্ত অর্থ পাশের রিলগুলিতে বাম থেকে ডানে, নির্বাচিত লাইনে, স্ক্যাটার ব্যতীত বাম রিল দিয়ে শুরু হয়৷ স্ক্যাটার জয় পেলাইন জয় যোগ করা হয়. সর্বোচ্চ পেলাইন যোগ / বা স্ক্যাটার শুধুমাত্র প্রদত্ত জয়. লাইন জয় বিজয়ী লাইনে বাজি মান দ্বারা গুণিত হয়. স্ক্যাটার জয় মোট বাজি মান দ্বারা গুণিত হয়. ত্রুটি শূন্য সব বহন করেনা এবং নাটক.',
            'All pays are left to right on adjacent reels, on selected lines, beginning with the leftmost reel. Highest payline wins only paid. Line wins are multiplied by the bet value on the winning line. Malfunction voids all pays and plays.' => 'সমস্ত পেমেন্টগুলি বাম থেকে ডানে সংলগ্ন রিলগুলিতে, নির্বাচিত পেলাইনগুলিতে, সবচেয়ে বাম রিল থেকে শুরু করে৷ লাইনে সবচেয়ে বড় অর্থ প্রদান করা হয়. লাইন জয় বিজয়ী লাইনে বাজি মান দ্বারা গুণিত হয়. একটি ত্রুটি সমস্ত অর্থ প্রদান এবং গেম বাতিল করে৷',
            'Current WIN' => 'বর্তমান জয়',
            'Gamble X2 to WIN' => 'জুয়া এক্স 2 জিততে',
            'Gamble X4 to WIN' => 'জুয়া এক্স 4 জিততে',
            'X2 to WIN' => 'উইন এক্স 2',
            'X4 to WIN' => 'জেতার জন্য এক্স 4',
            'Statistics' => 'পরিসংখ্যান',

            'Make your first bet to start receive Daily Spins cashback!' => 'দৈনিক স্পিন ক্যাশব্যাক পেতে শুরু করার জন্য আপনার প্রথম বাজি তৈরি করুন!',
            'You made bets for %d credits' => 'আপনি %d ক্রেডিট জন্য কয়টা বেট তৈরি',
            'No.' => '#',
            'Change' => 'পরিবর্তন',
            'Value' => 'মান',
            'Time left' => 'সময় বাকি',
            'Status' => 'অবস্থা',
            'Type' => 'প্রকার',
            'In' => 'In',
            'Out' => 'Out',
            'Coef.' => 'সহগ',
            'Balance' => 'ভারসাম্য',
            'Lines' => 'লাইন',
            'Expiration' => 'মেয়াদ শেষ',

            'Information is updated every 10 minutes' => 'তথ্য প্রতি 10 মিনিট আপডেট করা হয়',
            'Your cashback rate is' => 'আপনার ক্যাশব্যাক রেট হল',
            'Daily Spins forecast' => 'দৈনিক ঘূর্ণন পূর্বাভাস',
            'Total free spins' => 'মোট ফ্রি স্পিন',

            'Play every day and get Daily Spins (DS)' => 'প্রতিদিন খেলুন এবং দৈনিক স্পিন পান (ডিএস)',
            'Your current Daily Spins cashback coefficient is' => 'আপনার বর্তমান দৈনিক স্পিন ক্যাশব্যাক সহগ হল',
            'Play and get +0.5% for every day up to 10%' => 'খেলুন এবং প্রতিদিন 0.5% পর্যন্ত 10% পান%',
            'To recieve DS cashback you should go to any game' => 'ডিএস ক্যাশব্যাক পেতে আপনার যে কোনও গেমে যাওয়া উচিত',
            'Last bet time' => 'শেষ বাজি সময়',
            'Last update' => 'সর্বশেষ আপডেট',
            'Next update' => 'পরবর্তী আপডেট',
            'AWARDED / DECLINED DAILY SPINS HISTORY' => 'পুরস্কৃত / প্রত্যাখ্যাত দৈনিক স্পিন ইতিহাস',
            'Daily Spins (DS) are calculated as follows: the sum of all wagered funds in AGT software games, except for poker and keno games, from the moment of the previous accrual (up to a maximum of 10 days), minus the sum of all winnings for the same period multiplied by the DS coefficient. The DS coefficient for new players is 4.5%. The DS coefficient increases by 0.5% every day if the player bets on games, except for poker and keno games, on that day. If the player does not bet on any games, except for poker and keno games, the DS coefficient decreases by 1%. The DS coefficient cannot be less than 4.5% or more than 10%.
    
    DS is credited at the moment of entering the game if 8 hours have passed since the last bet. The day is considered in the UTC time zone. After accepting DS, it must be played within 20 minutes to be eligible for rewards.' => 'ডেইলি স্পিনগুলি (DS) নিম্নরূপ গণনা করা হয়: AGT সফ্টওয়্যার গেমগুলিতে সমস্ত বাজিযুক্ত তহবিলের যোগফল, পোকার এবং কেনো গেমগুলি ছাড়া, পূর্ববর্তী জমার মুহূর্ত থেকে (সর্বোচ্চ 10 দিন পর্যন্ত), সমস্ত যোগফল বিয়োগ একই সময়ের জন্য জয়গুলি DS সহগ দ্বারা গুণিত। নতুন খেলোয়াড়দের জন্য ডিএস সহগ হল 4.5%। DS সহগ প্রতিদিন 0.5% বৃদ্ধি পায় যদি খেলোয়াড় সেই দিন পোকার এবং কেনো গেম ব্যতীত গেমগুলিতে বাজি ধরে। প্লেয়ার যদি পোকার এবং কেনো গেম ব্যতীত কোনো গেমে বাজি না দেয়, তাহলে DS সহগ 1% কমে যায়। ডিএস সহগ 4.5% এর কম বা 10% এর বেশি হতে পারে না। খেলায় প্রবেশের মুহূর্তে DS ক্রেডিট করা হয় যদি শেষ বাজি থেকে 8 ঘন্টা কেটে যায়। দিনটিকে UTC সময় অঞ্চলে বিবেচনা করা হয়। DS গ্রহণ করার পর, পুরস্কারের জন্য যোগ্য হতে 20 মিনিটের মধ্যে এটি খেলতে হবে।',
            'How to play' => 'কিভাবে খেলতে হয়',
            'Animation' => 'অ্যানিমেশন',
            'Music' => 'সঙ্গীত',
            'Off' => 'বন্ধ',
            'Sounds' => 'শব্দ',
            'Close game' => 'খেলা বন্ধ করুন',
            'All bets' => 'সব বাজি',
            'My bets' => 'আমার বাজি',
            'User' => 'ব্যবহারকারী',
            'Bet' => 'বাজি',
            'Cash out' => 'নগদ আউট',
            'Auto cash out' => 'অটো ক্যাশ আউট',
            'You won' => 'আপনি জিতেছেন',
            'Top wins' => 'শীর্ষ জয়',
            'Top rates' => 'শীর্ষ হার',
            'Day' => 'দিন',
            'Month' => 'মাস',
            'Year' => 'বছর',
            'Date' => 'তারিখ',
            'Win' => 'জয়',
            'Rate' => 'হার',
            'Send' => 'পাঠান',
            'Message text' => 'বার্তার পাঠ্য',
            'Cancel bet' => 'বাজি বাতিল করুন',
            'Wait' => 'অপেক্ষা করুন',
            'Start auto bet' => 'অটো বাজি শুরু করুন',
            'Cancel bet and stop auto bet' => 'বাজি বাতিল করুন এবং অটো বাজি বন্ধ করুন',
            'Stop auto bet' => 'অটো বাজি বন্ধ করুন',
            'Bet now' => 'এখনই বাজি ধরুন',
            'Wait for next round' => 'পরবর্তী রাউন্ডের জন্য অপেক্ষা করুন',
            'Switch off auto cash first' => 'অটো নগদ প্রথম বন্ধ করুন',
            'Cancel bet or take win' => 'বাজি বাতিল করুন বা জয় নিন',
        ];

        if ($this->request->method() == 'POST') {

            $input = $this->request->post('orig');
            $parsed = preg_split("/((\r?\n)|(\r\n?))/", $input);

            $def_keys = array_keys($def);

            $text = '';

            foreach ($parsed as $i => $line) {
                $line = mb_substr($line, 0, -1);
                $text .= "'$def_keys[$i]' => '$line'," . PHP_EOL;
            }

            echo '<pre>';

            echo $text;

        }

        $form = Form::open(null, ['method' => 'POST']);
        $form .= Form::textarea('orig');
        $form .= Form::submit('go', 'go');
        $form .= Form::close();

        echo $form;

    }

}

