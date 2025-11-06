<?php

class Controller_Gameapi extends Controller
{

    protected $_ans;
    protected $_office_id;
    protected $_o;
    protected $_req_params=[];
    protected $_domain='';
    protected $_demo_domain='http://demo.api';

    public function before()
    {

        $c = kohana::$config->load('static.gameapi_domen');
        if($c) {
            $this->_domain = $c;
        }

        if(!API_DOMAIN) {
            throw new HTTP_Exception_403;
        }

        parent::before();

        //all checks

        $this->_req_params=$this->request->query();

        if($this->request->method()=='POST') {
            $this->_req_params=$this->request->post();
        }

        unset($this->_req_params['kohana_uri']);

        $this->_office_id = (int) arr::get($this->_req_params,'office_id');

        if(!$this->_office_id) {
            $this->error('no office id', '801');
        }

        $this->_o = new Model_Office($this->_office_id);

        if(!$this->_o->loaded()) {
            $this->error('office not found', '802');
        }

        if(!$this->_allowed_actions($this->_o->apitype)) {
            $this->error('api error', '8992');
        }

        $sign = arr::get($this->_req_params,'sign');
        unset($this->_req_params['sign']);

        if(!$this->_o->check_sign($this->_req_params, $sign)) {
            $this->error('sign fail', '803');
        }

		if($this->_o->currency_id==1) {
            $this->_domain = kohana::$config->load('static.gameapi_ru_domen');
        }

        $this->_ans['error']      = '1';
        $this->_ans['error_code'] = '222';
        $this->_ans['error_message'] = 'unknown';
        $this->_ans['data']       = '';
    }

    protected function _allowed_actions($api_type) {
        $allowed=[
            4=>['jpwidget'],
        ];

        if(!isset($allowed[$api_type])) {
            return true;
        }

        return in_array($this->request->action(),$allowed[$api_type]);
    }

