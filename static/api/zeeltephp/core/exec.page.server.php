<?php

/**
 * execute the +page.server.php file
 * @param {any} $data    data from earlier +.php files in route till +page.server.php 
 */
function zp_exec_pageserverphp() {
     global $zpAR, $data;
     // -- todo comeback of logging messages - zp_log_debug($zpAR->routeFile);

     // # include +page.server.php
     include($zpAR->routeFile);

     // we use states :string for internal knowing what we are doing / where we are.
     $state = 'init';

     // # execute load or actions
     if (is_null($zpAR->action)) {

          // if no zpAR->action defined -> exec load()
          $state = 'load?';
          if (function_exists('load')) {
               $state = 'load()'; 
               $data = load();
          } else {
               $state = 'no-load';
               throw new Error(801);
          }
     }
     else if ($zpAR->action) {

          // we go for actions
          $state = 'action?';

          // # check for outside/custom action first
          //      function action_ACTION()
          //      so it has not to be in like actions() switch ($action) case 'ACTION';
          $action_name_stripped = str_replace('?/', '', $zpAR->action);  // remove ?/ 
          $action_function_name = 'action_'.$action_name_stripped;       // real function name

          if (function_exists($action_function_name)) {
               // exec action_ACTION
               $state = 'action-outside '.$action_function_name;
               $data = $action_function_name($zpAR->value);
          } 
          else if (function_exists('actions')) {
               // exec actions($action, $value, $data)
               $state = 'actions '.$zpAR->action;
               $data = actions($action_name_stripped, $zpAR->value, $zpAR->data);
          }
          else {
               // uuiiii - no action found
               //    return 'No response of load() or action(?/action) :'.$action_response;
               throw new Error(802);
          }
     }
     else {
          throw new Error(501);
     }

     //return $state;
     return $data;

     /*
     }
     catch (Exception $exp) {
          zp_log_debug(' !! '.$exp->getMessage());
          zp_handle_error($exp);
          return $exp->getMessage();
     }
     finally {
          zp_log_debug('//'.$zpAR->routeFile);
     }
     */
}


?>