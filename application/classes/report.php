<?php

class report
{

    public static function getPiastrixBalance() {
        $phones = Kohana::$config->load('static.reportphones');
        $piastrix = new piastrix();
        $piastrix->addParam('now',date('Y-m-d H:i:s.v'));
        $balances = $piastrix->balance();

        if(!isset($balances['balances'])) {
            return false;
        }

        $prefix = THEME=='white'?'_n ':' ';

        $message = 'p' . $prefix;

        foreach ($balances['balances'] as $v) {
            $currency = new Model_Currency(['iso_4217'=>$v['currency']]);
            if($currency->loaded() AND $v['available']>0) {
                if($currency->code!='RUB'){
                    $message .= $currency->code . ' - ' . th::n1($v['available']/1000) . '; ';
                }else{
                    $message .= th::n1($v['available']/1000) . '; ';
                }
            }
        }

        if($message=='p' . $prefix) {
            return;
        }

        foreach($phones as $phone) {
            th::tgsend($phone, $message);
        }
    }

    public static function getTrioBalance() {
        $phones = Kohana::$config->load('static.reportphones');
        $trio = new trio();
        $trio->addParam('now',date('Y-m-d H:i:s.v'));
        $balances = $trio->balance();

        if(!isset($balances['balances'])) {
            return false;
        }

        $prefix = THEME=='white'?'_n ':' ';

        $message = 'trio' . $prefix;

        foreach ($balances['balances'] as $v) {
            $currency = new Model_Currency(['iso_4217'=>$v['currency']]);
            if($currency->loaded() AND $v['balance']>0) {
                if($currency->code!='RUB'){
                    $message .= $currency->code . ' - ' . th::n1($v['balance']/1000) . '; ';
                }else{
                    $message .= th::n1($v['balance']/1000) . '; ';
                }
            }
        }

        foreach($phones as $phone) {
            th::tgsend($phone, $message);
        }
    }

