<?php namespace ZeeltePHP\Core\Exec;

use function ZeeltePHP\Error\log_debug;


     /****
      * Handle requests for +server.js|ts 
      * GET, POST, PATCH, PUT, DELETE, ...
      */
     function exec_PlusServer($fqdn) {
          global $zpAR, $data;
          log_debug('zp_exec_ServerPHP()');
          log_debug('  @@ REQUEST_METHOD '.$_SERVER['REQUEST_METHOD']);
          log_debug('  @@ $zpAR->method  '.$zpAR->method);

          $method = $zpAR->method;

          //include($consumerFile);
          $callbackFunction = $fqdn . '\\'. $method;

          if (function_exists($callbackFunction)) {
               log_debug("execute $callbackFunction()");
               return $callbackFunction();
          }
          
          $callbackFunction = $fqdn . '\\fallback';
          if (function_exists($callbackFunction)) {
               log_debug("execute fallback()");
               return $callbackFunction();
          }

          log_debug("No METHOD $method or fallback() found.");
          log_debug('//zp_exec_ServerPHP()');
     }

?>