<?php

class Controller_Admin_Person extends Super {


public $mark='Persons'; //имя
public $model_name='person'; //имя модели
public $controller='person';
public $sh='super/normal';

public function configure() {


    $this->list=['name','fio','amount','office_id','role'];
    $this->search=['name','fio','office_id','role'];
    $this->show=['name','fio','amount','office_id','password','enable_telegram','role'];

    if (Person::$role=='sa'){
        $roles=[
            'gameman'=>'gameman',
            'client'=>'client',
            'cashier'=>'cashier'];
    }

    if (Person::$role=='gameman'){
        $roles=['client'=>'client'];
        
        if (empty(Person::user()->parent_id)){
            $roles['gameman']='gameman';           
        }
        
        $this->show=['name','fio','amount','password','enable_telegram','role'];
    }

    if (Person::$role=='client'){
        $roles=['cashier'=>'cashier'];
    }

    $this->vidgets['name'] = new Vidget_Echo('name',$this->model);
    
    $this->vidgets['password'] = new Vidget_Password('password',$this->model);
    $this->vidgets['password']->param('salt','salt');
    $this->vidgets['password']->param('func',['Person','pass']);
    
    $this->vidgets['enable_telegram'] = new Vidget_Checkbox('enable_telegram',$this->model);
    
    $this->vidgets['role'] = new Vidget_List('role',$this->model);
    $this->vidgets['role']->param('list',$roles);
    
    $this->vidgets['amount'] = new Vidget_Amount_Person('amount',$this->model);
    

    $this->vidgets['office_id'] = new Vidget_List('office_id',$this->model);
    $this->vidgets['office_id']->param('list',Person::user()->officesName(null,true));

}


public function handler_search($vars){
    $model = parent::handler_search($vars);
    
    if (Person::$role=='sa'){
        //$model->where('role','!=','sa');
    }
    
    if (Person::$role=='gameman'){
        $model->where('parent_id','=', Person::$user_id)
              ->where('role','in',['client','gameman'] );
    }

    if (Person::$role=='client'){
        $model->where('office_id','in', Person::user()->offices())
              ->where('role','=','cashier');
    }
    
    return $model;
}


public function canItem(){

    if ($this->model->loaded()){
        
        if (Person::$role=='gameman'){
            if ($this->model->parent_id!=Person::$user_id){
                throw new HTTP_Exception_404;
            }
        }
        
        if (Person::$role=='client'){
            if (!in_array($this->model->office_id,Person::user()->offices() ) ){
                throw new HTTP_Exception_404;
            }
        }
        
    }
    
    return parent::canItem();
}

public function handler_save($data){
    
    if (Person::$role=='gameman'  ){
        
        if (!in_array($data['role'],['client','gameman'] )){
            $data['role']='client';
        }
        if ($this->model->loaded()){
            $data['role']=$this->model->role;
        }
        
        
    }

    if (Person::$role=='client'){
        $data['role']='cashier';
    }
    
    return parent::handler_save($data);
    
}



    public function action_balance(){
        
        
        $view=new View('admin/person/replenish');
        
        $error=null;
        
        $id=$this->request->param('id');
        $amount=arr::get($_GET,'amount',0);
        $mode=arr::get($_GET,'mode');
        $view->id=$id;
        
        $person2=new Model_Person($id);
        
        $person2Parent=new Model_Person($person2->parent_id);
        
        
        if (!$person2->loaded() ){
            throw new HTTP_Exception_403();
        }
        
        if(Person::$role!='sa'){
            if ($person2->parent_id!= Person::$user_id and $person2Parent->parent_id!=Person::$user_id  ){
                throw new HTTP_Exception_403();
            }
        }
        
        if (!in_array($mode,['replenish','takeoff'])){
            throw new HTTP_Exception_403();
        }
        if (!is_numeric($amount) or $amount<=0 ){
            $view->error='Wrong amount value '. HTML::chars($amount);
            $this->template->content=$view;
            return null;
        }
        
        $m=1;
        if($mode=='takeoff'){
            $m=-1;
        }

        if($mode=='takeoff' and !in_array(Person::$role,['sa','client'])){
            throw new HTTP_Exception_403();
            $this->template->content=$view;
            return null;
        }
        
        if(Person::user()->amount<$amount and $mode=='replenish'){
            $view->error='Insufficient person balance, maximum amount value is '.Person::user()->amount;
            $this->template->content=$view;
            return null;
        }
        
        $office=new Model_Office($id);
        
        if($person2->amount<$amount and $mode=='takeoff'){
            $view->error='Insufficient person2 balance, maximum amount value is '.$person2->amount;
            $this->template->content=$view;
            return null;
        }

            
            
        database::instance()->begin();

        $sql='update persons set amount=amount+:amount where id=:pid';
        db::query(Database::UPDATE, $sql)->param(':amount',-1*$amount*$m)
                                         ->param(':pid',Person::$user_id)
                                         ->execute();


        $sql='update persons set amount=amount+:amount where id=:id';
        db::query(Database::UPDATE, $sql)->param(':amount',$amount*$m)
                                        ->param(':id',$person2->id)
                                        ->execute();


        $o=new Model_Person_Amount();
        $o->amount=$amount*$m;
        $o->person_id= Person::$user_id;
        $o->person2_id=$person2->id;
        $o->save();


        database::instance()->commit();
       
        
        
        if(!empty($error)){
            $view->error=$error;
            $this->template->content=$view;
            return null;
        }
        
        $this->request->redirect('/enter/person/item/'.$id);
            
        
    }

    
}
