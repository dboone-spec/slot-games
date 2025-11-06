<?php

class Service {


//bets to archive
public static function betArchive(){

    //days in bets
    $deep=10;

    //to seconds
    $deep*=60*60*24;
    $keyProcess='betarchive';


    if (!th::lockProcess($keyProcess)){
        return null;
    }

    //last day in archive
    $st=Status::instance();

    $fromDate=$st->get($keyProcess);

    $date=mktime(0, 0, 0, date('n'),date('j'),date('Y'))-$deep;


    if($date <= $fromDate){
        return null;
    }


    $txt='Days ';
    //from next in archive to now-deep
    for($i=$fromDate+60*60*24;$i<=$date;$i+=60*60*24){

        $name=date('Y',$i).'_'.date('m',$i).'_'.date('d',$i);

        $sql="alter table bets_$name no inherit bets ";
        db::query(Database::UPDATE, $sql)->execute();


        $sql="alter table bets_$name inherit bets_archive ";
        db::query(Database::UPDATE, $sql)->execute();

        $txt.=" $name";

    }

    $st->set($keyProcess,$date);
    $txt.=' moved to archive';

    th::techAlert($txt);
    th::unlockProcess($keyProcess);



}




public static function statisticLocal(){


    //days in bets
    $deep=1;

    //to seconds
    $deep*=60*60*24;

    $keyProcess='statlocal';

    if (!th::lockProcess($keyProcess)){
        return null;
    }

    //last day in archive
    $st=Status::instance();

    $fromDate=$st->get($keyProcess);

    $date=mktime(0, 0, 0, date('n'),date('j'),date('Y'))-$deep;

    if($date <= $fromDate){
        return null;
    }

    $days=[];
    for($i=$fromDate+60*60*24;$i<=$date;$i+=60*60*24){
        $days[]=date('Y-m-d',$i);
    }



    //from next in archive to now-deep
    $fromDate-=60*60*24;

    $sql="insert into statistics_local (
                                date,type, game,bettype,office_id,
                                                 amount_in,
                                                 amount_out,count ,game_id
                )

        select namedate(b.created,o.zone_time) as date,b.game_type as type, b.game,  b.type as bettype,b.office_id,
        sum(CASE When ( (b.game in ('acesandfaces','jacksorbetter','tensorbetter') ) and (b.type in ('free', 'normal', 'norcfs','norafs','frecfs','frecfs' ) )     )then 0 ELSE b.amount END) as amount_in ,
        sum(b.win) as amount_out, count(b.id) as \"count\", b.game_id
        from bets b
        join offices o on o.id=b.office_id
        where b.created>=:start
            and namedate(b.created,o.zone_time) in :days
        GROUP BY 1,2,3,4,5,9
        ON CONFLICT do nothing;";


    db::query(Database::DELETE, $sql)->param(':start',$fromDate)
                                    ->param(':days',$days)
                                    ->execute();



    $sql=" insert into  statistics_local (
           date, type,game,bettype,office_id,
					 amount_in,
					 amount_out,count,game_id
                )

        select namedate(b.created,o.zone_time) as date,b.game_type as type, b.game,  type as bettype,b.office_id,
                            sum(b.amount) as amount_in ,
                            0, count(b.id) as \"count\", b.game_id
        from pokerbets b
        join offices o on o.id=b.office_id
        where b.created>=:start
            and namedate(b.created,o.zone_time) in :days
        GROUP BY 1,2,3,4,5,9
        ON CONFLICT On CONSTRAINT statistics_t1_pkey do UPDATE
        SET amount_in=EXCLUDED.amount_in,
		\"count\"=EXCLUDED.count";

    db::query(Database::DELETE, $sql)->param(':start',$fromDate)
                                    ->param(':days',$days)
                                    ->execute();


    $st->set($keyProcess,$date);

    $txt='LOCAL statistic collects for days '.implode(', ',$days);
    th::techAlert($txt);
    th::unlockProcess($keyProcess);



}


