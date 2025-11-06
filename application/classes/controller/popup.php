<?php class Controller_Popup extends Controller_Base{

	public $auto_render=false;

	public function action_login(){

		$view=new View('site/popup/login');

        $bad_login_count = Cookie::get('bad_login_user')??0;

        if($bad_login_count >= 3) {
            $view->captcha = Captcha::instance();
        }

		$this->response->body($view->render());

	}

	public function action_register(){

		$view=new View('site/popup/register');
		$view->showbonus = false;
        $view->offices = orm::factory('office')->where('id', 'in', kohana::$config->load('static.offices'))->find_all();

		$bonuslink = Session::instance()->get('bonuslink');
		if($bonuslink) {
			$b = new Model_Bonus_Link($bonuslink);
			if($b->loaded() && $b->use==0) {
				$view->showbonus = true;
			}
		}

		$this->response->body($view->render());

	}

	public function action_remind(){

		$view=new View('site/popup/remind');
		$this->response->body($view->render());

	}

    public function action_contacts() {
        $view=new View('site/popup/contacts');


        $this->response->body($view->render());
    }

    public function action_regfs() {
        $view=new View('site/popup/regfs');

        $reg_fs_games = kohana::$config->load('static.reg_fs_games');
        $games = orm::factory('game')->where('name', 'in', $reg_fs_games)->find_all();

        $view->games = $games;

        $this->response->body($view->render());
    }

    public function action_chest() {

        $view = new View('site/popup/chest');

        $view->headers = [
            'day' => 'День',
            'bonus' => 'Бонус',
        ];

        $model_dayly_bon = orm::factory('daylybonus')
                ->order_by('day')
                ->where('type','in',[auth::user()->parent_acc()->dayly_bonus_type,'cashback'])
                ->find_all();

        $dayly_bonuses = [];

        foreach ($model_dayly_bon as $v) {
            $text = $v->bonus;

            switch ($v->type) {
                case 'cashback':
                    $text = '+ ' . $text*100 . '% cashback';
                    break;
                case 'freespins' or 'freespins2':
                    $text = '+ ' . intval($text) . ' freespin';
                    break;
            }

            $dayly_bonuses[] = [
                'day' => $v->day,
                'bonus' => $text,
            ];
        }


        $view->dayly_bonuses = $dayly_bonuses;

        $this->response->body($view->render());
    }

    public function action_choicedaylyfs() {
        if(!auth::$user_id) {
            throw new HTTP_Exception_404;
        }

        $view=new View('site/popup/daylyfs');

        $choice_dayly_games = kohana::$config->load('static.choice_dayly_games');
        $games = orm::factory('game')->where('name', 'in', $choice_dayly_games)->where('provider', '=', 'our')->find_all();

        $view->games = $games;

        $this->response->body($view->render());
    }

    public function action_forgotpassword() {
        $view = new View('site/popup/forgotpassword');

        $this->response->body($view->render());
    }
    public function action_info() {
        $view=new View('site/popup/info');
        $this->response->body($view->render());
    }
}
