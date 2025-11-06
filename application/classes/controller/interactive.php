<?php

class Controller_Interactive extends Controller_Base{



    public $template='layout/interactive2';
    public $active=null;


    public function before() {



	if(isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $country=strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
            if(in_array($country,['ru','tr','in'])) {

                echo '<h1 style="display: flex;justify-content: center;text-shadow: 1px 0 1px #fff,0 1px 1px #fff,-1px 0 1px #fff,0 -1px 1px #fff;">Sorry, no access for your country</h1>';
                exit;
            }
        }

        if (PROJECT!=1){
            throw new HTTP_Exception_404();
        }
        return parent::before();
    }

    public function after(){


        if (empty($this->active)){
            $this->active=$this->request->action();
        }

        $this->template->active=$this->active;
        parent::after();
    }



    public  function action_index(){

		if(INFINSOC_DOMAIN) {
            exit;
        }

		if(SBC_DOMAIN && empty(auth::$user_id)) {
            $this->request->redirect('/register');
        }


        $this->active='index';

        $static = kohana::$config->load('static');
        $sort=office::instance()->office()->sort();
        $games=office::instance()->office()->sorted_games;

        $view=new View('site/interactive1/index');
        $view->games=$games;
        $view->news = (new Model_News())->where('created','<', time())->order_by('created','desc')->limit(3)->find_all();
        $this->template->content=$view;


    }


    public  function action_all(){


        $this->active='index';
        /*
        $vg = new vipgameapi();
        $games = $vg->gamelist();
        */
        $static = kohana::$config->load('static');
         $sql="select g.name, g.visible_name, '{$static['static_domain']}'||g.image as image, g.id as game_id , category, demo, brand
                from games g
                join office_games og on og.game_id = g.id
                where og.office_id = :o_id
                    and og.enable = 1
                    and g.show=1
                    and g.brand!=:br
                    order by g.sort";

         $games=db::query(1, $sql)
                 ->param(':br','agt')
                 ->param(':o_id',777)
                               ->execute()
                               ->as_array();

        $view=new View('site/interactive/all');
        $view->games=$games;
        $this->template->content=$view;


    }



    public  function action_contacts(){


        $view=new View('site/interactive/contacts');
        $this->template->content=$view;

    }

    public  function action_demo(){

        $static = kohana::$config->load('static');
        $sql="select g.name, g.visible_name, '{$static['static_domain']}'||g.image as image, g.id as game_id , category, demo
                from games g
                join office_games og on og.game_id = g.id
                where og.office_id = :o_id
                    and og.enable = 1
                    and g.show=1
                    and brand='agt'
                    order by g.sort";

        $games=db::query(1, $sql)->param(':o_id',777)
                               ->execute()
                               ->as_array();

        $view=new View('site/interactive/demo');
        $view->games=$games;
        $this->template->content=$view;


    }



    public function action_news(){
        $this->active='news';

        $view=new View('site/interactive1/news');

        $all_news = db::query(1,'select count(id) from news where published=1')->execute()->as_array()[0]['count'];

        $per_page=20;

        $page_data   = array
                (
                'total_items'    => $all_news,
                'items_per_page' => $per_page,
                'current_page'   => array
                        (
                        'source' => 'route',
                        'key'    => 'id'
                ),
                'auto_hide'      => TRUE,
        );

        if(th::isMobile()) {
            $page_data['count_out']=2;
            $page_data['count_in']=2;
        }

        $view->news = (new Model_News())->where('created','<', time())->where('published','=','1')->order_by('created','desc')->limit($per_page)->offset(($this->request->param('id',1)-1)*$per_page)->find_all();
        $view->page = Pagination::factory($page_data)->render('pagination/interactive1');

        $this->template->content=$view;
    }

    public function action_send(){

        if ($this->request->method()!='POST'){
            throw new HTTP_Exception_404;
        }


        $name=arr::get($_POST,'name');
        $email=arr::get($_POST,'email');
        $subject=arr::get($_POST,'subject');
        $message=arr::get($_POST,'comments');

        $name=html::chars($name);
        $email=html::chars($email);
        $subject=html::chars($subject);
        $devmessage = $message=html::chars($message);


        $message=<<<MES
From: $name $email
Subject: $subject
$message
MES
;

        $c = new Model_Comments();
        $c->name=$name;
        $c->email=$email;
        $c->phone=$subject;
        $c->message=$devmessage;
        $c->save();

        th::ceoAlert($message);

        $this->auto_render=false;
        $a = 'Thank You for contacting us!';
        $this->response->body($a);
    }


    public function action_box(){
        $this->auto_render=false;

        $static = kohana::$config->load('static');

         $sql="select g.name, g.visible_name, '{$static['static_domain']}'||g.image as image, g.id as game_id , category, demo, sort
                from games g
                join office_games og on og.game_id = g.id
                where og.office_id = :o_id
                    and og.enable = 1
                    and g.show=1
                    and brand='agt'
                    order by g.sort";

         $games=db::query(1, $sql)->param(':o_id',777)
                               ->execute()
                               ->as_array();


         $games=db::query(1, $sql)
                 ->param(':br','agt')
                 ->param(':o_id',777)
                               ->execute()
                               ->as_array();

        $view=new View('site/interactive/box');
        $view->games=$games;
        $this->response->body($view);


    }


    public function action_info(){
        $id=$this->request->param('id');
        $game=new Model_Game(['name'=>$id]);
        if (!$game->loaded() or $game->show!=1){
            throw new HTTP_Exception_404();
        }

        $sql="select name,image
                from games
                where show=1
                and brand='agt'";

        $other=db::query(1, $sql)->execute()->as_array();
        shuffle($other);

        $config=Kohana::$config->load('agt/'.$game->name);
        $txtConf=[];

        if ( isset($config['bars']) && is_array($config['bars']) ){
            $heigth=$config['heigth']??3;
            $txtConf[]=count($config['bars'])."x$heigth";
        }


        if( isset($config['lines']) && is_array($config['lines']) ){
            $txtConf[]=count($config['lines']).' paylines';
        }

        $txtConf=count($txtConf)>0 ? implode(', ',$txtConf) : false;

        $img=false;
        $dir=DOCROOT."games".DIRECTORY_SEPARATOR."agt".DIRECTORY_SEPARATOR."screen".DIRECTORY_SEPARATOR."{$game->name}".DIRECTORY_SEPARATOR."small";
        if (is_dir($dir)){
            $img= scandir($dir);
            foreach($img as $k=>$i){
                if ($i=='.' || $i=='..'){
                    unset($img[$k]);
                }
            }
            $img=count($img)>0 ? $img : false;
        }

        $view=new View('/site/interactive/info');
        $view->game=$game;
        $view->other=$other;
        $view->txtConf=$txtConf;
        $view->img=$img;

        $this->template->content=$view;


    }
	
	public  function action_bcard(){

        $view=new View('site/interactive1/bcard');
        $this->template->content=$view;

    }


}
