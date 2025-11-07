<?php

$whiteIp=[
  
];

//убрано 15.06.20
/*if( ( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']!='app.site-domain.com') 
	&&isset($_SERVER['HTTP_CF_IPCOUNTRY']) 
	&& ( !in_array($_SERVER['REMOTE_ADDR'],$whiteIp) ) ) {
    $country=strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
    if(in_array($country,['nl','aw','bq','cw','fr','mf','us'])) {
		file_put_contents( 'ips' ,date('d-m-Y H:i:s ').$_SERVER['REMOTE_ADDR']."\r\n",FILE_APPEND );
        echo 'Sorry, no access for Your Country';
        exit;
    }
}*/

//перенесено в контроллер 26.08.22
/*if(isset($_SERVER['HTTP_CF_IPCOUNTRY']) && strpos($_SERVER['HTTP_HOST'],'content.')===0 && $_SERVER['REMOTE_ADDR']!='185.14.31.73') {
    $country=strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
    if(in_array($country,['nl','aw','bq','cw','fr','mf','us'])) {
	
        echo 'Sorry, no access for Your Country';
        exit;
    }
}*/


if (isset($_SERVER['HTTP_CF_IPCOUNTRY']) && isset($_SERVER['HTTP_HOST']) ) {
    $country = strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
    if (in_array($country, ['ru', 'tr', 'kz'])) {
        if (strpos($_SERVER['HTTP_HOST'], 'site-domain') !== false) {
            echo '<h1 style="display: flex;justify-content: center;text-shadow: 1px 0 1px #fff,0 1px 1px #fff,-1px 0 1px #fff,0 -1px 1px #fff;">Sorry, no access for your country</h1>';
            exit;
        }
    }
}

if(isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'],['agtint.ru'])) {
    $protocol='http://';
    $location='megawinapp.com';
    header('Location: ' . $protocol . $location . $_SERVER['REQUEST_URI']);
}

if(isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'],['megawinapp.com'])) {
    $protocol='https://';
    $location='site-domain.com';
    header('Location: ' . $protocol . $location . $_SERVER['REQUEST_URI']);
}

if(isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'],['office.megawinapp.com'])) {
    $protocol='https://';
    $location='office.site-domain.com';
    header('Location: ' . $protocol . $location . $_SERVER['REQUEST_URI']);
}

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#application
 */
$application = '../application';

/**
 * The directory in which your modules are located.
 *
 * @link http://kohanaframework.org/guide/about.install#modules
 */
$modules = '../modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#system
 */
$system = '../system';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @link http://kohanaframework.org/guide/about.install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link http://www.php.net/manual/errorfunc.configuration#ini.error-reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Make the application relative to the docroot, for symlink'd index.php
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot, for symlink'd index.php
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot, for symlink'd index.php
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

// Define the absolute paths for configured directories
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);

// Clean up the configuration vars
unset($application, $modules, $system);

if (file_exists('install'.EXT))
{
	// Load the installation check
	return include 'install'.EXT;
}

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::factory()
	->execute()
	->send_headers(TRUE)
	->body();
