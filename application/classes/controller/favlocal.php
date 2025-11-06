<?php

class Controller_Favlocal extends Controller_Base
{

    public $auto_render = false;

    public function action_index()
    {
        $postData = file_get_contents('php://input');//string получаем json
        $data     = json_decode($postData,true);//array
        $newgames = $data['games'];
        $games = json_decode($newgames,true);
        
        $a = new Model_Userfavourite();
        $b=$a->checkusertmp();
        $game = json_encode($games,JSON_FORCE_OBJECT);
        if(empty($b)){
            $a->addgametmp(NULL,$game);
        }
        else {
            $a->updgamestmp($game);
        }
        unset($a);
    }

}
