<?php

class Controller_Admin1_Gamesessions extends Controller_Admin1_Base
{

    public function action_index()
    {

        $data=[];
        
        $show_del=!!arr::get($_GET,'show_del',false);
        
        if($user_id=(int) arr::get($_GET,'user_id'))
        {
            $redis = dbredis::instance();

            $data = $redis->keys($user_id.'*');
            sort($data);
        }

        $view          = new View('admin1/games/sessions');
        $view->user_id = $user_id;
        $view->show_del = $show_del;
        
        $view->sessions = $data;
        $this->template->content = $view;
    }

    public function action_delete()
    {
        $u_id=$this->request->param('id');
        $game=$this->request->query('game');
        
        $redis = dbredis::instance();
        $key=implode('-',[$u_id,'agt',$game]);
        $del_key=$key.'_del'.time();
        $redis->renameNx($key,$del_key);
        $redis->expire($del_key, 2*24*60*60);
        
        $this->request->redirect('/enter/gamesessions?user_id='.$u_id);
    }
}
