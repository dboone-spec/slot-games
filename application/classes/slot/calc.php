<?php

//версия без автоматической обрабоки фриспинов. В режим фриспинов вводить вручную
abstract class Slot_Calc extends math {

    public $barcount = 5;
    //высота барабанов в символах
    public $heigth;
    public $bars = [];
    public $barFree=[];
    protected $name;
    public $game_id;
    protected $group;
    protected $config;
    public $pos = [];
    public $lines = [];
    protected $lineCount;
    protected $pay;
    protected $pay_rule;
    protected $anypay;
    public $wild;
    protected $wild_multiplier;
    protected $wild_except;
    public $scatter;
    protected $free_games;
    protected $free_multiplier;
    protected $bonus;
    protected $bonus_param;
    protected $free_mode;
    protected $multiplier;
    protected $free_multiplier_mode;
    protected $replaceWild;
    //общая ставка
    public $amount = 0;
    public $amount_line = 0;
    public $cline = 0;
    public $win = [];
    public $win_all = 0;
    //сколько будет выиграно в бонус игре (не входит в win_all)
    public $bonus_win = 0;
    public $freerun = 0;
    public $isFreerun;
    public $bonusrun = 0;
    public $bonusdata=[];
    //для букофра и может других
    public $extra_param;
    //всего выиграно на фри спинах
    public $total_win_free = 0;
    //осталось халявных вращений
    public $freeCountAll = 0;
    //сыграно халявных вращений
    public $freeCountCurrent = 0;
    //выигрыш получен путем вращения можно запускать бонус игру
    public $canDouble = false;
    //выигрыш по линиям
    public $LineSymbol = [];
    //выигрыш по линиям c wild 0/1
    public $LineUseWild = [];
    //длина комбинации в линии
    public $LineWinLen = [];

    public $replaced_symbols_in_bar=[];

    public $currency_mult=2;

    public $bettype = 'normal';

    public $isFreespin = false;
    public $isFreespinFromApi = false;
    public $isLuckyFreespin = false;
    public $isFreespinFromGame = false;
    public $isBonusMode = false;
    public $replaceBar=[];

    public $needRestoreFG=false;
	
	public $is_buy=false;

    public function fillLines() {
        foreach ($this->config['lines'] as $num => $line) {
            for ($x = 0; $x < $this->barcount; $x++) {
                for ($y = 0; $y < $this->heigth; $y++) {
                    if ($line[$y][$x] > 0) {
                        $this->lines[$num][] = $x + $y * $this->barcount + 1;
                    }
                }
            }
        }
    }

    public function gameId() {
        return $this->game_id;
    }


    public function forceBars(){

    }

    protected function initBars(){

    }

    public function __construct($group, $name) {

        $this->group = $group;
        $this->name = $name;
        $this->config = Kohana::$config->load("$group/$name");

        //to all games
        if(isset($this->config['bonus_double'])) {
            $this->config['bonus_double']='all';
        }

        if (defined('DEMO_MODE') && !DEMO_MODE){

            if(in_array(auth::user()->office_id,OFFICES_TEST_MODE) && (OFFICES_TEST_MODE_GAMES[0]=='*' || in_array($this->name,OFFICES_TEST_MODE_GAMES))) {
                $this->config['scatter']=[];
            }

            $this->game_model = ORM::factory('Game')
                ->where('provider','=','our')
                ->where('brand', '=', $group)
                ->where('name', '=', $name)
                ->find();

            $this->game_id = $this->game_model->id;

        }

        $this->currency_mult = auth::user()->office->currency->mult;

        $this->heigth= arr::get($this->config,'heigth',3);
        $this->initBars();
        $this->fillLines();



        $this->config['defaults'] = Kohana::$config->load($group);

        $this->lineCount = count($this->lines);
        $this->pay = $this->config['pay'];
        $this->pay_rule = arr::get($this->config, 'pay_rule', 'left');
        if (!is_array($this->pay_rule)) {
            $this->pay_rule = [$this->pay_rule];
        }

        $this->anypay = arr::get($this->config, 'anypay', []);
        $this->wild = arr::get($this->config, 'wild', []);
        if (!is_array($this->wild)) {
            $this->wild = [$this->wild];
        }
        $this->wild_multiplier = arr::get($this->config, 'wild_multiplier', 1);
        $this->wild_except = arr::get($this->config, 'wild_except', []);
        if (!is_array($this->wild_except)) {
            $this->wild_except = [$this->wild_except];
        }

        $this->scatter = arr::get($this->config, 'scatter', -1);
        if (!is_array($this->scatter)) {
            $this->scatter = [$this->scatter];
        }

        $this->bonus = arr::get($this->config, 'bonus', -1);
        if (!is_array($this->bonus)) {
            $this->bonus = [$this->bonus];
        }
        $this->bonus_param = arr::get($this->config, 'bonus_param', [0, 0, 0, 0, 0, 0]);

        $this->free_games = arr::get($this->config, 'free_games', [0, 0, 0, 0, 0, 0]);
        $this->free_multiplier = arr::get($this->config, 'free_multiplier', 1);
        $this->free_mode = arr::get($this->config, 'free_mode', 'sum');
        $this->free_multiplier_mode = arr::get($this->config, 'free_multiplier_mode', 'simple');

        $this->replaceBar= arr::get($this->config, 'replace_bar', []);
        if (!is_array($this->replaceBar)){
            $this->replaceBar=[$this->replaceBar];
        }

        $this->replaceWild=arr::get($this->config, 'replaceWild', false);


        $this->multiplier = 1;

    }

    //корректирует позицию барабана, если позиция находится вне допустимого диапазона
    public function correct_pos() {

        foreach ($this->pos as $num => $pos) {
            $c = count($this->bars[$num]);
            if ($pos > $c - 1) {
                $this->pos[$num] -= $c;
            }

            if ($pos < 0) {
                $this->pos[$num] += $c;
            }
        }
    }

    public function SetFreeSpinMode($from_api=false,$from_lucky=false,$from_game=false) {
        if(((int) $from_api + (int) $from_lucky + (int) $from_game)>1) {
            throw new LogicException('can not be FS from diff sources');
        }
        $this->isFreespin=true;
        $this->isFreespinFromApi=$from_api;
        $this->isLuckyFreespin=$from_lucky;
        $this->isFreespinFromGame=$from_game;
    }

    public function SetBonusMode() {
        $this->isBonusMode=true;
    }

	public function multiplier() {
        return $this->multiplier;
    }

    public function clearCurrentFreeCount() {

        //total to play FG count
        $key='freeCountTotal'.auth::$user_id.'-'.$this->name;
        dbredis::instance()->set($key, 0, array ('ex' => Game_Session::$session_time));

        //current played FG count
        $key2='freeCountAll'.auth::$user_id.'-'.$this->name;
        dbredis::instance()->set($key2, 0, array ('ex' => Game_Session::$session_time));
		
		//logfile::create(date('Y-m-d H:i:s') . ' ['.auth::$user_id.']','clearCurrentFreeCount: '.$this->name);
        //logfile::create(date('Y-m-d H:i:s') . ' ['.auth::$user_id.']'.PHP_EOL.print_r(debug_backtrace(0,7),1),'clearCurrentFreeCountTrace: '.$this->name);
    }

    public function getTotalFreeCount() {
        $key='freeCountTotal'.auth::$user_id.'-'.$this->name;

        dbredis::instance()
            ->set($key, (int) game::data('freeCountAll', 0), array ('nx', 'ex' => Game_Session::$session_time));

        return (int) dbredis::instance()
            ->get($key);
    }

    public function setTotalFreeCount($all) {
        $key='freeCountTotal'.auth::$user_id.'-'.$this->name;

        dbredis::instance()
            ->set($key, (int) $all, array ('ex' => Game_Session::$session_time));
			
		//logfile::create(date('Y-m-d H:i:s') . ' ['.auth::$user_id.']','setTotalFreeCount: '.$all.'; '.$this->name);
        //logfile::create(date('Y-m-d H:i:s') . ' ['.auth::$user_id.']'.PHP_EOL.print_r(debug_backtrace(0,7),1),'setTotalFreeCountTrace: '.$this->name);
    }

    public function getCurrentFreeCount($all) {
		
		if($all<=0) {
            $this->clearCurrentFreeCount();
            throw new Exception('freerun disabled');
        }
		
        $key='freeCountAll'.auth::$user_id.'-'.$this->name;

        //todo поменять game::data('freeCountCurrent', 0) на 0
        dbredis::instance()
            ->set($key, (int) game::data('freeCountCurrent', 0), array ('nx', 'ex' => Game_Session::$session_time));

        /*
         * функция вызывается в самом начале ставк
         * Увеличиваем сразу счетчик на будущее
         * при этом отдаем $next-1 для совместимости со старым кодом
         */

        $next=dbredis::instance()->incr($key);

        if($next>$all) {
            throw new Exception('freerun disabled, next: '.$next.'; all: '.$all);
        }

        return $next-1;
    }

    public function SetFreeRunMode() {
        $this->bars=$this->barFree;
        $this->isFreerun=true;
        $this->bettype='free';
        $this->total_win_free = game::data('total_win_free', 0);
        $this->freeCountAll = $this->getTotalFreeCount();
        $this->freeCountCurrent = $this->getCurrentFreeCount($this->freeCountAll);

        $this->amount = game::data('amount');
        $this->cline = game::data('lines');
        $this->amount_line = $this->amount / $this->cline;
        $this->multiplier = game::data('multiplier', $this->free_multiplier);
    }

