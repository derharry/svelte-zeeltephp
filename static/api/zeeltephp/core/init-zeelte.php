<?php 
#
# init-zeelte
#    
#
#

     //#region import
          # minimum-requirments instead boot.php
          require_once('zeeltephp/lib/cfg/cfg.env.php');
          require_once('zeeltephp/lib/io/io.dir.php');
          require_once('zeeltephp/lib/helper/load.php.files.php');
          require_once('zeeltephp/lib/request/json.response.php');
          require_once('zeeltephp/lib/request/headers.php');
          require_once('zeeltephp/lib/sveltekit/class.zp.apirouter.php');
          require_once('zeeltephp/lib/db/db.wordpress.php');
     //#endregion

     //#region set defaults

          global $db;
          global $env;
          global $zpAR;
          global $jsonResponse;

          $jsonResponse = new JSONResponse();

          function zp_setDefaults() {
               global $jsonResponse;

               // set default JSON Response
               $jsonResponse = new JSONResponse();

               // set headers to allow from anywhere
               // set headers to default json-response
               set_request_headers_to_allow_from_anywhere();
               set_request_headers_to_json_api();
          }
  
     //#endregion

     //#region methods

          function zp_load_lib($environment) {
               // if dev-env load all php files from /src/lib/zplib 
               //var_dump($environment);
               if ($environment == 'dev' && is_dir('../../src/lib/zplib/'))
                    zp_load_php_files( '../../src/lib/zplib/' );
               // load default /api/lib
               zp_load_php_files( './lib' );
          }

          function zp_main_pageserverphp() {          
               global $zpAR;

               try {
                    // include +page.sever.php
                    include($zpAR->routeFile);

                    // response from load or actions
                    $action_response = false;

                    
                    // if no action then just call load()
                    if (is_null($zpAR->action)) {
                         if (function_exists('load'))
                              $action_response = load();
                    } else {
                         // support custom function action_ACTION($value)
                         // instead of e.g. actions() switch ($action) case 'ACTION'
                         $action_function_name = 'action_'.str_replace('?/', '', $zpAR->action);
                         
                         // does custom function action_ACTION exist ? 
                         // else load default actions($action, $value, $data)
                         if (function_exists($action_function_name))
                              $action_response = $action_function_name($zpAR->value);
                         else
                              if (function_exists('actions')) 
                                   $action_response = actions($zpAR->action, $zpAR->value); //, $zpAR->data);

                         
                         // leave a message or error
                         if (!$action_response)     
                              return [
                                   'error' => 'No ?/action or response'
                              ];
                    }
                    return $action_response;
               } catch (Exception $exp) {

               }
          }
          
     //#endregion

     

     function main_init_zeeltephp() {
          global $jsonResponse, $env, $db, $zpAR;
          try {

               
               //echo "<h1>init-zeelte.php</h1>";
               //echo "<p>". getcwd() . "</p>\n";
               zp_setDefaults();

               // read config .env_file
               $env = zp_read_env_file();
               if (!$env) throw Error('could not find .env file');
               
               // fetch action details : route, params, ...
               $zpAR = new ZP_ApiRouter();
               // include the +page.server.php
               if (!is_file($zpAR->routeFile)) {
                    throw Error('Does routeFile exist? '.$zpAR->routeFile);
               }
               else {

                    // load lib files
                    zp_load_lib($zpAR->environment);

                    // set default DB-Provider
                    $db = new ZeeltePHP_DB_WordPress($env['ZEELTEPHP_DATABASE_URL']);

                    // for future roadmap - support ..routes../
                    //  +hooks.php  +server.php  ...
                    
                    // lets to the actions :-) 
                    $response = false;
                    $response = zp_main_pageserverphp();          

                    // if we have a response [] then parse response into $jsonResponse
                    if (is_array($response)) {
                         foreach ($response as $k=>$v) {
                              $jsonResponse->$k = $v;
                         }
                    }
               }
              

               if (is_null($jsonResponse->data)) {
                    $jsonResponse->ok = true;
                    $jsonResponse->data = 'hi';
                    $jsonResponse->message = 'fallback data / response';
               }
          } 
          catch (Error $error) {
               $jsonResponse->error = $error;
          }
          catch (Throable $throw) {
               $jsonResponse->error = $throw;
          }
          catch (Exception $exp) {
               $jsonResponse->error = $exp;
               error_log($exp);
          }
          finally {
               $jsonResponse->send();
          }
     }



     ## execute
     main_init_zeeltephp();





?>