    public function action_list()
    {

        $static = kohana::$config->load('static');

        //mango agt
        if(PROJECT==1 && in_array($this->_office_id,[1007,1008,1009,1010,1011,1012,1013,1014,1134])) {
            $static['static_domain']='https://content.gameconnectapi.com';
        }

        $imgurl=$static['static_domain'];
        $imgurl.='/games/agt/images/games/';

        $moon_min_bet=0.1;
        $moon_max_bet=100;
        $moon_max_win=10000;


        $office=office::instance($this->_office_id)->office();
        $currency=$office->currency;
        $office->sort();
        $result=array_values(office::instance($this->_office_id)->office()->sorted_games);

        $sets=[
            '1x1'=>[
                [250,250],
                [450,450],
                [600,600],
            ],
            '1.5x1'=>[600,400],
            '1.4x1'=>[420,300],
            '1.33x1'=>[400,300],
            '1.566x1'=>[752,480],
            '1.666x1'=>[400,240],
            '1x1.5'=>[400,600],
            '1x1.33'=>[420,560],
        ];

        $dest=$static['static_domain'].'/files/promo/';
		
		$officeModel=Office::instance($this->_office_id)->office();
        $currencyModel=$officeModel->currency;
		
		$moonLimits=th::getMoonLimits($officeModel,$currencyModel);

        foreach($result as $k=>&$r) {
            if(PROJECT!=1 && $officeModel->agtenable && $r['brand']=='agt') {
                $r['game_id'] = $r['external_id']+100000;
                $r['image'] = str_replace($static['static_domain'],'',$r['image']);
                unset($r['external_id']);
            }
            elseif(PROJECT!=1 && $r['brand']=='agt') {
                unset($result[$k]);
            }
            else {
                unset($r['external_id']);
            }

            if(PROJECT==1) {
                if($r['brand']!='agt') {
                    unset($result[$k]);
                }
                else {
                    $r['images']=[
                            $r['image'],
                            str_replace('.png','.webp',$r['image'])
                    ];
                    //0 - horisontal,
                    //1 - square
                    $r['lobby']=[
                            [
                                    [
                                        'src'=>$r['image'],
                                        'type'=>'png',
                                    ],
                                    [
                                        'src'=>str_replace('.png','.webp',$r['image']),
                                        'type'=>'webp',
                                    ],
                            ],
                            [
                                    [
                                        'src'=>str_replace('/thumb/','/sqthumb/',$r['image']),
                                        'type'=>'png',
                                    ],
                                    [
                                        'src'=>str_replace(['/thumb/','.png'],['/sqthumb/','.webp'],$r['image']),
                                        'type'=>'webp',
                                    ],
                            ],
                    ];


					if(!in_array($r['name'],['betfredbonanza','betfrednifty'])) {
                    foreach($sets as $set=>$sizes) {

                        if (!is_array($sizes[0])) {
                            $sizes = [$sizes];
                        }

                        foreach($sizes as $size) {

                            $dest_dir = $dest . 'agt' . $set . '_' . implode('-', $size);

                            $r['lobby'][]=[
                                [
                                    'src'=>$dest_dir.'/'.$r['visible_name'].'.png',
                                    'type'=>'png',
                                ],
                                [
                                    'src'=>$dest_dir.'cycle/'.$r['visible_name'].'.webp',
                                    'type'=>'webp',
                                ],
                                [
                                    'src'=>$dest_dir.'anim/'.$r['visible_name'].'.webp',
                                    'type'=>'webp',
                                ],
                            ];
                        }
                    }

					}

                    if($r['game_type']=='slot') {
                        $config = kohana::$config->load('agt/'.$r['name']);
                        $r['slot_bars_count']=isset($config['lines'])?count($config['lines'][1][0]):0;
                        $r['slot_bars_length']=isset($config['lines'])?count($config['lines'][1]):0;
                        $r['slot_icons_path']=$imgurl.$r['name'].'/icons/';
                        $r['slot_lines_count']=isset($config['lines'])?count($config['lines']):0;
                        $r['slot_paytable']=arr::get($config,'pay',[]);
                        $r['scatter']=arr::get($config,'scatter',[]);
                        $r['wild']=arr::get($config,'wild',[]);
                        $r['wild_multiplier']=arr::get($config,'wild_multiplier',[]);
                        $r['wild_except']=arr::get($config,'wild_except',[]);
                        $r['anypay']=arr::get($config,'anypay',[]);
                        $r['free_games']=arr::get($config,'free_games',[]);
                        $r['free_multiplier']=arr::get($config,'free_multiplier',[]);
						
						$lines_choose=$config['lines_choose'];

                        if(isset($config['staticlines']) && !empty($config['staticlines'])) {
                            $lines_choose=$config['staticlines'];
                        }

                        $r['bets']=[];

                        $gameModel=new Model_Game($r['game_id']);

                        foreach($lines_choose as $l) {
                            $r['bets'][$l]=$gameModel->getAllBets($officeModel,$currencyModel,$l);
                        }
                    }
                    elseif($r['game_type']=='videopoker') {
                        $c1 = Kohana::$config->load('agt/'.$r['name']);
                        $c2 = Kohana::$config->load('videopoker/'.$r['name']);

                        $pt = [];
                        foreach($c1['pay'] as $a=>$d) {
                            $pt[$a-1]=[];
                            foreach($d as $k=>$b) {
                                $pt[$a-1][$c2['level'][$k]] = $b;
                            }
                        }
                        $r['poker_bets_amount']=[1,2,3,6,10];
                        $r['poker_paytable']=$pt;
                    }
                    elseif($r['game_type']=='keno') {
                        $cc = kohana::$config->load('keno/'.$r['name']);
                        $r['keno_paytable']=$cc['pay'];
                    }
                    elseif($r['game_type']=='roshambo') {
                        $cc = kohana::$config->load('agt/'.$r['name']);
                        $r['roshambo_icons_path']=$imgurl.$r['name'].'/ui/';
                        $r['roshambo_paytable']=$cc['pay'];
                    }
                    elseif($r['game_type']=='shuffle') {
                        $cc = kohana::$config->load('agt/'.$r['name']);
                        $r['shuffle_paytable']=$cc['pay'];
                        //$r['anypay']=$cc['anypay'];
                        $r['heigth']=$cc['heigth'];
                        $r['width']=$cc['width'];
                        $r['shuffle_icons_path']=$imgurl.$r['name'].'/icons/';
                    }
                }
            }

            if(th::isMoonGame($r['name'])) {
                $r['moon_min_bet']=$moonLimits['moon_min_bet'];
                $r['moon_max_bet']=$moonLimits['moon_max_bet'];
                $r['moon_max_win']=$moonLimits['moon_max_win'];
            }
        }

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = $result;

    }

    public function action_betinfo() {

        $bet_id = arr::get($this->_req_params,'bet_id');
        $bet_poker_id = arr::get($this->_req_params,'bet_poker_id');

        if(!empty($bet_poker_id)) {
            $b = new Model_Bet(['external_id'=>$bet_poker_id]);
        }
        else {
            $b = new Model_Bet($bet_id);
        }

        if(!$b->loaded() || $b->office_id!=$this->_office_id) {
            $this->_ans['error']=1;
            $this->_ans['error_code'] = 404;
            $this->_ans['error_message'] = 'bet not found';
            $this->_ans['data'] = '';
            return;
        }

        $result = $b->as_array();
        unset($result['method']);
        unset($result['fg_level']);
        unset($result['calc']);
        unset($result['is_freespin']);
        if(!empty($result['external_id'])) {
            $result['bet_poker_id']=$result['external_id'];
        }
        unset($result['external_id']);

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = $result;
    }

    public function action_info()
    {
        $o = new Model_Office($this->_office_id);

        $result = [];
        $result['currency']=$o->currency->code;
        $result['zone_time']=$o->zone_time;
        $result['blocked']=$o->blocked;
        $result['apitype']=$o->apitype;
        $result['name']=$o->visible_name;
        $result['gameapiurl']=$o->gameapiurl;

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = $result;
    }

    public function action_jpwidget()
    {
        $orientation = arr::get($this->_req_params,'orientation','auto');
        $disable_currency = arr::get($this->_req_params,'disable_currency','0');
        $bgimg = arr::get($this->_req_params,'bgimg');
        $iframe = (int) arr::get($this->_req_params,'iframe','1');
        $view = View::factory('site/agt/jpwidget');
        $view->office_id=$this->_office_id;
        $o = new Model_Office($this->_office_id);
        $view->office=$o;
        $view->disable_currency=!!$disable_currency;
        $view->bgimg=$bgimg;
        $view->orientation=$orientation;
        $result = $view->render();

        if($iframe) {
            echo $result;
            exit;
        }

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = $result;
    }

