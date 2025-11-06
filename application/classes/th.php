<?php

class th {

	public static $moxie_config=array();


	public static function date($timestamp=null,$format='d.m.y H:i:s',$zone=null){

	if (empty($timestamp)){
		$timestamp=time();
	}

	if (empty($zone)){
		$zone=Cookie::get('timezone',3);
	}

	$timestamp+=60*60*$zone;

	return date($format, $timestamp);

	}




    public static function ObjectToArray($d){

		if (is_object($d)) {
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			return array_map(__METHOD__, $d);
		}
		else {

			return $d;
		}

	}

	public static function hidename($name){

		$len=strlen($name);
		$len2=round($len/2);
		$len2=$len2>0 ? $len2 : 1;

		$name=substr($name,0,$len-$len2);

		for($i=1;$i<=$len2;$i++){
			$name.='*';
		}

        $e=false;
        for($i=0;$i<=strlen($name)-1;$i++) {
            if($name[$i]=='@') {
                $e=true;
            }

            if($e) {
                $name[$i]='*';
            }
        }

		return $name;

	}

	public static function gamelink($cat,$name,$table=null){

		$games=Kohana::$config->load('games');

		if ($cat=='amarok'){
			$table=empty($table) ? 1 : $table;
			return "/{$cat}/{$name}/{$table}/game";
		}

		if (isset($games[$cat][$name]['href'])){

			$default=isset($games[$cat][$name]['default_table']) ? $games[$cat][$name]['default_table'] : '';
			$table=empty($table) ? $default : $table;

			$href=str_replace('{table}',$table,$games[$cat][$name]['href']);

			if(th::isMobile()) {
				$href = str_replace('/play','/',$href);
			}

			return $href;
		}

		if (isset($games[$cat][$name]['table'])){
			$table = empty($table) ? $games[$cat][$name]['default_table'] : $table;
			return "/{$cat}/{$name}/{$table}/play";
		}


		return "/{$cat}/{$name}/play";

	}

        public static function gamelinkimperium($cat,$id) {
            $action = 'index';

            if(th::isMobile()) {
                $action = 'mobile';
            }
            return "/i/{$action}/{$id}";
        }

	public static function isMobile(){
		$m = new MobileDetect();
		return $m->isMobile();
	}

	public static function force_dir($dir){

		if(file_exists($dir)){
			return true;
		}

		$d=explode(DIRECTORY_SEPARATOR,$dir);
		$dir='';
		foreach ($d as $cat){
			$dir.=$cat.DIRECTORY_SEPARATOR;
			if(!file_exists($dir)){
				mkdir($dir);
			}
		}


	}



	public static function packIP($ip=null){

		if (empty($ip)){
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		list($a, $b, $c, $d) = explode('.', $ip);
		return  ($a << 24) + ($b << 16) + ($c << 8) + $d;


	}



	public static function checkphone($phone) {
        return !preg_match('/\d{11,12}/',$phone);
        //не работала. не воспринимала номера с кодом 999 или 978
		return !preg_match("/^\+?([87](?!95[4-79]|99[^24579]|907|94[^0]|336)([348]\d|9[0-689]|7[027])\d{8}|[1246]\d{9,13}|68\d{7}|5[1-46-9]\d{8,12}|55[1-9]\d{9}|500[56]\d{4}|5016\d{6}|5068\d{7}|502[45]\d{7}|5037\d{7}|50[457]\d{8}|50855\d{4}|509[34]\d{7}|376\d{6}|855\d{8}|856\d{10}|85[0-4789]\d{8,10}|8[68]\d{10,11}|8[14]\d{10}|82\d{9,10}|852\d{8}|90\d{10}|96(0[79]|17[01]|13)\d{6}|96[23]\d{9}|964\d{10}|96(5[69]|89)\d{7}|96(65|77)\d{8}|92[023]\d{9}|91[1879]\d{9}|9[34]7\d{8}|959\d{7}|989\d{9}|97\d{8,12}|99[^456]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|380[34569]\d{8}|381\d{9}|385\d{8,9}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}|37[6-9]\d{7,11}|30[69]\d{9}|34[67]\d{8}|3[12359]\d{8,12}|36\d{9}|38[1679]\d{8}|382\d{8,9})$/", $phone);
	}


	public static function clearphone($phone) {
		return trim(trim($phone),'+');
        //return preg_replace("/[^0-9]/i","", $phone);
	}


	public static function tgsend($phone,$text,$params=[]){
        if(tgbot::phoneExists($phone)) {
            return tgbot::send(tgbot::getChatId($phone),$text,$params);
        }
        return false;
    }

	public static function smssend($phone,$text){

        $curl = curl_init();
        $log=new logfile();
		$url = "http://smsc.ru/sys/send.php?";
		$param = array(
				"login" => Kohana::$config->load("secret.sms_service_login"),
				"psw" => Kohana::$config->load("secret.sms_service_password"),
				"phones" => $phone,
				"mes" => UTF8::clean($text),
				"charset" => 'utf8',
				"sender" => Kohana::$config->load("secret.sms_sender_id"),
		);

		$param = http_build_query($param);
		$url = $url . $param;

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, "SMS SENDER");

		$result = curl_exec($curl);
		curl_close($curl);
        $log->sms="\r\n\r\n".th::date()." send  ".print_r($param,true)."Result: $result";
		if (strpos($result, "ERROR") !== false){
			return false;
		}

		return true;

	}

