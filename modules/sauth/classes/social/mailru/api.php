<?php

class Social_Mailru_Api extends Social_Mailru
{

    protected $api_url = 'http://www.appsmail.ru/platform/api';

    function sign(array $request_params,$secret_key)
    {
        ksort($request_params);
        $params = '';
        foreach($request_params as $key => $value)
        {
            $params .= "$key=$value";
        }
        return md5($params . $secret_key);
    }


    public function getUserInfo($uid) {
        $r['method']='users.getInfo';
        $r['app_id']=$this->config['id'];
        if($uid) {
            $r['uid']=$uid;
        }
        else {
            $r['session_key']=arr::get($this->get_access_token(),'access_token');
        }
        $r['secure']='1';
        $r['sig']=$this->sign($r,$this->config['secret']);
        $result = $this->curl_post($this->api_url,$r);
        return json_decode($result,1);
    }

    public function notification($message='',$uids=[]) {
        $r['method']='notifications.send';
        $r['app_id']=$this->config['id'];
        $r['uids']=implode(',',$uids);
        $r['text']=$message;
        $r['secure']='1';
        $r['sig']=$this->sign($r,$this->config['secret']);
        $result = $this->curl_post($this->api_url,$r);
        return json_decode($result,1);
    }

    public function message($message='',$uid=null) {
        $r['method']='messages.post';
        $r['app_id']=$this->config['id'];
        if($uid) {
            $r['uid']=$uid;
        }
        else {
            $r['session_key']=arr::get($this->get_access_token(),'access_token');
        }
        $r['message']=$message;
        $r['secure']='1';
        $r['sig']=$this->sign($r,$this->config['secret']);
        $result = $this->curl_post($this->api_url,$r);
        echo Debug::vars($result);
        return json_decode($result,1);
    }

    public function stream($text,$uid) {
        $r['method']='multipost.send';
        $r['app_id']=$this->config['id'];
        $r['uid2']=$uid;
        $r['text']=$text;
        $r['secure']='1';
        $r['session_key']=arr::get($this->get_access_token(),'access_token');
        $r['sig']=$this->sign($r,$this->config['secret']);
        $result = $this->curl_post($this->api_url,$r);
        var_dump($result);
        return json_decode($result,1);
    }

}
