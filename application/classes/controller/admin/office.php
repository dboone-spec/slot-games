<?php

class Controller_Admin_Office extends Super
{

    public $mark       = 'Offices'; //имя
    public $model_name = 'office'; //имя модели
    public $order_by = ['id', 'asc'];
    public $sh='super/normal';


    public $canCreate=false;


    public function before()
    {
        parent::before();

        if(!in_array(person::$role,['gameman','sa','client'])) {
            throw new HTTP_Exception_403;
        }
    }

    public function configure()
    {
        $this->search = [
            'id',
            'apienable',
            'blocked',
        ];

        $this->list = [
            'id',
            'visible_name',
            'amount',
            'currency_id',
            'lang',
            'blocked',
            'apienable',
        ];

        if (Person::$role=='sa'){
            $this->search = [
                'id',
                'owner',
                'apienable',
                'blocked',
            ];
            $this->list = [
                'id',
                'owner',
                'visible_name',
                'amount',
                'currency_id',
                'lang',
                'blocked',
                'apienable',
            ];
        }
	else {
            $this->vidgets['visible_name'] = new Vidget_Echo('visible_name',$this->model);
        }

        if(PROJECT==2) {
            $this->list[]='rtp';
            $this->list[]='agtenable';
        }

        $this->show=[
            'id',
            'visible_name',
            'amount',
            'currency_id',
            'lang',
            'apienable',
            'blocked',
            'encashment_time',
        ];

        if(PROJECT==2) {
            $this->show[]='rtp';
            $this->show[]='agtenable';
            if(Person::$role!='sa') {
                $o_ids = Person::user()->officesName(null,true);
                foreach([1112,1115,1118] as $o) {
                    if(isset($o_ids[$o])) {
                        unset($this->show[array_search('blocked',$this->show)]);
                    }
                }
            }
        }


        $sql='select id, code
                from currencies
                order by code';

        $cur=[];
        foreach( db::query(1,$sql)->execute()->as_array() as $row){
            $cur[$row['id']]=$row['code'];
        }


        $this->vidgets['id'] = new Vidget_Echo('id',$this->model);
        $this->vidgets['encashment_time'] = new Vidget_Echo('encashment_time',$this->model);
        $this->vidgets['blocked'] = new Vidget_CheckBox('blocked',$this->model);
        $this->vidgets['apienable'] = new Vidget_CheckBox('apienable',$this->model);
        $this->vidgets['agtenable'] = new Vidget_CheckBox('agtenable',$this->model);
        $this->vidgets['currency_id'] = new Vidget_Echo_List('currency_id',$this->model);
        $this->vidgets['currency_id']->param('list',$cur);

        if(PROJECT==1) {

            $this->show[]='default_dentab';

            $dentabs = Kohana::$config->load('agt.k_list');

            $this->vidgets['default_dentab'] = new Vidget_SelectDenTab('default_dentab',$this->model);
            $this->vidgets['default_dentab']->param('list',$dentabs);
        }

        $langs = [null=>'Auto'];
        $langs = array_merge($langs,(array) Kohana::$config->load('languages.lang'));

        $this->vidgets['lang'] = new Vidget_List('lang',$this->model);
        $this->vidgets['lang']->param('list',$langs);

        $this->vidgets['amount'] = new Vidget_Amount_Office('amount',$this->model);

        if (Person::$role=='sa'){

            $this->vidgets['owner']=new Vidget_Related('owner',$this->model);
            $this->vidgets['owner']->param('related','person');
            $this->vidgets['owner']->param('name','comment');
//            $this->show[]='owner';

        }
        else{
            $this->vidgets['rtp'] = new Vidget_Echo('rtp', $this->model);
        }

        $this->vidgets['rtp'] = new Vidget_Rtp('rtp', $this->model);
        $this->vidgets['rtp']->param('list',[ 92=>92, 94=>94, 96=>96 ]);


        if (person::$role=='client') {
            $this->list[]='secretkey';
            $this->list[]='gameapiurl';

            $this->show[]='secretkey';
            $this->show[]='gameapiurl';

        }

        if(person::$role=='sa'){
            $this->list[]='is_test';
            $this->list[]='comment';

            $this->show[]='bank';
            $this->show[]='users';
            $this->show[]='use_bank';

            $this->vidgets['bank'] = new Vidget_Inputenable('bank',$this->model);
            $this->vidgets['users'] = new Vidget_Echo('users',$this->model);
            $this->vidgets['use_bank'] = new Vidget_CheckBox('use_bank',$this->model);

            $this->show[]='is_test';
            $this->vidgets['is_test'] = new Vidget_CheckBox('is_test',$this->model);

            $this->show[]='comment';

            $this->show[]='selectgames';
            $this->vidgets['selectgames'] = new Vidget_Selectgames('games',$this->model);

            $this->show[]='enable_bia';
            $this->show[]='bonus_diff_last_bet';
            if(PROJECT==2) {
                $this->show[]='bonus_pay_period';
                $this->show[]='bonus_coeff';
            }

        }

        $this->vidgets['enable_bia'] = new Vidget_CheckBox('enable_bia',$this->model);
        $this->vidgets['enable_bia']->param('use_time',true);

        $bonus_cnf=kohana::$config->load('bonus');

        $this->vidgets['bonus_diff_last_bet'] = new vidget_number('bonus_diff_last_bet',$this->model);
        $this->vidgets['bonus_diff_last_bet']->param('min',0);
        $this->vidgets['bonus_diff_last_bet']->param('max',24);
        $this->vidgets['bonus_diff_last_bet']->param('default',$bonus_cnf['diff_last_bet']/60/60);
        $this->vidgets['bonus_pay_period'] = new vidget_number('bonus_pay_period',$this->model);
        $this->vidgets['bonus_pay_period']->param('max',60);
        $this->vidgets['bonus_pay_period']->param('default',$bonus_cnf['pay_period']/24/60/60);
        $this->vidgets['bonus_coeff'] = new vidget_number('bonus_coeff',$this->model);
        $this->vidgets['bonus_coeff']->param('min',1);
        $this->vidgets['bonus_coeff']->param('max',10);
        $this->vidgets['bonus_coeff']->param('c',0.01);
        $this->vidgets['bonus_coeff']->param('default',$bonus_cnf['coeffs']['z1']);

    }

