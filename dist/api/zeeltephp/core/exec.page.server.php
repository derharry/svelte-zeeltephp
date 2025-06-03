<?php

     /**
      * Executes the +page.server.php file and handles route actions.
      * 
      * @global ZP_ApiRouter $zpAR 
      * @global mixed        $data Data from previous server +.php files in route
      * 
      * @return mixed Response data from executed action or load function
      * @throws Error If no valid handler is found (801, 802, 501)
      */
     function zp_exec_pageServerPHP() {
          global $zpAR, $data;
          zp_log_debug('zp_exec_pageServerPHP()');

          // Include the +page.server.php from route
          include($zpAR->routeFile);

          // Normalize: treat string "null" as null
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
               $outsideActionHandler = 'action_' . $action;
               if (function_exists($outsideActionHandler)) {
                    // exec action_ACTION
                    return $data = $outsideActionHandler($zpAR->value);
               }

               // Fallback to general actions() handler
               if (function_exists('actions')) {
                    // exec actions($action, $value, $data)
                    return actions($action, $zpAR->value, $zpAR->data);
               }
               
               // No valid handler found
               throw new Error(802); // 802 = No action handler

          }

          // Handle GET/POST requests without specific action
          else if (function_exists('load')) {
               return $data = load();
               //throw new Error(801); // 801 no load() function
          }

     }


?>