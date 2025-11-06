<?php

class Model_Share extends ORM
{

    protected $_created_column    = ['column' => 'created','format' => true];
    protected $_serialize_columns = ['rules'];

    protected $_table_columns = [
            'id' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'id',
                'column_default' => 'nextval("shares_id_seq"::regclass)',
                'is_nullable' => '',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'name' =>
            [
                'type' => 'string',
                'column_name' => 'name',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'title' =>
            [
                'type' => 'string',
                'column_name' => 'title',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'text' =>
            [
                'type' => 'string',
                'column_name' => 'text',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'description' =>
            [
                'type' => 'string',
                'column_name' => 'description',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'rules' =>
            [
                'type' => 'string',
                'column_name' => 'rules',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'text',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'time_from' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'time_from',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'time_to' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'time_to',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'image' =>
            [
                'type' => 'string',
                'column_name' => 'image',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '255',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'enabled' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'enabled',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'created' =>
            [
                'type' => 'int',
                'min' => '-2147483648',
                'max' => '2147483647',
                'column_name' => 'created',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'integer',
                'character_maximum_length' => '',
                'numeric_precision' => '32',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'prize' =>
            [
                'type' => 'string',
                'column_name' => 'prize',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '100',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'slider_img' =>
            [
                'type' => 'string',
                'column_name' => 'slider_img',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'email_img' =>
            [
                'type' => 'string',
                'column_name' => 'email_img',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'notification' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'notification',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'email_text' =>
            [
                'type' => 'string',
                'column_name' => 'email_text',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'url' =>
            [
                'type' => 'string',
                'column_name' => 'url',
                'column_default' => '""::character varying',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'type' =>
            [
                'type' => 'string',
                'column_name' => 'type',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '15',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'slider_title' =>
            [
                'type' => 'string',
                'column_name' => 'slider_title',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '50',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'slider_text' =>
            [
                'type' => 'string',
                'column_name' => 'slider_text',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '100',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'theme' =>
            [
                'type' => 'string',
                'column_name' => 'theme',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '15',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'subject' =>
            [
                'type' => 'string',
                'column_name' => 'subject',
                'column_default' => '',
                'is_nullable' => '1',
                'data_type' => 'character varying',
                'character_maximum_length' => '',
                'numeric_precision' => '',
                'numeric_scale' => '',
                'datetime_precision' => '',
            ],
            'ready' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'ready',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'send_test' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'send_test',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
            'calc' =>
            [
                'type' => 'int',
                'min' => '-32768',
                'max' => '32767',
                'column_name' => 'calc',
                'column_default' => '0',
                'is_nullable' => '1',
                'data_type' => 'smallint',
                'character_maximum_length' => '',
                'numeric_precision' => '16',
                'numeric_scale' => '0',
                'datetime_precision' => '',
            ],
        ];

    protected $_layout_image      = '/uploads/mail/logo.png';
    protected static $translates  = null;
    protected $_has_many = [
            'tournament_games' => [
                    'model' => 'tournament_game',
                    'foreign_key' => 'share_id',
            ],
            'tournament_prizes' => [
                    'model' => 'tournament_prize',
                    'foreign_key' => 'share_id',
            ],
            'tournament_results' => [
                    'model' => 'sharewinners',
                    'foreign_key' => 'share_id',
            ],
            'share_langs' => [
                    'model' => 'sharelangs',
                    'foreign_key' => 'share_id',
            ],
    ];

    public function unserialize($field)
    {
        return json_decode($field,TRUE);
    }



    public function labels()
    {
        return [
                'name' => 'Название',
                'rules' => 'Правила',
                'title' => 'Заголовок',
                'time_from' => 'Дата начала',
                'time_to' => 'Дата окончания',
                'text' => 'Текст (например: приз за 1 место)',
                'description' => 'Описание',
                'image' => 'Картинка (в акциях) 284*211',
                'enabled' => 'Отображать на сайте?',
                'created' => 'Дата создания',
                'prize' => 'Главный приз (или приз для поля "текст")',
                'slider_img' => 'Картинка (в слайдер) 1000*275',
                'email_img' => 'Картинка (в письмо) 800*500',
                'notification' => 'Отправляли рассылку?',
                'ready' => 'Готово к отправке?',
                'send_test' => 'Отправить тест после сохранения',
                'email_text' => 'Текст письма',
                'type' => 'Тип(lottery - лотерея, bonuses - рассылка/акция, unreg - рассылка на почты)',
                'slider_title' => 'Заголовок в слайдере',
                'slider_text' => 'Текст в слайдере',
                'url' => 'Метка в кнопку письма(пример "?fr=share")',
                'theme' => 'Тема сайта(0 - default)',
                'tournament_games' => 'Игры участвующие в турнире',
                'tournament_prizes' => 'Призы',
                'subject' => 'Тема письма',
        ];
    }

    public function rules()
    {
        return array(
                'name' => array(
                        array('not_empty'),
                ),
        );
    }

    public function get_winners()
    {
        $ans = [];

        $sql_winners = <<<SQL
            Select sh.user_name as name, sh.prize, sh.place, sh.loss_prize
            From share_winners sh
            Where
                sh.share_id = :share_id
            Order by place
SQL;
        $res         = db::query(1,$sql_winners)->param(':share_id',$this->id)->execute()->as_array();

        foreach($res as $r)
        {
            $ans[] = [
                    'place' => $r['place'],
                    'loss_prize' => $r['loss_prize'],
                    'name' => th::hidename($r['name']),
                    'prize' => $r['prize']
            ];
        }

        return $ans;
    }

    public function pay_money($user_id,$sum_win)
    {
        $u = new Model_User($user_id);

        $share_winner = new Model_Sharewinners([
                'user_id' => $user_id,
                'share_id' => $this->id
        ]);

        if($u->loaded() AND $this->time_to < time() AND ! $share_winner->loaded())
        {
            database::instance()->begin();
            try
            {
                /*
                 * рендерим письмо о выигрыше
                 */
                $html      = <<<HTML
                    <tr>
                        <td align="center" style="max-width: 680px; padding-bottom: 10px; color: #3C6C98; font-size: 26px; font-weight: bold; line-height: 1.15; text-align: center;" >
                            <?php echo __('Вулкан приветствует Вас!') ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-left: 20px; padding-right: 20px; padding-bottom: 15px; color: #334D5E; font-size: 14px; font-weight: normal; line-height: 1.25; text-align: center" >
                            <p>
                                <?php echo __('С ') ?><?php echo date('d/m/Y', $share->time_from) ?> до <?php echo date('d/m/Y', $share->time_to) ?> <?php echo __('проводилась лотерея') ?> <?php echo !empty($sname=$share->share_langs->where('lang', '=',$u->lang)->find()->name) ? $sname: $share->name ?><br>
                            </p>
                            <p>
                                <?php echo __('Вы выиграли ') ?><?php echo $share->amount ?> RUB<br>
                            </p>
                        </td>
                    </tr>
HTML;
                $message   = Email::render($html,['share' => $this],[
                                'nospam_email' => $u->email,
                                'domain' => $u->get_domain(),
                                'name' => !empty($sname         = $this->share_langs->where('lang','=',$u->lang)->find()->name) ? $sname : $this->name,
                                'image' => $this->_layout_image,
                                'button' => [
                                        'text' => __('ПОЛУЧИТЬ ВЫИГРЫШ'),
                                        'link' => $u->get_domain()
                                ],
                                ]
                );
                /*
                 * начисляем на счет
                 */
                $u->amount += $this->amount;
                $u->save();

                /*
                 * делаем запись о победителе
                 */
                $share_winner           = new Model_Sharewinners();
                $share_winner->user_id  = $user_id;
                $share_winner->share_id = $this->id;
                $share_winner->sum_win  = $this->amount;
                $share_winner->save();

                email::stack($u->email,email::from($u->dsrc),__('Вы выиграли в лотерее'),$message,true,$u->dsrc,0,'shwin'.$this->id);

                database::instance()->commit();
            }
            catch(Database_Exception $e)
            {
                database::instance()->rollback();
            }
        }
    }

    public function loss_notification($users)
    {
        $games_config = kohana::$config->load('games');

        $games = [
                [
                        'name' => $games_config['igrosoft']['keks']['visible_name'],
                        'image' => $games_config['igrosoft']['keks']['image'],
                ],
                [
                        'name' => $games_config['novomatic']['luckyladycharmd']['visible_name'],
                        'image' => $games_config['novomatic']['luckyladycharmd']['image'],
                ],
                [
                        'name' => $games_config['novomatic']['dolphinsd']['visible_name'],
                        'image' => $games_config['novomatic']['dolphinsd']['image'],
                ],
        ];

        foreach($users as $user_id)
        {
            $child_acc = new Model_User($user_id);
            $u         = $child_acc->parent_acc();
            I18n::lang($u->lang);

            $type = __('проводилась лотерея');

            if($this->type=='tournament') {
                $type = __('проводился турнир');
            }

            $text = __('С') . ' ' . date('d/m/Y',$this->time_from) . ' ' . __('до') . ' ' . date('d/m/Y',$this->time_to) . ' '
                    . $type . ' ' . $this->translate('name',$u->lang) . ' ';
            $text .= __('Рекомендуем начать с этих:');

            $type = __('лотерее');

            $types = [
                    'lottery' => [
                            'type' => __('лотерее'),
                    ],
                    'tournament' => [
                            'type' => __('турнире'),
                    ],
            ];

            foreach($types as $k => $v)
            {
                if($this->type == $k)
                {
                    $type = $v['type'];
                }
            }

            $message = Email::render('email/lottery_loss',[
                    'games' => $games,
                    'domain' => $u->get_domain(),
                    'share' => $this,
                    'text' => $text,
                    'subject' => $this->translate('subject',$u->lang),
                ],[
                    'name' => $this->translate('name',$u->lang),
                    'nospam_email' => $u->email,
                    'domain' => $u->get_domain(),
                    'image' => $this->_layout_image,
                    'button' => [
                            'text' => __('ВЫИГРАТЬ БЫСТРО'),
                            'link' => $u->get_domain()
                    ],
                ]
            );

            email::stack($u->email,email::from($u->dsrc),__("Участие в $type"),$message,true,$u->dsrc,0,'shloss'.$this->id);
            $u->new_message([
                    "user_id" => $u->id,
                    "title" => __("ДОРОГОЙ ИГРОК!"),
                    "text" => __("Вы участвовали в $type ") . $this->translate('name',$u->lang) . "." . __("Вам не повезло, но вы можете выиграть в любой игре нашего клуба. ")
                    . __("При пополнении счета вы можете воспользоваться бонус кодами: kod150(150% на депозит), kod200(200% на депозит), ")
                    . __("kod300(300% на депозит) на минимальные суммы пополнений 1500, 5000 и 30000 рублей соответственно.")
            ]);

            if($tokens = $u->pushtokens->find_all())
            {
                foreach($tokens as $data)
                {
                    $u->new_message([
                            "user_id" => $u->id,
                            "push" => 1,
                            "title" => __("ДОРОГОЙ ИГРОК!"),
                            "text" => __("Вы участвовали в $type ") . $this->translate('name',$u->lang) . "." . __("Вам не повезло, но вы можете выиграть в любой игре нашего клуба. ")
                            . __("При пополнении счета вы можете воспользоваться бонус кодами: kod150(150% на депозит), kod200(200% на депозит), ")
                            . __("kod300(300% на депозит) на минимальные суммы пополнений 1500, 5000 и 30000 рублей соответственно."),
                            "push_link" => '/',
                            "browser" => $data->browser,
                            "push_token" => $data->token,
                    ]);
                }
            }
        }
    }

    public function winner_notification()
    {
        $sql = <<<SQL
            Select user_id, prize
            From share_winners
            where share_id = :id
                AND user_id is not null
                AND loss_prize = 0
SQL;

        if($this->type != 'lottery')
        {
            $sql .= 'AND place <= :count_places';
        }

        $count_places = orm::factory('tournament_prize')->where('share_id','=',$this->id)->count_all();
        $res_users    = db::query(1,$sql)->param(':id',$this->id)->param(':count_places',$count_places)->execute()->as_array();

        foreach($res_users as $user)
        {
            $child_acc = new Model_User($user['user_id']);
            $u         = $child_acc->parent_acc();
            I18n::lang($u->lang);

            $text = __('С') . ' ' . date('d/m/Y',$this->time_from) . ' ' . __('до') . ' ' . date('d/m/Y',$this->time_to) . ' ';

            $type = __('лотерее');

            $types = [
                    'lottery' => [
                            'type' => __('лотерее'),
                            'text' => __('проводилась лотерея'),
                    ],
                    'tournament' => [
                            'type' => __('турнире'),
                            'text' => __('проводился турнир'),
                    ],
            ];

            foreach($types as $k => $v)
            {
                if($this->type == $k)
                {
                    $type = $v['type'];
                    $text .= $v['text'];
                }
            }

            $text .= ' ';
            $text .= $this->translate('name',$u->lang);

            $message = Email::render('email/lottery_winner',[
                            'text' => $text,
                            'prize' => __($user['prize']),
                            'domain' => $u->get_domain(),
                            'subject' => $this->translate('subject',$u->lang),
                            ],[
                            'nospam_email' => $u->email,
                            'domain' => $u->get_domain(),
                            'name' => $this->translate('name',$u->lang),
                            'image' => $this->_layout_image,
                            'button' => [
                                    'text' => __('ПОЛУЧИТЬ ПРИЗ'),
                                    'link' => $u->get_domain()
                            ],
                            ]
            );

            email::stack($u->email,email::from($u->dsrc),__("Приз в $type"),$message,true,$u->dsrc,0,'shwin'.$this->id);
            $u->new_message([
                    "user_id" => $u->id,
                    "title" => __("ДОРОГОЙ ИГРОК!"),
                    "text" => __("Поздравляем! Вы выиграли ") . __($user['prize']) . __(" в $type ") . $this->translate('name',$u->lang),
            ]);

            if($tokens = $u->pushtokens->find_all())
            {
                foreach($tokens as $data)
                {
                    $u->new_message([
                            "user_id" => $u->id,
                            "push" => 1,
                            "title" => __("ДОРОГОЙ ИГРОК!"),
                            "text" => __("Поздравляем! Вы выиграли ") . __($user['prize']) . __(" в $type ") . $this->translate('name',$u->lang),
                            "push_link" => '/',
                            "browser" => $data->browser,
                            "push_token" => $data->token,
                    ]);
                }
            }
        }
    }

    public function notification($user_ids = [])
    {
        if($this->notification)
        {
            return true;
        }

        $sql_users = <<<SQL
            Select id
            From users
            Where
                email is not null
                AND
                getspam = 1
                AND
                blocked <> 1
SQL;
        $level     = 0;
        if(!empty($user_ids))
        {
            $sql_users .= ' and id in (' . implode(',',$user_ids) . ') ';
            $level     = 1;
        }

        $users = db::query(1,$sql_users)->execute()->as_array();

        foreach($users as $v)
        {
            $user = new Model_User($v['id']);
            I18n::lang($user->lang);

            $message = Email::render('email/lottery_notification',[
                            'time_from' => date('d/m/Y',$this->time_from),
                            'time_to' => date('d/m/Y',$this->time_to),
                            'name' => $this->translate('name',$user->lang),
                            'title' => $this->translate('title',$user->lang),
                            'email_text' => $this->translate('email_text',$user->lang)
                            ],[
                            'nospam_email' => $user->email,
                            'domain' => $user->get_domain(),
                            'name' => $this->translate('name',$user->lang),
                            'image' => $this->translate('email_img',$user->lang),
                            'button' => [
                                    'text' => __('УЧАСТВОВАТЬ'),
                                    'link' => $user->get_domain() . $this->url
                            ],
                            ]
            );

            email::stack($user->email,email::from($user->dsrc),$this->translate('subject',$user->lang),$message,true,$user->dsrc,$level,UTF8::str_ireplace('?fr=', '', $this->url));

            $type = __('Акция');
            $uri  = '/lottery/select/';

            $types = [
                    'lottery' => [
                            'type' => __('Лотерея'),
                            'uri' => '/lottery/select/' . $this->id,
                    ],
                    'bonuses' => [
                            'type' => __('Акция'),
                            'uri' => '/',
                    ],
                    'tournament' => [
                            'type' => __('Турнир'),
                            'uri' => '/tournaments/info/' . $this->id,
                    ],
            ];
            foreach($types as $k => $v)
            {
                if($this->type == $k)
                {
                    $type = $v['type'];
                    $uri  = $v['uri'];
                }
            }

            if($tokens = $user->pushtokens->find_all())
            {
                foreach($tokens as $data)
                {
                    $user->new_message([
                            "user_id" => $user->id,
                            "push" => 1,
                            "title" => __("Внимание! ") . $type . "!",
                            "text" => $this->translate('name',$user->lang),
                            "push_link" => $uri,
                            "browser" => $data->browser,
                            "push_token" => $data->token,
                    ]);
                }
            }

            $href = $this->enabled ? ", <a href='{$uri}'>подробнее</a>" : "";

            $user->new_message([
                    "user_id" => $user->id,
                    "title" => __("Внимание! ") . $type . "!",
                    "text" => __("В данный момент проходит ") . strtolower($type) . " - " . $this->translate('name',$user->lang) . $href,
            ]);
        }

        $this->notification = 1;
        $this->save();
    }

    public function notification_unreg($user_emails = [])
    {
        if($this->notification)
        {
            return true;
        }

        $level     = 1;
        $test=1;
        if(empty($user_emails))
        {
            $test=0;
            $level     = 0;
            $sql_users = <<<SQL
                Select id,name,email from users_unreg where sent=0
SQL;
            $user_emails = db::query(1,$sql_users)->execute()->as_array('email');
        }

        $dom = dd::get_domain(THEME);

        foreach($user_emails as $email => $aem)
        {
            $message = Email::render('email/lottery_notification',[
                            'time_from' => date('d/m/Y',$this->time_from),
                            'time_to' => date('d/m/Y',$this->time_to),
                            'name' => $this->name,
                            'title' => $this->title,
                            'email_text' => $this->email_text
                            ],[
                            'nospam_email' => $email,
                            'domain' => $dom,
                            'name' => $this->name,
                            'image' => $this->email_img,
                            'button' => [
                                    'text' => __('УЧАСТВОВАТЬ'),
                                    'link' => $dom . $this->url
                            ],
                            ]
            );
            email::stack($email,email::from(dd::email_config_name($dom)),$this->subject,$message,true,dd::email_config_name($dom),$level,UTF8::str_ireplace('?fr=', '', $this->url));
            if(!$test) {
                db::query(Database::UPDATE,'update users_unreg set sent=1 where id=:id')->param(':id',$aem['id'])->execute();
            }
        }

        $this->notification = 1;
        $this->save();
    }

    public function lastShare()
    {
        $sql = <<<SQL
                select id, name, image
                from shares
                where time_to >= :time
                    AND enabled = 1
                    AND theme = :current_theme
                    AND type in :types
                order by time_from
                limit 1;
SQL;

        $shrs = db::query(1,$sql)->parameters([
                        ':time' => time(),
                        ':current_theme' => THEME,
                        ':types' => ['lottery','bonuses'],
                ])->execute()->as_array();

        return $shrs;
    }

    public function nodep_notification()
    {
        if($this->notification)
        {
            return true;
        }

        $sql       = <<<SQL
            Select id
            From users
            Where
                email is not null
                AND
                last_drop = 0
                AND
                getspam = 1
                AND
                blocked <> 1
SQL;
        $res_users = db::query(1,$sql)->param(':id',$this->id)->execute()->as_array();

        foreach($res_users as $user)
        {

            $u = new Model_User($user['id']);

            $message = Email::render('email/user_nodep',[
                            'u' => $u,
                            'email_text' => !empty($setext      = $this->share_langs->where('lang','=',$u->lang)->find()->email_text) ? $setext : $this->email_text,
                            ],[
                            'nospam_email' => $u->email,
                            'domain' => $u->get_domain(),
                            'name' => !empty($sname         = $this->share_langs->where('lang','=',$u->lang)->find()->name) ? $sname : $this->name,
                            'image' => $this->_layout_image,
                            'button' => [
                                    'text' => __('ВЫИГРАТЬ БЫСТРО'),
                                    'link' => $u->get_domain()
                            ],
                            ]
            );

            email::stack($u->email,email::from($u->dsrc),!empty($sname = $this->share_langs->where('lang','=',$u->lang)->find()->name) ? $sname : $this->name,$message,true,$u->dsrc,0,UTF8::str_ireplace('?fr=', '', $this->url));
            $u->new_message([
                    "user_id" => $u->id,
                    "title" => __("ДОРОГОЙ ИГРОК!"),
                    "text" => !empty($setext   = $this->share_langs->where('lang','=',$u->lang)->find()->email_text) ? $setext : $this->email_text,
            ]);
        }

        $this->notification = 1;
        $this->save();
    }

    public function calc_tournament_winners()
    {
        $prizes       = $this->tournament_prizes->order_by('place')->find_all();
        $results      = $this->tournament_results->order_by('count_points','desc')->find_all();//->limit(count($prizes))
        $count_places = $this->tournament_prizes->count_all();

        $iter = 1;
        foreach($results as $res)
        {
            $res->place = $iter;
            foreach($prizes as $prize)
            {
                if($prize->place == $iter)
                {
                    /*
                     * todo Дописать начисление для быстрых турниров
                     */
//                    if($prize->user_id) {
//                        $bonus = new Model_Bonus_Code(['id' => $prize->code_id]);
//                        $bonus->pay($res->user_id);
//                    }
                    $res->prize = $prize->prize;
                }
            }

            if($res->place > $count_places)
            {
                $res->prize = null;
            }

            $res->save();
            $iter++;
        }
    }

    public function loss_prizes($users)
    {
        foreach($users as $user)
        {
            $child_acc = new Model_User($user['user_id']);
            $u         = $child_acc->parent_acc();
            I18n::lang($u->lang);

            $type = __('лотерее');

            $text = __('С') . ' ' . date('d/m/Y',$this->time_from) . ' ' . __('до') . ' ' . date('d/m/Y',$this->time_to) . ' ';

            $types = [
                    'lottery' => [
                            'type' => __('лотерее'),
                            'text' => __('проводилась лотерея'),
                    ],
                    'tournament' => [
                            'type' => __('турнире'),
                            'text' => __('проводился турнир'),
                    ],
            ];

            foreach($types as $k => $v)
            {
                if($this->type == $k)
                {
                    $type = $v['type'];
                    $text .= $v['text'];
                }
            }

            $text .= ' ';
            $text .= $this->translate('name',$u->lang);

            $message = Email::render('email/share_loss_prize',[
                            'text' => $text,
                            'prize' => $user['prize'],
                            'domain' => $u->get_domain(),
                            'subject' => $this->translate('subject',$u->lang)
                            ],[
                            'nospam_email' => $u->email,
                            'domain' => $u->get_domain(),
                            'name' => $this->translate('name',$u->lang),
                            'image' => $this->_layout_image,
                            'button' => [
                                    'text' => __('ПОЛУЧИТЬ ПРИЗ'),
                                    'link' => $u->get_domain()
                            ],
                            ]
            );

            email::stack($u->email,email::from($u->dsrc),__("Утешительный приз в $type"),$message,true,$u->dsrc,0,'loss'.$this->id);
            $u->new_message([
                    "user_id" => $u->id,
                    "title" => __("ДОРОГОЙ ИГРОК!"),
                    "text" => __("Вы получаете утешительный приз ") . $user['prize'] . __(" в $type ") . $this->translate('name',$u->lang),
            ]);

            if($tokens = $u->pushtokens->find_all())
            {
                foreach($tokens as $data)
                {
                    $u->new_message([
                            "user_id" => $u->id,
                            "push" => 1,
                            "title" => __("ДОРОГОЙ ИГРОК!"),
                            "text" => __("Вы получаете утешительный приз ") . $user['prize'] . __(" в $type ") . $this->translate('name',$u->lang),
                            "push_link" => '/',
                            "browser" => $data->browser,
                            "push_token" => $data->token,
                    ]);
                }
            }
        }
    }

    /*
     * обертка для перевода поля акции
     * на вход передаем поле которое нужно перевести
     */

    public function translate($field,$lang = null)
    {
        if(!$lang)
        {
            $lang = 'ru';
        }

        if(!isset(self::$translates[$this->id][$lang]))
        {
            self::$translates[$this->id][$lang] = null;

            foreach($this->share_langs->find_all() as $share)
            {
                self::$translates[$this->id][$share->lang] = $share;
            }
        }

        $text = (isset(self::$translates[$this->id][$lang]) AND ! empty(self::$translates[$this->id][$lang]->$field)) ? self::$translates[$this->id][$lang]->$field : $this->$field;

        return $text;
    }

}
