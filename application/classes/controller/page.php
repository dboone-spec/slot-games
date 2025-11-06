<?php

class Controller_Page extends Controller_Base{
	
	public $template='layout/site';
	
	public function action_view(){

		$id=$this->request->param('id');
		        
        if($this->request->is_ajax()) {
            $this->auto_render = false;
            
            try {
                $view=new View('pages/'.$id);
            }
            catch (Exception $exc){
                throw new HTTP_Exception_404;
            }
            
            $template = new View('pages/layoutpopup');
            $template->content = $view;
            $template->page_type = $id;
            $this->response->body($template->render());
            
        } else {
            try {
                $view=new View('pages/'.$id);
            }
            catch (Exception $exc){
                throw new HTTP_Exception_404;
            }

            $g = orm::factory('game')->where('show', '<>', 0)->find_all();

            $cats = [];        
            foreach ($g as $v) {
                if(!in_array($v->brand, $cats)) {
                    $cats[] = $v->brand;
                }
            }
            
            $this->template->cats = $cats;
            $this->template->page_type = $id;
            $this->template->content=$view;
        }
		
	}

	
}
