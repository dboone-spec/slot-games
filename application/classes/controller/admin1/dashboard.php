<?php

class Controller_Admin1_Dashboard extends Controller_Admin1_Base{

    public $mark       = 'Управление'; //имя
    public $model_name = 'empty'; //имя модели
    public $sh         = 'admin1/dashboard'; //шаблон
    public $controller = 'dashboard';

    protected function _check_limits($amount,$type) {
        $o=person::user()->my_office;

        $error=[];

        if(!empty($o->min_deposit) && $type=='deposit' && bccomp($o->min_deposit,$amount)>0) {
            $error[]='Minimum deposit is '.$o->min_deposit.' '.$o->currency->code;
        }

        if(!empty($o->max_deposit) && $type=='deposit' && bccomp($amount,$o->max_deposit)>0) {
            $error[]='Maximum deposit is '.$o->max_deposit.' '.$o->currency->code;
        }

        if(!empty($o->min_withdraw) && $type=='withdraw' && bccomp($o->min_withdraw,$amount)>0) {
            $error[]='Minimum withdrawal is '.$o->min_withdraw.' '.$o->currency->code;
        }

        if(!empty($o->max_withdraw) && $type=='withdraw' && bccomp($amount,$o->max_withdraw)>0) {
            $error[]='Maximum withdrawal is '.$o->max_withdraw.' '.$o->currency->code;
        }

        return $error;
    }

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

    public function action_officebalance(){
        if(person::$role!='cashier') {
            throw new HTTP_Exception_403;
        }

        echo person::user()->my_office->amount;

        exit;
    }

    public function action_index(){

        if(person::$role=='cashier') {
            $view = new View('admin1/terminals/index');

            $terminals = orm::factory('terminal')
                    ->where('office_id', '=', person::user()->office_id)
                    ->where('blocked', '=', 0)
                    ->limit(20)
                    ->order_by("id", "desc")->find_all();

            $view->terminals = $terminals;
        }
        else {
            $view = new View('admin1/dashboard/index');

            if(person::$role=='analitic') {
                $view = new View('admin1/dashboard/analitic');
            }

            $currencies_orm = orm::factory('currency');
            if(PROJECT==1) {
                $currencies_orm->where('code','=','EUR');
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
        $password = arr::get($_POST, 'password');
        $code = (string) arr::get($_POST, 'code');

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

/*
        //не штрих код
        if (empty($code) || empty($user->barcode) ){

            if(auth::pass($password,$user->salt)!=$user->password ) {

                $ans['error'] = 1;
                $ans['errors'][] = 'Incorrect password';
                $this->response->body(json_encode($ans));

            }
        }
        else{  //штрихх код
            if($code!=$user->barcode){
                $ans['error'] = 1;
                $ans['errors'][] = 'Incorrect barcode';
                $this->response->body(json_encode($ans));
            }
        }
*/
        //источник снятия кштрих код или пароль
        $src= $code==$user->barcode  ? 'code' : 'pass';

        $limit_errors=$this->_check_limits($amount,'withdraw');

        if(!empty($limit_errors)){
            $ans['error'] = 1;
            $ans['errors']=array_merge($ans['errors'],$limit_errors);
        }

        if(!count($ans['errors'])) {
            $sql_upd_balance = <<<SQL
                update users set amount=amount-:amount,
                    sum_out=sum_out+:amount
                where id=:user_id
                RETURNING amount
SQL;
            $sql_new_operation = <<<SQL
                insert into operations(office_id, person_id, updated_id, type, amount, before, after, created,office_amount,src)
                values (:office_id, :person_id, :updated_id, :type, :amount, :before, :after, :created,:office_amount,:src)
SQL;


        $office = new Model_Office(person::user()->office_id);


            database::instance()->begin();
            try {
                $res_amount =db::query(1, $sql_upd_balance)->parameters([
                                                                    ':amount' => $amount,
                                                                    ':user_id' => $user->id
                                                                ])->execute();

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
                    ':src'=>$src,
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

    public function action_amountpay() {

        if(person::$role!='cashier' && person::$role!='analitic') {
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

        if((float) $amount <=0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Некорректная сумма');
            echo json_encode($ans);
            exit;
        }

        $user = new Model_User($login);
        $office = new Model_Office(person::user()->office_id);



        if(!$user->loaded() OR (person::$role=='cashier' && person::user()->office_id != $user->office_id)) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Игрок не найден');
        }

        if(!is_numeric($amount) OR $amount <= 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите сумму пополнения больше 0');
        }

        if($office->amount < $amount && false) { 
            $ans['error'] = 1;
            $ans['errors'][] = __('Недостаточно средств на балансе ППС');
        }

        $limit_errors=$this->_check_limits($amount,'deposit');

        if(!empty($limit_errors)){
            $ans['error'] = 1;
            $ans['errors']=array_merge($ans['errors'],$limit_errors);
        }

        if(!count($ans['errors'])) {
            $sql_upd_balance = <<<SQL
                update users set amount=amount+:amount,
                    sum_in=sum_in+:amount
                where id=:user_id
                RETURNING amount
SQL;


            $sql_new_operation = <<<SQL
                insert into operations(office_id, person_id, updated_id, type, amount, before, after, created,office_amount)
                values (:office_id, :person_id, :updated_id, :type, :amount, :before, :after, :created,:office_amount)
SQL;




            database::instance()->begin();
            try {
                /*
                 * Пополняем счет
                 */
                $res_amount = db::query(3, $sql_upd_balance)->parameters([
                                ':amount' => $amount,
                                ':user_id' => $user->id
                            ])->execute();



               db::query(1,'update offices set amount=amount-:money')->param(':money',$amount)->execute();

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
}

