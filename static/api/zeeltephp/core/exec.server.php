<?php

     /****
      * Handle requests for +server.js|ts 
      * GET, POST, PATCH, PUT, DELETE, ...
      */
     function zp_exec_PlusServerPHPFile($fqdn) {
          global $zpAR, $data;
          zp_log_debug('zp_exec_ServerPHP()');
          zp_log_debug('  @@ REQUEST_METHOD '.$_SERVER['REQUEST_METHOD']);
          zp_log_debug('  @@ $zpAR->method  '.$zpAR->method);

          $method = $zpAR->method;

          //include($consumerFile);
          $callbackFunction = $fqdn . '\\'. $method;

          if (function_exists($callbackFunction)) {
               zp_log_debug("execute $callbackFunction()");
               return $callbackFunction();
          }
          
          $callbackFunction = $fqdn . '\\fallback';
          if (function_exists($callbackFunction)) {
               zp_log_debug("execute fallback()");
               return $callbackFunction();
          }

          zp_log_debug("No METHOD $method or fallback() found.");
          zp_log_debug('//zp_exec_ServerPHP()');
     }

?>