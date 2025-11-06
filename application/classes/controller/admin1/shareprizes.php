<?php

class Controller_Admin_Shareprizes extends Controller_Admin_Base
{

    public function action_index()
    {
        $view = new View('admin/shareprizes/index');

        $view->headers = [
                'id'   => 'ID',
                'type' => 'Тип',
                'name' => 'Название',
                'time_to' => 'Окончание',
        ];
        $view->shares  = orm::factory('share')->where('type','in',['lottery','tournament'])->order_by('id')->find_all();
        $view->dir = $this->dir;
        $this->template->content = $view;
    }

    public function action_item()
    {
        $id = $this->request->param('id');

        $calc_prizes           = arr::get($_GET,'calc_prizes');
        $calc_and_notification = arr::get($_GET,'notification');

        $share = new Model_Share($id);

        if(!$share->loaded())
        {
            throw new HTTP_Exception_404;
        }

        if($calc_prizes AND $share->type == 'tournament')
        {
            $share->calc_tournament_winners();
            $this->request->redirect($this->dir.'/shareprizes/item/' . $id);
        }

        if($calc_and_notification AND ! $share->calc)
        {
            $loss_prizes_users = [];
            $loss_users        = [];

            if($share->type == 'lottery')
            {
                $loss_prizes_users = [];
                $loss_users        = [];

                $winners = orm::factory('sharewinners')->where('share_id','=',$id)->and_where('payed','=',0)->find_all();

                foreach($winners as $winner)
                {
                    $winner_code   = new Model_Bonus_Code(['id' => $winner->code_id]);
                    $winner_code->use_code($winner->user_id);
                    $winner->payed = 1;
                    $winner->save();

                    if($winner->loss_prize)
                    {
                        $loss_prizes_users[] = [
                                'user_id' => $winner->user_id,
                                'prize'   => $winner->prize,
                        ];
                    }
                }

                $sql_loss = <<<SQL
                    Select DISTINCT st.user_id
                    From share_tickets st JOIN users u ON st.user_id = u.id
                    Where
                        st.share_id = :id
                        AND
                        u.getspam = 1
                        AND user_id not in (
                            Select user_id
                            From share_winners
                            Where share_id = :id
                                AND (
                                    place is not null
                                    OR
                                    loss_prize = 1
                                )
                        )
SQL;
                foreach(db::query(1,$sql_loss)->param(':id',$share->id)->execute() as $v)
                {
                    $loss_users[] = $v['user_id'];
                }

                $share->loss_prizes($loss_prizes_users);
                $share->loss_notification($loss_users);
                $share->winner_notification();
            }
            else
            {
                $winners = orm::factory('sharewinners')->where('share_id','=',$id)->and_where('payed','=',0)->find_all();

                foreach($winners as $winner)
                {
                    $winner_code   = new Model_Bonus_Code(['id' => $winner->code_id]);
                    $winner_code->use_code($winner->user_id);
                    $winner->payed = 1;
                    $winner->save();

                    if($winner->loss_prize)
                    {
                        $loss_prizes_users[] = [
                                'user_id' => $winner->user_id,
                                'prize'   => $winner->prize,
                        ];
                    }
                }

                $sql_loss = <<<SQL
                    Select sw.user_id
                    From share_winners sw JOIN users u ON sw.user_id = u.id
                    Where user_id is not null
                        AND sw.code_id is null
                        AND sw.share_id = :id
SQL;
                foreach(db::query(1,$sql_loss)->param(':id',$share->id)->execute() as $v)
                {
                    $loss_users[] = $v['user_id'];
                }
            }

            $share->loss_prizes($loss_prizes_users);
            $share->loss_notification($loss_users);
            $share->winner_notification();

            $share->calc = 1;
            $share->save()->reload();

            $this->request->redirect($this->dir . '/shareprizes/item/' . $id);
        }

        $view = new View('admin/shareprizes/item');
        $view->dir = $this->dir;
        if($this->request->method() == 'POST' AND ! $share->calc)
        {

            $user_prizes = arr::get($_POST,'user',[]);

            foreach($user_prizes as $user_id => $v)
            {
                $user = new Model_User($user_id);

                if(isset($v['code_id']) AND isset($v['prize']) AND isset($v['place']))
                {
                    $code = new Model_Bonus_Code(['id' => $v['code_id']]);

                    if($user->loaded() AND $code->loaded())
                    {
                        $winner = new Model_Sharewinners([
                                'share_id' => $id,
                                'user_id'  => $user_id,
                        ]);

                        if(!$winner->loaded())
                        {
                            $winner->share_id  = $id;
                            $winner->user_id   = $user_id;
                            $winner->user_name = $user->name;
                            $winner->payed     = 0;
                        }
                        $winner->place      = intval($v['place']) ? intval($v['place']) : null;
                        $winner->prize      = $v['prize'];
                        $winner->code_id    = $v['code_id'];
                        $winner->loss_prize = $v['loss_prize'] ?? 0;

                        $winner->save();
                    }
                }
            }
        }

        $sql_stat = '';

        if($share->type == 'tournament')
        {
            $sql_stat .= <<<SQL
                Select sw.user_id, up.email, coalesce(up.email,up.name) as name, u.sum_in-u.sum_out as bets,
                    sum(
                        CASE
                            when payments.amount>0 then payments.amount
                            else 0
                        END
                    ) as in,
                    sum(
                        CASE
                            when payments.amount<0 then payments.amount
                            else 0
                        END
                    ) as out,
                    sum(payments.amount) as all_payments
                From share_winners sw JOIN users u ON sw.user_id = u.id
                    JOIN payments ON u.id = payments.user_id
                    JOIN users up ON u.parent_id = up.id
                Where
                    payments.status in (20,30)
                    AND payments.payed >= :time_from
                    AND payments.payed <= :time_to
                    AND sw.share_id = :share_id
                GROUP BY 1,2,3,4

                UNION

                (Select sw.user_id, up.email, coalesce(up.email,up.name) as name, u.sum_in-u.sum_out as bets,
                    0 as in, 0 as out, 0 as all_payments
                From share_winners sw JOIN users u ON sw.user_id = u.id
                    JOIN users up ON u.parent_id = up.id
                Where sw.share_id = :share_id
                    AND sw.place <= :places
                GROUP BY 1,2,3,4)
                    
SQL;
        } else { 
            $sql_stat .= <<<SQL
                Select user_id, up.email, coalesce(up.email,up.name) as name, u.sum_in-u.sum_out as bets,
                    sum(
                        CASE
                            when payments.amount>0 then payments.amount
                            else 0
                        END
                    ) as in,
                    sum(
                        CASE
                            when payments.amount<0 then payments.amount
                            else 0
                        END
                    ) as out,
                    sum(payments.amount) as all_payments
                From payments join users u on u.id = payments.user_id
                    JOIN users up ON u.parent_id = up.id
                Where payments.status in (20,30)
                    AND payments.payed >= :time_from
                    AND payments.payed <= :time_to
                    AND payments.user_id in (
                        Select distinct user_id
                        From share_tickets
                        Where share_id = :share_id
                    )
                GROUP BY 1,2,3,4
SQL;
        }

        $sql_stat .= <<<SQL
            ORDER BY 7 desc
SQL;

        $stat = db::query(1,$sql_stat)->parameters([
            ':time_from' => $share->time_from,
            ':time_to'   => $share->time_to,
            ':share_id'  => $share->id,
            ':places'    => $share->tournament_prizes->count_all(),
        ])->execute()->as_array('user_id');

        $order  = 'count_points';
        $direct = 'desc';
        if($share->type != 'tournament')
        {
            $order  = 'place';
            $direct = 'asc';
        }
        $share_winners = orm::factory('sharewinners')->where('share_id','=',$id)->order_by($order,$direct)->find_all();

        $headers_stat = [
                'user_id'      => 'ID пользователя',
//            'email' => 'Почта',
                'name'         => 'Логин',
//            'visible_name' => 'Видимое имя',
                'bets'         => 'IN - OUT (за все время)',
                'in'           => 'Сумма депозитов(за период акции)',
                'out'          => 'Сумма выплат(за период акции)',
                'all_payments' => 'IN - OUT - баланс на конец периода акции',
                'place'        => 'Место',
                'payed'        => 'Давали приз?',
                'prize'        => 'Приз',
                'bonus_code'   => 'Применить бонус код',
                'count_points' => 'Количество очков',
                'loss_prize'   => 'Утешительный приз',
        ];


        $headers_bots = [
                'user_name'    => 'Имя бота',
                'count_points' => 'Количество очков',
                'place'        => 'Место',
                'prize'        => 'Приз',
        ];

        if($share->type != 'tournament')
        {
            unset($headers_stat['count_points']);
            unset($headers_bots['count_points']);
        }

        $view->headers_stat = $headers_stat;
        $view->headers_bots = $headers_bots;

        $bots = [];

        $sql_balances = <<<SQL
            Select user_id, balance
            From (
                Select ub.date, ub.balance, ub.user_id,
                    rank() OVER (PARTITION BY user_id ORDER BY date DESC) as rank
                From users_balances ub
                Where
                    user_id IN (
                        select distinct user_id
                        From share_winners
                        Where share_id = :share_id
                    )
                    AND ub.date <= :time_to
            ) as t
            Where rank=1
SQL;

        $balances = db::query(1,$sql_balances)->parameters([
            ':time_from' => $share->time_from,
            ':time_to'   => $share->time_to,
            ':share_id'  => $share->id
        ])->execute()->as_array('user_id');

        foreach($share_winners as $sw)
        {
            if(is_null($sw->user_id))
            {
                $bots[] = [
                        'user_name'    => $sw->user_name,
                        'place'        => $sw->place,
                        'prize'        => $sw->prize,
                        'count_points' => $sw->count_points,
                ];
            }
        }

        foreach($stat as $s)
        {
            $stat[$s['user_id']]['code_id']      = null;
            $stat[$s['user_id']]['loss_prize']   = 0;
            $stat[$s['user_id']]['all_payments'] -= $balances[$s['user_id']]['balance'] ?? 0;

            foreach($share_winners as $w)
            {
                if($s['user_id'] == $w->user_id)
                {
                    if($w->payed)
                    {
                        $stat[$s['user_id']]['payed'] = 'Да';
                    }
                    if($share->type == 'tournament')
                    {
                        $stat[$s['user_id']]['count_points'] = $w->count_points;
                    }

                    $stat[$s['user_id']]['prize']      = $w->prize;
                    $stat[$s['user_id']]['place']      = $w->place;
                    $stat[$s['user_id']]['code_id']    = $w->code_id;
                    $stat[$s['user_id']]['loss_prize'] = $w->loss_prize;
                }
            }
        }

        $bonus_codes = [0 => ' - '];
        $codes       = orm::factory('bonus_code')
                        ->where('type','in',['freespin','fixed'])
                        ->and_where('time','>',time())
                        ->order_by('created','desc')->limit(9)->find_all();

        foreach($codes as $code)
        {
            $name                   = $code->name . ' тип: ' . $code->type . ' сумма: ' . $code->bonus;
            $bonus_codes[$code->id] = $name;
        }

        $view->bonus_codes = $bonus_codes;
        $view->stat        = $stat;
        $view->bots        = $bots;
        $view->share       = $share;

        $this->template->content = $view;
    }

