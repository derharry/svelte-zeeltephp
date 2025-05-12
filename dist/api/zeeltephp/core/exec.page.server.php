<?php

function zp_exec_pageserverphp() {
     global $zpAR;
     //try {
          zp_log_debug($zpAR->routeFile);

          // include +page.server.php
          include($zpAR->routeFile);

          // response from load or actions
          // we use states :string for internal knowing what we are doing / where we are.
          $action_response = '__ZP-INIT__';

          // if no zpAR->action defined -> its basic GET load()
          // if no action then just call load()
          if (is_null($zpAR->action)) {
               // no action -> load()
               if (function_exists('load')) {
                    $action_response = '__ZP-LOAD__';   // at least set true because found
                    zp_log_debug('  >> '.$action_response);
                    $action_response = load();
               }
          }
          else if (!is_null($zpAR->action)) {
               $action_response = '__ZP-ACTIONS?__';   // at least set true because found
               // support custom function action_ACTION($value)
               // instead of e.g. actions() switch ($action) case 'ACTION'
               $action_name_stripped = str_replace('?/', '', $zpAR->action);
               $action_function_name = 'action_'.$action_name_stripped;
               if (function_exists($action_function_name)) {
                    // exec action_ACTION
                    $action_response = '__ZP-ACTION_-'.$action_function_name.'__';
                    zp_log_debug('  >> '.$action_response);
                    $action_response = $action_function_name($zpAR->value);
               }
               else if (function_exists('actions')) {
                    // exec actions($action, $value, $data)
                    $action_response = '__ZP-ACTIONS__';
                    zp_log_debug('  >> '.$action_response);
                    $action_response = actions($action_name_stripped, $zpAR->value, $zpAR->data);
               }
          }

          //return 'No response of load() or action(?/action) :'.$action_response;
          $msg = 'valid response from +page.server.php';
          if (is_string($action_response) && str_starts_with($action_response, '__ZP')) {
               $msg = null;
               if      ($action_response == '__ZP-ACTIONS?__')               $msg = "no action(s) found for ".$zpAR->action; 
               else if (str_starts_with('__ZP-ACTION_-', $action_response))  $msg = "no response of ".$action_function_name;
               else if (str_starts_with('__ZP-ACTIONS_-', $action_response)) $msg = "no response of actions()";
               else if ($action_response == '__ZP-LOAD__')                 $msg = "no response of load()";
               else if ($action_response == '__ZP-INIT__')                 $msg = "no load() or actions() found.";
               else $msg = $action_response;
               $action_response = $msg;
          }
          return $action_response;
     
     /*
     }
     catch (Exception $exp) {
          zp_log_debug(' !! '.$exp->getMessage());
          zp_error_handler($exp);
          return $exp->getMessage();
     }
     finally {
          zp_log_debug('//'.$zpAR->routeFile);
     }
     */
}


?>