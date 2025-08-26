<?php namespace ZeeltePHP\Core\Exec;

use function ZeeltePHP\Error\log_debug;

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
     function exec_PlusLayoutServer($fqdn, $response) {
          global $zpAR;
          log_debug('zp_exec_layoutServerPHP()');

          if (!$response) {
               $response = new \stdClass();
               $response->form  = null;
               $response->data  = null;
               $response->error = null;
          }
          
          $callbackFunction = "$fqdn\load";
          if (function_exists($callbackFunction)) {
               $response->data  = $callbackFunction($response->data);
               //throw new \Error(801); // 801 no load() function
          }
          return $response;
     }


?>