<?php

class Controller_Admin_Dashboard extends Controller_Admin_Base{

    public $mark       = 'Управление'; //имя
    public $model_name = 'empty'; //имя модели
    public $sh         = 'admin/dashboard'; //шаблон
    public $controller = 'dashboard';


    public function action_userbalance(){
        if(person::$role!='cashier') {
            throw new HTTP_Exception_403;
        }

        $user_id = (int) arr::get($_GET,'user_id');

        $user = new Model_User($user_id);

        if($user->office_id!=person::user()->office_id) {
            throw new HTTP_Exception_403;
        }

        echo $user->amount();
        exit;
    }
    public function action_rfid(){
        if(person::$role!='cashier') {
            throw new HTTP_Exception_403;
        }

        $rfid = arr::get($_GET,'rfid');

        if($rfid=='0' || $rfid=='') {
            throw new HTTP_Exception_404;
        }

        $user = new Model_User(['rfid'=>$rfid]);

        if(!$user->loaded()) {
            throw new HTTP_Exception_404;
        }

        if($user->office_id!=person::user()->office_id) {
            throw new HTTP_Exception_403;
        }

        echo $user->id;
        exit;
    }

    public function action_index(){


        if(person::$role=='cashier') {
            $view = new View('admin/dashboard/kassa');

            $users = orm::factory('user')
                    ->where('office_id', '=', person::user()->office_id)
                    ->limit(20)
                    ->order_by("id", "desc")->find_all();

            $view->users = $users;
            $view->terminals = [];
        }
        else {
            $view = new View('admin/dashboard/index');

            if(person::$role=='analitic') {
                $view = new View('admin/dashboard/analitic');
            }

            $currencies_orm = orm::factory('currency');
            if(PROJECT==1) {
                //$currencies_orm->where('code','=','EUR');
            }

            $currencies = $currencies_orm
                    ->find_all();

            $sql = <<<SQL
                SELECT o.id AS office_id, c.code
                FROM offices o
                JOIN currencies c ON o.currency_id=c.id
                WHERE o.id = :office_id
SQL;

            $person_offices = db::query(1, $sql)->param(':office_id', person::user()->office_id)->execute()->as_array('office_id');
            if(in_array(person::$role,['manager', 'rmanager','agent','gameman'])){
                $sql = <<<SQL
                    SELECT DISTINCT(po.office_id) AS office_id, c.code, o.amount,o.visible_name,o.encashment_time, o.zone_time, o.cashback
                    FROM person_offices po
                    JOIN offices o ON po.office_id=o.id
                    JOIN currencies c ON o.currency_id=c.id
                    WHERE person_id = :person_id ORDER BY 1 DESC
SQL;
                $person_offices = db::query(1, $sql)->param(':person_id', person::$user_id)->execute()->as_array('office_id');
            }

            if(person::$role=='analitic') {
                $sql = <<<SQL
                    SELECT DISTINCT(o.id) AS office_id, c.code, o.amount,o.visible_name,o.encashment_time, o.zone_time, o.cashback
                    FROM offices o
                    JOIN currencies c ON o.currency_id=c.id
                    WHERE o.id in :o_ids ORDER BY 1 DESC
SQL;
                $person_offices = db::query(1, $sql)->param(':o_ids', $this->offices())->execute()->as_array('office_id');
            }

            if(in_array(person::$role,['agent'])){

                $all_offices=$this->offices();

                $role_to_change = array_search($this->_roles[person::$role]-10,$this->_roles);

                $sql = <<<SQL
                    select p.id as person_id, po.office_id, p.visible_name from person_offices po
                    right join persons p on po.person_id=p.id
                    where p.role=:role_to_change
                    and p.parent_id = :parent_id
SQL;

                $res = db::query(1, $sql)->parameters([
                        ':parent_id'=>person::$user_id,
                        ':role_to_change'=>$role_to_change,
                        ])->execute()->as_array();

                $persons=[];

                foreach($res as $v){
                    $persons[$v['person_id']]['visible_name']=$v['visible_name'];
                    $persons[$v['person_id']]['offices'][]=$v['office_id'];
                }

                foreach($persons as $id => $person){

                    $persons[$id]['offices']=json_encode($person['offices']);
                }

                $view->all_offices = $all_offices;
                $view->persons = $persons;
                $view->role_to_change = person::rolelist($role_to_change);
            }

            $view->roles = $this->_can_roles;
            $view->currencies = $currencies;
            $view->person_offices = $person_offices;
        }

        $view->dir = $this->dir;
        $this->template->content = $view;
	}

