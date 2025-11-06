<?php

abstract class Controller_Game extends Controller_Base
{

    public $template  = 'layout/game';
    public $need_auth = true;
    public $brand;
    public $game_id;
    public $can_fun   = true;
    public $gameapi   = true;

    public function before()
    {
        if($this->request->action() == 'ask')
        {//Для попапа, если не авторизован
            $this->need_auth = FALSE;
        }
        if(DEMO_DOMAIN)
        {
            $this->demo        = true;
            $this->show_trader = false;
        }

        if(DEMO_DOMAIN) {
            $this->response->headers(['token'=>'demo']);
			
			if(isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe' && !DEMO_MODE) {
                $this->request->redirect(str_replace('//','https://demo.',$this->request->url()));
            }
        }

        $game_name = $this->request->param('id');

        if(defined('DEMO_MODE') && DEMO_MODE && $this->request->is_ajax()) {
            $game_name = $this->request->query('gamename');
        }

        if(GAMECONTENT)
        {
            $login = arr::get($_GET,'user');
            $token = arr::get($_GET,'token');

            $headers = [
                    'X-Frame-Options' => 'ALLOWALL',
            ];

            if(MOON_GAME) {

                if(empty($this->request->headers('tokenuser'))) {
                    Kohana::$log->add(Log::ALERT,Debug::vars($this->request->query()));
                    throw new Exception('empty token');
                }

                $u = new Model_User($this->request->headers('tokenuser'));

                if(!$u->loaded()) {
                    throw new Exception('moongame error user ['.$this->request->headers('tokenuser').']. not found '.$u->api_key.' | '.$token);
                }

                if(in_array($u->office_id,[777,999,456]) || !empty($u->barcode)) {
                    auth::game_login($u);
                }
                else {

                    if(empty($this->request->headers('sign'))) {
                        throw new Exception('empty sign');
                    }

                    $params=[
                        $this->request->headers('token'),
                        $this->request->headers('tokenuser'),
                    ];

                    $signString = implode(':', $params);
                    $signString.='vEr39LKJ@k!';

                    if(md5($signString)!=$this->request->headers('sign')) {
                        throw new Exception('wrong sign');
                    }

                    $moon_game = null;

                    if(in_array($this->request->query('gamename'),['aerobet'])) {
                        $moon_game=$this->request->query('gamename');
                    }

                    auth::from_token($this->request->headers('token'),$this->request->headers('tokenuser'),$moon_game);
                    $headers['token']=auth::$token;
                }

            }
            elseif($login && $token)
            { //need auth
                $u = new Model_User(['api_name' => $login]);
                if($u->loaded() && $u->api_key == $token)
                {
                    if($u->api_session_id) {
                        auth::setCustomSessionId($u->id,$u->api_session_id);
						auth::setCustomGameSessionId($u->id,$game_name,$u->api_session_id);
//                        Cookie::set('api_session_id',$u->api_session_id);
                        $u->api_session_id=null;
                    }
                    $u->api_key = guid::create();
                    $u->save();

                    if(PROJECT==1) {
                        if(in_array($game_name,['tensorbetter','jacksorbetter','acesandfaces','aerobet'])) {
                            auth::game_login($u,$game_name,false);
                        }
                        else {
                            auth::game_login($u);
                        }
                    }
                    else {
                        auth::force_login($u->name);
                    }
                }
            }
            elseif(PROJECT==1 && $this->request->is_ajax()) {
                if(!DEMO_DOMAIN) {
                    if(in_array($game_name,['tensorbetter','jacksorbetter','acesandfaces'])) {
                        auth::from_token($this->request->headers('token'),$this->request->headers('tokenuser'),$game_name);
                    }
                    else {
                        auth::from_token($this->request->headers('token'),$this->request->headers('tokenuser'));
                    }
                    $headers['token']=auth::$token;
                }
            }

            $this->template = 'layout/iframe';

            $this->response->headers($headers);
        }

        parent::before();

        $block=th::isBlockedByIP();

		$country=$_SERVER['HTTP_CF_IPCOUNTRY']??'';
        if(!empty($country)) {
            $country='('.$country.')';
        }

		if(auth::$user_id && isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $u=auth::user();
            db::query(1,'insert into country_stats(currency_id,office_id,user_id,country,domain) values(:currency_id,:office_id,:user_id,:country,:domain) on conflict(currency_id,office_id,user_id,country,domain) do nothing')
                ->parameters([
                    ':currency_id'=>$u->office->currency_id,
                    ':office_id'=>$u->office_id,
                    ':user_id'=>auth::$user_id,
                    ':country'=>$_SERVER['HTTP_CF_IPCOUNTRY'],
                    ':domain'=>substr($_SERVER['HTTP_HOST']??'unknown',0,50),
                ])
                ->execute();
        }

        if($block) {
            echo '<h1 style="display: flex;justify-content: center;text-shadow: 1px 0 1px #fff,0 1px 1px #fff,-1px 0 1px #fff,0 -1px 1px #fff;">Sorry, no access for your country '.$country.'</h1>';
            exit;
        }

        if(!GAMECONTENT && PROJECT==1 && !DEMO_DOMAIN && !empty(auth::$user_id)) {
            $this->response->headers(['token'=>'demo']);
        }


        $game = new Model_Game(['name' => $game_name,'brand' => $this->brand]);

        if(!$game->loaded() OR ( $this->request->action() != 'ask' AND ! auth::user()->canPlay($game->name)) OR $game->category=='coming')
        {
            $this->request->redirect('/');
        }

        $this->game_id = $game->id;

        if(auth::user(true)->last_game && !in_array(auth::user()->office->apitype,[4,8,9,10]) && $this->request->param('id') != auth::user()->last_game)
        {
            $game = new Model_Game(['name' => auth::user()->last_game]);
            $this->request->redirect($game->get_link());
        }
    }

