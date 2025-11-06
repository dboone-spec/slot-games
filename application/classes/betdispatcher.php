<?php

class betDispatcher
{

    public $channels = [];
    public $multi;
    public $time_started;

    public $good_bets = [];
    public $bad_bets = [];

    public function __construct()
    {
        $this->time_started=time();

        $this->multi = curl_multi_init();
    }

    public function createOneCurl($url,$data) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_ENCODING,0);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36');
        curl_setopt($ch,CURLOPT_COOKIEFILE,DOCROOT . "cookie.txt");
        curl_setopt($ch,CURLOPT_COOKIEJAR,DOCROOT . "cookie.txt");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FAILONERROR,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
        curl_setopt($ch,CURLOPT_TIMEOUT,7);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));

        return $ch;
    }

    protected $_times= [
        Date::MINUTE,
        Date::MINUTE*2,
        Date::MINUTE*5,
        Date::MINUTE*10,
        Date::MINUTE*40,
        Date::DAY,
    ];

    //todo сделать также и для wrongbets
    public function prepareChannels($bets)
    {
        $time=time();

        $games=[];

        foreach($bets as $bet_one)
        {

            if(!isset($games[$bet_one['game']])) {
                $games[$bet_one['game']]=new Model_Game(['name'=>$bet_one['game']]);
            }

            if((int) $bet_one['try'] >= count($this->_times)) {
                //todo. may be remove?
//                continue;
            }

            if($time<((int) $bet_one['created']+$this->_times[(int) $bet_one['try']])) {
//                continue;
            }


            $u = new Model_User($bet_one['user_id']);

            $a = explode('-',$u->name);

            array_pop($a);

            $bets_one_api = [
                    'amount' => 0,
                    'fs_amount' => 0,
                    'win' => 0,
					'finished' => 1,
					'round_num' => $bet_one['initial_id'],
                    'game' => $games[$bet_one['game']]->name,
                    'game_id' => $games[$bet_one['game']]->id,
                    'game_type' => $games[$bet_one['game']]->type,
                    'bet_type' => 'normal',
                    'bet_id' => 0,
                    'initial_bet_id' => $bet_one['initial_id'],
                    'come' => 0,
                    'result' => $bet_one['rate'],
                    'is_freespin' => false,
                    'base_amount' => 0,
                    'created' => $bet_one['created'],
                    'is_cashback' => false,
                    'slot_win_lines' => [],
                    'login' => implode('-',$a),
                    'office_id' => $bet_one['office_id'],
                    'user_id' => $bet_one['user_id'],
                    'time' => $time,
                    'action' => 'bet',
					'session_id' => $bet_one['session_id']??null,
					'game_session_id' => $bet_one['game_session_id']??null,
            ];

            $o=Office::instance($bet_one['office_id'])->office();

            $bets_one_api['sign'] = $o->sign([
                    'time' => $time,
                    'office_id' => $o->id,
            ]);

            $ch = $this->createOneCurl($o->gameapiurl,$bets_one_api);

            curl_multi_add_handle($this->multi,$ch);

            $this->channels[$o->id . '-' . $bet_one['id']] = $ch;

            logfile::create(date('Y-m-d H:i:s') . " REQUEST CRON[$this->time_started]: " . "\n" . $o->id . "\n" . 'DATA: ' . "\n" . json_encode($bets_one_api) . "\n",'moonloosebets');
        }
    }

    public function processResponse($response,$key) {

        $res=false;

        if(!$response) {
            //emulate wrongbet
            $this->bad_bets[]=explode('-',$key)[1];
            return;
        }

        $jr = json_decode($response,1);


        if(!$jr) {
            //todo log
        }
        else if(!isset($jr['error']) || $jr['error']!='0') {
            //todo log
        }
        else {
            $res=true;
        }

        if($res) {
            $this->good_bets[]=explode('-',$key)[1];
        }
        else {
            $this->bad_bets[]=explode('-',$key)[1];
        }
    }

    public function processChannels() {
                //running the requests
        $running = null;
        do {
          curl_multi_exec($this->multi, $running);
        } while ($running);

        //getting the responses
        foreach(array_keys($this->channels) as $key){
            $error = curl_error($this->channels[$key]);
            $last_effective_URL = curl_getinfo($this->channels[$key], CURLINFO_EFFECTIVE_URL);
            $time = curl_getinfo($this->channels[$key], CURLINFO_TOTAL_TIME);
            $response = curl_multi_getcontent($this->channels[$key]);  // get results
            if (!empty($error)) {
              echo "The request $key return a error: $error" . "\n";
              logfile::create(date('Y-m-d H:i:s')." REQUEST CRON FAILED[$this->time_started]: ". "\n" . $error . "\n",'moonloosebets');
            }
            else {

                logfile::create(date('Y-m-d H:i:s')." REQUEST CRON[$this->time_started]: ". "\n" . $last_effective_URL . "\n".'RESPONSE: '. "\n".$response. "\n",'moonloosebets');

                $this->processResponse($response,$key);

                echo "The request to '$last_effective_URL' returned '$response' in $time seconds." . "\n";
            }

            curl_multi_remove_handle($this->multi, $this->channels[$key]);
        }

        curl_multi_close($this->multi);
    }

}
