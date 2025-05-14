<?php
// import ENV for PUBLIC_ZEELTEPHP_BASE
// import ENV for BASE
// import JSON via 



class ZP_ApiRouter
{

      // ZP_ApiRouter Svelte + PHP
      public $route ;
      public $action;
      public $value ;
      public $data  ;

      // PHP SSR specific
      public $environment = null; // 'dev | build'; 
      public $method      = 'GET';
      public $routeFileExist = false;
      public $routeFile   = '/';
      public $routePath   = '/';
      public $routeBase   = '/';
      public $routeBaseApi= '/';
      public $headers     = null;
      public $error       = null;
      public $message     = null;
      public $xxx         = null;

      function Dump() {
            var_dump($this);
      }

      /**
       * read and parse ENVIRONMENT
       * read and parse headers/method/content-type/..
       *     GET, POST, PUT, ... json/formData/...
       * get route, method, action, value, data
       * @param ZP_ENV .env 
       */
      function __construct($env) {
               // deparse the request @zeelte:api 
               $this->parse_zprequest();
               // set env
               $this->environment  = ZP_ENV;        // from zeeltephp_loadEnv();
               $this->routeBase    = PATH_ZPROUTES; // from zp-paths.php
               $this->routeBaseApi = isset($env['PUBLIC_ZEELTEPHP_BASE']) ? $env['PUBLIC_ZEELTEPHP_BASE'] : '/';
               
               // check route -and replace BASE !tmpfix-001
               $this->check_route($env['PUBLIC_BASE']);
            // collect routes for +page.server.php, +api.php, +server.php, ..
            // routes found? yes or no
            // exec_route
            return;
      }

      function check_route($replaceBaseRoute) {
          // do we have a route?
          if (!$this->route) {
               $error   = true;
               $message = "request route is missing."; 
               return;
          }
          $phpFiles = [
                '+page.server.php', //'+api.php', //'+server.php',
          ];
          // check environment
          // check direct paths
          // if no scandir and recheck
          // done
          // first check direct paths as presuming we are in production

          // support /src/routes/**/+page.server.php only for now.
          // !! tmpFix-001-v103-Routing-zp_route counterpart PHP - replace BASE zp_route. 
          $this->route = str_replace($replaceBaseRoute, '', $this->route);  
          
          // v1.0.2 overhoul
          $zp_route      = str_replace('//', '/', $this->route);
          $zp_routeBase  = $this->routeBase;
          $zp_routeBase  = str_replace('/api/', '/', $zp_routeBase);
          $routeFile     = $zp_routeBase . $zp_route . '+page.server.php';
          $this->routeFile = $routeFile;
          $this->routeFileExist = is_file($this->routeFile);
          if (!$this->routeFileExist) {
               // route not found? is it in a (sub)/structure?
               // currently 1-level is supported 
               $paths = zp_scandir($zp_routeBase);
               foreach ($paths as $_) {
                    //echo "$_\n";
                    if (
                         str_contains($_, '/+page.server.php')
                         && str_starts_with($_, '(')
                         && str_contains($_, $zp_route)
                    ) {
                         $routeFile = $zp_routeBase .'/'. $_;
                         $this->routeFile = $routeFile;
                         $this->routeFileExist = is_file($this->routeFile);
                         if (is_file($routeFile)) break; // skip when found
                    }
               }
          }
      }

      function parse_zprequest() {
          // by method
          if ($_SERVER['REQUEST_METHOD'] == 'GET')
               $this->parse_request_GET();
          else if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'OPTIONS')
               $this->parse_request_POST();
          //else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {}
          //else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {} 
          //else if ($_SERVER['REQUEST_METHOD'] == 'UPDATE') {}
          //else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {}
          else
               $this->error = 'ZP unsupported method :'.$_SERVER['REQUEST_METHOD'];
      }

     function parse_request_GET() {
          zp_log_debug('  >> Request_Method = GET');
          $this->message = 'parse_method_zp_routing(GET)';
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
               //$this->data = $_GET;
          }
     }

     function parse_request_POST() {
          $this->message = 'parse_method_zp_routing(POST || OPTIONS)';  
          zp_log_debug('  >> Request_Method = POST || OPTIONS');
          $this->method = 'POST';
          //$contentType  = $_SERVER['CONTENT_TYPE'];

          if (isset($_POST['zp_route'])) {
               $this->message = 'parse_method_zp_routing(POST)';  
               // normal PHP POST request  
               // contentType = 'multipart/form-data, application/x-www-form-urlencoded';
               // nothing to do :-)
          } else {
               // no default PHP post
               // check for JSON and push into $_POST and $_REQUEST
               $this->message = 'parse_method_zp_routing(JSON)';  
               $rawInput = file_get_contents('php://input');
               $json     = json_decode($rawInput, true);
               $_POST    = $json;
               //$_REQUEST = $json;
               // json should be destructed as normal $_POST now.
          }
          if (isset($_POST['zp_route'])) { 
               $this->message = 'parse_method_zp_routing( finals )';  
               if (isset($_POST['zp_route']))  $this->route  = $_POST['zp_route'];
               if (isset($_POST['zp_action'])) $this->action = $_POST['zp_action'];
               if (isset($_POST['zp_value']))  $this->value  = $_POST['zp_value'];
               // remove zp_ from $_POST
               unset($_POST['zp_route']);  unset($_REQUEST['zp_route']);
               unset($_POST['zp_action']); unset($_REQUEST['zp_action']);
               unset($_POST['zp_value']);  unset($_REQUEST['zp_value']);
               if (isset($_POST['zp_data'])) {
                    $this->data = $_POST['zp_data'];
                    $_POST      = $_POST['zp_data'];
                    $_REQUEST   = $_POST;
               } 
          }
     }



      function collect_routes() {

      }

      function exec_route() {

      } 
}

?>
