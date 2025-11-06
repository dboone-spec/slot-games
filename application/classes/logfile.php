<?php

class logfile{

        public function __set($name, $value){
		$filename=APPPATH.'logs'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.$name;
		file_put_contents($filename, $value."\r\n", FILE_APPEND);
	}


        public static function create(string $data, $directory) {

            $directory = APPPATH.'logs'.DIRECTORY_SEPARATOR.$directory;

            if ( !is_dir($directory)) {
                mkdir($directory, 02777);
                chmod($directory, 02777);
            }

            $directory.=DIRECTORY_SEPARATOR.date('Y');

            if ( !is_dir($directory)) {
                mkdir($directory, 02777);
                chmod($directory, 02777);
            }

            $directory .= DIRECTORY_SEPARATOR.date('m');

            if ( !is_dir($directory)) {
                mkdir($directory, 02777);
                chmod($directory, 02777);
            }
			
			$filename = $directory.DIRECTORY_SEPARATOR.date('d').EXT;
			
			if ( ! file_exists($filename))
			{
			// Create the log file
				file_put_contents($filename, PHP_EOL.$data, FILE_APPEND);

				// Allow anyone to write to log files
				chmod($filename, 0666);
			}
			else {
				// Create the log file
				file_put_contents($filename, PHP_EOL.$data, FILE_APPEND);
			}

        }
}