<?php

class vipgameapi {
    protected $_url='/gameapi/';
    protected $_office_id=777;
    protected $_secretkey='aaaaaaa';

    public function __construct()
    {
        $this->_url = 'http://'.API_DOMAIN_URLS[0].$this->_url;
    }

    public function gamelist() {
        $o = new Model_Office($this->_office_id);
        $params = [
            'office_id' => $this->_office_id,
        ];
        $sign = $o->sign($params);

        $url=$this->_url.'list?'. http_build_query($params).'&sign='.$sign;

        $p = new Parser();
        $content = $p->get($url);

        $result = json_decode($content,1);
        if($result['error']=='1') {
            echo '['.$result['error_code'].'] '.$result['error_message'];
            exit;
        }
        return $result['data'];
    }

    public function getgame($game_id,$demo=0) {
        $o = new Model_Office($this->_office_id);

        if(!$demo && !auth::$user_id) {
            return 'Error. Need auth';
        }

        $params = [
            'office_id' => $this->_office_id,
            'demo' => $demo,
            'lang' => I18n::$lang,
        ];

        if(auth::$user_id) {
            $params['login']=auth::user()->name;
        }

        $sign = $o->sign($params);

        $url=$this->_url.'getgame/'.$game_id.'?'. http_build_query($params).'&sign='.$sign;

        $p = new Parser();
        $content = $p->get($url);

        $result = json_decode($content,1);

        if($result['error']=='1') {
            echo '['.$result['error_code'].'] '.$result['error_message'];
            exit;
        }

        echo '<style>
                html,body,iframe {margin:0;padding:0;border:0;}
              </style>';
        return $result['data'];
    }
}