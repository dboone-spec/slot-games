<?php


class Controller_Bot extends Controller{


    protected $_host='https://test22tg.site-domain.com';
    protected $_bot_name='agt';

    /*protected $_host='https://7708-94-25-186-251.ngrok.io';
    protected $_bot_name='ngrok';*/

    protected $ver=1;

    protected function _getUrlToken($user) {
        if(!$user->loaded()) {
            return 'error';
        }

        if($user->office->apienable==1) {
            $api=gameapi::instance(0);
            $url = $api->getAuthTGLink($user->tg_name,$user->office_id);

            if(!$url) {
                return 'Request for auth url failed';
            }
            return $url;
        }


        $token=guid::create();
        $user->api_key=$token;
        $user->api_key_time=time()+(5*Date::MINUTE);
        $user->save();
        return $this->_host.'/bot/auth/'.$token;
    }

    public function action_games(){

        auth::instance()->get_user();

        if(!auth::$user_id) {
            throw new HTTP_Exception_403();
        }

        $user=auth::user();

        $template=new View('layout/terminal');

        $view=new View('site/bot/index');
        $bot=new Bot($this->_bot_name,$user->tg_id);


        $view->user=$user;
        $view->games=$bot->enableGames;

        $template->content=$view;

        $this->response->body($template->render());
    }

    public function action_deposit(){
        auth::instance()->get_user();

        if(!auth::$user_id) {
            echo 'need auth';
            return;
        }

        $user=auth::user();
        $user->amount=1000;
        $user->save();

        if($this->request->param('id')=='tg') {
            $this->request->redirect('https://t.me/site-domainbot');
        }

        $this->request->redirect('/bot/games');
    }

    public function action_auth(){

        auth::instance()->get_user();

        if(!auth::$user_id) {
            $u=new Model_User(['api_key'=>$this->request->param('id')]);
            if(!$u->loaded() || time()>$u->api_key_time) {
                echo 'Your session expired. Request new link from telegram bot';
                exit;
            }
            auth::force_login_model($u);
        }

        $view=new View('site/bot/step');
        $this->response->body($view->render());

//        $this->request->redirect('/bot/games');
    }