        //возращает рандомный массив
        public static function mixedRange($mixed=[],$length) {
            $a=[];
            for($i=0;$i<$length;$i++) {
                $a[]=$mixed[array_rand($mixed)];
            }
            return $a;
        }

        public static function ver() {
            if(Kohana::$environment==Kohana::DEVELOPMENT) {
                return time();
            }
            return '260825';
        }

        //возвращает рандомное значение
        public static function randValue($min, $max){
            return sqrt(mt_rand($min, $max) * mt_rand($min, $max));
        }

        public static function n1($var){
            return number_format($var, 1, ",", "");
        }

    public static function domain() {
        return $_SERVER['HTTP_HOST'];
    }

    public static function domainImg() {
        return '';
    }

    public static function days_text($day_number) {
        $day_text = '';

        switch ($day_number) {
            case 1:
                $day_text = 'день';
                break;
            case 2:
            case 3:
                $day_text = 'дня';
                break;
            case 7:
            case 30:
                $day_text = 'дней';
                break;
            default :
                $day_text = 'дней';
        }

        return $day_text;
    }

    public static function explode_date($time=null,$n=null) {
        if($time == null) {
            return $time;
        }
        $d = explode('-',date('Y-m-d',$time));
        if(!is_null($n)) {
            return (int) $d[$n];
        }
        return $d;
    }

    public static function keywords($type, $game) {
        $conf_words = th::ObjectToArray(kohana::$config->load('gamekeywords'));

        $words = $conf_words[$type][$game] ?? '';

        return $words;
    }


    public static function dbname($name){
        $newname=strtolower(str_replace(' ','',$name));
        return $newname;
    }
    public static function retpay(){
        $sql = "SELECT game, counters.out, counters.in FROM counters where counters.in>0 and bettype!='double' and type in ('novomatic','igrosoft')";
        $retpay = db::query(Database::SELECT, $sql)->execute()->as_array('game');
        return $retpay;
    }
    public static function maxpayin(){
        $sql = "select max(counters.in) from counters";
        $res = db::query(Database::SELECT, $sql)->execute()->as_array();
        $maxp = $res[0]['max'];
        return $maxp;
    }
    public static function favegame(){
        $sql_f = "SELECT games FROM users_favourite WHERE user_id=:user_id";
        $fg = db::query(Database::SELECT, $sql_f)
                ->param(':user_id',auth::parent_acc()->id)
                ->execute()
                ->as_array();
        return $fg;
    }

    public static function games_models() {
        $sql_games = <<<SQL
            Select g.*
            From games g JOIN office_games og ON g.id = og.game_id
            Where office_id = :office_id
                AND g.show in :show
                AND og.enable = 1
SQL;

        $show = [1];

        if(th::isMobile()) {
            $show[] = 2;
        }

        $res_games  = db::query(1, $sql_games)->parameters([
            ':office_id' => OFFICE,
            ':show' => $show
        ])->execute('games')->as_array();

        $games_ids = [];
        $games = [];

        foreach ($res_games as $g) {
            $games_ids[] = $g['id'];
        }

        $games_models = orm::factory('game')->where('id', 'in', $games_ids)->find_all();

        foreach ($games_models as $g) {
            $games[$g->type][] = $g;
        }

        return $games;
    }

    public static function our_games() {
        $sql_games = <<<SQL
            Select g.*
            From games g JOIN office_games og ON g.id = og.game_id
            Where office_id = :office_id
                AND provider = 'our'
                AND g.show in :show
                AND og.enable = 1
SQL;

        $show = [1,3];

        if(th::isMobile()) {
            $show = [1,2];
        }

        $res_games  = db::query(1, $sql_games)->parameters([
            ':office_id' => OFFICE,
            ':show' => $show
        ])->execute('games')->as_array();

        $games = [];

        $static_domain = kohana::$config->load('static.static_domain');

        foreach ($res_games as $g) {
            $g['image'] = $static_domain.$g['image'];
            $games[$g['brand']][$g['name']] = [
                'image' => $g['image'],
                'visible_name' => $g['visible_name'],
            ];
        }

        return $games;
    }

