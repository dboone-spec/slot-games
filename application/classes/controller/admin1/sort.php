<?php

class Controller_Admin1_Sort extends Controller_Admin1_Base
{


    public function action_index()
    {


        $office_id = arr::get($_GET, 'office_id', 1073);

        $office = new Model_Office($office_id);

        $view = new View('admin1/sort/index');

        $view->office = $office;
        $view->officesList = Person::user()->officesName(null, true);

        $this->template->content = $view;


    }


}
