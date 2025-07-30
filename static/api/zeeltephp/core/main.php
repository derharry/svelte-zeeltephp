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
               // -- $hooksData;
               if (is_file(PATH_ZPROUTES."+hooks.server.php")) {
                    zp_log_debug('+hooks.server.php found.');
                    /*
                    -- can    +hooks.server.php be added to routeFiles to be executed from zp.executor?
                    -- needs  +hooks.server.php to be a FQDNfile?
                              array_unshift($zpAR->routeFiles, '+hooks.server.php');

                    -- can    +hooks.server.php be standalone? diff DEV and PROD (but lives in PATH_ZPROUTES);
                              $zpTime->start('+hooks.server.php');
                              include(PATH_ZPROUTES."+hooks.server.php");
                              zp_log_debug('+hooks.server.php found.');
                              if (function_exists('handle')) {
                                   zp_log_debug('executing handle()');
                                   $hooksData = handle();
                              } else zp_log_debug('No METHOD handle() found.');
                              zp_log_debug($zpTime->endN('+hooks.server.php'));
                    */
               }
               
               // the response of the +server files is passed the route downwards
               $data = null;
               
               // parse the route
               foreach ($zpAR->routeFiles as $plusPhpFile) {
                    $zpTime->start($plusPhpFile);
                    zp_log_debug("+server:    $plusPhpFile", 2);
                    zp_log_debug(str_starts_with($plusPhpFile, '+page.server.') ? 'yes':'no', 14);

                    $zpns = '';
                    include("$zpAR->routeBase/$plusPhpFile");
                    if ($zpns !== '') {
                         zp_log_debug("  has namespace");

                         if ($zpAR->context == 'api') {
                              zp_log_debug("  is context == 'api'");
                              include_once('core/exec.server.php');
                              $data = zp_exec_PlusServerPHPFile($zpns);
                         }
                         else { 
                              zp_log_debug("  is context == 'page'");
                              
                              if (str_starts_with($plusPhpFile, '+layout.server.')) {
                                   zp_log_debug("  loaded +layout.server");
                                   include_once('core/exec.layout.server.php');
                                   $data = zp_exec_PlusLayoutServerPHPFile($zpns);
                              }
                              else if (str_starts_with($plusPhpFile, '+page.server.')) {
                                   zp_log_debug("  loaded +page.server");
                                   include_once('core/exec.page.server.php');
                                   $data = zp_exec_PlusPageServerPHPFile($zpns);
                              }
                              else {
                                   zp_log_debug("  no +server file match for $zpAR->routeBase/$plusPhpFile");
                              }
                         }

                    } else {
                         # log error at current route and exit loop (root cause) 
                         $data = null; # just to be sure to not expose from parent data
                         zp_log_debug("  missing namespace for $zpAR->routeBase/$plusPhpFile");
                         break;
                    }

                    zp_log_debug($zpTime->endN($plusPhpFile));
               }

               // return data as JSON response, and let zp_fetch_api() do the rest :-)
               echo json_encode($data);
               /*
               from 1.0.4 only return only the data and use response-codes - no overhead-layer anymore.
               echo json_encode([
                    'ok'   => true,
                    'code' => 200,
                    'data' => $data,
                    //'zpDB' => $db
                    // -- ...$data // pitfall - do not because of send-data-JSON of any data  
               ]);
               */
          } 
          else {
               // no +server files found in route
               // previous 1.0.4 we returned an error, from now just send an default empty-JSON response
               echo json_encode((object)null);
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