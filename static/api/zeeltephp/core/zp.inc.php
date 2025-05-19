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
      * check all paths depending where ZP is running.
      */
     function zeeltephp_loadEnv() {
          $cfg = false;
          zp_log_debug('zeeltephp_loadEnv()');
          if (ZP_ENV == 'production' && file_exists('.env')) {
               // presume being in build/production 
               // load .env from current build './zeeltephp/.env'
               // no auto generation needed.
               $cfg = parse_ini_file('.env');
               $cfg['zp_info'] = 'loaded .env-production';
               zp_log_debug(' -- .env production loaded');
          } 
          else if (str_contains(ZP_ENV, 'development')) {
               // presume dev mode
               // CP_ROOT must be set to consumer-project-root 
               // 1.) try to load CP_ROOT/.env.development or .env.dev
               // 2.) load missing variables

               // 1...
               $cfgFile = '';
               if      (file_exists(PATH_CPROOT.'.env.development')) $cfgFile = PATH_CPROOT.'.env.development';
               else if (file_exists(PATH_CPROOT.'.env.dev'))         $cfgFile = PATH_CPROOT.'.env.dev';
               else if (file_exists(PATH_CPROOT.'.env'))             $cfgFile = PATH_CPROOT.'.env';
               if (file_exists($cfgFile)) {
                    $cfg = parse_ini_file($cfgFile);
                    //$cfg['zp_info'] = 'loaded '.str_replace(PATH_CPROOT, '', $cfgFile);
                    zp_log_debug("  -- loaded ".$cfgFile);
                    foreach ($cfg as $k=>$v)
                         zp_log_debug("     $k = $v");
               } 

               // 2...
               if (!$cfg) {
                    // no cfg so - init the $env
                    $cfg = [];
                    zp_log_debug("  -- missing .env.dev or .env.development ");
               }
               // BASE / PUBLIC_BASE - same as in SvelteKit (to replace from routes)
               if (!isset($cfg['BASE'])) {
                    // BASE -> should be PUBLIC_BASE ! (auto exported by VITE, export at @zeeltephp/vite-plugin/postbuild)
                    // probably not used yet, but in future I want to switch just to BASE (same naming as in SvelteKit)
                    // $cfg['BASE']  = '';
                    // zp_log_debug(' -- ! ', $cfg['BASE']);
               }        
               if (!isset($cfg['PUBLIC_BASE'])) {
                    $cfg['PUBLIC_BASE'] = '';
                    zp_log_debug('   ! PUBLIC_BASE =', $cfg['PUBLIC_BASE']);
               }
               // default DB provider
               if (!isset($cfg['ZEELTEPHP_DATABASE_URL'])) {
                    // to use ZP_DB -> consumer must set a .env file to connect to its DB! - no autoload anymore
                    // -- if (!isset($cfg['ZEELTEPHP_DATABASE_URL'])) $cfg['ZEELTEPHP_DATABASE_URL'] = 'wordpress://../../../wordpress/wp-load.php';
                    // -- if (!isset($cfg['ZEELTEPHP_DATABASE_URL'])) $cfg['ZEELTEPHP_DATABASE_URL'] = 'mysql2://root@localhost/test';
                    $cfg['ZEELTEPHP_DATABASE_URL'] = ''; // we set it empty placeholder. ZP_DB() will not be autoloaded.
                    zp_log_debug('   ! ZEELTEPHP_DATABASE_URL = '.$cfg['ZEELTEPHP_DATABASE_URL']);
               }
          }
          else {
               // abort when no .env-file and in production or unsupported environment
               throw new Error('zeeltephp_loadEnv() unsuported environment');
          }
          zp_log_debug('//zeeltephp_loadEnv()');
          return $cfg;
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

     /**
      * read $lib/*
      * read ./lib/*
      */
     function zp_load_lib_files($path) {
          zp_log_debug("zp_load_lib_files( $path )");
          $phpfiles = zp_scandir($path, '.*\.php$');
          foreach ($phpfiles as $file) {
               if ($file != '.' && $file != '..') {
               zp_log_debug('  loaded lib: '.$file);
               include_once(PATH_ZPLIB.$file);
          }}
     }
     
?>