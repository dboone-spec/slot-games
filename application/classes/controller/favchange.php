<?php

class Controller_Favchange extends Controller_Base
{

    public $auto_render = false;

    public function action_index()
    {
        $postData = file_get_contents('php://input');//string получаем json
        $data     = json_decode($postData,true);//array
        $a        = new Model_Userfavourite();
        if(!isset($data['locgames']))
        {
            if(isset($data['user_id']))
            {
                $userid  = $data['user_id']; // пользователь
                $game[0] = $data['game'];//array измненная игра
                $state   = $data['on'];//состояние

                $games = json_encode($game,JSON_FORCE_OBJECT);//string игра для базы

                $b = $a->checkuser($userid);//проверка на существование пользователя

                if(!isset($b[0]['user_id']) or $userid != $b[0]['user_id'])
                {
                    $a->addgame($userid,$games);//добавляем нового пользователя с отмеченной игрой
                }
                else
                {
                    $c = $a->getgames($userid);//array games from db получаем массив игр у пользователя
                    $d = $c[0]['games'];//string games from db json с играми
                    $b = json_decode($d,true);//array массив с играми
                    $e = array_unique(array_merge($b,$game)); //добавляем новую игру в массив
                    sort($e);
                    if($state == false)
                    {
                        foreach($e as $key => $item)
                        {
                            if($game[0] == $item)
                            {
                                unset($e[$key]);
                            }
                        }
                        sort($e);
                    }
                    $newgames = json_encode($e,JSON_FORCE_OBJECT); //json новых игр

                    $a->updgames($userid,$newgames);// обновляем игры у пользователя
                }
            }
        }
        else
        {
            $bb = $a->checkuser(auth::parent_acc()->id);//проверка на существование пользователя
            if(!isset($bb[0]['user_id']))
            {
                $lg[0] = $data['locgames'];
                $enclg = json_encode($lg[0],JSON_FORCE_OBJECT);
                $a->addgame(auth::parent_acc()->id,$enclg);
            }
        }
    }

}
