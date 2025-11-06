<?php

class Controller_Gameinfo extends Controller{
    
    
    
    public function action_table() {
        
        $id=$this->request->param('id');
        $bet=arr::get($_GET,'bet',1);
        $lines=arr::get($_GET,'lines',1);
        
        if (in_array($id,['acesandfaces','jacksorbetter','tensorbetter'])){
            if (!in_array($lines,[1,2,3,4,5])){
                $lines=1;
            }
            
            $c= Kohana::$config->load('videopoker/'.$id);
            unset($c['level'][0]);
            unset($c['level'][1]);
            $view=new View('test/poker');
            $view->game=$id;
            $view->c=$c;
            $view->bet=$bet;
            $view->lines=$lines;
            $this->response->body($view);
            return null;
        }
        
        if (in_array($id,['keno'])){
            
            $c= Kohana::$config->load('keno/keno');
                    if (isset($c['pay'][0])){
                        unset($c['pay'][0]);
                    }
            $view=new View('test/keno');
            $view->game=$id;
            $view->c=$c;
            $view->bet=$bet;
            $view->lines=$lines;
            $this->response->body($view);
            return null;
        }


        
        $c= Kohana::$config->load('agt/'.$id);
        $c=$c->as_array();
        if (!isset($c['bars'])){
            throw new HTTP_Exception_404();
        }
        
        $c['barCount']=count($c['bars']);
        $c['wild_multiplier']=$c['wild_multiplier']?? 1;
        
        $c['scatter']=$c['scatter']?? [];   
        $c['free_games']=$c['free_games']?? [];
        $c['anypay']=$c['anypay']?? [];
        $c['free_multiplier']=$c['free_multiplier']?? 1;


        $view=new View('test/table');
        $view->game=$id;
        $view->c=$c;
        $view->bet=$bet;
        $view->lines=$lines;
        $this->response->body($view);
        
    }
    
    
}