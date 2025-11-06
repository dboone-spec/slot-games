<?php

class Controller_Admin1_Promo extends Controller_Admin1_Base
{

    public function action_index() {

        $v = 'admin1/promo/index';


        $view=new View($v);
        $offices = [-1=>'-']+Person::user()->officesName(null,true);

        $jp_width=arr::get($_GET,'jp_width',250);
        $jp_height=arr::get($_GET,'jp_height',250);
        $bgimg=arr::get($_GET,'bgimg','');

        $view->office_id=arr::get($_GET,'office_id',-1);

        if(!isset($offices[$view->office_id])) {
            throw new HTTP_Exception_404();
        }

        if($size=arr::get($_GET,'size')) {
            list($jp_width,$jp_height)=explode('x',$size);
        }

        $sql='select id as game_id, promo_zip_name  as v
                from games
                where showpromo=1 and brand=\'agt\' order by 2';

        $games=db::query(1, $sql)->execute()->as_array('v');

        $view->offices=$offices;
        $view->games= array_combine(array_keys($games),array_keys($games));
        $view->jp_width=$jp_width;
        $view->jp_height=$jp_height;
        $view->bgimg=$bgimg;
		$view->bigcurrent = $this->bigcurrent;
        $this->template->content = $view;
    }
	
	public function action_langs() {

        $langs=Kohana::$config->load('languages.lang');

        I18n::lang('en');

        $csv = [['ISO 639-1 code','Language']];

        foreach ($langs as $code=>$l) {
            $csv[] = [
                $code,$l
            ];
        }

        $writer = new XLSXWriter();
        $writer->writeSheet($csv, 'AGT languages');            // no headers
        $this->response->headers('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->headers('Content-Disposition', 'attachment;filename="langs.xlsx"');
        $this->response->headers('Cache-Control', 'max-age=0');
        $this->response->body($writer->writeToString());
        $this->auto_render = false;
        return null;
    }

}