    public function action_offices()
    {

        $sql="select o.id as office_id from offices o "
                . "join persons p on p.id = o.owner "
                . "where p.name=:p_id";

        $result = db::query(1,$sql)
                ->param(':p_id',arr::get($this->_req_params,'person_login'))
                ->execute()
                ->as_array('office_id');

        $result = array_keys($result);

        if(!in_array($this->_office_id,$result)) {
            $this->error('person not found','472');
        }

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = $result;

    }

    public function action_newoffice(){

        //moved to adminapi controller
        exit;

        $currency=UTF8::strtoupper(arr::get($this->_req_params,'currency'));
        $visible_name=arr::get($this->_req_params,'title');

        $currency=new Model_Currency(['code'=>$currency,'source'=>'agt']);


        if (!$currency->loaded()  || $currency->disable!=0 ){
            throw new Exception("Cann't create office with currency $currency ");
        }

        $owner=new Model_Person($this->_o->owner);

        if (!$owner->loaded()){
            throw new Exception("Unknown owner");
        }

        $url=arr::get($this->_req_params,'apiurl',$this->_o->gameapiurl);
        $secretkey=arr::get($this->_req_params,'secretkey',$this->_o->secretkey);

        $o=new Model_Office;

        $o->currency_id=$currency->id;
        $o->external_name = $visible_name;
        $o->visible_name=$owner->comment." $visible_name {$currency->code}";

        $o->apienable=1;
        $o->apitype=0;
        $o->bank=$currency->default_bank;
        $o->use_bank=1;
        $o->bet_min=$currency->min_bet;
        $o->bet_max=$currency->max_bet;

        $o->gameapiurl=$url;

        $o->bonus_diff_last_bet=8;
        $o->enable_bia=time();
        $o->rtp=96;
        $o->owner=$owner->id;

        $o->dentabs=$currency->default_den;
        $o->default_dentab=$currency->default_dentab;
        $o->k_to_jp=0.005;
        $o->k_max_lvl=$currency->default_k_max_lvl;
        $o->enable_jp=1;

        $o->enable_moon_dispatch=1;

        $o->games_rtp=97;
        $o->gameui=1;

        $o->promopanel=1;

        $o->is_test = $test;
        $o->seamlesstype = 1;

        $o->secretkey=$secretkey;

        database::instance()->begin();

        //TODO поификсить создание джекпотаов

        try {
            //создаем игры здесь
            $o->need_create_default_games=false;
            $o->save()->reload();


            database::instance()->direct_query('insert into person_offices(person_id,office_id)
                                        values ('.$o->owner.','.$o->id.')');


            $sql_games = <<<SQL
                insert into office_games(office_id, game_id, enable)
                Select :office_id, g.id, 1
                From games g
                Where g.provider = 'our' and brand ='agt' and show=1 and g.category!='coming' and 
                and g.branded=0
SQL;

            db::query(Database::INSERT,$sql_games)
                ->param(':office_id',$o->id)
                ->execute();

            $o->createProgressiveEventForOffice();

            $redis = dbredis::instance();
            $redis->select(1);
            $redis->set('jpa-'.$o->id,1);

            for($i=1;$i<=4;$i++)
            {

                $redis->set('jpHotPercent-'.$o->id.'-'.($i),0.02);

                $j = new Model_Jackpot();
                $j->office_id = $o->id;
                $j->type = $i;
                $j->active = 1;

                $j->save();
            }

        } catch (Exception $ex) {
            database::instance()->rollback();
            throw $ex;
        }

        database::instance()->commit();

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = $o->id;

    }

    public function action_getfreespins() {
        $login = arr::get($this->_req_params,'login').'-'.$this->_office_id;

        if(empty($login)) {
            $this->error('empty login', '155');
        }

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            $this->error('user not found', '155');
        }

        $only_new = arr::get($this->_req_params,'only_new');

        $sql = 'select * from freespins where src=\'api\' and user_id=:user_id order by created asc';

        if($only_new) {
            $sql = 'select * from freespins where src=\'api\' and active=0 and user_id=:user_id order by created asc';
        }

        $r = db::query(Database::SELECT,$sql)
                ->param(':user_id',$u->id)
                ->execute()
                ->as_array();

        $data=[];
        $total_count=0;
        $total_sum=0;

        foreach($r as $row) {
            $data[]=[
                   'id'=>$row['id'],
                   'game_id'=>$row['game_id'],
                   'count'=>$row['fs_count'],
                   'played'=>$row['fs_played'],
                   'active'=>$row['active'],
                   'lines'=>$row['lines'],
                   'amount_per_line'=>$row['amount'],
                   'total_sum'=>$row['amount']*$row['fs_count'],
                   'expiration_time'=>$row['expirtime'],
                   'created'=>$row['expirtime'],
            ];

            $total_count+=$row['fs_count'];
            $total_sum+=$row['amount']*$row['fs_count'];
        }

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = [
                'data'=>$data,
                'total_count'=>$total_count,
                'total_sum'=>$total_sum,
        ];
    }

