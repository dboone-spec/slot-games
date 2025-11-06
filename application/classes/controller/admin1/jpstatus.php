<?php

class Controller_Admin1_Jpstatus extends Controller_Admin1_Base
{

    public function action_index()
    {

        $office_id = '';

        $data=[];

//        $content = '<table>';
//        foreach($allKeys as $key)
//        {
//            $content .= '<tr><td>' . $key . '</td><td>' . $redis->get($key) . '</td></tr>';
//        }
//        $content .= '</table>';

        if($this->request->method() == 'POST')
        {
            $office_id=$this->request->post('office_id');
            $redis = dbredis::instance();
            $redis->select(1);
            $data = $redis->keys('*-'.$office_id);
            $data = array_merge($data,$redis->keys('*-'.$office_id.'-*'));
            sort($data);
        }

        $officesList=[-1=>__('All')]+Person::user()->officesName(null,true);

        $view          = new View('admin1/jackpots/status');
        $view->officesList=$officesList;
        $view->office_id = $office_id;
        $view->sessions = $data;
        $this->template->content = $view;
    }

}
