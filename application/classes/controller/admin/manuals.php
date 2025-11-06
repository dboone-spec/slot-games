<?php

class Controller_Admin_Manuals extends Controller_Admin_Base
{

    public function action_index() {
        
        $view->manuals = $view=new View('admin/manuals/index'.PROJECT);
        $this->template->content = $view;
    }

}
