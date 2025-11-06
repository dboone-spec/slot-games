<?php

class Controller_Admin_Login extends Controller_Template {


public $template='layout/login';
protected $redirect='/admin';
public $dir = '/enter';



public function action_index() {

    I18n::$lang='en';

    if( !GAMEOFFICE) {
        throw new HTTP_Exception_404;
    }

    $route_name = $this->request->route()->name($this->request->route());

    I18n::$lang = Cookie::get('lang', I18n::$lang);

    $this->redirect = '/'.Route::get($route_name)->uri(array($this->request->directory()));

    $view = View::factory('/admin/login/index');
    $view->dir = $this->dir;
    $view->error=false;


    $this->template->content = $view;
}


public function action_login(){
    I18n::$lang='en';
    $this->auto_render=false;


    if ($this->request->method()!='POST') {
        throw new HTTP_Exception_404;
    }

    $ans=[];
    $ans['status']='error';

    $ans['error']=__('You entered an invalid username or password.');

    $auth_code = $_POST['auth_code']??null;
    $captcha = isset($_POST['captcha'])? $_POST['captcha'] : null;
    $person = new Model_Person(["name" => Arr::get($_POST, 'login')]);
    $password=Arr::get($_POST, 'password');
    $code=Arr::get($_POST, 'code',-1);
    $telegram=Arr::get($_POST, 'telegram',null);
    $telegram=trim($telegram,'@');

    //if(0 && $person->loaded() && ($person->role!=$route_name && $person->role!='admin')) {
    if(!$person->loaded()) {
        $ans['error']=__('You entered an invalid username or password! Try again!');
        $this->response->body(json_encode($ans));
        return null;
    }

    if($person->loaded() AND $person->blocked) {
        $ans['error']=__('User is blocked!');
        $this->response->body(json_encode($ans));
        return null;
    }

    if($person->loaded() AND $person->role=='cashier' && $person->my_office->apienable>0) {
        $ans['error']=__('User is blocked for API mode!');
        $this->response->body(json_encode($ans));
        return null;
    }

    if(Person::pass($password,$person->salt)!=$person->password){
        $ans['error']=__('You entered an invalid username or password! Try again!');
        $this->response->body(json_encode($ans));
        return null;
    }


    if ($person->enable_telegram!=0 and empty($code)){
        if (!$person->tgname and empty($telegram)){
            $ans['error']=__('No telegram user');
            $ans['status']=__('telegram');
            $this->response->body(json_encode($ans));
            return null;
        }

        if (empty($person->tgname) or empty($person->tgchatid) ){
            $person->tgname=$telegram;
            $person->save();
            tg::update();
            $person->reload();

        }

        if (empty($person->tgchatid)){
            $ans['error']=__('Failed to send message.<br>Try to send a message to the bot and try again.');
            $this->response->body(json_encode($ans));
            return null;
        }

        $person->auth_code=mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
        $person->save();

        if (!tg::send($person->tgchatid,$person->auth_code)){
            $ans['error']='Failed to send message.';
            $this->response->body(json_encode($ans));
            return null;
        }

        $ans['error']='Enter the code';
        $ans['status']='code';
        $this->response->body(json_encode($ans));
        return null;

    }

    if ($code!=$person->auth_code and $person->enable_telegram!=0){
        $ans['error']='Wrong code';
        $this->response->body(json_encode($ans));
        return null;
    }




    if (Person::login(Arr::get($_POST, 'login'), Arr::get($_POST, 'password') )) {
        $ans['error']=__('Wait...');
        $ans['status']='login';
        $this->response->body(json_encode($ans));
    }

    $this->response->body(json_encode($ans));



}


    private function checkBlocking() {
        $blocked = 0;
        $person = new Model_Person(["name" => Arr::get($_POST, 'login')]);

        if($person->blocked) {
            return 1;
        }

        if(!Cookie::get('bad_captcha')) {
            Cookie::set('bad_captcha', 1);
        } else {
            $bad_captcha_count = Cookie::get('bad_captcha') + 1;
            if($person->loaded() AND $bad_captcha_count >= 3) {
                $person->blocked = 1;
                $person->save();
                $blocked = 1;
                Cookie::delete('bad_captcha');
            } else {
                Cookie::set('bad_captcha', $bad_captcha_count);
            }
        }
        return $blocked;
    }

    public function action_logout() {
    	$this->auto_render=false;

        if(!defined('ADMINR')) {
            $route_name = $this->request->route()->name($this->request->route());
        } else {
            $route_name = 'admin'.ADMINR;
        }

        $this->redirect = '/'.Route::get($route_name)->uri(array($this->request->directory()));

        Person::logout();
        $this->request->redirect($this->redirect);
    }









}