    public static function mainpage($cat){
            $sql_games = <<<SQL
                Select g.*
                From games g JOIN office_games og ON g.id = og.game_id
                Where office_id = :office_id
                    AND g.show in :show
                    AND og.enable = 1
                    AND provider = 'imperium'
SQL;

            $show = [1,3];
            $tech_type = ['h','f','fh'];
            if(th::isMobile()) {
                $show = [1,2];
                if(OFFLINE) {
                        $tech_type = ['h','fh'];
                        $sql_games .= ' and g.tech_type in :tech_type ';
                }
            }

            $res_games  = db::query(1, $sql_games)->parameters([
                ':office_id' => OFFICE,
                ':show' => $show,
                ':tech_type' => $tech_type,
            ])->execute('games')->as_array();

            $imperium_games = [];

            $static_domain = kohana::$config->load('static.static_domain');

            foreach ($res_games as $g) {

                $imperium_games[$g['brand']][] = [
                    'game_id' => $g['external_id'],
                    'name' => $g['visible_name'],
                    'type_system' => $g['brand'],
                    'image' => $static_domain.'/games/imperium/' . $g['external_id'] . '.png',
                ];
            }

            $imperium_cats = array_keys($imperium_games);


            $cat = array_unique(array_merge($cat, $imperium_cats));
            return array($imperium_games, $cat);
    }
    public static function filPopImpGames($imperium_games, $retpay, $maxp){
        $imperium_games_s=[];
        foreach($imperium_games as $ig_cats => $iglist){
            foreach($iglist as $igames){
                $gn=th::dbname($igames['name']);
                if(isset($retpay[$gn]) AND round($retpay[$gn]['in']/$maxp,2)>0.05) {
                    $imperium_games_s[$ig_cats][]=$igames;
                }
            }
        }
        return $imperium_games_s;
    }
    public static function filPopGames($games, $retpay, $maxp){
        $games_s=[];
        foreach($games as $g_cats => $g_list){
            foreach($g_list as $gms =>$g_name){
                if(isset($retpay[$gms]) AND round($retpay[$gms]['in']/$maxp,2)>0.05) {
                    $games_s[$g_cats][$gms]=$g_name;
                }
            }
        }
        $games_count=0;//Число отфильтрованных игр
        foreach($games_s as $v){
                $games_count+=count($v);
        }
        if($games_count<5){//Добавляем игр
            foreach($games as $g_cats => $g_list){
                foreach($g_list as $gms =>$g_name){
                    if(in_array($gms,['luckyladycharmd', 'crazymonkey', 'dolphinsd', 'betonpoker', 'sizzlinghotd', 'resident', 'bananas', 'gnome', 'marcopolo', 'keks'])) {
                        $games_s[$g_cats][$gms]=$g_name;
                    }
                }
            }
        }

        return $games_s;
    }
    public static function filNewImpGames($imperium_games, $new_games){
        $imperium_games_s=[];
        foreach($imperium_games as $ig_cats => $iglist){
            foreach($iglist as $igames){
                $gn=th::dbname($igames['name']);
                if(in_array($gn, $new_games)) {
                    $imperium_games_s[$ig_cats][]=$igames;
                }
            }
        }

        return $imperium_games_s;
    }
    public static function filNewGames($games, $new_games){
        $games_s=[];
        foreach($games as $g_cats => $g_list){
            foreach($g_list as $gms =>$g_name){
                if(in_array($gms, $new_games)) {
                    $games_s[$g_cats][$gms]=$g_name;
                }
            }
        }

        return $games_s;
    }
    public static function filFavImpGames($imperium_games, $decfg){
        $imperium_games_s=[];
        foreach($imperium_games as $ig_cats => $iglist){
            foreach($iglist as $igames){
                $gn=th::dbname($igames['name']);
                if(isset($decfg) AND in_array($gn, $decfg)) {
                    $imperium_games_s[$ig_cats][]=$igames;
                }
            }
        }

        return $imperium_games_s;
    }
    public static function filFavGames($games, $decfg){
        $games_s=[];
        foreach($games as $g_cats => $g_list){
            foreach($g_list as $gms =>$g_name){
                if(isset($decfg) AND in_array($gms, $decfg)) {
                    $games_s[$g_cats][$gms]=$g_name;
                }
            }
        }

        return $games_s;
    }
    public static function gamerating(){

        $a=new Model_Userfavourite();
        $userrates=$a->gamesrating();
        $allgames=[];
        foreach($userrates as $user){
            if($user['games']!=NULL)
            {

                $games=json_decode($user['games'],true);
                if(isset($games))
                    {
                        $games_val= array_values($games);
        }
                $allgames= array_merge($games_val, $allgames);
        }
    }
        $rates= array_count_values($allgames);

        return $rates;

    }
    public static function gamelist($show=-1){
        $sql = "Select lower(name) as name, visible_name, brand, provider from games";

        if($show>=0) {
            $sql.=' where show=:show';
        }

        $result = db::query(Database::SELECT, $sql)->param(':show',$show)->execute('games')->as_array('name');
        return $result;
    }

    public static function wordform($number=0){
        switch($number){
            case $number%10==0 OR $number%10>=5 OR $number==0:
                return 'ОВ';
                break;
            case $number%10>=2 AND $number%10<=4:
                return 'А';
                break;
            default:
                return '';
        }
    }
    public static function bonus_time_str($t=0){
        if(!$t){
            $t=kohana::$config->load('static.active_time_bonus');
        }
        $d=round(floor($t/86400),0);
        $h=round(floor(($t-$d*86400)/3600),0);
        $m=round(floor(($t-$d*86400-$h*3600)/60),0);
        $s=$t - $d*86400 - $h*3600 - $m*60;

        $d=($d!=0?strval($d):'0');
        $h=($h!=0?strval($h):'00');
        $m=($m!=0?strval($m):'00');
        $s=($s!=0?strval($s):'00');

        $time = $d.'.'.$h.':'.$m.':'.$s;
        return $time;
    }

