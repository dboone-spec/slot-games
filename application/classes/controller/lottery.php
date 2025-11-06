<?php

class Controller_Lottery extends Controller_Base {
    
    public $auto_render = true;

    public function action_index() {
        $view = new View('pages/lottery');
                
        $this->template->content = $view;
    }
    
    public function action_select() {
        $share_id = $this->request->param('id');

        $share = new Model_Share($share_id);
        
        if($share->loaded() AND $share->enabled) {
            $view = new View('pages/lottery');
            $view->share = $share;
            
            $this->template->content = $view;
        } else {
            throw new HTTP_Exception_404;
        }
    }
}
