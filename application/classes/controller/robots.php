<?php

class Controller_Robots extends Controller
{

    public function action_index()
    {
		if($_SERVER['HTTP_HOST']=='site-domain.com'){
			 $this->response->body("User-agent: *\r\nAllow: /");
return null;
		}
		
        if(DEMO_DOMAIN || API_DOMAIN || TESTDOMAIN)
        {
            $robots = 'User-agent: *
Disallow: /';
        }
        else
        {
            $robots = 'User-agent: *
Disallow: 

Host: ';
            if(defined('THEME'))
            {
                $robots .= Dd::get_domain(THEME);
            }
        }
        $this->response->headers('Content-Type','text/plain');
        $this->response->body($robots);
    }

}
