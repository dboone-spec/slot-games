<?php

class Controller_Play extends Controller_Base
{
    public function before()
    {
        parent::before();

        $this->auto_render = false;


        $vg = new vipgameapi();

        if(!$this->request->action()) {
            $this->request->redirect('/');
        }

        $game = new Model_Game(['name'=>$this->request->action()]);

        if(!$game->loaded()) {
            $this->request->redirect('/');
        }

        echo($vg->getgame($game->id,0));
        exit;
    }

}
