<?php

class Service
{


//bets to archive
    public static function betArchive()
    {

        //days in bets
        $deep = 10;

        //to seconds
        $deep *= 60 * 60 * 24;
        $keyProcess = 'betarchive';


        if (!th::lockProcess($keyProcess)) {
            return null;
        }

        //last day in archive
        $st = Status::instance();

        $fromDate = $st->get($keyProcess);

        $date = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - $deep;


        if ($date <= $fromDate) {
            return null;
        }


        $txt = 'Days ';
        //from next in archive to now-deep
        for ($i = $fromDate + 60 * 60 * 24; $i <= $date; $i += 60 * 60 * 24) {

            $name = date('Y', $i) . '_' . date('m', $i) . '_' . date('d', $i);

            $sql = "alter table bets_$name no inherit bets ";
            db::query(Database::UPDATE, $sql)->execute();


            $sql = "alter table bets_$name inherit bets_archive ";
            db::query(Database::UPDATE, $sql)->execute();

            $txt .= " $name";

        }

        $st->set($keyProcess, $date);
        $txt .= ' moved to archive';

        th::techAlert($txt);
        th::unlockProcess($keyProcess);


    }


    public static function statisticLocal()
    {


        //days in bets
        $deep = 1;

        //to seconds
        $deep *= 60 * 60 * 24;

        $keyProcess = 'statlocal';

        if (!th::lockProcess($keyProcess)) {
            return null;
        }

        //last day in archive
        $st = Status::instance();

        $fromDate = $st->get($keyProcess);

        $date = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - $deep;

        if ($date <= $fromDate) {
            return null;
        }

        $days = [];
        for ($i = $fromDate + 60 * 60 * 24; $i <= $date; $i += 60 * 60 * 24) {
            $days[] = date('Y-m-d', $i);
        }


        //from next in archive to now-deep
        $fromDate -= 60 * 60 * 24;

        $sql = "insert into statistics_local (
                                date,type, game,bettype,office_id,
                                                 amount_in,
                                                 amount_out,count ,game_id
                )

        select namedate(b.created,o.zone_time) as date,b.game_type as type, b.game,  b.type as bettype,b.office_id,
        sum(CASE When ( (b.game in ('acesandfaces','jacksorbetter','tensorbetter') ) and (b.type in ('free', 'normal', 'norcfs','norafs','frecfs','frecfs' ) )     )then 0 ELSE b.amount END) as amount_in ,
        sum(b.win) as amount_out, count(b.id) as \"count\", b.game_id
        from bets b
        join offices o on o.id=b.office_id
		join users u on u.id=b.user_id
        where b.created>=:start
            and namedate(b.created,o.zone_time) in :days
			and u.test = 0
        GROUP BY 1,2,3,4,5,9
        ON CONFLICT do nothing;";


        db::query(Database::DELETE, $sql)->param(':start', $fromDate)
            ->param(':days', $days)
            ->execute();