    public function canDouble() {
        $this->canDouble = false;
        if ($this->win_all > 0 and $this->bonusrun == 0) { //убрал условие на выпадение фригеймов, т.к. в этот момент еще горит кнопка "РИСК" и можно удвоить.
            $this->canDouble = true;
        }

        if($this->freeCountAll>0 && $this->freeCountCurrent>0 && ($this->total_win_free+$this->win_all)>0) {
            $this->canDouble = true;
        }

        if($this->isFreespin || $this->isFreerun || $this->freerun>0) {
            $this->canDouble = false;
        }
    }

    protected $_dev_symbol=0;
    protected $_dev_symbol_count=0;


    public function bet($mode = null) {

        $amount = $this->amount;
        $no = [];
        if($this->isFreerun) {
            $amount = 0;
            $no[]=6;
        }
        else {
            $this->clearCurrentFreeCount();
        }

		if($this->is_buy) {
            $no=[6];
        }

        $error = bet::error($amount, $no, $this->isFreespin);
        if ($error > 0) {
            return $error;
        }

        $data = [];

        if(in_array(auth::user()->office_id,OFFICES_TEST_MODE) && (OFFICES_TEST_MODE_GAMES[0]=='*' || in_array($this->name,OFFICES_TEST_MODE_GAMES))) {
            $this->_dev_symbol = game::data('_dev_symbol',0);
            $this->_dev_symbol_count = game::data('_dev_symbol_count',0);
            $this->_dev_symbol_count++;

            if($this->_dev_symbol_count>count($this->bars)) {
                $this->_dev_symbol_count=1;
                $this->_dev_symbol++;
            }

            if($this->_dev_symbol>=count($this->pay)) {
                $this->_dev_symbol=0;
            }
            $data['_dev_symbol']=$this->_dev_symbol;
            $data['_dev_symbol_count']=$this->_dev_symbol_count;
        }


        $i = 0;
        $firstWin=-1;
        $exit = false;
        $min = PHP_INT_MAX;

        if($this->needRestoreFG) {

            $bet_start = new Model_Bet(game::data('start_bet_id'));
            $res = array_values(json_decode($bet_start->result,1));

            $bars=[];
            $pos=[];

            for($i=1;$i<=count($this->bars);$i++) {
                $bars[$i]=arr::flatten([$this->bars[$i],$this->bars[$i]]);
                $cmb=[];
                for($y=0;$y<$this->heigth;$y++) {
                    $cmb[]=$res[($i-1)+$y*count($this->bars)];
                }
                $pos[$i] = strpos(implode('',$bars[$i]),implode('',$cmb));
            }

            $this->pos = $pos;
            $this->win();

            $method = 'restore';

        }
        else {
            $needZero=bet::needZero();

            do {

                $this->spin($mode);
                if ($i==0){
                    $firstWin=$this->win_all + $this->bonus_win;
                }

                if($needZero){
                    if ($this->win_all==0){
                        $exit = true;
                    }
                }
                else{
                    if (bet::HaveBankAmount($this->win_all + $this->bonus_win,$this->game_id)) {
                        $exit = true;
                    }
                }


                //минимально возможный выигрыш
                if ($this->win_all < $min) {
                    $min = $this->win_all;
                    $pos = $this->pos;
                    $method = $needZero ? 'zero' : 'bank';
                }

                //нет вариантов
                if ($i >= 50) {
                    //закат солнца вручную
                    $this->pos = $pos;
                    $this->win();
                    $exit = true;
                    $method = 'hand';
                    continue;
                }
                $i++;
            } while (!$exit);
        }

        $bet['amount'] = $amount;
        $bet['come'] = $this->cline;
        $bet['result'] = json_encode($this->sym());
        $bet['office_id'] = OFFICE;
        $bet['win'] = $this->win_all;
        $bet['game_id'] = $this->game_id;
        $bet['method'] = $i > 1 ? $method : 'random';
        $bet['is_freespin'] = (int) $this->isFreespin + (int) $this->isFreespinFromApi;
        if($this->isLuckyFreespin) {
            $bet['is_freespin']=3;
        }
        if($this->isFreespinFromGame) {
            $bet['is_freespin']=4;
        }
        $bet['firstWin'] = $firstWin;
        $bet['is_last_freespin']=false;
        $bet['last_freespin_id']=-1;


        $this->canDouble();

        $data['win'] = $this->win_all;
        $data['amount'] = $this->amount;
		if($this->is_buy) {
            $data['amount'] = $this->amount/$this->config['bonus_buy_price'];
        }
        $data['lines'] = $this->cline;
        $data['comb'] = array_values($this->sym());
        $data['can_double'] = (int) $this->canDouble;
        $data['can_bonus'] = 0;
        $data['freeCountAll'] = 0;

        $data['first_bet'] = $this->win_all;



        if ($this->isFreerun){
            $this->total_win_free += $this->win_all;
            $this->freeCountCurrent++;

            if($this->bonusrun) {
                $this->freeCountCurrent--;
            }
        }
        else {
            $this->total_win_free=0;
        }

        //выиграли freespin

        $bet['free_games_started'] = false;
        if($this->freerun>0 && $this->freeCountAll==0) {
            $this->extra_param = math::random_int(0,8);
            $data['extra_param'] = $this->extra_param;
            $bet['free_games_started'] = true;
        }

        $data['fg_level']=game::data('fg_level',0);
        $data['fg_first_bet_id']=game::data('fg_first_bet_id',false);

        if ($this->freerun > 0) {
            if(!$this->isFreerun && $this->config['bonus_double']=='all') { //если старые игры и выиграли первые фри спины
                $this->total_win_free+=$this->win_all;
            }

            $data['fg_level']=game::data('fg_level',0)+1;

            //freespin выиграли во время freespin
            if ($this->isFreerun) {
                if ($this->free_mode == 'sum') {
                    $this->freeCountAll+=$this->freerun;
                }
                else {
                    $this->freeCountAll=$this->freerun;
                }

                if ($this->free_multiplier_mode == 'inc') {
                    $this->free_multiplier++;
                }
            }
            //freespin выиграли в обычном режиме
            else{
                $this->freeCountAll=$this->freerun;
            }

            if($this->freeCountAll==$this->freerun) { //если первые фриспины
                $data['freegames_start_from']=(auth::user(true)->amount>0)?'amount':'bonus';
            }

            $this->setTotalFreeCount($this->freeCountAll);
        }

        $data['total_win_free'] = $this->total_win_free;
        $data['freeCountCurrent'] = $this->freeCountCurrent;
        $data['freeCountAll'] = $this->freeCountAll;
        $data['multiplier'] = $this->free_multiplier;

        //нужно запускать бонус
        if ($this->bonusrun > 0) {
            $data['can_bonus'] = 1;
            $data['bonusdata'] = array_values($this->bonusdata);
            $data['bonusrun'] = $this->bonusrun;
            $data['bonusPay'] = $this->bonusPay;
        }
        $data['win_per_line']=$this->win;
        $data['step']=0; //TODO добавил, чтобы при любом вращении скидывался гембл

        if (!$this->isFreerun){
            if(!$this->freerun)
            {
                //clear bonus game
                $data['fg_level'] = 0;
                $data['freeCountCurrent'] = 0;
                $data['freeCountAll']     = 0;
                $data['total_win_free']   = 0;

                $this->clearCurrentFreeCount();
            }
            else
            {
                $data['comb_before_fg'] = array_values($this->sym());
                $data['comb_scatter_position'] = -1;

                $ch_arr=[];

                foreach($data['comb_before_fg'] as $i=>$scatter) {
                    if(in_array($scatter,$this->scatter)) {
                        $ch_arr[]=$i;
                    }
                }

                $data['comb_scatter_position'] = $ch_arr[math::array_rand($ch_arr)];

            }
        }

        $data['li'] = $this->cline;
        $data['lv']  = $this->win;
        $data['lm']  = $this->lightingLine();

        $data['extracomb'] = $this->extrasym();

        if($this->needRestoreFG) {
            return;
        }

        $bet['info']='';
		$bet_info=[];

        if($this->isFreespin) {

            $checkGame=$this->game_id;

			if(in_array(auth::user()->api,['9','12'])) {
                $checkGame=false;
            }

            $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);

            if(!empty($fs->uuid)) {
                $exFS=explode('-',$fs->uuid);
                unset($exFS[count($exFS)-1]);
                $bet['fs_uuid']=implode('-',$exFS);
            }

            if($fs->fs_offer_type=='infingift') {
                $bet['send_api']=false;
            }

			


            if($this->bettype=='normal') {

                $bet_info[]=$fs->getTypeName().';'.($fs->fs_played+1).'/'.$fs->fs_count;

                $isLastFS=!$fs->spinOneFreespin($fs->id,$this->win_all);

                if($fs->fs_offer_type=='infingift' && $isLastFS) {
                    $bet['send_api']=true;
                    $bet['is_last_freespin']=$isLastFS;
                    $bet['last_freespin_id']=$fs->id;
                }
				
				if($fs->fs_offer_type=='betconst') {
                    $bet['last_freespin_id']=$fs->id;
                }
				
                if($fs->fs_offer_type=='softgaming') {
                    $bet['last_freespin_id']=$fs->id;
                }
				
				if($fs->fs_offer_type=='pinup') {
					$bet['is_last_freespin']=$isLastFS;
                    $bet['last_freespin_id']=$fs->id;
                }
				
				if($fs->fs_offer_type=='softswiss' && $isLastFS) {
                    $bet['send_api']=true;
                    $bet['is_last_freespin']=true;
                    $bet['last_freespin_id']=$fs->id;
                }
            }
            elseif($this->bettype=='free' && $this->win_all>0) {
                $fs->updateSum($fs->id,$this->win_all);
            }
            //здесь списываются фриспины
        }
        elseif(auth::user()->promo_started!==0) {
            //если это не фриспины и игрок не отказался от турнира, учавствует в нем.
            $events=auth::user()->checkEvents($this->game_model);
            if($events && $events->type=='promo') {
                //если игрок не видел окна, активируем его участие в турнире
                auth::user()->joinEvent($this->game_model,$events);
            }
        }

