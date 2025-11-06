<?php

class Controller_Admin1_Lang extends Controller_Admin1_Base {

	public function action_set(){
        $lang = $this->request->param('id');
        //$lang='en';
        Cookie::set('lang',$lang, Date::YEAR); //language
        if(person::user()->id){
            $person = new Model_Person(person::user()->id);
            $person->lang = $lang;
            $person->save();
        }
        $this->request->redirect($this->request->referrer());
	}
}