    public function action_ask()
    {
        $this->auto_render = false;

        if(!th::isMobile())
        {
            throw new HTTP_Exception_404;
        }
        $view = new View('site/popup/demo');

        $game_short_name = $this->request->param('id');
        $game            = new Model_Game(['name' => $game_short_name,'provider' => 'our']);

        if(!$game->loaded())
        {
            throw new HTTP_Exception_404;
        }

        $view->imperium = false;
        $view->game     = $game_short_name;
        $view->name     = $game->visible_name;
        $view->img      = $game->image;
        $view->link     = $game->get_link();

        $this->response->body($view->render());
    }

    public function action_inituser()
    {


        $this->auto_render = false;

        $speed = 0;
        if($this->brand == 'egt')
        {
            $speed = 4;
            $speed = 0;
        }

        $bars = [];

        $pers = 50;

        $config = Kohana::$config->load($this->brand . '/' . $this->request->param('id'));

        //del fo igrosoft
        if($this->brand != 'igrosoft')
        {
            foreach($config['bars'] as $bar)
            {
                $bars[] = array_slice($bar,0,floor(count($bar) * $pers / 100));
            }
        }

        $ans = ["token" => "123123123",
                "view" => th::isMobile() ? 1 : 0,
                "desktop" => 0,
                "full" => 1,
                "game_logo" => "DC",
                "gcat" => "/",
                "exit" => "/",
                "lang" => I18n::$lang,
                "rolls_smoothing" => 0,//
                "server_type" => 2,//
                "speed" => $speed,
                "bars" => $bars,
                "success" => 1,
                "url_math" => "/games/" . $this->brand . '/' . $this->request->param('id') . "/init.php",
                "currency" => "",
                "volume" => 100,
                "game_continue" => 1,
                "url_logo" => 0];

        $ans['gamelist'] = th::get_enabled_games(true);

        $this->response->body(json_encode($ans));
    }

