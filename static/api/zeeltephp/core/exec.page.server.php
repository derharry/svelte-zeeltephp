<?php namespace ZeeltePHP\Core\Exec;

use function ZeeltePHP\Error\handle_error;
use function ZeeltePHP\Error\log_debug;

     /**
      * Executes the +page.server.php file and handles route actions.
      * 
      * @global ZP_ApiRouter $zpAR 
      * @global mixed        $data Data from previous server +.php files in route
      * 
      * @return mixed Response data from executed action or load function
      * @throws Error If no valid handler is found (801, 802, 501)
      */
     function exec_PlusPageServer($fqdn, $options = []) {
          global $zpAR, $data, $db, $zpTime;
          $zpTime->start('exec_PlusPageServer()');
          log_debug('exec_PlusPageServer()');
          $response = new \stdClass();
          $response->zpxc  = $zpAR->context;
          $response->form  = null;
          $response->error = null;
          $response->data  = null;
          try {
               // Normalize: threat string "null" as null
               $action = $zpAR->action;
               if ($action === "null" || is_string($action) && !str_starts_with($action, '?/'))
                    $action = null;

               // Handle actions
               if (is_string($action) && str_starts_with($action, '?/')) {

                    $action = str_replace('?/', '', $zpAR->action);

                    $outsideActionHandler = "$fqdn\action_$action";
                    if (function_exists($outsideActionHandler)) {
                         $response->form = $outsideActionHandler($zpAR->value);
                    }

                    if (!$response->form) {
                         $actionHandler = "$fqdn\actions";
                         if (function_exists($actionHandler)) {
                              $response->form = $actionHandler($action, $zpAR->value, $zpAR->data);
                         }
                    }

                    if (!$response->form) {
                         throw new \Error(802);
                    }
               }
               else {
                    $callbackFunction = "$fqdn\load";
                    if (function_exists($callbackFunction)) {
                         $response->data = $callbackFunction();                    
                    } else throw new \Error(801); // 801 no load() function
               }
          }
          catch (\Exception $exp) {
               $response->error = $exp;
               handle_error($exp);
          } finally {
               log_debug($zpTime->endN('exec_PlusPageServer()'));
               log_debug('//exec_PlusPageServer()');
          }
          return $response;
     }


?>