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
     function exec_PlusPageServer($fqdn) {
          global $zpAR, $data;
          log_debug('zp_exec_pageServerPHP()');

          // Include the +page.server.php from route
          //include($zpAR->routeFile);
          //include($consumerFile);

          // Normalize: threat string "null" as null
          $action = $zpAR->action;
          if ($action === "null")
               $action = null;
          if (is_string($action) && !str_starts_with($action, '?/'))
               $action = null;

          // Handle actions
          if (is_string($action) && str_starts_with($action, '?/')) {
               
               // Remove action prefix ?/
               $action = str_replace('?/', '', $zpAR->action); // preg_replace('/^\\?\\//', '', $zpAR->action); 

               // Try outside action handler first (action_FOO())
               $outsideActionHandler = $fqdn . '\\action_' . $action;
               if (function_exists($outsideActionHandler)) {
                    return $data = $outsideActionHandler($zpAR->value);
               }

               // Fallback to general actions() handler
               $actionHandler = $fqdn . '\\actions';
               if (function_exists($actionHandler)) {
                    // exec actions($action, $value, $data)
                    return $actionHandler($action, $zpAR->value, $zpAR->data);
               }
               
               // No valid handler found
                throw new \Error(802); // 802 = No action handler

          }
          else {
               // Handle GET/POST requests without specific action
               $load = $fqdn .'\\load';

               if (function_exists($load)) {
                    return $load();                    
               }
               throw new \Error(801); // 801 no load() function
          }
     }


?>