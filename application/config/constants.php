<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */

define('SESS_PROTOCOL',              '');
define('SITE_LOGO_PATH',        APPPATH.'/assets/images/logo.png'); // Site logo
// Farmstaff Registry constants
define('SITE_NAME',         'Farm Staff Registry');
define('SITE_TAGLINE',      'Trust. Transparency. Better Farms.');
define('SITE_EMAIL',        'info@farmstaff.ng');
define('SITE_PHONE',        '+234 800 FARMSTAFF');
define('OSACA_NAME',        'Ondo State Farm Staff Registry');
define('UPLOAD_WORKERS',    'uploads/workers/');
define('UPLOAD_INCIDENTS',  'uploads/incidents/');
define('UPLOAD_EMPLOYERS',  'uploads/employers/');
define('WORKER_ID_PREFIX',  'FSR');
define('TRUST_SCORE_MAX',   100);
define('TRUST_SCORE_START', 50);