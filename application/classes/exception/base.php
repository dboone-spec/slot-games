<?php

class Exception_Base extends Kohana_Exception {

    public static function exception($e) {

        try {

			if ($e instanceof HTTP_Exception_404){

				header('HTTP/1.0 404 Not Found');
				$s='Page not found. <br>
					<a href="/">Main page.</a>';
				exit($s);

			}
			if ($e instanceof HTTP_Exception_405){
                            header('HTTP/1.0 404 Not Found');
                            exit('<body style="background: black;"></body>');
                        }

            $code=$e->getCode();
            if ($e instanceof Exception_ApiResponse){
                $code=605;
            }

            if (is_object(Kohana::$log)) {

                if ($e instanceof Exception_ApiResponse){
                    logfile::create(date('Y-m-d H:i:s').' '.Kohana_Exception::text($e).PHP_EOL,'apierrors');
                }
                else {
                    Kohana::$log->writeException($e);
                }
            }
            $e->getMessage();
            $s= __('Произошла ошибка. Попробуйте обновить страницу или сделать запрос снова.<br>')
                  .__('Если ошибка повторяется, обратитесь в службу технической поддержки').'.';
            header("HTTP/1.1 ".$code);
            exit($s);
        }
        catch (Exception $e) {
            exit(1);
        }
    }

    public static function error($code, $error, $file = NULL, $line = NULL) {


        // This error is not suppressed by current error reporting settings
        // Convert the error into an ErrorException
        //Пишем в лог реальный текст ошибки
        try {
            $exc = new ErrorException($error, $code, 0, $file, $line);
            Kohana::$log->writeException($exc);
            //А выводим на экран заглушку
            $s= __('Произошла ошибка. Попробуйте обновить страницу или сделать запрос снова.<br>')
                  .__('Если ошибка повторяется, обратитесь в службу технической поддержки').'.';
            header("HTTP/1.1 ".$exc->getCode());
            exit($s);
        }
        catch (Exception $e) {
            exit(1);
        }


        // Do not execute the PHP error handler
        return TRUE;
    }

}
