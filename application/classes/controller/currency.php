<?php

class Controller_Currency extends Controller_Base {

	public $auto_render=false;
	public $need_auth=true;

	public function action_set(){
        $id = $this->request->param('id');
        
        $office = new Model_Office($id);
        
        $offices = kohana::$config->load('static.offices');
        
        if($office->loaded() AND in_array($id, $offices)) {
            $user = new Model_User(auth::parent_acc()->id);
            $user->office_id = $id;
            $user->save()->reload();
            
            auth::force_login($user->name);
        }
        
        $this->request->redirect('/');
	}
}