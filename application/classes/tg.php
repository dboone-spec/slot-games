<?php

class tg {
    
    static function send($chatid,$message,$bot_instance=0){
        
        if(PROJECT==1){

            $bot_instances=[
                '8079692883:AAEEv9kvlY4i3Y2tY1MnzUl-GgOgsAphtGA',
                '7943741987:AAFJiEeMYDFjitCyNdUuMPS7qCTaLIEJ5mw'
            ];

            $token=$bot_instances[$bot_instance];
        }
        else {
            throw new Exception('Failed to sent message');
        }
        
                
        $message= urlencode($message);
        
        
        $url="https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatid}&text={$message}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result= json_decode($result);
        
        if(!isset($result->ok)){
            return false;
        }
        
        if ($result->ok===true){
            return true;
        }

        return false;
        
    }
    
    
    
    static function update(){
        
         if(PROJECT==1){
            $token='8079692883:AAEEv9kvlY4i3Y2tY1MnzUl-GgOgsAphtGA';
        }
        elseif(PROJECT==2){
            $token='1282132724:AAE607eY1w7q_WgLpFvFz8PHknaZKndXco4';
        }
        else {
            throw new Exception('Failed to sent message');
        }
        $offset= Status::instance()->tgOffset;
        $url="https://api.telegram.org/bot{$token}/getUpdates?offset={$offset}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result= json_decode($result);
        
        
        if(!isset($result->result)){
            return null;
        }
        
        
        if (count($result->result)==0){
            return false;
        }

        $offset=end($result->result);
        $offset=$offset->update_id;
        Status::instance()->tgOffset=$offset;
        
        if ($result->ok!==true){
            return false;
        }

        $users=[];
        
        foreach($result->result as $r){
            $users[$r->message->from->username]=$r->message->from->id;
        }
        
        foreach ($users as $name=>$id){
            
            $sql='update persons
                    set tgchatid=:id
                    where tgname=:name';
            

            
            db::query(Database::UPDATE,$sql)->param(':id',$id)
                                            ->param(':name',$name)
                                            ->execute();
        }
        
    }
    
    
    
}

