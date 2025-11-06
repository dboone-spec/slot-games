<?php

class Controller_Lang extends Controller_Base {

	public $auto_render=false;
	public $need_auth=false;

	public function action_set(){
        //$lang = $this->request->param('id');
        $lang='en';
        if($lang!='ru' AND !I18n::load($lang)) {
            $this->request->redirect($this->request->referrer());
        }

        Cookie::set('lang',$lang, Date::YEAR); //language
        if(auth::$user_id){
            $user = auth::user();
            $user->lang = $lang;
            $user->save();
        }
        $this->request->redirect($this->request->referrer());
	}
}