    public static function get_subscriber_for_push($endpoint) {
        $endpoint_parsed = parse_url($endpoint);
        $b = explode('/', $endpoint_parsed['path']);
        $subscriber_id = end($b);

        return $subscriber_id;
    }

    public static function currgamelink($game_id) {
        $game = new Model_Game($game_id);

        return $game->get_link();
    }

    public static function get_enabled_games($our=false) {
        $games = orm::factory('game');
        if(th::isMobile()) {
            $games->where('show', 'in', ['1','2']);
        }
        else {
            $games->where('show', 'in', ['1','3']);
        }

		$games->join('office_games')
                ->on('game.id', '=', 'office_games.game_id')
                ->where('office_id','=',OFFICE)
                ->where('office_games.enable','=',1);

        if($our) {
            $agames=[];
            foreach($games->where('provider','=','our')->find_all() as $g) {
                $agames[$g->name]=$g->visible_name;
    }
            return $agames;
        }

        return $games->find_all();
    }

    public static function get_shares_with_type($type) {
        $promos = orm::factory('share')->where('enabled', '=', 1)->and_where('theme', '=', THEME);

        if(is_array($type)) {
            $promos->where_open();
            foreach ($type as $k => $t) {
                if($k==0) {
                    $promos->where('type', '=', $t);
                } else {
                    $promos->or_where('type', '=', $t);
                }
            }
            $promos->where_close();
        } else {
            $promos->and_where('type', '=', $type);
        }

        return $promos->order_by('created', 'desc')->find_all();
    }

    public static function sequence_next_value($name) {
        $sql = <<<SQL
            select nextval(:name)
SQL;
        $res = db::query(1, $sql)->param(':name', $name)->execute();

        return $res[0]['nextval']??0;
    }
    public static function to_csv($data, $time_from, $time_to, $tablename='export'){
        $str = '';
        foreach($data as $d)
        {
            foreach($d as $v){
                $str .= $v."\r\n";
            }
        }
        $str = iconv("UTF-8","CP1251", $str);//Кодировка для экселя
        $fh = fopen('php://output', 'w');
        ob_start();
        fwrite($fh, $str);
        $string = ob_get_clean();
        fclose($fh);
        $filename = $tablename .'_'. $time_from .'_' . $time_to;
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv";');
        header('Content-Transfer-Encoding: binary');
        exit($string);
    }
    public static function main_domain(){

        if(DEMO_DOMAIN){
            $md = explode('.',$_SERVER['HTTP_HOST']);
            $main = implode('.',[$md[count($md)-2],$md[count($md)-1]]);
//            $main = implode('.',[$md[1]]);
        }else{
            $main=$_SERVER['HTTP_HOST'];
        }
        return $main;
    }

    public static function nic() {
        $nic = nic::randomName();
        $u = new Model_User(['name'=>$nic]);

        if($u->loaded()) {
            $nic = self::nic();
        }

        return $nic;
    }

    /*
     * возвращает email/phone для связи с нами из конфига tpcontacts
     */
    public static function tp_contact($theme, $type='email') {
        $contact = '';
        $config = kohana::$config->load("tpcontacts.$theme");

        if(isset($config[$type])) {
            $contact = $config[$type];
        }

        return $contact;
    }

    /*
     * example
     * @param $name string Lacky Lady Charm
     * returning LLC
     */
    public static function short_name_game($name, $limit=3) {
        $short_name = '';

        $name_parts = explode(' ', $name);

        foreach ($name_parts as $part) {
            if(trim($part) != '' AND intval($part)) {
                $short_name .= $part;
            } elseif(trim($part) != '') {
                $short_name .= substr($part,0,1);
            }
        }

        if(strlen($short_name)==1) {
            $short_name = substr($name,0,$limit);
        }

        return $short_name;
    }

    public static function gamestars() {
        $game_rating = self::gamerating();

        $max_stars = ['luckyladycharmd', 'crazymonkey', 'dolphinsd', 'betonpoker', 'sizzlinghotd', 'resident', 'bananas', 'gnome', 'marcopolo', 'keks'];
        $min = min($game_rating);

        $stars = [];

        foreach ($game_rating as $game => $v) {
            $value = ceil($v/$min);
            $stars[$game] = $value>5?5:$value;
        }

        foreach ($max_stars as $game) {
            $stars[$game] = 5;
        }

        return $stars;
    }

    public static function games_names($provider=null) {
        $sql = <<<SQL
            Select visible_name
            From games
SQL;
        if($provider) {
            $sql .= <<<SQL
                Where provider = :provider
SQL;
        }

        $res = db::query(1, $sql)->param(':provider', $provider)->execute('games')->as_array('visible_name');

        $games_names = array_keys($res);

        return $games_names;
    }

    public static function number_format($number,$comma='.',$decimals=2) {
        if (empty($number)){
//            return '';
        }

        $v = $number - floor($number);

        if(!$v) {
            $number = number_format($number, $decimals-2, $comma, ' ');
        } else {
            $number = number_format($number, $decimals, $comma, ' ');
        }

        return $number;
    }

