<?php

class Controller_Admin1_Cashusers extends Controller_Admin1_Dashboard {


    public function before() {

        parent::before();

        if(person::$role!='cashier') {
            throw  new HTTP_Exception_404;
        }


    }



    public function action_index(){

        $o=new Model_Office(Person::user()->office_id);

        $terminals = orm::factory('user')
                ->where('office_id', '=', person::user()->office_id)
                ->where('blocked', '=', 0)
                ->limit(15)
                ->order_by("id", "desc")->find_all();


        $view = new View('admin1/cashusers/index');

        $view->terminals = $terminals;
        $view->dir = $this->dir;
        $this->template->content=$view;

    }


     public function action_createuser(){

        if(person::$role!='cashier') {
            throw new HTTP_Exception_403;
        }


        $this->auto_render=false;

        $errors = [];
        $login=null;
        $rfid = arr::get($_POST,'rfid','0');

        $login = th::sequence_next_value('users_id_seq');
        $u=new Model_User();

        $password = rand(1000,9999);


        if(empty($errors) && !$u->loaded()) {

            $u->id = $login;
            $u->name = $login;
            $u->salt=rand(1,10000000);
            $u->password=auth::pass($password,$u->salt);
            $u->office_id=person::user()->office_id;

            $u->barcode=$this->barCode($login);

            $this->calc_changes($u,'createuser');
            $u->save()->reload();
            $this->log_changes($u->id);
        } else {
            $errors[] = __('Ошибка при создании пользователя. Повторите попытку.');
        }



        if(!empty($errors)) {
            $errors=[$errors[0]];
        }

        //use "createrotated" for ticket with rotated barcode
        $view = new View('admin1/user/createrotated');
        $o=new Model_Office(Person::user()->office_id);

        $view->login = $login;
        $view->fio = person::user()->fio;
        $view->password = $password;
        $view->user=$u;
        $view->errors = $errors;
        $view->rfid = $rfid;
        $view->print = true;

        $data=['code'=>$view->render(), 'login'=>$login, 'errors'=>$errors,];

        $data = json_encode($data);
        $this->response->body($data);

	}

	public function barCode($barCode){

        $barCode=(string) $barCode ;
        $barCode=ltrim($barCode,'0');
        $count=7-strlen($barCode);
        for ($i=1;$i<=$count;$i++){
            $barCode='0'.$barCode;
        }

        return '00'.$barCode.$this->crc($barCode);

    }

	public function crc($code):string{

        $code=(string) $code;
        $multipliers=[
                    0=>[3,5,7],
                    1=>[1,5,17],
                    2=>[11,17,5],
                    3=>[13,5,11],
                    4=>[3,1,17],
                    5=>[23,17,13],
                    6=>[23,5,7],
                    7=>[13,5,1],
                    8=>[23,13,7],
                    9=>[3,23,7],
            ];
        $multiplierIndex=$code[strlen($code)-1];
        $sum=0;
        $j=0;
        for ($i=0;$i<strlen($code);$i++){
            $sum+=$code[$i]*$multipliers[$multiplierIndex][$j];
            $j++;
            $j=$j>2 ? 1 : $j;
        }
        if ($sum>1000){
            return (string) $this->crc($sum);
        }
        if ($sum<10){
            return '00'.$sum;
        }
        if ($sum<100){
            return '0'.$sum;
        }


        return (string) $sum;

    }




