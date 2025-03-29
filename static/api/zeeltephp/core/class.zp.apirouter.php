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
    //public $params; depricated we load get into $data
      public $data  ;

      // PHP SSR specific
      public $environment = null; // 'dev | build'; 
      public $method      = 'GET';
      public $routeFile   = '/';
      public $routeFileExist = false;
      public $headers     = null;
      public $error       = null;
      public $message     = null;

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
                  $this->message = 'ZP_ApiRouter()';
                  zp_log_debug('ZP_ApiRouter()');
                  $this->parse_method_zp_routing();
                  $this->set_environment();
                  $this->parse_request();
                  zp_log_debug('//ZP_ApiRouter()');
            }
            catch (Exception $exp) {
                  zp_error_handler($exp);
            }
      }

      function set_environment() {
            try {
                  zp_log_debug('set_environment()');
                  // load the .env file -> zp_read_env_file
                  if (!$this->environment) {
                        //$this->message = 'set_environment()';
                        //error_log'set_environment()/env');
                        $env = zp_read_env_file();
                        // by $env->build_dir we know 
                        //     dev (defined in .env.dev) 
                        //     build mode (not exported into ./zeeltephp/.env)
                        //     - instead of build = file_exist("/zeeltephp/.env") 
                        $this->environment = isset($env['BUILD_DIR']) ? 'dev' : 'build';
                  }
                  if ($this->route) {
                        //$this->message = 'set_environment()/route';
                        //error_log('set_environment()/route');
                        //error_log('route:'.$this->route);

                        $routeFile = $this->route .'+page.server.php';
                        $routePath = $this->environment == 'dev' ? '../../src/routes' : 'routes';
                        $this->routeFile = $routePath.$routeFile;
                        $this->routeFileExist = is_file($this->routeFile);
                        if (!$this->routeFileExist) {
                              $this->error = 'ZP: could not find route: '.$this->routeFile.'.';
                              //$this->routeFile = null; // set default value back
                        }
                  }
                  zp_log_debug('//set_environment()');
            }
            catch (Exception $exp) {
                  zp_error_handler($exp);
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
                  $this->message = 'parse_method_zp_routing()';
                  // by method
                  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                        zp_log_debug('  >> Request_Method = GET');
                        $this->message = 'parse_method_zp_routing(GET)';
                        // route, action, value, data is set by funtion
                        //error_log'PARSE GET');
                        //$this->parse_method_GET_url();
                        $idx = -1;
                        foreach ($_GET as $key => $value) {
                              $idx++;
                              if ($key == null) continue;
                              zp_log_debug('   > parse:'.$key);
                              if ( $idx == 0 && str_starts_with($key, '/') && str_ends_with($key, '/') ) {
                                    //route can only be on index 0 
                                    zp_log_debug('   route:'.$key);
                                    $this->route = $key;
                                    //unset($_GET[$key], $_REQUEST[$key]);
                              }
                              else if ( ($idx == 0 || $idx == 1) && str_starts_with($key,'?/') ) {
                                    //action can be on index 0 or 1
                                    //$this->message = 'action:'.$key;
                                    zp_log_debug('   action:'.$key);
                                    $this->action = $key;
                                    $this->value  = $value;
                                    //unset($_GET[$key], $_REQUEST[$key]);
                              }
                              else {
                                    //$this->message = 'param:'.$key;
                                    zp_log_debug('   param:'.$key);
                                    if (!is_array($this->data)) $this->data = [];
                                    $this->data[$key] = $value;
                                    //$this->params[][$key] = $value;
                              }
                        }
                        //$this->data = $_GET;
                  }
                  else if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                        $this->message = 'parse_method_zp_routing(POST || OPTIONS)';
                        zp_log_debug('  >> Request_Method = POST || OPTIONS');
                        $this->method = 'POST';
                        //$contentType  = $_SERVER['CONTENT_TYPE'];

                        if (isset($_POST['zp_route'])) { //|| str_contains($contentType, 'application/json') ) {
                              zp_log_debug('  >> PHP reading $_POST');
                              //$contentType = 'multipart/form-data, application/x-www-form-urlencoded';
                              if (isset($_POST['zp_route']))  $this->route  = $_POST['zp_route'];
                              if (isset($_POST['zp_action'])) $this->action = $_POST['zp_action'];
                              if (isset($_POST['zp_value']))  $this->value  = $_POST['zp_value'];
                              unset($_POST['zp_route']);  unset($_REQUEST['zp_route']);
                              unset($_POST['zp_action']); unset($_REQUEST['zp_action']);
                              unset($_POST['zp_value']);  unset($_REQUEST['zp_value']);
                              $this->data   = $_POST;

                        }
                        else {
                              // parse json
                               $this->message = 'parse_method_zp_routing(POST JSON)';
                              zp_log_debug('  >> PHP reading $_POST from JSON-input');
                              $data = read_json_input(); 
                              if (!$data) {
                                    $this->error = 'ZP: could not read JSON input.';
                                    return;
                              } else {
                                    if (isset($_POST['zp_route']))  $this->route  = $data['zp_route'];
                                    if (isset($_POST['zp_action'])) $this->action = $data['zp_action'];
                                    if (isset($_POST['zp_value']))  $this->value  = $data['zp_value'];
                                    unset($_POST['zp_route']);  unset($_REQUEST['zp_route']);
                                    unset($_POST['zp_action']); unset($_REQUEST['zp_action']);
                                    unset($_POST['zp_value']);  unset($_REQUEST['zp_value']);
                                    $this->data   = $_POST;
                              }
                        }
                  }
                  //else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {}
                  //else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {} 
                  //else if ($_SERVER['REQUEST_METHOD'] == 'UPDATE') {}
                  //else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {}
                  else {
                        $this->error = 'ZP: Unknown method for routing.'.$_SERVER['REQUEST_METHOD'];
                  }
                  zp_log_debug('//parse_method_zp_routing()');
            }
            catch (Exception $exp) {
                  zp_log_debug('//parse_method_zp_routing() !!');
                  zp_error_handler($exp);
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
                              $this->route = "/".ltrim(rtrim($param, '/'), '/') ."/";
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
                  zp_error_handler($exp);
            }
      }


      function parse_request() {
            try {
                  
            }
            catch (Exception $exp) {
                  zp_error_handler($exp);
            }
      }


}


?>