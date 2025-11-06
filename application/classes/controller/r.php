<?php
/*
 * для записи и регистрации игроков
 * из партнерской программы
 */
class Controller_R extends Controller_Base
{

    public function before()
    {

        $partner = $this->request->action();
        $project = $this->request->param('id');
        $label = arr::get($_GET,'fr');
        $label = strlen($label)>0?$label:null;
        
        if($label) {
            //метка партнера
            Cookie::set('msrc', $label, Date::YEAR);
        }
        
        if($partner AND $project) {
            //проставляем партнера в куку для пользователя
            Cookie::set('partner', $partner, Date::YEAR); //partner
            Cookie::set('project', $project, Date::YEAR); //partner
            
            $guid = Cookie::get('uniqueuser');
            
            if(!$guid) {
                $guid = guid::create();
                Cookie::set('uniqueuser', $guid, Date::YEAR);
            }
            
            /*
             * пишем в таблицу с переходами
             * от партнеров
             */
            $follow = new Model_Follow();
            $follow->partner = $partner;
            $follow->referrer = $this->request->referrer();
            $follow->project = $project;
            $follow->msrc = $label;
            $follow->hash = $guid;
            $follow->ip = $_SERVER['REMOTE_ADDR'];
            $follow->save();
            
            if($partner==42) { 
                Cookie::set('lang','en', Date::YEAR);  
            }
            
        }

        $this->request->redirect('/');
    }

}
