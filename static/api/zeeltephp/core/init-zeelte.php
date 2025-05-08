<?php
/**
 * INIT-ZEELTE.PHP
 * 
 * Core init script of ZeeltePHP (aka main())
 */


require_once('zp-bootstrap.php');
require_once('zp-paths.php');
require_once('zeeltephp/lib/sveltekit/class.zp.apirouter.php');
require_once('zeeltephp/lib/cfg/cfg.env.php');
require_once('zeeltephp/lib/io/io.dir.php');
require_once('zeeltephp/lib/helper/zp.log.error.handler.php');
require_once('zeeltephp/lib/helper/load.php.files.php');
require_once('zeeltephp/lib/db/db.db.php');


$debug = false;
global $debug;
global $db;
global $env;
global $zpAR;


try {
     // read config .env_file
     $env = zp_read_env_file();
     if (!$env) {
          $env = [];
          // load defaults
         $env['ZEELTEPHP_DATABASE_URL'] = 'mysql2://root@localhost/test';
          //throw Error('could not find .env file');
     }
     
     // load the ApiRouter to get the rest :-)
     $zpAR = new ZP_ApiRouter();
     $data = null;

     if (!$zpAR->routeFileExist) {
          throw new Error('No route/+page.server.php found');
     }
     else {

          // real main() for +page.server.php

          // load lib files
          zp_load_lib($zpAR->environment);

          // init ZP_DB - database provider
          $db = new ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
          //$db->connect();
          //$data = $db->query('SELECT 5 as x');
          //$db = new ZeeltePHP_DB_WordPress($env['ZEELTEPHP_DATABASE_URL']);

          $data = zp_exec_pageserverphp();
     }

     // Normal JSON response
     echo json_encode([
          'ok'   => true,
          'code' => 200,
          'data' => $data,
          //'zpAR' => $zpAR,
          //'zpDB' => $db,
          //'le' => $db->last_error()
     ]);
     
} catch (Error $e) {
     echo json_encode([
          'ok'      => false,
          'code'    => 500,
          'message' => 'error',
          'error'   => [
               'type' => get_class($e),
               'message' => $e->getMessage(),
               'file' => $e->getFile(),
               'line' => $e->getLine()
          ],
          //'zpAR' => $zpAR,
          //'zpDB' => $db,
     ]);
} catch (ErrorException $e) {
     // Handle non-fatal errors
     echo json_encode([
          'ok'      => false,
          'code'    => 500,
          'message' => 'Runtime error',
          'error'   => [
               'type' => get_class($e),
               'message' => $e->getMessage(),
               'file' => $e->getFile(),
               'line' => $e->getLine()
          ],
          //'zpAR' => $zpAR,
          //'zpDB' => $db,
     ]);
}
finally {
    ob_end_flush();
}


/**
 * check for 
 */
function zp_read_SvelteKitRoutes() {
     try {
          //code...
     } catch (\Throwable $th) {
          //throw $th;
     }
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


function zp_exec_pageserverphp() {
     global $zpAR;
     //try {
          zp_log_debug($zpAR->routeFile);

          // include +page.server.php
          include($zpAR->routeFile);

          // response from load or actions
          // we use states :string for internal knowing what we are doing / where we are.
          $action_response = '__ZP-INIT__';

          // if no zpAR->action defined -> its basic GET load()
          // if no action then just call load()
          if (is_null($zpAR->action)) {
               // no action -> load()
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
          return $action_response;
     
     /*
     }
     catch (Exception $exp) {
          zp_log_debug(' !! '.$exp->getMessage());
          zp_error_handler($exp);
          return $exp->getMessage();
     }
     finally {
          zp_log_debug('//'.$zpAR->routeFile);
     }
     */
}


?>