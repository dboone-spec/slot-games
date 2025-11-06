<?php

class Controller_Admin1_Replays extends Controller_Admin1_Base
{


    public $config;
    public $barName,$game;

    public function action_index()
    {

        $startId = $_GET['startId'] ?? false;
        $limit = $_GET['limit'] ?? false;
        $userId = $_GET['userId'] ?? false;

        $view = new View('admin1/replays/index');

        if ($startId && $limit && $userId) {

            $sql = 'select *
        from bets
        where id>=:startId
        	and user_id=:uid
        	order by id
        	limit :limit';

            $bets = db::query(1, $sql)->param(':startId', $startId)
                ->param(':limit', $limit)
                ->param(':uid', $userId)
                ->execute(null, Model_Bet::class)
                ->as_array();

            $this->setBarName($bets[0]);
            $poss = [];
            foreach ($bets as $bet) {
                $barName=$bet->type=='free' ? 'barFree' :$this->barName;
                $come = $this->parseStr($bet->result);
                $pos = [];
                for ($i = 1; $i <= $this->barCount; $i++) {
                    $pos[$i] = $this->findPos($come[$i], $i);
                }
                $poss[] = $pos;

            }


            $view->poss = $poss;
            $view->game = $this->game;
            $view->vidget = new Vidget_SlotResult('result', $bets[0]);
            $view->bets = $bets;
            $view->guid = guid::create();
            $view->startId=$startId;
            $view->userId=$userId;
            $view->limit=$limit;
        }
        else {
            $view->poss = [];
            $view->game = 'Select game';
            $view->vidget = new stdClass();
            $view->bets = [];
            $view->guid = guid::create();
            $view->startId='';
            $view->userId='';
            $view->limit='';
        }

        $this->template->content=$view;

    }


    public $heigth, $barCount;

    public function setBarName($bet)
    {

        $o = new Model_Office($bet->office_id);
        $rtp = $o->games_rtp;
        if ($rtp == '96.5') {
            $rtp = '965';
        } elseif ($rtp == '97.5') {
            $rtp = '975';
        } else {
            $rtp = (int)$rtp;
        }


        $this->config = Kohana::$config->load('agt/' . $bet->game);
        $this->heigth = $this->config['heigth'] ?? 3;

        if (isset($this->config["bars$rtp"])) {
            $this->barName = "bars$rtp";
        } else {
            $this->barName = "bars";
        }
        $this->barCount = count($this->config[$this->barName]);
        $this->game=$bet->game;

    }

    public function parseStr($come)
    {

        if (!is_array($come)) {
            $come = json_decode($come, true);
        }
        $result = array_fill(1, $this->barCount, []);
        for ($y = 1; $y <= $this->heigth; $y++) {
            for ($x = 1; $x <= $this->barCount; $x++) {
                $result[$x][] = $come[($y - 1) * $this->barCount + $x];
            }
        }
        return $result;
    }


    public function findPos($line, $barNum, $barName = null)
    {
        if (empty($barName)) {
            $barName = $this->barName;
        }

        if (is_array($line)) {
            $line = implode(',', $line);
        }


        $bar = $this->config[$barName][$barNum];
        $length = count($bar);

        for ($i = 0; $i < $this->barCount; $i++) {
            $bar[] = $bar[$i];
        }

        $bar = implode(',', $bar);

        $pos = strpos($bar, $line);
        $bar = substr($bar, 0, $pos);
        $bar = explode(',', $bar);
        $pos = count($bar) - 1;
        $pos = $pos > $length ? $pos - $length : $pos;

        return $pos;


    }

}
