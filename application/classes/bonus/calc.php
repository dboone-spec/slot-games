<?php

class Bonus_Calc {

    private $user;
    protected $coeffs;
    protected $referals;
    protected $cache_time = 2*60;//2 минуты
    protected $time_from;

    public function __construct(int $user_id) {
        $user = new Model_User($user_id);
        if(!$user->loaded()) {
            throw new Exception_Base('User not found');
        }
        $this->user = $user;
        /*
         * вычисляем время для расчета бонуса
         */
        $sql_last_time = <<<SQL
            Select coalesce(max(b.created), 0) as time_from
            From bonuses b
            Where b.user_id = :user_id
                AND type = 'activity'
            UNION
            Select s.value
            From status s
            Where id = 'enable_bia'
            UNION
            Select coalesce(max(created), 0)
            From operations o
            Where o.updated_id = :user_id AND o.amount < 0
SQL;
        $result_time = db::query(database::SELECT, $sql_last_time)->param(':user_id', $user->id)->execute()->as_array();
        $max_time = max($result_time);
        $this->time_from = $max_time['time_from'];
        //необходимо для предыдущей реализации
//        $this->coeffs = kohana::$config->load('bonus.coeffs');

//        $this->get_referals();
    }

    /*
     * получаем рефералы пользователя
     */
    protected function get_referals() {
        $sql_referals = <<<SQL
            Select br.referal_id,
                CASE WHEN br.user_id = :user_id
                     then 1 else 2
                END as level
            From bonus_referals br JOIN users u ON br.referal_id = u.id
            Where (
                user_id in
                (
                    Select referal_id
                    From bonus_referals
                    where user_id = :user_id
                )
                OR br.user_id = :user_id
            ) AND u.last_game is null
SQL;
        $res = db::query(database::SELECT, $sql_referals)->param(':user_id', $this->user->id)->execute()->as_array();

        $this->get_referals_info($res);
    }

    /*
     * получаем данные пользователя
     * и его рефералов
     */
    protected function get_referals_info(array $arr) {
        $data = [
            $this->user->id => [
                'referal_id' => $this->user->id,
                'level' => 0
            ],
        ];
        $data = array_merge($data,$arr);

        $referals = [];

        foreach ($data as $referal) {
            $info_ref = $referal;

            //cумма всех вводов минус сумма всех выводов
            $sql_amount = <<<SQL
                Select coalesce(sum(amount), 0) as sum
                From payments
                Where user_id = :user_id AND status = :status AND created >= :time
SQL;
            $params_amount = [
                ':user_id' => $referal['referal_id'],
                ':status' => PAY_SUCCES,
                ':time' => $this->time_from,
            ];
            $amount = db::query(database::SELECT, $sql_amount)->parameters($params_amount)->execute()->as_array();
            $info_ref['amount'] = $amount[0]['sum'];

            $sql_balance = <<<SQL
                Select coalesce(sum(amount), 0) as sum
                From payments
                Where user_id = :user_id AND status <> :status AND created >= :time AND amount < 0
SQL;
            $balance = db::query(database::SELECT, $sql_balance)->parameters($params_amount)->execute();
            $ref = new Model_User($referal['referal_id']);
            //баланс игрока включая все заблокированные средства
            $info_ref['balance'] = $ref->amount() + abs($balance[0]['sum']);

            //БИА
            $sql_bonuses = <<<SQL
                Select coalesce(sum(bonus), 0) as sum
                From bonuses
                Where user_id = :user_id AND created >= :time AND type = 'activity'
SQL;
            $bonus = db::query(database::SELECT, $sql_bonuses)->param(':user_id', $referal['referal_id'])->param(':time', $this->time_from)->execute()->as_array();
            $info_ref['bonus_prev'] = $bonus[0]['sum'];
            $bonus_base = $info_ref['amount'] - $info_ref['balance'] - $info_ref['bonus_prev'];
            $info_ref['bonus_base'] = $bonus_base>0 ? $bonus_base : 0;

            $referals[$referal['referal_id']] = $info_ref;
        }
        //данные о текущем пользователе и его рефералах
        $this->referals = $referals;
    }