    public static function float_format($float,$mult) {
        if(empty($float)) { return '0'; }

        if($mult==0) {
            return th::number_format($float);
        }

        $float = rtrim(sprintf('%.'.$mult.'F',$float),'0');
        $check=explode('.',$float);
        if(empty($check[1])) {
            return th::number_format($check[0]);
        }
        if($mult>2) {
            return rtrim(th::number_format($float,'.',$mult),'0');
        }
        return th::number_format($float);
    }

    public static function holiday_image() {
        $holidays = kohana::$config->load('holidays');

        $image = '';

        foreach ($holidays as $h) {
            if(date('m-d') >= $h['date_start'] AND date('m-d') <= $h['date_end']) {
                $image = $h['image'];
            }
        }

        return $image;
    }

    public static function default_office_games($office_id) {

        $sql = 'select * from games where show>0 and branded=0';
        $params=[];
        if(PROJECT==1) {
            $o = new Model_Office($office_id);

            if($o->loaded() && $o->owner!=1023) {

                $sql.=' and name not in :pars';
                $params[]='besthottest5';
                $params[]='besthottest20';
                $params[]='besthottest40';
                $params[]='besthottest100';
                $params[]='6luckyclover20';
                $params[]='6luckyclover40';
            }
        }

        $games=array_keys(db::query(1, $sql)->param(':pars',$params)->execute('games')->as_array('id'));

        //оставил на случай когда нужно обновить список из файла
//        $sql_u = 'update games set show = case when show=2 then 2 else 1 end where id in :ids;';
//        echo db::query(database::UPDATE, $sql_u)->param(':ids', $games)->compile(Database::instance());

        $sql_i = 'insert into office_games(office_id, game_id, ENABLE) VALUES';

        foreach($games as $i=>$g) {
            $sql_i.='('.$office_id.','.$g.',1)';
            if($i<count($games)-1) {
                $sql_i.=',';
            }
        }

        $sql_i.=' on conflict(office_id,game_id) do nothing';
        db::query(Database::INSERT,$sql_i)->execute('games');
    }

    public static function vd($vars,$exit=true) {
        echo Debug::vars($vars);
        if($exit) {
            exit();
        }
    }
    public static function veksUpdate($uid, $data, $bets = [], $messages = []) {

        $path = DOCROOT . "/veksel/data/".(substr($uid, -1)??"0");
        if( !is_dir($path)) {
            mkdir($path, 02777);
            chmod($path, 02777);
        }
        $path .= DIRECTORY_SEPARATOR.(substr($uid,-2,1)??"0");
        if( !is_dir($path)) {
            mkdir($path, 02777);
            chmod($path, 02777);
        }

        $file = $path .DIRECTORY_SEPARATOR. $uid . ".json";
        $ans = [];
        if(file_exists($file)) {
            $ans = json_decode(file_get_contents($file),1);
        }

        $bet_id = $data['bet_id']??null;
        $amount = $data['amount']??null;
        $win = $data['win']??null;
        $balance = isset($data['balance'])?$data['balance']:(isset($ans['balance'])?base64_decode($ans['balance']):0);
        if($bet_id) {
            $bet = [
                'bet_id' => $bet_id,
                'number' => $data['number']??null,
                'lot' => $data['lot']??null,
                'amount' => base64_encode($amount),
                'win' => base64_encode($win),
                'veksel_value' => base64_encode($data['veksel_value']??null),
                'stop_price' => base64_encode($data['stop_price']??null),
                'nominal' => base64_encode($data['nominal']??null),
            ];
            if(empty($ans["bets"])) {
                $ans["bets"] = [$bet];
            }
            else {
            $bets = $ans["bets"];
                if($bets[0]['number']==$bet['number']) {
                    unset($bets[0]);
                }
                array_unshift($bets,$bet);
            $bets = array_slice($bets,0,50, true);//оставляем 50 значений
            $ans["bets"]=$bets;
        }
        }

        $ans["balance"] = base64_encode($balance - ($data['veksel_value']??0)); //??
        $ans["messages"]=$data['messages']??[];
        $ans["updated"]=time();


        $res = json_encode($ans);
        file_put_contents($file,$res);

    }

    public static function getBit($bitMask, $bitNum)
    {
        return $bitMask & 1 << $bitNum;
    }


    public static function array2BitMask(array $inputArray)
    {
        $bitMask = 0;
        foreach ($inputArray as $val)
        {
            $bitMask = $bitMask | (1 << $val);
        }
        return $bitMask;
    }

	public static function hard_sort_games() {
        return [1 =>
                'bookofrad',
                'luckyladycharmd',
                'bananasplash',
                'downunder',
                'unicornmagic',
                'oliversbar',
                'dolphinsd',
                'bookofra',
                'emperorschina',
'richesofindia',
                'kingofcards',
                'columbusd',
				'starattraction',
				'coldspell',
                'themoneygame',
                'keks',
				'attila',
                'gnome',
                'luckyhaunter',
                'resident',
                'crazymonkey',
                'alwayshot',
                'hotcherry',
                'threee',
                'ultrahot',
                'royaltreasures',
                'marcopolo',
                'pharaohsgold2',
                'bananas',
                'luckyladycharm',
                'sizzlinghot',
                'sizzlinghotd',
                'dolphins',
        ];
    }
    //save user balance

