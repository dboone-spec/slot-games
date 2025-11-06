<?php

class Controller_Admin1_Lockstatus extends Controller_Admin1_Base
{

    public function action_index()
    {

        $view          = '';

        $find_keys=dbredis::instance()->keys('lastExecProcess-*');

        if($find_keys) {
            foreach($find_keys as $k) {
                if(strpos($k,'__process_lock__')!==false) {
                    continue;
                }
                $view.=$k.' : '.date('Y-m-d H:i:s',dbredis::instance()->get($k)).'<br />';
            }
        }
        else {
            $view='no entries';
        }

        $this->template->content = '<div class="pc-container">'.$view.'</div>';
    }

}
