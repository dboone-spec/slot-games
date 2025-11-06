<?php

class Controller_Admin1_Service extends Controller_Admin1_Base
{

    public function action_index()
    {

        $moonGames = [
            863=>'ToTheMoon',
            903=>'Aerobet',
        ];

        if(defined('LOCAL') && LOCAL) {
            $moonGames = [
                83 => 'app',
                863 => 'appcopy',
                53 => 'appmulti',
            ];
        }

        $rdb=dbredis::instance();

        $json=json_decode($rdb->get('moon_apps'),1);

        if(!empty($json)) {
            foreach($json as $id=>$val) {
                if($val>0) {
                    unset($moonGames[$id]);
                }
            }
        }

        if($this->request->method()=='POST') {
            $game=$this->request->param('id');

            if(isset($moonGames[$game])) {

                $json[$game]=time();

                $rdb->set('moon_apps',json_encode($json));
            }

            $this->request->redirect('/enter/service');
        }

        $v=new View('admin1/service/moon');
        $v->moonGames=$moonGames;

        $this->template->content=$v;
    }

}
