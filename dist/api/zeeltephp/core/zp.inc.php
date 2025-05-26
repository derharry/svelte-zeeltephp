<?php
/**
 * Environment Utilities for ZeeltePHP (paths, allow cors, ...)
 */  


     /**
      * Configures CORS headers for development environment (allow from anywhere *)
      */
     function zp_allow_cors(): void {
          header("Access-Control-Allow-Origin: *");
          header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
          header("Access-Control-Allow-Headers: Content-Type");
          header('Access-Control-Max-Age: 3600');
     }

     /**
      * Loads PHP library files from specified directory
      * 
      * @param string $path Directory path containing library files
      */
     function zp_load_lib_files(string $path): void {
          zp_log_debug("zp_load_lib_files($path)");
          $phpFiles = zp_scandir($path, '.php$'); //'/\.php$/');
          foreach ($phpFiles as $file) {
               if ($file !== '.' && $file !== '..') {
                    $fullPath = PATH_ZPLIB . $file;
                    if (is_file($fullPath)) {
                         include_once $fullPath;
                         zp_log_debug("  loaded lib: $file");
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
     function zp_change_full_paths_to_zp_relative(string $stringToReplace): string {
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
     function zeeltephp_loadEnv(): array {
          $cfg = [];
          zp_log_debug('zeeltephp_loadEnv()');
          try {
               // Production environment
               if (ZP_ENV === 'production' && file_exists('.env')) {
                    $cfg = parse_ini_file('.env');
                    zp_log_debug(' -- .env production loaded');
               } 
               // Development environment
               elseif (str_contains(ZP_ENV, 'development')) {
                    $cfg = zeeltephp_load_development_env();
               } else {
                    throw new RuntimeException('Unsupported environment: ' . ZP_ENV);
               }

               // Validate critical configuration
               zeeltephp_validate_env_config($cfg);

          } catch (Throwable $e) {
               zp_log_debug('Environment Error: ' . $e->getMessage());
               throw $e;
          }

          zp_log_debug('//zeeltephp_loadEnv()');
          return $cfg;
     }

     /**
      * Loads development environment configuration
      */
     function zeeltephp_load_development_env(): array {
          $cfgFiles = [
               PATH_CPROOT . '.env.development',
               PATH_CPROOT . '.env.dev',
               PATH_CPROOT . '.env'
          ];

          foreach ($cfgFiles as $file) {
               if (file_exists($file)) {
                    $cfg = parse_ini_file($file);
                    zp_log_debug("  -- loaded $file");
                    return $cfg;
               }
          }

          zp_log_debug("  -- No .env file found, using defaults");
          return [
               'BASE' => '',  // PUBLIC_BASE now just BASE (whitelisted)
               'ZEELTEPHP_DATABASE_URL' => ''
          ];
     }

     /**
      * Validates required environment configuration
      */
     function zeeltephp_validate_env_config(array &$cfg): void {
          // Set defaults for missing values
          $defaults = [
               'BASE' => ''  // PUBLIC_BASE now just BASE (whitelisted)
          ];

          foreach ($defaults as $key => $value) {
               if (!isset($cfg[$key])) {
                    $cfg[$key] = $value;
                    zp_log_debug("   ! $key = $value");
               }
          }
     }

?>