<?php

class Parser_Winline extends Parser{
    
    
    public function getList(){
        
        $this->get('https://winlinebet.ru/now/?t=pre1x2&g=bychamp&s=futbol');
        
        echo $this->rawhtml;
        
    }
    
    
}