		if(isset($this->minibarsRate) && $this->minibarsRate>1) {
            $bet_info[]="x{$this->minibarsRate}";
        }

        if($this->bettype=='free') {
            $bet_info[]='FG: '.$this->freeCountCurrent.'/'.$this->freeCountAll;
			$bet_info[]='bet: '.$this->amount;
            if ($this instanceof Slot_Agt_Pharaoh){
                $bet_info[]="({$this->extra_param})";
            }

            $bet['free_games_end'] = false;
            if($this->freeCountAll==$this->freeCountCurrent) {
                $bet['free_games_end'] = true;

                //последний фригейм? тогда удаляем счетчик
                $data['fg_level'] = 0;
                $data['freeCountCurrent'] = 0;
                $data['freeCountAll']     = 0;
                $data['total_win_free']   = 0;

                $this->clearCurrentFreeCount();
            }
        }
		
		$bet['is_buy']=0;
        if($this->is_buy) {
            $bet_info[]='BUY';
            $bet['is_buy']=1;
        }
		$bet['info']=implode('; ',$bet_info);


        bet::make($bet, $this->bettype, $data);
    }

    //вращаем
    public function spin($mode = null) {

        for ($i = 1; $i <= $this->barcount; $i++) {
            $this->pos[$i] = math::random_int(0, count($this->bars[$i]) - 1);
        }

        if ($mode=='free'){
            for($i=1;$i<=count($this->bars);$i++) {
                foreach($this->bars[$i] as $y=>$k) {
                    if($k==$this->scatter[0]) {
                        break;
                    }
                }
                $this->pos[$i] = $y;
            }
        }

        if(in_array(auth::user()->office_id,OFFICES_TEST_MODE) && (OFFICES_TEST_MODE_GAMES[0]=='*' || in_array($this->name,OFFICES_TEST_MODE_GAMES))) {
            for($i=1;$i<=$this->_dev_symbol_count;$i++) {
                foreach($this->bars[$i] as $y=>$k) {
                    if($k==$this->_dev_symbol) {
                        break;
                    }
                }
                $this->pos[$i] = $y-1;
            }
        }

//        $this->pos[1]=0;
//        $this->pos[2]=0;
//        $this->pos[3]=0;
//        $this->pos[4]=0;
//        $this->pos[5]=0;

        if ($mode=='lc'){
            $this->pos[1] = 3;
            $this->pos[2] = 20;
            $this->pos[3] = 22;
            $this->pos[4] = 0;
            $this->pos[5] = 3;
        }

        if ($mode=='scat'){
            $this->pos[1] = -1;
            $this->pos[2] = 3;
            $this->pos[3] = -1;
            $this->pos[4] = 6;
            $this->pos[5] = 9;
        }

        if (0 || $mode=='anypay'){
            for($i=1;$i<=count($this->bars);$i++) {
                foreach($this->bars[$i] as $y=>$k) {
                    if($k==$this->anypay[0] && mt_rand(0,1)==0) {
                        break;
                    }
                }
                $this->pos[$i] = $y-1;
            }
        }

        if (0 || $mode=='morefree'){
            for($i=1;$i<=5;$i++) {
                foreach($this->barFree[$i] as $y=>$k) {
                    if($k==$this->scatter[0]) {
                        break;
                    }
                }
                $this->pos[$i] = $y-1;
            }
        }

        if (0 || $mode=='wild'){
            for($i=1;$i<=count($this->barFree);$i++) {
                foreach($this->barFree[$i] as $y=>$k) {
                    if($k==2) {
                        break;
                    }
                }
                $this->pos[$i] = $y-1;
            }
        }

        if ($mode=='replace'){
            for($i=1;$i<=count($this->barFree);$i++) {
                foreach($this->barFree[$i] as $y=>$k) {
                    if($k==$this->extra_param) {
                        break;
                    }
                }
                $this->pos[$i] = $y-1;
            }
        }

        $this->correct_pos();
        $this->win();
    }

    public function sym($num = null) {


        if (empty($num)) {
            $r = [];
            for ($i = 1; $i <= $this->barcount * $this->heigth; $i++) {
                $r[$i] = $this->sym($i);
            }
            return $r;
        }



        $bar = $num % $this->barcount;
        if ($bar == 0) {
            $bar = $this->barcount;
        }


        $pos = $this->pos[$bar] + floor(($num - 0.01) / $this->barcount);

        if ($pos >= count($this->bars[$bar])) {
            $pos -= count($this->bars[$bar]);
        }
        return $this->bars[$bar][$pos];
    }

    //битовая маска участвующих в выигрыше символов
    //$num - номер линии
    //скаттеры anypay не участвуют тут
    //TODO пока поддерживается только pay_rule==left добавить остальные
    public function lightingLine($num = null) {

        if (is_null($num)) {
            for ($i = 0; $i <= $this->cline; $i++) {
                $a[$i] = $this->lightingLine($i);
            }
            return $a;
        }

        //scatter
        if ($num==0){
            $light=0;
            if ($this->win[0] > 0) {
                foreach ($this->sym() as $sym) {
                    $light = $light << 1;
                    if (in_array($sym, $this->anypay)) {
                        $light ++;
                    }
                }
            }
            return $light;
        }

        switch ($this->LineWinLen[$num]) {
            case 0: return 0;
            case 1: return 0b10000;
            case 2: return 0b11000;
            case 3: return 0b11100;
            case 4: return 0b11110;
            case 5: return 0b11111;
            case -1: return 0b00001;
            case -2: return 0b00011;
            case -3: return 0b00111;
            case -4: return 0b01111;
            case -5: return 0b11111;
        }

        return 0;
    }

    //freerun и bonusrun - это выпадение бонусов или фригеймов в результате спина.

    public function calcbonusgames($count) { //bonus run, каждый bonus считается вместе с другими
        $this->bonusrun = 0;
        $cb=0;
        foreach ($this->bonus as $sym) {
            if (isset($count[$sym])) {
                $cb+=$count[$sym];
            }
        }

        $this->bonusrun = $this->bonus_param[$cb];
    }

    public function calcfreegames($count) { //free run, все scatter складываются и работают как одинаковые
        $this->freerun = 0;
        $cf=0;
        foreach ($this->scatter as $sym) {
            if (isset($count[$sym])) {
                $cf+=$count[$sym];
            }
        }

        $this->freerun = $this->free_games[$cf];
    }

    //текущий выигрыш
    public function win() {

        $this->win_all = 0;
        $this->bonus_win = 0;
        $this->replaced_symbols_in_bar=[];

        $this->LineSymbol = array_fill(1, $this->lineCount, -1);
        $this->LineUseWild = array_fill(1, $this->lineCount, false);
        $this->LineWinLen = array_fill(1, $this->lineCount, 0);

        $this->win = array_fill(0, $this->lineCount+1, 0);
        //выигрыш по линиям
        for ($i = 1; $i <= $this->cline; $i++) {
            $this->win[$i] = $this->payLine($i) * $this->amount_line * $this->multiplier;
        }

        $count = array_count_values($this->sym());

        //anypay
        $this->win[0] = 0;
        foreach ($this->anypay as $sym) {
            if (isset($count[$sym])) {
                $this->win[0] += $this->pay($sym, $count[$sym]) * $this->amount * $this->multiplier;
            }
        }

        $this->win_all = array_sum($this->win);

        $this->correctMaxWin($this->win_all);

        $this->calcfreegames($count);
        $this->calcbonusgames($count);

        if ($this->bonusrun > 0) {
            $this->bonus_win=$this->calcbonus();
        }
    }

    public function calcbonus() {
        return 0;
    }

    public function pay($sym, $c) {
        $c=abs($c);
        return $this->pay[$sym][$c] ?? 0;
    }



    protected function pay3($comb){
        $win=['pay'=>0,'sym'=>-1,'useWild'=>false,'len'=>0];
        //ищем wild
        $wildPos=[];
        foreach($this->wild as $w){
            foreach(array_keys($comb,$w) as $pos){
                $wildPos[]=$pos;
            }
        }



        $simple=[0,1,2];
        foreach ($wildPos as $pos){
            unset($simple[$pos]);
        }

        //первый в линии символ, который не wild
        $posSym=count($simple)>0 ? min($simple) : -1;
        //если комбинация состоит не из одних wild
        if ($posSym>=0){

            $sym=$comb[$posSym];
            if (!in_array($sym,$this->anypay)){
                $comb1=$comb;
                //если wild действует на текущий символ
                if (!in_array($sym,$this->wild_except)){
                    foreach($wildPos as $pos){
                        $comb1[$pos]=$sym;
                    }
                }



                $len=0;
                $m=1;
                $useWild=false;

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2]){
                    $len=3;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

		if ($this->pay($sym,$len)>0){
		    $win=['pay'=>$this->pay($sym,$len)*$m,'sym'=>$sym,'useWild'=>$useWild,'len'=>$len];
		}

            }
        }

        //считаем wild
        $len=0;
        $wild=[];

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos)){
            $len=3;
            $wild=[$comb[0],$comb[1],$comb[2]];
        }


        foreach(array_unique($wild) as $sym){
            if ($this->pay($sym,$len)>$win['pay']){
                $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>false,'len'=>$len];
            }
        }

        return $win;

    }

    protected function payLeft6($comb){




        $win=['pay'=>0,'sym'=>-1,'useWild'=>false,'len'=>0];
        //ищем wild
        $wildPos=[];
        foreach($this->wild as $w){
            foreach(array_keys($comb,$w) as $pos){
                $wildPos[]=$pos;
            }
        }

        $simple=[0,1,2,3,4,5];
        foreach ($wildPos as $pos){
            unset($simple[$pos]);
        }

        //первый в линии символ, который не wild
        $posSym=count($simple)>0 ? min($simple) : -1;
        //если комбинация состоит не из одних wild
        if ($posSym>=0){

            $sym=$comb[$posSym];
            if (!in_array($sym,$this->anypay)){
                $comb1=$comb;

                //если wild действует на текущий символ
                if (!in_array($sym,$this->wild_except)){
                    foreach($wildPos as $pos){
                        $comb1[$pos]=$sym;
                    }
                }



                $len=1;
                $m=1;
                $useWild=false;
                if ($comb1[0]==$comb1[1]){
                    $len=2;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2]){
                    $len=3;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2] and $comb1[2]==$comb1[3]){
                    $len=4;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2] and $comb1[2]==$comb1[3] and $comb1[3]==$comb1[4]){
                    $len=5;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) or in_array(4,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2] and $comb1[2]==$comb1[3] and $comb1[3]==$comb1[4] and $comb1[4]==$comb1[5]){
                    $len=6;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) or in_array(4,$wildPos) or in_array(5,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if($this->pay($sym,$len)>$win['pay']){
                    $win=['pay'=>$this->pay($sym,$len)*$m,'sym'=>$sym,'useWild'=>$useWild,'len'=>$len];
                }

            }
        }

        //считаем wild
        $len=0;
        $wild=[];
        if (in_array(0,$wildPos) ){
            $len=1;
            $wild=[$comb[0]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos)){
            $len=2;
            $wild=[$comb[0],$comb[1]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos)){
            $len=3;
            $wild=[$comb[0],$comb[1],$comb[2]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos)){
            $len=4;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos) and in_array(4,$wildPos)){
            $len=5;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3],$comb[4]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos) and in_array(4,$wildPos) and in_array(5,$wildPos)){
            $len=6;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3],$comb[4],$comb[5]];
        }


        foreach(array_unique($wild) as $sym){

            if ($this->replaceWild===false){
                if ($this->pay($sym,$len)>0 and $this->pay($sym,$len)>=$win['pay'] and ! in_array($sym, $this->anypay)){
                    $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>false,'len'=>$len];
                }
            }
            else{
                if (in_array($sym,$this->wild)){
                    $sym=$this->replaceWild;
                }
                if ($this->pay($sym,$len)>0 and $this->pay($sym,$len)>=$win['pay'] ){
                    $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>false,'len'=>$len];
                }
            }
        }

        return $win;




    }




     protected function payLeft($comb){
        $win=['pay'=>0,'sym'=>-1,'useWild'=>false,'len'=>0];
        //ищем wild
        $wildPos=[];
        foreach($this->wild as $w){
            foreach(array_keys($comb,$w) as $pos){
                $wildPos[]=$pos;
            }
        }



        $simple=[0,1,2,3,4];
        foreach ($wildPos as $pos){
            unset($simple[$pos]);
        }

        //первый в линии символ, который не wild
        $posSym=count($simple)>0 ? min($simple) : -1;
        //если комбинация состоит не из одних wild
        if ($posSym>=0){

            $sym=$comb[$posSym];
            if (!in_array($sym,$this->anypay)){
                $comb1=$comb;
                //если wild действует на текущий символ
                if (!in_array($sym,$this->wild_except)){
                    foreach($wildPos as $pos){
                        $comb1[$pos]=$sym;
                    }
                }



                $len=1;
                $m=1;
                $useWild=false;
                if ($comb1[0]==$comb1[1]){
                    $len=2;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2]){
                    $len=3;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }

                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2] and $comb1[2]==$comb1[3]){
                    $len=4;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if ($comb1[0]==$comb1[1] and $comb1[1]==$comb1[2] and $comb1[2]==$comb1[3] and $comb1[3]==$comb1[4]){
                    $len=5;
                    $m=1;
                    $useWild=false;
                    if (!in_array($sym,$this->wild_except) ){
                        if (in_array(0,$wildPos) or in_array(1,$wildPos) or in_array(2,$wildPos) or in_array(3,$wildPos) or in_array(4,$wildPos) ){
                            $m=$this->wild_multiplier;
                            $useWild=true;
                        }
                    }
                }

                if($this->pay($sym,$len)>$win['pay']){
                    $win=['pay'=>$this->pay($sym,$len)*$m,'sym'=>$sym,'useWild'=>$useWild,'len'=>$len];
                }

            }
        }

        //считаем wild
        $len=0;
        $wild=[];
        if (in_array(0,$wildPos) ){
            $len=1;
            $wild=[$comb[0]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos)){
            $len=2;
            $wild=[$comb[0],$comb[1]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos)){
            $len=3;
            $wild=[$comb[0],$comb[1],$comb[2]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos)){
            $len=4;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3]];
        }

        if (in_array(0,$wildPos) and in_array(1,$wildPos) and in_array(2,$wildPos) and in_array(3,$wildPos) and in_array(4,$wildPos)){
            $len=5;
            $wild=[$comb[0],$comb[1],$comb[2],$comb[3],$comb[4]];
        }


        foreach(array_unique($wild) as $sym){

            if ($this->replaceWild===false){
                if ($this->pay($sym,$len)>0 and $this->pay($sym,$len)>=$win['pay'] and ! in_array($sym, $this->anypay)){
                    $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>false,'len'=>$len];
                }
            }
            else{
                if (in_array($sym,$this->wild)){
                    $sym=$this->replaceWild;
                }
                if ($this->pay($sym,$len)>0 and $this->pay($sym,$len)>=$win['pay'] ){
                    $win=['pay'=>$this->pay($sym,$len),'sym'=>$sym,'useWild'=>true,'len'=>$len];
                }
            }
        }

        return $win;

    }

    //line номер линии
    public function payLine($line) {

        //TODO добавить поддержку any
        if (in_array('left', $this->pay_rule)) {
           $f='payLeft';
        }

        if (in_array('leftright', $this->pay_rule)) {
           $f='payLeftRight';
           //$f='payRight';
        }

        if (in_array('left6', $this->pay_rule)) {
            $f='payLeft6';
        }


        if (in_array('3', $this->pay_rule)) {
            $f='pay3';
        }


        if (in_array('left4', $this->pay_rule)) {
            $f = 'payLeft4';
        }

        $win=$this->{$f}($this->GetElLine($line));
        $this->LineSymbol[$line] = $win['sym'];
        $this->LineUseWild[$line] = $win['useWild'];
        $this->LineWinLen[$line] = $win['len'];

        return $win['pay'];
    }

    protected function payLeft4($comb)
    {


        $win = ['pay' => 0, 'sym' => -1, 'useWild' => false, 'len' => 0];
        //ищем wild
        $wildPos = [];
        foreach ($this->wild as $w) {
            foreach (array_keys($comb, $w) as $pos) {
                $wildPos[] = $pos;
            }
        }


        $simple = [0, 1, 2, 3];
        foreach ($wildPos as $pos) {
            unset($simple[$pos]);
        }

        //первый в линии символ, который не wild
        $posSym = count($simple) > 0 ? min($simple) : -1;
        //если комбинация состоит не из одних wild
        if ($posSym >= 0) {

            $sym = $comb[$posSym];
            if (!in_array($sym, $this->anypay)) {
                $comb1 = $comb;
                //если wild действует на текущий символ
                if (!in_array($sym, $this->wild_except)) {
                    foreach ($wildPos as $pos) {
                        $comb1[$pos] = $sym;
                    }
                }


                $len = 1;
                $m = 1;
                $useWild = false;
                if ($comb1[0] == $comb1[1]) {
                    $len = 2;
                    $m = 1;
                    $useWild = false;
                    if (!in_array($sym, $this->wild_except)) {
                        if (in_array(0, $wildPos) or in_array(1, $wildPos)) {
                            $m = $this->wild_multiplier;
                            $useWild = true;
                        }
                    }
                }

                if ($comb1[0] == $comb1[1] and $comb1[1] == $comb1[2]) {
                    $len = 3;
                    $m = 1;
                    $useWild = false;
                    if (!in_array($sym, $this->wild_except)) {
                        if (in_array(0, $wildPos) or in_array(1, $wildPos) or in_array(2, $wildPos)) {
                            $m = $this->wild_multiplier;
                            $useWild = true;
                        }
                    }

                }

                if ($comb1[0] == $comb1[1] and $comb1[1] == $comb1[2] and $comb1[2] == $comb1[3]) {
                    $len = 4;
                    $m = 1;
                    $useWild = false;
                    if (!in_array($sym, $this->wild_except)) {
                        if (in_array(0, $wildPos) or in_array(1, $wildPos) or in_array(2, $wildPos) or in_array(3, $wildPos)) {
                            $m = $this->wild_multiplier;
                            $useWild = true;
                        }
                    }


                }


                if ($this->pay($sym, $len) > $win['pay']) {
                    $win = ['pay' => $this->pay($sym, $len) * $m, 'sym' => $sym, 'useWild' => $useWild, 'len' => $len];
                }

            }
        }

        //считаем wild
        $len = 0;
        $wild = [];
        if (in_array(0, $wildPos)) {
            $len = 1;
            $wild = [$comb[0]];
        }

        if (in_array(0, $wildPos) and in_array(1, $wildPos)) {
            $len = 2;
            $wild = [$comb[0], $comb[1]];
        }

        if (in_array(0, $wildPos) and in_array(1, $wildPos) and in_array(2, $wildPos)) {
            $len = 3;
            $wild = [$comb[0], $comb[1], $comb[2]];
        }

        if (in_array(0, $wildPos) and in_array(1, $wildPos) and in_array(2, $wildPos) and in_array(3, $wildPos)) {
            $len = 4;
            $wild = [$comb[0], $comb[1], $comb[2], $comb[3]];
        }



        foreach (array_unique($wild) as $sym) {

            if ($this->replaceWild === false) {
                if ($this->pay($sym, $len) > 0 and $this->pay($sym, $len) >= $win['pay'] and !in_array($sym, $this->anypay)) {
                    $win = ['pay' => $this->pay($sym, $len), 'sym' => $sym, 'useWild' => false, 'len' => $len];
                }
            } else {
                if (in_array($sym, $this->wild)) {
                    $sym = $this->replaceWild;
                }
                if ($this->pay($sym, $len) > 0 and $this->pay($sym, $len) >= $win['pay']) {
                    $win = ['pay' => $this->pay($sym, $len), 'sym' => $sym, 'useWild' => true, 'len' => $len];
                }
            }
        }
        return $win;

    }

    //сравнение массивов
    function array_diff1($comb, $win) {

        if (count($comb) != count($win)) {
            throw new Exception('Несовпадение');
        }

        foreach ($comb as $key => $el) {

            //массив
            if (is_array($comb[$key])) {
                if (array_diff1($comb[$key], $win[$key]) == 1) {
                    return 1;
                }
            }
            //значение
            //0 - wild в комбинации comb
            elseif ($comb[$key] == 0) {
                continue;
            }
            //0 - любой символ во входе win
            elseif (($win[$key] > 0 and $comb[$key] != $win[$key])) {
                return 1;
            }
        }

        return 0;
    }

    //получаем элемент и следующие за ним
    function getels($bars, $key, $count = 3) {

        $el = [];
        $bc = count($bars) - 1;
        for ($i = 1; $i <= $count; $i++) {
            $el[] = $bars[$key];
            $key++;
            if ($key > $bc) {
                $key = 0;
            }
        }

        return $el;
    }

    public function symreplace($num) {

        $bar = $num % $this->barcount;
        if ($bar == 0) {
            $bar = $this->barcount;
        }

        $pos = $this->pos[$bar] + floor(($num - 0.01) / $this->barcount);

        if ($pos >= count($this->bars[$bar])) {
            $pos -= count($this->bars[$bar]);
        }

        for($i=0;$i<$this->heigth;$i++) {

            $posi = $this->pos[$bar] + $i;

            if ($posi >= count($this->bars[$bar])) {
                $posi -= count($this->bars[$bar]);
            }

            if(in_array($this->bars[$bar][$posi],$this->replaceBar)) {
                if($posi!=$pos) {
                    //TODO ЧТО ЭТО???
                    $v = implode(',',[$bar-1,floor(($num - 0.01) / $this->barcount)]);
                    if(!in_array($v,$this->replaced_symbols_in_bar)) {
                        $this->replaced_symbols_in_bar[]=$v;
                    }
                }
                return $this->bars[$bar][$posi];
            }
        }

        return $this->bars[$bar][$pos];
    }

    public function findReplaceBarSymbolPos() {
        $a = [];
	if(count($this->replaceBar)==0) {
            return [];
        }
        for($b=1;$b<=$this->barcount;$b++){
            for($i=0;$i<$this->heigth;$i++) {
                $posi = $this->pos[$b] + $i;

                if ($posi >= count($this->bars[$b])) {
                    $posi -= count($this->bars[$b]);
                }

                if(in_array($this->bars[$b][$posi],$this->replaceBar)) { //TB может и не быть, как в Liek A Diamond
                    $a[]=[$b,$i]; //x-y format
                }
            }
        }
        return $a;
    }

    function GetElLine($num) {

        $comb = [];

        if(count($this->replaceBar)>0) {
            foreach ($this->lines[$num] as $pos) {
                $comb[] = $this->symreplace($pos);
            }
        }
        else{
            foreach ($this->lines[$num] as $pos) {
                $comb[] = $this->sym($pos);
            }
        }

        return $comb;
    }

    protected $doubleclass;
    public $double_result;
    public $first_bet;

    public function double() {

        if (game::data('can_double') != 1) {
            throw new Exception('cant double');
        }

        $lockKey='_doubleaction_'.auth::$user_id.$this->gameId();

        if(!th::lockProcess($lockKey,5)) {
            throw new Exception('cant double');
        }

        $this->bettype = 'double';
        $this->amount = game::data('first_bet');
        $this->step = game::data('step',0);
        if($this->step>=$this->config['defaults']['max_double']) {
                throw new Exception('cant double. max steps limit');
        }
        $this->first_bet = game::data('first_bet');

        $this->doubleclass->select = $this->select;
        $this->doubleclass->amount = game::data('win');

        if(game::data('freeCountCurrent',0)>0 && $this->config['bonus_double']=='spin') {
            $this->total_win_free = game::data('total_win_free',0) - game::data('win'); //удвоение бонусов
        }

        if(game::data('freeCountCurrent',0)>0 && $this->config['bonus_double']=='all') {
            $this->amount = game::data('total_win_free',0);
            $this->doubleclass->amount = game::data('total_win_free',0);
        }

        $i = 0;
        $exit = false;
        $min = PHP_INT_MAX;
        $method = '';

        do {

            $this->doubleclass->clear();
            $this->doubleclass->select();
            $win = $this->doubleclass->win();
            //сколько можем выиграть?
            if (bet::HaveBankAmount($win)) {
                $exit = true;
                $method = 'bank';
            }

            //минимально возможный выигрыш
            if ($win < $min) {
                $min = $this->win;
                $state = $this->doubleclass->state;
            }

            //нет вариантов
            if ($i >= 50) {
                //закат солнца вручную
                $this->doubleclass->state = $state;
                $exit = true;
                $method = 'hand';
                continue;
            }

            $i++;
        } while (!$exit);

        if ($i == 1) {
            $method = 'random';
        }

        $this->win_all = $this->doubleclass->win();
        $this->double_result = $this->doubleclass->state;

        $data = [];
        if ($this->win_all > 0) {
            $data['can_double'] = 1;
            $data['step']=$this->step+1; //TODO. проверить, чтобы в случае ошибки, не попадало в сессию
            $data['first_bet']=$this->win_all;
        }
        else {
            $data['can_double'] = 0;
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }

        if($this->freeCountCurrent>0) {
            $this->total_win_free += $this->win_all;
        }

        $data['total_win_free'] = $this->total_win_free;

        $data['win'] = $this->win_all;
        //TODO поправить из другого конфига
        if($this->step==$this->config['defaults']['max_double']) {
            $data['step']=0; //TODO надо посмотреть, отдавать нужно 0 или 1.
        }

        $bet['amount'] = $this->amount;
        $bet['come'] = $this->doubleclass->come();
        $bet['result'] = $this->doubleclass->result();
        $bet['win'] = $this->win_all;
        $bet['game_id'] = $this->game_id;
        $bet['game'] = game::session()->game . ' double';
        $bet['method'] = $method;
        $bet['is_freespin'] = (int) $this->isFreespin + (int) $this->isFreespinFromApi;
        if($this->isLuckyFreespin) {
            $bet['is_freespin']=3;
        }

        if(!$this->isFreespin && auth::user()->promo_started!==0) {
            //если это не фриспины и игрок не отказался от турнира, учавствует в нем.
            $events=auth::user()->checkEvents($this->game_model);
            if($events && $events->type=='promo') {
                //если игрок не видел окна, активируем его участие в турнире
                auth::user()->joinEvent($this->game_model,$events);
            }
        }


        bet::make($bet,  $this->bettype, $data);

        th::unlockProcess($lockKey);
    }

    public function bonus() {

        if (game::data('can_bonus') != 1) {
            throw new Exception('cant bonus');
        }

        $this->bettype = 'bonus';
        $this->amount = game::data('amount');

        $this->win_all = game::data('bonusPay');

        $data = [];

        $data['win'] = $this->win_all;
        $data['bonusPay'] = 0;
        $data['bonusrun'] = 0;
        $data['bonusdata'] = array_fill(0,count($this->config['bonus_chance']), 0);

        $bet['amount'] = 0;
        $bet['come'] = null;
        $bet['result'] = json_encode(game::data('bonusdata'));
        $bet['win'] = $this->win_all;
        $bet['game_id'] = $this->game_id;
        $bet['game'] = game::session()->game . ' bonus';
        $bet['method'] = 'calced';
        $bet['is_freespin'] = (int) $this->isFreespin + (int) $this->isFreespinFromApi;
        if($this->isLuckyFreespin) {
            $bet['is_freespin']=3;
        }

        if(!$this->isFreespin && auth::user()->promo_started!==0) {
            //если это не фриспины и игрок не отказался от турнира, учавствует в нем.
            $events=auth::user()->checkEvents($this->game_model);
            if($events && $events->type=='promo') {
                //если игрок не видел окна, активируем его участие в турнире
                auth::user()->joinEvent($this->game_model,$events);
            }
        }

        bet::make($bet,  $this->bettype, $data);
    }

    public function calcmath() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);

        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }

        $fSpin= array_fill(0,15, 0);

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;
                            $in += $this->amount;
                            $out += $this->win_all;

                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;

                            $winScatter += $this->win[0];

                             $this->freerun = 0;
                            $cf=0;
                            foreach ($this->scatter as $sym) {
                                $sc= array_count_values($this->sym());
                                if (isset($sc[$sym])) {
                                    $cf=$sc[$sym];
                                }
                            }

                            $fSpin[$cf]++;


                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] ++;
                            if ($this->LineWinLen[1]==1){
                                echo 'lineWinLen==1';
                                print_r($this->pos);
                                exit;
                            }

                        }
                    }
                }

            }

            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 6) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true)."
                        free:".print_r($fSpin,true);

        print_b($csym);


        return [
            $in,$out,$freespin
        ];


        $s = "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 6) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
			time: $time
			bars: " . print_a1($this->bars, true) . "\r\n\r\n " . print_b($csym);

        file_put_contents('1', $s);
    }



    public function calcmath6() {
        ob_end_clean();


        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6=>0];
        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);
        $count[6] = count($this->bars[5]);

        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
            $csym[$sym][6] = [0, 0];
        }

        $fSpin= array_fill(0,15, 0);

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5] * $count[6];
        $winScatter = 0;

        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            for ($pos[6] = 0; $pos[6] <= $count[6] - 1; $pos[6] ++) {

                                $this->pos[1] = $pos[1];
                                $this->pos[2] = $pos[2];
                                $this->pos[3] = $pos[3];
                                $this->pos[4] = $pos[4];
                                $this->pos[5] = $pos[5];
                                $this->pos[6] = $pos[6];
                                $this->correct_pos();
                                $this->win();
                                $spin_count++;
                                $in += $this->amount;
                                $out += $this->win_all;

                                if ($this->bonusrun > 0) {
                                    $bonus[$this->bonusrun] ++;
                                }
                                $freespin += $this->freerun;

                                $winScatter += $this->win[0];


                                $this->freerun = 0;
                                $cf=0;
                                foreach ($this->anypay as $sym) {
                                    $sc= array_count_values($this->sym());
                                    if (isset($sc[$sym])) {
                                        $cf=$sc[$sym];
                                    }
                                }

                                $fSpin[$cf]++;


                                $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] ++;
                            }
                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 6) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true)."
                        free:".print_r($fSpin,true);

        print_b($csym);


        return [
            $in,$out,$freespin
        ];


        $s = "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 6) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
			time: $time
			bars: " . print_a1($this->bars, true) . "\r\n\r\n " . print_b($csym);

        file_put_contents('1', $s);
    }



    public function calcall($normal,$fg) {
        $z=$normal[1]/$normal[0];
        $fz=$fg[1]/$fg[0];

        $per=$normal[2]/$this->free_games[3]/$normal[0];

        $fper=$fg[2]/$this->free_games[3]/$fg[0];

        $sum_fs = [$normal[2]];

        for($i=1;$i<20;$i++) {
            $sum_fs[]=$sum_fs[$i-1]*$this->free_games[3]*$fper;
        }
        $calced = array_sum($sum_fs)*$this->free_multiplier*$fz;

        $calced_z = ($calced+$normal[1])/$normal[0];

        return $calced_z;
    }

    //версия с перебором ограниченного числа символов
    public function calcmath2() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;



        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);

        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }


        $lowBar = [];
        $countSym = [];
        $lowCount = [];
        foreach ($this->bars as $num => $bar) {
            $lowBar[$num] = array_values(array_unique($bar));
            $lowCount[$num] = count($lowBar[$num]);
            $countSym[$num] = array_count_values($bar);
        }

        //меняем барабан на маленький
        $this->bars = $lowBar;



        $allSpin = $lowCount[1] * $lowCount[2] * $lowCount[3] * $lowCount[4] * $lowCount[5];
        $curSpin = 0;

        for ($pos[1] = 0; $pos[1] <= $lowCount[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $lowCount[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $lowCount[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $lowCount[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $lowCount[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();

                            //увеличиваем все на общее количество таких комбинаций на барабане
                            $mn = 1;

                            for ($i = 1; $i <= 5; $i++) {
                                //-1 потому что кривые данные, лень переделывать
                                //А потому и не работает, что ты ленивая жопа
                                //А в linecomb вообще говно лежит, и так считать нельзя
                                $mn *= $countSym[$i][$this->lineComb[1][$i - 1]];
                            }

                            $spin_count += $mn;
                            $in += $this->amount * $mn;
                            $out += $this->win_all * $mn;

                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] += $mn;
                            }
                            $freespin += $this->freerun * $mn;


                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] += $mn;

                            $curSpin++;
                        }
                    }
                }
                $time = floor((time() - $start) / $curSpin * ($allSpin - $curSpin));
            }
            echo "$curSpin/$allSpin lost:$time сек\r\n";
        }

        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);
    }

    //версия для расчета 3-х барабанных слотов
    public function calcmath3() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0];
        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];



        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
        }

        $allSpin = $count[1] * $count[2] * $count[3];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {

                    $this->pos[1] = $pos[1];
                    $this->pos[2] = $pos[2];
                    $this->pos[3] = $pos[3];

                    $this->correct_pos();
                    $this->win();
                    $spin_count++;
                    $in += $this->amount;
                    $out += $this->win_all;

                    if ($this->bonusrun > 0) {
                        $bonus[$this->bonusrun] ++;
                    }
                    $freespin += $this->freerun;
                    $winScatter += $this->win[0];

		    $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] ++;
                }
                $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            }
        }

        $time = time() - $start;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);

        $s = "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 2) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freespin
			time: $time
			bars: " . print_a1($this->bars, true) . "\r\n\r\n " . print_b($csym);

        file_put_contents('1', $s);
    }

    public function bonuscalcmath() {

        return [0, 0, 0, 0, 0, 0];
    }


    public function manyScatterTest(){

        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);

        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
        }

        $free= array_fill(0, 10,0);

        foreach ($this->scatter as $num){
            $free[9]+=$oc[1]*($countSym[2][$num]-2)*($countSym[3][$num]-2)*($countSym[4][$num]-2)*$oc[5];
        }

        print_r($free);


    }


    //быстрая версия
	public function calcmathq() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }


        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);




        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
                $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
        }

        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);

	$sc=[0,0,0,0,0,0];





        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;

                            $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*$countSym[3][$this->sym(8)]*$countSym[4][$this->sym(9)]*$countSym[5][$this->sym(10)];



                            $this->win[0]=0;


                            $this->win_all = array_sum($this->win);



                            $in += $this->amount*$mn;
                            $out += $this->win_all*$mn;


                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;
                            $winScatter += $this->win[0];

                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;


                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }

		$sc2=0;
		$sc3=0;
		$sc4=0;
		$sc5=0;


		foreach($this->anypay as $sym){
                        $countSym[1][$sym]=$countSym[1][$sym]??0;
                        $countSym[2][$sym]=$countSym[2][$sym]??0;
                        $countSym[3][$sym]=$countSym[3][$sym]??0;
                        $countSym[4][$sym]=$countSym[4][$sym]??0;
                        $countSym[5][$sym]=$countSym[5][$sym]??0;


                        $sc2+=$countSym[1][$sym]*$countSym[2][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[1][$sym]*$countSym[3][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
			$sc2+=$countSym[1][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[1][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[2][$sym]*$countSym[3][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[2][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[2][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*9*$this->pay($sym,2);
                        $sc2+=$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*9*$this->pay($sym,2);


			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*27*$this->pay($sym,3);

			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*81*$this->pay($sym,4);
			$sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*81*$this->pay($sym,4);

			$sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*243*$this->pay($sym,5);
		}


                $fg3=0;
		$fg4=0;
		$fg5=0;


		foreach($this->scatter as $sym){

			$countSym[1][$sym]=$countSym[1][$sym] ?? 0;
			$countSym[2][$sym]=$countSym[2][$sym] ?? 0;
			$countSym[3][$sym]=$countSym[3][$sym] ?? 0;
			$countSym[4][$sym]=$countSym[4][$sym] ?? 0;
			$countSym[5][$sym]=$countSym[5][$sym] ?? 0;



			$fg3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->free_games[3];
			$fg3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*27*$this->free_games[3];

			$fg4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*3)*81*$this->free_games[4];
			$fg4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*3)*81*$this->free_games[4];
			$fg4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*81*$this->free_games[4];
			$fg4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*81*$this->free_games[4];
			$fg4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*81*$this->free_games[4];

			$fg5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*243*$this->free_games[5];
		}

                $mud= array_fill(0,9,0);
                foreach([0,1,2,3,4,5,6,7,8] as $sym){
                        $countSym[1][$sym]=$countSym[1][$sym]??0;
                        $countSym[2][$sym]=$countSym[2][$sym]??0;
                        $countSym[3][$sym]=$countSym[3][$sym]??0;
                        $countSym[4][$sym]=$countSym[4][$sym]??0;
                        $countSym[5][$sym]=$countSym[5][$sym]??0;


                        $mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[1][$sym]*$countSym[3][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[1][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[2][$sym]*$countSym[3][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[2][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[2][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*9*$this->pay($sym,2);
                        $mud[$sym]+=$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*9*$this->pay($sym,2);


			$mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
			$mud[$sym]+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*27*$this->pay($sym,3);

			$mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*81*$this->pay($sym,4);
			$mud[$sym]+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*81*$this->pay($sym,4);
			$mud[$sym]+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*81*$this->pay($sym,4);

			$mud[$sym]+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*243*$this->pay($sym,5);

		}

		print_r($mud);

		echo "scatter:$sc2+$sc3+$sc4+$sc5\r\n";
                echo "free   :$fg3+$fg4+$fg5\r\n";

                $time = time() - $start;

		$winScatter=$sc2+$sc3+$sc4+$sc5;
		$freeSpin=$fg3+$fg4+$fg5;
		$out+=$winScatter;





        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 8) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freeSpin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);


    }


    //быстрая версия 6 барабанов
	public function calcmathq6() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);

        $winScatter=$this->calcAnypay6();
        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6=>0];


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
            $csym[$sym][6] = [0, 0];
        }


        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);
        $oc[6] = count($this->bars[6]);



        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
                $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
        }

        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);
        $count[6] = count($this->bars[6]);

	$sc=[0,0,0,0,0,0];





        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5] * $count[6];

        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            for ($pos[6] = 0; $pos[6] <= $count[6] - 1; $pos[6] ++) {
                                $this->pos[1] = $pos[1];
                                $this->pos[2] = $pos[2];
                                $this->pos[3] = $pos[3];
                                $this->pos[4] = $pos[4];
                                $this->pos[5] = $pos[5];
                                $this->pos[6] = $pos[6];
                                $this->correct_pos();
                                $this->win();
                                $spin_count++;

                                $mn=$countSym[1][$this->sym(7)]*$countSym[2][$this->sym(8)]*$countSym[3][$this->sym(9)]*$countSym[4][$this->sym(10)]*$countSym[5][$this->sym(11)]*$countSym[6][$this->sym(12)];
                                $this->win[0]=0;
                                $this->win_all = array_sum($this->win);

                                $in += $this->amount*$mn;
                                $out += $this->win_all*$mn;


                                if ($this->bonusrun > 0) {
                                    $bonus[$this->bonusrun] ++;
                                }
                                $freespin += $this->freerun;

                                $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;

                            }
                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }



                $time = time() - $start;


		$freeSpin=0;
		$out+=$winScatter;





        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 8) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freeSpin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);


    }

	//быстрая версия
	public function calcmathq45() {
        ob_end_clean();
        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }


		$countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

		$oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);

        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
                $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
        }

        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);

		$sc=[0,0,0,0,0,0];

        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;

                            $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*$countSym[3][$this->sym(8)]*$countSym[4][$this->sym(9)]*$countSym[5][$this->sym(10)];
                            $this->win[0]=0;
                            $this->win_all = array_sum($this->win);


                            $in += $this->amount*$mn;
                            $out += $this->win_all*$mn;


                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;
                            $winScatter += $this->win[0];

                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;


                        }
                    }
                }
            }
            $time = floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }


		$sc3=0;
		$sc4=0;
		$sc5=0;


		foreach($this->anypay as $sym){

			$countSym[1][$sym]=$countSym[1][$sym] ?? 0;
			$countSym[2][$sym]=$countSym[2][$sym] ?? 0;
			$countSym[3][$sym]=$countSym[3][$sym] ?? 0;
			$countSym[4][$sym]=$countSym[4][$sym] ?? 0;
			$countSym[5][$sym]=$countSym[5][$sym] ?? 0;



			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
			$sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*64*$this->pay($sym,3);

			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*256*$this->pay($sym,4);
			$sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*256*$this->pay($sym,4);

			$sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*1024*$this->pay($sym,5);
		}

                $fg3=0;
		$fg4=0;
		$fg5=0;


		foreach($this->scatter as $sym){

			$countSym[1][$sym]=$countSym[1][$sym] ?? 0;
			$countSym[2][$sym]=$countSym[2][$sym] ?? 0;
			$countSym[3][$sym]=$countSym[3][$sym] ?? 0;
			$countSym[4][$sym]=$countSym[4][$sym] ?? 0;
			$countSym[5][$sym]=$countSym[5][$sym] ?? 0;



			$fg3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->free_games[3];
			$fg3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*64*$this->free_games[3];

			$fg4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*4)*256*$this->free_games[4];
			$fg4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*4)*256*$this->free_games[4];
			$fg4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*256*$this->free_games[4];
			$fg4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*256*$this->free_games[4];
			$fg4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*256*$this->free_games[4];

			$fg5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*1024*$this->free_games[5];
		}



		echo "scatter:$sc3+$sc4+$sc5\r\n";
                echo "free   :$fg3+$fg4+$fg5\r\n";

        $time = time() - $start;

		$winScatter=$sc3+$sc4+$sc5;
		$freeSpin=$fg3+$fg4+$fg5;
		$out+=$winScatter;

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 4) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freeSpin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);


    }



    public function printBar(){


        for($i=0; $i<$this->heigth;$i++){
            for($j=0; $j<$this->barcount;$j++){
                echo $this->sym($i*$this->barcount+$j+1). ' ';
            }
            echo '<br>';
        }

    }


    public function to2($num,$smart){

        $bars=$this->bars;
        $keys2= array_keys($bars[$num],$smart);

        foreach($keys2 as $key){

            /* Для 4 линейных burning hot  раскомментировать
            $check=$key-2;
            $check=$check<0 ? $check+count($bars[$num]) : $check;
            if (!in_array($check,$keys2)){
                $keys2[]=$check;
            }
            */

            $check=$key-1;
            $check=$check<0 ? $check+count($bars[$num]) : $check;
            if (!in_array($check,$keys2)){
                $keys2[]=$check;
            }
            $check=$key+1;
            $check=$check>count($bars[$num])-1 ? $check-count($bars[$num]) : $check;
            if (!in_array($check,$keys2)){
                $keys2[]=$check;
            }

        }
        sort($keys2);

        $tempW=[];
        $tempNo=$bars[$num];
        foreach($keys2 as $key){
            $tempW[]=$bars[$num][$key];
            unset($tempNo[$key]);
        }

        return [1=>$tempW,0=>$tempNo];

    }


    public function smartCalc($smart=7){

        ob_end_clean();

        $in=0;

        if ($this->heigth==4){
            $out=$this->calcAnypay4();
        }
        else{
            $out=$this->calcAnypay();
        }


        $t=[];

        //$t[1]=[$this->bars[1]];
        $t[1]=$this->to2(1,$smart);
        $t[2]=$this->to2(2,$smart);
        $t[3]=$this->to2(3,$smart);
        $t[4]=$this->to2(4,$smart);
        $t[5]=$this->to2(5,$smart);
        //$t[5]=[$this->bars[5]];//$this->to2(5,$smart);

        $r=[];
        foreach($t as $a){


           if (count($a)==1){

               if(count($r)==0){
                   $r[]=$a;

                   continue;
               }

               foreach($a as $el){
                   foreach($r as $key=>$value){
                       $r[$key][]=$el;
                   }
               }

           }
           else{
               if(count($r)==0){
                    foreach($a as $el){
                        $r[]=[$el];
                    }

                   continue;
               }

               $base=$r;
               $r=[];
               foreach($a as $el){
                   foreach($base as $her){
                       $her[]=$el;
                       $r[]=$her;
                   }
               }

           }


        }



        //Считаем наборы барабанов по отдельности
        $i=0;
        foreach($r as $bar){
            $this->bars[1]=$bar[0];
            $this->bars[2]=$bar[1];
            $this->bars[3]=$bar[2];
            $this->bars[4]=$bar[3];
            $this->bars[5]=$bar[4];

            echo "\r\n";
            echo $i++;

            $result=$this->calcMathSmart($smart);
            $in+=$result['in'];
            $out+=$result['out'];
        }

        $z=round($out/$in,8);
        echo "\r\n\r\n\r\n$in $out $z";

    }




    public function smartCalc6($smart=7){

        ob_end_clean();

        $in=0;

        if ($this->heigth==4){
            $out=$this->calcAnypay4();
        }
        else{
            $out=$this->calcAnypay6();
        }

        $t=[];

        //$t[1]=[$this->bars[1]];
        $t[1]=$this->to2(1,$smart);
        $t[2]=$this->to2(2,$smart);
        $t[3]=$this->to2(3,$smart);
        $t[4]=$this->to2(4,$smart);
        $t[5]=$this->to2(5,$smart);
        $t[6]=$this->to2(6,$smart);



        $r=[];
        foreach($t as $a){


           if (count($a)==1){

               if(count($r)==0){
                   $r[]=$a;

                   continue;
               }

               foreach($a as $el){
                   foreach($r as $key=>$value){
                       $r[$key][]=$el;
                   }
               }

           }
           else{
               if(count($r)==0){
                    foreach($a as $el){
                        $r[]=[$el];
                    }

                   continue;
               }

               $base=$r;
               $r=[];
               foreach($a as $el){
                   foreach($base as $her){
                       $her[]=$el;
                       $r[]=$her;
                   }
               }

           }


        }



        //Считаем наборы барабанов по отдельности
        $i=0;
        foreach($r as $bar){
            $this->bars[1]=$bar[0];
            $this->bars[2]=$bar[1];
            $this->bars[3]=$bar[2];
            $this->bars[4]=$bar[3];
            $this->bars[5]=$bar[4];
            $this->bars[6]=$bar[5];

            echo "\r\n";
            echo $i++;

            $result=$this->calcMathSmart6($smart);
            $in+=$result['in'];
            $out+=$result['out'];
        }

        $z=round($out/$in,8);
        echo "\r\n\r\n\r\n$in $out $z";

    }