        $sql = " insert into  statistics_local (
           date, type,game,bettype,office_id,
					 amount_in,
					 amount_out,count,game_id
                )

        select namedate(b.created,o.zone_time) as date,b.game_type as type, b.game,  type as bettype,b.office_id,
                            sum(b.amount) as amount_in ,
                            0, count(b.id) as \"count\", b.game_id
        from pokerbets b
        join offices o on o.id=b.office_id
		join users u on u.id=b.user_id
        where b.created>=:start
            and namedate(b.created,o.zone_time) in :days
			and u.test = 0
        GROUP BY 1,2,3,4,5,9
        ON CONFLICT On CONSTRAINT statistics_t1_pkey do UPDATE
        SET amount_in=EXCLUDED.amount_in,
		\"count\"=EXCLUDED.count";

        db::query(Database::DELETE, $sql)->param(':start', $fromDate)
            ->param(':days', $days)
            ->execute();


        $st->set($keyProcess, $date);

        $txt = 'LOCAL statistic collects for days ' . implode(', ', $days);

        th::unlockProcess($keyProcess);


    }


    public static function statisticDynamics()
    {


        //days in bets
        $deep = 1;

        //to seconds
        $deep *= 60 * 60 * 24;

        $keyProcess = 'statdynamics';

        if (!th::lockProcess($keyProcess)) {
            return null;
        }

        //last day in archive
        $st = Status::instance();

        $fromDate = $st->get($keyProcess);

        if (empty($fromDate)) {
            //26.02.2023 включили в 1030 luckyspins
            $fromDate = mktime(0, 0, 0, 8, 1, 2023);
        }

        $date = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - $deep;

        if((($date-$fromDate)/60/60/24)>1) {
            $date=$fromDate+24*60*60;
        }

        if ($date <= $fromDate) {
            return null;
        }

        $days = [];

        $sql_day='';

        for ($i = $fromDate + 60 * 60 * 24; $i <= $date; $i += 60 * 60 * 24) {
            $days[] = date('Y-m-d', $i);
            $sql_day=date('Y_m_d', $i);
        }


        //from next in archive to now-deep
        $fromDate -= 60 * 60 * 24;

        $sql = "insert into statistics_dynamics (
                            date,office_id,bettype,avgbet,
                            sumwin,sumamount,users_count,bets_count
            )

    select namedate(b.created) as date,
    b.office_id,
    (CASE 
        When b.is_freespin=3 then 
            case 
                when substring(e.extra_params, '\[\"' || REPLACE(RIGHT ( REGEXP_REPLACE(info,'\s\(\d\)',''), 2 ),'/','') || '\",') is not null then 'ls1' else 'ls2'
            end
        when b.is_freespin=1 then 'cs'
        ELSE 'bet' 
    END),
    avg(b.real_amount),
    sum(b.real_win),
    sum(b.real_amount),
    count(distinct b.user_id),
    count(b.id)
    from bets_{$sql_day} b
    left join events e on e.office_id=b.office_id and e.type='progressive' and b.created>e.starts and b.created < e.ends
    where b.created>=:start
        and namedate(b.created) in :days
    group by 1,b.office_id, 3
    ON CONFLICT do nothing;";

        echo db::query(Database::DELETE, $sql)
            ->param(':start', $fromDate)
            ->param(':days', $days)->compile(database::instance());


        db::query(Database::DELETE, $sql)
            ->param(':start', $fromDate)
            ->param(':days', $days)
            ->execute();


        $sqlJP = "insert into statistics_dynamics (
                            date,office_id,bettype,avgbet,
                            sumwin,sumamount,users_count,bets_count
            )

    select namedate(b.created) as date,
    b.office_id,
    'jp',
    0,
    sum(b.win),
    0,
    count(distinct b.user_id),
    count(b.id)
    from jackpot_history b
    where b.created>=:start
        and namedate(b.created) in :days
    group by 1,b.office_id
    ON CONFLICT do nothing;";

        echo db::query(Database::DELETE, $sqlJP)
            ->param(':start', $fromDate)
            ->param(':days', $days)->compile(database::instance());

        db::query(Database::DELETE, $sqlJP)
            ->param(':start', $fromDate)
            ->param(':days', $days)
            ->execute();

        $st->set($keyProcess, $date);

        $txt = 'LS DS statistic collects for days ' . implode(', ', $days);
        th::techAlert($txt);
        th::unlockProcess($keyProcess);


    }

    /**
     * собирает все ставки по 1003 клубу за вчерашний день и отправляет на указанную почту вложением
     */

    public static function bets1003()
    {
        static::officebets(1003, 'arijus@supawayja.com');
    }

    /**
     * собирает все ставки по ownerу 1062 за вчерашний день и отправляет на указанныу почты вложением
     */

    public static function betsOwner1062()
    {
        static::officebets(0, [
            'accounts@posttopostja.com',
            'donmarksmith@posttopostja.com',
            'jevaunwhyte@posttopostja.com',
        ], 1062);
    }

    /**
     * собирает все ставки по указанному клубу за вчерашний день и отправляет на указанную почту(ы) вложением
     */

    public static function officebets($office_id, $emails = [], $owner_id = null)
    {
        if (!is_array($emails)) {
            $emails = [$emails];
        }
        $sql = "select b.id, b.user_id, b.office_id,c.code,  b.info,  b.amount as rate, b. win, b.balance-b.win+b.amount as balance_before, b.balance as balance_after, b.come as lines,g.visible_name,
                    vdate(b.created+o.zone_time*60*60)
                    from bets b
                    join offices o on b.office_id=o.id
                    join currencies c on o.currency_id=c.id
                    join games g on g.id=b.game_id where ";

        if (empty($owner_id)) {
            $sql .= "b.office_id={$office_id}";
        } else {
            $sql .= "o.owner={$owner_id}";
        }

        //обязательно! сортировка первая по office_id для отправки с owner

        $sql .= "   and b.created>= EXTRACT( epoch from date_trunc('day', now() at time zone 'utc' ) )-24*60*60-o.zone_time*60*60
                    and b.created< EXTRACT( epoch from date_trunc('day', now() at time zone 'utc' ) )-o.zone_time*60*60 order by o.id,b.id";

        $data = db::query(1, $sql)
            ->execute()->as_array();

        $last_o_id = false;
        $need_send_email = false;

        //поля в заголовок csv-файла

        $fields = [
            'id',
            'user_id',
            'office_id',
            'info',
            'rate',
            'win',
            'balance_before',
            'balance_after',
            'lines',
            'visible_name',
            'vdate',
        ];

        $csv = implode(';', $fields) . PHP_EOL;

        foreach ($data as $i => $row) {

            if (!$last_o_id) {
                $last_o_id = $row['office_id'];
            }

            // дошли до конца. отправляем отчет

            if ($i == count($data) - 1) {
                $need_send_email = true;
            }

            if ($row['office_id'] != $last_o_id || $need_send_email) {
                //send email
                $date = date('Y-m-d', strtotime("-1 day"));

                $o = new Model_Office($last_o_id);

                //отправка письма. работает через Swift
                foreach ($emails as $email) {
                    try {
                        Email::send($email, ['no-reply@site-domain.com', 'site-domain.com'], 'report [' . $last_o_id . '] ' . $o->visible_name . ' ' . $date . '', 'report ' . $date, false, $csv, 'report_' . $date);
                    } catch (Exception $e) {
                        th::techAlert('cant send email [' . $email . '] with bets [' . $last_o_id . ']');
                        Kohana::$log->writeException($e);
                    }
                }
                logfile::create(date('Y-m-d H:i:s') . PHP_EOL . $csv, $last_o_id . 'csv');

                //clear csv
                $csv = implode(';', $fields) . PHP_EOL;

                $last_o_id = $row['office_id'];
            }

            $csv .= implode(';', $row) . PHP_EOL;
        }
    }

    public static function statisticUsers()
    {

        //days in bets
        $deep = 1;

        //to seconds
        $deep *= 60 * 60 * 24;

        $keyProcess = 'statUsers';


        if (!th::lockProcess($keyProcess)) {
            return null;
        }


        //last day in archive
        $st = Status::instance();

        $fromDate = $st->get($keyProcess);

        $date = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - $deep;

        if ($date <= $fromDate) {
            return null;
        }


        $days = [];
        for ($i = $fromDate + 60 * 60 * 24; $i <= $date; $i += 60 * 60 * 24) {
            $day = date('Y-m-d', $i);
            $nameday = date('Y_m_d', $i);
            $sql = "insert into  statistic_users (date,office_id,users)
                select  :day, b.office_id, count(distinct user_id) as users
                    from bets_$nameday b
                    join users u on u.id = b.user_id
										where u.test = 0
                    group by 1,2";


            db::query(Database::DELETE, $sql)->param(':day', $day)
                ->execute();

            $days[] = $day;

        }


        $sql = "with nu as (select namedate(created) as created,office_id,count(id) as newusers
            from users
            where namedate(created) in :days
                    and created>=:created
					and test = 0
            group by 1,2)

            insert into  statistic_users (date,office_id,newusers)
            select created,office_id,newusers
            from nu
            ON CONFLICT On CONSTRAINT statistic_users_pkey do UPDATE SET newusers = EXCLUDED.newusers; ";

        db::query(Database::DELETE, $sql)->param(':days', $days)
            ->param(':created', $fromDate - 60 * 60 * 24)
            ->execute();

        $st->set($keyProcess, $date);
        $txt = 'USER statistic collects for days ' . implode(', ', $days);
        th::techAlert($txt);
        th::unlockProcess($keyProcess);

    }


    public static function usersMH()
    {
        $from = status::instance()->usersMH;
        $now = time();

        if ($now - $from < 60 * 60 * 24 * 31.5) {
            return;
        }

        $m = date('m', $from);
        $y = date('Y', $from);

        $m++;
        if ($m > 12) {
            $m = 1;
            $y++;
        }

        $to = mktime(0, 0, 0, $m, 1, $y);


        $sql = 'delete from users_month_history where date=:date';
        db::query(Database::DELETE, $sql)->param(':date', $from)->execute();


        $sql = "insert into users_month_history (user_id,date,game,amount,win,count)

            SELECT
            b.user_id,
            extract(epoch from (date_trunc('month', to_timestamp(b.created) at time zone 'UTC')  )at time zone 'utc' )::int4 as date,
            b.game,
            sum( case when (p.amount is null ) then b.amount else p.amount end) as amount,
            sum( b.win ) as win,
            count(*)

            from bets_archive b
            join offices o on b.office_id=o.id and o.is_test=0
            left join pokerbets p on b.external_id=p.id
            where b.created>=:from and b.created<:to
            group by 1,2,3";

        DB::query(4, $sql)->param(':from', $from)->param(':to', $to)->execute();

        status::instance()->usersMH = $to;


        //th::ceoAlert("Users' bets collect for $m $y");
        echo "Users' bets collect for $m $y";


    }


    public static function parseCurrencyRateByDate($date)
    {

        $date = date('Y-m-d', $date);


        $url = 'https://www.xe.com/currencytables/?from=EUR&date=' . $date;

        $p = new Parser();
        $res = $p->get($url);
        if (!$res) {
            th::critAlert('xe not work '.$date);
        }
        $data = $p->html()->find('#table-section', 0)->find('table', 0);
        $rows = $data->find('tr');

        $data = [];
        foreach ($rows as $row) {
            $name = $row->find('th', 0)->plaintext;
            if ($name == 'Currency') {
                continue;
            }
            $value = $row->find('td', 2)->plaintext;
            $value = str_replace(',', '', $value);
            $data[$name] = $value;
			if($name=='XOF') {
                $data['CFA'] = $value;
            }
        }

        return $data;

    }

    public static function getCurrencyRatesVertbet($date_int, $dateForDB, $data)
    {

        $date = date('Y-m-d', $date_int);

        $result_rates = [];

        //https://currency.world/historical_data/EUR_pre_2022-09-20.tsv

        /*$urlEUR='https://currency.world/historical_data/EUR_pre_'.date('Y-m-d',$date_int+60*60*24).'.tsv';

        $pEUR=new Parser();
        $pEUR->UseComp();

        $txtEUR=$pEUR->get($urlEUR);

        $ratesEUR=[];

        foreach(explode("\n",$txtEUR) as $i=>$rowEUR) {
            if($i==0 || empty($rowEUR)) {
                continue;
            }
            list($dateEUR,$valEUR)=explode("	",$rowEUR);
            $ratesEUR[$dateEUR]=$valEUR;
        }*/

        $other_currencies = db::query(1, 'SELECT distinct c.code, cc.id 
                FROM currencies c
                join coinmarketcap_listings cc on c.id=cc.currency_id
                where c.code not in(select currency from currency_rates where date=' . $dateForDB . ') and c.code not in :exclude order by 1')
            ->param(':exclude', array_merge(['FUN',
            ], array_keys($data)))
            ->execute()->as_array();


        //не нашел ничего по валютам lat LVL byr icx xb2,XB3,LTL,DEC,DAI,SHIB,TRX
        //валюта pnt есть на https://coinmarketcap.com/currencies/pnetwork/historical-data/
        //MRO -  фиат мавритании. нужно где то взять
        //BNB++
        //BUSD++
        //USDT++
        //TUSD
        //USDC
        //PRB приднестровская валюта
        //CFA west african frank
        //SIT - словенский толар. написано что заменен на евро

        foreach ($other_currencies as $currency) {

            /*if($currency['code']=='CBET') {
                $pc=new Parser();
                //todo или дата+сутки?
                $req=$pc->get('https://api.coingecko.com/api/v3/coins/cbet-token/history?date='.date('d-m-Y',$date_int));
                if($req) {
                    $json=json_decode($req,1);

                    $result_rates[$currency['code']]="".($json['market_data']['current_price']['eur']);

                    continue;
                }
                echo 'https://api.coingecko.com/api/v3/coins/cbet-token/history?date='.date('d-m-Y',$date_int);
                exit;
            }*/

            //парсим каждую историю валют отдельно
            /*$urlCurr='https://currency.world/historical_data/'.$currency['code'].'_pre_'.date('Y-m-d',$date_int+60*60*24).'.tsv';

            $pCurr=new Parser();
            $pCurr->UseComp();

            $txtCurr=$pCurr->get($urlCurr);

            $ratesCurr=[];

            foreach(explode("\n",$txtCurr) as $i=>$rowCurr) {
                if($i==0 || empty($rowCurr)) {
                    continue;
                }
                list($dateCurr,$valCurr)=explode("	",$rowCurr);
                $ratesCurr[$dateCurr]=$valCurr;
            }

            $result_rates[$currency['code']]="".($ratesCurr[$date]/$ratesCurr[$date]);*/

            $url='https://api.coinmarketcap.com/data-api/v3.1/cryptocurrency/historical?id='.$currency['id'].'&convertId=2790&timeStart='.($dateForDB - 1 - 60 * 60 * 24).'&timeEnd='.($dateForDB).'&interval=1d';
            $pCurr = new Parser();
            $txtCurr = $pCurr->get($url);

            if(!$txtCurr) {
                continue;
            }

            $data=json_decode($txtCurr,1);

            if(!isset($data['data']['quotes'][0])) {
                Kohana::$log->add(Log::ERROR,'currency not found: '.$currency['code'].PHP_EOL.Debug::vars($txtCurr));
                continue;
            }

            $result_rates[$currency['code']]=$data['data']['quotes'][0]['quote']['close'];
        }

        return $result_rates;

    }

    public static function updateRatesDay($date, $dateForDB)
    {

        $data = static::parseCurrencyRateByDate($date);

        $currency_world_data = static::getCurrencyRatesVertbet($date, $dateForDB, $data);

        $data = $data + $currency_world_data;

        foreach ($data as $name => $value) {

            $sql = 'insert into currency_rates (date,currency,value)
                values(:date,:currency,:value)
                ON CONFLICT (date,currency) do
					update
				    set value=:value';

            db::query(Database::UPDATE, $sql)->param(':date', $dateForDB)
                ->param(':currency', $name)
                ->param(':value', $value)
                ->execute();

        }

    }


    public static function collectRatesYesterday()
    {

        $yesterday=strtotime("-1 days");
        $dateForDB = $date = mktime(0, 0, 0, date('m',$yesterday), date('d',$yesterday), date('Y',$yesterday));

        $date = $yesterday;

        static::updateRatesDay($date, $dateForDB);

    }

    public static function monthStat($m = null, $y = null)
    {

        if (empty($y)) {
            $y = date('Y', time());
        }

        if (empty($m)) {
            $m = date('m', time());
        }

        $dateForDB = $date = mktime(0, 0, 0, $m, 1, $y);
        $date = $date - 60 * 60 * 24;

        static::updateRatesDay($date, $dateForDB);

    }


    public static function updateNullCurrencies($source = 'vertbet')
    {

        $url = 'https://currency.world/exchange_rates/EUR/';

        $currencies = db::query(1, 'select code from currencies where source=:source')
            ->param(':source', $source)
            ->execute()->as_array();

        $p = new Parser();
        $p->UseComp();

        $time = time();

        foreach ($currencies as $curr) {

            //vertbet cryptocurrency
            if ($curr['code'] == 'CBET') {
                $pc = new Parser();
                $req = $pc->get('https://api.coingecko.com/api/v3/coins/cbet-token/history?date=' . date('d-m-Y', strtotime("-1 days")));
                if ($req) {
                    $json = json_decode($req, 1);
                    $sql = 'update currencies set val=:value, updated=:updated where code=:currency';

                    db::query(Database::UPDATE, $sql)
                        ->param(':currency', $curr['code'])
                        ->param(':value', "" . ($json['market_data']['current_price']['eur']))
                        ->param(':updated', $time)
                        ->execute();
                }

                continue;
            }

            echo $url . $curr['code'] . PHP_EOL;
            $res = $p->get($url . $curr['code']);
            if ($res) {
                $html = $p->html();
                $rate = $html->find('.main_info .num', 1);

                if ($rate) {
                    echo 'ok' . PHP_EOL;
                    $sql = 'update currencies set val=:value, updated=:updated where code=:currency';

                    db::query(Database::UPDATE, $sql)
                        ->param(':currency', $curr['code'])
                        ->param(':value', "" . (1 / $rate->plaintext))
                        ->param(':updated', $time)
                        ->execute();
                }
            }
        }

    }

    public static function updateCurrencies($source = 'agt', $time = null)
    {

        if (is_null($time)) {
            $time = strtotime("-1 days");
        }

        $data = static::parseCurrencyRateByDate($time);

        foreach ($data as $name => $value) {

            $sql = 'update currencies set val=:value, updated=:updated where code=:currency and source=:source';

            db::query(Database::UPDATE, $sql)
                ->param(':currency', $name)
                ->param(':value', $value)
                ->param(':source', $source)
                ->param(':updated', $time)
                ->execute();

        }
    }

    public static function refundMoonRoundBets($round_num)
    {

        $sql = 'insert into moon_dispatch_bets(initial_id,sended,office_id,user_id,rate,created,try,win,amount,session_id)
                                      select b.id,0,b.office_id,b.user_id,1,:time,0,b.amount,0,b.session_id 
                                      from bets b where b.game in :game and b.come=:roundnum::varchar and amount>0';
        db::query(1, $sql)
            ->param(':game', th::getMoonGames())
            ->param(':roundnum', $round_num)
            ->param(':time', time())
            ->execute();
    }

    /**
     * Отдает топ выигрышей по office_id
     * @param integer $office_id
     * @return  array
     */
    public static function topWins($office_id)
    {

        dbredis::instance()->select(5);

        $cache_key = 'topwins_' . $office_id;
        $cache_time = Date::HOUR / 2;

        if (!$topwin = dbredis::instance()->get($cache_key)) {

            //part2
            $sql = "
            
            select DISTINCT ON (t.game) t.win,t.game,t.time as created,g.visible_name from topwins t
            join games g on g.name=t.game
            where office_id=$office_id and time>=extract(epoch from now() at time zone 'UTC')::int - 24*60*60*30
            order by t.game asc, win desc
            
            limit 12
            ";

            $topwin = db::query(1, $sql)->execute()->as_array();

            if (empty($topwin)) {
                dbredis::instance()->select(0);
                return [];
            }

            dbredis::instance()->set($cache_key, json_encode($topwin));
            dbredis::instance()->expire($cache_key, $cache_time);
            dbredis::instance()->select(0);
        } else {
            $topwin = json_decode($topwin, 1);
        }

        return $topwin;

    }


    /***
     * Добавляет в таблицу bets_avg новый день
     */
    public static function betsAvg()
    {

        $sql='select max(date) as date 
                from bets_avg';
        $min=db::query(1,$sql)->execute()->as_array()[0]['date'];
        $day=date('Y_m_d',$min+60*60*24);

        echo $day;
        //текущий день не считаем
        if ($day==date('Y_m_d') ){
            return null;
        }


        $sql="insert into bets_avg (date,value)
                SELECT  round(created/60)::int*60 , (count(*)/60)::int
                from bets_$day
                GROUP BY 1
                on conflict(date) do NOTHING";

        db::query(3,$sql)->execute();

        echo 'complite';
    }

public static function userMinus()
    {
        ob_end_clean();
        $interval = 60 * 24;
        $try = 3;
        $money = 50;
        $moneyLimit = 0;
        $rtpLimit = 1.5;
        $countLastAmount = 10;


        //08
        //$start = 1690837200;
        //$end =   1693515600;


        //10
        //$start = 1696107600;
        //$end =   1698786000;

        //11
        //$start = 1698786000;
        //$end = 1701378000;

        $end = mktime(0, 0, 0, date('m'), date('d'), date('Y'))-24*60*60;
        $start = $end - 60 * 60 * 24 * 30;


        $sql = 'select user_id, case when sum(in1)>0 then sum(out1)/sum(in1) else 0 end as rtp, sum(count1) as count1
                    from (
                        
                    select   user_id,sum(b.amount*c.val) as in1, sum(b.win*c.val) as  out1, count(b.id) as count1
                                        from bets b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created > :start
                                          and b.created < :end
                                        and b.is_freespin = 0
                                        and o.is_test=0
                                        group by 1
                    
                    UNION all
                    
                    select   user_id,sum(b.amount*c.val) as in1, sum(b.win*c.val) as  out1, count(b.id) as count1
                                        from bets_archive b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created > :start
                                          and b.created < :end
                                        and b.is_freespin = 0
                                        and o.is_test=0
                                        group by 1
                    
                    UNION all
                    
                    select user_id,sum(b.amount*c.val) as in1, sum(b.win*c.val) as  out1, 0 as count1 
                                        from pokerbets b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created > :start
                                          and b.created < :end
                                        and o.is_test=0
                                        group by 1
                    
                    ) ut
                    GROUP BY 1';

        $rtp = db::query(1, $sql)->param(':start', $start)->param(':end', $end)->execute()->as_array('user_id');

        $sql = "select  user_id, sum(amount) as amount
                    from (
                    
                    select trunc(b.created/60/:interval)*60*:interval||'-'||user_id as user_id,(sum(b.amount*c.val)-sum(b.win*c.val)) as amount
                                        from bets b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created >= :start
                                          and b.created < :end
                                        and b.is_freespin = 0
                                        and o.is_test=0
                                        group by 1
                    
                    UNION all
                    
                    select trunc(b.created/60/:interval)*60*:interval||'-'||user_id as user_id,(sum(b.amount*c.val)-sum(b.win*c.val)) as amount
                                        from bets_archive b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created >= :start
                                          and b.created < :end
                                        and b.is_freespin = 0
                                        and o.is_test=0
                                        group by 1
                    
                    UNION all
                    
                    select trunc(b.created/60/:interval)*60*:interval||'-'||user_id as user_id,(sum(b.amount*c.val)-sum(b.win*c.val)) as amount
                                        from pokerbets b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created >= :start
                                          and b.created < :end
                                        and o.is_test=0
                                        group by 1
                    
                    ) ut
                    GROUP BY 1";

        $wins = db::query(1, $sql)->param(':start', $end - 60 * 60 * 24 * 9)->param(':end', $end)->param(':interval', $interval)->param(':money', $money)->execute()->as_array('user_id');


        $sql = 'select date, user_id, sum(amount) as amount, sum(count) as count
                    from (
                    
                    select trunc(b.created/60/:interval)*60*:interval as date, user_id,(sum(b.amount*c.val)-sum(b.win*c.val)) as amount, count(b.id) as count
                                        from bets b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created > :start
                                          and b.created < :end
                                        and b.is_freespin = 0
                                        and o.is_test=0
                                        group by 1,2
                    
                    UNION all
                    
                    select trunc(b.created/60/:interval)*60*:interval as date, user_id,(sum(b.amount*c.val)-sum(b.win*c.val)) as amount, count(b.id) as count
                                        from bets_archive b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created > :start
                                          and b.created < :end
                                        and b.is_freespin = 0
                                        and o.is_test=0
                                        group by 1,2
                    
                    UNION all
                    
                    select trunc(b.created/60/:interval)*60*:interval as date, user_id,(sum(b.amount*c.val)-sum(b.win*c.val)) as amount, 0 as count
                                        from pokerbets b
                                        join offices o on o.id=b.office_id
                                        join currencies c on c.id=o.currency_id
                                        where b.created > :start
                                          and b.created < :end
                                        and o.is_test=0
                                        group by 1,2
                    
                    ) ut
                    GROUP BY 1,2
                    having (sum(amount)) > :money or (sum(amount)) < -:money
                    order by 2,1';


        $data = db::query(1, $sql)->param(':start', $start)->param(':end', $end)->param(':interval', $interval)->param(':money', $money)->execute()->as_array();
        $minusUsers = [];


        $user = 0;
        foreach ($data as $row) {
            if ($user != $row['user_id']) {
                if (isset($minusUsers[$user])) {
                    $minusUsers[$user]['all'] = round($all, 2);
                }
                $minusCount = 0;
                $all = 0;
                $info = '';
            }
            $all += $row['amount'];
            $user = $row['user_id'];
            if ($row['amount'] <= 0) {
                $minusCount++;
                $info .= " " . round($row['amount']);
                if ($minusCount >= $try) {
                    if (!isset($minusUsers[$user])) {
                        $minusUsers[$user] = ['count' => $minusCount, 'info' => $info];
                    } else {
                        $minusUsers[$user]['count'] = max($minusUsers[$user]['count'], $minusCount);
                        $minusUsers[$user]['info'] .= " " . round($row['amount'], 0);

                    }
                }
            }

            if ($row['amount'] > 0) {
                $minusCount = 0;
                $info = '';
            }

        }

        foreach ($minusUsers as $user => $value) {
            $minusUsers[$user]['rtp'] = round($rtp[$user]['rtp'], 4);
            $minusUsers[$user]['betsCount'] = $rtp[$user]['count1'];
            if ($minusUsers[$user]['all'] > $moneyLimit || $minusUsers[$user]['rtp'] < $rtpLimit) {
                unset($minusUsers[$user]);
            }

        }

        foreach ($minusUsers as $user => &$u) {
            for ($day = $end - 60 * 60 * 24 * 9; $day <= $end; $day += 60 * 60 * 24) {
                $u['amount'][date('d-m-Y', $day)] = round($wins[$day . '-' . $user]['amount'] ?? 0, 0);
            }

        }

		//correct output
        foreach ($minusUsers as $user => &$u) {
            $u['10days']=implode(' ',$u['amount']);
            unset($u['amount']);
            $u['rtp']=($u['rtp']*100).'%';
        }

		function print_tg($a,$lvl=0){

            $str='';

            foreach ($a as $k => $b) {
                if (is_array($b)) {
                    $str .= str_pad('',$lvl+1,' ',STR_PAD_LEFT).$k . '=>[' . PHP_EOL;
                    $lvl=2;
                    $str.=print_tg($b,$lvl);
                    $str .= str_pad('',$lvl,' ',STR_PAD_LEFT).']'. PHP_EOL;
                } else {
                    $str .= str_pad('',$lvl+1,' ',STR_PAD_LEFT).$k . '=' . $b . PHP_EOL;
                }
            }
            return $str;
        }


        $strToTG='BAD USERS: '.count($minusUsers).PHP_EOL;

        $strToTG.=print_tg($minusUsers);

        $toS = ['331325323',
                '847393',
                '333168345',
                '371527172',
            ];
        foreach (str_split($strToTG, 1024) as $part) {
            foreach ($toS as $to){
                tg::send($to,$part,1);
                sleep(1);
            }
        }

    }
	
	
	   public static function promoCalc()
    {

        $keyProcess = 'promoCalc';

        /*if (!th::lockProcess($keyProcess)) {
            return null;
        }
*/
        $st = Status::instance();

        $lastTime = $st->get($keyProcess);

        //$time = mktime(0, 0, 0, 01, 22, 2024);

        $time = $lastTime + 60 * 60 * 24 -1;
		
		/*
        if ($time > time()) {
            throw new Exception("Time: $time > now " . time());
        }
*/
        $sql = "select *
                from events 
                where ends > :time
				and start < :now
                and type = 'promo'";

        $events = db::query(1, $sql)->param(':time', $time)->param(':now', time())->execute()->as_array();


        foreach ($events as $event) {

            $d = date('d', $time);
            $m = date('m', $time);
            $y = date('Y', $time);

            $start = mktime($event['h'], $event['m'], 0, $m, $d, $y);
            $end = $start + $event['duration'];
            $end = min($end, $event['ends']);


            $sql = 'select sum(amount) as in , sum(win) as out, count(id) as cnt,count( DISTINCT user_id) as users
                from bets
                where office_id=:oid
                and created>=	:start
                and created<= 	:end';

            $data = db::query(1, $sql)->param(':start', $start)
                ->param(':end', $end)
                ->param(':oid', $event['office_id'])
                ->execute()
                ->as_array();

            if (count($data) == 0) {
                continue;
            }

            $data = $data[0];

            $date = "$y-$m-$d";


            $sql = 'insert into statistic_events (
                  "event_id" ,  "created" ,  "date" ,  "office_id" ,  "in" ,  "out" ,  "count" ,  "users" ,  "calc")
                  VALUES (:eventId, :created,  :date,  :oid,  :in,  :out,  :count,  :users,  1)
                    ON CONFLICT On CONSTRAINT statistic_events_pkey do UPDATE
                        SET  "in" = EXCLUDED.in ,
                                        "out" = EXCLUDED.out ,
                                    "count" = EXCLUDED.count ,
                                    users = EXCLUDED.users ,
                                    calc = 1
                    ';


                

            db::query(Database::UPDATE, $sql)
                ->parameters([
                    ':eventId' => $event['id'],
                    ':created' => time(),
                    ':date' => $date,
                    ':oid' => $event['office_id'],
                    ':in' => $data['in'],
                    ':out' => $data['out'],
                    ':count' => $data['cnt'],
                    ':users' => $data['users']
                ])
                ->execute();


        }

        $st->set($keyProcess, $time);
        th::unlockProcess($keyProcess);


    }
	

      public static function sort()
    {


        $minCount = 10000;

        $time = time();

        $m = date('m', $time);
        $y = date('Y', $time);

        $end = mktime(0, 0, 0, $m, 1, $y) - 1;

        $m--;
        if ($m <= 0) {
            $m += 12;
            $y--;
        }

        $start = mktime(0, 0, 0, $m, 1, $y);

        $sql = 'select id, created, branded ,name
                from games
                order by created desc';

        $games = db::query(1, $sql)->execute()->as_array('name');

        foreach ($games as $name => $game) {

            if ($game['created'] < 100) {
                $games[$name]['created'] = $end - 1;
                $g = new Model_Game($game['id']);
                $g->created = $game['created'];
                $g->save();
            }

        }


        $sql = 'select o.id, o.created
                    from offices o ';

        $offices = db::query(1, $sql)->execute()->as_array('id');

        $sql = "select s.game,s.office_id, sum(s.count) as count
                from statistics s
                where date>=:start and date<=:end
                and game != 'jp'
                group by 1,2
                order by 2,3 desc";

        $statistics = db::query(1, $sql)->param(':start', date('Y-m-d', $start))
            ->param(':end', date('Y-m-d', $end))
            ->execute()
            ->as_array();

        $listNew = [];
        //Игры, которые не проработали более месяца ставятся первыми, сортировка по дате включения, по убыванию.
        foreach ($games as $game) {
            if ($game['created'] > $start) {
                $listNew[$game['name']] = $game;
            }
        }


        $listBrand = [];
        $list = [];
        $count = [];

        foreach ($statistics as $stat) {
            $officeId = $stat['office_id'];
            $gameName = $stat['game'];

            if (isset($count[$officeId])) {
                $count[$officeId] += $stat['count'];
            }
            else{
                $count[$officeId] = $stat['count'];
            }

            //Для owner, который существует менее месяца, применяется сортировка infin
            if ($offices[$officeId]['created'] > $start) {
                continue;
            }

            //Игры, которые не проработали более месяца ставятся первыми
            if (in_array($gameName, array_keys($listNew))) {
                continue;
            }

            //Брендированные игры идут в списке вторыми, сортировка по количеству ставок за прошлый месяц по убыванию.
            if ($games[$gameName]['branded'] == 1) {
                $listBrand[$officeId][$gameName] = $games[$gameName];
                continue;
            }

            $list[$officeId][$gameName] = $games[$gameName];

        }


        $createdInsert = time();
        foreach ($offices as  $office) {

            if (!isset($count[$office['id']]) || $count[$office['id']] < $minCount){
                continue;
            }

            $allGames = $games;
            $insert = [];
            $sort = 10000;
            foreach ($listNew as $game) {
                $insert[] = "( {$game['id']}, '{$game['name']}', {$office['id']}, $sort, 'new', $createdInsert )";
                $sort += 100;
                unset ($allGames[$game['name']]);
            }

            if (isset($listBrand[$office['id']]) && count($listBrand[$office['id']]) > 0) {
                foreach ($listBrand[$office['id']] as $game) {
                    $insert[] = "( {$game['id']}, '{$game['name']}', {$office['id']}, $sort, 'brand', $createdInsert )";
                    $sort += 100;
                    unset ($allGames[$game['name']]);
                }
            }

            foreach ($allGames as $key => $game) {
                if ($game['branded'] == 1) {
                    $insert[] = "( {$game['id']}, '{$game['name']}', {$office['id']}, $sort, 'lostBrand', $createdInsert )";
                    $sort += 100;
                }
                unset ($allGames[$key]);
            }

            if (isset($list[$office['id']]) && count($list[$office['id']]) > 0) {
                foreach ($list[$office['id']] as $game) {
                    $insert[] = "( {$game['id']}, '{$game['name']}', {$office['id']}, $sort, 'stat', $createdInsert )";
                    $sort += 100;
                    unset ($allGames[$game['name']]);
                }
            }

            foreach ($allGames as $game) {
                $insert[] = "( {$game['id']}, '{$game['name']}', {$office['id']}, $sort, 'lost', $createdInsert )";
                $sort += 100;
            }

            $insert = implode(",\r\n", $insert);
            $insert = "insert into games_sort  (game_id, game_name , office_id, sort, type, created) values \r\n" . $insert;
            echo $insert;
            db::query(Database::UPDATE, $insert)->execute();

        }

        database::instance()->begin();

        $sql = 'delete from games_sort where use=1';
        db::query(Database::UPDATE, $sql)->execute();

        $sql = 'update games_sort set use=1';
        db::query(Database::UPDATE, $sql)->execute();

        database::instance()->commit();


    }


}