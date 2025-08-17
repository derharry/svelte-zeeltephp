<?php namespace ZeeltePHP\Core;

/*
-- needs  +hooks.server.php to be a FQDNfile?
          array_unshift($zpAR->routeFiles, '+hooks.server.php');

          $zpTime->start('+hooks.server.php');
               $hooksData = handle();
          } else log_debug('No METHOD handle() found.');
          log_debug($zpTime->endN('+hooks.server.php'));
*/

use function ZeeltePHP\Error\log_debug;

function exec_hooksServer() {
     
     log_debug('zp_exec_ServerPHP()');

     $zpns = '';
     include(PATH_ZPROUTES."+hooks.server.php");

     $handleFn = $zpns.'handle';
     if (function_exists($handleFn)) {
          log_debug("  execute +hooks.server/handle()");
          return $handleFn($event = $event, $resolve = 'resolve_routes');
     }

}

?>