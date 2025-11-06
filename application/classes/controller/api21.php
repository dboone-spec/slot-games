<?php

//test infin env
class Controller_Api21 extends Controller {

    protected $_api_url='https://dev-staging.infingame.com/ag';
    protected $_test_office=true;

    protected function _check_auth($login,$password) {
        if($login!='infingame' || $password!='yoPCs9t90qU8rzd') {
            return false;
        }
        //todo need check or create auth
        return true;
    }


    public function  action_launch(){

		logfile::create(date('Y-m-d H:i:s')." request launch: ".print_r($_GET,1).PHP_EOL,'infin');

        $api=new Api_Infin();
        $api->setURL($this->_api_url);

        $api->key=arr::get($_GET,'key');
        $api->gameName=arr::get($_GET,'gameName');
        $api->partner=arr::get($_GET,'partner');
        $api->platform=arr::get($_GET,'platform');
        //A unique game session identifier, generated on theGSside, is first specified in the requestenterand then does not change throughout the entire game session.
        $api->guid=guid::create();

        $lang=arr::get($_GET,'lang','en');

        $forceMobile=($api->platform=='mob');
        $noClose=!$forceMobile;

        $exit_url = arr::get($_GET,'exit_url',false);
        if(!$exit_url) {
            if($forceMobile) {
                $exit_url = $this->request->referrer();
            }
            else {
                $exit_url = 'https:'.URL::site('/black','https');
            }
        }

        if(strpos($api->key,'TEST')===0) {
            $demoUrl='https://demo.kolinz.xyz/games/agt/'.$api->gameName.'?demobalance='.UTF8::str_ireplace('TEST','',$api->key);

            if($forceMobile) {
                $demoUrl.='&force_mobile=1';
            }
            if($noClose) {
                $demoUrl.='&no_close=1';
            }

            if($exit_url!==urldecode($exit_url)) {
                $exit_url=urlencode($exit_url);
            }

            $demoUrl.='&closeurl='.$exit_url;
            $demoUrl.='&lang='.$lang;

            $this->request->redirect($demoUrl);
        }

        $data=$api->enter();

        $data['currency'] = UTF8::strtoupper($data['currency']);

		if(in_array($data['currency'],['SS1','GLD','YOH','TOK'])) {
			
			//if(th::isBlockedByIP()) {

                $ip=$_SERVER['REMOTE_ADDR'];
                $country='??';
                $domain='??';

                if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                    $ip=$_SERVER['HTTP_CF_CONNECTING_IP'];
                }

                if(isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
                    $country = strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
                }

                if(isset($_SERVER['HTTP_HOST'])) {
                    $domain=$_SERVER['HTTP_HOST'];
                }

                Kohana::$log->add(Log::INFO,'INFIN SOC ENTRY!'.PHP_EOL.'ip: '.$ip.'; country: '.$country.'; domain: '.$domain);
            //}
			
			if(!INFINSOC_DOMAIN) {
                exit;
            }
			
            $lang='socen';
        }
		
		if(INFINSOC_DOMAIN && !in_array($data['currency'],['SS1','GLD','YOH','TOK'])) {
            exit;
        }

        $office_id = $api->checkOffice($data['currency'],$api->partner,(int) $this->_test_office);

        if(!$office_id) {
            $office_id = $api->createOffice($data['currency'],$api->partner,(int) $this->_test_office);
        }

        if(!$api->checkGame($office_id)) {
            throw new Exception("Game not found");
        }

        $user_id = $api->checkUser($data['userId'],$data['balance'],$office_id);

        if(!$user_id) {
            $user_id = $api->createUser($data['userId'],$office_id,$data['balance'],$api->guid);
        }
        else {
            $wasWrongBets = $api->processWrongBets($user_id);

            if($wasWrongBets==='notpass') {
                th::ceoAlert('infin user '.((int) $user_id).'; lock '.$api->gameName);
                exit('Sorry, game is not available now. Try to refresh the game or try to start the game later');
            }
            else if($wasWrongBets) {
                $user_id = $api->checkUser($data['userId'],$api->getBalance(),$office_id);
            }
        }

        if(true || office::instance($office_id)->office()->is_test) {
            $api->setCustomSessionId($user_id,$api->gameName,$api->guid);
        }


        $cashier_url = arr::get($_GET,'cashier_url',false);
        $redirectURL=$api->getGame($user_id,$lang,$forceMobile,$noClose,$exit_url,$cashier_url);

