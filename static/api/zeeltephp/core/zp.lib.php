<?php namespace ZeeltePHP\Core\Lib;

use function ZeeltePHP\Error\log_debug;
use function ZeeltePHP\Lib\IO\scan_dir;


#
#  Environment Utilities for ZeeltePHP (paths, allow cors, ...)
#  

     /**
      * Loads PHP library files from specified directory
      * 
      * @param string $path Directory path containing library files
      */
     function load_lib_files(string $path): void {
          log_debug("load_lib_files($path)");
          $phpFiles = scan_dir($path, '#\.php$#'); //'/\.php$/');
          foreach ($phpFiles as $file) {
               if ($file !== '.' && $file !== '..') {
                    $fullPath = PATH_ZPLIB . $file;
                    if (is_file($fullPath)) {
                         include_once $fullPath;
                         log_debug("  loaded lib: $file");
                    }
               }
          }
     }

     /**
      * Sanitizes full system paths in strings to relative paths for readability.
      * 
      * @param string $stringToReplace The string containing paths to sanitize
      * @return string Sanitized string with relative paths
      */
     function change_full_paths_to_zp_relative(string $stringToReplace): string {
          // Normalize path separators, always use /
          $removeFullSystemPath = str_replace('\\',   '/', PATH_CPROOT);
          $stringToReplace      = str_replace('\\\\', '/', $stringToReplace);
          
          // Remove absolute paths
          $stringToReplace = str_replace($removeFullSystemPath, '', $stringToReplace);
          
          // Simplify common ZeeltePHP paths
          $replacements = [
               'node_modules/dist/' => '',
               'static/'            => '/',
               '/api/zeeltephp/'    => '/api/',
               'src/lib/php_log/'   => '/php_log/',
               'src/routes/'        => '/routes/'
          ];
          
          return str_replace(array_keys($replacements), array_values($replacements), $stringToReplace);
     }   
  
     


     /**
      * Loads and validates environment variables for ZeeltePHP
      * 
      * @return array Parsed environment configuration
      * @throws RuntimeException If environment configuration is invalid
      */
     function load_DotEnv_file(): array {
          $cfg = [];
          log_debug('load_DotEnv_file()');
          try {
               // Production environment
               if (ZP_ENV === 'production' && file_exists('.env')) {
                    $cfg = parse_ini_file('.env');
                    log_debug(' -- .env production loaded');
               } 
               // Development environment
               elseif (str_contains(ZP_ENV, 'development')) {
                    $cfg = scan_dir_recursive_upload_development_env();
               } else {
                    throw new \RuntimeException('Unsupported environment: ' . ZP_ENV);
               }

               // Validate critical configuration
               scan_dir_recursive_upvalidate_env_config($cfg);

          } catch (\Throwable $e) {
               log_debug('Environment Error: ' . $e->getMessage());
               throw $e;
          }

          log_debug('//load_DotEnv_file()');
          return $cfg;
     }

     /**
      * Loads development environment configuration
      */
     function scan_dir_recursive_upload_development_env(): array {
          $cfgFiles = [
               PATH_CPROOT . '.env.development',
               PATH_CPROOT . '.env.dev',
               PATH_CPROOT . '.env'
          ];

          foreach ($cfgFiles as $file) {
               if (file_exists($file)) {
                    $cfg = parse_ini_file($file);
                    log_debug("  -- loaded $file");
                    return $cfg;
               }
          }

          log_debug("  -- No .env file found, using defaults");
          return [
               'BASE' => '',  // PUBLIC_BASE now just BASE (whitelisted)
               'ZEELTEPHP_DATABASE_URL' => ''
          ];
     }

     /**
      * Validates required environment configuration
      */
     function scan_dir_recursive_upvalidate_env_config(array &$cfg): void {
          // Set defaults for missing values
          $defaults = [
               'BASE' => ''  // PUBLIC_BASE now just BASE (whitelisted)
          ];

          foreach ($defaults as $key => $value) {
               if (!isset($cfg[$key])) {
                    $cfg[$key] = $value;
                    log_debug("   ! $key = $value");
               }
          }
     }

?>