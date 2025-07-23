<?php

     //namespace SveltePHP

     /**
      * Executes the +page.server.php file and handles route actions.
      * 
      * @global ZP_ApiRouter $zpAR 
      * @global mixed        $data Data from previous server +.php files in route
      * 
      * @return mixed Response data from executed action or load function
      * @throws Error If no valid handler is found (801, 802, 501)
      */
     function zp_exec_PlusLayoutServerPHPFile($fqdn) {
          global $zpAR, $data;
          zp_log_debug('zp_exec_layoutServerPHP()');

          // Include the +page.server.php from route
          //include($zpAR->routeFile);
          //include($consumerFile);

          $callbackFunction = $fqdn . '\\load';
          if (function_exists($callbackFunction)) {
               return $callbackFunction();
               //throw new Error(801); // 801 no load() function
          }
          return;
     }


?>