    private function calc() {
        /*
         * считаем бонус который должен получить пользователь
         * от суммы проигранных средств им самим и
         * от суммы проигранных средств своих рефералов
         */
        $total_bonus = 0;

        foreach ($this->referals as $referal) {
            $total_bonus += $referal['bonus_base']*$this->coeffs['z'.$referal['level']];
        }
        if($total_bonus > 0) {
            database::instance()->begin();
            try {
                $sql_insert = <<<SQL
                    Insert into bonuses("user_id", "bonus", "created", "type") VALUES(:user_id, :bonus, :created, :type)
SQL;
                $params_insert = [
                    ':user_id' => $this->user->id,
                    ':bonus' => $total_bonus,
                    ':created' => time(),
                    ':type' => 'activity'
                ];
                db::query(database::SELECT, $sql_insert)->parameters($params_insert)->execute();

                /*
                 * обновляем данные пользователя
                 */
                $this->user->amount += $total_bonus;
                $this->user->last_bonus += $total_bonus;
                $this->user->save();

                database::instance()->commit();
            } catch (Exception $e) {
                database::instance()->rollback();
            }
        }
        return $total_bonus;
    }

    //добавляем бонусы пользователю и пишем в таблицу бонусов
    protected function addBonuses(array $data) {
        database::instance()->begin();
        try {
            $sql_insert = <<<SQL
                Insert into bonuses("user_id", "bonus", "created", "type", "referal_id", "payed", "log", "last_notification")
                VALUES (:user_id, :bonus, :created, :type, :referal_id, :payed, :log, :last_notification)
SQL;
            $params_insert = [
                ':user_id' => $data['user_id'],
                ':referal_id' => $data['referal_id'],
                ':bonus' => $data['bonus'],
                ':created' => time(),
                ':type' => $data['type'],
                ':payed' => $data['payed'],
                ':log' => $data['log'],
                ':last_notification' => time(),
            ];
            db::query(database::SELECT, $sql_insert)->parameters($params_insert)->execute();

            /*
             * обновляем данные пользователя
             */
            $user = new Model_User($data['user_id']);
            $user->last_bonus += $data['bonus'];
            $user->last_bonus_type = $data['type'];

            if($data['payed'] == 1) {
                $user->amount += $data['bonus'];
                $user->sum_bonus += $data['bonus'];
            }
            $user->save();

            database::instance()->commit();
        } catch (Exception $e) {
            echo $e->getMessage();
            database::instance()->rollback();
        }
    }