    public function action_mobile()
    {

        $this->request->redirect(str_replace('mobile','',$this->request->url()));

        $id = $this->request->param('id');
        if(!$id)
        {
            throw new HTTP_Exception_404;
        }
        $games = Kohana::$config->load('games');
        if(!isset($games[$this->brand][$id]))
        {
            throw new HTTP_Exception_404;
        }

        $this->template = View::factory('layout/iframe');

        $src = 'http://192.168.0.105';
        $src = 'http://main';

        $src .= UTF8::str_ireplace('mobile','',$this->request->url());

        if(!empty($this->request->query()))
        {
            $src .= '?' . http_build_query($this->request->query());
        }

        $this->template->src  = $src;
        $this->template->game = $id;
    }

    public function action_play()
    {

        $id = $this->request->param('id');
        if(!$id)
        {
            throw new HTTP_Exception_404;
        }

        if((int) arr::get($_GET,'fun',0) == 1)
        {
            $office = new Model_Office(555);

            if($office->loaded() && auth::user()->office_id != $office->id)
            {
                $user            = new Model_User(auth::user()->id);
                $user->office_id = 555;
                $user->save()->reload();

                auth::force_login($user->name);

                $code = new Model_Bonus_Code([
                        'name' => 'fs_fun_' . $id,
                        //для разделения фриспинов при регистрации по офисам
                        'office_id' => auth::user()->office_id,
                ]);

                if($code->loaded())
                {
                    $use_info = auth::user()->can_use_code($code->id);

                    if(!$use_info['error'])
                    {
                        $code->use_code(auth::$user_id,$_SERVER['REMOTE_ADDR']);

                        $game = new Model_Game(['name' => $id,'provider' => 'our']);

                        $this->request->redirect($game->get_link() . '?fun=1');
                    }
                }
            }
        }

        $game = new Model_Game(['name' => $id]);
        if(!$game->loaded() || $game->show == 0)
        {
            throw new HTTP_Exception_404;
        }

        if($this->request->is_ajax())
        {
            $this->request->redirect('/games/' . $this->brand . '/' . $id . '/');
            exit;
        }

        $bad                 = ['sizzlinghot','luckyladycharm','themoneygame','bananas','threee','dolphins','ultrahot','hotcherry',
                'alwayshot','pharaohsgold2','royaltreasures'];
        $this->template->bad = in_array($id,$bad);

        //проверка если есть фриспины, редирект в эту игру. тоже и в империум
        if(false && auth::user()->freespin_code_active)
        {
            $fs = new Model_Freespin(auth::user()->freespin_code_active);
            if($fs->freespins_current != $fs->freespins_break && $fs->game != $id)
            {
                $game = new Model_Game(['name' => $fs->game,'provider' => 'our']);
                $this->request->redirect($game->get_link());
            }
        }

        $this->template->brand   = $this->brand;
        //TODO привести в порядок генерацию ссылок на игру
        $this->template->content = '<iframe width="1920px" height="1080px" allow="autoplay" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" src="/games/' . $this->brand . '/' . $id . '/" style="width: 100%; height:100%;"></iframe>';
        //
        $this->template->game_id = $this->game_id;
        $this->template->games   = [];
        ////
    }

    public function action_game()
    {

        $id = $this->request->param('id');
        if(!$id)
        {
            throw new HTTP_Exception_404;
        }

        $game = new Model_Game(['name' => $id,'show'=>1]);
        if(!$game->loaded())
        {
            throw new HTTP_Exception_404;
        }

        $viewpath = 'site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . $id;

        if($game->provider != 'our')
        {
            $viewpath = 'site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . $game->provider . DIRECTORY_SEPARATOR . $id;

            if($game->provider=='greentube') {
                $viewpath = 'site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . $game->provider;
            }
        }

        //todo need to del it after loading
        try
        {
            $view = new View($viewpath);
        }
        catch(Exception $ex)
        {
            $full_viewpath = APPPATH . 'views' . DIRECTORY_SEPARATOR . $viewpath . '.php';

            $c = (array) kohana::$config->load('novomatic/' . $id);

            $p   = new Parser();
            $url = 'https://free-slots.games/greenslots/' . $c['common']['slotId'] . '/index.php';

            $html = $p->get($url);
            $html = str_replace('free-slots.games','',$html);

            file_put_contents($full_viewpath,$html);
            header("Location: ".'/'.$this->request->uri().'/');
            exit;
            $this->request->redirect();
        }

        $view->game     = $game;
        $view->name     = $id;
        $this->template = $view;
    }