    public function action_index(){


        $data=file_get_contents('php://input');
        $data= json_decode($data,true);
        logfile::create(date('Y-m-d H:i:s') . print_r($data,true),'bot');


        $chatId=$data['message']['from']['id'];
        $cmd=$data['message']['text'] ?? false;

        $bot= new Bot($this->_bot_name,$chatId);


        if (!$cmd){
            return null;
        }



        $bot->reply_message_id=$data['message']['message_id'];

        $office=new Model_Office($bot->office_id);
        if($office->blocked) {
            $bot->sendText('Games not available [100]');
            return;
        }


        dbredis::instance()->select(0);

        $lastMessageId=dbredis::instance()->get('lastTgMessage'.$chatId);

        if($lastMessageId && $lastMessageId>=$bot->reply_message_id) {
            exit;
        }

        dbredis::instance()->set('lastTgMessage'.$chatId,$bot->reply_message_id);

        if ($cmd=='/start'){
            $user=$bot->user();

            if($office->apienable>0) {

                if($office->apitype!=0) {
                    $bot->sendText('Games not available [101]');
                    return;
                }

                $api = gameapi::instance(0);
                $checkTGUser = $api->checkTGUser($data['message']['from']['username'], $office->id, $office->gameapiurl);

                if(!$checkTGUser) {
                    $bot->sendText('Games not available [104]');
                    return;
                }

                if (!$user->loaded()){
                    $user->external_id=arr::get($checkTGUser,'login');
                    $user->office_id=$bot->office_id;
                    $user->tg_id=(string) $chatId;
                    $user->tg_name=$data['message']['from']['username'];
                    $user->amount=arr::get($checkTGUser,'balance');
                    $user->api = 1;
                    $user->visible_name = $user->external_id;
                    $user->name = $user->external_id.'-'.$user->office_id;
                    $user->lang = arr::get($checkTGUser,'lang',$data['message']['from']['language_code']);
                    $user->save();
                }
            }
            else {
                if (!$user->loaded()){
                    $user->amount=0;
                    $user->api = 0;
                    $user->office_id=$bot->office_id;
                    $user->tg_id=(string) $chatId;
                    $user->tg_name=$data['message']['from']['username']??$data['message']['from']['first_name'];
                    $user->save();
                }
            }

            $bot->keyboard('main')->sendText('Hello!');
            return null;
        }

        //Ignore all message older than 5 minutes except start
        if ($data['message']['date']<time()-60*5){
            return null;
        }

        if ($cmd=='Go to website'){
            $user=$bot->user();
            $bot->sendText($this->_getUrlToken($user));
            return null;
        }

        if ($cmd=='Information'){
            $bot->sendText($bot->getInfoText());
            return null;
        }

        //We cann't send messages if user does not exist
        if (!$bot->user()->loaded()){
            $bot->sendText('You are unregistered, please sign up at https://site-domain.com');
            return null;
        }

        if ($cmd=='Info'){

            $gameId=$bot->user()->tg_last_game;
            $img=new Bot_Image();
            if ($img->info($gameId)){
                $bot->sendPhoto($this->_host."/".$img->info($gameId));
                $bot->sendPhoto($this->_host."/".$img->info($gameId,2));
                if($gameId=='cherryhot') {
                    $bot->sendPhoto($this->_host."/".$img->info($gameId,3));
                }
            }
            else {
                $bot->sendText('No info for this game');
            }

            return null;
        }

        if ($cmd=='Games'){

            $bot->keyboard('gameSelect')->sendText('Select a game');
            return null;
        }

        if ($cmd=='Main menu'){
            $bot->keyboard('main')->sendText('Main menu');
            return null;
        }

        if ($cmd=='Bet'){
            $bot->keyboard('bet')->sendText('Select amount of bet');
            return null;
        }

        if ($cmd=='Collect'){
            $bot->keyboard('game')->sendText('Collected');
            return null;
        }

        if (strpos($cmd,'Set ')==0 && strpos($cmd,'quality')!==false){
            dbredis::instance()->select(0);
            $quality=dbredis::instance()->get('imageQuality'.$chatId);
            if(empty($quality)) {
                $quality='low';
            }

            $quality=($quality=='low')?'high':'low';
            $bot->keyboard('quality');

            dbredis::instance()->set('imageQuality'.$chatId,$quality);
            $bot->keyboard('main')->sendText('Image quality is '.$quality.' now');
            return null;
        }

        if ($cmd=='Back to game'){
            $gameId=$bot->user()->tg_last_game;
            $bot->keyboard('game')->sendText('Continue playing '.$bot->enableGames[$gameId]);
            return null;
        }

        if ($cmd=='Balance'){

            if($office->apienable>0) {

                if($office->apitype!=0) {
                    $bot->sendText('Games not available [105]');
                    return;
                }

                $api = gameapi::instance(0);
                $checkTGUser = $api->checkTGUser($data['message']['from']['username'], $office->id, $office->gameapiurl);

                if(!$checkTGUser) {
                    $bot->sendText('Games not available [106]');
                    return;
                }

                $u=$bot->user();
                $u->amount=arr::get($checkTGUser,'balance');
                $u->save();
            }

            $bot->keyboard('main')->sendText('Balance: '.th::number_format($bot->user()->amount()));
            return null;
        }



        //Double in
        if ($cmd=='Double'){
            $user=$bot->user();
            auth::force_login_model($user);
            $session=game::view_session('agt',$user->tg_last_game);

            if(!$session['can_double']) {
                $bot->keyboard('game')->sendText('Can\'t double');
            }
            else {

                $quality=dbredis::instance()->get('imageQuality'.$chatId);
                if(empty($quality)) {
                    $quality='low';
                }

                $history=$session['gamble_history'];
                $img2url= http_build_query([
                        'h'=>$history,
                        'balance'=>$user->amount(),
                        'v'=>$this->ver
                ]);
                $bot->keyboard('double')->sendPhoto("{$this->_host}/bot/img2/".$user->tg_last_game."?$img2url&q=".$quality);
            }

            return null;
        }

        if ($cmd=='Red' or $cmd=='Black'){
            $user=$bot->user();
            $gameId=$user->tg_last_game;
            auth::force_login_model($user);
            $session=game::view_session('agt',$gameId);

            $path = 'games/agt/'.$gameId.'/init.php';
            $params=[
                'action'=>'double',
                'gamename'=>$gameId,
                'li'=>$session['li'],
                'color'=>strtolower($cmd),
            ];

            $url=$path.'?'. http_build_query($params);

            try {
                //if !user->loaded nothing works
                $data=Request::factory($url)
                        ->execute();

            } catch (Exception $ex) {
                //do nothing or below
                $bot->keyboard('game')->sendText('Can\'t double');
                return;
            }

            $data=json_decode($data,true);

            $quality=dbredis::instance()->get('imageQuality'.$chatId);
            if(empty($quality)) {
                $quality='low';
            }

            unset($data['gamble_suit_history'][0]);

            $imgparams=[
                    'h'=>$data['gamble_suit_history'],
                    'win'=>$data['win'],
                    'card'=>$data['suite'],
                    'q'=>$quality,
                    'balance'=>$data['balance']+round($data['win'],2),
            ];

            if($data['win']==0) {
                $bot->keyboard('game');
            }

            $bot->sendPhoto($this->_host."/bot/img2/{$gameId}?". http_build_query($imgparams));

            return null;
        }




        //Select a game
        if ($bot->gameExists($cmd)){
            $gameId=$bot->gameId($cmd);
            $user=$bot->user();

            $user->tg_last_game=$gameId;
            $user->save();

            $message = 'You have selected '.$cmd;


            if($user->last_game) {
                auth::force_login_model($user);
                $session=game::view_session('agt',$user->last_game);

                if(!empty($session)) {
                    $gameId=$user->last_game;
                    $user->tg_last_game=$gameId;
                    $user->save();

                    $message='Continue playing free games '.$bot->enableGames[$gameId];
                    $bot->keyboard('gameFG',[
                        'current'=>$session['freeCountCurrent']+1,
                        'all'=>$session['freeCountAll']
                    ])->sendPhoto($this->_host."/games/agt/thumb/{$gameId}.png",$message);
                    return;
                }
                else {
                    //todo добавить в основной контроллер, где новые юзеры проверяются
                    $user->last_game=null;
                    $user->save();
                }
            }


            $bot->keyboard('game')->sendPhoto($this->_host."/games/agt/thumb/{$gameId}.png",$message);
            return null;
        }

        if ($cmd == 'Spin free game'){
            $user=$bot->user();

            $betId=dbredis::instance()->Incr('tgBetId');

            $gameId=$user->tg_last_game;

            if ($gameId && !$bot->gameExists($gameId,true)){
                $user->tg_last_game=null;
                $user->save();
                $bot->keyboard('main')->sendText('Game not found');
                return;
            }

            $gameConf= Kohana::$config->load('agt/'.$gameId);

            $quality=dbredis::instance()->get('imageQuality'.$chatId);
            if(empty($quality)) {
                $quality='low';
            }

            auth::force_login_model($user);

            $session=game::view_session('agt',$gameId);

            $path = 'games/agt/'.$gameId.'/init.php';
            $params=[
                'action'=>'freespin',
                'gamename'=>$gameId,
                'li'=>$session['li'],
                'di'=>'0',
                'amount'=>$session['amount'],
                'userId'=>$user->id,
            ];

            $url=$path.'?'. http_build_query($params);

            try {
                //if !user->loaded nothing works
                $data=Request::factory($url)
                        ->execute();

                $data=json_decode($data,true);
//                file_put_contents('tgRespo',print_r($data,1),FILE_APPEND);

            } catch (Exception $ex) {
                //do nothing or below
                $bot->keyboard('game')->sendPhoto($this->_host."/games/agt/thumb/{$gameId}.png",'Continue playing '.$bot->enableGames[$gameId]);
                return;
            }




            $save=['sym'=>$data['comb'],
                'lm'=>$data['linesMask'],
                'win'=>round($data['win'],2),
                'amount'=>$params['amount'],
                'balance'=>$data['balance']+round($data['session_total_win_free'],2),
                'game'=>$gameId,
                'bonus'=>$data['bonus_all']-$data['bonus'],
                'bonus_all'=>$data['bonus_all'],
                'bonus_win'=>$data['bonus_win'],
            ];


            //here we go
            dbredis::instance()->select(0);
            dbredis::instance()->set('tgBet'.$betId, json_encode($save));
            dbredis::instance()->expire('tgBet'.$betId,60*60);

            if($data['bonus']==0) {
                $bot->keyboard('game');
            }
            else {
                $bot->keyboard('gameFG',[
                    'current'=>$data['bonus_all']-$data['bonus']+1,
                    'all'=>$data['bonus_all']
                ]);
            }


            $bot->sendPhoto($this->_host.'/bot/imgfg/'.$betId.'?q='.$quality);
            return null;
        }

        if (strpos($cmd,'Spin')!==false){
            $cmd= explode(' ',$cmd);
            $amount=$cmd[1];

            $user=$bot->user();
            $gameId=$user->tg_last_game;

            if ($gameId && !$bot->gameExists($gameId,true)){
                $user->tg_last_game=null;
                $user->save();
                $bot->keyboard('main')->sendText('Game not found');
                return;
            }

            $gameConf= Kohana::$config->load('agt/'.$gameId);

            $quality=dbredis::instance()->get('imageQuality'.$chatId);
            if(empty($quality)) {
                $quality='low';
            }

            auth::force_login_model($user);
            $session=game::view_session('agt',$gameId);
			
			
			kohana::$log->add(Log::INFO,Debug::vars($session).auth::$user_id.$gameId.Debug::vars($user->loaded()));

            if(!empty($session) && ($session['freeCountAll']>0 && $session['freeCountAll']!=$session['freeCountCurrent'])) {
                $bot->keyboard('gameFG')->sendText('Continue playing free games '.$bot->enableGames[$gameId]);
                return null;
            }

            $betId=dbredis::instance()->Incr('tgBetId');



            $lines=arr::get( $gameConf,'lines_choose',1);
            $lines=max($lines);

            $path = 'games/agt/'.$gameId.'/init.php';
            $params=[
                'action'=>'spin',
                'gamename'=>$gameId,
                'li'=>$lines,
                'di'=>'0',
                'amount'=>$amount,
                'userId'=>$user->id,
            ];

            $url=$path.'?'. http_build_query($params);

			kohana::$log->add(Log::INFO,$url);

            //if !user->loaded nothing works
            try {
                $data=Request::factory($url)
                        ->execute();

                $data=json_decode($data,true);

//                file_put_contents('tgRespo',print_r($data,1),FILE_APPEND);
            } catch (Exception $ex) {
				
                $bot->sendText('Can\'t bet. Check your balance or bet amount.'."\r\n".'Or try again later. If the problem persists, contact technical support.');
                return;
            }

			kohana::$log->add(Log::INFO,Debug::vars($data));

            $save=['sym'=>$data['comb'],
                'win'=>round($data['win'],2),
                'amount'=>$amount,
                'balance'=>$data['balance']+round($data['win'],2),
                'game'=>$gameId,
                'lm'=>$data['linesMask'],
                'bonus_win'=>$data['bonus_win'],
            ];


            //here we go
            dbredis::instance()->select(0);
            dbredis::instance()->set('tgBet'.$betId, json_encode($save));
            dbredis::instance()->expire('tgBet'.$betId,60*60);

            if ($data['bonus_win']>0){
                $bot->keyboard('gameFG',[
                    'current'=>1,
                    'all'=>$data['bonus_all']
                ]);
            }
            elseif ($data['win']>0){
                $bot->keyboard('gameDouble');
            }
            else{
                $bot->keyboard('game');
            }

            $bot->sendPhoto($this->_host.'/bot/img/'.$betId.'?q='.$quality);
            return null;
        }

        //set bet amount
        if (is_numeric($cmd)){
            $bet=(float) $cmd;
            if($bet<1) {
                $bot->sendText('Min bet is 1');
                return;
            }
            if($bet>200) {
                $bot->sendText('Max bet is 200');
                return;
            }

            $bot->user()->tg_bet=$cmd;
            $bot->user()->save();
            $bot->keyboard('game')->sendText('Bet was set to '.$cmd);
            return null;
        }

        $bot->keyboard('main')->sendText('Hello!');

    }



