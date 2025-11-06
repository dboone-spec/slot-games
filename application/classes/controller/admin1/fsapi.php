<?php

class Controller_Admin1_Fsapi extends Controller_Admin1_Base
{

    public function action_item()
    {

        $is_new = empty($this->request->param('id'));

        $officesList = Person::user()->officesName(null,true);

        $setsModel = new Model_Fssets($this->request->param('id'));
        $builder   = new paramsbuilder();
        $builder->add('user','last_bet_time','rangetime');
        $builder->add('user','last_bet_time','selecttimediff');

        $mFreespinsstack = new Model_freespinsstack();

        $autocorrect = [];

        /* gamelist */
        $sql = 'select g.name, g.visible_name,g.id
                from games g
                join office_games og on g.id=og.game_id
                where og.office_id in :oid
                and g.provider = \'our\'
                and g.category!=\'coming\'
                and g.show = 1
                and g.brand=\'agt\'
                order by g.name';

        if(Person::$role == 'sa')
        {
            $sql = 'select g.name, g.visible_name,g.id
                    from games g
                    where brand=\'agt\'
                    and g.provider = \'our\'
                    and g.category!=\'coming\'
                    and g.show = 1
                    order by g.name';
        }
        else {
            $mFreespinsstack->where('office_id','in',Person::user()->offices());
        }


        $games = db::query(1,$sql)->param(':oid',Person::user()->offices())->execute()->as_array('name');

        $gamelist    = [];
        $gamelist_id = [];

        foreach($games as $k => $g)
        {
            $gamelist[$k]    = $games[$k]['visible_name'];
            $gamelist_id[$k] = $games[$k]['id'];
        }

        asort($gamelist);
        /* gamelist */

        $errors = [];

        if($this->request->method() == 'POST')
        {



            $u            = new Model_User();
            $u->office_id = $this->request->post('office_id');
            $game         = new Model_Game(['name' => $this->request->post('game')]);
            $count = (int) $this->request->post('fs_count');

            if($count>30) {
                $errors[] = 'Max spins is 30';
            }

            if(!in_array($game->type,['slot','shuffle'])) {
                $errors[]='game must be slot type';
            }

            if($is_new && (new Model_Fssets(['name'=>$this->request->post('name')]))->loaded()){
                $errors[] = 'Name already in use';
            }

            $setsModel->name     = $this->request->post('name');
            $setsModel->visible_name     = $this->request->post('visible_name');
            $setsModel->game     = $this->request->post('game');
            $setsModel->game_id  = $gamelist_id[$this->request->post('game')];
            $setsModel->mass     = (int) $this->request->post('mass');
            $setsModel->amount   = $this->request->post('amount');
            $setsModel->fs_count = $this->request->post('fs_count');
            $setsModel->params   = $builder->save($this->request->post());
            $setsModel->active   = (int) $this->request->post('active');


            if(!empty($errors))
            {
                $setsModel->active = 0;
            }
            else {

                $lines=kohana::$config->load('agt/'.$game->name)['lines_choose'][0];
                $dentab_index = 0;

                $setsModel->dentab_index = $dentab_index;
                $setsModel->lines        = $lines;
            }

            $postLogin = $this->request->post('login');
            $expirtime = (int) $this->request->post('expirtime');

            $expirtime+=time();

            if($setsModel->mass)
            {
                $postLogin = null;
            }

            $user_id=null;


            if(!empty($postLogin)) {
                $u = new Model_User(['external_id'=>$postLogin]);

                if(!$u->loaded()) {
                    $pas = mt_rand(10000000, 999999999);

                    $u->name = $postLogin.'-'.$this->request->post('office_id');
                    $u->office_id = $this->request->post('office_id');
                    $u->salt = rand(1, 10000000);
                    $u->password = auth::pass($pas, $u->salt);
                    $u->api = 1;
                    $u->amount = 0;
                    $u->api_key = guid::create();
                    $u->external_id = $postLogin;
                    $u->visible_name = $postLogin;
                    $u->api_key_time = time();
                    $u->save()->reload();
                }

                $user_id = $u->id;

            }


            if(empty($errors))
            {

                database::instance()->begin();

                try
                {

                    $this->calc_changes($setsModel,$is_new ? 'insert' : 'update');
                    $setsModel->save()->reload();
                    $this->log_changes($setsModel->id);

                    if($this->request->post('process_btn'))
                    {

                        if($expirtime<=time()){
                            $errors[]='wrong expiration time';
                        }

                        if(!isset($officesList[$u->office_id])) {
                            $errors[] = 'Office not found';
                        }

                        if (!$setsModel->active) {
                            $errors[] = 'Set is not active';
                        }

                        if(empty($postLogin)) {
                            $errors[] = 'Mass FS disabled';
                        }

                        /*if(Office::instance($this->request->post('office_id'))->office()->currency->code=='ZAR' &&
                            $setsModel->amount>15000) {
                            $errors[] = 'Max amount is 15000';
                        }*/

                        $office=Office::instance($this->request->post('office_id'))->office();
                        $val=$office->currency->val;

                        if(empty($val) || $val<0.0001) {
                            $errors[] = 'Incorrect currency. Please, contact AGT support';
                        }

                        $limit = 1000; //EUR

                        if(($setsModel->amount*$val)>$limit) {
                            $errors[] = 'Max amount is '.(ceil($limit/$val));
                        }

                        if(!$office->checkFSApiLimit($setsModel->amount)) {
                            $errors[]='FS out of limit';
                        }

                        if(empty($errors)) {
                            $setsModel->to_process_stack($this->request->post('office_id'), $user_id, $expirtime);
                        }
                    }

                    database::instance()->commit();
                }
                catch(Database_Exception $e)
                {
                    database::instance()->rollback();
                    throw $e;
                }

                if($is_new)
                {
                    $this->request->redirect('/enter/fsapi/item/' . $setsModel->id.'?s=1');
                }
            }
        }

        $last10FSstack=$mFreespinsstack->order_by('created','desc')->limit(10)->find_all();

        $view                    = new View('admin1/fsapi/item');
        $view->dir               = $this->dir;
        $view->set               = $setsModel;
        $view->form              = $builder->render($setsModel->params);
        $view->gamelist          = $gamelist;
        $view->autocorrect       = $autocorrect;
        $view->errors            = $errors;
        $view->post              = $this->request->post();
        $view->officesList       = $officesList;
        $view->last10FSstack     = $last10FSstack;
        $view->is_new            = !$setsModel->loaded();
        $this->template->content = $view;
    }