    public static function urlb($uid) {
        $path = "/s/ub/".(substr($uid, -1)??"0");
        $path .= '/'.(substr($uid,-2,1)??"0");
        return $path .'/'. $uid . ".json";
    }

    public static function ub($uid,$amount) {
        $path = DOCROOT . "/s/ub/".(substr($uid, -1)??"0");
        if( !is_dir($path)) {
            mkdir($path, 02777);
            chmod($path, 02777);
}
        $path .= DIRECTORY_SEPARATOR.(substr($uid,-2,1)??"0");
        if( !is_dir($path)) {
            mkdir($path, 02777);
            chmod($path, 02777);
        }

        $file = $path .DIRECTORY_SEPARATOR. $uid . ".json";
        file_put_contents($file,$amount);
    }



    public static function flag($flag){
        if (defined('FLAGS') and in_array($flag,FLAGS)){
            return true;
        }
        return false;

    }


    public static function officeIsEnable($id){

        //если есть DISABLED, то ENABLED игнорируется
        if(defined('DISABLED_OFFICES')){
            return !in_array($id,DISABLED_OFFICES);
        }

        if(defined('ENABLED_OFFICES')){
            return in_array($id,ENABLED_OFFICES);
        }

        //Если настроек нет, то все разрешено
        return true;

    }

    public static function importantAlert($txt){

        $sms=new Model_Sms;
        $sms->text=$txt;
        $sms->to='331325323';
        $sms->save();

        $sms=new Model_Sms;
        $sms->text=$txt;
        $sms->to='371527172';
        $sms->save();
    }

    public static function critAlert($txt){

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='331325323';
            $sms->bot='1';
            $sms->save();

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='371527172';
            $sms->bot='1';
            $sms->save();

    }

    public static function techAlert($txt){

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='331325323';
            $sms->bot='1';
            $sms->save();

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='847393';
            $sms->bot='1';
            $sms->save();
			
            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='7442893002';
            $sms->bot='1';
            $sms->save();

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='333168345';
            $sms->bot='1';
            $sms->save();

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='371527172';
            $sms->bot='1';
            $sms->save();
    }


    public static function ceoAlert($txt){

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='331325323';
            $sms->bot='1';
            $sms->save();

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='847393';
            $sms->bot='1';
            $sms->save();
			
            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='7442893002';
            $sms->bot='1';
            $sms->save();


            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='333168345';
            $sms->bot='1';
            $sms->save();

            $sms=new Model_Sms;
            $sms->text=$txt;
            $sms->to='371527172';
            $sms->bot='1';
            $sms->save();
    }

	public static function isBackupRunning() {
        return count(db::query(1,"select pid from pg_stat_activity where state='active' and query like 'copy %' and query not like 'select%'")->execute()->as_array())>0;
    }

	/**
     * Атомарно записывает данные в файл с автоматическим созданием директорий
     *
     * @param string $filename Путь к файлу
     * @param mixed $data Данные для записи
     * @param int $mode Права доступа к файлу (например, 0644)
     * @param int $dirMode Права доступа для создаваемых директорий (например, 0755)
     * @param bool $exclusive_lock Использовать эксклюзивную блокировку
     * @return bool true в случае успеха, false при ошибке
     * @throws Exception Если не удается записать или создать директории
     */
    public static function atomicFileWrite($filename, $data, $mode = 0664, $dirMode = 02777) {
        // Создаем директорию если нужно
        $dir = dirname($filename);
        if (!is_dir($dir) && !mkdir($dir, $dirMode, true) && !is_dir($dir)) {
            throw new Exception("Directory creation failed");
        }

        // Открываем файл с режимом 'c+'
        $handle = fopen($filename, 'c+');
        if (!$handle) {
            throw new Exception("Failed to open file");
        }

        // Получаем эксклюзивную блокировку
        if (!flock($handle, LOCK_EX | LOCK_NB, $wouldBlock) || $wouldBlock) {
            fclose($handle);
            throw new Exception("File is locked by another process");
        }

        // Инициализируем переменную для отслеживания ошибок
        $error = null;

        // Устанавливаем позицию записи
        fseek($handle, 0, SEEK_END);

        // Записываем данные
        if (fwrite($handle, $data) === false) {
            $error = "Write failed";
        }

        // Устанавливаем права только если нет ошибок и файл новый
        if (!$error && !file_exists($filename)) {
            if (!chmod($filename, $mode)) {
                $error = "Chmod failed";
            }
        }

        // Сбрасываем буферы если нет ошибок
        if (!$error && !fflush($handle)) {
            $error = "Flush failed";
        }

        // Освобождаем блокировку
        flock($handle, LOCK_UN);
        fclose($handle);

        // Если были ошибки - бросаем исключение
        if ($error) {
            throw new Exception($error);
        }

        return true;
    }

    public static function lockProcess($key,$ttl=60*10){
            $value= time().'_'.mt_rand(1000,9999);
            $def_key=$key;
            $key='__process_lock__'.$key;
            if (dbredis::instance()->setNx($key, $value)){
                dbredis::instance()->set('lastExecProcess-'.$def_key, time());
                dbredis::instance()->expire('lastExecProcess-'.$def_key, Date::MONTH);
                if ($ttl!=0){
                    dbredis::instance()->expire($key, $ttl);
                }
                return true;
            }
            return false;


        }


