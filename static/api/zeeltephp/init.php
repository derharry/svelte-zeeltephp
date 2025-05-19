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
require_once('lib/io/io.dir.php');

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
define('ZP_DEBUG', false);

// Set up the current running environment
zeeltephp_loadRunEnvironment();

// All setup complete – start ZeeltePHP
zeeltephp_main();

?>
