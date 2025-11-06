<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('GMT');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

//TODO убрать в конфиг сервера потом
Kohana::$environment = Kohana::PRODUCTION;
Kohana::$environment = Kohana::DEVELOPMENT;

define('X_DOMAIN','/');

define('GAMECONTENT_DOMAIN','');

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'index_file'=>'',
	'base_url'   => '/',
	'errors'=> true,

));

// Устанавливаем обработчик для исключений
//set_exception_handler(array("Exception_Base", "exception"));
// Enable Kohana error handling, converts all PHP errors to exceptions.
//set_error_handler(array('Exception_Base', 'error'));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// 'auth'       => MODPATH.'auth',       // Basic authentication
	 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	 'database'   => MODPATH.'database',   // Database access
	 'image'      => MODPATH.'image',      // Image manipulation
	 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	'super'        => MODPATH.'super1',        // Admin module
	'pagination'   => MODPATH.'pagination',   // Pagination
	'sauth'   => MODPATH.'sauth',    // Social
    'captcha' => MODPATH.'captcha', //Captcha
    'flash' => MODPATH.'flash', //flesh module
	'clickhouse' => MODPATH.'clickhouse',
	));


Kohana::$environment = Kohana::DEVELOPMENT;
Kohana::$environment = Kohana::PRODUCTION;
//  demo
/*
define('MOON_GAME',false);
define('GAMECONTENT',true);
define('TERMINAL',false);
define('TELEGRAM',false);
define('DEMO_MODE',true);
define('API_DOMAIN',true);
define('DEMO_DOMAIN',true);
define('API_DOMAIN_URLS',['api']);
*/


//real

define('MOON_GAME',false);
define('GAMECONTENT',false);
define('TERMINAL',false);
define('TELEGRAM',false);
define('DEMO_MODE',false);
define('API_DOMAIN',true);
define('DEMO_DOMAIN',true);
define('API_DOMAIN_URLS',['api']);



define('INFINSOC_DOMAIN', false);

define('GAMEOFFICE', false);
define('FLAGS',['saLogin','personLogin']);
define('OFFICES_TEST_MODE',[1029]);

define('OFFICES_TEST_MODE_GAMES',['*']);



if(defined('ADMINR')) {
    Route::set('admin'.ADMINR, ADMINR.'(/<controller>(/<action>)(/<id>))')
        ->defaults(array(
            'controller' => 'index',
            'action'     => 'index',
            'directory'     => 'admin1',
        ));
}

Route::set('admin', 'enter(/<controller>(/<action>)(/<id>))')
	->defaults(array(
		'controller' => 'index',
		'action'     => 'index',
		'directory'     => 'admin1',
	));

if(defined('DEMO_MODE') && DEMO_MODE) {
    /*for local test*/
    Route::set('demo', 'dinit.php')
        ->defaults(array(
            'controller' => 'demo',
            'action'     => 'init',
            'directory'     => 'games',
        ));
    Route::set('ogames', 'games(/<nocontroller>(/<id>(/<action>)))')
        ->defaults(array(
            'controller' => 'demo',
            'action'     => 'game',
            'directory'     => 'games',
        ));
}
else {
    Route::set('games', 'games(/<controller>(/<id>(/<action>.php)))')
        ->defaults(array(
            'controller' => 'index',
            'action'     => 'game',
            'directory'     => 'games',
        ));

    Route::set('ogames', 'games(/<controller>(/<id>(/<action>)))')
        ->defaults(array(
            'controller' => 'index',
            'action'     => 'game',
            'directory'     => 'games',
        ));
}

Route::set('robots', 'robots.txt')
	->defaults(array(
		'controller' => 'robots',
		'action'     => 'index',
	));

Route::set('softswiss', 'apisw/v2/a8r_provider.<action>(/<id>)')
    ->defaults(array(
        'controller' => 'apisw',
        'action'     => 'unknown',
        'id'     => 'unknown',
        'apiver'     => '2',
    ));

Route::set('tvbet', 'apitvbet(<mode>)/v1/(games/<gameid>/)<action>',['mode'=>'(?:live)'])
    ->defaults(array(
        'controller' => 'apitvbet',
        'action'     => 'unknown',
        'mode'     => 'dev',
    ));

Route::set('pinup', 'apipinup(<zone>)(<mode>)/partner/<partnerid>/<action>(/<extraaction>)',['zone' => '(?:ua|preprodcom|com)','mode'=>'(?:live)','extraaction'=>'(?:cancel|freespin)'])
    ->defaults(array(
        'controller' => 'apipinup',
        'action'     => 'unknown',
        'extraaction'     => 'unknown',
        'zone'     => 'com',
        'mode'     => 'dev',
    ));
	
Route::set('pinco', 'apipinco(<zone>)(<mode>)/partner/<partnerid>/<action>(/<extraaction>)',['zone' => '(?:pinco)','mode'=>'(?:live)','extraaction'=>'(?:cancel|freespin)'])
    ->defaults(array(
        'controller' => 'apipinup',
        'action'     => 'unknown',
        'extraaction'     => 'unknown',
        'zone'     => 'com',
        'mode'     => 'dev',
    ));

Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'index',
		'action'     => 'index',
	));

Session::$default='cookie';
Cookie::$salt='MYMYMYMYMY112ldllasfa#';
Cookie::$expiration=60*60*24*365*15;
Cookie::$secure=true;

/***********/

define('PROJECT',1);


