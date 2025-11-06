<?php

class Controller_Admin1_Manuals extends Controller_Admin1_Base
{

    public function action_index() {

        $v = 'admin1/manuals/index'.PROJECT;


        $view->manuals = $view=new View($v);
		$view->bigcurrent = $this->bigcurrent;
        $this->template->content = $view;
    }

}
