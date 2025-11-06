<?php

class Controller_Games_Egt extends Controller_Games_Agt
{

    public $brand = 'egt';

    public function action_game()
    {

        $id = $this->request->param('id');
        if(!$id)
        {
            throw new HTTP_Exception_404;
        }

        $game = new Model_Game(['name' => $id]);
        if(!$game->loaded() || $game->show == 0)
        {
            throw new HTTP_Exception_404;
        }

        if(th::isMobile() && $game->mobile==0) {
            throw new HTTP_Exception_404;
        }

        $egtgame = $game->egtgame;

        if($game->brand!='egt' || !$egtgame->loaded()) {
            throw new HTTP_Exception_404;
        }

        $view           = new View('site' . DIRECTORY_SEPARATOR . $this->brand . DIRECTORY_SEPARATOR . 'common');
        $view->game     = $game;
        $view->egtgame  = $egtgame;
        $view->name     = $id;
        $this->template = $view;
    }

    public function action_inituser()
    {

        $this->auto_render = false;

        $egt = Kohana::$config->load('egt');

        $sql='select ee.* from egtgames ee join games g on g.id=ee.game_id where g.show=1';

        if(th::isMobile()) {
            $sql.=' and g.mobile=1';
        }

        $r = db::query(1,$sql)->execute()->as_array('code');

        $egtgames=[];

        foreach($r as $b => $a) {
            $a['groups']= json_decode($a['groups']);
            $a['featured']=(bool) $a['featured'];
            $a['mlmJackpot']=(bool) $a['mlmJackpot'];
            $egtgames[$b]=[$a];
        }

        Cookie::set('egt_session_key',arr::get($_GET,'sessionKey'));

        $ans = [
                "playerName" => auth::$user_id,
                "balance" => 100 * auth::user(true)->amount(),
                "currency" => "", //EGT
                "showRtp" => false,
                "multigame" => true,
                "sendTotalsInfo" => false,
                'languages'=>$egt['languages'],
                'groups'=>$egt['groups'],
                'complex'=>$egtgames,
                "sessionKey" => arr::get($_GET,'sessionKey'),
                "msg" => "success",
                "messageId" => $_GET['messageId'] ?? '-1',
                "qName" => "app.services.messages.response.LoginResponse",
                "command" => "login",
                "eventTimestamp" => time()*1000,
        ];


        $this->response->body(json_encode($ans));
    }

    public function action_init ()
    {

        $gameIdNumber = arr::get ($_GET, 'gameIdentificationNumber');
        $gameId = $this->request->param('id');

        if($gameIdNumber) {
            $gg = new Model_Egtgame(['gameIdentificationNumber'=>$gameIdNumber]);
            if(!$gg->loaded()) {
                throw new HTTP_Exception_404;
            }
            $gameId = $gg->game->name;
        }


        $novomatic = Kohana::$config->load ($this->brand.'/' . $gameId);

        if (!$novomatic)
        {
            throw new HTTP_Exception_404;
        }

        game::session ($this->brand, $gameId);

        $this->auto_render = false;
        $action            = arr::get ($_GET, 'action');

        $mGame = new Model_Game(['name'=>$gameId,'brand'=>$this->brand]);

        $type=$mGame->type;
        $type=($type=='slots')?'slot':$type;

        $logic_class='game_'.$type.'_'.$this->brand;

        $game = new $logic_class($gameId);

        $logic_game_class='game_'.$type.'_'.$this->brand.'_'.$gameId;

        if(class_exists($logic_game_class)) {
            $game= new $logic_game_class($gameId);
        }

        //init
        if ($action == 'start')
        {
            $ans = $game->init ();
        }

        if ($action == 'close')
        {

            $game->save_win();
            $ans = [
                'command' => "unsubscribe",
                'gameIdentificationNumber' => arr::get($_GET,'gameIdentificationNumber'),
                'gameNumber' => -1,
                'qName' => "jServer.TSHJSlot.unsubscribe",
                'sessionKey' => arr::get($_GET,'sessionKey'),
                'messageId' => arr::get($_GET,'messageId')
            ];
        }

        //init comb
        if ($action == 'restore')
        {
            $ans = $game->restore ();
        }

        //balance update
        if ($action == 'balance')
        {
            $ans = $game->get_balance ();
        }

        //spin
        if ($action == 'spin')
        {
            //li (line index)- индекс выбранного элемента массива линий
            $lidx = arr::get ($_GET, 'li', -1);
            //bi - индекс выбранного элемента массива базовых ставок
            $bidx = arr::get ($_GET, 'bi', -1);
            //di - индекс выбранного элемента массива деноминаций
            $didx = arr::get ($_GET, 'di', -1);
            $ans  = $game->spin ($lidx, $bidx, $didx);
        }

        //save win
        if ($action == 'save')
        {
            $ans = $game->save_win();
        }

        //freerun
        if ($action == 'freespin')
        {
            $ans = $game->bonus_game ();
        }

        //double
        if ($action == 'double')
        {
            $ans = $game->double (arr::get($_GET, 'color'));
        }

        $this->response->body (json_encode ($ans));
    }
}