    public function action_img2(){

        $this->response->headers('Content-Type','image/png');
        $history=arr::get($_GET,'h');
        $game=$this->request->param('id');

        $win=arr::get($_GET,'win',-1);
        $balance=arr::get($_GET,'balance',-1);

        $card=arr::get($_GET,'card',-1);

        $quality=$this->request->query('q')??'low';

        $img=new Bot_Image($quality);
        echo $img->image2($game,$history,$win,$card,$balance)->render();

        return null;


        $tgCommon=DOCROOT.'games'.DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR.'tgbot'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR;
        $tgDir=DOCROOT.'games'.DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR.'tgbot'.DIRECTORY_SEPARATOR.$game.DIRECTORY_SEPARATOR;
        $back=Image::factory($tgDir.'back.jpg');

        $card=arr::get($_GET,'card',-1);

        if ($card==-1){
            $card=Image::factory($tgDir.'gamble_card_back.png');
        }
        else{
            $card=Image::factory($tgCommon."card{$card}.png");
        }
        $back->watermark($card,300/2-90/2,20);

        $win=arr::get($_GET,'win',-1);
        if ($win==-1){
            $txt='Guess the color of the next card and double your winnings';
        }
        elseif ($win=='win'){
            $txt='You won!';
        }
        elseif ($win=='lose'){
            $txt='You lose!';
        }
        else{
            $txt='';
        }
        $back->text($txt,25,165,[255,255,255]);


        $this->response->headers('Content-Type','image/png');
        echo $back->render();

    }