/**
     * собирает все ставки по 1003 клубу за вчерашний день и отправляет на указанную почту вложением
     */

    public static function bets1003(){
        $sql="select b.id, b.user_id, b.office_id,c.code,  b.info,  b.amount as rate, b. win, b.balance-b.win+b.amount as balance_before, b.balance as balance_after, b.come as lines,g.visible_name,
                    vdate(b.created+o.zone_time*60*60)
                    from bets b
                    join offices o on b.office_id=o.id
                    join currencies c on o.currency_id=c.id
                    join games g on g.id=b.game_id
                    where b.office_id=1003
                    and b.created>= EXTRACT( epoch from date_trunc('day', now() at time zone 'utc' ) )-24*60*60-o.zone_time*60*60
                    and b.created< EXTRACT( epoch from date_trunc('day', now() at time zone 'utc' ) )-o.zone_time*60*60";

        $data=db::query(1,$sql)
            ->execute()->as_array();

        /**
         * поля в заголовок csv-файла
         */

        $fields=[
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

        $csv='';

        $csv.=implode(';',$fields).PHP_EOL;

        foreach($data as $row) {
            $csv.=implode(';',$row).PHP_EOL;
        }

        $date=date('Y-m-d',strtotime("-1 day"));

        /**
         * отправка письма. работает через Swift
         */

        Email::send('arijus@supawayja.com',['no-reply@site-domain.com','site-domain.com'],'report','report '.$date,false,$csv,'report_'.$date);

        logfile::create(date('Y-m-d H:i:s') . PHP_EOL . $csv, '1003csv');
    }


public static function statisticUsers(){

    //days in bets
    $deep=1;

    //to seconds
    $deep*=60*60*24;

    $keyProcess='statUsers';


    if (!th::lockProcess($keyProcess)){
        return null;
    }


    //last day in archive
    $st=Status::instance();

    $fromDate=$st->get($keyProcess);

    $date=mktime(0, 0, 0, date('n'),date('j'),date('Y'))-$deep;

    if($date <= $fromDate){
        return null;
    }


    $days=[];
    for($i=$fromDate+60*60*24;$i<=$date;$i+=60*60*24){
        $day=date('Y-m-d',$i);
        $nameday=date('Y_m_d',$i);
        $sql="insert into  statistic_users (date,office_id,users)
                select  :day, office_id, count(distinct user_id) as users
                    from bets_$nameday
                    group by 1,2";


        db::query(Database::DELETE, $sql)->param(':day',$day)
                                    ->execute()
                                    ;

        $days[]=$day;

    }



    $sql="with nu as (select namedate(created) as created,office_id,count(id) as newusers
            from users
            where namedate(created) in :days
                    and created>=:created
            group by 1,2)

            insert into  statistic_users (date,office_id,newusers)
            select created,office_id,newusers
            from nu
            ON CONFLICT On CONSTRAINT statistic_users_pkey do UPDATE SET newusers = EXCLUDED.newusers; ";

    db::query(Database::DELETE, $sql)->param(':days',$days)
                                    ->param(':created',$fromDate-60*60*24)
                                    ->execute();

    $st->set($keyProcess,$date);
    $txt='USER statistic collects for days '.implode(', ',$days);
    th::techAlert($txt);
    th::unlockProcess($keyProcess);

}


public static function usersMH(){
    $from=status::instance()->usersMH;
    $now=time();

    if ($now-$from < 60*60*24*31.5){
        return;
    }

    $m=date('m',$from);
    $y=date('Y',$from);

    $m++;
    if ($m>12){
        $m=1;
        $y++;
    }

    $to= mktime(0,0,0, $m, 1, $y );


    $sql='delete from users_month_history where date=:date';
    db::query(Database::DELETE,$sql)->param(':date',$from)->execute();


    $sql="insert into users_month_history (user_id,date,game,amount,win,count)

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

    DB::query(4, $sql)->param(':from',$from)->param(':to',$to)->execute();

    status::instance()->usersMH=$to;


    //th::ceoAlert("Users' bets collect for $m $y");
    echo "Users' bets collect for $m $y";



}


public static function parseCurrencyRateByDate($date){

    $date=date('Y-m-d',$date);


    $url='https://www.xe.com/currencytables/?from=EUR&date='.$date;

    $p=new Parser();
    $p->get($url);
    $data=$p->html()->find('#table-section',0)->find('table',0) ;
    $rows=$data->find('tr');

    $data=[];
    foreach ($rows as $row) {
        $name = $row->find('th', 0)->plaintext;
        if ($name == 'Currency') {
            continue;
        }
        $value = $row->find('td', 2)->plaintext;
        $value = str_replace(',', '', $value);
        $data[$name] = $value;
    }

    return $data;

}


