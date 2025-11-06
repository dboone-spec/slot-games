<?php

class Controller_Bonus extends Controller_Base {
    
    public $auto_render = false;

    public function before() {
        parent::before();
        
        if(!auth::$user_id OR !$this->request->is_ajax()) {
            throw new HTTP_Exception_404;
        }
    }
    
    public function action_index() {
        $view = new View('site/popup/bonus');
        $view->bonus = auth::user()->pay_bonuses();
        
        $this->response->body($view->render());
    }
    
    public function action_info() {
        $ans = ["show" => 0];
        
        if($this->request->is_ajax()) {
            if(auth::$user_id) {
                $ans = [
                    "last_bonus" => auth::user()->last_bonus,
                    "show" => auth::user()->last_bonus>0 ? 1 : 0,
                ];
            }
        }        
        $this->response->body(json_encode($ans));
    }
    
    public function action_referallink() {        
        $view = new View('site/popup/referallink');
        $this->response->body($view->render());
    }
}