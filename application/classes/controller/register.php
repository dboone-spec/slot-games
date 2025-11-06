<?php

class Controller_Register extends Controller_Base{
	
	public $template='layout/site';
	
	public function action_index(){

        if(!SBC_DOMAIN) {
            throw new HTTP_Exception_403();
        }

        if(auth::$user_id) {
            $this->request->redirect('/interactive');
        }

        if($this->request->method()=='POST') {

            $data=array_filter($_POST,function($value) { return !empty($value); });

            $visible_name=trim(arr::get($data,'name'));
            $company_name=trim(arr::get($data,'company'));

            $visible_name.=' ['.$company_name.']';

            $phone=trim(arr::get($data,'phone'));
            $tg=trim(arr::get($data,'telegram'));
            $email=trim(arr::get($data,'email'));

            $all_contacts=[];

            if(!empty($phone)) {
                $all_contacts[]='p|'.$phone;
            }

            if(!empty($email)) {
                $all_contacts[]='e|'.$email;
            }

            if(!empty($tg)) {
                $all_contacts[]='t|'.$tg;
            }

            if(empty($all_contacts)) {
                throw new Exception('not enough contacts. 1 need at least');
            }

            auth::add_sbc_account($visible_name,implode('-',$all_contacts));

            $this->request->redirect('/interactive');
        }

        $view=new View('login/register');

        $this->template->content=$view;

	}

	
}
