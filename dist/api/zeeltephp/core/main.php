<?php

/***
 * Start ZeeltePHP.
 * Start main() after zeeltephp_loadRunEnvironment() has set the environment paths (consts).
 */
function zeeltephp_main() {
     global $debug, $zpAR, $env, $db, $data;
     try {
          zp_log_debug('zeeltephp_main()');

          // hello at ZeeltePHP
          // I should be ready-to-run in my environment now

          // load the .env-file or auto generate missing variables 
          //   equivalent to vite-plugin/zeeltephp_loadEnv
          $env = zeeltephp_loadEnv();

          // load the ApiRouter deparse @zeeltephp:api-request
          $zpAR = new ZP_ApiRouter($env);

          if ($zpAR->route) {
               // yuhu - all is set to exec +.php files

               // # load consumer lib files
               zp_load_lib_files(PATH_ZPLIB);

               // # init ZP_DB provider (if set)
               if (isset($env['ZEELTEPHP_DATABASE_URL'])) {
                    require_once('lib/db/db.db.php');
                    $db = new ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
               }

               // init response of +.php files
               $data = null;

               // # run +page.server.php
               //   zpAr now collects all +.php files but for now we still support just page.server.php
               if ($zpAR->routeFileExist) {

                    include('core/exec.page.server.php');
                    $data = zp_exec_pageServerPHP();
               }

               // # yuhu - all is good :-)
               //   return data as JSON response, and let zp_fetch_api() do the rest :-)
               echo json_encode([
                    'ok'   => true,
                    'code' => 200,
                    'data' => $data,
                    //'zpDB' => $db
                    // -- ...$data // pitfall - do not because of send-data-JSON of any data  
               ]);
          }
          else {
               // wel - lets notifiy what is missing
               if (!$zpAR->route) {
                    // nothing to do - just no route is given
                    throw new Error('400');
               }
               else if (!$zpAR->routeFileExist) {
                    // no .php file found in route
                    throw new Error('404');
               }
               else {
                    // # last resort return an unsupported error
                    zp_log('?? unknown error');
                    zp_log_debug('?? unknown error');
                    throw new Error(501);
               }
          }

          zp_log_debug('//zeeltephp_main()');

     } catch (Error $exp) {  
          zp_handle_error($exp); //, "Error");
     } catch (Exception $exp) {
          zp_handle_error($exp); //, "Exception");
     } catch (ErrorException $e) {
          zp_handle_error($exp); //, "ErrorException, Runtime error");
     } catch (Throwable $exp) {
          zp_handle_error($exp); //, "Throwable");
     }
}

?>