    public function action_addbot()
    {
        $this->auto_render = false;

        $share_id   = $this->request->param('id');
        $share_type = ORM::factory('share',$share_id)->type;

        $ans = ['error' => 1,'text' => __('Ошибка при добавлении бота')];

        $place = intval(arr::get($_POST,'place'));
        $prize = arr::get($_POST,'prize');

        if($share_type == 'tournament')
        {
            $count_points = arr::get($_POST,'count_points');
        }

        $bot_name = nic::randomName();

        if($place AND $prize)
        {
            $bot = orm::factory('sharewinners')
                    ->where('share_id','=',$share_id)
                    ->where_open()
                    ->where('user_name','=',$bot_name)
                    ->or_where('place','=',$place)
                    ->where_close()
                    ->find();

            foreach($bot as $v)
            {
                echo debug::vars($v->id);
            }

            if(!$bot->loaded())
            {
                $bot->share_id  = $share_id;
                $bot->user_name = $bot_name;
                $bot->prize     = $prize;
                $bot->place     = $place;

                if($share_type == 'tournament')
                {
                    $bot->count_points = $count_points;
                }

                $bot->save();

                $bot_arr = ['user_name' => $bot_name];
                if($share_type == 'tournament')
                {
                    $bot_arr['count_points'] = $count_points;
                }
                $bot_arr['place'] = $place;
                $bot_arr['prize'] = $prize;

                $ans = [
                        'error' => 0,
                        'text'  => __('Бот добавлен'),
                        'bot'   => $bot_arr
                ];
            }
            else
            {
                $ans['text'] = __('Бот уже существует');
            }
        }

        $this->response->body(json_encode($ans));
    }

