<?php

class Controller_Parser extends Controller {
    
    
    
    public function action_winline(){
        
        $p=new Parser_Winline;
        
        $p->getList();
        
    }
    
    
    
    
    
    
    
}

