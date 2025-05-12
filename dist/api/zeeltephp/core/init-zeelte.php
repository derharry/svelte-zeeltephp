<?php
/**
 * INIT-ZEELTE.PHP
 * 
 * Core init script of ZeeltePHP (aka main())
 */


// import core
require_once('zp-bootstrap.php');
require_once('zp-pathsEnv.php');
require_once('zp-inc.php');
require_once('zp-errors.php');
require_once('class.zp.apirouter.php');

// import core minimum dependencies
require_once('zeeltephp/lib/cfg/cfg.env.php');
require_once('zeeltephp/lib/io/io.dir.php');
require_once('zeeltephp/lib/db/db.db.php');


$debug = false;
global $debug;
global $db;
global $env;
global $zpAR;

// zeeltephp_main();
try {

     // hello at ZeeltePHP
     // I should now be running after zp-pathsEnv set me in correct cwd()

     // lets load the .env or auto generate missing ones 
     $env = zeeltephp_loadEnv();

     // load the ApiRouter to get @zeelte:api
     // deparse  zp_fetch_api() 
     //        deparse ZP_API request
     //        check if all routes exist 
     //             parse routes and handle pushing, receving the data - like sveltekit does
     //
     //  ....
     $zpAR = new ZP_ApiRouter($env);

     // default response of 
     //    +page.server.php
     //    // roadmap also from further +.php files like SvelteKit does
     $data = null;

     // exists first 
     // then executes


     if (!$zpAR->route) {
          // nothing to do - just no route is given
          //throw new Error('500');
          throw new Error('501');
     }
     else if (!$zpAR->routeFileExist) {
          throw new Error('502');
     }
     else {

          // real main() for +page.server.php
          require_once('exec.page.server.php');


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
          'code'    => zp_get_errorMessage($e->getMessage(), true),
          'message' => zp_get_errorMessage($e->getMessage()),
          'type'    => get_class($e),
          'file' => $e->getFile(),
          'line' => $e->getLine(),
          'zpAR' => $zpAR,
          //'zpDB' => $db,
     ]);
} catch (ErrorException $e) {
     // Handle non-fatal errors
     echo json_encode([
          'ok'      => false,
          'code'    => 500,
          'message1' => 'Runtime error',
          'type' => get_class($e),
          'message2' => $e->getMessage(),
          'file' => $e->getFile(),
          'line' => $e->getLine(),
          //'zpAR' => $zpAR,
          //'zpDB' => $db,
     ]);
}
finally {
    ob_end_flush();
}



?>