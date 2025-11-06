<?php defined('SYSPATH') or die('No direct script access.');

class Log extends Kohana_Log {
	
        public static function userAction($user_id,$action) {
                $path = APPPATH.'logs'.DIRECTORY_SEPARATOR.'useractions';
                if( !is_dir($path)) {
                    mkdir($path, 02777);
                    chmod($path, 02777);
                }
                $path .= DIRECTORY_SEPARATOR.(substr($user_id, -1)??"0");
                if( !is_dir($path)) {
                    mkdir($path, 02777);
                    chmod($path, 02777);
                }
                $path .= DIRECTORY_SEPARATOR.(substr($user_id,-2,1)??"0");
                if( !is_dir($path)) {
                    mkdir($path, 02777);
                    chmod($path, 02777);
                }
                $file = $path .DIRECTORY_SEPARATOR. $user_id . ".json";
                file_put_contents($file, date('Y-m-d H:i:s').' '.$action.PHP_EOL, FILE_APPEND);
        }
    
	public function writeException($e,$trace = TRUE){
		$error = '';
		$error .= isset($_SERVER['REMOTE_ADDR'])?'IP: ' . $_SERVER['REMOTE_ADDR']:'';
		$error .= isset($_SERVER['HTTP_REFERER'])?' HTTP_REFERER: ' . $_SERVER['HTTP_REFERER']:'';
		$error .= isset($_SERVER['HTTP_HOST'])?' HTTP_HOST: ' . $_SERVER['HTTP_HOST']:'';
		
		if ( ! empty($_SERVER['PATH_INFO'])){
			$error .= ' PATH_INFO: ' . $_SERVER['PATH_INFO'];
		}
		else{
			if (isset($_SERVER['REQUEST_URI'])){
				$error .= ' REQUEST_URI: ' . rawurldecode($_SERVER['REQUEST_URI']);
			}
			elseif (isset($_SERVER['PHP_SELF'])){
				$error .= ' PHP_SELF: ' . $_SERVER['PHP_SELF'];
			}
			elseif (isset($_SERVER['REDIRECT_URL'])){
				$error .= ' REDIRECT_URL: ' . $_SERVER['REDIRECT_URL'];
			}
		}
		
		if( ! $trace){
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
				$error .= ' $_POST: ' . json_encode($post);
			}
		}
		
		$this->add(Log::ERROR, $error);
		
		$strace = Kohana_Exception::text($e);
		if($trace){
			$strace .= "\n\n" . $e->getTraceAsString();
		}
		$strace .= "\n__________\n";
		
		$this->add(Log::STRACE, $strace);
		$this->write();
		
	}
}

