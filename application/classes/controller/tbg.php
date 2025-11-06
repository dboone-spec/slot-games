<?php

class Controller_Tbg extends Controller {
     public function action_index() {
        $u = new Model_User(43);
        $token = guid::create();
        $u->betgames_token = $token;
        $u->betgames_token_time = time();
        $u->save();
        echo $token;
     }

}