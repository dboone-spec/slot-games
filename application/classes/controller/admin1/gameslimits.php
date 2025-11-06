<?php

class Controller_Admin1_Gameslimits extends Controller_Admin1_Base
{

    protected $_showBets=false;

    public function action_index()
    {

        if(Person::$role=='sa') {
            $this->_showBets=true;
        }

        $office_id = (int)arr::get($_GET, 'office_id');
        $data = [];
        $settings = [];

        $extra_games_params=[];

        if(Person::$user_id==1156) {
            $extra_games_params=['tvbet_show'=>1];
        }

        if ($office_id>0) {
            $o = new Model_Office($office_id);
            $currency=$o->currency;
            $data=$this->getOfficeLimits($o,$extra_games_params);

            $settings=[
                'bet_max'=>$o->bet_max>0? $o->bet_max : 1000/$currency->val
            ];
        }

        $officesList = [-1 => 'Choose office'] + Person::user()->officesName(null, true);

        $view = new View('admin1/games/limits');
        $view->office = $o ?? new stdClass();
        $view->officesList = $officesList;
        $view->data = $data;
        $view->showBets = $this->_showBets;
        $view->settings = $settings;

        $this->template->content = $view;
    }

    private function getOfficeLimits(Model_Office $o,$extra_games_params=[]) {
        $games = db::query(1, 'select * from games where show=1 and brand=\'agt\' and branded=0 order by 3')->execute(null,'Model_Game')->as_array();

        //  можно удалить
        if(  person::$role =='sa' || person::$user_id == 1023 || person::$user_id == 1156 ) {

            $sql='select * from games where show=1 and brand=\'agt\'';

            if(!empty($extra_games_params)) {
                foreach($extra_games_params as $k=>$v) {
                    $sql.=' and '.$k.'='.$v;
                }
            }

            $sql.=' order by 3';

            $games = db::query(1, $sql)->execute(null,'Model_Game')->as_array();
        }

        $data=[];

        $currency=$o->currency;

        foreach ($games as $g) {
			
			
            $config_name='agt/' . $g->name;

            if($g->type=='keno') {
                $config_name='keno/' . $g->name;
            }

            if($g->type=='roshambo') {
                $config_name='roshambo/' . $g->name;
            }

            if($g->type=='videopoker') {
                $config_name='videopoker/' . $g->name;
            }

            $c = Kohana::$config->load($config_name);

            $maxrate=0;

            if (th::isMoonGame($g->name)) {
                $bet_max = $currency->moon_max_bet ?? 100;

                $maxWin=$currency->moon_max_win;
                $maxrate=$maxWin/$bet_max;
            }

            $needcalc = true;

            if (isset($c['staticlines']) && !empty($c['staticlines'])) {
                $needcalc = false;
                $c['lines_choose'] = $c['staticlines'];
            }

            if (in_array($g->type, ['keno','roshambo','moon'])) {
                $needcalc = false;
            }

            if (in_array($g->type, ['videopoker', 'keno'])) {
                $c['lines_choose'] = [1];
            }


            if (in_array($g->type, ['roshambo'])) {
                $s=array_map(function($e) {return count($e); },$c['pay']);
                $c['lines_choose'] = array_reverse($s);
            }

            if(!isset($c['lines_choose'])) { continue; }

            $min_lines = $c['lines_choose'][count($c['lines_choose']) - 1];
            $max_lines = $c['lines_choose'][0];

            list($minrate,$maxrate)=$g->getMinMaxRate($c,$maxrate,$min_lines);

            $defmaxrate=$maxrate;

            if($g->needReduceMaxRate()) {

                $maxrate=1.2*$maxrate/$max_lines;

                if($maxrate>$defmaxrate) {
                    $maxrate=$defmaxrate;
                }
            }

            $allBets=[];
            foreach($c['lines_choose'] as $set) {
                $allBets[$set]=$g->getAllBets($o,$currency,$set);
            }

            $allBetsValues=array_values($allBets);

            $allMinBets=$allBetsValues[0];
            $allMaxBets=$allBetsValues[count($allBetsValues)-1];

			if(empty($allMinBets) || empty($allMaxBets)) {
                kohana::$log->add(Log::INFO,PHP_EOL.$g->visible_name.PHP_EOL.print_r($o->as_array(),1).PHP_EOL.print_r($allBets,1));
            }
			
            $minbets = min(array_merge($allMinBets,$allMaxBets));
            $minMAXbets = max($allMinBets);
            $maxbets = max(array_merge($allMinBets,$allMaxBets));

            $maxWin=$minMAXbets * $maxrate;

            $defBet=$maxbets;

            if($defBet<1 && $currency->mult>2) {
                $defBet=rtrim(number_format($defBet,$currency->mult),'0');
            }

            $oneItem=[
                $g->visible_name,
                $defBet,
                (!in_array($g->type, ['videopoker', 'shuffle', 'keno','moon'])) ? $max_lines : '-',
                $needcalc ? (bcdiv(number_format($minbets,$currency->mult), $min_lines, $currency->mult)) : '',
                $needcalc ? ($minMAXbets / $min_lines): '',
                $defmaxrate,
                number_format($minbets,$currency->mult),
                th::number_format($maxbets,'.',$currency->mult),
                th::number_format($maxWin,'.',$currency->mult),
            ];

            if($this->_showBets) {
                $oneItem['full_bets']=array_map(function($a) {
                    return implode(',',$a);
                },$allBets);
            }

            $data[] = $oneItem;

        }
        return $data;
    }

    public function action_1win() {
        $_1win_offices=db::query(1,'select * from offices where external_name=:partner')
            ->param(':partner','1win.prod')
            ->execute(null,'Model_Office')
            ->as_array();

        $writer = new XLSXWriter();

        $empty_data=true;

        foreach($_1win_offices as $o) {
            $data=[['Game','Default bet','Game Lines','Max Multiplier','Min Bet Per Line','Min Total','Max Bet Per Line','Max Total','Max Exposure']];
            $data=array_merge($data,$this->getOfficeLimits($o,['infin_show'=>1]));

            if(!empty($data)) {
                $empty_data=false;
                $writer->writeSheet($data, $o->currency->code.' ('.$o->id.')');
            }
        }
        if(!$empty_data) {
            $this->auto_render=false;
            $this->response->headers('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->response->headers('Content-Disposition', 'attachment;filename="gameslimits'.date('d-m-Y').'.xlsx"');
            $this->response->headers('Cache-Control', 'max-age=0');
            $this->response->body($writer->writeToString());
        }
        else {
            echo 'empty_data';
            exit;
        }
    }

}
