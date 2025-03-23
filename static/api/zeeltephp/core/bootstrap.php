<?php
// 1. Enable output buffering to capture errors
ob_start();

// 2. Set custom error handler
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// 3. Handle fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Fatal PHP error',
            'details' => [
                'type' => $error['type'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line']
            ]
        ]);
        ob_end_clean();
        exit;
    }
});

// 4. Set exception handler
set_exception_handler(function ($e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Uncaught exception',
        'details' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace()
        ]
    ]);
    ob_end_clean();
    exit;
});

// 5. Set JSON headers early
header('Content-Type: application/json');

// Your actual PHP code follows...
// Example syntax error for testing:
if if ($test) {} // This will trigger parse error
?>
