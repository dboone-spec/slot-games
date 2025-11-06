<?php

class Controller_Admin1_Processstatus extends Controller_Admin1_Base
{

    public function action_index()
    {


        $data = dbredis::instance()->get('__process_lock__*');


        $view          = new View('admin1/status/process');
        $view->processes = $data;
        $this->template->content = $view;
    }

}
