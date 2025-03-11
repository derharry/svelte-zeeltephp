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
               //set_request_headers_to_allow_from_anywhere();
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

               
               // fetch action details : route, params, ...
               $zpAR = new ZP_ApiRouter();
               $jsonResponse->message = $zpAR;
               $jsonResponse->data    = $zpAR->data;

               // include the +page.server.php
               if (is_file($zpAR->routeFile)) {

                    // load lib files
                    zp_load_lib($zpAR->environment);

                    // set default DB-Provider
                    $db = new ZeeltePHP_DB_WordPress($env['ZEELTEPHP_DATABASE_URL']);

                    // include +page.sever.php
                    include($zpAR->routeFile);

     
                    // if no action then just call load()
                    if (is_null($zpAR->action)) {
                         if (function_exists('load')) load();
                    } else {
                         // does action_ACTION exist ? do so else take actions()
                         $action_function_name = 'action_'.$zpAR->action;
                         $action_response = false;
                         if (function_exists($action_function_name))
                              $action_response = $action_function_name($zpAR->value);
                         else 
                              if (function_exists('actions')) 
                                   $action_response = actions($zpAR->action, $zpAR->value);

                         // leave a message or error
                         if (!$action_response) 
                              $jsonResponse->error = "No ?/action or response";
                         if (is_array($action_response)) {
                              foreach ($action_response as $k=>$v) {
                                   $jsonResponse->$k = $v;
                              }
                         }
                    }

               } else {
                    //$zpAR->Dump();
                    $jsonResponse->error = $zpAR->error;
                    //error_log('ZeeltePHP: '. $jsonResponse->error);
               }
               //$jsonResponse->message = $zpAR;

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