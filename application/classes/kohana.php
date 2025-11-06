<?php

class Kohana extends Kohana_Core{



    public static function shutdown_handler(){


        if (!defined("DEBUG_APIGETGAME")) {
            return parent::shutdown_handler();
        }

			$_end=  microtime(1);
		$sql='insert into vlog (domain,created,time,url,post,get,pid) values (:domain,:created,:time,:url,:post,:get,:pid)';
		$url='emptyurl';

        $post = [];
        if($_POST){
            $post = $_POST;
            if(isset($post['password'])){
                $post['password'] = str_repeat('*', UTF8::strlen($post['password']));
            }
            foreach(array("face","selfiepasp","pasp1","pasp2") as $k){
                if(isset($post[$k])){
                    $post[$k] = '_base64_encode_image_';
                }
            }
        }

        $get = $_GET;

        if (Kohana::$is_cli){
            $args = arr::get($_SERVER,'argv');
            if($args){
                $args = implode(' ', $args);
            }
            $url = $args;
            $domain = 'cli';
        }
        else{
            if ( ! empty($_SERVER['PATH_INFO'])){
                $url = $_SERVER['PATH_INFO'];
            }
            elseif (isset($_SERVER['REQUEST_URI'])){
                $url = rawurldecode($_SERVER['REQUEST_URI']);
            }
            elseif (isset($_SERVER['PHP_SELF'])){
                $url = $_SERVER['PHP_SELF'];
            }
            elseif (isset($_SERVER['REDIRECT_URL'])){
                $url = $_SERVER['REDIRECT_URL'];
            }
            $p=strpos($url,'?');

			if ($p!==false){
				$url = substr($url, 0,$p);
			}
            $domain = (isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'');
        }

			unset($get['rnd']);

			db::query(Database::INSERT,$sql)->param(':time',$_end-$GLOBALS['_start'])
                                ->param(':domain',$domain)
								->param(':created',time())
								->param(':url',$url)
                                // Если данные приходят из php://input
								->param(':post',isset($GLOBALS["raw_content_to_log"]) ? $GLOBALS["raw_content_to_log"] : json_encode($post))
								->param(':get',json_encode($get))
								->param(':pid',Person::$user_id)
								->execute('log');



        return parent::shutdown_handler();
    }


}