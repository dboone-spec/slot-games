<?php

class Controller_Base extends Controller_Template{

	public $template='layout/site';

    public $demo=false;
    public $show_trader=true;
    public $gameapi=false;
    public $flash_check=false;
	public $need_auth=false;
	public $can_fun=false;
	public $demo456=false;

    protected $show_search = false;

    public function before() {

        //todo другое условие
        if(API_DOMAIN && Kohana::$environment == Kohana::PRODUCTION) {
//            throw new HTTP_Exception_403;
        }

        if(GAMEOFFICE) {
            $this->request->redirect('/enter');
        }


        if(DEMO_DOMAIN) {
            if(!$this->demo456 && DEMO_MODE && !$this->request->is_ajax())
            {
                auth::$prefix .= 'demo' . guid::create();
            }
            else
            {

                auth::$prefix .= 'demo';

                if(null != (arr::get($_GET,'game')))
                {
                    $game_name    = arr::get($_GET,'game');
                    Cookie::set('demo_game',$game_name);
                    Auth::$prefix .= $game_name;
                }
                elseif($game_name = Cookie::get('demo_game'))
                {
                    Auth::$prefix .= $game_name;
                }
            }
        }

        if($popup_name=arr::get($_GET,'popup')) {
            Flash::warning("/popup/$popup_name");
            $this->request->redirect('/');
        }

        I18n::$lang=Cookie::get('lang','en');

        $m = Arr::get($_GET,'fr');
        $m = strlen($m)>0?$m:null;

        if($m) {
            Cookie::set('msrc',$m, Date::YEAR); //metka

            /*
             * TODO временно для сбора статистики
             */
            $partners = [
               'email_fs' => 1,
               'cst' => 3
            ];

            $partner = $partners[$m] ?? null;
            $project = $partner?'':null;

            if($partner AND $project) {
                //проставляем партнера в куку для пользователя
                Cookie::set('partner', $partner, Date::YEAR); //partner
                Cookie::set('project', $project, Date::YEAR); //project
            }

            $guid = Cookie::get('uniqueuser');

            if(!$guid) {
                $guid = guid::create();
                Cookie::set('uniqueuser', $guid, Date::YEAR);
            }

            /*
             * пишем в таблицу с переходами
             * от партнеров
             */
            $follow = new Model_Follow();
            $follow->partner = $partner;
            $follow->project = $project;
            $follow->referrer = $this->request->referrer();
            $follow->msrc = $m;
            $follow->hash = $guid;
            $follow->ip = $_SERVER['REMOTE_ADDR'];
            $follow->save();
        }

        parent::before();

        Auth::instance()->get_user();

        if(auth::$user_id) {
            if(!$this->can_fun && auth::user()->office_id==555) {
                $user = new Model_User(auth::user()->id);
                $user->office_id = 1;
                $user->save()->reload();
                auth::force_login($user->name);
            }

            if(DEMO_DOMAIN) {
                if(!(defined('RELEASE_DOMAIN') && RELEASE_DOMAIN) && !DEMO_MODE) {
                    if(!auth::user()->loaded()) {
                        define('OFFICE', DEMO_OFFICE_ID);
                        Session::instance()->delete(auth::$prefix . 'user_id');
                        auth::add_demo_account();
                    }
                }
            }
            if(!defined('OFFICE')) {
                define('OFFICE', auth::user()->office_id);
            }
        } else {
            if(DEMO_DOMAIN) {
				if(DEMO_MODE) {
					define('OFFICE', 456);
				}
				else {
					define('OFFICE', DEMO_OFFICE_ID);
				}
                if(empty(auth::$user_id) && !SBC_DOMAIN) {
                    auth::add_demo_account();
                }
            }
            elseif(PROJECT=='2') {
                define('OFFICE', 444);
            }
            else {
                define('OFFICE', 1);
            }
        }

        if(office::isBlocked()) {
            throw new HTTP_Exception_405;
        }
        if ($this->need_auth and empty(auth::$user_id)){
            if($this->request->is_ajax()) {
                throw new HTTP_Exception_404;
            }

            $this->request->redirect('/black');
        }
	}

    public function after(){
        if(auth::$user_id && PROJECT==2) {
            $jackpots = auth::user()->office->activeJackpots();
            $this->template->jackpots = $jackpots;
        }
        if(auth::$user_id) {
            //log::userAction(auth::$user_id, $this->request->controller().'_'.$this->request->action().'_'.implode(':',$this->request->param()).'_'.implode(':',$this->request->query()));
        }
        parent::after();
    }

}
