<?php namespace ZeeltePHP\Core;

use function ZeeltePHP\Error\log_debug;
use function ZeeltePHP\Error\handle_error;
use function ZeeltePHP\Core\prepare_RunTimeEnvironment_context;
use ZeeltePHP\Lib\Time\StopWatch;

#  ZeeltePHP – init
#    - prepare environment
#      - set defaults (response-header -code, PATH_ vars, out)
#      - set working directory
#      - import core depedencies
#      - set default error_handler
#    - start ZeeltePHP

// Enable ZeeltePHP debugging
define('ZP_DEBUG',   true);

// Default response type is JSON
header('Content-Type: application/json');

// Current CWD to return later (if needed)
define('PATH_INIT', str_replace('\\', '/', getcwd()));
chdir(__DIR__); // Set /api/zeeltephp as working directory

// import core dependencies
require_once('lib/time/class.stopwatch.php');
require_once('lib/io/io.dir.php');
require_once('core/zp.lib.php');
require_once('core/zp.error.php');
require_once('core/zp.environment.php');
require_once('core/class.zp.apirouter.php');
require_once('core/main.php');

$zpTime = new StopWatch();
$zpTime->start('init()');

// Set default error handling
// Start output buffering to capture fatal errors
ob_start();
// Convert warnings/notices to exceptions
set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});
// Handle fatal errors globally
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
        handle_error($error, 'Fatal Error', 500);
    }
});

// Polyfill missing getallheaders() when .env.ZEELTEPHP_EXE
if (!function_exists('getallheaders')) {
     function getallheaders() {
          $headers = [];
          foreach ($_SERVER as $name => $value) {
               #if (is_string($value)) log_debug("$name = $value");
               if (substr($name, 0, 5) == 'HTTP_') {
                    // Convert HTTP_HEADER_NAME to Header-Name
                    $headerName = str_replace(' ', '-', str_replace('_', ' ', substr($name, 5)));
                    $headers[$headerName] = $value;
          }}
          return $headers;
     }
}

// Prepare runtime context prod/dev
prepare_RunTimeEnvironment_context();

// Autoload $lib files
spl_autoload_register(function ($class_name) {
    if (str_starts_with($class_name, 'Zeelte\\')) {
        $class_file = str_replace('Zeelte\\', '', $class_name);
        $class_file = str_replace('Lib\\',    '', $class_name);
        $class_file = str_replace('_',       '.', $class_name);
        $file = PATH_ZPROOT . '/lib/' . str_replace('\\', '/', $class_file) . '.php';
    } else {
        $file = PATH_ZPLIB . '/' . str_replace('\\', '/', $class_name) . '.php';
    }
    if (file_exists($file)) {
        require_once $file;
    }
});

\ZeeltePHP\Core\main();

log_debug($zpTime->endN('init()'));
log_debug(date('Y-m-d H:i:s'));

?>
