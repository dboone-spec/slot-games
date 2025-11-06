<?php

class Controller_Games_Demo extends Controller_Games_Agt{

    public $brand='agt';
    protected $_office_id=456;
    //todo выбрать другой потом.


        public $games=[
            'anonymous'=> ['id'=>'anonymous', 'label'=>'Anonymous', 'type'=>'slot', 'image'=>'/games/agt/thumb/anonymous.png', 'category'=>'classic'],
            'infinitygems'=> ['id'=>'infinitygems', 'label'=>'Infinity gems', 'type'=>'slot', 'image'=>'/games/agt/thumb/infinitygems.png', 'category'=>'classic'],
            'casino'=> ['id'=>'casino', 'label'=>'Casino', 'type'=>'slot', 'image'=>'/games/agt/thumb/casino.png', 'category'=>'classic'],
            'arabiannights'=> ['id'=>'arabiannights', 'label'=>'Arabian nights', 'type'=>'slot', 'image'=>'/games/agt/thumb/arabiannights.png', 'category'=>'classic'],
            'aislot'=> ['id'=>'aislot', 'label'=>'AI', 'type'=>'slot', 'image'=>'/games/agt/thumb/aislot.png', 'category'=>'classic'],
            'bigfive'=> ['id'=>'bigfive', 'label'=>'Big Five', 'type'=>'slot', 'image'=>'/games/agt/thumb/bigfive.png', 'category'=>'classic'],
            'megashine100'=> ['id'=>'megashine100', 'label'=>'100 Mega Shine', 'type'=>'slot', 'image'=>'/games/agt/thumb/megashine100.png', 'category'=>'hot'],
            'extraspin'=> ['id'=>'extraspin', 'label'=>'Extra spin', 'type'=>'slot', 'image'=>'/games/agt/thumb/extraspin.png', 'category'=>'hot'],
            'bankofny'=> ['id'=>'bankofny', 'label'=>'Grand Theft', 'type'=>'slot', 'image'=>'/games/agt/thumb/bankofny.png', 'category'=>'classic'],
            'double'=> ['id'=>'double', 'label'=>'Double Hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/double.png', 'category'=>'hot'],
            'jokers100'=> ['id'=>'jokers100', 'label'=>'100 Jokers', 'type'=>'slot', 'image'=>'/games/agt/thumb/jokers100.png', 'category'=>'hot'],
            'firefighters'=> ['id'=>'firefighters', 'label'=>'Firefighters', 'type'=>'slot', 'image'=>'/games/agt/thumb/firefighters.png', 'category'=>'classic'],
            'tesla'=> ['id'=>'tesla', 'label'=>'Tesla', 'type'=>'slot', 'image'=>'/games/agt/thumb/tesla.png', 'category'=>'classic'],
            'hotpepper100'=> ['id'=>'hotpepper100', 'label'=>'100 Hot Pepper', 'type'=>'slot', 'image'=>'/games/agt/thumb/hotpepper100.png', 'category'=>'hot'],
            'dreamcatcher100'=> ['id'=>'dreamcatcher100', 'label'=>'100 Dream catcher', 'type'=>'slot', 'image'=>'/games/agt/thumb/dreamcatcher100.png', 'category'=>'hot'],
            'bluestar100'=> ['id'=>'bluestar100', 'label'=>'100 Blue Star', 'type'=>'slot', 'image'=>'/games/agt/thumb/bluestar100.png', 'category'=>'hot'],
            'hothothot5'=> ['id'=>'hothothot5', 'label'=>'5 Hot hot hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/hothothot.png', 'category'=>'hot'],
            'crystalskull100'=> ['id'=>'crystalskull100', 'label'=>'100 Crystal skull', 'type'=>'slot', 'image'=>'/games/agt/thumb/crystalskull100.png', 'category'=>'hot'],
            'gems20'=> ['id'=>'gems20', 'label'=>'Gems', 'type'=>'slot', 'image'=>'/games/agt/thumb/gems20.png', 'category'=>'hot'],
            'stalker'=> ['id'=>'stalker', 'label'=>'Stalker', 'type'=>'slot', 'image'=>'/games/agt/thumb/stalker.png', 'category'=>'classic'],
            '6dreamcatcher'=> ['id'=>'6dreamcatcher', 'label'=>'Dream catcher 6 reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6dreamcatcher.png', 'category'=>'hot'],
            'greenhot'=> ['id'=>'greenhot', 'label'=>'Green Hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/greenhot.png', 'category'=>'hot'],
            'pharaoh2'=> ['id'=>'pharaoh2', 'label'=>'Pharaoh II', 'type'=>'slot', 'image'=>'/games/agt/thumb/pharaoh2.png', 'category'=>'classic'],
            'bitcoin'=> ['id'=>'bitcoin', 'label'=>'Bitcoin', 'type'=>'slot', 'image'=>'/games/agt/thumb/bitcoin.png', 'category'=>'classic'],
            'besthottest5'=> ['id'=>'besthottest5', 'label'=>'5 Lucky Clover', 'type'=>'slot', 'image'=>'/games/agt/thumb/besthottest5.png', 'category'=>'hot'],
            'applesshine'=> ['id'=>'applesshine', 'label'=>'Apple\'s shine', 'type'=>'slot', 'image'=>'/games/agt/thumb/applesshine.png', 'category'=>'hot'],
            'timemachine2'=> ['id'=>'timemachine2', 'label'=>'Time Machine II', 'type'=>'slot', 'image'=>'/games/agt/thumb/timemachine2.png', 'category'=>'classic'],
            'leprechaun'=> ['id'=>'leprechaun', 'label'=>'The leprechaun', 'type'=>'slot', 'image'=>'/games/agt/thumb/leprechaun.png', 'category'=>'classic'],
            '6hotpepper'=> ['id'=>'6hotpepper', 'label'=>'Hot Pepper 6 reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6hotpepper.png', 'category'=>'hot'],
            '6bluestar40'=> ['id'=>'6bluestar40', 'label'=>'40 Blue Star 6 reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6bluestar40.png', 'category'=>'hot'],
            '6dreamcatcher100'=> ['id'=>'6dreamcatcher100', 'label'=>'100 Dream catcher 6 Reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6dreamcatcher100.png', 'category'=>'hot'],
            'shiningstars'=> ['id'=>'shiningstars', 'label'=>'Shining Stars', 'type'=>'slot', 'image'=>'/games/agt/thumb/shiningstars.png', 'category'=>'hot'],
            'megahot100'=> ['id'=>'megahot100', 'label'=>'100 Mega Hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/megahot100.png', 'category'=>'hot'],
            'piratesgold'=> ['id'=>'piratesgold', 'label'=>'Pirate Gold', 'type'=>'slot', 'image'=>'/games/agt/thumb/pirates.png', 'category'=>'classic'],
            'hotpepper'=> ['id'=>'hotpepper', 'label'=>'Hot Pepper', 'type'=>'slot', 'image'=>'/games/agt/thumb/hotpepper.png', 'category'=>'hot'],
            'besthottest40'=> ['id'=>'besthottest40', 'label'=>'40 Lucky Clover', 'type'=>'slot', 'image'=>'/games/agt/thumb/besthottest40.png', 'category'=>'hot'],
            '6bluestar'=> ['id'=>'6bluestar', 'label'=>'Blue Star 6 reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6bluestar.png', 'category'=>'hot'],
            'applesshine50'=> ['id'=>'applesshine50', 'label'=>'50 Apple\'s shine', 'type'=>'slot', 'image'=>'/games/agt/thumb/applesshine50.png', 'category'=>'hot'],
            'iceqween'=> ['id'=>'iceqween', 'label'=>'Ice Queen', 'type'=>'slot', 'image'=>'/games/agt/thumb/iceqween.png', 'category'=>'classic'],
            'luckyhot10'=> ['id'=>'luckyhot10', 'label'=>'Lucky Hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/luckyhot10.png', 'category'=>'hot'],
            '6luckyclover40'=> ['id'=>'6luckyclover40', 'label'=>'40 Lucky Clover 6', 'type'=>'slot', 'image'=>'/games/agt/thumb/6luckyclover40.png', 'category'=>'hot'],
            'dreamcatcher'=> ['id'=>'dreamcatcher', 'label'=>'Dream catcher', 'type'=>'slot', 'image'=>'/games/agt/thumb/dreamcatcher.png', 'category'=>'hot'],
            'sevenhot20'=> ['id'=>'sevenhot20', 'label'=>'Seven Hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/sevenhot.png', 'category'=>'hot'],
            '6hotpepper40'=> ['id'=>'6hotpepper40', 'label'=>'40 Hot Pepper 6 reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6hotpepper40.png', 'category'=>'hot'],
            'jokers20'=> ['id'=>'jokers20', 'label'=>'Jokers', 'type'=>'slot', 'image'=>'/games/agt/thumb/jokers.png', 'category'=>'hot'],
            '6superhot5'=> ['id'=>'6superhot5', 'label'=>'Super Hot 6 reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6superhot.png', 'category'=>'hot'],
            'besthottest20'=> ['id'=>'besthottest20', 'label'=>'20 Lucky Clover', 'type'=>'slot', 'image'=>'/games/agt/thumb/besthottest20.png', 'category'=>'hot'],
            'megahot20'=> ['id'=>'megahot20', 'label'=>'Mega hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/megahot.png', 'category'=>'hot'],
            'cherryhot'=> ['id'=>'cherryhot', 'label'=>'Cherry Hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/cherryhot.png', 'category'=>'hot'],
            'tropichot'=> ['id'=>'tropichot', 'label'=>'Tropic Hot', 'type'=>'slot', 'image'=>'/games/agt/thumb/tropichot.png', 'category'=>'hot'],
            '6superhot40'=> ['id'=>'6superhot40', 'label'=>'40 Super Hot 6 reels', 'type'=>'slot', 'image'=>'/games/agt/thumb/6superhot40.png', 'category'=>'hot'],
            'besthottest100'=> ['id'=>'besthottest100', 'label'=>'100 Lucky Clover', 'type'=>'slot', 'image'=>'/games/agt/thumb/besthottest100.png', 'category'=>'hot'],
            '6luckyclover20'=> ['id'=>'6luckyclover20', 'label'=>'20 Lucky Clover 6', 'type'=>'slot', 'image'=>'/games/agt/thumb/6luckyclover20.png', 'category'=>'hot'],
            'coolblizzard'=> ['id'=>'coolblizzard', 'label'=>'Blue Star', 'type'=>'slot', 'image'=>'/games/agt/thumb/coolblizzard.png', 'category'=>'hot'],
            'megashine'=> ['id'=>'megashine', 'label'=>'Mega Shine', 'type'=>'slot', 'image'=>'/games/agt/thumb/megashine.png', 'category'=>'hot'],
            'crystalskull'=> ['id'=>'crystalskull', 'label'=>'Crystal skull', 'type'=>'slot', 'image'=>'/games/agt/thumb/crystalskull.png', 'category'=>'hot'],
            'roshambo'=> ['id'=>'roshambo', 'label'=>'Rock Paper Scissors', 'type'=>'roshambo', 'image'=>'/games/agt/thumb/roshambo.png', 'category'=>'hot'],
            'spinners'=> ['id'=>'spinners', 'label'=>'Spinner', 'type'=>'roshambo', 'image'=>'/games/agt/thumb/spinners.png', 'category'=>'hot'],
            'vangogh'=> ['id'=>'vangogh', 'label'=>'Van Gogh', 'type'=>'shuffle', 'image'=>'/games/agt/thumb/vangogh.png', 'category'=>'arcade'],
    ];



