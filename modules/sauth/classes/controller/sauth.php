<?php defined('SYSPATH') or die('No direct script access.');

class Controller_SAuth extends Controller {

	public $types = array('vk', 'fb', 'od', 'mailru', 'yandex', 'google','tw', 'tg');

//	public $types = array('vk','fb', 'tw');

	public function action_auth()
	{

		$type = $this->request->param('id');
		if(in_array($type, $this->types)) {
            $fp = arr::get($_GET,'fingerprint');
            if(!$fp && in_array($type,['yandex','fb'])) {
                $fp = arr::get($_GET,'state');
            }
			Social::factory($type, $fp)->auth();
		}
		exit();
	}

	public function action_callback()
	{
        //Редирект, если сервер шлет error
        $err=Arr::get($_GET, 'error');
        if ($err=='access_denied') Request::initial()->redirect('/');

		$type = $this->request->param('id');
		if(in_array($type, $this->types)) {
            $fp = arr::get($_GET,'fingerprint');
            if(!$fp && $type=='yandex') {
                $fp = arr::get($_GET,'state');
            }
			Social::factory($type, $fp)->callback();
		}
		$this->action_auth();
	}
}