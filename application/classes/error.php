<?php

class Error extends Kohana_Exception {

	public static function exception(Exception $e) {

		try {
			if ($e instanceof HTTP_Exception_404) {
				header("Content-type:text/html;charset=utf-8", TRUE, 404);
				
			} 
			else {
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

				kohana::$log->add(Log::ERROR, $error);

				$strace = Kohana_Exception::text($e);
				$strace .= "\r\n\r\n" . $e->getTraceAsString();
				
				$strace .= "\r\n\r\n";

				kohana::$log->add(Log::STRACE, $strace);
				kohana::$log->write();
				return true;
			}
		}
		/* Обязательный перехват любый исключений во избежание зацикливания */ 
		catch (Exception $e) {
			exit(1);
		}
	}

	public static function error_handler($code, $error, $file = NULL, $line = NULL) {

		if (error_reporting() & $code) {
			// This error is not suppressed by current error reporting settings
			// Convert the error into an ErrorException
			throw new ErrorException($error, $code, 0, $file, $line);
		}

		// Do not execute the PHP error handler
		return TRUE;
	}

}