//для расчета барабнов по кускам
public function calcMathSmart($smart) {

        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);


        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }


        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);

        foreach($this->bars as $bnum=>$bar){
                //Укорачиваем барабаны только там где это требуется
                if (in_array($smart,$bar)){
                    $countSym[$bnum]= array_fill(0,50,1);
                }
                else{
                    $countSym[$bnum]= array_count_values($bar);
                    $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
                }

        }


        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);

	$sc=[0,0,0,0,0,0];





        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            $this->pos[1] = $pos[1];
                            $this->pos[2] = $pos[2];
                            $this->pos[3] = $pos[3];
                            $this->pos[4] = $pos[4];
                            $this->pos[5] = $pos[5];
                            $this->correct_pos();
                            $this->win();
                            $spin_count++;

                            $mn=$countSym[1][$this->sym(6)]*$countSym[2][$this->sym(7)]*$countSym[3][$this->sym(8)]*$countSym[4][$this->sym(9)]*$countSym[5][$this->sym(10)];



                            $this->win[0]=0;


                            $this->win_all = array_sum($this->win);



                            $in += $this->amount*$mn;
                            $out += $this->win_all*$mn;


                            if ($this->bonusrun > 0) {
                                $bonus[$this->bonusrun] ++;
                            }
                            $freespin += $this->freerun;
                            $winScatter += $this->win[0];

                            $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;


                        }
                    }
                }
            }
            $time = 0;//floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }




        return ['in'=>$in, 'out'=>$out];

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 8) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freeSpin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);


    }