    public function action_init()
    {

        $gameId = arr::get($_GET,'gamename',$this->request->param('id'));

        $novomatic = Kohana::$config->load($this->brand . '/' . $gameId);

        if(!$novomatic)
        {
            throw new HTTP_Exception_404;
        }

        $gameM = new Model_Game(['name' => $gameId,'show'=>1]);
        if(!$gameM->loaded())
        {
            throw new HTTP_Exception_404;
        }

        game::session($this->brand,$gameId);

        $this->auto_render = false;
        $action            = arr::get($_GET,'action');

        $logic_class = 'game_slot_' . $this->brand;

        if($gameM->provider == 'fsgames')
        {
            $r = json_decode($this->request->body(),1);

            if($r['slotEvent']=='getSettings') {
                $action = 'start';
            }
            elseif($r['slotEvent']=='bet') {

                $_GET['li'] = $r['slotLines']-1;
                $_GET['bi'] = $r['slotBet']-1;
                $_GET['di'] = 0;

                $action = 'spin';
            }
            elseif($r['slotEvent']=='slotGamble') {
                $_GET['color'] = (int) ($r['gambleChoice']=='black');

                $action = 'double';
            }
            elseif($r['slotEvent']=='freespin') {
                $action = 'freespin';
            }
        }
        elseif($gameM->provider == 'greentube') {

            $request = trim(json_encode(Request::current()->body()),'"');

            if($request=='30') {
                $action = 'spin';
            }
            if($request=='40') {
                $action = 'save';
            }
            if(strpos($request,"\u0007")===0) {
                $action = 'restore';
            }
            if(strpos($request,"\u0033")===0) {
                $action = 'init';
            }
            if(strpos($request,"\u0050")===0) {
                $action = 'saveparams';
            }

            $logic_class = 'game_slot_' . $gameM->provider;
        }

        $game = new $logic_class($gameId);

        $logic_game_class = 'game_slot_' . $this->brand . '_' . $gameId;

        if(class_exists($logic_game_class))
        {
            $game = new $logic_game_class($gameId);
        }

        if($gameM->provider == 'fsgames') {
            $game->restoreFg=true;
        }

        $ans=[];

        //init
        if($action == 'start')
        {
            $ans = $game->init();
        }

        if($action == 'close')
        {
            $game->save_win();
            exit;
        }

        //init comb
        if($action == 'restore')
        {
            $ans = $game->restore();
        }

        //balance update
        if($action == 'balance')
        {
            $ans = $game->get_balance();
        }

        //spin
        if($action == 'spin')
        {
            //li (line index)- индекс выбранного элемента массива линий
            $lidx = arr::get($_GET,'li',-1);
            //bi - индекс выбранного элемента массива базовых ставок
            $bidx = arr::get($_GET,'bi',-1);
            //di - индекс выбранного элемента массива деноминаций
            $didx = arr::get($_GET,'di',-1);
            $ans  = $game->spin($lidx,$bidx,$didx);
        }

        //save win
        if($action == 'save')
        {
            $ans = $game->save_win();
        }

        //freerun
        if($action == 'freespin')
        {
            $ans = $game->bonus_game();
        }

        //double
        if($action == 'double')
        {
            $ans = $game->double(arr::get($_GET,'color'));
        }

        if($gameM->provider == 'fsgames') {

            $ans = (new Game_Slot_Fsgames($gameId,$ans))->$action()->converted_ans();
        }

        $this->response->body(json_encode($ans));
    }
}
