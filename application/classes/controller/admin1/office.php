<?php

class Controller_Admin1_Office extends Super
{

    public $mark       = 'Offices'; //имя
    public $model_name = 'office'; //имя модели
    public $order_by = ['id', 'asc'];
    public $sh='admin1/super';
    public $max_pages=100;


    public $canCreate=false;


    //list of persons for creating offices manually
    //nux, advaark
    protected function isOwnerWithOurAPI() {
        return Person::$role=='client' && gameapi::isOurAPI(Person::$user_id);
    }

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
            //'amount',
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
				'visible_name',
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

        if(Person::$role=='sa' || $this->isOwnerWithOurAPI()) {
            $this->canCreate=true;
        }

        if(Person::$role=='sa') {
            $this->list[]='rtp';
            $this->list[]='games_rtp';
        }

        $this->show=[
            'id',
            'visible_name',
//            'amount',
            'zone_time',
            'currency_id',
            'lang',
            'apienable',
            'blocked',
        ];

        $this->show[]='enable_moon_dispatch';
	$this->show[]='showfakeversion';

        if(Person::$role=='sa') {
            $this->show[]='rtp';
            $this->show[]='games_rtp';
            $this->show[]='strict_double';
			$this->show[]='max_win_eur';
			$this->show[]='check_new_ls';
			$this->show[]='ls_first_wager';
        }

        $sql='select id, code,source, default_k_max_lvl
                from currencies
                order by code';


        if($this->isOwnerWithOurAPI()) {
            $sql='select id, code,source, default_k_max_lvl
                from currencies
                where source=\'agt\'
                order by code';
        }

        $cur=[];
        $jpK=[];
        foreach( db::query(1,$sql)->execute()->as_array() as $row){
            $cur[$row['id']]=$row['code'];
            $jpK[$row['id']]=$row['default_k_max_lvl'];
            if($row['source']!='agt') {
                $cur[$row['id']].=' ('.$row['source'][0].')';
            }
        }


        $this->vidgets['id'] = new Vidget_Echo('id',$this->model);
        if(person::$role!='sa' && !$this->isOwnerWithOurAPI()) {
            $this->vidgets['visible_name'] = new Vidget_Echo('visible_name',$this->model);
        }
        $this->vidgets['encashment_time'] = new Vidget_Echo('encashment_time',$this->model);
        $this->vidgets['blocked'] = new Vidget_CheckBox('blocked',$this->model);
        if(!$this->isOwnerWithOurAPI()) {
            $this->vidgets['apienable'] = new Vidget_CheckBox('apienable',$this->model);
            $this->vidgets['apienable']->param('can_edit',person::$role=='sa');
        }
        else {
            unset($this->show[array_search('apienable',$this->show)]);
        }

        $this->vidgets['agtenable'] = new Vidget_CheckBox('agtenable',$this->model); //for share agt games
        if(empty($this->request->param('id')) && (person::$role=='sa' || $this->isOwnerWithOurAPI())){
            $this->vidgets['currency_id'] = new Vidget_Currency('currency_id',$this->model);
            $this->vidgets['currency_id']->param('jpk',$jpK);
            $this->vidgets['zone_time'] = new Vidget_List('zone_time',$this->model);
        }
        else {
            $this->vidgets['currency_id'] = new Vidget_Echo_List('currency_id',$this->model);
            $this->vidgets['zone_time'] = new Vidget_Echo_List('zone_time',$this->model);
        }
        if(person::$role=='sa') {
            $this->vidgets['strict_double'] = new Vidget_CheckBox('strict_double',$this->model);
        }
	$this->vidgets['showfakeversion'] = new Vidget_CheckBox('showfakeversion',$this->model);
        $this->vidgets['currency_id']->param('list',$cur);
        $this->vidgets['zone_time']->param('list',tz::lst());