    public function action_imgfg(){

        $betId=$this->request->param('id');

        $this->response->headers('Content-Type','image/png');
        if (empty($betId) ){
            throw new HTTP_Exception_404;
        }


        $data=dbredis::instance()->get('tgBet'.$betId);
        if (!$data){
            throw new HTTP_Exception_404;
        }


        $data= json_decode($data,true);

        $quality=$this->request->query('q')??'low';
        $img=new Bot_Image($quality);

        echo $img->imageFG($data)->render();


    }

    public function action_img(){

        $betId=$this->request->param('id');
        $quality=$this->request->query('q')??'low';

        $this->response->headers('Content-Type','image/png');
        if (empty($betId) ){
            throw new HTTP_Exception_404;
        }


        $data=dbredis::instance()->get('tgBet'.$betId);
        if (!$data){
            throw new HTTP_Exception_404;
        }


        $data= json_decode($data,true);


        $img=new Bot_Image($quality);

        echo $img->image($data)->render();


    }





    public function action_test(){

        phpinfo();
        exit;

        $user=new Model_User(1764369);

        auth::$user_id=1764369;
        game::session('agt','timemachine2');

        print_r(game::session()->data['gamble_history']);

        exit;

        /*
        $data=Request::factory('games/agt/leprechaun/init.php?action=start&gamename=leprechaun&userId='.$user->id);
                ->execute();

        */


        //important! need request /bot/test from "content." subdomain so CONTENT constant is true
        auth::force_login_model($user);

        $game='anonymous';

        $path = 'games/agt/'.$game.'/init.php';
        $params=[
            'action'=>'spin',
            'gamename'=>$game,
            'li'=>'10',
            'di'=>'0',
            'amount'=>'10',
            'userId'=>$user->id,
        ];

        $url=$path.'?'. http_build_query($params);

        //if !user->loaded nothing works
        $data=Request::factory($url)
                ->execute();

        echo $data;
/*
        $data=json_decode($data,1);

        //gamble red
        if($data['win']>0) {
            $params['action']='double';
            $params['color']='red';


            $dataGamble=Request::factory($url)
                ->execute();
        }
*/
    }






    public function action_init(){

        $bot= new Bot($this->_bot_name,0);
        echo $bot->setCallback($this->_host.'/bot');

    }


    public function action_initgames(){

        $img=new Bot_Image();

        $img->initCommon();


        $bot= new Bot($this->_bot_name,0);
        foreach ($bot->enableGames as $game=>$name){
            $img->initGame($game);
        }



    }







}

