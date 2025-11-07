<?php

class Social_Api extends Social
{

    public static function factory( $type, $fingerprint=null )
	{
		$social = 'Social_'.ucfirst($type).'_Api';
		$social = new $social;
		$social->alias = $type;
		$social->fingerprint = $fingerprint;
		$social->config = $social->config();
		$social->access_token = $social->get_access_token();
		return $social;
	}

    function get_profile_url() {
        return '';
    }

    function user_array($content) {
        return [];
    }

}