    public function action_index()
    {

        /* CREATE TABLE "public"."freespins_sets" (
          "id" serial4 NOT NULL,
          "name" varchar(30),
          "office_id" int4,
          "mass" int2 DEFAULT 0,
          "params" text,
          "active" int2 DEFAULT 0,
          "game" varchar(20),
          PRIMARY KEY ("id"),
          UNIQUE ("name")
          )
          WITH (OIDS=FALSE)
          ;
         */

        $sql = 'select g.name, g.visible_name
                from games g
                join office_games og on g.id=og.game_id
                where og.office_id in :oid
                order by g.name';

        if(Person::$role == 'sa')
        {
            $sql = 'select g.name, g.visible_name
                    from games g
                    order by g.name';
        }

        $games = db::query(1,$sql)->param(':oid',Person::user()->offices())->execute()->as_array('name');

        $gamelist = [];

        foreach($games as $k => $g)
        {
            $gamelist[$k] = $games[$k]['visible_name'];
        }

        asort($gamelist);


        $setsModel   = new Model_Fssets();
        $sets        = $setsModel->find_all();
        $officesList = [-1 => 'All'] + Person::user()->officesName(null,true);

        $view                    = new View('admin1/fsapi/index');
        $view->dir               = $this->dir;
        $view->sets              = $sets;
        $view->gamelist          = $gamelist;
        $view->officesList       = $officesList;
        $this->template->content = $view;
    }


    public function action_cancel()
    {
        $stack_id = (int) $this->request->param('id');
        if($stack_id<=0) {
            $this->request->redirect($this->request->referrer());
        }
        $stackRow=new Model_freespinsstack($stack_id);
        if(!$stackRow->loaded()) {
            $this->request->redirect($this->request->referrer());
        }
        if($stackRow->status!=0) {
            $this->request->redirect($this->request->referrer());
        }
        $offices=Person::user()->offices();
        if(!in_array($stackRow->office_id,$offices)) {
            $this->request->redirect($this->request->referrer());
        }

        $stackRow->status=3;
        $stackRow->updated=time();
        $stackRow->save();

        $this->request->redirect($this->request->referrer());
    }