        logfile::create(date('Y-m-d H:i:s')." response launch: ".$redirectURL.PHP_EOL,'infin');

        $this->request->redirect($redirectURL);
    }

    public function  action_fscontrol() {
        $login = arr::get($_GET,'login');
        $password = arr::get($_GET,'password');
        $format = arr::get($_GET,'format','xml');
        $command = arr::get($_GET,'cm');

        $request_id=guid::create();

        logfile::create(date('Y-m-d H:i:s')." fscontrol request [$request_id] params: ".print_r($_GET,1),'infin');

        if(!$this->_check_auth($login,$password)) {
            throw new Exception('wrong auth');
        }

        switch($command) {
            case 'create_offer';

                $bet_per_line=(int) arr::get($_GET,'bet-line');
                $game=arr::get($_GET,'game');
                $offer=arr::get($_GET,'offer');
                $spins=(int) arr::get($_GET,'spins');
                $start_live=arr::get($_GET,'start_live');
                $end_date_add_offer=arr::get($_GET,'end_date_add_offer');
                $end_live=arr::get($_GET,'end_live');

                $error=0;

                $g = new Model_Game(['name'=>$game]);
                if(!$g->loaded() || $g->show!=1 || $g->type!='slot'){
                    $error=1;
                }
                else {

                    $game_config = kohana::$config->load('agt/'.$g->name);

                    $lines = $game_config['lines_choose'][0];

                    $offerModel = new Model_InfinOffer(['guid'=>$offer]);

                    if(!$offerModel->loaded()) {

                        $offerModel->betamount=$lines*$bet_per_line/100;
                        $offerModel->lines=$lines;
                        $offerModel->game_name=$g->name;
                        $offerModel->game_id=$g->id;
                        $offerModel->spins=$spins;
                        $offerModel->guid=$offer;
                        $offerModel->active=1;

                        //время начала действия предложения
                        if($start_live) {
                            $offerModel->start_time=DateTime::createFromFormat('d/m/Y H:i',$start_live)->getTimestamp();
                        }

                        //время, до которого можно давать игрокам
                        if($end_date_add_offer) {
                            $offerModel->active_time=DateTime::createFromFormat('d/m/Y H:i',$end_date_add_offer)->getTimestamp();
                        }

                        //время окончания действия предложения (нужно удалить)
                        if($end_live) {
                            $offerModel->end_time=DateTime::createFromFormat('d/m/Y H:i',$end_live)->getTimestamp();
                            if($offerModel->end_time<=time()) {
                                //delete сразу
                                $offerModel->active=0;
                            }
                        }


                        $offerModel->save();
                    }
                    else {
                        $error=2;
                    }
                }

                logfile::create(date('Y-m-d H:i:s')." fscontrol response [$request_id] error: ".$error,'infin');

                $this->response->headers(['Content-Type: application/xml']);
                if(!$error) {
                    $this->response->body('<result status="ok" />');
                }
                else {
                    $this->response->body('<result status="error" />');
                }

                break;
            case 'add_offer';

                $wlid     = arr::get($_GET,'wlid');
                $offer    = arr::get($_GET,'offer');
                $wlcode   = arr::get($_GET,'wlcode'); //???
                $lifetime = arr::get($_GET,'lifetime');

                $error      = 0;
                $last_fs_id = false;

                $offerModel = new Model_InfinOffer(['guid' => $offer]);

                if(!$offerModel->loaded() || !$offerModel->active)
                {
                    $error = 2;
                }
                else
                {
                    $u = new Model_User(['external_id' => $wlid,'api' => 4]);

                    if(!$u->loaded())
                    {
                        $api = new Api_Infin();
                        $u   = $api->createAnonym($wlid);
                    }

                    if($u->blocked)
                    {
                        $error = 1;
                    }
                    else
                    {

//                        $calced = $u->calc_fsback($offerModel->betamount*$offerModel->spins,$offerModel->game_name,$offerModel->game_id, true,true);

                        $calced = [
                                'game_id' => $offerModel->game_id,
                                'cnt' => $offerModel->spins,
                                'zzz' => $offerModel->betamount,
                                'near' => $offerModel->lines . '-0',
                        ];

                        if($calced)
                        {

                            $ex = explode('-',$calced['near']);

                            $lines        = (int) $ex[0];
                            $dentab_index = (float) $ex[1];

                            $expire = time() + 30 * 24 * 60 * 60;

                            if($lifetime)
                            {
                                $expire = DateTime::createFromFormat('d/m/Y H:i',$lifetime)->getTimestamp();
                            }


                            $fs                = new Model_Freespin();
                            $fs->fs_offer_id   = $offerModel->id;
                            $fs->fs_offer_type = 'infingift';
                            $last_fs_id        = $fs->giveFreespins($u->id,$u->office_id,$calced['game_id'],$calced['cnt'],$calced['zzz'],$lines,$dentab_index,'api',false,null,$expire);

                            if(!$last_fs_id)
                            {
                                $error = 4;
                            }
                        }
                        else
                        {
                            $error = 3;
                        }
                    }
                }

                logfile::create(date('Y-m-d H:i:s') . " fscontrol response [$request_id] error: " . $error,'infin');

                $this->response->headers(['Content-Type: application/xml']);
                if(!$error && $last_fs_id)
                {
                    $this->response->body('<result status="ok"><gift_offer_user gift_id="' . $last_fs_id . '" /></result>');
                }
                else
                {
                    $this->response->body('<result status="error" code="' . $error . '" />');
                }

                break;
            default:
                throw new Exception('wrong command');
        }
    }

    public function  action_testlaunch(){
        $xml=simplexml_load_string(file_get_contents('php://input'));
        $b = (float) file_get_contents('bbb');

        file_put_contents('infinlog',file_get_contents('php://input'),FILE_APPEND);

//        $b=bcmul($b,100,0);

        $attrs=$xml->roundbetwin->attributes();

         if($xml->enter) {

             $enter=$xml->enter->attributes();

             $wlid=crc32($enter->key);

            $ans = <<<XML
<service session="site-domain" time="2021-09-09T12:14:14.082479">
<enter result="ok" id="163053028560">
<balance currency="EUR" version="0" type="real" value="$b"/>
<user mode="normal" type="real" wlid="$wlid"/>
</enter>
</service>

XML;
         }

         if($xml->{"re-enter"}) {
            $ans = <<<XML
<service session="site-domain" time="2021-09-09T12:14:14.082479">
<re-enter result="ok" id="163053028560">
<balance currency="EUR" version="0" type="real" value="$b"/>
<user mode="normal" type="real" wlid="2154107"/>
</re-enter>
</service>

XML;
         }

         if($xml->roundbetwin){

             $amount=$attrs->bet;
             $win=$attrs->win;

             if($xml->roundbetwin->giftfin) {
                 $win=$xml->roundbetwin->giftfin->attributes()->giftwin;
             }

            if($b<$amount-$win) {
                 //error
                 $ans = <<<XML
    <service session="site-domain" time="2021-06-15T12:52:45.258033">
        <roundbetwin id="123456" result="fail">
            <error code="NOT_ENOUGH_MONEY"><msg></msg></error>
        </roundbetwin>
    </service>
XML;
            }
            else {


                //emulate WL_ERROR
                if(0) {
                    $win=0;
                    $ans = <<<XML
    <service session="site-domain" time="2021-06-15T12:52:45.258033">
        <roundbetwin id="123456" result="error">
            <error code="WL_ERROR"><msg></msg></error>
        </roundbetwin>
    </service>
XML;
                    $b = $b-$amount+$win;

                }
                else {

                    $b = $b-$amount+$win;

                 $ans = <<<XML
    <service session="site-domain" time="2021-09-09T12:15:22.596953">
    <roundbetwin result="ok" id="324757147">
    <balance currency="EUR" version="5" type="real" value="$b" t1="$amount" t2="$win"/>
    </roundbetwin>
    </service>
XML;

                }

                if(mt_rand(0,4)==4) {
                    sleep(4);
                    echo 'Need roundbetwin command repeat';
                    exit;
                }


                file_put_contents('bbb',$b);

            }
         }

         if($xml->refund) {
            $refund=$xml->refund->attributes()->cash;
            $b = $b+$refund;
            file_put_contents('bbb',$b);
            $ans='ok';
         }

         if($xml->getbalance) {
             $ans = <<<XML
<service session="site-domain" time="2019-03-04T04:38:21.504774">
<getbalance id="1000000" result="ok">
<balance value="$b" version="1" type="real" currency="EUR"/>
</getbalance>
</service>
XML;
         }

         $this->response->headers(['Content-Type: application/xml']);
         $this->response->body($ans);
     }

    public function  action_test(){

        $api=new Api_Infin;
        echo '<pre>';

        $data=$api->parse('<service session="site-domain" time="2021-09-02T20:29:24.683240"><enter result="ok" id="163053028417"><balance currency="EUR" version="1" type="real" value="111"/><user mode="normal" type="real" wlid="2146499"/></enter></service>');

        $r=['balance'=> (string) $data->enter->balance->attributes()->value,
            'currency'=> (string) $data->enter->balance->attributes()->currency,
            'userId'=> (string) $data->enter->user->attributes()->wlid, // unique number differs for different currencies
        ];

        print_r($r);
        $this->response->body();
    }

    public function action_frametest() {
        $token=arr::get($_GET,'key');

        $games = db::query(1,'select name,visible_name from games where infin_show=1 and brand=\'agt\'')
            ->execute()
            ->as_array('name');

        $possible_params=[
            'gameName'=>array_combine(array_keys($games),Arr::pluck($games,'visible_name')),
            'lang'=>array_combine(array_keys(Kohana::$config->load('languages.lang')),array_keys(Kohana::$config->load('languages.lang'))),
            'platform'=>['pc'=>'pc','mob'=>'mob'],
        ];

        $form = Form::open(null,['method'=>'get']);
        $form.= Form::input('key',$token);
        foreach($possible_params as $param_key=>$possible_values) {
            $form.= Form::label($param_key,$param_key).' ';
            $form.= Form::select($param_key,$possible_values,Arr::get($_GET,$param_key));
        }
        $form.= Form::hidden('partner','frameOwner');
        $form.= Form::submit('go','go');
        $form.= Form::close();

        if(!empty($token)) {
            echo '<h2>User token: '.$token.'</h2>';
        }

        echo $form;

        $a=[
            'gameName'=>'stalker',
            'lang'=>'en',
            'partner'=>'frameOwner',
            'platform'=>'pc',
            'key'=>'4228032_6_CETbMsalzku1m1ewb8vWGA',
            'exit_url'=>'https://google.com',
        ];

        if(!empty($_GET)) {
            $a=$_GET;
        }
//        echo '<pre>';
//        echo http_build_query($a);
//        echo '</pre>';


        if(!empty($token)) {
            echo '<iframe width="100%" height="100%" name="emwindow" src="/api21local/launch?' . http_build_query($a) . '"></iframe>';
        }
        exit;
    }


    public function action_list(){


        $static = kohana::$config->load('static');
        $domain=$static['static_domain_infin'];


        $office=new Model_Office(1073);

        $sort=$office->sort();

        $games=$office->sorted_games;

        $data=[];
		
		$i=1;

        foreach ($games as $game){



            $images=[0=>
                        [
                            [   'src'=>$game['image'],
                                'type'=>'png',],
                            [   'src'=>str_replace('.png','.webp',$game['image']),
                                'type'=>'webp', ],
                        ],
                    1=>
                        [
                            [   'src'=>str_replace('/thumb/','/sqthumb/',$game['image']),
                                'type'=>'png',],
                            [   'src'=>str_replace(['/thumb/','.png'],['/sqthumb/','.webp'],$game['image']),
                                'type'=>'webp', ],
                        ]
                    ];



            $data[]=[
                'name'=>$game['name'],
                'visible_name'=>$game['visible_name'],
                'category'=>$game['category'],
                'type'=>$game['type'],
                'sort'=>$game['sort'],
                'images'=>$images
            ];
			$i++;
        }

        if (strtolower(arr::get($_GET,'type','xml'))=='json'){
            $this->response->body(json_encode($data));
            return null;
        }



        $xml='<list>';

        foreach ($data as $row){
            $str="<game id=\"{$row['name']}\" name=\"{$row['visible_name']}\" category=\"{$row['category']}\" type=\"{$row['type']}\" sort=\"{$row['sort']}\" >";
            $str.="<images>";
            foreach ($row['images'] as $number=>$imgs){
                $str.="<set number=\"{$number}\"> ";
                    foreach ($imgs as $img){
                        $str.="<image type=\"{$img['type']}\">{$img['src']}</image>";
                    }
                $str.="</set>";
            }


            $str.="</images></game>";
            $xml.=$str;
        }

        $xml.='</list>';
        $this->response->body($xml);






    }

}
