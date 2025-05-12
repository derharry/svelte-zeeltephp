<?php
     require_once('zeeltephp/lib/io/io.dir.php');

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
function zp_load_lib($environment) {
     global $path_ZP_LIB;
     // if dev-env load all php files from /src/lib/zplib 
     zp_load_php_files(PATH_ZPLIB);
     /*
     //var_dump($environment);
     if (is_dir('./lib')) {
          echo 'ewflkwöflkweölfwekföl';
          zp_load_php_files('./lib');
     } else {
          //echo '3242423432423';
          zp_load_php_files( '../../src/lib/zplib/' );
     }
     //if ($environment == 'dev' && is_dir('../../src/lib/zplib/'))
     //     zp_load_php_files( '../../src/lib/zplib/' );
     // load default /api/lib
     //zp_load_php_files( './lib' );
     */
}

     
     function zp_load_php_files($path, $phpfiles = null) {
          $path  = rtrim($path, '/') . '/';
          if ($phpfiles == null) {
               $phpfiles = zp_scandir($path);
          }
          foreach ($phpfiles as $file) {
               include_once($path . $file);
          }
     }





?>