    public static function unlockProcess($key){
        return dbredis::instance()->del('__process_lock__'.$key);
    }

    public static function create_agt_terminal($office_id,$host='https://terminal.site-domain.com')
    {

        $tpl_path = realpath(DOCROOT . 'files' . DIRECTORY_SEPARATOR . 'terminal_agt_tpl');
        $zip_path = realpath(DOCROOT . 'files');

        $zip      = new ZipArchive();
        $filename = $zip_path . DIRECTORY_SEPARATOR.'terminal_agt'.$office_id.'.zip';

        if($zip->open($filename,ZipArchive::CREATE) !== TRUE)
        {
            Kohana::$log->add(Log::ALERT,"cannot open <$filename>\n");
        }

        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tpl_path),RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach($files as $name => $file)
        {
            if(!$file->isDir())
            {
                $filePath     = $file->getRealPath();
                $relativePath = substr($filePath,strlen($tpl_path) + 1);

                if($file->getFilename()=='background.js') {
                    $str = '';

                    foreach(file($name) as $r) {
                        if(strpos($r,'<office_id>')!==false) {
                            $replace= str_replace('<host>',$host,trim($r));
                            $str.=' '.str_replace('<office_id>',$office_id,$replace);
                        }
                        else {
                            $str.=' '.trim($r);
                        }
                    }
                    $zip->addFromString($file->getFilename(),$str);
                }
                else {
                    $zip->addFile($filePath,$relativePath);
                }
            }
        }

