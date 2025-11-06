<?php

class URL extends Kohana_URL
{

    public static function base($protocol = NULL,$index = FALSE)
    {
        // Start with the configured base URL
        $base_url = Kohana::$base_url;


        if(!$protocol)
        {
            // Use the configured default protocol
            $protocol = parse_url($base_url,PHP_URL_SCHEME);
        }

        if($index === TRUE AND ! empty(Kohana::$index_file))
        {
            // Add the index file to the URL
            $base_url .= Kohana::$index_file . '/';
        }

        if($port = parse_url($base_url,PHP_URL_PORT))
        {
            // Found a port, make it usable for the URL
            $port = ':' . $port;
        }

        if($domain = parse_url($base_url,PHP_URL_HOST))
        {
            // Remove everything but the path from the URL
            $base_url = parse_url($base_url,PHP_URL_PATH);
        }
        else
        {
            // Attempt to use HTTP_HOST and fallback to SERVER_NAME
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']??'';
        }

        // Add the protocol and domain to the base URL
        $base_url = '//' . $domain . $port . $base_url;

        return $base_url;
    }


	public static function site($uri = '', $protocol = NULL, $index = TRUE)
	{
		// Chop off possible scheme, host, port, user and pass parts
		$path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', ltrim($uri, '/'));

		if ( ! UTF8::is_ascii($path))
		{
			// Encode all non-ASCII characters, as per RFC 1738
			$path = preg_replace_callback('~([^/]+)~', 'URL::_rawurlencode_callback', $path);
		}

		// Concat the URL
		return URL::base($protocol, $index).$path;
	}
}