    public function handler_search($vars){
        $model = parent::handler_search($vars);
        return $model->where('id','in', Person::user()->offices());
    }


    public function action_item(){

        if (!in_array($this->request->param('id'),Person::user()->offices())){
            throw new HTTP_Exception_403();
        }
        return parent::action_item();

    }




    public function action_balance(){


        $view=new View('admin/office/replenish');

        $error=null;

        $id=$this->request->param('id');
        $amount=arr::get($_GET,'amount',0);
        $mode=arr::get($_GET,'mode');
        $view->id=$id;


        if (Person::$role!='sa' and !in_array($id,Person::user()->offices())){
            throw new HTTP_Exception_403();
        }
        if (!in_array($mode,['replenish','takeoff'])){
            throw new HTTP_Exception_403();
        }
        if (!is_numeric($amount) or $amount<=0 ){
            $view->error='Wrong amount value '. HTML::chars($amount);
            $this->template->content=$view;
            return null;
        }

        $m=1;
        if($mode=='takeoff'){
            $m=-1;
        }

        if($mode=='takeoff' and !in_array(Person::$role,['sa','client'])){
            throw new HTTP_Exception_403();
            $this->template->content=$view;
            return null;
        }

        if(Person::user()->amount<$amount and $mode=='replenish'){
            $view->error='Insufficient person balance, maximum amount value is '.Person::user()->amount;
            $this->template->content=$view;
            return null;
        }

        $office=new Model_Office($id);

        if($office->amount<$amount and $mode=='takeoff'){
            $view->error='Insufficient office balance, maximum amount value is '.$office->amount;
            $this->template->content=$view;
            return null;
        }



        database::instance()->begin();

        $sql='update persons set amount=amount+:amount where id=:pid';
        db::query(Database::UPDATE, $sql)->param(':amount',-1*$amount*$m)
                                         ->param(':pid',Person::$user_id)
                                         ->execute();


        $sql='update offices set amount=amount+:amount where id=:id';
        db::query(Database::UPDATE, $sql)->param(':amount',$amount*$m)
                                        ->param(':id',$id)
                                        ->execute();


        $o=new Model_Office_Amount();
        $o->amount=$amount*$m;
        $o->office_id=$id;
        $o->person_id= Person::$user_id;
        $o->save();


        database::instance()->commit();



        if(!empty($error)){
            $view->error=$error;
            $this->template->content=$view;
            return null;
        }

        $this->request->redirect('/enter/office/item/'.$id);


    }


}
