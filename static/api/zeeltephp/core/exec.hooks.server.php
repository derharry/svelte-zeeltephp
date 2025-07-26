<?php

     function zp_exec_hooksServerPHP() {
          global $zpAR, $data;
          zp_log_debug('zp_exec_ServerPHP()');

          if (function_exists('handle')) {
               zp_log_debug("execute handle()");
               return $callbackFunction();
          }

          zp_log_debug("No handle found.");

     }

?>