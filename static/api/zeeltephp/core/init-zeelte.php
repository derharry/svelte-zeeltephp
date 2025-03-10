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
          require_once('zeeltephp/lib/sveltekit/class.zp.apirouter.php');
          require_once('zeeltephp/lib/request/json.response.php');
          require_once('zeeltephp/lib/request/headers.php');
          require_once('zeeltephp/lib/cfg/cfg.env.php');
          require_once('zeeltephp/lib/db/db.wordpress.php');
     //#endregion

     //#region set defaults

          global $db;
          global $env;
          global $zpAR;
          global $action;
          global $server;
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
               if ($environment == 'dev' && is_dir('../../src/lib/zplib/'))
                    zp_load_php_files( '../../src/lib/zplib/' );
               // load default /api/lib
               zp_load_php_files( 'lib' );
          }
          
     //#endregion

     

     function main_init_zeeltephp() {
          global $jsonResponse, $env, $db, $zpAR;
          try {
               
               // set JSON Response
               $jsonResponse = new JSONResponse();

               //echo "<h1>init-zeelte.php</h1>";
               //echo "<p>". getcwd() . "</p>\n";
               zp_setDefaults();

               // read config .env_file
               $env = zp_read_env_file();
               if (!$env) throw Error('could not find .env file');
               //var_dump($env);

               //
               //if (isset($env['PUBLIC_PathToWPload'])) {
               //     load_wordpress( $env["PUBLIC_PathToWPload"] );
               //}

               // fetch action details : route, params, ...
               $zpAR = new ZP_ApiRouter();

               // include the +page.server.php
               if (is_file($zpAR->routeFile)) {

                    // load lib files
                    zp_load_lib($zpAR->environment);

                    // set default DB-Provider
                    $db = new ZeeltePHP_DB_WordPress($env['ZEELTEPHP_DATABASE_URL']);

                    // include +page.sever.php
                    include($zpAR->routeFile);

          
                    if (is_null($zpAR->action)) {
                         if (function_exists('load')) load();
                    } else {
                         $action_function_name = 'action_'.$zpAR->action;
                         if (function_exists($action_function_name)) 
                              $action_function_name($zpAR->value);
                         else 
                              if (function_exists('actions')) 
                                   actions($zpAR->action, $zpAR->value);
                    }

               } else {
                    //$zpAR->Dump();
                    $jsonResponse->error = $zpAR->error;
                    error_log('ZeeltePHP: '. $jsonResponse->error);
               }
               $jsonResponse->message = $zpAR;
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