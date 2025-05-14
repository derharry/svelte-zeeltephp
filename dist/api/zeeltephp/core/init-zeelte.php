<?php
/**
 * INIT-ZEELTE.PHP
 * 
 * Core init script of ZeeltePHP (aka main())
 */


// # first - set everything ready
include_once('zp-bootstrap.php');


// register globals to use in any +.php files
$debug = false;
global $debug, $data, $zpAR, $env, $db;
// debug
// data - contains data returned from +.php files (prev $responseData)
// zpAR - new ZP_ApiRouter()
// $env - your .env variables
// $db  - new ZP_DB()


//# exec ZeeltePHP()
zeeltephp_main();
/// thats it bye bye

function zeeltephp_main() {
     global $debug, $zpAR, $env, $db, $data;
     try {

          // hello at ZeeltePHP
          // I should now be running in my environment 

          // # lets load the .env or auto generate missing ones 
          $env = zeeltephp_loadEnv();

          // # load the ApiRouter to get @zeelte:api
          //   deparse 
          //      deparse ZP_API request
          //      check if all routes exist 
          //         parse routes and handle pushing, receving the data - like sveltekit does

          //  ....
          $zpAR = new ZP_ApiRouter($env);

          // # check for exit reasons first
          if (!$zpAR->route) {
               // nothing to do - just no route is given
               throw new Error('400');
          }
          else if (!$zpAR->routeFileExist) {
               // no .php file found in route
               throw new Error('404');
          }
          else if ($zpAR->routeFileExist) {
               // real main()
               // lets execute the +.php files

               // # load lib files
               zp_load_php_files(PATH_ZPLIB);

               // # init ZP_DB provider
               $db = new ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
               //$db->connect();
               //$data = $db->query('SELECT 5 as x');
               //$db = new ZeeltePHP_DB_WordPress($env['ZEELTEPHP_DATABASE_URL']);

               // init response of +.php files
               $data = null;

               // # run +.php files

               // todo - add $zpAR->type  or $zpAR->preExecuteRoute to 
               //   execute +hook, +server.php, etc.. in route until +api or +page.server

               // # run +page.server.php
               require_once('exec.page.server.php');
               $data = zp_exec_pageserverphp();

               // # yuhu - all is good :-)
               //   return data as JSON response, and let zp_fetch_api() do the rest :-)
               echo json_encode([
                    'ok'   => true,
                    'code' => 200,
                    'data' => $data,
                    'zpDB' => $db
                    // -- ...$data // pitfall - do not because of send-data-JSON of any data  
               ]);
          }
          else {
               // # last resort return an unsupported error
               throw new Error(501);
          }
     } catch (Error $e) {
          zp_handle_error($e);
     } catch (ErrorException $e) {
          zp_handle_error($e, "Runtime error");
     }
     finally {
          ob_end_flush();
     }
}

?>