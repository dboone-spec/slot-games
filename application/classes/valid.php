<?php


class Valid extends Kohana_Valid
{

    public static function email($email,$strict = FALSE)
    {
        $valid = parent::email($email,$strict);

        if($valid) {
            foreach(['@wimsg.com','@vmani.com','@p33.org'] as $bad) {
                if(strpos($email,$bad)!==FALSE) {
                    $valid=false;
                }
            }
        }
        return $valid;
    }

}