        if(!$this->isOwnerWithOurAPI()) {
            $this->show[]='workmode';
            $this->show[]='default_dentab';
            $this->show[]='gameui';

            $dentabs = Kohana::$config->load('agt.k_list');

            $this->vidgets['workmode'] = new Vidget_Select('workmode',$this->model);
            $this->vidgets['workmode']->param('fields',[
                    0=>'Default',
                    1=>'Terminal',
    //                    2=>'Users',
    //                    3=>'Terminal+Users',
            ]);
            $this->vidgets['workmode']->param('can_edit',person::$role=='sa');

            $this->vidgets['gameui'] = new Vidget_Select('gameui',$this->model);
            $this->vidgets['gameui']->param('fields',[
                    0=>'Classic',
                    1=>'Modern',
                    2=>'Classic(Switch to modern)',
                    3=>'Modern(Switch to classic)',
            ]);
            $this->vidgets['gameui']->param('can_edit',person::$role=='sa');

            $this->vidgets['default_dentab'] = new Vidget_SelectDenTab('default_dentab',$this->model);
            $this->vidgets['default_dentab']->param('list',$dentabs);
        }

        $langs = [null=>'Auto'];
        $langs = array_merge($langs,(array) Kohana::$config->load('languages.lang'));

        if(person::$role=='sa' || $this->isOwnerWithOurAPI()){
            $this->vidgets['lang'] = new Vidget_List('lang',$this->model);
        }
        else {
            $this->vidgets['lang'] = new Vidget_Echo_List('lang',$this->model);
        }
        $this->vidgets['lang']->param('list',$langs);

        $this->vidgets['amount'] = new Vidget_Amount_Office('amount',$this->model);

        if (Person::$role=='sa'){
            $this->vidgets['owner']=new Vidget_Ownerslist('id',$this->model);
			$this->vidgets['check_new_ls'] = new Vidget_CheckBox('check_new_ls',$this->model);
        }

        $this->vidgets['rtp'] = new Vidget_Rtp('rtp', $this->model);
        $this->vidgets['rtp']->param('list',[ 92=>92, 94=>94, 96=>96 ]);

        $this->vidgets['games_rtp'] = new Vidget_Rtp('games_rtp', $this->model);
        $this->vidgets['games_rtp']->param('list',[
                '96.00'=>96,
                '96.50'=>96.5,
                '97.00'=>97,
                '97.50'=>97.5,
                '98.00'=>98,
        ]);


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
			
			$this->show[]='promopanel';
            $this->vidgets['promopanel'] = new Vidget_CheckBox('promopanel',$this->model);

			$this->vidgets['max_win_eur'] = new vidget_number('max_win_eur',$this->model);
            $this->vidgets['max_win_eur']->param('min',0);
			$this->vidgets['max_win_eur']->param('max',1500000);
            $this->vidgets['max_win_eur']->param('default',0);
			
			$this->vidgets['ls_first_wager'] = new vidget_number('ls_first_wager',$this->model);
            $this->vidgets['ls_first_wager']->param('min',0);
            $this->vidgets['ls_first_wager']->param('default',0);

            $this->show[]='comment';


        }

        $this->show[]='enable_bia';
        if(person::$role=='sa') {
            $this->show[]='bonus_diff_last_bet';
        }

