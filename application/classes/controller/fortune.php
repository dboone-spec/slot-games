<?php

class Controller_Fortune extends Controller_Base
{

    public $auto_render = false;

    public function action_index() {
        $salt = 7203370;
        $pass = 'uyt765';

        echo person::pass($pass, $salt);

//        2b8e3bfa256ca9ca30265d5c867a7def - Eev78od
//        28e76aadfc93b5627397c30c180a45bd - qwerty1
    }


    public function action_popup() {
        $view=new View('site/fortune/popup');
        $this->response->body($view->render());
    }

    public function action_status() {
        $ans=[
                'balance'=>0,
                'denomination'=>1,
                'spins'=>-1,
        ];
        $this->response->body(json_encode($ans));
    }

}
