<?php

class Controller_Secret extends Controller{

	
	
	public function action_index(){
		
		if (arr::get($_GET,'a35')=='lambda'){
			Cookie::set('secret','jharutqwce4');
			$this->request->redirect('/');
		}
		
		$s='<form> 
				<input type="text" name="a35" />
				<input type="submit" value="Ok" /> 
			</form>';
		
		$this->response->body($s);
	}
	
	
	
}

