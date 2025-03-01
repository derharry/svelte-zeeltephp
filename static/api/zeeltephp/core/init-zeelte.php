<?php 
#
# init-zeelte
#    
#
#

     //#region import
          # minimum-requirments as for boot.php
          require_once('zeeltephp/lib/io/io.dir.php');
          require_once('zeeltephp/lib/helper/load.php.files.php');
          require_once('zeeltephp/lib/sveltekit/actionDetails.php');
          require_once('zeeltephp/lib/sveltekit/serverDetails.php');
          require_once('zeeltephp/lib/request/json.response.php');
          require_once('zeeltephp/lib/request/headers.php');
          require_once('zeeltephp/lib/cfg/cfg.env.php');
          require_once('zeeltephp/lib/db/db.wordpress.php');
     //#endregion

     //#region set defaults

          global $db;
          global $cfg;
          global $jsonResponse;

          function zp_setDefaults() {
               global $jsonResponse;
               // default json-response
               set_request_headers_to_json_api();
          }
  
     //#endregion

     //#region methods

          function zp_load_lib($environment) {
               // if dev-env load the /src/lib/zplib files
               //var_dump($environment);
               if (str_starts_with($environment, 'dev-') && is_dir('../../src/lib/zplib/'))
                    zp_load_php_files( '../../src/lib/zplib/' );
               // load default /api/lib
               zp_load_php_files( 'lib' );
          }
          
     //#endregion

     

     function main_init_zeeltephp() {
          global $jsonResponse, $cfg, $db;
          try {
               
               // set JSON Response
               $jsonResponse = new JSONResponse();

               //echo "<h1>init-zeelte.php</h1>";
               //echo "<p>". getcwd() . "</p>\n";
               zp_setDefaults();

               // read config .env_file
               $cfg = zp_read_env_file();
               if (!$cfg) throw Error('could not find .env file');
               //var_dump($cfg);

               //
               //if (isset($cfg['PUBLIC_PathToWPload'])) {
               //     load_wordpress( $cfg["PUBLIC_PathToWPload"] );
               //}

               // fetch action details : route, params, ...
               $action  = new ZP_ActionDetails();
               $server  = new ZP_ServerDetails();
               
               // include the +page.server.php file
               if (!is_null($server->error)) {
                    $jsonResponse->error = $server->error;
                    error_log('ZeeltePHP: '. $jsonResponse->error);
               }
               else {

                    // set default DB-Provider
                    $db = new ZeeltePHP_DB_WordPress($cfg['ZEELTEPHP_DATABASE_URL']);

                    // load lib files
                    zp_load_lib($server->environment);

                    // include the +page.server.php
                    include($server->routeFile);

                    //var_dump($action);
                    if (is_null($action->action)) {
                         if (function_exists('load')) load();
                    } else {
                         if (function_exists('actions')) actions();
                    }

                    // load or post/get/etc...
               }
          } 
          catch (Exception $th) {
               error_log($th);
               $jsonResponse->exception = $th;
          }
          finally {
               $jsonResponse->send();
          }
     }



     ## execute
     main_init_zeeltephp();





?>