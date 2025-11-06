<?php

class Controller_Admin1_Balance extends Controller_Admin1_Base
{


public function action_index()
{

    $sql="select p.name as who, 
    o.id||' '||o.visible_name as object, 

    a.amount,a.created
    from office_amounts a
    join offices o on o.id=a.office_id
    join persons p on p.id=a.person_id";
    
    if(Person::$role!='sa'){
        $sql.=" where o.id in :oid ";
    }
    

    $sql.=" union all
    select p.name who, p2.name as object, a.amount, a.created
    from person_amounts a
    join persons p on p.id=a.person_id
    join persons p2 on p2.id=a.person2_id";
    
    if(Person::$role!='sa'){
        $sql.=" where p.id = :pid or p2.id = :pid ";
    }
    
    $sql.=" order by 4";
    
    
    $data=db::query(1, $sql)->param(':oid', Person::user()->offices())
                            ->param(':pid',Person::$user_id)
                            ->execute()
                            ->as_array();
    


    $view=new View('admin1/balance/index');
    $view->data=$data;
    
    $this->template->content=$view;

}

   

}
