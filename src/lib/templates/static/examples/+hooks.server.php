<?
//+hooks.server.php

     /**
      * @param object   $event 
      * @param callable $resolve
      */
     function handle($event, callable $resolve) {
          global $zpAR, $env, $db;

          //
          // ... do any custom implementation
          //

          // required to execute /routes/ 
          // or don't to bypass ZeeltePHP
          $response = $resolve();

          // customize response or headers
          // expose zp-dev-vars globally
          $response->_zpAR  = $zpAR;
          $response->_zpENV = $env;
          $response->_zpENV = $env;

          return $response;
     }

?>