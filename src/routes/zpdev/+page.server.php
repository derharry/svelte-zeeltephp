<?php namespace zp1752176751; $zpns=__NAMESPACE__;
#
# /zpdev/+page.server.php
# ...
#

// inc.demo.zpdev.php 
//    phpfiles inside same route - include manually.
//    put shared php files in /src/lib_php/, they will be autoloaded.
# include_once("inc.demo.zpdev.php");

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
      return [
            '+page.server.php'   => 'ZPDev load( ) ',
            //'demo_lip_example()' => demo_lip_example(),
            '$_REQUEST'   => $_POST,
            '_zpAR' => $zpAR,
            //'zpDB' => $db
            //'zpEnv' => $end
      ];
}

/**
 * default actions
 */
function actions($action, $value) {
      global $zpAR, $env, $db;
            
      $message = '+page.server.php/actions()';

      switch ($action) {
            case 'action':
                        // ... do anything
                        $message = 'actions/action';
                  break;
            case 'send_data_json':
                        // ... do anything
                        $message = 'actions/send_data_json';
                  break;
            case 'action_submit_2Form':
                        $message = 'actions/action_submit_form';
                  break;
            case 'example':
                        $message = 'actions/example';
                  break;
            default:
                        $message = 'actions/default-fallback. expected: '.$action;
                  break;
      }
      // add value to message
      $message .= ' '.$value;

      error_log('json_encode($db)');
      error_log(json_encode($db));
      return [
            'message'  => $message,
            'requests' => [
                  '$_GET'     => $_GET,
                  '$_POST'    => $_POST,
                  '$_REQUEST' => $_REQUEST
            ],
            '_zpAR'  => $zpAR,
            //'_zpEnv' => $env,
            //'zpDB'  => $db
      ];
}

/**
 * you also can have your actions standalone outside from actions()
 */
function action_foo($value) {
      global $zpAR;
      return [
            'message'    => 'action_FOO '.$value,
            '$_REQUEST'  => $_REQUEST,
            '$zpAR'      => $zpAR
      ];
}

/**
 * another examplse of a standalone action.
 */
function action_submit_Form($value) {
      global $zpAR;
      return [
            'message' => 'PHP received Send-Form: ',
            '$_POST'  => $_POST,
            '_zpAR'   => $zpAR
      ];
}

/**
 * check DB connecter via ZP_DEV
 */
function action_DB_get() {
      global $zpAR, $db;
      return [
            'message' => 'action_DB_get()',
            '_zpAR'   => $zpAR,
            //'_zpDB'   => $db
      ];
}

/**
 * check DB connecter via ZP_DEV
 */
function action_DB_execSQL() {
      global $zpAR, $db;
      $result = $db->query($_POST['sqlstatement']);
      return [
            'result' => $result,
            //'_zpDB'=> $db,
            '_zpAR'  => $zpAR       
      ];
}

/**
 * check DB connecter via ZP_DEV
 */
function action_ENV_get() {
      global $zpAR, $env;
      return [
            '_zpEnv' => $env,
            'PATHS'   => [
                  'ZP_ENV'         => ZP_ENV,
                  'PATH_INIT'      => PATH_INIT,
                  'PATH_ZPLIB'     => PATH_ZPLIB,
                  'PATH_ZPROUTES'  => PATH_ZPROUTES,
                  'PATH_ZPLOG'     => PATH_ZPLOG,
                  'PATH_ZPAPIPHP'  => PATH_ZPAPIPHP
            ],
            '_zpAR'  => $zpAR       
      ];
}

?>