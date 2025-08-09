<?php namespace ZeeltePHP\Core;

use function ZeeltePHP\Core\Lib\load_DotEnv_file;
use function ZeeltePHP\Core\Lib\load_lib_files;
use function ZeeltePHP\Error\log_debug;
use function ZeeltePHP\Error\handle_error;

use stdClass;
use ZeeltePHP\Lib\DB\ZP_DB;

/***
 * Start ZeeltePHP.
 * Start main() after zeeltephp_loadRunEnvironment() has set the environment paths (consts).
 */
function main() {
     global $debug, $zpAR, $env, $db, $data, $zpTime;
     try {
          $zpTime->start('main()');
          log_debug('zeeltephp_main()');

          // load the .env-file or auto generate missing variables,
          // equivalent to vite-plugin/load_DotEnv_file
          $env = load_DotEnv_file();

          // deparse api-request via ZP_ApiRouter 
          $zpAR = new ZP_ApiRouter($env);

          // 2025-07-17 1.0.4 support for context page/api
          // previous version executed +page.server.php if is_file(routeFileExist + routeFile) 
          // new version changed to collected ZP_ApiRouter.routeFiles[] 
          if (sizeof($zpAR->routeFiles) > 0) {
               //log_debug("  route files:");
               //log_debug($zpAR->routeFiles);

               // # load consumer lib files
               load_lib_files(PATH_ZPLIB);

               // # init ZP_DB provider (if set)
               if (isset($env['ZEELTEPHP_DATABASE_URL'])) {
                    log_debug('  load ZP_DB');
                    require_once('lib/db/db.db.php');
                    $db = new ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
               }

               // 2025-07-16 1.0.4 support for +hooks.server.php
               // -- $hooksData;
               if (is_file(PATH_ZPROUTES."+hooks.server.php")) {
                    log_debug('+hooks.server.php found.');
                    /*
                    -- can    +hooks.server.php be added to routeFiles to be executed from zp.executor?
                    -- needs  +hooks.server.php to be a FQDNfile?
                              array_unshift($zpAR->routeFiles, '+hooks.server.php');

                    -- can    +hooks.server.php be standalone? diff DEV and PROD (but lives in PATH_ZPROUTES);
                              $zpTime->start('+hooks.server.php');
                              include(PATH_ZPROUTES."+hooks.server.php");
                              log_debug('+hooks.server.php found.');
                              if (function_exists('handle')) {
                                   log_debug('executing handle()');
                                   $hooksData = handle();
                              } else log_debug('No METHOD handle() found.');
                              log_debug($zpTime->endN('+hooks.server.php'));
                    */
               }
               
               $data  = null;
               $form  = null;
               $error = null;

               // parse the route
               foreach ($zpAR->routeFiles as $plusPhpFile) {
                    $zpTime->start($plusPhpFile);
                    log_debug("+server:    $plusPhpFile", 2);

                    $zpns    = '';
                    $options = [
                         'db'     => $db,
                         'env'    => $env,
                         'zpAR'   => $zpAR,
                         'params' => $_REQUEST
                    ];

                    include("$zpAR->routeBase/$plusPhpFile");
                    if ($zpns !== '') {
                         log_debug("  has namespace $zpns");
                         if ($zpAR->context == 'page') {
                              log_debug("  is context 'page' ");

                              if (str_starts_with($plusPhpFile, '+layout.server.')) {
                                   log_debug("  load +layout.server");
                                   include_once('core/exec.layout.server.php');
                                   //$data = \ZeeltePHP\Core\Exec\exec_PlusLayoutServer($zpns, $options);
                                   log_debug("data");
                                   log_debug($data);
                              }
                              else if (str_starts_with($plusPhpFile, '+page.server.')) {
                                   log_debug("  load +page.server");
                                   include_once('core/exec.page.server.php');
                                   $response = \ZeeltePHP\Core\Exec\exec_PlusPageServer($zpns, $options);
                                   log_debug("  response");
                                   log_debug($response);
                                   $data  = $response->data  ?? $data;
                                   $form  = $response->form  ?? $form;
                                   $error = $response->error ?? $error;
                              }
                              else {
                                   log_debug("  no +server file match for $zpAR->routeBase/$plusPhpFile");
                              }
                         }
                         else if ($zpAR->context == 'api') {
                              log_debug("  is context 'api' ");
                              include_once('core/exec.server.php');
                              $data = \ZeeltePHP\Core\Exec\exec_PlusServer($zpns, $options);
                         }
                    } else {
                         # log error at current route and exit loop (root cause) 
                         $data = null; # just to be sure to not expose from parent data
                         log_debug("  ! missing namespace for $zpAR->routeBase/$plusPhpFile !");
                         break;
                    }
                    log_debug($zpTime->endN($plusPhpFile));
               }

               // return data as JSON response, and let zp_fetch_api() do the rest :-)
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
               log_debug('response is context '.$zpAR->context);
               $response;
               if ($zpAR->context == 'page') {
                    $response = new stdClass();
                    $response->page  = $zpAR->context;
                    $response->form  = $form ;
                    $response->error = $error;
                    $response->data  = $data ;
               } else if ($zpAR->context == 'api') {
                    $response = $data;
               }
          }

          // no +server files found in route
          // previous 1.0.4 we returned an error, from now just send an default empty-JSON response
          //echo json_encode((object)null);
          $jsonResponse = json_encode($response, JSON_PRETTY_PRINT, 4);
          log_debug($jsonResponse);
          echo $jsonResponse;

          log_debug('//zeeltephp_main()');
          log_debug($zpTime->endN('main()'));
     } catch (\Error $exp) {  
          handle_error($exp); //, "Error");
     } catch (\Exception $exp) {
          handle_error($exp); //, "Exception");
     } catch (\ErrorException $e) {
          handle_error($exp); //, "ErrorException, Runtime error");
     } catch (\Throwable $exp) {
          handle_error($exp); //, "Throwable");
     }
}

?>