<?php

    class Model_Jackpot extends ORM
    {

        protected $_table_name = "jackpots";
        protected $_table_columns = array(
            "id" => array(
                "type" => "int",
                "min" => "-2147483648",
                "max" => "2147483647",
                "column_name" => "id",
                "column_default" => "nextval('jackpots_id_seq'::regclass)",
                "is_nullable" => FALSE,
                "data_type" => "integer",
                "character_maximum_length" => NULL,
                "numeric_precision" => "32",
                "numeric_scale" => "0",
                "datetime_precision" => NULL
            ),
            "office_id" => array(
                "type" => "int",
                "min" => "-2147483648",
                "max" => "2147483647",
                "column_name" => "office_id",
                "column_default" => NULL,
                "is_nullable" => FALSE,
                "data_type" => "integer",
                "character_maximum_length" => NULL,
                "numeric_precision" => "32",
                "numeric_scale" => "0",
                "datetime_precision" => NULL
            ),
            "last_win_time" => array(
                "type" => "int",
                "min" => "-2147483648",
                "max" => "2147483647",
                "column_name" => "last_win_time",
                "column_default" => NULL,
                "is_nullable" => FALSE,
                "data_type" => "integer",
                "character_maximum_length" => NULL,
                "numeric_precision" => "32",
                "numeric_scale" => "0",
                "datetime_precision" => NULL
            ),
            "user_id" => array(
                "type" => "int",
                "min" => "-2147483648",
                "max" => "2147483647",
                "column_name" => "user_id",
                "column_default" => NULL,
                "is_nullable" => FALSE,
                "data_type" => "integer",
                "character_maximum_length" => NULL,
                "numeric_precision" => "32",
                "numeric_scale" => "0",
                "datetime_precision" => NULL
            ),
            "type" => array(
                "type" => "int",
                "min" => "-32768",
                "max" => "32768",
                "column_name" => "type",
                "column_default" => '1',
                "is_nullable" => FALSE,
                "data_type" => "integer",
                "character_maximum_length" => NULL,
                "numeric_precision" => "32",
                "numeric_scale" => "0",
                "datetime_precision" => NULL
            ),
            "active" => array(
                "type" => "int",
                "min" => "-32768",
                "max" => "32768",
                "column_name" => "active",
                "column_default" => '1',
                "is_nullable" => FALSE,
                "data_type" => "integer",
                "character_maximum_length" => NULL,
                "numeric_precision" => "32",
                "numeric_scale" => "0",
                "datetime_precision" => NULL
            ),
            "prev_value" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "prev_value",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
            "min_bet" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "min_bet",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
            "max_bet" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "max_bet",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
            "min_value" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "min_value",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
            "max_value" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "max_value",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
            "procent" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "procent",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
            "hot_percent" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "hot_percent",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "4",
                "numeric_scale" => "4",
                "datetime_precision" => NULL
            ),
            "value" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "value",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
            "current" => array(
                "type" => "float",
                "exact" => TRUE,
                "column_name" => "current",
                "column_default" => NULL,
                "is_nullable" => TRUE,
                "data_type" => "numeric",
                "character_maximum_length" => NULL,
                "numeric_precision" => "12",
                "numeric_scale" => "2",
                "datetime_precision" => NULL
            ),
			"trigger_bet_id" => array(
                'type' => 'int',
                'min' => '-9223372036854775808',
                'max' => '9223372036854775807',
                'column_name' => 'trigger_bet_id',
                'column_default' => '1',
                'is_nullable' => false,
                'data_type' => 'bigint',
                'character_maximum_length' => NULL,
                'numeric_precision' => '64',
                'numeric_scale' => '0',
                'datetime_precision' => NULL,
            ),
        );
        protected $_belongs_to = array(
            'office' => array(
                'model' => 'office',
                'foreign_key' => 'office_id',
            ),
        );
