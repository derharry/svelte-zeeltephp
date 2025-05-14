<?php

// # include zp init/boot required
require_once('zp-inc.php');
require_once('zp-environment.php');
require_once('zp-error.php');
require_once('class.zp.apirouter.php');


// # set current running environment
zp_loadRunEnvironment();


// # import zp core dependencies
require_once('zeeltephp/lib/cfg/cfg.env.php');
require_once('zeeltephp/lib/io/io.dir.php');
require_once('zeeltephp/lib/db/db.db.php');



// # set default response type to JSON
header('Content-Type: application/json');


// # allow cors
//   -- moved to zp-pathsEnv.php/dev-env.
//   -- from here in builds not required as by default export is for running on same httpd-environment


// # setup layer of default error handling

// create output buffer to capture fatal errors
ob_start();

// Convert warnings/notices to exceptions
set_error_handler(function($severity, $message, $file, $line) {
     throw new ErrorException($message, 0, $severity, $file, $line);
});

// handle fatal errors as overall-wrapper on ZP
register_shutdown_function(function() {
     $error = error_get_last();
     if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
          zp_handle_error($error, 'Fatal Error', 500);
     }
});


?>