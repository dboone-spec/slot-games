<?php


class Parser{


    protected $curl;
    public $error='';
	protected $html;
	protected $rawhtml;
	protected $writer;
	protected $rString = "/^(\"|')(.*?)(\"|')$/";
	protected $rBoolean = "/(false|true)/";
	protected $rNumber = "/([0-9.]+?)/";
	private $isHtmlLoad=false;

	//0-connect timeout,1-exec timout
    protected $_curl_timeouts=[
        1,7
    ];

    const ERROR_CONNECT_TIMEOUT=1;
    const ERROR_EXEC_TIMEOUT=2;

    public function getErrorType($time) {

        $err_type=static::ERROR_CONNECT_TIMEOUT;

        if($time>=$this->_curl_timeouts[1]) {
            $err_type=static::ERROR_EXEC_TIMEOUT;
        }

        return $err_type;

        //old
        /*if($time>1) {
            $time=floor($time);
        }

        if(bccomp($time,$this->connect_timeout,1)==0) {
            return static::ERROR_CONNECT_TIMEOUT;
        }
        if(bccomp($time,$this->exec_timeout,1)==0) {
            return static::ERROR_EXEC_TIMEOUT;
        }

        return 0;*/
    }

	public function __construct($config_type='default') {

        $this->_curl_timeouts=kohana::$config->load('static.curl_timeouts.'.$config_type);

		$this->html=new SimpleHtmlDom();
		$this->InitCurl();
    }



	public function ClearCookie(){
		if (file_exists(public_path()."/cookie.txt")){
			unlink(public_path()."/cookie.txt");
		}
	}

	public function disableFailOnError() {
        curl_setopt($this->curl, CURLOPT_FAILONERROR, 0);
    }

	public function UseMobile(){
		curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 4.4.2; thl T6S Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.124 Mobile Safari/537.36'); //Прописываем User Agent, чтобы приняли за своего
	}

	public function UseComp(){
		curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36'); //Прописываем User Agent, чтобы приняли за своего
	}


	public function InitCurl(){
		$this->curl= curl_init();
		curl_setopt($this->curl, CURLOPT_ENCODING, 0);
		$this->UseComp();
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, DOCROOT."cookie.txt");
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, DOCROOT."cookie.txt");
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_FAILONERROR, 1);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->_curl_timeouts[0]);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->_curl_timeouts[1]);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);

                //curl_setopt($this->curl, CURLOPT_USERPWD, "user999:qwe");


	}

	public function ReInitCurl(){
		curl_close($this->curl);
		$this->InitCurl();



	}


	public function __destruct() {
		curl_close($this->curl);
    }

	public $http_code=0;


    public function post($url,$post=array(), $json=false, $headers=array(),$auth=''){

		$data=array();

        if(!$json) {
            $data = http_build_query($post);
        }
        else {
            $data = json_encode($post);
        }

		curl_setopt($this->curl, CURLOPT_URL,$url);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);

        if(!empty($headers)) {
            curl_setopt($this->curl, CURLINFO_HEADER_OUT ,true);
            curl_setopt($this->curl, CURLOPT_HTTPHEADER,$headers);
        }

        if(!empty($auth)) {
            curl_setopt($this->curl, CURLOPT_USERPWD,$auth);
        }

		curl_setopt($this->curl, CURLOPT_POST,1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data); //Устанавливаем значения, которые мы передаем через POST на сервер в нужном формат

		$this->rawhtml=curl_exec($this->curl);

//        var_dump(curl_getinfo($this->curl,CURLINFO_HEADER_OUT ));
        $this->error = curl_error($this->curl);
		
		$this->http_code=curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        $err_no=curl_errno($this->curl);

        if(in_array($err_no,[28,])) {
            //убрать эту проверку со счетчиком и слать смс при ошибке

//            th::checkCurlError();
        }


		$this->isHtmlLoad=false;
		return $this->rawhtml;



    }



	public function get($url, $headers=[], $auth=''){

		curl_setopt($this->curl, CURLOPT_URL,$url);

        if(!empty($headers)) {
            curl_setopt($this->curl, CURLINFO_HEADER_OUT ,true);
            curl_setopt($this->curl, CURLOPT_HTTPHEADER,$headers);
        }

        if(!empty($auth)) {
            curl_setopt($this->curl, CURLOPT_USERPWD,$auth);
        }

		$this->rawhtml=curl_exec($this->curl);
		
		$this->http_code=curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

//        var_dump(curl_getinfo($this->curl,CURLINFO_HEADER_OUT ));
//        echo curl_error($this->curl);

		$this->isHtmlLoad=false;
		return $this->rawhtml;

	}