public function calcMathSmart6($smart) {

        $start = time();
        //ставка на линию
        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $this->barcount = count($this->bars);



        $pos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0,0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
            $csym[$sym][6] = [0, 0];
        }


        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => [],1=>[]];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);
        $oc[6] = count($this->bars[6]);

        foreach($this->bars as $bnum=>$bar){
                //Укорачиваем барабаны только там где это требуется
                if (in_array($smart,$bar)){
                    $countSym[$bnum]= array_fill(0,50,1);
                }
                else{
                    $countSym[$bnum]= array_count_values($bar);
                    $this->bars[$bnum]= array_values(array_unique($this->bars[$bnum]));
                }

        }



        $count = [];
        $count[1] = count($this->bars[1]);
        $count[2] = count($this->bars[2]);
        $count[3] = count($this->bars[3]);
        $count[4] = count($this->bars[4]);
        $count[5] = count($this->bars[5]);
        $count[6] = count($this->bars[6]);



        $allSpin = $count[1] * $count[2] * $count[3] * $count[4] * $count[5]* $count[6];
        $winScatter = 0;
        echo "start\r\n";
        for ($pos[1] = 0; $pos[1] <= $count[1] - 1; $pos[1] ++) {
            for ($pos[2] = 0; $pos[2] <= $count[2] - 1; $pos[2] ++) {
                for ($pos[3] = 0; $pos[3] <= $count[3] - 1; $pos[3] ++) {
                    for ($pos[4] = 0; $pos[4] <= $count[4] - 1; $pos[4] ++) {
                        for ($pos[5] = 0; $pos[5] <= $count[5] - 1; $pos[5] ++) {
                            for ($pos[6] = 0; $pos[6] <= $count[6] - 1; $pos[6] ++) {
                                $this->pos[1] = $pos[1];
                                $this->pos[2] = $pos[2];
                                $this->pos[3] = $pos[3];
                                $this->pos[4] = $pos[4];
                                $this->pos[5] = $pos[5];
                                $this->pos[6] = $pos[6];
                                $this->correct_pos();
                                $this->win();
                                $spin_count++;


                                $mn=$countSym[1][$this->sym(7)]*$countSym[2][$this->sym(8)]*$countSym[3][$this->sym(9)]*$countSym[4][$this->sym(10)]*$countSym[5][$this->sym(11)]*$countSym[6][$this->sym(12)];




                                $this->win[0]=0;


                                $this->win_all = array_sum($this->win);



                                $in += $this->amount*$mn;
                                $out += $this->win_all*$mn;


                                if ($this->bonusrun > 0) {
                                    $bonus[$this->bonusrun] ++;
                                }
                                $freespin += $this->freerun;
                                $winScatter += $this->win[0];

                                $csym[$this->LineSymbol[1]][$this->LineWinLen[1]][$this->LineUseWild[1]] +=$mn;

                            }
                        }
                    }
                }
            }
            $time = 0;//floor((time() - $start) / $spin_count * ($allSpin - $spin_count));
            echo "$spin_count/$allSpin lost:$time сек\r\n";
        }




        return ['in'=>$in, 'out'=>$out];

        echo "
			spin_count: $spin_count
			in: $in
			out: $out
			z: " . round($out / $in, 8) . "
			bonus: " . implode(' ', $bonus) . "
			freespin: $freeSpin
                        winScatter: $winScatter
			time: $time
			bars: " . print_a1($this->bars, true);

        print_b($csym);


    }



    public function calcAnypay6(){


        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);
        $oc[6] = count($this->bars[6]);



        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
        }


        $winScatter = 0;



        $sc2=0;
        $sc3=0;
        $sc4=0;
        $sc5=0;
        $sc6=0;

        foreach($this->anypay as $sym){
                $countSym[1][$sym]=$countSym[1][$sym]??0;
                $countSym[2][$sym]=$countSym[2][$sym]??0;
                $countSym[3][$sym]=$countSym[3][$sym]??0;
                $countSym[4][$sym]=$countSym[4][$sym]??0;
                $countSym[5][$sym]=$countSym[5][$sym]??0;
                $countSym[6][$sym]=$countSym[6][$sym]??0;


                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[6][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);

                $sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[6][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[6][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);

                $sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);

                $sc3+=$countSym[2][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[3][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);




                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[6][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*$countSym[6][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);

                $sc4+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[6][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*81*$this->pay($sym,4);

                $sc4+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[6]-$countSym[6][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*81*$this->pay($sym,4);



                $sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[6]-$countSym[6][$sym]*3)*243*$this->pay($sym,5);
                $sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[6][$sym]*($oc[5]-$countSym[5][$sym]*3)*243*$this->pay($sym,5);
                $sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[4]-$countSym[4][$sym]*3)*243*$this->pay($sym,5);
                $sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[3]-$countSym[3][$sym]*3)*243*$this->pay($sym,5);
                $sc5+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[2]-$countSym[2][$sym]*3)*243*$this->pay($sym,5);
                $sc5+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*($oc[1]-$countSym[1][$sym]*3)*243*$this->pay($sym,5);

                $sc6+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*$countSym[6][$sym]*729*$this->pay($sym,6);
        }

        echo "$sc2+$sc3+$sc4+$sc5+$sc6\r\n";
        $winScatter=$sc2+$sc3+$sc4+$sc5+$sc6;

        return $winScatter;


    }


    public function calcAnypay(){


        $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $start = time();


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }


        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);




        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
        }


        $winScatter = 0;



        $sc2=0;
        $sc3=0;
        $sc4=0;
        $sc5=0;


        foreach($this->anypay as $sym){
                $countSym[1][$sym]=$countSym[1][$sym]??0;
                $countSym[2][$sym]=$countSym[2][$sym]??0;
                $countSym[3][$sym]=$countSym[3][$sym]??0;
                $countSym[4][$sym]=$countSym[4][$sym]??0;
                $countSym[5][$sym]=$countSym[5][$sym]??0;


                $sc2+=$countSym[1][$sym]*$countSym[2][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[1][$sym]*$countSym[3][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[1][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[1][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[2][$sym]*$countSym[3][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[2][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[2][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*9*$this->pay($sym,2);
                $sc2+=$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*9*$this->pay($sym,2);


                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[5]-$countSym[5][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[4]-$countSym[4][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[3]-$countSym[3][$sym]*3)*27*$this->pay($sym,3);
                $sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*3)*($oc[2]-$countSym[2][$sym]*3)*27*$this->pay($sym,3);

                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*3)*81*$this->pay($sym,4);
                $sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*3)*81*$this->pay($sym,4);

                $sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*243*$this->pay($sym,5);
        }

        echo "$sc2+$sc3+$sc4+$sc5";
        $winScatter=$sc2+$sc3+$sc4+$sc5;

        return $winScatter;






    }






    public function calcAnypay4(){


         $this->amount_line = 1;
        //выбрано линий
        $this->cline = 1;
        //ставка всего
        $this->amount = $this->cline * $this->amount_line;


        $start = time();


        $spin_count = 0;
        $in = 0;
        $out = 0;
        $bonus = [0, 0, 0, 0, 0, 0];
        $freespin = 0;


        $csym = [];
        $csym[-1][0] = [0, 0];
        foreach (array_keys($this->pay) as $sym) {
            $csym[$sym][1] = [0, 0];
            $csym[$sym][2] = [0, 0];
            $csym[$sym][3] = [0, 0];
            $csym[$sym][4] = [0, 0];
            $csym[$sym][5] = [0, 0];
        }


        $countSym=[1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

        $oc[1] = count($this->bars[1]);
        $oc[2] = count($this->bars[2]);
        $oc[3] = count($this->bars[3]);
        $oc[4] = count($this->bars[4]);
        $oc[5] = count($this->bars[5]);




        foreach($this->bars as $bnum=>$bar){
                $countSym[$bnum]= array_count_values($bar);
        }


        $winScatter = 0;



        $sc2=0;
        $sc3=0;
        $sc4=0;
        $sc5=0;

            foreach($this->anypay as $sym){

                $countSym[1][$sym]=$countSym[1][$sym] ?? 0;
                $countSym[2][$sym]=$countSym[2][$sym] ?? 0;
                $countSym[3][$sym]=$countSym[3][$sym] ?? 0;
                $countSym[4][$sym]=$countSym[4][$sym] ?? 0;
                $countSym[5][$sym]=$countSym[5][$sym] ?? 0;


                $sc2+=$countSym[1][$sym]*$countSym[2][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[1][$sym]*$countSym[3][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[1][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[1][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[2][$sym]*$countSym[3][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[2][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[2][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*16*$this->pay($sym,2);
                $sc2+=$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*16*$this->pay($sym,2);


                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*($oc[4]-$countSym[4][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[5]-$countSym[5][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*($oc[3]-$countSym[3][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[4]-$countSym[4][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[1][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[2]-$countSym[2][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[2][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[3]-$countSym[3][$sym]*4)*64*$this->pay($sym,3);
                $sc3+=$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*($oc[1]-$countSym[1][$sym]*4)*($oc[2]-$countSym[2][$sym]*4)*64*$this->pay($sym,3);

                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[5]-$countSym[5][$sym]*4)*256*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[5][$sym]*($oc[4]-$countSym[4][$sym]*4)*256*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[5][$sym]*$countSym[4][$sym]*($oc[3]-$countSym[3][$sym]*4)*256*$this->pay($sym,4);
                $sc4+=$countSym[1][$sym]*$countSym[5][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[2]-$countSym[2][$sym]*4)*256*$this->pay($sym,4);
                $sc4+=$countSym[5][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*($oc[1]-$countSym[1][$sym]*4)*256*$this->pay($sym,4);

                $sc5+=$countSym[1][$sym]*$countSym[2][$sym]*$countSym[3][$sym]*$countSym[4][$sym]*$countSym[5][$sym]*1024*$this->pay($sym,5);
        }




        echo "$sc2+$sc3+$sc4+$sc5";
        $winScatter=$sc2+$sc3+$sc4+$sc5;

        return $winScatter;






    }

}
