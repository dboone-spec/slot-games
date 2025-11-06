<?php

class Model_Event extends ORM {

    protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_serialize_columns = array('games_ids','extra_params');

    protected $_belongs_to = [
	    'user' => [
		    'model'		 => 'user',
		    'foreign_key'	 => 'user_id',
	    ],
        'office' => [
            'model'		 => 'office',
            'foreign_key'	 => 'office_id',
        ]
    ];

    private function _now() {
        return time();
    }

    public function duration() {
        $d=$this->duration ?? 0;

        return date('H:i',$d);
    }

    public function time_to_collect() {
        $d=$this->time_to_collect ?? 0;

        return date('H:i',$d);
    }

    public function gameList($full=false) {
        $games_ids=$this->games_ids;
        if(empty($games_ids)) {
            return [];
        }
        $dbgames=db::query(1,'select id,name,visible_name from games where id in :ids')
            ->param(':ids',$games_ids)
            ->execute();
        if(!$full) {
            return $dbgames->as_array('id','visible_name');
        }

        return $dbgames->as_array();
    }

	/**
     * Округляет в соответствии с валютой размер ставки для LS
     * @param $currency_val
     * @param $currency_mult
     * @return float|int
     */
    public function getValueForLS($currency_val,$currency_mult,$def_amount_eur=0.02) {

        $one_amount = $def_amount_eur/$currency_val;

        $min=round($one_amount);

        if($min<=0) {
            $min=round($one_amount*pow(10,$currency_mult),-1)/pow(10,$currency_mult);
        }
        else {
            $min=round($min,-strlen($min)+1);
        }

        if($min<=0) {
            $min=round($one_amount*pow(10,$currency_mult))/pow(10,$currency_mult);
        }

        return $min;
    }

    /**
     * Проверяет, идет в данное время событие или нет
     * @return  boolean
     */

    public function checkEventIfReady() {
        if(!$this->loaded()) {
            return false;
        }

        if(!$this->active) {
            return false;
        }

        $time=$this->_now();
        $timeStart=$this->startTime();


        if($time<$timeStart || $time>($this->duration+$timeStart)) {
            return false;
        }

        //доп. проверка
        if($time<$this->starts || $time>$this->ends) {
            return false;
        }

        /*if($this->m>=0 && date('i',$time)!=$this->m) {
            var_dump('7');
            return false;
        }

        if($this->h>=0 && date('H',$time)!=$this->h) {
            var_dump('8');
            return false;
        }*/

        if(strpos($this->dow,',')!==false) {
            $dows = explode(',', $this->dow);
            $find=false;
            foreach($dows as $dow) {
                if((int) date('w',$time)==(int) $dow) {
                    $find=true;
                }
            }

            return $find;
        }
        else {
            if($this->dom>=0 && (int) date('d',$time)!=(int) $this->dom) {
                return false;
            }

            if($this->mon>=0 && (int) date('m',$time)!=(int) $this->mon) {
                return false;
            }

            if($this->dow>=0 && (int) date('w',$time)!=(int) $this->dow) {
                return false;
            }
        }

        return true;
    }

    /**
     * проверка на возможность выплаты по событию. здесь не проверяется период time_to_collect
     * @param Model_Game $game
     * @param Model_User $user
     * @return $this|false
     */
    public function canPay(Model_Game $game,Model_User $user) {
        if($this->type!='promo') {
            return false;
        }

        if($this->startTime()+$this->duration>$this->_now()) {
            return false;
        }
		
		if(empty($this->calc) || $this->calc<=0) {
            return false;
        }

        if(date('dmy',$this->calc)!=date('dmy')) {
            return false;
        }

        return $this;
    }