    public function action_give()
    {

        $user_id = (int) $this->request->query('user_id');


        $errors=[];

        $gamelist    = [];
        $gamelist_id = [];

        $message = '';

        if(empty($user_id)) {
            $message = 'Enter USER ID';
        }
        else {
            $officesList = [-1 => 'All'] + Person::user()->officesName(null,true);
            $u = new Model_User($user_id);

            if(!$u->loaded() || !isset($officesList[$u->office_id])) {
                $errors[]='User not found';
                $user_id=0;
            }
            elseif($u->blocked) {
                $errors[]='User is blocked';
                $user_id=0;
            }
            elseif($u->office->blocked) {
                $errors[]='Office is blocked';
                $user_id=0;
            }


            $sql = 'select g.name, g.visible_name,g.id
                from games g
                join office_games og on g.id=og.game_id
                where og.office_id in :oid
                and g.provider = \'our\'
                and g.category!=\'coming\'
                and g.show = 1
                and g.brand=\'agt\'
                order by g.name';

            if(Person::$role == 'sa')
            {
                $sql = 'select g.name, g.visible_name,g.id
                        from games g
                        where brand=\'agt\'
                        and g.provider = \'our\'
                        and g.category!=\'coming\'
                        and g.show = 1
                        order by g.name';
            }

            $games = db::query(1,$sql)->param(':oid',Person::user()->offices())->execute()->as_array('name');


            foreach($games as $k => $g)
            {
                $gamelist[$k]    = $games[$k]['visible_name'];
                $gamelist_id[$k] = $games[$k]['id'];
            }

            asort($gamelist);
        }

        if($this->request->method()=='POST' && empty($errors)) {
            $amount = $this->request->post('amount');

            if(empty($amount)) {
                $errors[]='empty amount';
            }

            $count = $this->request->post('count');

            if(empty($count)) {
                $errors[]='empty count';
            }


            $game = $this->request->post('game');

            $g = new Model_Game(['name'=>$game]);

            if(!$g->loaded()) {
                $errors[]='game not found';
            }

            if(!in_array($g->type,['slot','shuffle'])) {
                $errors[]='game must be slot type';
            }

            $og = new Model_Office_Game(['office_id'=>$u->office_id,'game_id'=>$g->id,'enable'=>'1']);

            if(!$og->loaded()) {
                $errors[]='game not found';
            }

            $val=$u->office->currency->val;

            if(empty($val) || $val<0.0001) {
                $errors[] = 'Incorrect currency. Please, contact AGT support';
            }

            $limit = 1000; //EUR

            if(($amount*$val)>$limit) {
                $errors[] = 'Max amount is '.(ceil($limit/$val));
            }

            if(!$u->office->checkFSApiLimit($amount)) {
                $errors[]='FS out of limit';
            }

            if(empty($errors)) {


                if($count>30) {
                    $errors[]='Max spins is 30';
                }
                else {
                    $lines=kohana::$config->load('agt/'.$game)['lines_choose'][0];
                    $dentab_index = 0;

                    $expire=time()+30*24*60*60;

                    $f = new Model_Freespin();

                    $one_bet_amount=$amount/$count;

                    if($f->giveFreespins($u->id,$u->office_id,$g->id,$count,$one_bet_amount,$lines,$dentab_index,'api',true,(array) $this->request->post(),false,null,$expire)) {

                        $u->office->updateFSamount($amount);

                        $message = $count.' FS was gived to '.$u->id.
                                ' for total sum: '.(th::float_format($one_bet_amount*$count,$u->office->currency->mult)).' ('.th::float_format($one_bet_amount,$u->office->currency->mult).' per spin) at game '.$g->visible_name.
                                ' [lines: '.$lines.']';

                    }
                    else {
                        $errors[]='can not pay freespins';
                    }
                }
            }
        }

        $view                    = new View('admin1/fsapi/give');
        $view->dir               = $this->dir;
        $view->errors            = $errors;
        $view->user_id           = $user_id;
        $view->gamelist          = $gamelist;
        $view->is_post           = ($this->request->method() == 'POST');
        $view->message           = $message;
        $this->template->content = $view;
    }
}