    public function action_clearfreespins() {
        $login = arr::get($this->_req_params,'login').'-'.$this->_office_id;

        if(empty($login)) {
            $this->error('empty login', '155');
        }

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            $this->error('user not found', '155');
        }

        $only_new = arr::get($this->_req_params,'only_new');
        $ids = arr::get($this->_req_params,'ids','');

        $sql = 'delete from freespins where src=\'api\' and user_id=:user_id';

        if($only_new) {
            $sql = 'delete from freespins where src=\'api\' and active=0 and user_id=:user_id';
        }

        if(!empty($ids)) {
            $ids=explode('|',$ids);
            $sql.=' and id in :ids';
        }

        $r=db::query(Database::DELETE,$sql)
                ->param(':user_id',$u->id)
                ->param(':ids',$ids)
                ->execute();


        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = [
                'deleted_rows'=>intval($r),
        ];


    }
    public function action_freespinset() {
        $set_name = arr::get($this->_req_params,'name');

        if(empty($set_name)) {
            $this->error('empty set\'s name', '201');
        }

        $set = new Model_Fssets(['name'=>$set_name]);

        if(!$set->loaded()) {
            $this->error('set not found', '202');
        }

        if(!$set->active) {
            $this->error('set not active', '203');
        }

        $mass = arr::get($this->_req_params,'mass');

        if($set->mass && !$mass) {
            $this->error('set is mass', '204');
        }

        if(!$set->mass && $mass) {
            $this->error('set is not mass', '205');
        }


        $login = arr::get($this->_req_params,'login');

        $user_id=null;

        if(!empty($login)) {

            $u = new Model_User(['external_id'=>$login]);

            if(!$u->loaded()) {
                $pas = mt_rand(10000000, 999999999);

                $u->name = $login.'-'.$this->_office_id;
                $u->office_id = $this->_office_id;
                $u->salt = rand(1, 10000000);
                $u->password = auth::pass($pas, $u->salt);
                $u->api = 1;
                $u->api_session_id = arr::get($this->_req_params,'session_id');
                $u->amount = 0;
                $u->api_key = guid::create();
                $u->external_id = arr::get($this->_req_params,'login');
                $u->visible_name = arr::get($this->_req_params,'login');
                $u->api_key_time = time();
                $u->save()->reload();
            }

            $user_id = $u->id;
        }
        else {
            $this->error('mass is disabled', '206');
        }

        $val=Office::instance($this->_office_id)->office()->currency->val;

        if(empty($val) || $val<0.0001) {
            $this->error('Incorrect currency. Please, contact AGT support', '207');
        }

        $limit = 1000; //EUR

        if(($set->amount*$val)>$limit) {
            $this->error('Max amount is '.(ceil($limit/$val)), '208');
        }

        if(!Office::instance($this->_office_id)->office()->checkFSApiLimit($set->amount)) {
            $this->error('FS out of limit', '209');
        }

        $expire = arr::get($this->_req_params,'expire');

        $set->to_process_stack($this->_office_id,$user_id,$expire);

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = [];


    }

    public function action_givefreespins()
    {
		
		logfile::create(date('Y-m-d H:i:s')." givefreespins request; params: ".print_r($this->_req_params,1),'givefreespins');
		
        $login = arr::get($this->_req_params,'login');

        if(empty($login)) {
            $this->error('empty login', 155);
        }

        $login.='-'.$this->_office_id;

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            $pas = mt_rand(10000000, 999999999);

            $u->name = $login;
            $u->office_id = $this->_office_id;
            $u->salt = rand(1, 10000000);
            $u->password = auth::pass($pas, $u->salt);
            $u->api = 1;
            $u->api_session_id = arr::get($this->_req_params,'session_id');
            $u->amount = 0;
            $u->api_key = guid::create();
            $u->external_id = arr::get($this->_req_params,'login');
            $u->visible_name = arr::get($this->_req_params,'login');
            $u->api_key_time = time();
            $u->save()->reload();
        }

        if($u->blocked) {
            $this->error('user is blocked', 503);
        }

        if($u->office->blocked) {
            $this->error('office is blocked', 506);
        }

        $f = new Model_Freespin();

        $amount = arr::get($this->_req_params,'amount');

        if(empty($amount)) {
            $this->error('empty amount', 160);
        }

        $currency=Office::instance($this->_office_id)->office()->currency;
        $val=$currency->val;

        if(empty($val) || $currency->crypto && $val<0.0001) {
            $this->error('Incorrect currency. Please, contact AGT support', 207);
        }

        $limit = 1000; //EUR

        if(($amount*$val)>$limit) {
            $this->error('Max amount is '.(ceil($limit/$val)), 208);
        }


        if(!Office::instance($this->_office_id)->office()->checkFSApiLimit($amount)) {
            $this->error('FS out of limit', 209);
        }

        $is_floor_amount = !!arr::get($this->_req_params,'floor_amount',false);

        $game = arr::get($this->_req_params,'game');

        if(empty($game)) {
            $sql="select g.name, g.id as game_id
                    from games g
                    join office_games og on og.game_id = g.id
                    where og.office_id = :o_id
                        and og.enable = 1
                        and g.show=1
                        and g.provider='our'
                        and (g.type='slot' or g.type='shuffle')
                        order by g.sort";

            $games = db::query(1,$sql)
                    ->param(':o_id',$this->_office_id)
                    ->execute()
                    ->as_array();

            $found=false;

            while(!$found) {
                $r = $games[math::array_rand($games)];
                $c = kohana::$config->load('agt/'.$r['name']);

                if(isset($c['staticlines'])) {
                    continue;
                }
                $game = $r['name'];
                $found=true;
            }
        }

        $g = new Model_Game(['name'=>$game]);

        if(!$g->loaded()) {
            $this->error('game not found', 152);
        }

        if(!in_array($g->type,['slot','shuffle'])) {
            $this->error('game must be slot type', 153);
        }


        $calced = $u->calc_fsback($amount,$g->name,$g->id,false,false,$this->_o->owner==1023);


        if(arr::get($this->_req_params,'game') && $g->id!=$calced['game_id']) {
            $this->error('can not pay freespins for game '.arr::get($this->_req_params,'game'), 223);
        }

        if($fs_count=arr::get($this->_req_params,'fs_count')) {

            $max_limit_count=30;

            if($this->_o->owner==1023) {
                $max_limit_count=150;
            }

            if($fs_count>$max_limit_count) {
                $this->error('max fs_count is '.$max_limit_count, 162);
            }

            $calced_cnt=$calced['cnt'];
            $calced['cnt']=$fs_count;
            $calced['zzz']*=$calced_cnt/$calced['cnt'];
            //fix after new bets list
            $calced['zzz']=$calced['win']/$fs_count;
        }

        if($calced['zzz']!=$calced['win'] && !$is_floor_amount && bccomp($calced['zzz']*$calced['cnt'],$calced['win'])<0) {
            if(arr::get($this->_req_params,'game')) {
                $this->error('it is not possible to match game and amount: '.json_encode($calced), 161);
            }
            else {
                $this->error('can not find game for this amount', 162);
            }
        }

        $ex = explode('-',$calced['near']);

        $lines = (int) $ex[0];
        $dentab_index = (float) $ex[1];

        $only_check = !!arr::get($this->_req_params,'only_check',0);

        $expire = (int) arr::get($this->_req_params,'expire'); //utc time

        if($expire<=0) {
            $expire=30*24*60*60;
            $expire+=time();
        }

        $start = (int) arr::get($this->_req_params,'start'); //utc time

        $fs_uuid = arr::get($this->_req_params,'fs_uuid');
		$force_activate = !!arr::get($this->_req_params,'force_activate',0);

        if($only_check) {
            $this->_ans['error']=0;
            $this->_ans['error_code'] = 0;
            $this->_ans['error_message'] = '';
            $this->_ans['data'] = [
                    'fs_count'=>$calced['cnt'],
                    'fs_total_sum'=>$calced['zzz']*$calced['cnt'],
                    'fs_lines'=>$lines,
                    'fs_dentab_index'=>$dentab_index,
                    'fs_game'=>$g->visible_name,
                    'fs_gameid'=>$g->id,
            ];
        }
        else {

            if(!empty($fs_uuid)) {
                $fs_uuid = $fs_uuid.'-'.$u->office_id;
                $check_fs_uuid = new Model_Freespin(['uuid'=>$fs_uuid]);
                if($check_fs_uuid->loaded()) {
                    $this->error('fs_uuid already exists', 170);
                }
            }

			$fsid = $f->giveFreespins($u->id,$u->office_id,$calced['game_id'],$calced['cnt'],$calced['zzz'],$lines,$dentab_index,'api',true,$this->_req_params,false,null,$expire,$fs_uuid,$start);

            if($fsid) {
                $this->_ans['error']=0;
                $this->_ans['error_code'] = 0;
                $this->_ans['error_message'] = '';
                $this->_ans['data'] = [
                        'fs_count'=>$calced['cnt'],
                        'fs_total_sum'=>$calced['zzz']*$calced['cnt'],
                        'fs_lines'=>$lines,
                        'fs_dentab_index'=>$dentab_index,
                        'fs_game'=>$g->visible_name,
                        'fs_gameid'=>$g->id,
                        'fs_id'=>$fsid,
                ];
            }
            else {
                $this->error('can not pay freespins', 222);
            }
			
			if($force_activate && $fsid) {
                $f->activateFreespins($fsid);
            }
        }
    }

    public function action_getgame()
    {
		
		logfile::create(date('Y-m-d H:i:s')." getgame request; params: ".print_r($_GET,1),'getgame');
		
        $game_id = (int) $this->request->param('id');

        if(!$game_id) {
            $this->error('empty game id','150');
        }

        $agt = $game_id>100000;

        $g = new Model_Game($game_id);

        if(!$g->loaded()) {
            $this->error('game not found', '152');
        }

        $login = arr::get($this->_req_params,'login');

        if(empty($login)) {
            $this->error('empty login', '155');
        }

        $login.='-'.$this->_office_id;

        $u = new Model_User(['name'=>$login]);

        $fs = $u->getFreespins($u->id, true,false,$g->id);

        if($fs && $fs->loaded()) {
            $auto=false;
            if(empty($fs->starttime) || (in_array($fs->src,['cashback','lucky']) && $fs->active==0 && $fs->updated<=3)) {
                $fs->starttime=time();
                $fs->save();
            }

            if(
                    (in_array($fs->src,['cashback','lucky']) && $fs->active==0 && (($fs->updated>3 || $fs->starttime+Date::MINUTE*20<=time()) && $auto=true)) ||
                    ($fs->active==1 && $fs->starttime+Date::MINUTE*20<=time() && $auto=true)) {

                $fs->declineFreespins($fs->id,$auto);
            }
        }

        //приоритет Lucky Spins
        //Daily spins
        //Api spins

        $gived_fs=auth::user()->checkEvents($g);

        if($gived_fs) {
            auth::user()->joinEvent($g,$gived_fs);
        }
        else {
            $gived_fs = $u->pay_bia(true);
        }


        if($u->last_game && $g->name != $u->last_game) {
            $g = new Model_Game(['name'=>$u->last_game]);
        }

        if($gived_fs) {
            $fs = new Model_Freespin(['user_id'=>$u->id]);
        }

        $link_params = [];

        //убрано 23.11.23
        /*if($fs && $fs->loaded() && $g->name != $fs->game->name) {
            $link_params[]='back='.$g->name;
            $g = new Model_Game(['name' => $fs->game->name]);
        }*/


        if($u->last_game && $g->name != $u->last_game) {
            $g = new Model_Game(['name'=>$u->last_game]);
        }

        if(!$g->loaded() && !$agt) {
            $this->error('game not found','151');
        }

        if($g->show==0 && !$agt) {
            $this->error('game not found','152');
        }

        if($g->brand=='egt' && !$agt) {
            //$this->error('work on regulation','355');
        }

        $og = new Model_Office_Game([
            'office_id' => $this->_office_id,
            'game_id' => $game_id,
        ]);

        if(!$og->loaded() && !$agt) {
            $this->error('game not found','153');
        }

        if($og->enable == 0 && !$agt) {
            $this->error('game not found','154');
        }


        $lang = arr::get($this->_req_params,'lang');
        $display_langs = arr::get($this->_req_params,'display_langs', 'yes');

        $lg = '';

        if($lang) {
            $lg .= $lang;
        }

        $lg.='-'.$display_langs;
        $u->lang = $lg;

		if(th::isB2B($this->_o->owner)) {
            $this->checkB2BTestUser($u,$this->_o->currency->code);
        }

        if(!$u->loaded()) {

            $pas = mt_rand(10000000, 999999999);

            $u->name = $login;
            $u->office_id = $this->_office_id;
            $u->salt = rand(1, 10000000);
            $u->password = auth::pass($pas, $u->salt);
            $u->api = 1;
            $u->api_session_id = arr::get($this->_req_params,'session_id');
            $u->amount = 0;
            $u->api_key = guid::create();
            $u->visible_name = arr::get($this->_req_params,'login');
            $u->external_id = arr::get($this->_req_params,'login');
            $u->api_key_time = time();
            $u->save()->reload();
        }
        else {
            $u->api_session_id = arr::get($this->_req_params,'session_id');
            $u->visible_name = arr::get($this->_req_params,'login');
            $u->external_id = arr::get($this->_req_params,'login');
            $u->api_key = guid::create();
            $u->api_key_time = time();
            $u->save()->reload();
        }

        //todo request balance from partner url
        $ga = new gameapi();

        if($this->_o->apitype==0 && !$ga->checkBalance(arr::get($this->_req_params,'login'), $this->_o->id, $this->_o->gameapiurl,arr::get($this->_req_params,'session_id'))) {
            $this->error('user not found', 401);
        }

        $u->reload();

        if(!$u->canPlay($g->name) && !$agt) {
            $this->error('can not play', 402);
        }

        $is_demo=arr::get($this->_req_params, 'demo')=='1';

        if($is_demo && ($g->type=='live' || $g->brand=='netent')) {
            $this->error('can not demo', 403);
        }

        $noiframe = arr::get($this->_req_params, 'noiframe')=='1';

        $closeurl = arr::get($this->_req_params, 'closeurl');

        if($closeurl!==urldecode($closeurl)) {
            $closeurl=urlencode($closeurl);
        }

        $specurl = arr::get($this->_req_params, 'specurl');

        if($specurl!==urldecode($specurl)) {
            $specurl=urlencode($specurl);
        }

        if(!Valid::url($closeurl)) { //not work!
            //$closeurl=false;
        }

        $no_close = (int) arr::get($this->_req_params, 'no_close',0);
		
		if(th::isB2B($this->_o->owner)) {
            $no_close=1;
        }

        if($noiframe && !$closeurl && $no_close==0) {
            $this->error('invalid CLOSE URL'.Debug::vars(arr::get($this->_req_params, 'closeurl')), 409);
        }

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';

        $exittxt = __('Выход');
        $display = 'none';

        $js_close_url=!empty($closeurl)?$closeurl:'/';

        $js=<<<JS
                <script>
                    if(typeof closeGame !='function') {
                        function closeGame() {
                            try{
                                window.top.location = '{$js_close_url}';
                            }
                            catch (e) {
                                console.log(e);
                            }
                            location = '{$js_close_url}';
                        }
                    }
                    window.onmessage=function(event) {
                        if (event.data=='closeGame' || event.data=='close') {
                            closeGame();
                        }
                    }
                </script>
JS;


		if($this->_o->id==1006) {
            $this->_domain=str_replace('content.','test-sa.',$this->_domain);
        }

		if($this->_o->owner==1023 && $this->_o->currency->code=='ZAR') {
            $this->_domain=str_replace('content.','content-sa.',$this->_domain);
        }
		
		

        if($agt) {
            $link = $this->_domain.'/games/apiagt/' . ($game_id-100000);
        }
        else {
            $link=$g->get_link($this->_domain);
        }

        
        $link_params[]='no_close='.$no_close;

        $fulllink=$link.'?user='.$u->api_name.'&token='.$u->api_key.'&closeurl='.$closeurl.'&no_close='.$no_close;

        $this->_ans['data'] = $js.'<iframe allow="autoplay" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%" height="100%" src="'.$fulllink.'"></iframe>';

        if($g->tech_type=='f') {
            $this->_ans['data'].=block::flashcheck();
        }

        if($specurl) {
            $link_params[]='specurl='.$specurl;
        }

        if($noiframe) {
            $link_params[]='user='.$u->api_name;
            $link_params[]='token='.$u->api_key;
            if($closeurl) {
                $link_params[]='closeurl='.$closeurl;
            }
            $this->_ans['data']=$link.'?'.implode('&',$link_params);
        }
    }

    public function action_balancein()
    {
        $login = arr::get($this->_req_params,'login').'-'.$this->_office_id;

        if(empty($login)) {
            $this->error('empty login', '155');
        }

        $balance = (float) arr::get($this->_req_params,'balance');

        if(empty($balance) || $balance<0) {
            $this->error('empty balance', '156');
        }

        $request_id = (int) arr::get($this->_req_params,'request_id');

        if(!$request_id) {
            $this->error('empty request_id', '157');
        }

        if($this->_o->apitype==0) {
            $this->error('api type error', 601);
        }

        $apireq = new Model_Apireq([
                'office_id'=>$this->_office_id,
                'request_id'=>$request_id,
        ]);

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';

        if($apireq->loaded()) {

            if($apireq->type!='in') {
                $this->error('incorrect request id',452);
            }

            $this->_ans['data'] = [
                    'balance_before'=>(float) $apireq->balance_before,
                    'balance_after'=>(float) $apireq->balance_after,
                    'repeat'=>1,
            ];
            $this->response();
            exit;
        }

        if($this->_o->amount<$balance) {
//            $this->error('not enough office balance', 602);
        }

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {

            $pas = mt_rand(10000000, 999999999);

            $u->name = $login;
            $u->office_id = $this->_office_id;
            $u->salt = rand(1, 10000000);
            $u->password = auth::pass($pas, $u->salt);
            $u->api = 1;
            $u->amount = 0;
            $u->api_key = guid::create();
            $u->api_key_time = time();
            $u->visible_name = arr::get($this->_req_params,'login');
            $u->external_id = arr::get($this->_req_params,'login');
            $u->save()->reload();
        }
        else {
            $u->api_key = guid::create();
            $u->api_key_time = time();
            $u->visible_name = arr::get($this->_req_params,'login');
            $u->save()->reload();
        }

        database::instance()->begin();

        if($this->_o->apienable AND $this->_o->apitype==1) {
            db::query(Database::UPDATE,'update offices set amount=amount-:balance where id=:id')
                    ->param(':id',$this->_office_id)
                    ->param(':balance',$balance)
                    ->execute();
        }

        $sql='update users
                set amount=amount+:balance
                where id=:uid
                RETURNING amount as am';

        $new_amount_res = db::query(1, $sql)
                    ->param(':balance', (float) $balance)
                    ->param(':uid', $u->id)
                    ->param(':last_bet_time', time())
                    ->execute()
                    ->as_array('am');

        $new_amount = arr::get(current($new_amount_res),'am',0);

        $apireq->balance_before = (float) $u->amount();
        $apireq->balance_after = (float) $new_amount;

        $apireq->request_id = $request_id;
        $apireq->office_id = $this->_office_id;
        $apireq->user_id = $u->id;
        $apireq->amount = $balance;
        $apireq->type = 'in';
        $apireq->save();

        database::instance()->commit();

        $this->_ans['data'] = [
                'balance_before'=>(float) $u->amount(),
                'amount'=>$balance,
                'balance_after'=>(float) $new_amount,
                'repeat'=>0,
        ];
    }

    public function action_balanceout()
    {
        $login = arr::get($this->_req_params,'login').'-'.$this->_office_id;

        if(empty($login)) {
            $this->error('empty login', '155');
        }

        $balance = (float) arr::get($this->_req_params,'balance');

        if($balance<0) {
            $this->error('invalid balance', '156');
        }

        $request_id = (int) arr::get($this->_req_params,'request_id');

        if(!$request_id) {
            $this->error('empty request_id', '157');
        }

        $apireq = new Model_Apireq([
                'office_id'=>$this->_office_id,
                'request_id'=>$request_id,
        ]);

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';

        if($apireq->loaded()) {
            if($apireq->type!='out') {
                $this->error('incorrect request id',452);
            }
            $this->_ans['data'] = [
                    'balance_before'=>(float) $apireq->balance_before,
                    'balance_after'=>(float) $apireq->balance_after,
                    'repeat'=>1,
            ];
            $this->response();
            exit;
        }

        $u = new Model_User(['name'=>$login]);

        if(!$u->loaded()) {
            $this->error('user not found', 401);
        }

        if($u->amount()<$balance) {
            $this->error('not enough user balance', 603);
        }

        database::instance()->begin();


        if($this->_o->apienable AND $this->_o->apitype==1) {
            db::query(Database::UPDATE,'update offices set amount=amount+:balance where id=:id')
                    ->param(':id',$this->_office_id)
                    ->param(':balance',$balance)
                    ->execute();
        }

        $diff = $u->amount();
        $sql='update users
                set amount=0
                where id=:uid
                RETURNING amount as am';

        if($balance>0) {
            $diff = $balance;
            $sql='update users
                    set amount=amount-:balance
                    where id=:uid
                    RETURNING amount as am';
        }

        $new_amount_res = db::query(1, $sql)
                    ->param(':balance', $diff)
                    ->param(':uid', $u->id)
                    ->param(':last_bet_time', time())
                    ->execute()
                    ->as_array('am');

        $new_amount = arr::get(current($new_amount_res),'am',0);

        $apireq->balance_before = (float) $u->amount();
        $apireq->balance_after = (float) $new_amount;

        $apireq->request_id = $request_id;
        $apireq->office_id = $this->_office_id;
        $apireq->user_id = $u->id;
        $apireq->amount = $balance;
        $apireq->type = 'out';
        $apireq->save();

        database::instance()->commit();

        $this->_ans['data'] = [
                'balance_before'=>(float) $u->amount(),
                'amount'=>$balance,
                'balance_after'=>(float) $new_amount,
                'repeat'=>0,
        ];
    }

    public function after() {

        $this->response();
    }

    protected function error($message,$code) {
        $this->_ans['error']=1;
        $this->_ans['error_code'] = $code;
        $this->_ans['error_message'] = $message;
        $this->response();
    }

    protected function response()
    {

		if(in_array($this->request->action(),['getgame','givefreespins'])) {
            logfile::create(date('Y-m-d H:i:s').' '.$this->request->action()." response; params: ".print_r($this->_ans,1),$this->request->action());
        }

        echo json_encode($this->_ans);
        //Kohana::$log->add(Log::INFO,'action: '.$this->request->action().'; params: '.print_r($this->_req_params,1)."\n".Debug::vars($_SERVER));
        //todo need log
        exit;
    }
	
	protected function checkB2BTestUser(Model_User &$u,$curr_code) {
        $fullLogins=[
            "782655205_BDT","782708163_BDT","783362437_BDT","784160807_BDT","804510483_BDT","804695057_BDT",
            "824214427_BDT","876582743_BDT","877682017_BDT","1007926609_BOB","933181859_BOB","779602499_EUR",
            "782675329_EUR","783359015_EUR","784162665_EUR","802073075_EUR","804722705_EUR","876540929_EUR",
            "876779837_EUR","876781469_EUR","877432221_EUR","782019473_INR","782712645_INR","783362575_INR",
            "784163481_INR","804549793_INR","876581917_INR","877644615_INR","877681523_INR","782022225_KRW",
            "782713783_KRW","783362483_KRW","784164461_KRW","804553191_KRW","804707139_KRW","876648779_KRW",
            "877589481_KRW","877682419_KRW","804447661_MBT","804457193_MBT","804459531_MBT","804463469_MBT",
            "876601707_MBT","877660689_MBT","782019773_MMK","782714539_MMK","783362749_MMK","784166013_MMK",
            "804513189_MMK","876586537_MMK","876651245_MMK","877589001_MMK","877648267_MMK","877684693_MMK",
            "782019161_MNT","782714597_MNT","783362513_MNT","784166083_MNT","804559255_MNT","876585027_MNT",
            "877646863_MNT","779489543_RUB","781793467_RUB","782716185_RUB","783359071_RUB","784167805_RUB",
            "804511297_RUB","876564067_RUB","876642875_RUB","876755155_RUB","877680383_RUB","781798625_TRY",
            "781800145_TRY","782716873_TRY","782841157_TRY","782841495_TRY","802118243_TRY","804653283_TRY",
            "876581535_TRY","876647295_TRY","876756211_TRY","781791519_USD","782717777_USD","783359309_USD",
            "783382029_USD","784169379_USD","784184627_USD","802076399_USD","804680987_USD","876677947_USD",
            "876798047_USD","877681673_USD","781800145_UZS","782717995_UZS","783362083_UZS","784169621_UZS",
            "804645809_UZS","804718303_UZS","876646713_UZS","876755863_UZS","782019685_VND","782718075_VND",
            "784169711_VND","804638595_VND","804713855_VND","876583809_VND","877587821_VND","877646053_VND",
            "877776387_VND","781800411_XOF","782718255_XOF","783362357_XOF","784169873_XOF","804654231_XOF",
            "876647135_XOF","876756129_XOF",
        ];

        $u->test=(int) in_array(arr::get($this->_req_params,'login'),$fullLogins);
    }
}