public function filepost($url,array $assoc = array(), array $files = array()) {

    // invalid characters for "name" and "filename"
    static $disallow = array("\0", "\"", "\r", "\n");

    // build normal parameters
    foreach ($assoc as $k => $v) {
        $k = str_replace($disallow, "_", $k);
        $body[] = implode("\r\n", array(
            "Content-Disposition: form-data; name=\"{$k}\"",
            "",
            filter_var($v),
        ));
    }

    // build file parameters
    foreach ($files as $k => $v) {
        switch (true) {
            case false === $v = realpath(filter_var($v)):
            case !is_file($v):
            case !is_readable($v):
                break; // or return false, throw new InvalidArgumentException
        }
        $data = file_get_contents($v);
        $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
        $k = str_replace($disallow, "_", $k);
        $v = str_replace($disallow, "_", $v);
        $body[] = implode("\r\n", array(
            "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
            "Content-Type: application/octet-stream",
            "",
            $data,
        ));
    }

    // generate safe boundary
    do {
        $boundary = "---------------------" . md5(mt_rand() . microtime());
    } while (preg_grep("/{$boundary}/", $body));

    // add boundary for each parameters
    array_walk($body, function (&$part) use ($boundary) {
        $part = "--{$boundary}\r\n{$part}";
    });

    // add final boundary
    $body[] = "--{$boundary}--";
    $body[] = "";

     curl_setopt_array($this->curl, array(
        CURLOPT_POST       => true,
        CURLOPT_POSTFIELDS => implode("\r\n", $body),
        CURLOPT_HTTPHEADER => array(
            "Expect: 100-continue",
            "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
        ),
			));

	curl_setopt($this->curl, CURLOPT_URL,$url);
	curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);

	$this->rawhtml=curl_exec($this->curl);
	$this->isHtmlLoad=false;
	return $this->rawhtml;

}

public function LoadFormFile($file){

	$this->rawhtml=file_get_contents($file);
	$this->isHtmlLoad=false;
	return $this->rawhtml;
}


public function html(){
	if ($this->isHtmlLoad===false){
		$this->html->load($this->rawhtml);
		$this->isHtmlLoad=true;
	}
	return $this->html;
}

//поиск переменной в javascript;
public function JsVar ($var){
		if(strpos($this->rawhtml, $var) == false) return false;
		$var=  str_replace('[','\[', $var);
		$var=  str_replace(']','\]', $var);
		/**
		 * Match var variableName
		 */
		preg_match ("/($var\s=\s)(.[^;]*)/m", $this->rawhtml, $matches);

		$start = strpos($this->rawhtml, $matches[1]) + strlen($matches[1]);

		/* Get position until the end of statement */
		$stop = strpos($this->rawhtml, ";", $start);
		$value = substr($this->rawhtml, $start, ($stop-$start));

		/* Check is this a JSON variable */
		if ($this->is_json($value))
		{
			$value = json_decode($value, TRUE);
		}
		/* Check is this a string variable */
		elseif (preg_match($this->rString, $value, $matches))
		{
			$value = $matches[2];
		}

		/* Check is this a boolean variable */
		elseif (preg_match($this->rBoolean, $value, $matches))
		{
			$value = filter_var($matches[0], FILTER_VALIDATE_BOOLEAN);
		}

		/* Check is this an integer variable */
		elseif (preg_match($this->rNumber, $value, $matches))
		{
			$value = intval($matches[1]);
		}

		return $value;
	}


	/**
 * Find JSON in string
 * @return array
 */
public function findJSON(){
	preg_match_all('/\[([{|"].*["|}])\]/', $this->js_string, $matches);
	$json = array_filter($matches[0], array($this, 'is_json'));
	return $json;
}

/**
 * Validate JSON
 * @return boolean
 */
public function is_json()
{
	call_user_func_array('json_decode', func_get_args());
	return (json_last_error()===JSON_ERROR_NONE);
}




}
