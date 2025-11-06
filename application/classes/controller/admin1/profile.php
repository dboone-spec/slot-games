<?php

class Controller_Admin1_Profile extends Controller_Admin1_Base
{


public function action_index()
    {


        $view=new View('admin1/profile/index');
        $view->order=arr::get($_GET,'order','profile');
        $view->alert=arr::get($_GET,'alert');
        $view->action=arr::get($_GET,'action');
        $this->template->content=$view;

    }

    
    
    
    public function action_password(){
        if (!Person::$user_id){
            throw new HTTP_Exception_404;
        }
        
        if ($this->request->method()!='POST'){
            throw new HTTP_Exception_404();
        }

        
        $p=arr::get($_POST,'password');
        $n=arr::get($_POST,'newpassword');
        $c=arr::get($_POST,'confirmpassword');
   

        
        if ($n!=$c){
            $this->request->redirect('/enter/profile?order=password&alert=noeq');
        }
        
        if (strlen($n)<6){
            $this->request->redirect('/enter/profile?order=password&alert=no6');
        }
        
        if (Person::pass($p, person::user()->salt)!=person::user()->password){
            $this->request->redirect('/enter/profile?order=password&alert=nopass');
        }
        
        person::user()->salt=mt_rand(10000,99999);
        person::user()->password=Person::pass($n, person::user()->salt);
        person::user()->save();
        
        $this->request->redirect('/enter/profile?order=password&action=change');
        
        
        
        
    }
    
    
    
    
    public function action_account(){
        if (!Person::$user_id){
            throw new HTTP_Exception_404;
        }
        
        if ($this->request->method()!='POST'){
            throw new HTTP_Exception_404();
        }

        
        $name=arr::get($_POST,'visible_name',person::user()->visible_name);
        $phone=arr::get($_POST,'phone',person::user()->phone);
        $email=arr::get($_POST,'email',person::user()->email);
   

        
        
        person::user()->visible_name=$name;
        person::user()->phone=$phone;
        person::user()->email=$email;
        person::user()->save();
        
        $this->request->redirect('/enter/profile?action=change');
        
        
        
        
    }
    
    
    
    
    public function action_tgname(){
        
        if (!Person::$user_id){
            throw new HTTP_Exception_404;
        }
        
        if ($this->request->method()!='POST'){
            throw new HTTP_Exception_404();
        }
        
        $this->auto_render=false;
        
        $name=arr::get($_POST,'tgname',person::user()->tgname);
        
        $name=trim($name,'@');
        
        if (strlen($name)<2){
            $this->response->body( json_encode(['error'=>'1','message'=>'Name cannot be empty.']) );
            return null;
        }
        
        
        if (person::user()->tgchatid>0){
            $this->response->body( json_encode(['error'=>'1','message'=>'Security error.']) );
            return null;
        }
        
        person::user()->tgname=$name;
        person::user()->save();
        
        $this->response->body( json_encode(['error'=>'0', 'message'=>'Name changed successfully.']) );
        
        
    }
    
    
    public function action_apitoken(){

        if (!Person::$user_id){
            throw new HTTP_Exception_404;
        }

        if ($this->request->method()!='POST'){
            throw new HTTP_Exception_404();
        }

        $this->auto_render=false;

        person::user()->apitoken=guid::create();
        person::user()->save();

        $this->response->body( json_encode(['error'=>'0', 'message'=>'New API token created successfully.', 'apitoken'=>person::user()->apitoken]) );


    }
    
    
    
     public function action_tgid(){
        
        if (!Person::$user_id){
            throw new HTTP_Exception_404;
        }
        
        if ($this->request->method()!='POST'){
            throw new HTTP_Exception_404();
        }
        
        $this->auto_render=false;
        
        //if two users press allow at the same time will be this error 
        if (Person::user()->tgchatid>0){
           $this->response->body( json_encode(['error'=>'1','message'=>'Security error.']) ); 
           return null;
        }
        
        tg::update();
        
        if (Person::user(true)->tgchatid>0){
            
            Person::user()->enable_telegram=1;
            Person::user()->save();
                    
            tg::send(Person::user()->tgchatid,'Hello');
            $this->response->body( json_encode(['error'=>'0','message'=>'Success']) ); 
            return null;
        }
        
        $name=Person::user()->tgname;
        $this->response->body( json_encode(['error'=>'1','message'=>"Can't send message to $name. Check Telegram Name or send any message to bot and try again."]) ); 
        
        
        
    }


}
