<?php
class guid{

static function create_guid_section($characters)
{
	$return = "";
	for($i=0; $i<$characters; $i++)
	{
		$return .= dechex(rand(0,15));
	}
	return $return;
}

static function ensure_length(&$string, $length)
{
	$strlen = strlen($string);
	if($strlen < $length)
	{
		$string = str_pad($string,$length,"0");
	}
	else if($strlen > $length)
	{
		$string = substr($string, 0, $length);
	}
}

public static function v3($namespace, $name)
{

    // Calculate hash value
    $hash = md5($namespace.$name);

    return sprintf(
        '%08s-%04s-%04x-%04x-%12s',
        // 32 bits for "time_low"
        substr($hash, 0, 8),
        // 16 bits for "time_mid"
        substr($hash, 8, 4),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 3
        (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
        // 48 bits for "node"
        substr($hash, 20, 12)
    );
}

static function create($without_dash=false)
{
	$microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = dechex($a_dec* 1000000);
	$sec_hex = dechex($a_sec);

	self::ensure_length($dec_hex, 5);
	self::ensure_length($sec_hex, 6);

	$guid = "";
	$guid .= $dec_hex;
	$guid .= self::create_guid_section(3);
    if(!$without_dash) {
	    $guid .= '-';
    }
	$guid .= self::create_guid_section(4);
    if(!$without_dash) {
        $guid .= '-';
    }
	$guid .= self::create_guid_section(4);
    if(!$without_dash) {
        $guid .= '-';
    }
	$guid .= self::create_guid_section(4);
    if(!$without_dash) {
        $guid .= '-';
    }
	$guid .= $sec_hex;
	$guid .= self::create_guid_section(6);

	return $guid;

}
	

}