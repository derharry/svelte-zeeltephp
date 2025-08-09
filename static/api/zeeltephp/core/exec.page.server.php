<?php namespace ZeeltePHP\Core\Exec;

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
          global $zpAR, $data, $db;
          $response = new \stdClass();
          $response->form  = null;
          $response->error = null;
          $response->data  = null;
          try {
               log_debug('zp_exec_pageServerPHP()');
               
               // Normalize: threat string "null" as null
               $action = $zpAR->action;
               if ($action === "null" || is_string($action) && !str_starts_with($action, '?/'))
                    $action = null;

               // Handle actions
               if (is_string($action) && str_starts_with($action, '?/')) {
                                           
                    // Remove action prefix ?/
                    $action  = str_replace('?/', '', $zpAR->action); // preg_replace('/^\\?\\//', '', $zpAR->action); 

                    // Try outside action handler first (action_FOO())
                    $outsideActionHandler = $fqdn . '\\action_' . $action;
                    if (function_exists($outsideActionHandler)) {
                         $response->form = $outsideActionHandler($zpAR->value);
                    }

                    // Fallback to general actions() handler
                    $actionHandler = $fqdn . '\\actions';
                    if (function_exists($actionHandler)) {
                         // exec actions($action, $value, $data)
                         $response->form = $actionHandler($action, $zpAR->value, $zpAR->data);
                    } 
                    else {
                         // No valid handler found
                         throw new \Error(802); // 802 = No action handler
                    }
               }

               // Handle GET/POST requests without specific action
               else {
                    $load = $fqdn .'\\load';
                    if (function_exists($load)) {
                         $response->data = $load();                    
                    }
                    throw new \Error(801); // 801 no load() function
               }
          }
          catch (\Exception $exp) {
               zp_handle_error($exp);
               $response->error = 'error';
          }
          return $response;
     }


?>