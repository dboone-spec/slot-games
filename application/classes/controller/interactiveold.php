<?php

class Controller_Interactive extends Controller_Base{



    public $template='layout/interactive';
    public $active=null;


    public function before() {

        if (PROJECT!=1){
            throw new HTTP_Exception_404();
        }
	
	if(API_DOMAIN && Kohana::$environment == Kohana::PRODUCTION) {
            throw new HTTP_Exception_404;
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

        $this->active='index';
        /*
        $vg = new vipgameapi();
        $games = $vg->gamelist();
        */
        $static = kohana::$config->load('static');
         $sql="select g.name, g.visible_name, '{$static['static_domain']}'||g.image as image, g.id as game_id , category, demo, label
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

        $view=new View('site/interactive/index');
        $view->games=$games;
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




    public function action_send(){

        if ($this->request->method()!='POST'){
            throw new HTTP_Exception_404;
        }

        $name=arr::get($_POST,'name');
        $email=arr::get($_POST,'email');
        $subject=arr::get($_POST,'subject');
        $message=arr::get($_POST,'message');

        $name=html::chars($name);
        $email=html::chars($email);
        $subject=html::chars($subject);
        $message=html::chars($message);


        $message=<<<MES
From: $name $email
Subject: $subject
$message
MES
;

        $a=['331325323',//v
            '847393',//i
            ];



        foreach ($a as $id){
            tg::send($id,$message);
        }

        $this->auto_render=false;
        $a = ['response'=>'success'];
        $this->response->body(json_encode($a));
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
        $dir=DOCROOT."games/agt/screen/{$game->name}/small";
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


}