    /*
     * returning [] вида
     *  [
     *      'bonus' => 100,
     *      'invited' => [
     *          'bonus' => 200,
     *          'user_id' => 123
     *      ],
     *  ]
     */
    public function go() {
        $ans = [
            'bonus' => 0,
            'invited' => [],
        ];

        //cумма всех вводов минус сумма всех выводов
        $sql_amount = <<<SQL
                Select coalesce(sum(amount+bonus), 0) as sum
                From payments
                Where user_id = :user_id AND status = :status AND created > :time
SQL;

        $sql_amount = <<<SQL
                Select coalesce(sum(amount), 0) as sum
                From operations
                Where updated_id = :user_id and created > :time
SQL;

        $params_amount = [
                ':user_id' => $this->user->id,
//                ':status' => PAY_SUCCES,
                ':time' => $this->time_from,
//                ':status_blocked' => PAY_CANCEL,
        ];
        $amount_res = db::query(database::SELECT, $sql_amount)->parameters($params_amount)->execute()->as_array();
        $amount = $amount_res[0]['sum'];

        $sql_balance = <<<SQL
                Select coalesce(sum(amount+bonus), 0) as sum
                From payments
                Where user_id = :user_id AND status not in (:status, :status_blocked)
                    AND created > :time AND amount < 0
SQL;

        $sql_balance = <<<SQL
                Select coalesce(sum(amount), 0) as sum
                From operations
                Where updated_id = :user_id
                    AND created > :time AND amount < 0
SQL;

        $balance_res = db::query(database::SELECT, $sql_balance)->parameters($params_amount)->execute();

        //баланс игрока включая все заблокированные средства
        $balance = $this->user->amount() + abs($balance_res[0]['sum']);
        $balance = $balance>0?$balance:0;

        //БИА
        $sql_bonuses = <<<SQL
                Select coalesce(sum(bonus), 0) as sum
                From bonuses
                Where user_id = :user_id
                    AND created > :time
                    AND type not in :types
SQL;
        $bonus = db::query(database::SELECT, $sql_bonuses)
                ->param(':user_id', $this->user->id)
                ->param(':time', $this->time_from)
                ->param(':types', ['activity', 'referal_activity'])
                ->execute()
                ->as_array();

        $bonus_prev = $bonus[0]['sum'];
        $bonus_base = $amount - $balance - $bonus_prev;

//        $coff = $this->user->cashback();
        $coff = 1;

        $total_bonus = $bonus_base > 0 ? $bonus_base*$coff : 0;
        $ans['bonus'] = $total_bonus;

        if($total_bonus > 0) {
            $data_bonus = [
                'user_id' => $this->user->id,
                'referal_id' => 0,
                'bonus' => $total_bonus,
                'created' => time(),
                'type' => 'activity',
                'payed' => 0,
                'log' => json_encode([
                    'sum_in_out' => $amount,
                    'balance_with_block' => $balance,
                    'bonus_prev' => $bonus_prev,
                    'coeff' => $coff,
                    'bonus_base' => $bonus_base,
                ]),
            ];
            //добавляем бонусы
            $this->addBonuses($data_bonus);

            /*
            * проверяем, есть ли у пользователя рефералы
            * и зарегистрировался ли кто-то
            * в последние 3 месяца по его реф ссылке
            */
            $sql_invited = <<<SQL
                Select coalesce(max(u.created), 0) as time
                From users u JOIN payments p ON u.id = p.user_id
                Where u.invited_by = :id
                     AND u.created > :time_from
                     AND p.status = 30
SQL;

            $sql_invited = <<<SQL
                Select coalesce(max(u.created), 0) as time
                From users u JOIN operations o ON u.id = o.updated_id
                Where u.invited_by = :id
                     AND u.created > :time_from
SQL;

            $time_from = time() - 90*24*60*60;

            $res = db::query(database::SELECT, $sql_invited)->parameters([
                ':id' => $this->user->invited_by,
                ':time_from' => $time_from
            ])->execute();

            if(count($res) > 0) {
                /*
                 * в зависимости от даты последней регистрации
                 * пересчитываем бонусы за реферала пригласившему
                 */
                $diff = (time() - $res[0]['time']) / (24*60*60);
                $coeff = 0;

                //комиссия платежной системы
//                $sql_comiss_system = <<<SQL
//                    Select sum(p.total_commission) as comiss
//                    From payments p
//                    Where p.user_id = :user_id
//                        AND p.created > :time_from
//                        AND p.status not in (0, 40)
//SQL;
//                $res_comiss_system = db::query(1, $sql_comiss_system)->parameters([
//                    ':user_id' => $this->user->id,
//                    ':time_from' => $this->time_from,
//                ])->execute()->as_array();
//
//                $comission_pay_system = isset($res_comiss_system[0]['comiss']) ? round($res_comiss_system[0]['comiss'],2) : 0;
//
//                //комиссия империум геймз
//                $sql_comiss_imperium = <<<SQL
//                    Select coalesce(sum(b.amount - b.win), 0) as sum
//                    From bets b
//                    Where method = 'api_imperium'
//                        AND b.user_id = :user_id
//                        AND b.created > :time_from
//SQL;
//                $res_comiss_imperium = db::query(1, $sql_comiss_imperium)->parameters([
//                    ':time_from' => $this->time_from,
//                    ':user_id' => $this->user->id
//                ])->execute()->as_array();
//
//                $comission_imperium = $res_comiss_imperium[0]['sum'] * 0.1;

                $comission_imperium=0;
                $comission_pay_system=0;

                //размер базы бонуса для пригласившего игрока
                $bonus_base_invited = $bonus_base - $total_bonus - $comission_imperium - $comission_pay_system;

                //проверяем разницу дат и устанавливаем коэфф
                if($diff <= 30) {
                    $coeff = 0.5;
                } elseif($diff <= 60) {
                    $coeff = 0.3;
                } elseif($diff <= 90) {
                    $coeff = 0.15;
                }

                $total_bonus_invited = $bonus_base_invited*$coeff;

                if($total_bonus_invited>0) {
                    $invited_by = new Model_User($this->user->invited_by);
                    if($invited_by->parent_id) {
                       $invited_by=$invited_by->parent_acc();
                    }

                    $invited_user = auth::create_office_account($invited_by, $this->user->office_id);

                    $data_bonus = [
                        'user_id' => $invited_user->id,
                        'referal_id' => $this->user->id,
                        'bonus' => $total_bonus_invited,
                        'created' => time(),
                        'type' => 'referal_activity',
                        'payed' => 1,
                        'log' => json_encode([
                            'last_date_reg_referal_with_deposit' => $res[0]['time'],
                            'coeff' => $coeff,
                            'comission_pay_system' => $comission_pay_system,
                            'comission_imperium' => $comission_imperium,
                            'bonus_base_referal' => $bonus_base,
                            'bonus_referal' => $total_bonus,
                            'bonus_base' => $bonus_base_invited,
                        ]),
                    ];
                    //добавляем бонус пригласившему
                    $this->addBonuses($data_bonus);

                    $ans['invited']['bonus'] = $total_bonus_invited;
                    $ans['invited']['user_id'] = $invited_user->id;
                }
            }
        }

        return $ans;
    }
}
