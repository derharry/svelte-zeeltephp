<?php 
#
# init-zeelte
#    
# return JSON
#


     //#region import
          # minimum-requirments instead boot.php
          require_once('zeeltephp/core/class.zp.apirouter.php');
          require_once('zeeltephp/lib/cfg/cfg.env.php');
          require_once('zeeltephp/lib/io/io.dir.php');
          require_once('zeeltephp/lib/helper/zp.log.error.handler.php');
          require_once('zeeltephp/lib/helper/load.php.files.php');
          require_once('zeeltephp/lib/request/json.response.php');
          require_once('zeeltephp/lib/request/headers.php');
          require_once('zeeltephp/lib/db/db.db.php');
          //require_once('zeeltephp/lib/db/db.wordpress.php');
     //#endregion

     //#region set defaults

          $debug = true;
          global $debug;
          global $db;
          global $env;
          global $zpAR;
          global $jsonResponse;

          $jsonResponse = new JSONResponse();

          function zp_setDefaults() {
               global $jsonResponse;

               // set default JSON Response
               //header('Content-Type: application/json');
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
                    zp_log_debug('zp_main_pageserverphp()');

                    // include +page.server.php
                    include($zpAR->routeFile);
                    zp_log_debug('  >> loaded +page.server.php');

                    // response from load or actions
                    // we use states :string for internal knowing what we are doing / where we are.
                    $action_response = '__ZP-INIT__';
                    
                    // if no zpAR->action defined -> its basic GET load()
                    // if no action then just call load()
                    if (is_null($zpAR->action)) {
                         // no action 
                         // now - when load() is defined in +page.server.php - execute it.
                         if (function_exists('load')) {
                              $action_response = '__ZP-LOAD__';   // at least set true because found
                              zp_log_debug('  >> '.$action_response);
                              $action_response = load();
                         }
                    }
                    else if (!is_null($zpAR->action)) {
                         $action_response = '__ZP-ACTIONS?__';   // at least set true because found
                         // support custom function action_ACTION($value)
                         // instead of e.g. actions() switch ($action) case 'ACTION'
                         $action_name_stripped = str_replace('?/', '', $zpAR->action);
                         $action_function_name = 'action_'.$action_name_stripped;
                         if (function_exists($action_function_name)) {
                              // exec action_ACTION
                              $action_response = '__ZP-ACTION_-'.$action_function_name.'__';
                              zp_log_debug('  >> '.$action_response);
                              $action_response = $action_function_name($zpAR->value);
                         }
                         else if (function_exists('actions')) {
                              // exec actions($action, $value, $data)
                              $action_response = '__ZP-ACTIONS__';
                              zp_log_debug('  >> '.$action_response);
                              $action_response = actions($action_name_stripped, $zpAR->value, $zpAR->data);
                         }
                    }

                    //return 'No response of load() or action(?/action) :'.$action_response;
                    $msg = 'valid response from +page.server.php';
                    if (is_string($action_response) && str_starts_with($action_response, '__ZP')) {
                         $msg = null;
                         if      ($action_response == '__ZP-ACTIONS?__')               $msg = "no action(s) found for ".$zpAR->action; 
                         else if (str_starts_with('__ZP-ACTION_-', $action_response))  $msg = "no response of ".$action_function_name;
                         else if (str_starts_with('__ZP-ACTIONS_-', $action_response)) $msg = "no response of actions()";
                         else if ($action_response == '__ZP-LOAD__')                 $msg = "no response of load()";
                         else if ($action_response == '__ZP-INIT__')                 $msg = "no load() or actions() found.";
                         else $msg = $action_response;
                         $action_response = $msg;
                    }
                    zp_log_debug('  << '.$msg);
                    zp_log_debug('//zp_main_pageserverphp()');
                    return $action_response;
               } catch (Exception $exp) {
                    zp_log_debug('//zp_main_pageserverphp() !! '.$exp->getMessage());
                    zp_error_handler($exp);
                    return $exp->getMessage();
               }
          }
          
     //#endregion

     

     function main_init_zeeltephp() {
          global $jsonResponse, $env, $db, $zpAR;
          try {

               zp_log_debug('main() ');
               //echo "<h1>init-zeelte.php</h1>";
               //echo "<p>". getcwd() . "</p>\n";
               zp_setDefaults();

               // read config .env_file
               $env = zp_read_env_file();
               if (!$env) throw Error('could not find .env file');
               
               // fetch action details : route, params, ...
               $zpAR = new ZP_ApiRouter();
               // include the +page.server.php
               if (!$zpAR->routeFileExist) {
                    $jsonResponse->data = $zpAR;
                    throw new Error('No routeFile found, expected: '.$zpAR->routeFile);
               }
               else {

                    // load lib files
                    zp_load_lib($zpAR->environment);

                    // init ZP_DB - database provider
                    $db = new ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
                    //$db = new ZeeltePHP_DB_WordPress($env['ZEELTEPHP_DATABASE_URL']);

                    // for future roadmap - support ..routes../
                    //  +hooks.php  +server.php  ...
                    
                    // lets to the actions :-) 
                    $response = false;
                    $response = zp_main_pageserverphp();

                    // $response is the jsonResponse->data!
                    $jsonResponse->data = $response;

                    // turned off - 2025-03-18 let the $jsonResponse as ZP_JsonResponse() response for zp_fetch_api
                    // if we have a response [] then parse response into $jsonResponse
                    //if (is_array($response)) {
                    //     foreach ($response as $k=>$v) {
                    //          $jsonResponse->$k = $v;
                    //     }
                    //}

                    // on ZeeltePHP side now everythin is ok
                    $jsonResponse->ok = true;
               }
              
               if ($jsonResponse->data === null) {
                    // turned off - 2025-03-18 - maybe null is what we what to return ;-) debugging not needed
                    //$jsonResponse->message = 'fallback data / response';
               } 

               zp_log_debug('//main() ');

          } 
          catch (Error $error) {
               zp_log_debug('//main() ** ERROR ');
               $jsonResponse->error = $error->getMessage();
               zp_error_handler($error);
          }
          catch (Throwable $throw) {
               zp_log_debug('//main() ** ERROR ');
               $jsonResponse->error = 'ZP_Throwable: '.$throw->getMessage();
               zp_error_handler($throw);
          }
          catch (Exception $exp) {
               zp_log_debug('//main() ** ERROR ');
               $jsonResponse->error = 'ZP_Exception: '.$exp->getMessage().' '.$error->getCode();
               zp_error_handler($exp);
          }
          finally {
               $jsonResponse->send();
          }
     }



     ## execute
     main_init_zeeltephp();





?>