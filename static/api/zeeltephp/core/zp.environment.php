<?php

/**
 * Sets up ZeeltePHP runtime environment paths and configurations.
 * 
 * Prioritizes production environment if .env file exists,  
 * otherwise falls back to development configuration with CORS support
 */
function zeeltephp_loadRunEnvironment() {
     // Production Environment Setup
     if (is_file('.env')) {
          define('ZP_ENV',        'production');
          define('PATH_CPROOT',   str_replace('\\', '/', realpath(getcwd().'/../..')));
          define('PATH_ZPAPIPHP', PATH_CPROOT.'/api/');
          define('PATH_ZPLOG',    PATH_CPROOT.'/api/zeeltephp/php_log/');
          define('PATH_ZPLIB',    PATH_CPROOT.'/api/zeeltephp/lib_php/');
          define('PATH_ZPROUTES', PATH_CPROOT.'/api/zeeltephp/routes/');
     }
     // Development Environment Setup 
     else {
          zp_loadRunEnvironmentDev();
          zp_allow_cors(); // Enable cross-origin requests
     }
     // Debug Output
     if (ZP_DEBUG) {
          zp_log_debug('ZP_ENV:        '.ZP_ENV, true); // Reset log file
          zp_log_debug('PATH_INIT:     '.PATH_INIT); 
          zp_log_debug('PATH_CPROOT:   '.PATH_CPROOT);
          zp_log_debug('PATH_ZPAPIPHP: '.PATH_ZPAPIPHP);
          zp_log_debug('PATH_ZPLOG:    '.PATH_ZPLOG);
          zp_log_debug('PATH_ZPLIB:    '.PATH_ZPLIB);
          zp_log_debug('PATH_ZPROUTES: '.PATH_ZPROUTES);
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
function zp_loadRunEnvironmentDev() {
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

     // PHP Configuration
     ini_set('error_log', PATH_ZPLOG.'php_errors.log');
     ini_set('display_errors', 0);           // Uncomment for production-like behavior
     ini_set('display_startup_errors', 0);   // Uncomment for production-like behavior
} 

?>