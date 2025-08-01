<?php namespace ZeeltePHP\Core\Exec;

use function ZeeltePHP\Error\log_debug;

     function exec_hooksServer() {
          global $zpAR, $data;
          log_debug('zp_exec_ServerPHP()');

          if (function_exists('handle')) {
               log_debug("execute handle()");
               return $callbackFunction();
          }

          log_debug("No handle found.");

     }

?>