    public function action_updatebot()
    {
        $this->auto_render = false;

        $share_id = $this->request->param('id');

        $ans = ['error' => 1,'text' => __('Ошибка при обновлении бота')];

        $place        = intval(arr::get($_POST,'place'));
        $prize        = arr::get($_POST,'prize');
        $count_points = arr::get($_POST,'count_points');
        $bot_name     = arr::get($_POST,'user_name');

        $bot_in_share = new Model_Sharewinners(['user_name' => $bot_name,'share_id' => $share_id]);

        if($bot_in_share->loaded())
        {
            $bot_in_share->prize = $prize;
            $bot_in_share->place = $place;
            if(ORM::factory('share',$share_id)->type == 'tournament')
            {
                $bot_in_share->count_points = $count_points;
            }



            $bot_in_share->save();

            $ans = [
                    'error' => 0,
                    'text'  => __('Бот обновлен'),
                    'bot'   => [
                            'user_name'    => $bot_name,
                            'place'        => $place,
                            'prize'        => $prize,
                            'count_points' => $count_points,
                    ]
            ];
        }

        $this->response->body(json_encode($ans));
    }

    public function action_deletebot()
    {
        $this->auto_render = false;

        $share_id = $this->request->param('id');

        $ans = ['error' => 1,'text' => __('Ошибка при удалении бота')];

        $place        = intval(arr::get($_POST,'place'));
        $prize        = arr::get($_POST,'prize');
        $count_points = arr::get($_POST,'count_points');
        $bot_name     = arr::get($_POST,'user_name');

        $bot_in_share = new Model_Sharewinners(['user_name' => $bot_name,'share_id' => $share_id]);

        if($bot_in_share->loaded())
        {

            $bot_in_share->delete();

            $ans = [
                    'error' => 0,
                    'text'  => __('Бот удален'),
                    'bot'   => [
                            'user_name'    => $bot_name,
                            'place'        => $place,
                            'prize'        => $prize,
                            'count_points' => $count_points,
                    ]
            ];
        }

        $this->response->body(json_encode($ans));
    }

}
