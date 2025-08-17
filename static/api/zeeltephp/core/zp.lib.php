<?php namespace ZeeltePHP\Core\Lib;

use function ZeeltePHP\Error\log_debug;
use function ZeeltePHP\Lib\IO\scan_dir;
use ZeeltePHP\Core\zpTime;  

     /**
      * Load /src/lib/lib_php/
      * 
      * @param string $path Directory path containing library files
      */
     function load_php_lib(string $path): void {
          global $zpTime;
          $zpTime->start('load_php_lib/()');
          log_debug("load_php_lib($path)");
          $zpTime->start('load_php_lib()');
          $phpFiles = scan_dir($path, '#\.php$#'); # '/\.php$/');
          $zpTime->start('load_php_lib()');
          foreach ($phpFiles as $file) {
               if ($file !== '.' && $file !== '..') {
                    $fullPath = PATH_ZPLIB . $file;
                    if (is_file($fullPath)) {
                         include_once $fullPath;
                         log_debug("  loaded lib: $file");
                    }
               }
          }
          log_debug($zpTime->endN('load_php_lib()'));
          log_debug($zpTime->endN('load_php_lib/()'));
     }

     /**
      * Sanitizes full system paths to relative paths.
      * 
      * @param  string|array|object $data
      * @return string|array|object $data
      */
     function change_full_paths_to_zp_relative(mixed $data): mixed {
          if (is_array($data)) {
               return array_map(fn($val) => change_full_paths_to_zp_relative($val), $data );
          }
          elseif (is_object($data)) {
               foreach ($data as $key => $value) {
                    $data->$key = change_full_paths_to_zp_relative($value);
               }
               return $data; // <-- FIXED: make sure mutated object is returned!
          }
          elseif (is_string($data)) {
               $removeFullSystemPath = str_replace('\\',   '/', PATH_CPROOT);
               $data = str_replace('\\',   '/', $data);
               $data = str_replace($removeFullSystemPath, '', $data);
               $replacements = [ // Simplify common ZeeltePHP paths
                    'node_modules/dist/' => '',
                    'static/'            => '/',
                    '/api/zeeltephp/'    => '/api/',
                    'src/lib/php_log/'   => '/php_log/',
                    'src/routes/'        => '/routes/'
               ];
               $data = str_replace(array_keys($replacements), array_values($replacements), $data);
          }
          //error_log(gettype($data)."$data\n", 3, PATH_ZPLOG.'error.log');
          return $data;
     }
  
  
     /**
      * Loads and validates $env variables for ZeeltePHP.
      * Equivalent to vite-plugin/load_DotEnv_file
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
                    $cfg = load_DotEnv_dev();
               } else {
                    throw new \RuntimeException('Unsupported environment: ' . ZP_ENV);
               }
               load_env_mimimum_vars($cfg);
          } catch (\Throwable $e) {
               log_debug('Environment Error: ' . $e->getMessage());
               throw $e;
          }
          log_debug('//load_DotEnv_file()');
          return $cfg;
     }

     /**
      * Load .env-file or $env
      */
     function load_DotEnv_dev(): array {
          $cfgFiles = [
               PATH_CPROOT . '.env.development',
               PATH_CPROOT . '.env.dev',
               PATH_CPROOT . '.env',
               PATH_CPROOT . '.local'
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
               'BASE' => '',  // todo: PUBLIC_BASE now just BASE (whitelisted)
               'ZEELTEPHP_DATABASE_URL' => ''
          ];
     }

     /**
      * Validates required environment configuration
      */
     function load_env_mimimum_vars(array &$cfg): void {
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