        //activate
    public function action_sendcode(){

        $this->auto_render=false;

        $id=arr::get($_POST,'userid');
        $login=arr::get($_POST,'login');
        $login=trim($login);
        $login=trim($login,'@');

        $u=new Model_User($id);

        $a=['error'=>1,'text'=>'User not found'];

        if (!$u->loaded()){
            $this->response->body(json_encode($a));
            return null;
        }

        if ($u->office_id!= Person::user()->office_id){
            $this->response->body(json_encode($a));
            return null;
        }


        if ($u->tg_id>0){
            $a=['error'=>1,'text'=>"$login is already activate."];
            $this->response->body(json_encode($a));
            return null;
        }

        $u2=new Model_User(['tg_name'=>$login,'office_id'=>person::user()->office_id]);

        if ($u2->loaded() && $u2->id!=$u->id){
            $a=['error'=>1,'text'=>"Telegram login '$login' is already in use, choose another one or restore access "];
            $this->response->body(json_encode($a));
            return null;
        }


        $u->tg_name=$login;
        $u->save();
        $this->update();


        $u->reload();

        if (!($u->tg_id>0)){
            $a=['error'=>1,'text'=>"Can't send message to $login. Check Telegram Login or send any message to bot and try again."];
            $this->response->body(json_encode($a));
            return null;
        }

        $password = rand(1000,9999);
        $u->salt=rand(1,10000000);
        $u->password=auth::pass($password,$u->salt);
        $u->save();
        $mess="Login: {$u->name}\r\nPassword: {$password}";
        $this->send($u->tg_id,$mess);

        $a=['error'=>0,'text'=>"Enter the code."];
        $this->response->body(json_encode($a));

    }



        //activate
    public function action_sendinfo(){

        $this->auto_render=false;

        $id=arr::get($_POST,'userid');

        $u=new Model_User($id);

        $a=['error'=>1,'text'=>'User not found'];

        if (!$u->loaded()){
            $this->response->body(json_encode($a));
            return null;
        }

        if ($u->office_id!= Person::user()->office_id){
            $this->response->body(json_encode($a));
            return null;
        }


        if (!($u->tg_id>0)){
            $a=['error'=>1,'text'=>"User is not activated."];
            $this->response->body(json_encode($a));
            return null;
        }



        $u->reload();

        if (!($u->tg_id>0)){
            $a=['error'=>1,'text'=>"Can't send message to $login. Check Telegram Login or send any message to bot and try again."];
            $this->response->body(json_encode($a));
            return null;
        }

        $password = rand(1000,9999);
        $u->salt=rand(1,10000000);
        $u->password=auth::pass($password,$u->salt);
        $u->save();
        $mess="Login: {$u->name}\r\nPassword: {$password}";
        $this->send($u->tg_id,$mess);

        $a=['error'=>0,'text'=>"Data was sent successfully."];
        $this->response->body(json_encode($a));

    }


    public function action_userbalance(){
        $this->auto_render=false;

        $user_id = (int) arr::get($_GET,'user_id');

        $user = new Model_User($user_id);

        if($user->office_id!=person::user()->office_id) {
            throw new HTTP_Exception_403;
        }

        $a=['amount'=>$user->amount,
            'tg_name'=>$user->tg_name,
            'active'=> ($user->tg_id>0)
                ];

       $this->response->body(json_encode($a));

    }


    public function action_amountwithdraw(){


        $this->auto_render=false;

        if (person::user()->my_office->tg_cashusers==0){
            return parent::action_amountwithdraw();
        }

        $login = arr::get($_POST, 'login');
        $amount = arr::get($_POST, 'amount');
        $password = arr::get($_POST, 'password');

        $user = new Model_User($login);
        if(arr::get($_GET,'m')=='all') {
            $amount = $user->amount;
        }

        $ans = ['error' => 0,'errors' => [], 'text' => ''];

        if(!$user->loaded() || $user->office_id!=person::user()->office_id) {
            $ans['error'] = 1;
            $ans['errors'][] = __('User not found');
            echo json_encode($ans);
            exit;
        }

        if((float) $amount <=0 && arr::get($_GET,'m')!='all') {
            $ans['error'] = 1;
            $ans['errors'][] = __('Некорректная сумма');
            echo json_encode($ans);
            exit;
        }

        if(!is_numeric($amount) OR $amount <= 0) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Введите сумму списания больше 0');
            $this->response->body(json_encode($ans));
            return null;
        }


        if($amount > $user->amount) {
            $ans['error'] = 1;
            $ans['errors'][] = __('Недостаточно средств, максимальная сумма вывода') . " {$user->amount}";
            $this->response->body(json_encode($ans));
            return null;
        }

