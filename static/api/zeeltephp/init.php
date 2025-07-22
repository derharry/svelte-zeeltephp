<?php
#
#  ZeeltePHP – Initialization Loader for /api/
#
#  This script bootstraps the ZeeltePHP environment.
#  - Sets up the working directory
#  - Loads core dependencies
#  - Configures error handling
#  - Starts the main ZeeltePHP application
#

// preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
    header('Access-Control-Allow-Headers: X-ZPC-Api, Content-Type');
    exit(0);
}

// Set default response type to JSON
header('Content-Type: application/json');

// Remember the original working directory (for returning later if needed)
define('PATH_INIT', str_replace('\\', '/', getcwd()));
chdir(__DIR__); // Set ZeeltePHP working directory

// Include core and dependencies
require_once('core/class.zp.apirouter.php');
require_once('core/main.php');
require_once('core/zp.environment.php');
require_once('core/zp.error.php');
require_once('core/zp.inc.php');
require_once('lib/time/class.timediff.php');
require_once('lib/io/io.dir.php');

$zpTime = new ZP_TimeDiff();
$zpTime->start('init()');

//// Set up default error handling
// Start output buffering to capture fatal errors
ob_start();
// Convert warnings/notices to exceptions
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
// Handle fatal errors globally
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
        zp_handle_error($error, 'Fatal Error', 500);
    }
});

// Enable ZeeltePHP debugging (set to false in production)
define('ZP_DEBUG', true);

// #####################################
// ## main()
// #####################################
// zp_log_debug() restart happens at zeeltephp_loadEnvironmnet() because const PATH_* are required first

// Set up the current running environment
zeeltephp_loadRunEnvironment();

// All setup complete – start ZeeltePHP
zeeltephp_main();

zp_log_debug($zpTime->endN('init()'));
zp_log_debug(date('Y-m-d H:i:s'));


?>
