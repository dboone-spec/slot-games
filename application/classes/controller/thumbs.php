<?php

class Controller_Thumbs extends Controller_Template{

    public $template='site/thumbs/index';

	public function action_index() {
        $sql="select g.name, g.visible_name
                from games g
                where g.show=1
                and brand='agt'
                order by g.sort nulls last";

        $games=db::query(1, $sql)
                               ->execute()
                               ->as_array();

        $this->template->games = $games;

    }


}

