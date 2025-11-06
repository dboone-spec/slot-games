<?php

class Controller_Pguid extends Controller{

    
    public function before() {
        $guid = $this->request->action();
        $n =  new Model_Savedpayments($guid);
        
        if(!$n->loaded()) {
            exit('Ошибка платежа. Code=962');
        }
        
        if($n->showed!=0) {
            exit('Платеж просрочен. Code=963');
        }
        
        $n->showed=1;
        $n->save();
        
        $data = json_decode($n->data,1);
        
        if(empty($data['link']) && !empty($data['info'])) {
            echo $data['info'];
            exit;
        }
        
        $this->request->redirect($data['link']);
    }
}
