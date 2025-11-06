<?php

return array(
        'native' => array(
                'name'     => 'session',
                'lifetime' => 3600*10,
        ),
        'cookie' => array(
            'name' => 'superashka',
            'encrypted' => Request::current()->protocol()=='http',
            'lifetime' => Date::DAY,
        ),
);