    public function before()
    {
        if(PROJECT!=1) {
            throw new HTTP_Exception_404;
        }


        $games_list=file_get_contents(DOCROOT.'gmlist.f');

        if($games_list) {
            $this->games=json_decode($games_list,1);
        }

        $game = arr::get ($_GET, 'gamename',$this->request->param ('id'));

        if($game!='demo' && !isset($this->games[$game])) {
            $this->demo456=true;
            parent::before();
        }
        else {
            $this->response->headers(['token'=>'demo']);
        }

    }

    public function action_savelist(){
        $games=db::query(1,'select name as id,visible_name as label,image,category,type,id as game_id from games where show=1 and (type=\'slot\' or type=\'roshambo\' or type=\'shuffle\')')->execute()->as_array('id');
        file_put_contents(DOCROOT.'gmlist.f',json_encode($games));
        echo 'ok';
        exit;
    }

    public function action_list(){

        $this->auto_render=false;

        $games_list=file_get_contents(DOCROOT.'gmlist.f');

        if($games_list) {
            $this->games=json_decode($games_list,1);
        }

        foreach($this->games as &$g){
            $g['url']="https://demo.kolinz.xyz/games/agt/".$g['id'];
            $g['image']='https://demo.kolinz.xyz'.$g['image'];
            $g['type']=$g['type'];
            $g['images']=[
                $g['image'],
                str_replace('.png','.webp',$g['image'])
            ];
            //0 - horisontal,
            //1 - square
            $g['lobby']=[
                [
                    [
                        'src'=>$g['image'],
                        'type'=>'png',
                    ],
                    [
                        'src'=>str_replace('.png','.webp',$g['image']),
                        'type'=>'webp',
                    ],
                ],
                [
                    [
                        'src'=>str_replace('/thumb/','/sqthumb/',$g['image']),
                        'type'=>'png',
                    ],
                    [
                        'src'=>str_replace(['/thumb/','.png'],['/sqthumb/','.webp'],$g['image']),
                        'type'=>'webp',
                    ],
                ],
            ];
        }

        $this->response->body(json_encode($this->games));

    }