    public function action_amountwithdraw(){

        if(person::$role!='cashier') {
            throw new HTTP_Exception_403;
        }

        $this->auto_render=false;

        $login = arr::get($_POST, 'login');
        $amount = arr::get($_POST, 'amount');

        $ans = ['error' => 0,'errors' => [], 'text' => ''];

        if(!$login) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите логин игрока');
            echo json_encode($ans);
            exit;
        }

        if((float) $amount <=0 && arr::get($_GET,'m')!='all') {
            $ans['error'] = 1;
            $ans['errors'][] = __('Некорректная сумма');
            echo json_encode($ans);
            exit;
        }

        $user = new Model_User($login);

        if(arr::get($_GET,'m')=='all') {
            $amount = $user->amount;
        }

        /*
         * реальные деньги
         */
        $currency_coeff = $user->office->currency_coeff??1;
        $money = $amount/$currency_coeff;

        if(!$user->loaded() OR person::user()->office_id != $user->office_id) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Игрок не найден');
        }

        if(!is_numeric($amount) OR $amount <= 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите сумму списания больше 0');
        }

        if($amount > $user->amount) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Недостаточно средств, максимальная сумма вывода') . " {$user->amount}";
        }

        if(!count($ans['errors'])) {
            $sql_upd_balance = <<<SQL
                update users set amount=amount-:amount,
                    sum_out=sum_out+:amount
                where id=:user_id
                RETURNING amount
SQL;
            $sql_new_operation = <<<SQL
                insert into operations(office_id, person_id, updated_id, type, amount, before, after, created,office_amount)
                values (:office_id, :person_id, :updated_id, :type, :amount, :before, :after, :created,:office_amount)
SQL;
            /*
             * получаем подготовленный запрос
             */
            $sql_upd_balance =db::query(3, $sql_upd_balance)->parameters([
                ':amount' => $amount,
                ':user_id' => $user->id
            ])->compile(database::instance());

            $sql_status="update status
					set value_numeric=value_numeric-:bank
					where id='bank' and type=:type";

            $office = new Model_Office(person::user()->office_id);
            $office->amount += $money;
            $office->bank -= $money;

            database::instance()->begin();
            try {
                /*
                 * Пополняем счет
                 */
                $res_amount = Database::instance()->direct_query($sql_upd_balance);


                $office->save();

                /*
                 * Пишем в операции
                 */
                db::query(2, $sql_new_operation)->parameters([
                    ':office_id' => person::user()->office_id,
                    ':person_id' => person::user()->id,
                    ':updated_id' => $user->id,
                    ':type' => 'user_withdraw',
                    ':amount' => -$amount,
                    ':before' => $user->amount,
                    ':office_amount' => $office->amount,
                    ':after' => $res_amount[0]['amount']??0,
                    ':created' => time(),
                ])->execute();

                /*
                 * обновляем банк
                 */
                db::query(1,$sql_status)->parameters([
                    ':bank' => $amount,
                    ':type' => $user->office_id,
                ])->execute();

                $ans['error'] = 0;
                $ans['newamount'] = $res_amount[0]['amount']??0;
                $ans['text'] = __('С аккаунта') . " $login " . __('успешно списано') . " $amount";

                $this->query_log_changes(
                        "amountwithdraw",
                        [
                            "amount"=>-$amount,
                            'updated_id' => $user->id,
                            'office_id' => person::user()->office_id,
                            'before' => $user->amount,
                            'after' => $res_amount[0]['amount']??0,
                            'office_amount' => $office->amount,
                            'created' => time(),
                        ]
                        );

                //todo подумать, нужно ли это в транзакции
                $balance = $user->amount - $amount;

                database::instance()->commit();
            } catch (Exception $ex) {
                $ans['error'] = 1;
                $ans['errors'][] = __('Ошибка при списании со счета');
                database::instance()->rollback();

                throw $ex;
            }
        }

        $this->response->body(json_encode($ans));
	}

    public function action_personpay() {

        if(person::$role!='analitic') {
            throw new HTTP_Exception_403;
        }

        $this->auto_render=false;

        $login = UTF8::trim(arr::get($_POST, 'login'));
        $money = UTF8::trim(arr::get($_POST, 'amount'));

        $ans = ['error' => 0,'errors' => [], 'text' => ''];

        if(!$login) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите логин персонала');
            echo json_encode($ans);
            exit;
        }

        if((float) $money <=0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Некорректная сумма');
            echo json_encode($ans);
            exit;
        }

        $user = new Model_Person(['name'=>$login]);

        /*
         * домножаем на коэф office
         */
        $currency_coeff = $user->office->currency_coeff??1;
        $amount = $money * $currency_coeff;

        if(!$user->loaded()) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Персонал не найден');
        }

        if(!is_numeric($money) OR $money <= 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите сумму пополнения больше 0');
        }

        if(!count($ans['errors'])) {
            $sql_upd_balance = <<<SQL
                update person_balances set amount=amount+:amount,
                    sum_in=sum_in+:amount
                where person_id=:user_id
                RETURNING amount
SQL;
            /*
             * получаем подготовленный запрос
             */
            $sql_upd_balance =db::query(3, $sql_upd_balance)->parameters([
                ':amount' => $amount,
                ':user_id' => $user->id
            ])->compile(database::instance());

            $sql_new_operation = <<<SQL
                insert into operations(person_id, updated_id, type, amount, before, after, created)
                values (:person_id, :updated_id, :type, :amount, :before, :after, :created)
SQL;

            $sql_status="update status
                            set value_numeric=value_numeric+:bank
                            where id='bank' and type=:type";


            database::instance()->begin();
            try {
                /*
                 * Пополняем счет
                 */
                $res_amount = Database::instance()->direct_query($sql_upd_balance);

                /*
                 * обновляем банк
                 */
                db::query(1,$sql_status)->parameters([
                    ':bank' => $amount,
                    ':type' => $user->office_id,
                ])->execute();


                /*
                 * Пишем в операции
                 */
                db::query(2, $sql_new_operation)->parameters([
                    ':person_id' => person::user()->id,
                    ':updated_id' => $user->id,
                    ':type' => 'person_payment',
                    ':amount' => $amount,
                    ':before' => $user->amount,
                    ':after' => $res_amount[0]['amount']??0,
                    ':created' => time(),
                ])->execute();


                $ans['error'] = 0;
                $ans['text'] = __('Аккаунт') . " $login " . __('успешно пополнен на') . " $amount";

                $this->query_log_changes(
                        "amountpay",
                        [
                            "amount"=>$amount,
                            'updated_id' => $user->id,
                            'before' => $user->amount,
                            'after' => $res_amount[0]['amount']??0,
                            'created' => time(),
                        ]
                        );

                $balance = $amount + $user->amount;
//                th::veksUpdate($user->id, null, $balance);

                database::instance()->commit();
            } catch (Exception $ex) {
                $ans['error'] = 1;
                $ans['errors'][] = __('Ошибка при пополнении счета');
                database::instance()->rollback();

                throw $ex;
            }
        }

        $this->response->body(json_encode($ans));
    }

    public function action_amountpay() {

        if(person::$role!='cashier' && person::$role!='analitic') {
            throw new HTTP_Exception_403;
        }

        $this->auto_render=false;

        $login = arr::get($_POST, 'login');
        $money = arr::get($_POST, 'amount');

        $ans = ['error' => 0,'errors' => [], 'text' => ''];

        if(!$login) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите логин игрока');
            echo json_encode($ans);
            exit;
        }

        if((float) $money <=0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Некорректная сумма');
            echo json_encode($ans);
            exit;
        }

        $user = new Model_User($login);
        $office = new Model_Office(person::user()->office_id);

        /*
         * домножаем на коэф office
         */
        $currency_coeff = $user->office->currency_coeff??1;
        $amount = $money * $currency_coeff;

        if(!$user->loaded() OR (person::$role=='cashier' && person::user()->office_id != $user->office_id)) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Игрок не найден');
        }

        if(!is_numeric($money) OR $money <= 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите сумму пополнения больше 0');
        }

        if($office->amount < $money) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Недостаточно средств на балансе ППС');
        }

        if(!count($ans['errors'])) {
            $sql_upd_balance = <<<SQL
                update users set amount=amount+:amount,
                    sum_in=sum_in+:amount
                where id=:user_id
                RETURNING amount
SQL;
            /*
             * получаем подготовленный запрос
             */
            $sql_upd_balance =db::query(3, $sql_upd_balance)->parameters([
                ':amount' => $amount,
                ':user_id' => $user->id
            ])->compile(database::instance());

            $sql_new_operation = <<<SQL
                insert into operations(office_id, person_id, updated_id, type, amount, before, after, created,office_amount)
                values (:office_id, :person_id, :updated_id, :type, :amount, :before, :after, :created,:office_amount)
SQL;

            $sql_status="update status
					set value_numeric=value_numeric+:bank
					where id='bank' and type=:type";

            $office->amount -= $money;
            $office->bank += $money;


            database::instance()->begin();
            try {
                /*
                 * Пополняем счет
                 */
                $res_amount = Database::instance()->direct_query($sql_upd_balance);

                /*
                 * обновляем банк
                 */
                db::query(1,$sql_status)->parameters([
                    ':bank' => $amount,
                    ':type' => $user->office_id,
                ])->execute();


                $office->save();

                /*
                 * Пишем в операции
                 */
                db::query(2, $sql_new_operation)->parameters([
                    ':office_id' => person::user()->office_id,
                    ':person_id' => person::user()->id,
                    ':updated_id' => $user->id,
                    ':type' => 'user_payment',
                    ':amount' => $amount,
                    ':before' => $user->amount,
                    ':office_amount' => $office->amount,
                    ':after' => $res_amount[0]['amount']??0,
                    ':created' => time(),
                ])->execute();


                $ans['error'] = 0;
                $ans['newamount'] = $res_amount[0]['amount']??0;
                $ans['text'] = __('Аккаунт') . " $login " . __('успешно пополнен на') . " $amount";

                $this->query_log_changes(
                        "amountpay",
                        [
                            "amount"=>$amount,
                            'updated_id' => $user->id,
                            'office_id' => person::user()->office_id,
                            'before' => $user->amount,
                            'after' => $res_amount[0]['amount']??0,
                            'office_amount' => $office->amount,
                            'created' => time(),
                        ]
                        );

                $balance = $amount + $user->amount;

                database::instance()->commit();
            } catch (Exception $ex) {
                $ans['error'] = 1;
                $ans['errors'][] = __('Ошибка при пополнении счета');
                database::instance()->rollback();

                throw $ex;
            }
        }

        $this->response->body(json_encode($ans));
    }

    public function action_createuser(){

        if(person::$role!='cashier') {
            throw new HTTP_Exception_403;
        }

        $this->auto_render=false;

        $errors = [];
        $login=null;
        $rfid = arr::get($_POST,'rfid','0');

        if($rfid!='-1') {
            if($rfid!='0' && $rfid!='') {
                $u = new Model_User(['rfid'=>$rfid]);
                if($u->loaded()) {
                    $errors[] = 'Player already exists';
                }
                else {
                    $login = th::sequence_next_value('users_id_seq');
                }
            }
            else {
                $errors[] = 'empty rfid';
            }
        }
        else {
            $rfid=null;
            $login = th::sequence_next_value('users_id_seq');
            $u = new Model_User(["name" => $login]);
        }

        $password = rand(1000,9999);


        if(empty($errors) && !$u->loaded()) {

            $u->id = $login;
            $u->name = $login;
            $u->rfid = $rfid;
            $u->salt=rand(1,10000000);
            $u->password=auth::pass($password,$u->salt);
            $u->office_id=person::user()->office_id;
            $u->comment=arr::get($_POST,'comment');

            $this->calc_changes($u,'createuser');
            $u->save()->reload();
            $this->log_changes($u->id);
        } else {
            $errors[] = __('Ошибка при создании пользователя. Повторите попытку.');
        }

        $view = new View('admin/user/create');


        if(!empty($errors)) {
            $errors=[$errors[0]];
        }

        $view->login = $login;
        $view->password = $password;
        $view->errors = $errors;
        $view->rfid = $rfid;
        $view->print = arr::get($_GET, 'print');
        $data = json_encode(['code'=>$view->render(), 'login'=>$login,'errors'=>$errors]);
        $this->response->body($data);
//        $this->template->content = $view;
	}

    public function action_createperson(){

        if(person::$role=='kassa') {
            throw new HTTP_Exception_403;
        }

        $this->auto_render = true;
        $errors = [];

        $this->template = new View('layout/empty');
        $view = new View('admin/user/create');

        $role = arr::get($_POST,'role',$_GET['role']??null);
        $comment = arr::get($_POST,'comment',$_GET['comment']??null);

        $offices = [];

        foreach (arr::get($_GET, 'office_id', []) as $o_id) {
            $offices[] = $o_id;
        }
        $office_id = $offices[0]??null;

        if(!$office_id && $role!='rmanager') {
            $errors[] = __('Ошибка при выборе OFFICE');
        }

        if(in_array(person::$role,['rmanager','manager'])){
                $office = new Model_Office($office_id);

                $person_office = new Model_Person_Office([
                    'person_id' => person::user()->id,
                    'office_id' => $office_id,
                ]);

                if(!$office->loaded() OR !$person_office->loaded()){
                    $errors[] = __('Ошибка при выборе OFFICE');
                }
        }else{
            if(!in_array($role, $this->_can_roles)) {
                $errors[] = __('Ошибка при выборе роли');
            }
        }


        $login = th::sequence_next_value('users_id_seq');
        $password = rand(100000,999999);

        if(!count($errors)) {
            $person = new Model_Person(["name" => $login]);

            if(!$person->loaded()) {
                $person->id = $login;
                $person->name = $login;

                if(!in_array($role,['manager','rmanager'])) {
                    $person->office_id = $office_id;
                }

                $person->role = $role;
                $person->comment = $comment;
                $person->salt=rand(1,10000000);
                $person->password=auth::pass($password,$person->salt);
                $person->enable_telegram = 0;
            }


            database::instance()->begin();
            try {
                $this->calc_changes($person,'createperson');
                $person->save()->reload();
                $this->log_changes($person->id);

                if(in_array($role,['manager'])) {
                    foreach ($offices as $o) {
                        $person_office = new Model_Person_Office();
                        $person_office->person_id = $person->id;
                        $person_office->office_id = $o;
                        $person_office->save();
                    }
                }


                database::instance()->commit();
            } catch (Exception $e) {
                database::instance()->rollback();
                $errors[] = __('Ошибка при создании кассира. Повторите попытку.');

                //выбрасываем выше
                throw $e;
            }
        }

        $view->login = $login;
        $view->password = $password;
        $view->errors = $errors;
        $view->print = arr::get($_GET, 'print');

        $this->template->content = $view;
	}

    public function action_create(){
        $this->auto_render = false;
        $ans = ['error' => 0,'errors' => [], 'text' => ''];
        $currency = arr::get($_POST, 'currency');
	$lang = arr::get($_POST, 'lang');
        $amount = arr::get($_POST, 'amount');
        $v_name = (string) arr::get($_POST, 'v_name');
        $encashment_time = (int) arr::get($_POST, 'encashment_time');
        $zone_time = (int) arr::get($_POST, 'zone_time');
        $cashback = (float) arr::get($_POST, 'cashback');
	$default_dentab = (float) arr::get($_POST, 'default_dentab');

        $currency_m = new Model_Currency($currency);

        if(!$currency_m->loaded()){
            $ans['error'] = 1;
            $ans['errors'][] = __('Выберите валюту из выпадающего списка');
        }

        if(person::$role=='client') {
            $amount = 0;
        }


        $diff_amount = person::user()->balance($currency) - $amount;

        if(person::$role=='client') {
            $diff_amount = 0;
        }

        if($diff_amount < 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Для создания ППС на Вашем счете не хватает') . ' ' . abs($diff_amount) . ' ' . $currency_m->code;
        }

	

        //добавить мин сумма 100000
        if(!count($ans['errors'])) {
            $imperium_hall = kohana::$config->load('api.imperium_currency.' . $currency_m->code . '.hall');
            $betgames = kohana::$config->load('api.betgames.web');

            $office = new Model_Office();
            $office->imperium_hall = $imperium_hall;

            $office->currency_id = $currency;
            $office->amount = $amount;
	    $office->lang = $lang;
            $office->visible_name = $v_name;
            $office->encashment_time = $encashment_time;
            $office->zone_time = $zone_time;
            $office->min_sum_pay = 0;
            $office->currency_coeff = 1;
            $office->created_time = time();
            $office->bg_request_url = $betgames['requestUrl'];
            $office->bg_widget_url = $betgames['widgetUrl'];
            $office->bg_partner_id = $betgames['partnerId'];
            $office->secretkey = arr::get($_POST, 'secretkey');
            $office->gameapiurl  = arr::get($_POST, 'gameapiurl');
            $office->apitype  = arr::get($_POST, 'apitype');
            $office->apienable  = arr::get($_POST, 'apienable');
		$office->owner  = person::user()->id;
		if((int) $office->apitype == 0) {
                    $office->seamlesstype = 1;
                }

	$office->default_dentab  = $default_dentab;

            $person_office = new Model_Person_Office();
            $person_office->person_id = person::user()->id;

//            $sql_new_operation = <<<SQL
//                insert into operations(office_id, person_id, type, updated_id)
//                values (:office_id, :person_id, :type, :updated_id)
//SQL;

            $sql_games = <<<SQL
                insert into office_games(office_id, game_id, enable)
                Select :office_id, g.id, 1
                From games g
                Where g.provider = 'our'
                    AND g.brand <> 'live'
SQL;

            $settings = [
                'social' => 0,
                'register' => 0,
                'kassa' => 0,
                'change_currency' => 0,
                'tournament' => 0,
                'share' => 0,
                'referal' => 0,
                'compoint' => 0,
                'reg_fs' => 0,
                'mobileplay' => 0,
                'partnerlink' => 0,
                'demoplay' => 0,
                'live' => 0,
                'phone' => 0,
                'faq' => 0,
                'bonus' => 0,
                'live' => 0,
                'rules' => 0,
                'topwin' => 0,
            ];

            database::instance()->begin();
            try {
                $this->calc_changes($office,'create');
                $office->save()->reload();
                $this->log_changes($office->id);
                $person_office->office_id = $office->id;
                $person_office->save();

                person::user()->reduce_balance($amount, $currency);

                foreach ($settings as $k => $v) {
                    $o_set = new Model_Office_Setting();
                    $o_set->office_id = $office->id;
                    $o_set->param=$k;
                    $o_set->enabled=$v;
                    $o_set->save();
                }

                //db::query(2, $sql_games)->param(':office_id', $office->id)->execute();
                //сейчас включаются все доступные игры


//                db::query(2, $sql_new_operation)->parameters([
//                    ':office_id' => $office->id,
//                    ':person_id' => person::user()->id,
//                    ':updated_id' => $office->id,
//                    ':type' => 'create_office',
//                ])->execute();

                /*
                 * создаем bank и users в таблице status
                 */
                status::instance($office->id);

                /*create jackpot*/
                $jpconfig = Kohana::$config->load('jackpot');
                foreach ($jpconfig as $jpcfg_type => $jpcfg)
                {
                    $j = new Model_Jackpot();
                    $j->office_id = $office->id;
                    $j->type = $jpcfg_type;
                    $j->active = (int) (person::$role!='gameman');
                    foreach ($jpcfg as $f => $v)
                    {
                        $j->$f = $v;
                    }

                    $j->value = $j->rand_value();

                    $j->save();
                    $j->clear();
                }

                $ans['text'] = __('Новый ППС № ').$office->id.__(' был успешно создан');

                database::instance()->commit();

                th::default_office_games($office->id);
            } catch (Exception $e) {
                database::instance()->rollback();

                $ans['error'] = 1;
                $ans['errors'][] = __('Ошибка при создании ППС').'<br>'.$e->getMessage();
            }
        }

        $this->response->body(json_encode($ans));
	}

    public function action_addmoney(){
        $this->auto_render = false;
        $ans = ['error' => 0,'errors' => [], 'text' => ''];
        $office_id = arr::get($_POST, 'office_id');
        $amount = arr::get($_POST, 'amount');

        if(!$office_id) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Выберите ППС');
            echo json_encode($ans);
            exit;
        }

        if((float) $amount <=0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Некорректная сумма');
            echo json_encode($ans);
            exit;
        }

        $office = new Model_Office($office_id);

        $person_office = new Model_Person_Office([
            'person_id' => person::user()->id,
            'office_id' => $office_id,
        ]);

        if(!$office->loaded() OR (!$person_office->loaded() AND person::$role!='analitic')) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Ошибка при выборе ППС');
        }

        if(!is_numeric($amount) OR $amount <= 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Укажите сумму больше 0');
        }

        $diff_amount = person::user()->balance($office->currency_id) - $amount;

        if($diff_amount < 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Недостаточно средств на счету');
        }

        if(!count($ans['errors'])) {
            $amount_before = $office->amount;
            $office->amount += $amount;

            $sql_new_operation = <<<SQL
                insert into operations(office_id, person_id, updated_id, type, amount, before, after, created,office_amount)
                values (:office_id, :person_id, :updated_id, :type, :amount, :before, :after, :created,:office_amount)
SQL;

            database::instance()->begin();
            try {
                $office->save()->reload();
                person::user()->reduce_balance($amount, $office->currency_id);
                /*
                 * Пишем в операции
                 */

                db::query(2, $sql_new_operation)->parameters([
                    ':office_id' => $office_id,
                    ':person_id' => person::user()->id,
                    ':updated_id' => $office_id,
                    ':type' => 'payment_office',
                    ':amount' => $amount,
                    ':created' => time(),
                    ':office_amount' => (int)$office->amount,//добавил (int) иначе pg_query ругалось на пополнение
                    ':before' => $amount_before,
                    ':after' => $office->amount,
                ])->execute();


                $this->query_log_changes(
                        "addmoney",
                        [
                                'office_id' => $office_id,
                                'amount' => $amount,
                                'office_amount' => (int)$office->amount
                        ],
                        "operation");

                database::instance()->commit();
                $ans['text'] = __('ППС') . " $office_id " . __('успешно пополнен на') . " $amount {$office->currency->code}";
            } catch (Exception $e) {
                database::instance()->rollback();

                $ans['error'] = 1;
                $ans['errors'][] = __('Ошибка при пополнении счета');

                //выбрасываем выше
                throw $e;
            }
        }

        $this->response->body(json_encode($ans));
	}

    public function action_removemoney(){
        $this->auto_render = false;
        $ans = ['error' => 0,'errors' => [], 'text' => ''];
        $office_id = arr::get($_POST, 'office_id');
        $amount = arr::get($_POST, 'amount');

        if(!$office_id) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Выберите ППС');
            echo json_encode($ans);
            exit;
        }

        if((float) $amount <=0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Некорректная сумма');
            echo json_encode($ans);
            exit;
        }

        $office = new Model_Office($office_id);

        $person_office = new Model_Person_Office([
            'person_id' => person::user()->id,
            'office_id' => $office_id,
        ]);

        if(!$office->loaded() OR (!$person_office->loaded() AND person::$role!='analitic')) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Ошибка при выборе ППС');
        }

        if(!is_numeric($amount) OR $amount <= 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Укажите сумму больше 0');
        }

        $diff_amount = $office->amount - $amount;

        if($diff_amount < 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Недостаточно средств на счету');
        }

        if(!count($ans['errors'])) {
            $amount_before = $office->amount;
            $office->amount -= $amount;

            $sql_new_operation = <<<SQL
                insert into operations(office_id, person_id, updated_id, type, amount, before, after, created,office_amount)
                values (:office_id, :person_id, :updated_id, :type, :amount, :before, :after, :created,:office_amount)
SQL;

            database::instance()->begin();
            try {
                $office->save()->reload();
                person::user()->increase_balance($amount, $office->currency_id);
                /*
                 * Пишем в операции
                 */

                db::query(2, $sql_new_operation)->parameters([
                    ':office_id' => $office_id,
                    ':person_id' => person::user()->id,
                    ':updated_id' => $office_id,
                    ':type' => 'payment_office',
                    ':amount' => -$amount,
                    ':created' => time(),
                    ':office_amount' => (int)$office->amount,//добавил (int) иначе pg_query ругалось на пополнение
                    ':before' => $amount_before,
                    ':after' => $office->amount,
                ])->execute();


                $this->query_log_changes(
                        "removemoney",
                        [
                                'office_id' => $office_id,
                                'amount' => -$amount,
                                'office_amount' => (int)$office->amount
                        ],
                        "operation");

                database::instance()->commit();
                $ans['text'] = __('ППС') . " $office_id " . __('успешно списан на') . " $amount {$office->currency->code}";
            } catch (Exception $e) {
                database::instance()->rollback();

                $ans['error'] = 1;
                $ans['errors'][] = __('Ошибка при пополнении счета');

                //выбрасываем выше
                throw $e;
            }
        }

        $this->response->body(json_encode($ans));
	}

    public function action_createkassa(){
        $this->auto_render = false;
		$ans = ['error' => 0,'errors' => [], 'text' => __('Касса успешно создана')];

        $office_id = arr::get($_POST, 'office_id');

        $person_office = new Model_Person_Office([
            'office_id' => $office_id,
            'person_id' => person::user()->id,
        ]);

        if($person_office->loaded()) {
            $count_kassa = orm::factory('kassa')->where('office_id', '=', $office_id)->count_all();
            $count_kassa += 1;

            $kassa = new Model_Kassa();
            $kassa->name = '№ ' . $count_kassa;
            $kassa->office_id = $office_id;
            //текущий кассир использующий кассу
            $kassa->person_id = null;
            $kassa->save();
        } else {
            $ans['error'] = 1;
            $ans['errors'][] = __('Ошибка при создании кассы');
        }

        $this->response->body(json_encode($ans));
	}

    public function action_settings(){

        throw new HTTP_Exception_403;

        $this->auto_render = false;
        $ans = ['error' => 0,'errors' => [], 'text' => ''];
        $office_id = arr::get($_POST, 'office_id');
        $v_name = (string) arr::get($_POST, 'v_name');
        $encashment_time = (float) arr::get($_POST, 'encashment_time');
        $zone_time = (float) arr::get($_POST, 'zone_time');
        $cashback = (float) arr::get($_POST, 'cashback');

        $office = new Model_Office($office_id);

        $person_office = new Model_Person_Office([
            'person_id' => person::user()->id,
            'office_id' => $office_id,
        ]);

        if(!$office->loaded() OR !$person_office->loaded()){
            $ans['error'] = 1;
            $ans['errors'][] = __('Ошибка при выборе OFFICE');
        }

        $sql_settings = <<<SQL
                UPDATE offices
                SET
                    visible_name=:v_name,
                    encashment_time=:encashment_time,
                    zone_time=:zone_time,
                    cashback=:cashback
                WHERE id=:office_id
SQL;

        try {
            $office->save()->reload();
            db::query(Database::UPDATE, $sql_settings)->parameters([
                    ':office_id' => $office_id,
                    ':v_name' => $v_name,
                    ':encashment_time' => $encashment_time,
                    ':zone_time' => $zone_time,
                    ':cashback' => $cashback,
                ])->execute();

            $this->query_log_changes(
                    "update",
                    [
                            'v_name' => $v_name,
                            'encashment_time' => $encashment_time,
                            'zone_time' => $zone_time,
                            'cashback' => $cashback
                    ],
                    "office",
                    $office_id);

            $ans['text'] = __('OFFICE') . " $office_id " . __('успешно изменен');
        } catch (Exception $e) {

            $ans['error'] = 1;
            $ans['errors'][] = __('Ошибка при изменении настроек');

            throw $e;
        }


        $this->response->body(json_encode($ans));
    }

    public function action_changeoffice(){

        if(in_array(person::$role,['manager','administrator', 'kassa'])) {
            throw new HTTP_Exception_403;
        }

        $this->auto_render = false;
        $ans = ['error' => 0,'errors' => [], 'text' => ''];

        $changed_id = arr::get($_POST,'changed_id',null);

        $offices = [];
        foreach (arr::get($_POST, 'new_offices', []) as $new_oid) {
            $offices[] = $new_oid;
        }

        $person = new Model_Person(['id' => $changed_id]);

        if($person->loaded()){
            foreach($offices as $office){

                $person_office = new Model_Person_Office([
                    'person_id' => $changed_id,
                    'office_id' => $office,
                ]);

                if(!$person_office->loaded()){
                    $person_office->person_id = $changed_id;
                    $person_office->office_id = $office;
                    $person_office->save();
                }

            }
        }else{
            $ans['error'] = 1;
            $ans['errors'][] = __('Ошибка при выборе ID');
        }

        $sql_delete_offices = <<<SQL
                DELETE FROM person_offices
                WHERE person_id=:person_id
                AND office_id not in :offices
                RETURNING id
SQL;

        if(!$ans['error']) {
            try {
                $deleted = db::query(Database::DELETE, $sql_delete_offices)->parameters([
                        ':offices' => $offices,
                        ':person_id' => $changed_id,
                    ])->execute();

                $this->query_log_changes(
                        "changeoffice",
                        [
                                'person_id' => $changed_id,
                                'offices' => $offices,
                        ],
                        "person_office") ;

                $ans['text'] = __('Список ППС успешно изменен');

            } catch (Exception $e) {

                $ans['error'] = 1;
                $ans['errors'][] = __('Ошибка при изменении настроек');

                throw $e;
            }
        }


        $this->response->body(json_encode($ans));
    }

    public function action_blockoffice() {
        if(!in_array(person::$role,['kassa','administrator'])) {
            throw new HTTP_Exception_403;
        }

        $db = database::instance();
        $db->begin();

        $office_id = person::user()->office_id;

        try {

            $action = new Model_Action;

            $action->type = 'block_office';
            $action->person_id = person::$user_id;
            $action->model_name = 'office';
            $action->model_id = $office_id;
            $action->save();
            office::block($office_id);

            $db->commit();
            $this->request->redirect($this->dir);
        } catch (Exception $ex) {
            $db->rollback();
            throw $ex;
        }
    }
}

