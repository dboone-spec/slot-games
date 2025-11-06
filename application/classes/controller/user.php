<?php class Controller_User extends Controller_Base{

    public $auto_render = false;
    public $can_fun = true;

    public function before() {
        parent::before();

        if(!auth::$user_id OR !$this->request->is_ajax()) {
            throw new HTTP_Exception_404;
        }
    }

    public function action_loyality() {
        $view = new View('user/loyality');
        $view->comp_config = model_user::get_compoint_config();
        $this->response->body($view->render());
    }

    public function action_compswap() {
        $ans = [
            'error' => 1,
            'text' => __('Недостаточно компоинтов для обмена'),
        ];

        $compoints = $this->request->post('compoints');
        if(auth::user()->comp_level == 1) {
            $ans['text'] = __('Для обмена компоинтов необходимо повысить уровень');
        } elseif($compoints <= 0) {
            $ans['text'] = __('Введите количество компоинтов для обмена');
        } elseif(auth::user()->comp_current >= $compoints) {
           $comp = auth::user()->accrual_compoints($compoints);
           $ans = [
                'balance' => $comp['balance'],
                'comp' => $comp['comp'],
                'error' => 0,
                'text' => __("На Ваш баланс зачислено "). $comp['amount_to_balance'].__(" руб. "),
           ];
        }

        $this->response->body(json_encode($ans));
    }

    public function action_bonuses() {
        if(th::isMobile()) {
            $view = new View('user/mobile/bonuses');
        } else {
            $view = new View('user/bonuses');
        }

        $this->response->body($view->render());
    }

    public function action_setveksel() {
        $this->auto_render=false;
        $ans=['error'=>1];

        if(THEME=='veksel' &&  $this->request->method()=='POST') {
            $veksel_provider = $this->request->post('provider');
            $veksel_value = $this->request->post('value');

            if($veksel_provider>=0 && $veksel_value) {
                Session::instance()->set('veksel.provider',$veksel_provider);
                Session::instance()->set('veksel.value',$veksel_value);

                game::session('virtual', 'veksel');
                $sess = game::data();
                $sess['provider'] = $veksel_provider;
                $sess['value'] = $veksel_value;
                game::save($sess);

                $ans['error'] = 0;
            }
        }
        $this->response->body(json_encode($ans));
    }

    public function action_setspins() {
        $type = arr::get($_GET, 'type');
        $freespins_id = arr::get($_GET, 'freespins_id');

        $ans = ['enabled' => true];

        $user_spins = new Model_Freespin($freespins_id);

        /*
         * если id из таблицы фриспинов не для текущего пользователя
         */
        if(!$user_spins->loaded() OR $user_spins->user_id != auth::user()->id) {
            $this->response->body(json_encode(['enabled'=>false]));
            return;
        } else {
            auth::user()->freespin_code_active = $user_spins->id;
            auth::user()->save()->reload();
        }

        $freepins = new Model_Freespin(auth::user()->freespin_code_active);

        $set_in_bonuses = function ($k=1) {
            $spins = new Model_Freespin(auth::user()->freespin_code_active);

            $bonus_code = new Model_Bonus_Code(['id'=>$spins->code_id]);

            if($bonus_code->loaded() AND $spins->loaded() AND $spins->freespins_current == 0 AND ($spins->payed == 0 OR $k<0)) {
                $bonus = $spins->freespins_break * $spins->lines * $spins->bet * $k;

                database::instance()->begin();
                try {
                    $sql='UPDATE users SET
                    bonus=bonus+:bonus,
                    sum_bonus=sum_bonus+:bonus,
                    bonusbreak=bonusbreak+:bonusbreak
                    WHERE id=:uid';

                    db::query(1,$sql)
                        ->param(':bonus', $bonus)
                        ->param(':bonusbreak', $bonus * $bonus_code->vager)
                        ->param(':uid', auth::$user_id)
                        ->execute();

                    $bonus_model = new Model_Bonus();
                    $bonus_model->user_id = auth::$user_id;
                    $bonus_model->bonus = $bonus;
                    $bonus_model->share_prize = $bonus_code->share_prize;
                    $bonus_model->type = $bonus_code->type;
                    $bonus_model->payed = 1;
                    $bonus_model->log = json_encode([
                        "bonus_name" => $bonus_code->name,
                        "id" => $bonus_code->id,
                        "code_type" => $bonus_code->type,
                        "bonus" => $bonus,
                    ]);
                    $bonus_model->save();

                    $spins->payed = 1;
                    $spins->save();

                    database::instance()->commit();
                } catch (Database_Exception $e) {
                    database::instance()->rollback();
                }
            }
        };

        switch ($type) {
            case 'now':
                $freepins->active = 1;
                $set_in_bonuses();
                break;
            case 'off':
                $freepins->active = 0;
                $freepins->freespins_current = 0;
                $freepins->freespins_break = 0;
                $set_in_bonuses(-1);
                break;
            default :
                $freepins->active = -1;
                $set_in_bonuses();
        }

        $freepins->save();

        $this->response->body(json_encode($ans));
    }

    public function action_bonustemp() {
        $code_id = $this->request->post('code_id');

        $ans = auth::user()->enable_bonus_code($code_id);

        $this->response->body(json_encode($ans));
    }

    public function action_messages() {
        $view = new View('site/popup/messages');
        $view->messages = auth::user()->messages();

        $this->response->body($view->render());
    }

    public function action_deletemess() {
        $message_id = intval($this->request->param('id'));
        $ans = [
            'error' => 1,
            'text' => __('Ошибка при удалении сообщения'),
            'count_new_messages' => auth::user()->count_new_messages(),
        ];

        $message = new Model_User_Message([
            'user_id' => auth::user()->parent_id,
            'id' => $message_id,
        ]);

        if($message->loaded()) {
            $message->show = 0;
            /*
             * если сообщение не помечалось как прочитанное
             * и пользователь сразу удаляет его, то ставим время удаления
             */
            if(!$message->time_read) {
                $message->time_read = time();
            }
            $message->save();

            $ans = [
                'error' => 0,
                'text' => __('Сообщение удалено'),
                'count_new_messages' => auth::user()->count_new_messages(),
            ];
        }

        $this->response->body(json_encode($ans));
    }

    public function action_readmess() {
        $message_id = intval($this->request->param('id'));
        $ans = [
            'error' => 1,
            'text' => __('Ошибка при чтении сообщения'),
            'count_new_messages' => auth::user()->count_new_messages(),
        ];

        $message = new Model_User_Message([
            'user_id' => auth::user()->parent_id,
            'id' => $message_id,
        ]);

        if($message->loaded()) {
            $message->time_read = time();
            $message->save();

            $ans = [
                'error' => 0,
                'text' => __('Сообщение прочитано'),
                'count_new_messages' => auth::user()->count_new_messages(),
            ];
        }

        $this->response->body(json_encode($ans));
    }

    public function action_readallmess() {
        $ans = [
            'error' => 0,
            'text' => __('Сообщения отмеченны как прочитанные'),
            'count_new_messages' => 0,
        ];

        $sql_read = <<<SQL
            Update user_messages set time_read = :time_read
            Where user_id = :user_id
SQL;
        db::query(3, $sql_read)->parameters([
            ':time_read' => time(),
            ':user_id' => auth::user()->parent_id,
        ])->execute();

        $this->response->body(json_encode($ans));
    }

    public function action_subscribe() {
        $endpoint = arr::get($_POST,'url');
        $subscriber_id = th::get_subscriber_for_push($endpoint);

        $find_browser = false;
        $urls = [
            'chrome' => 'https://android.googleapis.com/gcm/send/',
            'firefox' => 'https://updates.push.services.mozilla.com/wpush/v1/'
        ];

        $ans = [
            'response' => 'error',
            'id' => '',
            'user_id' => auth::user()->parent_id,
        ];

        foreach ($urls as $browser => $url) {
            if (strpos($endpoint, $url) !== false) {
                $find_browser = $browser;
                break;
            }
        }

        if ($find_browser AND auth::$user_id) {
            $pushtoken = new Model_User_Pushtoken([
                'browser' => $find_browser,
                'token' => $subscriber_id,
            ]);

            if(!$pushtoken->loaded()) {
                $pushtoken->user_id = auth::user()->parent_id;
                $pushtoken->browser = $find_browser;
                $pushtoken->token = $subscriber_id;
                $pushtoken->save();

                $ans['response'] = 'ok';
                $ans['id'] = $subscriber_id;
            }
        }

        $this->response->body(json_encode($ans));
    }

}