        $limit_errors=$this->_check_limits($amount,'withdraw');

        if(!empty($limit_errors)){
            $ans['error'] = 1;
            $ans['errors']=array_merge($ans['errors'],$limit_errors);
        }

        $ans['needcode']=true;
        $user->wcode=rand(1000,9999);
        $user->wamount=$amount;
        $user->save();
        $this->send($user->tg_id,"Provide the withdrawal code to the operator:\r\n Amount: {$user->wamount}\r\n Code: {$user->wcode}");

        $ans['text']='Enter the code.';
        $this->response->body(json_encode($ans));


    }



    public function action_withdrawcode(){

        $this->auto_render=false;

        $code= arr::get($_POST, 'wcode');
        $login = arr::get($_POST, 'login');

        $user = new Model_User($login);


        if ($user->wcode>0 && $user->wcode==$code){



            $_POST['amount']=$user->wamount;
            $user->wcode=0;
            $user->wamount=0;
            $user->save();
            return parent::action_amountwithdraw();
        }

        $ans = ['error' => 1,'errors' => ['Wrong code'], 'text' => ''];
        $this->response->body(json_encode($ans));
    }



    public function send($chatid,$message){

        $token='2136283114:AAFMCKqpgoie8-t3FkqE8ZoCGj7OXi51iWg';
        $token='5780743937:AAF_cX8WjYG6b4UYV1ASqz66kSZQFZ5bqxE';


        $message= urlencode($message);


        $url="https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatid}&text={$message}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $result= json_decode($result);

        if(!isset($result->ok)){
            return false;
        }

        if ($result->ok===true){
            return true;
        }

        return false;

    }




      public function update(){

        $token='2136283114:AAFMCKqpgoie8-t3FkqE8ZoCGj7OXi51iWg';
        $token='5780743937:AAF_cX8WjYG6b4UYV1ASqz66kSZQFZ5bqxE';

        $offset= Status::instance()->tgOffset;

        $url="https://api.telegram.org/bot{$token}/getUpdates?offset={$offset}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $result= json_decode($result);


        if(!isset($result->result)){
            return null;
        }


        if (count($result->result)==0){
            return false;
        }

        $offset=end($result->result);
        $offset=$offset->update_id;
        Status::instance()->tgOffset=$offset;

        if ($result->ok!==true){
            return false;
        }

        $users=[];

        foreach($result->result as $r){
            $users[$r->message->from->username]=$r->message->from->id;
        }

        foreach ($users as $name=>$id){

            $sql='update users
                    set tg_id=:id
                    where tg_name=:name';



            db::query(Database::UPDATE,$sql)->param(':id',$id)
                                            ->param(':name',$name)
                                            ->execute();
        }

    }



    public function action_barcode(){

        $this->auto_render=false;

        require_once(APPPATH.'vendor/barcode/BCGFontFile.php');
        require_once(APPPATH.'vendor/barcode/BCGColor.php');
        require_once(APPPATH.'vendor/barcode/BCGDrawing.php');

// Including the barcode technology
        require_once(APPPATH.'vendor/barcode/BCGcode39.barcode.php');

// Loading Font
        $font = new BCGFontFile(APPPATH.'vendor/barcode/font/Arial.ttf', 18);

// The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $drawException = null;
        try {
            $code = new BCGcode39();
            $code->drawtext=false;
            $code->setScale(1); // Resolution
            $code->setThickness(30); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont($font); // Font (or 0)
            $code->parse($this->request->param('id')); // Text
        } catch(Exception $exception) {
            $drawException = $exception;
        }

        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $drawing = new BCGDrawing('', $color_white);
        if($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            if(arr::get($_GET,'rotated')) {
                $drawing->setRotationAngle(90);
            }
            $drawing->draw();
        }

// Header that says it is an image (remove it if you save the barcode to a file)
        $this->response->headers('Content-Type','image/png');

// Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

    }


}