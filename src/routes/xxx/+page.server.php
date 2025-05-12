<?php
/* 
/zpdemo/+page.server.php
*/
// inc.demo.zpdemo.php 
//    phpfiles inside same route - include manually.
//    put shared php files in $lib/zplib, they will be autoloaded.
include_once("inc.demo.zpdemo.php");

# slowing down the fetch to take a break and a coffee 
# sleep(2);

/**
 * default load 
 */
function load() {
      global $zpAR, $env, $db;
      // $zpAR   = ZP_ApiRouter
      // $env    = .env values
      // $db     = ZP_DB
      //
      // your code
      // $zpAR = ZP_ApiRouter 
      // $_REQUEST, $_GET, $_POST contains projects real data ($zpAR->data) 
      //
      return [
            '+page.server.php'   => '/load( xxx ) ',
            //'demo_lip_example()' => demo_lip_example(),
            '$_REQUEST'   => $_POST,
            'zpAR' => $zpAR,
            //'zpDB' => $db,
            //'zpEnv' => $end
      ];
}

/**
 * default actions
 */
function actions($action, $value) {
      global $zpAR, $env, $db;

      class response {
            public $message   = '+page.server.php/actions()';
            public $requests  = null;
            public $zpAR      = null;
            public $zpEnv     = null;
            public $zpDB      = null;
      }
      $resp = new response();

      switch ($action) {
            case 'action':
                        // ... do anything
                        $resp->message = 'actions/action';
                  break;
            case 'send_data_json':
                        // ... do anything
                        $resp->message = 'actions/send_data_json';
                  break;
            case 'action_submit_2Form':
                        $resp->message = 'actions/action_submit_form';
                  break;
            case 'example':
                        $resp->message = 'actions/example';
                  break;
            default:
                        $resp->message = 'actions/default-fallback. expected: '.$action;
                  break;
      }
      // add value to message
      $resp->message .= ' '.$value;
      // requests
      $resp->requests = [
            '$_GET' => $_GET,
            '$_POST' => $_POST,
            '$_REQUEST' => $_REQUEST
      ];
      // expose $zpAR for ZPDev
      $resp->zpAR      = $zpAR;

      return $resp;
}

/**
 * you also can have your actions standalone outside from actions()
 */
function action_foo($value) {
      global $zpAR;
      return [
            'message'   => 'action_FOO '.$value,
            '$_REQUEST' => $_REQUEST,
            'zpAR'      => $zpAR,
      ];
}

/**
 * another examplse of a standalone action.
 */
function action_submit_Form($value) {
      global $zpAR;
      return [
            'message' => 'PHP received POST: ',
            '$_POST'  => $_POST,
            'zpAR'    => $zpAR,
      ];
}

/**
 * check DB connecter via ZP_DEV
 */
function action_checkDB($value) {
      global $zpAR;
      return [
            'message' => 'hello from action_checkDB',
            'zpAR'    => $zpAR,
            'zpAR'    => $zpAR,
            //'zpDB' => $db,
            //'zpEnv' => $end
      ];
}

?>