    public static function daylyForcast() {

        $phones = Kohana::$config->load('static.reportphones');
        $test_offices = kohana::$config->load('static.test_offices');

        $project_id = 0;
        switch(THEME) {

            default:
                $project_id=1;
        }

        if(date('d', time()) == 1) {
            $sql_payments_in = <<<SQL
                SELECT (SUM(p.amount)/1000)::NUMERIC(14,2) as payments_in, c.code,
                    ((sum(p.total_commission))/1000)::NUMERIC(14,2) as commission_in
                FROM payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                WHERE
                    p.status = 30
                    AND p.amount > 0
                    AND payed >= extract('epoch' from date_trunc('month', current_timestamp at time zone 'UTC') - interval '1 month')
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;

            //сумма вводов за предыдущий день
            $payments_in = db::query(1,$sql_payments_in)->param(':test_offices', $test_offices)->execute()->as_array('code');

            $sql_payments_out = <<<SQL
                SELECT (abs(SUM(p.amount)/1000))::NUMERIC(14,2) as payments_out, c.code,
                    (sum(p.total_commission)/1000)::NUMERIC(14,2) as commission_out
                FROM payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                WHERE
                    p.status = 30
                    AND p.amount < 0
                    AND payed >= extract('epoch' from date_trunc('month', current_timestamp at time zone 'UTC') - interval '1 month')
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;

            //сумма выводов за предыдущий день
            $payments_out = db::query(1,$sql_payments_out)->param(':test_offices', $test_offices)->execute()->as_array('code');

            $sql_partner_profit = <<<SQL
                Select ((sum(profit_partner))/1000)::NUMERIC(14,2) as profit_partner, c.code
                From statistics s JOIN currencies c ON s.currency_id = c.id
                Where
                    date >= extract('epoch' from date_trunc('month', current_timestamp at time zone 'UTC') - interval '1 month')
                    AND date <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    and project_id=:p_id
                    and partner_id<>1
                GROUP BY c.code
SQL;

            $partner_profit = db::query(1, $sql_partner_profit)->param(':test_offices', $test_offices)->param(':p_id',$project_id)->execute('partner')->as_array('code');

            $sql_forecast_profit_partner = <<<SQL
                Select (sum(profit_partner)/1000)::NUMERIC(14,2) as forecast_profit_partner,
                    c.code
                From statistics s JOIN currencies c ON s.currency_id = c.id
                Where
                    date >= extract('epoch' from date_trunc('month', current_timestamp at time zone 'UTC') - interval '1 month')
                    AND date <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    and project_id=:p_id
                    and partner_id<>1
                GROUP BY c.code
SQL;

            //прогноз по профиту партнеров
            $forecast_profit_partner = db::query(1, $sql_forecast_profit_partner)->param(':test_offices', $test_offices)->param(':p_id',$project_id)->execute('partner')->as_array('code');

            $sql_count_deposits = <<<SQL
                Select c.code, count(distinct user_id) as count_deposits
                From payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                Where
                    p.amount > 0
                    AND payed >= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC') - interval '1 month')
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;
            $count_deposits = db::query(1, $sql_count_deposits)->param(':test_offices', $test_offices)->execute()->as_array('code');


            $sql_forecast_payments = <<<SQL
                select (sum(p.amount-p.total_commission)/1000)::NUMERIC(14,2) as forecast_payments,
                    c.code
                FROM payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                Where
                    status = 30
                    AND payed >= extract('epoch' from date_trunc('month', current_timestamp at time zone 'UTC') - interval '1 month')
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;

            //прогноз по платежам с учетом комиссий
            $forecast_payments = db::query(1,$sql_forecast_payments)->param(':test_offices', $test_offices)->execute()->as_array('code');
        } else {
            $sql_payments_in = <<<SQL
                SELECT (abs(SUM(p.amount))/1000)::NUMERIC(14,2) as payments_in, c.code,
                    (sum(p.total_commission)/1000)::NUMERIC(14,2) as commission_in
                FROM payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                WHERE
                    p.status = 30
                    AND p.amount > 0
                    AND payed >= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC') - interval '1 day')
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;

            //сумма вводов за предыдущий день
            $payments_in = db::query(1,$sql_payments_in)->param(':test_offices', $test_offices)->execute()->as_array('code');

            $sql_payments_out = <<<SQL
                SELECT (abs(SUM(p.amount))/1000)::NUMERIC(14,2) as payments_out, c.code,
                    (sum(p.total_commission)/1000)::NUMERIC(14,2) as commission_out
                FROM payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                WHERE
                    p.status = 30
                    AND p.amount < 0
                    AND payed >= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC') - interval '1 day')
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;

            //сумма выводов за предыдущий день
            $payments_out = db::query(1,$sql_payments_out)->param(':test_offices', $test_offices)->execute()->as_array('code');

            $sql_partner_profit = <<<SQL
                Select (sum(profit_partner)/1000)::NUMERIC(14,2) as profit_partner, c.code
                From statistics s JOIN currencies c ON s.currency_id = c.id
                Where
                    date >= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC') - interval '1 day')
                    AND date <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    and project_id=:p_id
                    and partner_id<>1
                GROUP BY c.code
SQL;

            $partner_profit = db::query(1, $sql_partner_profit)->param(':p_id',$project_id)->execute('partner')->as_array('code');

            $sql_forecast_profit_partner = <<<SQL
                Select (sum(profit_partner)/
                    (date_part('days',current_date) - 1)*
                    (date_part('days',date_trunc('month', current_timestamp at time zone 'UTC' + interval '1 month') - date_trunc('month', current_timestamp)))/1000)::NUMERIC(14,2) as forecast_profit_partner,
                    c.code
                From statistics s JOIN currencies c ON s.currency_id = c.id
                Where
                    date >= extract('epoch' from date_trunc('month', current_timestamp at time zone 'UTC'))
                    AND date <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    and project_id=:p_id
                    and partner_id<>1
                GROUP BY c.code
SQL;

            //прогноз по профиту партнеров
            $forecast_profit_partner = db::query(1, $sql_forecast_profit_partner)->param(':p_id',$project_id)->execute('partner')->as_array('code');

            $sql_forecast_payments = <<<SQL
                select (sum(p.amount-p.total_commission)/
                    (date_part('days',current_date) - 1)*
                    (date_part('days',date_trunc('month', current_timestamp at time zone 'UTC' + interval '1 month') - date_trunc('month', current_timestamp)))/1000)::NUMERIC(14,2) as forecast_payments,
                    c.code
                FROM payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                Where
                    status = 30
                    AND payed >= extract('epoch' from date_trunc('month', current_timestamp at time zone 'UTC'))
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;

            //прогноз по платежам с учетом комиссий
            $forecast_payments = db::query(1,$sql_forecast_payments)->param(':test_offices', $test_offices)->execute()->as_array('code');

            $sql_count_deposits = <<<SQL
                Select c.code, count(distinct user_id) as count_deposits
                From payments p
                    JOIN users u ON p.user_id = u.id
                    JOIN offices o ON u.office_id = o.id
                    JOIN currencies c ON o.currency_id = c.id
                Where
                    p.amount > 0
                    AND payed >= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC') - interval '1 day')
                    AND payed <= extract('epoch' from date_trunc('day', current_timestamp at time zone 'UTC'))
                    AND o.id not in :test_offices
                GROUP BY c.code
SQL;
            $count_deposits = db::query(1, $sql_count_deposits)->param(':test_offices', $test_offices)->execute()->as_array('code');
        }

        $sql_bank = <<<SQL
            Select c.code, (sum(s.value_numeric)/1000)::NUMERIC(14,2) as bank
            From status s JOIN offices o ON s.type::integer=o.id
                JOIN currencies c ON o.currency_id=c.id
            Where s.id = 'bank'
                AND s.type <> 'main'
                AND s.type::integer not in :test_offices
            GROUP BY c.code
SQL;
        $bank = db::query(1, $sql_bank)->param(':test_offices', $test_offices)->execute()->as_array('code');

        $sql_st_users = <<<SQL
            Select c.code, (sum(s.value_numeric)/1000)::NUMERIC(14,2) as st_users
            From status s JOIN offices o ON s.type::integer=o.id
                JOIN currencies c ON o.currency_id=c.id
            Where s.id = 'users'
                AND s.type <> 'main'
                AND s.type::integer not in :test_offices
            GROUP BY c.code
SQL;
        $st_users = db::query(1, $sql_st_users)->param(':test_offices', $test_offices)->execute()->as_array('code');

        $default_params = [
            'have_data' => false,
            'payments_in' => 0,
            'commission_in' => 0,
            'payments_out' => 0,
            'commission_out' => 0,
            'profit_partner' => 0,
            'profit' => 0,
            'forecast' => 0,
            'count_deposits' => 0,
            'forecast_payments' => 0,
            'forecast_profit_partner' => 0,
            'profit_without_partner' => 0,
            'bank' => 0,
            'st_users' => 0,
        ];

        //результаты всех sql запросов для сбора статистики
        $data = [$payments_in, $payments_out, $partner_profit, $count_deposits, $forecast_payments, $forecast_profit_partner, $bank, $st_users];

        $result = [];

        foreach ($data as $array) {
            foreach ($array as $currency => $value) {
                if(!isset($result[$currency])) {
                    $result[$currency] = $default_params;
                }

                foreach ($default_params as $k=>$v) {
                    if(isset($value[$k])) {
                        $result[$currency][$k] = $value[$k];
                    }
                }
            }
        }

        /*
         * считаем профит за вчера и прогноз по профиту на конец месяца
         * и проставляем флаг если есть данные
         */
        foreach ($result as $currency => $v) {
            $result[$currency]['profit'] = $v['payments_in'] - $v['payments_out'] - $v['commission_in'] - $v['commission_out'] - $v['profit_partner'];
            $result[$currency]['forecast'] = $v['forecast_payments'] - $v['forecast_profit_partner'];
            $result[$currency]['profit_without_partner'] = $v['payments_in'] - $v['payments_out'] - $v['commission_in'] - $v['commission_out'];

            foreach ($v as $value) {
                if($value != 0) {
                    $result[$currency]['have_data'] = true;
                }
            }
        }

        foreach ($result as $currency_code => $res) {
            $message=[date('m.d',time()-24*60*60)];

            $currency_model = new Model_Currency(['code'=>$currency_code]);

            if($currency_code != 'RUB') {
                $message[]=$currency_code;
            }

            $message[]=th::n1($res['payments_in']);
            $message[]=th::n1($res['profit_without_partner']);
            $message[]=th::n1($res['forecast']);
            $message[]='U'.(int)$res['count_deposits'];
            $message[]='P'.th::n1($res['profit_partner']);
            $message[]='B '.th::n1($res['bank']).'/'.th::n1($res['st_users']);

            $date_for_report = date('Y-m-d', time()-24*60*60);

            $params_query = [
                ":date" => $date_for_report,
                ":currency_id" => $currency_model->id,
                ":payments_in" => $res['payments_in'],
                ":profit" => $res['profit'],
                ":forecast" => $res['forecast'],
                ":count_deposits" => $res['count_deposits'],
            ];

            $row = DB::query(Database::UPDATE, '
                update users_statistics set
                    payments_in = :payments_in, profit = :profit,
                    forecast = :forecast, count_deposits = :count_deposits
                WHERE
                    date = :date
                    AND
                    currency_id = :currency_id
                RETURNING id')
            ->parameters($params_query)
            ->execute();

            if($row == 0) {
                $sql_insert = <<<SQL
                    insert into users_statistics(date, currency_id, payments_in, profit, forecast, count_deposits)
                    values(:date, :currency_id, :payments_in, :profit, :forecast, :count_deposits)
SQL;
                DB::query(Database::INSERT, $sql_insert)
                    ->parameters($params_query)
                    ->execute();

            }

            if($res['have_data']){
                foreach($phones as $phone) {
                    th::tgsend($phone,implode(' ',$message));
                }
            }

        }
    }

    public static function counters() {
        $sql_counters = <<<SQL
            Select s.*
            From statistics s
            join offices o on o.id=s.office_id
            Where s.date = :date
                and s.bettype not in :bettypes
                and o.is_test = 0
SQL;
        $bettypes = ['norcfs'];
        $res_counters = db::query(1, $sql_counters)->param(':date', date('Y-m-d'))->param(':bettypes', $bettypes)->execute();

        $data = [
                0=>[]
        ];

        foreach ($res_counters as $counter) {
            $bettype = '';

            switch ($counter['bettype']) {
                case 'normal':
                case 'norcfs':
                case 'norafs':
                case 'frecfs':
                case 'freafs':
                case 'bonus':
                case 'free':
                    $bettype = 'normal_free';
                    break;
                case 'double':
                case 'doucfs':
                case 'douafs':
                    $bettype = 'double';
                    break;
                default :
                    $bettype = 'normal_free';
                    break;
            }

            if(!isset($data[$counter['office_id']][$counter['type']][$counter['game']][$bettype])) {
                $data[$counter['office_id']][$counter['type']][$counter['game']][$bettype] = [
                    'amount_in' => 0,
                    'amount_out' => 0,
                    'count' => 0,
                ];
            }

            if(!isset($data[0][$counter['type']][$counter['game']][$bettype])) {
                $data[0][$counter['type']][$counter['game']][$bettype] = [
                    'amount_in' => 0,
                    'amount_out' => 0,
                    'count' => 0,
                ];
            }

            $data[$counter['office_id']][$counter['type']][$counter['game']][$bettype]['amount_in'] += $counter['amount_in'];
            $data[$counter['office_id']][$counter['type']][$counter['game']][$bettype]['amount_out'] += $counter['amount_out'];
            $data[$counter['office_id']][$counter['type']][$counter['game']][$bettype]['count'] += $counter['count'];

            $data[0][$counter['type']][$counter['game']][$bettype]['amount_in'] += $counter['amount_in'];
            $data[0][$counter['type']][$counter['game']][$bettype]['amount_out'] += $counter['amount_out'];
            $data[0][$counter['type']][$counter['game']][$bettype]['count'] += $counter['count'];
        }

        $current_date = mktime(0,0,0);

        foreach ($data as $office_id => $offices) {
            foreach ($offices as $type => $value) {
                foreach ($value as $game => $val) {
                    foreach ($val as $bt => $v) {
                        $monitor_counter = new Model_Countersmonitoring([
                            'type' => $type,
                            'game' => $game,
                            'bettype' => $bt,
                            'office_id' => $office_id,
                        ]);

                        $current_percent_returning = ($v['amount_in']>0)?$v['amount_out']/$v['amount_in']*100:100;
			$current_win = ($v['amount_in']-$v['amount_out'])<-1000;

                        if($monitor_counter->loaded()) {
                            $diff_amount_out = $v['amount_out'] - $monitor_counter->amount_out;

                            if($diff_amount_out >= 100 && $current_win) {
                                $monitor_counter->office_id = $office_id;
                                $monitor_counter->date = $current_date;
                                $monitor_counter->amount_in = $v['amount_in'];
                                $monitor_counter->amount_out = $v['amount_out'];
                                $monitor_counter->count_bets = $v['count'];
                                $monitor_counter->updated = time();

                                if(($current_percent_returning >= $monitor_counter->percent)) {
                                    $monitor_counter->count_danger += 1;
                                } else {
                                    $monitor_counter->count_danger = 0;
                                }
                                $monitor_counter->percent = $current_percent_returning;
                            }

                        } else {
                            $monitor_counter->office_id = $office_id;
                            $monitor_counter->date = $current_date;
                            $monitor_counter->type = $type;
                            $monitor_counter->game = $game;
                            $monitor_counter->bettype = $bt;
                            $monitor_counter->amount_in = $v['amount_in'];
                            $monitor_counter->amount_out = $v['amount_out'];
                            $monitor_counter->count_bets = $v['count'];
                            $monitor_counter->percent = $current_percent_returning;
                            $monitor_counter->updated = time();
                        }

                        if($monitor_counter->count_danger > 3) {
                            $monitor_counter->count_danger = 0;


                            $game_model = new Model_Game(['name'=>$game]);
                            $game_name = $game_model->loaded()?$game_model->visible_name:$game;

                            $office_id_sms = $office_id;

                            if($office_id_sms==0) {
                                $office_id_sms = 'total';
                            }

                            $tg_message = $office_id_sms.' '.th::short_name_game($game_name) . ' D ';
                            $tg_message .= round($current_percent_returning, 2) . ' ' . round(($v['amount_in']-$v['amount_out'])/1000, 2);

                            th::ceoAlert($tg_message);
                        }

                        $monitor_counter->save();
                    }
                }
            }
        }
    }
}
