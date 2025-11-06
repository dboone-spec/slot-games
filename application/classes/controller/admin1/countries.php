<?php

class Controller_Admin1_Countries extends Controller_Admin1_Base
{


    public function action_index()
    {
        $n = new Model_News(56);
        $view = new View('admin1/countries/index');
        $view->updated = false;

        if ($this->request->method() == 'POST' & person::$role == 'sa' & isset($_POST['text'])) {
            $a = new Model_Action();
            $a->person_id = person::$user_id;
            $a->type = 'countries';
            $a->model_name = 'countries';
            $a->model_id = 1;
            $a->old_data = $n->text;
            $a->new_data = $_POST['text'];
            $a->save();

            $n->text = $_POST['text'];
            $n->save();

            $view->updated = true;
        }



        $view->news = $n;
        $this->template->content = $view;
    }


}
