<?php

class Controller_Terminal extends Controller_Base {

    public $template='layout/terminal';


    public  function action_index(){
	//Kohana::$log->add(Log::DEBUG,'initial terminal: '.Debug::vars($_SERVER));
        $this->active='index';

        /*$static = kohana::$config->load('static');
         $sql="select g.name, g.visible_name, '{$static['static_domain']}'||g.image as image, g.id as game_id , category, demo, label
                from games g
                join office_games og on og.game_id = g.id
                where og.office_id = :o_id
                    and og.enable = 1
                    and g.show=1
                    and brand='agt'
                    order by g.sort nulls last";

         $games=db::query(1, $sql)->param(':o_id',OFFICE)
                               ->execute()
                               ->as_array();*/
							   
		$sort=office::instance()->office()->sort();
        $games=office::instance()->office()->sorted_games;

        $games=array_filter($games,function($el) {return $el['branded']=='0';});

        $view=new View('site/terminal/index');
        $view->games=$games;
        $view->news = (new Model_News())->order_by('created','desc')->limit(3)->find_all();
        $this->template->content=$view;


    }

    public function action_error() {

        $this->template=View::factory('site/terminal/error');

    }


}