	public function isNewPlayer($user) {
        if($this->type=='progressive' && (int) $user->last_bet_time<strtotime('monday this week')) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет, может ли игрок учавствовать в акции заданной игры
     * @param   Model_Game   $game  модель игры
     * @param   Model_User   $user  модель игрока
     * @return  boolean
     */

    public function canJoin(Model_Game $game,Model_User $user) {

        if(!$this->checkEventIfReady()) {
            return false;
        }

        $available_games=$this->games_ids;

        if(!in_array($game->id,$available_games)) {
            return false;
        }

        if(!in_array($game->type,['slot','moon','shuffle'])) {
            return false;
        }

		if(!!$this->office->check_new_ls && $this->isNewPlayer($user)) {
            return false;
        }

        //проверяем, не играл ли он уже
        //todo повесить индекс на event_id или составной
//        $fs_history=new Model_Freespinhistory(['event_id'=>$this->id,'user_id'=>$user_id,'src'=>'lucky']);

        //внутри функции устанавливается fs_count
        $isPlayed=$this->isNotPlayed($user);

        if($isPlayed && $this->type=='progressive') {
            $wager=$this->wager();

            $playeddays = $this->getPlayedDaysCount($this->fs_count);

            if($playeddays==0) {
                $wager=0;
				
				if($user->office->ls_first_wager>0) {
                    $wager=$user->office->ls_first_wager;
                }
            }

            if($wager>0 && $wager>$this->madeBetsSum($user->id)) {
                return false;
            }
        }

        return $isPlayed;
    }

    /**
     * @param $user_id
     * @return int
     */
    public function getProgressiveFScount($user_id) {

        $progressive_map=$this->extra_params;

        if(date('w',$this->_now())=='1') {
            return $progressive_map[0];
        }

        $monday=strtotime('last Monday');
        $cnt=db::query(1,'select count(fs.id) from freespins_history fs 
                                where user_id=:user_id and event_id=:eid
                                and created>=:created')
            ->param(':user_id',$user_id)
            ->param(':created',$monday)
            ->param(':eid',$this->id)
            ->execute();

        return $progressive_map[$cnt[0]['count']] ?? 0;
    }

    public function getPlayedDaysCount($fs_count) {
        $i = array_search($fs_count,$this->extra_params);

        return $i>=0?$i:0;
    }

    public static $_last_collect_fs;
    public static $_last_collect_fs_count=0;

    /**
     * @param Model_User $user
     * @param $checkFuture
     * @return bool
     * @throws Kohana_Exception
     */
    public function isNotPlayed(Model_User $user,$checkFuture=false) {

        if($this->type=='promo') {
            return $user->promo_started!==0;
        }

        $fs_history_model=new Model_Freespinhistory();
        $fs_history=$fs_history_model
            ->where('user_id','=',$user->id)
            ->where('event_id','=',$this->id);
			
		if($this->type!='progressive') {
			$fs_history->where('event_end_time','>',$this->_now());
		}
			
		$fs_history=$fs_history->order_by('created','desc')
            ->find();

        if(!$fs_history->loaded()) {

            if($this->type=='progressive') {
                $this->fs_count=$this->getProgressiveFScount($user->id);
            }

            return true;
        }

        self::$_last_collect_fs=$fs_history->created;
        self::$_last_collect_fs_count=$fs_history->fs_count;

        if($this->once) {
            return false;
        }

        //если прошла неделя, то разрешаем еще раз
        if($this->type=='dayweek' && ($fs_history->created+Date::WEEK)<time()) {
            return true;
        }

        if($this->type=='progressive' && $checkFuture && date('Y-m-d',$fs_history->created)==date('Y-m-d',$this->_now())) {
            $this->dow=date('w',strtotime('tomorrow'));
            $this->fs_count=$this->getProgressiveFScount($user->id);
            return true;
        }

        if($this->type=='progressive' && date('Y-m-d',$fs_history->created)!=date('Y-m-d',$this->_now())) {
            $this->fs_count=$this->getProgressiveFScount($user->id);
            return true;
        }

        return false;
    }

    public function wager() {
        return self::$_last_collect_fs_count*$this->fs_amount*3;
    }

    public function madeBetsSum($user_id) {

	    $u=new Model_User($user_id);

        if(!$u->loaded()) {
            throw new Exception('internal error: user not found ['.$user_id.']');
        }
		
		if(self::$_last_collect_fs<time()-Date::WEEK && empty($u->office->ls_first_wager)) {
			return 0;
			throw new Exception('err');
		}

		//todo удалить условие, и оставить на постоянку после 15 августа или спустя неделю
		
        if(time()>=mktime(0,0,0,8,17,2023)) {
            return $u->ls_wager;
        }

        $bets=db::query(1,'select coalesce(sum(amount),0) as sum from bets 
                                where user_id=:user_id and is_freespin=0
                                and created>=:created')
            ->param(':user_id',$user_id)
            ->param(':created',self::$_last_collect_fs)
            ->execute();


        logfile::create(date('Y-m-d H:i:s') . ' [ls wager bets sum '.$user_id.'] '.self::$_last_collect_fs.
            ' ('.date('Y-m-d H:i:s',self::$_last_collect_fs).') '.
            'from bets: '.$bets[0]['sum'].'; from users: '.$u->ls_wager.PHP_EOL, 'lswager');

        return $bets[0]['sum'];
    }

    /**
     * Определяет когда начнется событие
     * Пока работает с днями недели
     * @return integer seconds
     */
    public function startTime() {
        $minutes=$this->m>=0?$this->m:0;
        $hours=$this->h>=0?$this->h:0;

        /*if($this->dom>=0) {
            $day=$this->m;
            return strtotime('next '.date('l',$this->dow));
        }*/

        if($this->dow>=0) {
			
			if(strpos($this->dow,',')!==false) {
                $dows=explode(',',$this->dow);

                $times=[];

                foreach($dows as $dow) {

                    if($dow!=date('w',$this->_now())) {
                        $times[]=strtotime('next '.$this->days_of_week[$dow])+$hours*60*60+$minutes*60;
                    }
                    elseif($this->_now()<=mktime($hours,$minutes,0,date('m',$this->_now()),date('d',$this->_now()),date('Y',$this->_now()))+$this->duration+$this->time_to_collect) {
                        $times[]=mktime($hours,$minutes,0,date('m',$this->_now()),date('d',$this->_now()),date('Y',$this->_now()));
                    }
                }

                return min($times);
            }
			
            if($this->dow!=date('w',$this->_now())) {
                return strtotime('next '.$this->days_of_week[$this->dow])+$hours*60*60+$minutes*60;
            }
            return mktime($hours,$minutes,0,date('m',$this->_now()),date('d',$this->_now()),date('Y',$this->_now()));
        }

        if($this->type=='promo') {

            $start=mktime($hours,$minutes,0,date('m',$this->_now()),date('d',$this->_now()),date('Y',$this->_now()));
//
//            if(($start+$this->duration+$this->time_to_collect)<$this->_now()) {
//                $start+=Date::DAY;
//            }

            return $start;
        }

        return $this->_now();
    }

    public $days_of_week=[
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ];

    public function randomGames($office_id) {
        $random_count=mt_rand(3,8);
        $sql = 'select g.id as game_id
                from games g
                join office_games og on g.id=og.game_id
                where g.show=1 and g.branded=0 and og.office_id = :oid and og.enable=1
                and g.name not in :cant
                and g.type in :types
                ';

        $res=db::query(1,$sql)
            ->param(':oid',$office_id)
            ->param(':cant',th::$_strict_for_FSback)
            ->param(':types',['slot','moon','shuffle'])
            ->execute()
            ->as_array('game_id');

        $game_ids=array_keys($res);

        shuffle($game_ids);
        array_splice($game_ids,$random_count);

        return $game_ids;
    }
	
	public static function updateStats($data) {
        $all_keys=[
            'event_id',
            'created',
            'date',
            'office_id',
            'in',
            'out',
            'count',
            'users',
            'promo_out',
            'promo_count',
            'max_promo_out',
            'max_promo_count',
            'cancel_count',
            'calc',
        ];

        $keys=[];
        $db_keys=[];


        foreach($all_keys as $k) {
            $keys[':'.$k]=0;
            $db_keys[]='"'.$k.'"';

            if(isset($data[$k])) {
                $keys[':'.$k]=$data[$k];
            }
        }

        $sql='insert into statistic_events('.implode(',',$db_keys).') values('.implode(',',array_keys($keys)).') on conflict(event_id,date,office_id) do update set ';

        $updates=[];

        foreach($data as $k=>$v) {

            if(in_array($k,['created','event_id','office_id','date'])) {
                continue;
            }

            $updates[]=$k.'=statistic_events.'.$k.'+:'.$k;
        }

        $sql.=implode(',',$updates);

        db::query(database::UPDATE,$sql)
            ->parameters($keys)
            ->execute();
    }
	
	public function getDowName() {
        if(strpos($this->dow,',')!==false) {
            $dows=explode(',',$this->dow);
            return implode(',',arr::map(function($el) {return $this->days_of_week[$el];},$dows));
        }
        if($this->dow<0) {
            return 'day';
        }
        return $this->days_of_week[$this->dow]??'';
    }

}

