<?php

class controller_demo extends controller_base {
    public $template='layout/demo';
    public $need_auth=false;
    public $demo=true;
    public $gameapi=true;

    public function action_index() {
        $vg = new vipgameapi();
        $list = $vg->gamelist();
        $this->template->gamelist = $list;
    }

    public function action_play() {
        $this->auto_render = false;
        $vg = new vipgameapi();

        if(!$this->request->param('id')) {
            $this->request->redirect('/');
        }
        //only demo first
        $this->response->body($vg->getgame($this->request->param('id'),1));
    }

}