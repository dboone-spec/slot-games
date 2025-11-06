<?php

class Controller_Online extends Controller_Base{



    public $template='layout/online';
    public $active=null;



    public function before(){
        if (PROJECT!=2){
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

        $this->active='index';
        /*
        $vg = new vipgameapi();
        $games = $vg->gamelist();
        */
        $static = kohana::$config->load('static');

        $strict='';

        if(th::isMobile()) {
            $strict=' and g.mobile=1';
        }

         $sql="select g.name, g.visible_name, '{$static['static_domain']}'||g.image as image, g.id as game_id , category, demo, brand, provider
                from games g
                join office_games og on og.game_id = g.id
                where og.office_id = :o_id
                    and og.enable = 1
                    and g.show=1
                    {$strict}
                    and g.brand!='agt'
                    order by g.sort";

         $games=db::query(1, $sql)
                 ->param(':o_id',OFFICE)
                               ->execute()
                               ->as_array();

        $view=new View('site/online/index');
        $view->games=new View('site/online/games');
        $view->games->games=$games;
        $this->template->content=$view;


    }


    public  function action_games(){


        $static = kohana::$config->load('static');
         $sql="select g.name, g.visible_name, '{$static['static_domain']}'||g.image as image, g.id as game_id , category, demo, brand, provider
                from games g
                join office_games og on og.game_id = g.id
                where og.office_id = :o_id
                    and og.enable = 1
                    and g.show=1
                    and g.brand!='agt'
                    order by g.sort";

         $games=db::query(1, $sql)
                 ->param(':o_id',777)
                               ->execute()
                               ->as_array();

        $view=new View('site/online/games');
        $view->games=$games;
        $this->template->content=$view;


    }



    public  function action_contacts(){


        $view=new View('site/online/contacts');
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
        $this->response->body('1');
    }





}