public static function monthStat($m=null,$y=null){

    if (empty($y)) {
        $y = date('Y', time());
    }

    if (empty($m)){
        $m=date('m',time());
    }

    $dateForDB=$date=mktime(0,0,0,$m,1,$y);
    $date=$date-60*60*24;

    $data=static::parseCurrencyRateByDate($date);

    foreach ($data as $name=>$value){

        $sql='insert into currency_rates (date,currency,value)
                values(:date,:currency,:value)
                ON CONFLICT (date,currency) do
					update
				    set value=:value';

        db::query(Database::UPDATE,$sql)->param(':date',$dateForDB)
                        ->param(':currency',$name)
                        ->param(':value',$value)
                        ->execute();

    }







}


public static function updateNullCurrencies($source='vertbet'){

    $url='https://currency.world/exchange_rates/EUR/';

    $currencies = db::query(1,'select code from currencies where source=:source')
        ->param(':source',$source)
        ->execute()->as_array();

    $p=new Parser();
    $p->UseComp();

    $time=time();

    foreach($currencies as $curr) {

        //vertbet cryptocurrency
        if($curr['code']=='CBET') {
            $pc=new Parser();
            $req=$pc->get('https://api.coingecko.com/api/v3/coins/cbet-token/history?date='.date('d-m-Y',strtotime("-1 days")));
            if($req) {
                $json=json_decode($req,1);
                $sql='update currencies set val=:value, updated=:updated where code=:currency';

                db::query(Database::UPDATE,$sql)
                    ->param(':currency',$curr['code'])
                    ->param(':value',"".($json['market_data']['current_price']['eur']))
                    ->param(':updated',$time)
                    ->execute();
            }

            continue;
        }

        echo $url.$curr['code'].PHP_EOL;
        $res=$p->get($url.$curr['code']);
        if($res) {
            $html=$p->html();
            $rate = $html->find('.main_info .num',1);

            if($rate) {
                echo 'ok'.PHP_EOL;
                $sql='update currencies set val=:value, updated=:updated where code=:currency';

                db::query(Database::UPDATE,$sql)
                    ->param(':currency',$curr['code'])
                    ->param(':value',"".(1/$rate->plaintext))
                    ->param(':updated',$time)
                    ->execute();
            }
        }
    }

}
public static function updateCurrencies($source='agt'){

    $time=strtotime("-1 days");

    $data=static::parseCurrencyRateByDate($time);

    foreach ($data as $name=>$value){

        $sql='update currencies set val=:value, updated=:updated where code=:currency and source=:source';

        db::query(Database::UPDATE,$sql)
                        ->param(':currency',$name)
                        ->param(':value',$value)
                        ->param(':source',$source)
                        ->param(':updated',$time)
                        ->execute();

    }
}

public static function getCurrencyRatesVertbet($date_int){

    $date=date('Y-m-d',$date_int);

    $result_rates=[];
    var_dump($date);

    //https://currency.world/historical_data/EUR_pre_2022-09-20.tsv

    $urlEUR='https://currency.world/historical_data/EUR_pre_'.date('Y-m-d',$date_int+60*60*24).'.tsv';

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
    }

    $other_currencies = db::query(1,'SELECT c.code 
                FROM currencies c
                where c.code not in(select currency from currency_rates where date=1664582400) and c.code not in :exclude')
        ->param(':exclude',['FUN','LAT','LVL','BYR','mLTC',
            'µBTC','ICX','XB2','PNT','LTL','XB3',
            'MRO','DEC','SIT','BUSD','USDT','PRB','CFA','BNB','DAI','SHIB','TRX','TUSD','USDC'])
	->execute()->as_array();

    foreach($other_currencies as $currency) {

	if($currency['code']=='CBET') {
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
        }

        //парсим каждую историю валют отдельно
        $urlCurr='https://currency.world/historical_data/'.$currency['code'].'_pre_'.date('Y-m-d',$date_int+60*60*24).'.tsv';

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
	if(!isset($ratesCurr[$date])) {
		var_dump($ratesCurr,$date,$currency['code']);
			exit;
	}
        $result_rates[$currency['code']]="".($ratesCurr[$date]/$ratesCurr[$date]);
    }

    return $result_rates;

}

}