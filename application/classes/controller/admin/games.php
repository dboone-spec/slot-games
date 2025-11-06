<?php

class Controller_Admin_Games extends Controller_Admin_Base
{



public function action_index()
{

    $office_id = arr::get($_GET,'office_id',-1);
    
    if ($office_id!=-1 and !in_array($office_id, Person::user()->offices()) ){
        throw new HTTP_Exception_403();
    }
    
    if ($this->request->method()=='POST'){
        
        $sql='update office_games
            set enable=0
            where office_id=:oid';
                
        db::query(Database::UPDATE,$sql)->param(':oid',$office_id)->execute();
        
        
        
        $games=arr::get($_POST,'games',[]);
        $sql='update office_games
            set enable=1
            where office_id=:oid
                and game_id in :games ';
        db::query(Database::UPDATE,$sql)->param(':oid',$office_id)
                                        ->param(':games',$games)
                                        ->execute();
        
    }
    
    
    
    $sql='select  g.id, g.visible_name, g.brand, o.enable
        from office_games o
        join games g on g.id=o.game_id
        where 
            g.show=1 ';
    
    
    if (PROJECT==1){
        $sql.=" and g.brand='agt' ";
        $brands=['agt'=>'AGT'];
    }
    elseif (PROJECT==2){
        $sql.=" and g.brand in ('egt', 'novomatic', 'igrosoft')";
        $brands=['egt'=>'EGT', 'novomatic'=>'Novomatic', 'igrosoft'=>'Igrosoft'];
    }
    else{
        throw new HTTP_Exception_404();
    }
            
    $sql.='and o.office_id=:oid
        order by g.brand, g.visible_name    ' ;
    
    $games=db::query(1, $sql)->param(':oid',$office_id)->execute()->as_array();
    
    
    
    $view=new View('admin/games/index');
    $view->officesList= [-1=>'Select office']+Person::user()->officesName(null,true);
    $view->office_id=$office_id;
    $view->games=$games;
    $view->brands=$brands;
    
    $this->template->content=$view;

}

   

}
