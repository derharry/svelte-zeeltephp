<?php

     /**
      * changes/replaces full paths from string to 
      * removes full file-system paths to relative in $error (readability).
      */
     function zp_change_full_paths_to_zp_relative($stringToReplace) {
          // # get current system path from PATH_CPROOT   as defined by zp_set_running_environment() 
          $removeFullSystemPath = PATH_CPROOT;
          // # change paths \\ to /  
          //     always use / in paths, so str-replace(is i skip if-on-env-win) 
          $removeFullSystemPath = str_replace('\\',   '/', PATH_CPROOT); 
          $stringToReplace      = str_replace('\\\\', '/', $stringToReplace); 
          // # remove full path from String
          $stringToReplace = str_replace($removeFullSystemPath, '', $stringToReplace);
          // # shorty-fy zeeltephp related paths 
          $stringToReplace = str_replace('node_modules/dist/', '', $stringToReplace); // remove dev-env node_modules/
          $stringToReplace = str_replace('static/', '', $stringToReplace); // remove dev-env static/ 
          $stringToReplace = str_replace('/api/zeeltephp/', 'api/', $stringToReplace);
          $stringToReplace = str_replace('src/lib/zplib/', 'zplib/', $stringToReplace);
          $stringToReplace = str_replace('src/routes/', 'routes/', $stringToReplace);
          return $stringToReplace;
     }


     /**
      * deprecated - v1.0.2 loads env from zp-env.php
      * check all paths depending where ZP is running.
      */
     function zeeltephp_loadEnv() {
          $cfg = false;

          // presume being in build/production 
          // load .env from current build './zeeltephp/.env'
          if (file_exists(PATH_ZP_ENVfile)) {
               define('ZP_ENV', 'production'); // required by ZP_Router
               $cfg = parse_ini_file(PATH_ZP_ENVfile);
               $cfg['zp_env'] = 'build';
          } else {
               define('ZP_ENV', 'development'); // required by ZP_Router
               // presume dev mode

               //// check and load .env.dev
               // check paths for .env
               $cfgFile = '';
               if      (file_exists('../../.env.development')) $cfgFile = '../../.env.development';
               else if (file_exists('../../.env.dev'))         $cfgFile = '../../.env.dev';
               // load config
               if (file_exists($cfgFile)) {
                    $cfg = parse_ini_file($cfgFile);
                    $cfg['zp_env'] = 'loaded '.$cfgFile;
               } 
               if (!$cfg) {
                    // no cfg so - init the $env
                    $cfg = [];
                    $cfg['zp_env'] = '.env.dev auto generate';
                    //throw Error('could not find .env file');
               }
               //// check for missings defaults and auto generate

               // BASE / PUBLIC_BASE - same as in SvelteKit (to replace from route)
               // -- ah i already did, but with BASE -> should be PUBLIC_BASE!
               // -- if (!isset($cfg['BASE'])) $cfg['BASE'] = ''; 
               if (!isset($cfg['PUBLIC_BASE'])) $cfg['PUBLIC_BASE'] = ''; 

               // default DB provider
               if (!isset($cfg['ZEELTEPHP_DATABASE_URL'])) $cfg['ZEELTEPHP_DATABASE_URL'] = 'wordpress://../../../wordpress/wp-load.php';
               if (!isset($cfg['ZEELTEPHP_DATABASE_URL'])) $cfg['ZEELTEPHP_DATABASE_URL'] = 'mysql2://root@localhost/test';
          }
          return $cfg;
     }

     /**
      * read $lib/*
      * read ./lib/*
      */
     function zp_load_php_files($path, $phpfiles = null) {
          $path  = rtrim($path, '/') . '/';
          if ($phpfiles == null) {
               $phpfiles = zp_scandir($path);
          }
          foreach ($phpfiles as $file) {
               include_once($path . $file);
          }
     }


     /**
      * allow CORS (allow from anywhere *)
      * required in dev mode runs in different environments, svelte 5173, httpd 80/443.
      * in production this should not be needed, because same environment.
      */
     function zp_allow_cors() {
          // allow CORS (allow from anywhere *)
          header("Access-Control-Allow-Origin: *");
          header("Access-Control-Allow-Methods: *");
          header('Access-Control-Max-Age: 0'); // cache 0
     }


?>