        $zip->close();
    }


    public static function sendCurlError($type='') {

        $txt = 'CRITICAL! POST CURL| CONNECTION IS FALLING. '.date('i:s');
        if(!empty($type)) {
            $txt='['.$type.']'.$txt;
        }

        th::importantAlert($txt);
    }

    public static function checkCurlError() {

        $redis = dbredis::instance();

        $key='curlerr';
        $ttl=60;

        if($redis->setNx($key,0)) {
            $redis->expire($key, $ttl);
        }

        $n = $redis->incr($key);


        if($n>=3){
            //sms
            static::sendCurlError();
        }
    }
    public static function jpWidgetAGT($office_id, $size=[], $class=['prod-thumbnail'],$bgimg=null,$disable_currency=false) {

        $o=new Model_Office($office_id);

        if(!$o->loaded()) {
            return '';
        }

        $params=[
            'office_id' => $office_id,
        ];

        if($disable_currency) {
            $params['disable_currency']=1;
        }

        if(empty($size)) {
            $size=['250px','250px'];
        }

        $sign=$o->sign($params);

        if(!$o->secretkey) {
            return 'Not available';
        }

        /*$r=Request::factory(Kohana::$config->load('static.gameapi_domen').'/gameapi/jpwidget?' . http_build_query($params) . '&sign=' . $sign)
                ->options(CURLOPT_SSL_VERIFYPEER,0)
                ->options(CURLOPT_SSL_VERIFYHOST,0)
                ->execute();*/

        if($bgimg) {
            $params['bgimg']=$bgimg;
        }

        $frame_url='https://app.kolinz.xyz/gameapi/jpwidget?' . http_build_query($params) . '&sign=' . $sign;

        return '<iframe '.PHP_EOL
            . 'width="'.$size[0].'" '.PHP_EOL
            . 'height="'.$size[1].'" '.PHP_EOL
            . 'scrolling="no" '.PHP_EOL
            . 'class="'.implode(' ',$class).' iframe_agt_widget" '.PHP_EOL
            . 'src="'.$frame_url.'"></iframe>';
    }

    public static $_strict_for_FSback = ['keno','spinners','roshambo','sapper','tensorbetter','acesandfaces','jacksorbetter','aerosupabets','xplane','crushplane','aerobet'];

    public static function cantFSback($game_name) {
        return in_array($game_name,self::$_strict_for_FSback);
    }

    public static function getRandomGameId($office_id) {
        $sql = 'select g.id as game_id,g.name as game
                from games g
                join office_games og on g.id=og.game_id
                where g.show=1 and g.branded=0 and og.office_id = :oid and og.enable=1
                and g.name not in :cant
                order by g.sort nulls last limit 1';

        $res=db::query(1,$sql)
                ->param(':oid',$office_id)
                ->param(':cant',self::$_strict_for_FSback)
                ->execute()
                ->as_array();

        return $res;
    }

    public static function saveUserDevice($game,$params=[]) {

        $params=arr::extract($params,['android','mobile','pc','ios','user_id','ip'],0);

        if(empty($params)) {
            return;
        }

        $o_id = auth::user()->office_id;


        if(!in_array($o_id,[1075,1076,1081])) {
            return;
        }

        $date = date('Y-m-d');

        $params['total']=1;

        $up_sql = [];

        foreach($params as $f=>$v) {
            $up_sql[]=$f.'='.$f.'+'.$v;
            $in_sql_vals[]='\''.$v.'\'';
        }


        $in_sql_vals[]='\''.$date.'\'';
        $in_sql_vals[]='\''.$o_id.'\'';
        $in_sql_vals[]='\''.$game.'\'';

        $up_sql = implode(',',$up_sql);
        $up_sql = 'update devicestats set '.$up_sql;
        $up_sql.=' where office_id='.$o_id.' and date=\''.$date.'\' and game=\''.$game.'\' returning office_id';

        $in_sql_keys = array_keys($params);
        $in_sql_keys[]='date';
        $in_sql_keys[]='office_id';
        $in_sql_keys[]='game';

        $in_sql = 'insert into devicestats('.implode(',',$in_sql_keys).') values ('.implode(',',$in_sql_vals).')';

        set_error_handler(function(int $number, string $message) {
            Kohana::$log->add(Log::ALERT,$message);
        });

        Database::instance()->direct_query($in_sql);

        restore_error_handler();
    }

    public static function getLangsTranslate($forcelang=null) {
        $langs = [];
        $d=I18n::$lang;
        $all_langs=array_keys(Kohana::$config->load('languages.lang'));
        if(!empty($forcelang)) {
            $all_langs[]=$forcelang;
        }
        foreach($all_langs as &$l) {

            if(in_array(OFFICE,[5320,1629]) && !in_array($l,['ru','en','lt'])) {
                unset($l);
                continue;
            }

            I18n::$lang=$l;
            foreach(Kohana::$config->load('agt.langs') as $e=>$k) {
                $langs[$l][$e]=__($k);
            }
        }
        I18n::$lang=$d;
        return $langs;
    }

    public static function getMoonGames() {
        return ['tothemoon','aerosupabets','xplane','crushplane','aerobet'];
    }
    public static function isMoonGame($game) {
        return in_array($game,self::getMoonGames());
    }

    public static function isBlockedByIP() {

        $strict_countries=[
            'nl','aw','bq','cw','fr','mf','us','za','in','tr'
        ];

        $block=false;

        $ips=['185.14.31.73','52.28.204.3','88.208.40.206','82.135.195.168','195.123.217.136','195.123.217.176','195.123.218.14','88.119.186.63','185.14.30.124'];

        //betsson
        $ips[]='88.119.17.78';
        $ips[]='88.119.158.27';
		
		//b2b uat
        $ips[]='8.209.81.33';
        $ips[]='8.209.78.65';
		
		//adv
		$ips[]='78.60.146.22';
		$ips[]='52.212.75.16';
		$ips[]='54.247.14.45';
		
		//olimp
        $ips[]='77.72.132.251';
        $ips[]='77.72.132.252';
        $ips[]='77.72.132.253';
		
		//pinup
        $ips[]='18.153.161.245';
        $ips[]='3.122.78.220';
        $ips[]='3.68.9.38';

        $ip=$_SERVER['REMOTE_ADDR']??'';

        if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip=$_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        if(isset($_SERVER['HTTP_CF_IPCOUNTRY']) && !in_array($ip,$ips)) {
            $country=strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
            if(in_array($country,$strict_countries)) {
                $block=true;
            }

            if(OFFICE==1102 && $country=='ru') {
                $block=false;
            }

			if(INFINSOC_DOMAIN && $country=='us') {
                $block=false;
            }

            if((defined('DEMO_MODE') && DEMO_MODE) || in_array(OFFICE,[777,999,456])) {
                $block=false;
            }

            if($country=='za' && auth::user()->office->owner==1023) {
                $block=false;
            }

            if(auth::user()->office->is_test==1) {
                $block=false;
            }

            //28.11.23 new rules
            if(in_array($country,['lt','il'])) {
                $block=true;
            }

			if(in_array(OFFICE,[5320,1629,1672]) && $country=='lt') {
                $block=false;
            }

/*
            if(in_array($country,['lt']) && in_array(OFFICE,[777])) {
                $block=false;
            }
	*/		
			//test offices
            if(in_array(OFFICE,[1029,1028,1027,1024])) {
                $block=false;
            }
        }

        return $block;
    }
	
	public static function getMoonLimits(Model_Office $office,Model_Currency $currency) {
        $moon_min_bet=0.1;
        $moon_max_bet=100;
        $moon_max_win=10000;

        $r=[];

        $r['moon_min_bet']=!empty($currency->moon_min_bet)?$currency->moon_min_bet:$moon_min_bet;
        $r['moon_max_bet']=!empty($currency->moon_max_bet)?$currency->moon_max_bet:$moon_max_bet;
        $r['moon_max_win']=!empty($currency->moon_max_win)?$currency->moon_max_win:$moon_max_win;

        if(!empty($office->moon_min_bet)) {
            $r['moon_min_bet']=$office->moon_min_bet;
        }

        if(!empty($office->moon_max_bet)) {
            $r['moon_max_bet']=$office->moon_max_bet;
        }

        if(!empty($office->moon_max_win)) {
            $r['moon_max_win']=$office->moon_max_win;
        }

        if($office->bet_max>0 && $office->bet_max<$r['moon_max_bet']) {
            $r['moon_max_bet']=$office->bet_max;
        }

        return $r;

    }
	
	public static function isB2B($owner) {
        return in_array($owner,[1030,1219,1220]);
    }
}
