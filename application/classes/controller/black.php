<?php

class Controller_Black extends Controller {


    public function action_index() {
        echo '<html><body><style>html,body{background: black;}</style></body></html>';
        exit;
    }

}