//      $this->show[]='enable_jp';
        $this->show[]='k_to_jp';
        $this->show[]='k_max_lvl';

        $this->vidgets['enable_jp']=new Vidget_CheckBox('enable_jp',$this->model);
        $this->vidgets['enable_jp']->param('can_edit',person::$role=='sa' || $this->isOwnerWithOurAPI());

        $jpPercent=[

            '0.001'=>'0.1%',
            '0.002'=>'0.2%',
            '0.003'=>'0.3%',
            '0.004'=>'0.4%',
            '0.005'=>'0.5%',

            '0.006'=>'0.6%',
            '0.007'=>'0.7%',
            '0.008'=>'0.8%',
            '0.009'=>'0.9%',
            '0.01'=>'1 %',
        ];

        $this->vidgets['k_to_jp']=new Vidget_Listfloat('k_to_jp',$this->model);
        $this->vidgets['k_to_jp']->param('list',$jpPercent);
        $this->vidgets['k_to_jp']->param('can_edit',person::$role=='sa' || $this->isOwnerWithOurAPI());
        $this->vidgets['k_to_jp']->param('text','site-domain recommends to set this value to 0.5%');
        $this->vidgets['k_max_lvl']=new Vidget_Jpset('k_max_lvl',$this->model);
        $this->vidgets['k_max_lvl']->param('can_edit',person::$role=='sa' || $this->isOwnerWithOurAPI());

        $this->vidgets['enable_moon_dispatch'] = new Vidget_CheckBox('enable_moon_dispatch',$this->model);

        $this->show[]='enable_jp';

        /*if((int) $this->request->param('id')>0) {
            $this->show[]='selectgames';
        }


        $this->vidgets['selectgames'] = new Vidget_Selectgames('games',$this->model);
        $this->vidgets['selectgames']->param('can_edit',person::$role=='sa' || $this->isOwnerWithOurAPI());*/

        $this->vidgets['enable_bia'] = new Vidget_CheckBox('enable_bia',$this->model);
        $this->vidgets['enable_bia']->param('use_time',true);
        $this->vidgets['enable_bia']->param('can_edit',person::$role=='sa' || $this->isOwnerWithOurAPI());

        $bonus_cnf=kohana::$config->load('bonus');

        $this->vidgets['bonus_diff_last_bet'] = new vidget_number('bonus_diff_last_bet',$this->model);
        $this->vidgets['bonus_diff_last_bet']->param('onlyshow',person::$role!='sa');
        $this->vidgets['bonus_diff_last_bet']->param('min',0);
        $this->vidgets['bonus_diff_last_bet']->param('max',24);
        $this->vidgets['bonus_diff_last_bet']->param('default',$bonus_cnf['diff_last_bet']/60/60);
        $this->vidgets['bonus_diff_last_bet']->param('text','<br>Hours from last bet to calc FSback');
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
        if(Person::$role=='sa') {
            return $model;
        }
        return $model->where('id','in', Person::user()->offices());
    }

    public function handler_save($data)
    {

		//MBT B2B
        if (th::isB2B(Person::$user_id) && $data['currency_id']==313){
            throw new Exception('currency not allowed');
        }

        $save = parent::handler_save($data);
        $this->model->games_rtp=97;
        //ADV RTP
        if (Person::$user_id==1023){
            $this->model->games_rtp=96;
        }

        $this->model->save();

        //only if office is esists
        if($this->model->loaded() && in_array('enable_jp',$this->show) && $this->vidgets['enable_jp']->getparam('can_edit')===true) {
            $enable_jp=(int) (isset($data['enable_jp']));

            $redis = dbredis::instance();
            $redis->select(1);

            $redis->set('jpa-'.$this->request->param('id'),$enable_jp);

            $r=db::query(database::UPDATE,'update jackpots set active=:active where office_id=:o_id')
                ->param(':o_id',$this->model->id)
                ->param(':active',$enable_jp)
                ->execute();
        }
        return $save;
    }

    public function action_item(){

        if(person::$role=='sa') {
            return parent::action_item();
        }

        if (!empty($this->request->param('id')) && !in_array($this->request->param('id'),Person::user()->offices())){
            throw new HTTP_Exception_403();
        }

        if(empty($this->request->param('id')) && !$this->canCreate) {
            throw new HTTP_Exception_403();
        }

        if($this->isOwnerWithOurAPI()) {
            return $this->item_action_for_our_api();
        }
        return parent::action_item();

    }

    public function item_action_for_our_api() {
        if(!person::user()->can_edit($this->model)) {
            $this->request->redirect($this->dir.'/'.$this->controller);
        }

        $this->model=ORM::factory($this->model_name,$this->request->param('id'));

        if(!$this->canItem()){
            throw new HTTP_Exception_404();
        }

        $sh=$this->request->is_initial() ? '/item' : '/item_related';
        $view=new View($this->sh.$sh);
        $new=!$this->model->loaded();//новая запись

        if($new) {
            unset($this->show[array_search('enable_bia',$this->show)]);
            unset($this->show[array_search('enable_moon_dispatch',$this->show)]);
            unset($this->show[array_search('blocked',$this->show)]);
        }

        if ($this->request->method() == 'POST'){

            $this->model->need_create_default_games = false;

            $this->handler_save(Request::current()->post());
            $errors = array();
            $v=$this->model->validation();
            $v->check();
            $errors=$v->errors($this->model_name);

            if (count($errors)==0){

                $type = $new?'insert':'update';
                $this->calc_changes($this->model,$type);
                if(!$new) {
                    $this->model->save();
                }
                else {
                    try {
                        //создаем игры здесь

                        $currency=new Model_Currency($this->model->currency_id);

                        $this->model->enable_jp=(int) !$this->model->enable_jp;

                        $this->model->enable_bia = time();
                        $this->model->enable_moon_dispatch = 1;

                        $this->model->is_test = 0;
                        $this->model->gameui = 1;
                        $this->model->bonus_diff_last_bet = 8;
                        $this->model->apienable = 1;
                        $this->model->apitype = 0;
                        $this->model->seamlesstype = 1;

                        $this->model->bank = $currency->default_bank;
                        $this->model->use_bank = 1;
                        $this->model->bet_min=$currency->min_bet;
                        $this->model->bet_max = $currency->max_bet;
                        $this->model->dentabs = $currency->default_den;
                        $this->model->default_dentab = $currency->default_dentab;

                        $this->model->owner=Person::$user_id;

                        if(in_array(Person::$user_id,[1089,1100])) {
                            $this->model->games_rtp=97;
                        }

						if (Person::$user_id==1023){
                            $this->model->enable_bia=0;
                        }
						
                        $this->model->need_create_default_games = false;
                        $this->model->save()->reload();


                        $sql_games = <<<SQL
                            insert into office_games(office_id, game_id, enable)
                            Select :office_id, g.id, 1
                            From games g
                            Where g.provider = 'our' and brand ='agt' and show=1 and g.category!='coming' and g.branded=0
SQL;

                        db::query(Database::INSERT, $sql_games)
                            ->param(':office_id', $this->model->id)
                            ->execute();

                        //добавляем везде не зависимо от выбранного

                        if(in_array('enable_jp',$this->show)) {
                            $redis = dbredis::instance();
                            $redis->select(1);
                            $redis->set('jpa-' . $this->model->id, $this->model->enable_jp);

                            for ($i = 1; $i <= 4; $i++) {

                                $redis->set('jpHotPercent-' . $this->model->id . '-' . ($i), 0.02);

                                $j = new Model_Jackpot();
                                $j->office_id = $this->model->id;
                                $j->type = $i;
                                $j->active = $this->model->enable_jp;

                                $j->save();
                            }
                        }

                        if (Person::$user_id!=1023) {
                            $this->model->createProgressiveEventForOffice();
                        }

                    } catch (Exception $ex) {
                        database::instance()->rollback();
                        throw $ex;
                    }
                    database::instance()->commit();
                }

                $this->log_changes($this->model->id);

                if ($new) {
                    if ($this->request->initial()){
                        $this->request->redirect($this->dir.'/'.$this->model_name.'/item/'.$this->model->id.'?s=1');
                    }
                    else {
                        return null;
                    }
                }
                $view->suc=1;
            }
            else{
                $view->error=$errors;
            }
        }

        $view->item=$this->model;
        $view->label=$this->_labels($new);
        $view->show=$this->show;
        $view->model=$this->model_name;
        $view->model=$this->controller;
        $view->mark=$this->mark;
        $view->dir=$this->dir;
        $view->vidgets=$this->vidgets;
        $view->actions=$this->actions;

        if ($this->request->is_initial()){
            $this->template->content=$view->render();
        }
        else{
            $this->response->body($view->render());
        }
    }

    protected function _labels($is_new=false) {
        $labels=$this->model->labels();

        if($is_new) {
            $labels['enable_jp']=__('Disable JP');
        }

        return $labels;
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
