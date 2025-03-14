<?php
// import ENV for PUBLIC_ZEELTEPHP_BASE
// import ENV for BASE
// import JSON via 
require_once('zeeltephp/lib/request/api.helper.php');

class ZP_ApiRouter 
{

      // ZP_ApiRouter Svelte + PHP
      public $route ;
      public $action;
      public $value ;
      public $params;
      public $data  ;

      // PHP SSR specific
      public $environment = null; // 'dev | build'; 
      public $method      = 'GET';
      public $routeFile   = '/';
      public $routeFileExist = false;
      public $headers     = null;
      public $error       = null;

      function Dump() {
            var_dump($this);
      }

      /**
       * read and parse ENVIRONMENT
       * read and parse headers/method/content-type/..
       *     GET, POST, PUT, ... json/formData/...
       * get route, method, action, value, data
       */
      function __construct() {
            try {
                  $this->parse_method_zp_routing();
                  $this->set_environment();
                  $this->parse_request();
            }
            catch (Exception $exp) {
                error_log($exp);
            }
      }

      function set_environment() {
            try {
                  // load the .env file -> zp_read_env_file
                  if (!$this->environment) {
                        //error_log'set_environment()/env');
                        $env = zp_read_env_file();
                        // by $env->build_dir we know 
                        //     dev (defined in .env.dev) 
                        //     build mode (not exported into ./zeeltephp/.env)
                        //     - instead of build = file_exist("/zeeltephp/.env") 
                        $this->environment = isset($env['BUILD_DIR']) ? 'dev' : 'build';
                  }
                  if ($this->route) {
                        //error_log('set_environment()/route');
                        //error_log('route:'.$this->route);

                        $routeFile = $this->route .'+page.server.php';
                        $routePath = $this->environment == 'dev' ? '../../src/routes/' : 'routes/';
                        $this->routeFile = $routePath.$routeFile;
                        $this->routeFileExist = is_file($this->routeFile);
                        if (!$this->routeFileExist) {
                              $this->error = 'ZP: could not find route: '.$this->routeFile.'.';
                              //$this->routeFile = null; // set default value back
                        }
                  }
            }
            catch (Exception $exp) {
                error_log($exp);
            }
      }

