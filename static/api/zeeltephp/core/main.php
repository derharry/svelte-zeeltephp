<?php

/***
 * Start ZeeltePHP.
 * Start main() after zeeltephp_loadRunEnvironment() has set the environment paths (consts).
 */
function zeeltephp_main() {
     global $debug, $zpAR, $env, $db, $data, $zpTime;
     try {
          $zpTime->start('main()');
          zp_log_debug('zeeltephp_main()');

          // load the .env-file or auto generate missing variables 
          //   equivalent to vite-plugin/zeeltephp_loadEnv
          $env = zeeltephp_loadEnv();

          // load the ApiRouter deparse @zeeltephp:api-request
          $zpAR = new ZP_ApiRouter($env);

          // 2025-07-17 1.0.4 support for context page/api
          // changing to routeFiles[] instead routeFileExist + routeFile
          if (sizeof($zpAR->routeFiles) > 0) {

               //zp_log_debug("  route files:");
               //zp_log_debug($zpAR->routeFiles);

               // # load consumer lib files
               zp_load_lib_files(PATH_ZPLIB);

               // # init ZP_DB provider (if set)
               if (isset($env['ZEELTEPHP_DATABASE_URL'])) {
                    require_once('lib/db/db.db.php');
                    $db = new ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
               }

               // 2025-07-16 1.0.4 support for +hooks.server.php
               // maybe - hooks/handle also needs to be in a UniqFile-NameSpace
               $hooksData;
               if (is_file(PATH_ZPROUTES."+hooks.server.php")) {
                    $zpTime->start('+hooks.server.php');
                    include(PATH_ZPROUTES."+hooks.server.php");
                    zp_log_debug('+hooks.server.php found.');
                    if (function_exists('handle')) {
                         zp_log_debug('executing handle()');
                         $hooksData = handle();
                    } else zp_log_debug('No METHOD handle() found.');
                    zp_log_debug($zpTime->endN('+hooks.server.php'));
               }
               
               // init response of +.php files
               $data = 'x';
               
               // parse the route
               include_once('core/zp.executor.php');
               foreach ($zpAR->routeFiles as $plusPhpFile) {
                    $zpTime->start($plusPhpFile);
                    $data = zp_executor($zpAR->routeBase, $plusPhpFile, $data);
                    zp_log_debug($zpTime->endN($plusPhpFile));
               }

               // return data as JSON response, and let zp_fetch_api() do the rest :-)
               echo json_encode([
                    'ok'   => true,
                    'code' => 200,
                    'data' => $data,
                    //'zpDB' => $db
                    // -- ...$data // pitfall - do not because of send-data-JSON of any data  
               ]);

          } else {
               // error handling .. no +php-files to exec found
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
          zp_log_debug($zpTime->endN('main()'));

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