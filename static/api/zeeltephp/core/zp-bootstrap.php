<?php

// Allow CORS (allow from anywhere *)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header('Access-Control-Max-Age: 0'); // cache 0

// Disable default PHP error display
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Set the header content-type
header('Content-Type: application/json');

// Create output buffer to capture fatal errors
ob_start();

// Convert warnings/notices to exceptions
set_error_handler(function($severity, $message, $file, $line) {
     throw new ErrorException($message, 0, $severity, $file, $line);
});

// Handle fatal errors
register_shutdown_function(function() {
     $error = error_get_last();
     if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
         echo json_encode([
             'ok' => false,
             'code' => 500,
             'message' => 'Critical error',
             'error' => [
                 'type' => 'Fatal Error',
                 'message' => $error['message'],
                 'file' => $error['file'],
                 'line' => $error['line']
             ],
             'cwd' => getcwd()
         ]);
     }
});

?>