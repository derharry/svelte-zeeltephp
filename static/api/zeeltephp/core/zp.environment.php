<?php namespace ZeeltePHP\Core;

use function ZeeltePHP\Core\lib\change_full_paths_to_zp_relative;
use function ZeeltePHP\Error\log_debug;

/**
 * Enable cross-origin requests for development environment (allow from anywhere/no-cors)
 */
function allow_cors() :void {
     header("Access-Control-Allow-Origin: *");
     header('Access-Control-Allow-Headers: X-ZPC-PAGE, X-ZPC-API, Content-Type');
     header("Access-Control-Allow-Headers: Content-Type");
     header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE, HEAD");
     header('Access-Control-Max-Age: 3600');
}

/**
 * Sets up ZeeltePHP runtime environment paths and configurations.
 * 
 * Prioritises production environment first if .env.build file exists,  
 * otherwise falls back to development configuration with CORS support
 */
function prepare_RunTimeEnvironment_context() :void {
     // Production Environment Setup
     if (is_file('.env.build')) {
          define('ZP_ENV',        'production');
          define('PATH_CPROOT',   str_replace('\\', '/', realpath(getcwd().'/../..')));
          define('PATH_ZPAPIPHP', PATH_CPROOT.'/api/');
          define('PATH_ZPLOG',    PATH_CPROOT.'/api/zeeltephp/php_log/');
          define('PATH_ZPLIB',    PATH_CPROOT.'/api/zeeltephp/lib_php/');
          define('PATH_ZPROUTES', PATH_CPROOT.'/api/zeeltephp/routes/');
          define('PATH_ZPTMP',    PATH_CPROOT.'/api/zeeltephp/tmp/');
     }
     // Development Environment Setup 
     else {
          prepare_RunTimeEnvironment_context_dev();
          allow_cors();
     }
     //
     if (ZP_DEBUG) \ZeeltePHP\Lib\IO\empty_dir(PATH_ZPLOG, true);
     #if (ZP_DEBUG) \empty_dir(PATH_ZPLOG, true);
     // re-create deleted dirs
     if (!is_dir(PATH_ZPLOG)) mkdir(PATH_ZPLOG, 0666, true);
     if (!is_dir(PATH_ZPTMP)) mkdir(PATH_ZPTMP, 0666, true);
     // Debug Output 
     if (ZP_DEBUG) {                          // true = restart log file
          log_debug('ZP_ENV:        '.ZP_ENV, 0, true); 
          log_debug('PATH_INIT:     '.PATH_INIT); 
          log_debug('PATH_CPROOT:   '.PATH_CPROOT);
          log_debug('PATH_ZPAPIPHP: '.PATH_ZPAPIPHP);
          log_debug('PATH_ZPLOG:    '.PATH_ZPLOG);
          log_debug('PATH_ZPLIB:    '.PATH_ZPLIB);
          log_debug('PATH_ZPROUTES: '.PATH_ZPROUTES);
          log_debug('PATH_ZPTMP:    '.PATH_ZPTMP);
     }
}

/**
 * Configures development environment paths and settings
 * 
 * Handles three scenarios:
 * 1. Installed package in node_modules
 * 2. Local development of ZeeltePHP itself
 * 3. Linked package development
 */
function prepare_RunTimeEnvironment_context_dev() {
     $envType = 'init';

     // Path Configuration
     $consumerRoot = str_replace('\\', '/', realpath(PATH_INIT.'/../..'));
     $projectName  = basename($consumerRoot);
     $apiPath      = "node_modules/zeeltephp/dist/api";

     // Environment Detection
     $isLibPackage = str_contains(PATH_INIT, '/static/api') && is_dir("$consumerRoot/$apiPath"); // default, consumer-project
     $isLinked     = false;                              // locally-linked ?; not implemented: might be not required anymore.
     $isMySelf     = $projectName == 'svelte-zeeltephp'; // self-development

     // Path Setup
     if ($isLibPackage) {
          // official lib-package, nothing todo
          $envType = 'development';
     }
     else if ($isMySelf) {
          $envType = 'self-development';
          $apiPath = 'static/api';
     }
     else if ($isLinked) {
          // probably not required anymore as /static/api is main
          // entry point and $isLibPackage does the path-detection. 
     }
     else {
          // Handle unsupported environments
          exit(json_encode([
               'ok'    => false,
               'error' => 'Unsupported development environment',
               'details' => [
                    'consumer_root' => $consumerRoot,
                    'project_name'  => $projectName
               ]
          ]));
     }

     // Define Constants
     define('ZP_ENV',         $envType);
     define('PATH_CPROOT',    $consumerRoot.'/');
     define('PATH_ZPAPIPHP', "$consumerRoot/$apiPath/");
     define('PATH_ZPLOG',    "$consumerRoot/php_log/");
     define('PATH_ZPLIB',    "$consumerRoot/src/lib_php/");
     define('PATH_ZPROUTES', "$consumerRoot/src/routes/");
     define('PATH_ZPTMP',    "$consumerRoot/$apiPath/zeeltephp/tmp/");

     // PHP Configuration
     ini_set('error_log', PATH_ZPLOG.'error.log');
     ini_set('display_errors', 0);           // Uncomment for production-like behavior
     ini_set('display_startup_errors', 0);   // Uncomment for production-like behavior
} 

?>