//        protected $_load_with = array('office');

        public function labels()
        {
            return array(
                "game" => __("Игра"),
                "office_id" => __("ППС"),
                "type" => __("№ п/п"),
                "min_bet" => __("Минимальная ставка накопления"),
                "max_bet" => __("Максимальная ставка накопления (Включительно)"),
                "min_value" => __("Минимальный джекпот"),
                "max_value" => __("Максимальный джекпот"),
                "procent" => __("% возвращаемой выручки от прибыли"),
                "value" => __("Выпадение"),
                "oborot" => __("Оборот"),
                "active" => __("Активен"),
                "profit" => __("Расчетный доход"),
                "avg" => __("Средний джекпот"),
                "count" => __("Кол-во джекпотов за 30 дней"),
                "donation" => __("Отчисление в джекпот в %"),
                "sum" => __("Сумма джекпота в месяц"),
                "current" => __("Текущий джекпот"),


                "values_range" => __("Диапазон выпадения джекпота (мин-макс)"),
                "bet_range" => __("Диапазон ставок, при которых идет отчисление в джекпот (мин-макс)"),
                "trigger_range" => __("Диапазон ставок, при которых выпадает джекпот (мин-макс)"),
            );
        }

        public function filters()
        {
            return array(
                "office_id" => array(
                    array('intval'),
                ),
                "type" => array(
                    array('intval'),
                ),
                "min_bet" => array(
                    array('floatval'),
                ),
                "max_bet" => array(
                    array('floatval'),
                ),
                "min_value" => array(
                    array('floatval'),
                ),
                "max_value" => array(
                    array('floatval'),
                ),
                "value" => array(
                    array('floatval'),
                ),
            );
        }

        public function minMaxBet($var)
        {

            if (($var >= 1) AND ( $var <= 10))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public static function isRange($var)
        {
            return true;

            if (($var >= 1) AND ( $var <= 10))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public function rules()
        {
            return parent::rules();

            return array(
                "office_id" => array(
                    array('not_empty'),
                ),
                'procent' => array(
                    array('not_empty'),
                    array('Model_Jackpot::isRange', array(':value'))
                ),
                'min_bet' => array(
                    array('not_empty'),
                ),
                'max_bet' => array(
                    array('not_empty'),
                    array(function(Validation $object) {
                            $data = $object->data();
                            if ($data['min_bet'] >= $data['max_bet'])
                            {
                                $object->error('max_bet', __('Неверные значения минимальной и максимальной ставки'));
                            }
                        }, array(':validation')
                    ),
                    array(function(Validation $object) {
                            $data = $object->data();
                            $max_bet = db::query(1, 'select id from jackpots '
                                            . 'where office_id = :office_id '
                                            . 'and ((min_bet < :min_bet and type > :type) or (max_bet > :max_bet and type < :type))')
                                    ->param(':office_id', $data['office_id'])
                                    ->param(':min_bet', $data['max_bet'])
                                    ->param(':max_bet', $data['min_bet'])
                                    ->param(':type', $data['type'])
                                    ->execute()
                                    ->as_array();

                            if (count($max_bet) > 0)
                            {
                                $object->error('max_bet', __('Минимальные и максимальные ставки в разных джекпотах не могут пересекаться'));
                            }
                        }, array(':validation')
                    ),
                ),
            );
        }

        public function save(\Validation $validation = NULL)
        {
            $isempty=true;
            foreach($this->object() as $f=>$v) {
                if($f=='office_id' || $f=='type') {
                    continue;
                }
                if(!is_null($v)) {
                    $isempty=false;
                    break;
                }
            }
            if(!$isempty) {
                parent::save($validation);
            }
        }

        public function rand_value() {
            return mt_rand($this->min_value,$this->max_value);
        }

        public function hot() {
            return $this->current>=$this->value*0.9;
        }

        public function jp_show_time() {
            return 30;
        }

        public function win($win_arr) {

            $win=$win_arr['win'];
            $types=$win_arr['types'];

            if($win<=0) {
                return 0;
            }

            $sql_nextval = <<<SQL
                Select nextval('bets_id_seq')
SQL;
            $bet_id = key(db::query(1, $sql_nextval)->execute()->as_array('nextval'));

            $bet = new Model_Bet();
            $bet->id = $bet_id;
            $bet->user_id = auth::$user_id;
            $bet->amount = 0;
            $bet->game_type = 'jp';
            $bet->game = 'jp'.game::session()->game;
            $bet->type = 'normal'; //normal, free game, bonus game, double
            $bet->come = null;
            $bet->result = 'win';
            $bet->office_id = auth::user()->office_id;
            $bet->win = $win;
            $bet->game_id = 0;
            $bet->method = 'jp'.$types;
            $bet->is_freespin = false;
            $bet->balance = auth::user()->amount(true);//amount + bonus
            $bet->fg_level = 0;
            $bet->save();

            $sql='update users
				set amount=amount+:win,
				sum_win=sum_win+:win
				';

            $sql.='where id=:uid';

            db::query(1, $sql)->param(':win', (float) $win)
                    ->param(':uid', auth::$user_id)
                    ->execute()
                    ->as_array();
        }

        public static function calcAllJPs($amount,Model_Office $office) {
            $kToJp = empty($office->k_to_jp) ? 0.005 : $office->k_to_jp;

            $toBegin=0.51;

            $o=[0.25,0.25,0.25,0.25];

            $all=[];
            foreach($o as $num=>$s) {
                $all[]+=$amount*$o[$num]*(1-$toBegin)*$kToJp;
            }

            return $all;
        }

        public static function calcNewValJP($amount,Model_Office $office) {
            return array_sum(self::calcAllJPs($amount,$office));
        }

        public function donateagtredis($amount,$office_id, $betID) {


            if($amount<=0) {
                return ['win'=>0,'types'=>'','values'=>[]];
            }

            $office = Office::instance($office_id)->office();

            $kToJp = empty($office->k_to_jp) ? 0.005 : $office->k_to_jp;
            $kMaxLvl = empty($office->k_max_lvl) ? 1 : $office->k_max_lvl;

            $game = game::session()->game;

            $redis = dbredis::instance();
            $redis->select(1);

            //можно начислять в ДП
            $jpactive = (bool) $redis->get('jpa-'.$office_id);

            if(!$jpactive) {
                return ['win'=>0,'types'=>'','values'=>[]];
            }

            $jpStartTime = (int) $redis->get('jpStartTime-'.$office_id);

            if($jpStartTime>0 && time()<$jpStartTime) {
                return ['win'=>0,'types'=>'','values'=>[]];
            }

            $toBegin=0.51;

            $multiplayer=$office->currency->mult ?? 2;

            //отчисления на jp sum($o)==1
            $o=[0.25,0.25,0.25,0.25];
            //макисмальная сумма срабатывания jp
            $maxLevel=[20,50,100,250]; //хранится в бд, поле max_value

            foreach($maxLevel as &$mx) {
                $mx = $mx*$kMaxLvl;
            }

            //сумма начала накопления jp
            $startJp=0.1;  //хранится в бд, поле min_value
            $endJP=0.8;
            //сумма начала срабатывания jp
            $startPay=0.2;

            $jpCurrentTrigger = [
                    $this->triggerRandomVals($maxLevel[0]*$startPay,$maxLevel[0],null,$multiplayer),
                    $this->triggerRandomVals($maxLevel[1]*$startPay,$maxLevel[1],null,$multiplayer),
                    $this->triggerRandomVals($maxLevel[2]*$startPay,$maxLevel[2],null,$multiplayer),
                    $this->triggerRandomVals($maxLevel[3]*$startPay,$maxLevel[3],null,$multiplayer)
            ];

            $jpCurrentValues=[];

            if(!$redis->setNx('jp-'.$office_id,json_encode($jpCurrentTrigger))) {
                $jpCurrentTrigger = json_decode($redis->get('jp-'.$office_id));

                for($i=0;$i<4;$i++) {
                    $jpCurrentValues[$i]=$redis->get('jps-'.$office_id.'-'.$i);
                }
            }
            else {
                $jpCurrentValues=[$maxLevel[0]*$startJp,$maxLevel[1]*$startJp,$maxLevel[2]*$startJp,$maxLevel[3]*$startJp];
                for($i=0;$i<4;$i++) {
                    $redis->setNx('jps-'.$office_id.'-'.$i,$jpCurrentValues[$i]);
                }
            }

            $win=0;

            $win_jps = $jpCurrentValues;

            $jpNumWin=-1;

            foreach($jpCurrentValues as $num=>&$s){
                $newVal = $amount*$o[$num]*(1-$toBegin)*$kToJp;
                $redis->incrByFloat('jps-'.$office_id.'-'.$num,$newVal);
                $s+=$newVal;

                if ($s>=$jpCurrentTrigger[$num]){

                    if($jpNumWin<0) {

                        $jc = new jpcard();
                        $cards = array_values($jc->gencards());
                        $redis->setNx('jpcards-'.$office_id,json_encode($cards));
                        $redis->setNx('alljpcards-'.$office_id,json_encode($cards));
                        $redis->setNx('currjpcards-'.$office_id,json_encode([]));
                        $level = $jc->level($cards);
                        $jpNumWin = $jc->getJPNum($level);

                        $redis->set('jpWinNum-'.$office_id,$jpNumWin);
                        $redis->set('jpTriggerNum-'.$office_id,$num);
                        $redis->set('jpTriggerSum-'.$office_id.'-'.$num,$jpCurrentTrigger[$num]);
                        $redis->set('jpTriggerTime-'.$office_id,time());

                        logfile::create(date('Y-m-d H:i:s').' ['.auth::$user_id.'] '.' num: '.$num.'; newVal: '.$newVal.'; jpNumWin: '.$jpNumWin.'; trigger before: '.print_r($jpCurrentTrigger,1).'; win_jps: '.print_r($win_jps,1).'; bet_id: '.$betID,'jpwin');

                        if($jpNumWin==4) {
                            $winValue = array_sum($win_jps);
                        }
                        else {
                            $winValue = $win_jps[$jpNumWin];
                        }

                        $redis->set('jpWinSum-'.$office_id,$winValue);

                        if($jpNumWin==4) {
                            foreach($maxLevel as $n=>$lvl) {
                                //новое накопление jp
                                $jpCurrentValues[$n]=$this->mt_rand_with_precision($lvl*$startJp,$lvl* $endJP,$multiplayer );

                                //новая сумма срабатывания jp
                                $jpCurrentTrigger[$n]=$this->triggerRandomVals($jpCurrentValues[$n]+$lvl*$startJp,$lvl,$lvl,$multiplayer);
                            }
                        }
                        else {
                            //новое накопление jp
                            $jpCurrentValues[$jpNumWin]=$this->mt_rand_with_precision($maxLevel[$jpNumWin]*$startJp,$maxLevel[$jpNumWin]* $endJP,$multiplayer );

                            //новая сумма срабатывания jp
                            $jpCurrentTrigger[$jpNumWin]=$this->triggerRandomVals($jpCurrentValues[$jpNumWin]+$maxLevel[$jpNumWin]*$startJp,$maxLevel[$jpNumWin],$maxLevel[$jpNumWin],$multiplayer);

                            //если выпавший джекпот не текущий
                            if($num!=$jpNumWin){
                                //генерим новую сумму срабатывания
                                $jpCurrentTrigger[$num]+=$this->triggerRandomVals(100/pow(10,$multiplayer),$maxLevel[$num],null,$multiplayer);
                            }
                        }

                        logfile::create(date('Y-m-d H:i:s').' ['.auth::$user_id.'] trigger after: '.print_r($jpCurrentTrigger,1),'jpwin');

                        $win=1;
                    }
                }
                else {
                    $hot=$redis->get('jpHotPercent-'.$office_id.'-'.$num);

                    if (((1-$hot) * $jpCurrentTrigger[$num])<=$s) {
                        if($redis->setNx('jpHotStart-'.$office_id.'-'.$num,time())) {
                            $redis->set('jpHotStartSum-'.$office_id.'-'.$num,((1-$hot) * $jpCurrentTrigger[$num]));
                            logfile::create(date('Y-m-d H:i:s').' ['.auth::$user_id.'] '.' num: '.$num.'; JPHOT: '.$s.'; trigger '.print_r($jpCurrentTrigger,1),'jpwin');
                        }
                    }
                }
            }


            if ($win && $redis->setNx('jpBlock-'.$office_id,1)){
                $redis->set('jpa-'.$office_id,(int) !$win);
                $redis->set('jp-'.$office_id,json_encode($jpCurrentTrigger));
                if($jpNumWin==4) {
                    foreach($jpCurrentValues as $n=>$v){
                        $redis->set('jps-'.$office_id.'-'.$n,$v);
                    }
                }
                else {
                    $redis->set('jps-'.$office_id.'-'.$jpNumWin,$jpCurrentValues[$jpNumWin]);
                }

                $redis->set('jpUser-'.$office_id,auth::$user_id);
                $redis->set('jpGame-'.$office_id,$game);
                $redis->set('jpTime-'.$office_id,time());
				$redis->set('jpTriggerBetId-'.$office_id,$betID);

                foreach($win_jps as $k=>$jp) {
                    db::query(Database::UPDATE,'update jackpots set current=:c where type=:t and office_id=:o_id')
                            ->param(':c',$jp)
                            ->param(':t',$k+1)
                            ->param(':o_id',$office_id)
                            ->execute();
                }


                $redis->select(0);
                return ['win'=>$win,'types'=>'','values'=>$win_jps];
            }

            //на выходе переключаем на бд по умолчанию
            $redis->select(0);
            return ['win'=>0,'types'=>'','values'=>$win_jps];

        }

        public function mt_rand_with_precision($min,$max,$precision=2) {
            $val = mt_rand($min*pow(10,$precision),$max*pow(10,$precision));
            $val = round($val/pow(10,$precision),$precision);
            return $val;
        }

//https://site-domain.local/api23dev/launch/icecream100?currency=BNB&game-id=icecream100&lang=en-US&session-token=8a240058-3e03-4111-ba51-89968e381ece&user-id=60099691651553604_BRL
        public function triggerRandomVals($min,$max,$notHigher=null,$precision=2) {
            $val = mt_rand($min*pow(10,$precision),$max*pow(10,$precision));
            if(!empty($notHigher)) {
                $val = min($val,$notHigher*pow(10,$precision));
            }
            $val = round($val/pow(10,$precision),$precision);
            return $val;
        }


        public function donateagt($amount,$office_id,$betID)
        {

            return $this->donateagtredis($amount,$office_id,$betID);

            //отчисления на jp sum($o)==1
            $o=[0.25,0.25,0.25,0.25]; //хранится в бд, поле procent
            //макисмальная сумма срабатывания jp
            $maxLevel=[20,50,100,250]; //хранится в бд, поле max_value
            //сумма начала накопления jp
            $startJp=0.1;  //хранится в бд, поле min_value
            ///////////////////////////////////////////////////////////

            $user_id=auth::$user_id;

            $toBegin=0.33;
            //вероятности выпадения jp
            $v=[61,23,9,6];

            //сумма начала срабатывания jp
            $startPay=0.2;

            //jp в соответствии с вероятностью
            $jpNumWin = $this->getRandWeight($v)+1;

            $donate_value = "($amount * j.procent * (1 - $toBegin))";

            $sql = "update jackpots j set "
                    . "current=case when j.type=$jpNumWin AND j.value<=$donate_value+j.current then random()*(j.max_value-(j.min_value*j.max_value)) else $donate_value+j.current end, "
                    . "value=case when j.value<=$donate_value+j.current then "
                    . "case when j.type=$jpNumWin then random()*(j.max_value-($startPay*j.max_value)) else (random()*(j.max_value-($startPay*j.max_value)))+j.value end "
                    . "else j.value end, "
                    . "prev_value=case when j.type=$jpNumWin AND j.value<=$donate_value+j.current then j.value else j.prev_value end "
                    . "where office_id = {$office_id} and active = 1"
                    . "RETURNING j.current,j.value,j.type,case when j.type=$jpNumWin AND j.value<=$donate_value+j.current then 1 else 0 end as win";


            $db = Database::instance();
            $q = $db->direct_query($sql);

            if ($q)
            {
                $win=0;
                foreach($q as $v) {
                    if($v['win'] != '0') {
                        $win=1;
                        break;
                    }
                }
                return ['win'=>$win,'types'=>''];
            }
            return [];
        }

        public function getRandWeight($weight,$count = 1)
        {

            if(count($weight) < $count)
            {
                throw new Exception('Не достаточное количество элементов в массиве ');
            }

            $result = [];
            for($i = 1; $i <= $count; $i++)
            {

                $sum  = array_sum($weight);
                $rand = mt_rand(0,$sum);

                $ts = 0;
                foreach($weight as $key => $w)
                {
                    $ts += $w;

                    if($ts >= $rand)
                    {
                        $result[] = $key;
                        unset($weight[$key]);
                        break;
                    }
                }
            }

            if($count == 1)
            {
                return $result[0];
            }


            shuffle($result);
            return $result;
        }

        public function donate($amount,$office_id,$betID)
        {

            if(PROJECT==1) {
                return $this->donateagt($amount,$office_id,$betID);
            }

            $user_id=auth::$user_id;
            $win = 0;
            $types='';
            if ($amount > 0)
            {
                $sql = "update jackpots j set "
                        . "value = case when j.min_trigger<={$amount} AND j.max_trigger>={$amount} AND ({$amount}*(j.procent/100))+j.current >= j.value "
                                . "then random()*(j.max_value-j.min_value)+j.min_value "
                                . "ELSE j.value END, "
                        . "current = case when ({$amount}*(j.procent/100))+j.current >= j.value AND j.min_trigger<={$amount} AND j.max_trigger>={$amount}"
                                  . "then 0 when j.min_bet<={$amount} AND j.max_bet>={$amount} then ({$amount}*(j.procent/100))+j.current else j.current end, "
                        . "prev_value = case when j.min_trigger<={$amount} AND j.max_trigger>={$amount} AND ({$amount}*(j.procent/100))+j.current >= j.value "
                                     . "then j.current else j.prev_value end, "
                        . "last_win_time = case when j.min_bet<={$amount} AND j.max_bet>={$amount} AND ({$amount}*(j.procent/100))+j.current >= j.value then extract('epoch' from CURRENT_TIMESTAMP) else j.last_win_time end, "
                        . "user_id = case when j.min_bet<={$amount} AND j.max_bet>={$amount} AND ({$amount}*(j.procent/100))+j.current >= j.value then {$user_id} else j.user_id end "
                        . "where office_id = {$office_id} and active = 1 "
                        . "RETURNING case when j.current > 0 then 0 else j.prev_value end, type";

                $db = Database::instance();
                $q = $db->direct_query($sql);

                if ($q)
                {
                    foreach($q as $w) {
                        $win+=$w['prev_value'];
                        if($w['prev_value']>0) {
                            $types.=$w['type'];
                        }
                    }
                }
            }

            return ['win'=>$win,'types'=>$types];
        }

        public function toFile($a=[],$office_id) {
            $path = realpath(DOCROOT.'../'.'www') .DIRECTORY_SEPARATOR . "jp";
            if( !is_dir($path)) {
                mkdir($path, 02777);
                chmod($path, 02777);
            }

            $path .= DIRECTORY_SEPARATOR.'data';

            if( !is_dir($path)) {
                mkdir($path, 02777);
                chmod($path, 02777);
            }
            $path .= DIRECTORY_SEPARATOR.$office_id;

            $a['updated']=time();

            file_put_contents($path,json_encode($a));
        }

    }
