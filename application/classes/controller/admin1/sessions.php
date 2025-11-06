<?php

class Controller_Admin1_Sessions extends Controller_Admin1_Base
{


public function action_index()
{
    
    $userId=arr::get($_GET,'userId',false);
    
    $data=[];
    
    if ($userId){
        $u=new Model_User($userId);
        
        if ($u->loaded()){
            $r= dbredis::instance();
    
            $sql='select name, visible_name
                  from games
                  where show=1
                  order by visible_name';


            $games=db::query(1, $sql)->execute()->as_array();

            foreach ($games as $game){
                $row=dbredis::instance()->get("$userId-agt-{$game['name']}");
                if (!empty($row)){
                    $data[]=['data'=>print_r(th::ObjectToArray($row),true),
                             'ttl'=>dbredis::instance()->ttl("$userId-agt-{$game['name']}"),
                             'game'=>$game['visible_name']
                             ];
                }
            }
            
        }
        
    }
    
    $view=new View('admin1/sessions/index');
    $view->data=$data;
    $view->userId=$userId;
    
    $this->template->content=$view;
    
    
    

}



}
