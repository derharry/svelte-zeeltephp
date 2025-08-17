<?php namespace ZeeltePHP\Core;

use function ZeeltePHP\Core\Lib\load_DotEnv_file;
use function ZeeltePHP\Core\Lib\load_php_lib;
use function ZeeltePHP\Error\log_debug;
use function ZeeltePHP\Error\handle_error;

global $db;
global $env;
global $lib;
global $zpAR; 
global $request;

/***
 * Start ZeeltePHP
 */
function main() {
     global $debug, $zpAR, $env, $lib, $db, $zpTime;
     try {
          $zpTime->start('main()');
          log_debug('zeeltephp_main()');

          $lib  = PATH_ZPLIB;
          $env  = load_DotEnv_file();
          $zpAR = new ZP_ApiRouter($env);

          $event = [];
          $event['url'] = [];
          $event['url']['pathname'] = $zpAR->route;
          $event['zpAR'] = $zpAR;

          // idea: make it optional via evn.ZEELTEPHP_LOADLIB = true
          load_php_lib(PATH_ZPLIB);

          ### todo: idea: move to +hooks.server.php
          # use ZeeltePHP\DB\DB 
          # handle() $db = new \ZeeltePHP\DB\DB\ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
          if (isset($env['ZEELTEPHP_DATABASE_URL'])) {
               log_debug('  load ZP_DB');
               require_once('lib/db/zp.db.php');
               $db = new \ZeeltePHP\Lib\DB\ZP_DB($env['ZEELTEPHP_DATABASE_URL']);
          }

          $response = null;
          if (is_file(PATH_ZPROUTES."+hooks.server.php")) {
               log_debug(count($zpAR->routeFiles). ' hooks');
               require_once('  exec.hooks.server.php');
               $response = exec_hooksServer();
          }
          else {
               $response = resolve_routes();
          }

          if (ZP_DEBUG) {
               $jsonResponse = json_encode($response, JSON_PRETTY_PRINT, 4);
               log_debug($jsonResponse);
          }
          echo json_encode($response);

          log_debug('//zeeltephp_main()');
          log_debug($zpTime->endN('main()'));
     }
     catch (\Throwable $exp) {
          handle_error($exp); 
     }
}

/**
 * 
 */
function resolve_routes() {
     global $zpAR, $zpTime, $db, $env;
     $zpTime->start('resolve_routes()');
     log_debug('resolve_routes() total:'.count($zpAR->routeFiles));
     $response = null;
     if (sizeof($zpAR->routeFiles) > 0) {
          log_debug("  -- execute route files:");
          log_debug($zpAR->routeFiles);

          // parse the route
          foreach ($zpAR->routeFiles as $plusPhpFile) {
               $zpTime->start($plusPhpFile);
               log_debug("+server:    $plusPhpFile", 2);

               $options = [
                    'db'     => $db,
                    'env'    => $env,
                    'zpAR'   => $zpAR,
                    'params' => $_REQUEST
               ];

               $zpns = ''; // required uniq namespace in route
               #include("$zpAR->routeBase/$plusPhpFile");
               include($plusPhpFile);
               if ($zpns === '') {
                    $data = null; // reset data to not expose parent data
                    log_debug("  ! missing namespace for $zpAR->routeBase/$plusPhpFile !");
                    break;
               } else {
                    log_debug("  has namespace $zpns");
                    if ($zpAR->context == 'api') {
                         log_debug("  is context 'api' ");
                         include_once('core/exec.server.php');
                         $response = \ZeeltePHP\Core\Exec\exec_PlusServer($zpns, $options);
                    }
                    else if ($zpAR->context == 'page') {
                         log_debug("  is context 'page' ");
                         if (str_ends_with($plusPhpFile, '+layout.server.php')) {
                              include_once('core/exec.layout.server.php');
                              $response = \ZeeltePHP\Core\Exec\exec_PlusLayoutServer($zpns, $options);
                         }
                         else if (str_ends_with($plusPhpFile, '+page.server.php')) {
                              log_debug("  load +page.server");
                              include_once('core/exec.page.server.php');
                              $response = \ZeeltePHP\Core\Exec\exec_PlusPageServer($zpns, $options);
                              log_debug("  response:");
                              log_debug($response);
                         }
                         else {
                              log_debug("  no +server file match for $plusPhpFile");
                         }
                    }
               }
          }
          log_debug('responseLayer is context '.$zpAR->context);
          log_debug($zpTime->endN('resolve_routes()'));
     }
     log_debug('//resolve_routes() ');
     return $response;
}

?>