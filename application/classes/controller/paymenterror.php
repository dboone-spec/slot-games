<?php

class Controller_Paymenterror extends Controller_Index {

    public function action_index() {
        parent::action_index();

        $this->template->popup_link = '/paymenterror/popup';
    }

    public function action_popup() {
        $this->auto_render = false;

        $view = new View('site/payment/error');

        $this->response->body($view->render());
    }

}