      /**
       * by method and content-type
       * GET string :-) $_GET or $_REQUEST
       * POST content-type JSON, $_POST, $_REQUEST
       * PUT, ..
       */
      function parse_method_zp_routing() {
            try {
                  // by method
                  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                        // route, action, value, data is set by funtion
                        //error_log'PARSE GET');
                        //$this->parse_method_GET_url();
                        $idx = -1;
                        foreach ($_GET as $key => $value) {
                              $idx++;
                              //error_log('key:'.$key);
                              if ( $idx == 0 && str_starts_with($key, '/') && str_ends_with($key, '/') ) {
                                    //route can only be on index 0 
                                    //error_log('route:'.$key);
                                    $this->route = $key;
                                    unset($_GET[$key], $_REQUEST[$key]);
                              }
                              else if ( ($idx == 0 || $idx == 1) && str_starts_with($key,'?/') ) {
                                    //action can be on index 0 or 1
                                    $this->action = $key;
                                    $this->value  = $value;
                                    unset($_GET[$key], $_REQUEST[$key]);
                              }
                              else {
                                    if (!is_array($this->params)) $this->params = [];
                                    $this->params[$key] = $value;
                              }
                        }
                        //$this->data = $_GET;
                  }
                  else if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                        //error_log'PARSE POST');
                        $this->method = 'POST';
                        //$contentType  = $_SERVER['CONTENT_TYPE'];

                        if (isset($_POST['zp_route'])) { //|| str_contains($contentType, 'application/json') ) {
                              //error_log('normal');
                              // normal PHP behaviour
                              // default PHP is $_POST is already set
                              // e.g. multipart/form-data, application/x-www-form-urlencoded
                              $this->route  = $_POST['zp_route'];
                              $this->action = $_POST['zp_action'];
                              $this->value  = $_POST['zp_value'];
                              $this->data   = $_POST;
                              unset($_POST['zp_route']);  unset($_REQUEST['zp_route']);
                              unset($_POST['zp_action']); unset($_REQUEST['zp_action']);
                              unset($_POST['zp_value']);  unset($_REQUEST['zp_value']);
                        }
                        else {
                              // parse json
                              //error_log('JSON');
                              $jsonData     = read_json_input();
                              //$this->data   = $jsonData;
                              $this->route  = $jsonData['zp_route'];
                              $this->action = $jsonData['zp_action'];
                              $this->value  = $jsonData['zp_value'];
                              $this->data   = $jsonData['zp_data'];
                              // load data into $_REQUEST
                        }
                  }
                  //else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {}
                  //else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {} 
                  //else if ($_SERVER['REQUEST_METHOD'] == 'UPDATE') {}
                  //else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {}
                  else {
                        $this->error = 'ZP: Unknown method for routing.'.$_SERVER['REQUEST_METHOD'];
                  }
      
            }
            catch (Exception $exp) {
                  error_log($exp);
            }
      }
      



      /**
       * get route, action, value from the GET REQUEST
       * and remove the matches from the $_GET/$_REQUEST 
       * previous $this->parse_url_string() but we only need QUERY_STRING and $_GET/$_REQUEST :-)
       */
      function parse_method_GET_url() {
            try {
                  return;
                  $queryString = $_SERVER['QUERY_STRING'];
                  //error_log$queryString);
                  $queryParts  = explode('&', $queryString);
                  //$this->params = $_GET;
                  //$this->error  = $_SERVER;

                  $idx = -1;
                  foreach ($_GET as $param) {
                        $idx++;
                        $unload = false;
                        if ( $idx == 0 && str_starts_with('/', $param) && str_ends_with('/', $param) ) {
                              //route can only be on index 0 
                              //error_log$param);
                              $this->route = ltrim(rtrim($param, '/'), '/') ."/";
                              $unload = true;
                        }
                        else if ($i == 0 && !$param) 
                              $this->route = '/'; // set default to ./
                        else if ( ($idx == 0 || $idx == 1) && str_starts_with('?/', $param) ) {
                              //action can be on index 0 or 1
                              $this->action = $param;
                              $this->action = $_GET[$param];
                              $unload = true;
                        }

                        if ($unload) {
                              unset($_GET[$param], $_REQUEST[$param]);
                        }
                  }

                  /*
                  //$this->route  = $queryString;
                  //$this->route  = $get['zp_route'];
                  //$this->action = $get['zp_action'];
                  //$this->value  = $get['zp_value'];
                  $this->data   = $_GET;
                        
                  return [
                        'zp_route' => $queryString,
                        'zp_action' => '?/test',
                        'zp_value' => '69'
                  ];

                  /*
                  $queryParts  = explode('&', $queryString);

                  for ($i=0; $i < sizeof($queryParts); $i++) {

                        // current part
                        $part = $queryParts[$i];
                        //var_dump($part);

                        // set route only from first occurence/position
                        // if (match) presume route else set default route to ./
                        if ($i == 0 && strpos($part, '=') === false && strpos($part, '?') === false) {
                              $this->route = ltrim(rtrim($part, '/'), '/') ."/";
                              unset($_GET[$part]);
                              unset($_REQUEST[$part]);
                        } else if ($i == 0 && !$this->route) $this->route = '/'; // set default to ./

                        // set action and value from first or second occurence/position
                        if (($i >= 0 && $i <= 1) && (str_starts_with($part, '?/'))) {
                              // presume action
                              $action = explode('=', $part)[0];
                              $this->action = $action;
                              $this->value  = $_GET[ $action ];
                              unset($_GET[$this->action]);
                              unset($_REQUEST[$this->action]);

                              $this->action = str_replace('?/', '', $this->action);
                              $this->action = str_replace('/', '', $this->action);
                        }
                  }
                  $this->params = $_GET;
                  $this->data   = &$_GET;
                  */
            }
            catch (Exception $exp) {
                  error_log($exp);
            }
      }


      function parse_request() {
            try {
                  
            }
            catch (Exception $exp) {
                error_log($exp);
            }
      }


}


?>