    public function action_info()
    {
        echo('Daily Spins not available with demo mode');
        exit;
    }

    public function action_jserror()
    {
        $post = $this->request->post();
        Kohana::$log->add(Log::ALERT,'JS ERROR!' . json_encode($post));
    }

    public function action_game ()
    {

		


        $id = $this->request->param ('id');
        if (!$id)
        {
            throw new HTTP_Exception_404;
        }

        if (!isset($this->games[$id]))
        {

            $u=auth::user();
            $u->amount = bcdiv(abs(arr::get($_GET,'demobalance',200000)),100,2);
            $u->save();

            return parent::action_game();
        }

        define('OFFICE',$this->_office_id);

		$block=th::isBlockedByIP();

        if($block) {
            echo '<h1 style="display: flex;justify-content: center;text-shadow: 1px 0 1px #fff,0 1px 1px #fff,-1px 0 1px #fff,0 -1px 1px #fff;">Sorry, no access for your country</h1>';
            exit;
        }

        auth::$user_id=mt_rand(999999,1999999999);
        auth::$token='demo';

        if($force_amount=abs(arr::get($_GET,'demobalance'))) {
            dbredis::instance()->set('demoForceAmount'.auth::$token.auth::$user_id,bcdiv($force_amount,100,2));
            dbredis::instance()->expire('demoForceAmount'.auth::$token.auth::$user_id, 60*60);
        }

        $lang=arr::get($_GET,'lang','en');

        dbredis::instance()->set('demoLang'.auth::$token.auth::$user_id,$lang);
        dbredis::instance()->expire('demoLang'.auth::$token.auth::$user_id, 60*60);

        auth::from_token('demo',auth::$token.auth::$user_id);

        $view           = new View ('site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . 'demo');
        $view->game     = $id;
        $view->name     = $id;
        $view->gametype     = $this->games[$id]['type'];
        $view->theme     = (string) Kohana::$config->load('agtthemes.'.$id);
        $this->template = $view;
    }

    public function action_init ()
    {

        $gameId = arr::get ($_GET, 'gamename');

        if (!isset($this->games[$gameId])){
            return parent::action_init();
        }

        define('OFFICE',$this->_office_id);

        auth::from_token('demo',$this->request->headers('tokenuser'));

        game::session ($this->brand.'Demo', $gameId);




        $this->auto_render = false;
        $action            = arr::get ($_GET, 'action');





        $logic_game_class='game_demo_agt_'.$gameId;

        if(class_exists($logic_game_class)) {
            $game= new $logic_game_class($gameId);
        }
        else{

            $game = new Game_Demo_Agt($gameId);
        }




        if ($action == 'info')
        {
            $ans = $game->info();
            $this->response->body($ans);
            exit;
        }
        //init
        //YES
        if ($action == 'start')
        {
            $ans = $game->init ();
        }

        if ($action == 'close')
        {

		$game->save_win();
		exit;
        }

        //init comb
        //YESNO
        if ($action == 'restore')
        {
            $ans = $game->restore ();
        }

        if ($action == 'fsprocess')
        {
            $ans = $game->fsprocess(arr::get($_GET,'act','accept'));
        }

        //balance update
        if ($action == 'balance')
        {
            $ans = $game->get_balance ();
        }

		//buyfg
        if ($action == 'buyfg')
        {
			
            //li (line index)- индекс выбранного элемента массива линий
            $lidx = arr::get ($_GET, 'li', -1);
            $didx = arr::get ($_GET, 'di', -1);
            $amount = arr::get ($_GET, 'amount', 0);
            $ans  = $game->buyfg($lidx, $amount,$didx);
        }

        //spin
        //YES
        if ($action == 'spin')
        {

            //li (line index)- индекс выбранного элемента массива линий
            $lidx = arr::get ($_GET, 'li', -1);
            $didx = arr::get ($_GET, 'di', -1);
            $amount = arr::get ($_GET, 'amount', 0);
            $ans  = $game->spin ($lidx, $amount,$didx);

        }

        //save win
        if ($action == 'save')
        {
            $ans = $game->save_win();
        }

        if ($action == 'finishjp')
        {
            $ans = $game->finishjp();
        }

        //save choose
        if ($action == 'savechoose')
        {
            $chooser_btns = arr::get ($_GET, 'chooser_btns', []);
            $ans = $game->savechoose($chooser_btns);
        }

        //nextjpcard
        if ($action == 'nextjpcard')
        {
            $ans = $game->nextjpcard();
        }

        //nextjpcard
        if ($action == 'lastjpcard')
        {
            $ans = $game->lastjpcard();
        }

        //freerun
        if ($action == 'freespin')
        {
            $ans = $game->bonus_game ();
        }

        //double
        //YES
        if ($action == 'double')
        {
            $ans = $game->double (arr::get($_GET, 'color'));
        }

        //Set ttl for demo session
        $keys=['user_id'=>auth::$user_id, 'type'=>$this->brand.'Demo', 'game'=>$gameId];
        $key=implode('-',$keys);
        dbredis::instance()->expire($key, 30*60);


        $this->response->body (json_encode ($ans));
    }
}

