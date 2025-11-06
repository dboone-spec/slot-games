<?php


class Controller_Paymentsuccess extends Controller_Index {

    public function action_index() {
        parent::action_index();
        
        $this->template->popup_link = '/paymentsuccess/popup';
    }

    public function action_popup() {
        $this->auto_render = false;

        $view = new View('site/payment/success');

        $this->response->body($view->render());
    }

}
