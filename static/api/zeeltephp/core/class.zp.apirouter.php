<?php

/**
 * ZP_ApiRouter for PHP environment.
 * Parses requests from Svelte's ZP_ApiRouter and checks for +.php files.
 */
class ZP_ApiRouter
{

     //#region Svelte/SSR Routing Properties

          /** @var string|null Route string (e.g. /foo/bar/) */
          public $route;

          /** @var string|null Action string (e.g. ?/ACTION) */
          public $action;

          /** @var mixed Action value */
          public $value;

          /** @var mixed Additional data */
          public $data;

     //#endregion

     //#region PHP SSR specific

          /** @var string Environment name ('development', 'library', 'self-development') */
          public $environment = ZP_ENV;

          /** @var string page | api */
          public $context = 'page';

          /** @var string HTTP request method */
          public $method = 'GET';

          /** @var string|null used content-type */
          public $contentType = null;

          /** deprecated @var bool Whether a +page.server.php file exists for the route */
          public $routeFileExist = false;

          /** deprecated @var string Path to the matched +page.server.php file */
          public $routeFile = '/';

          /** @var string Base path for routes */
          public $routeBase = PATH_ZPROUTES;

          /** @var string  is the real-path of route with (group-routes) */
          public $routePath = '/';

          /** deprecated @var string Base API path */
          public $routeBaseApi = '/';

          /** @var array|null HTTP headers */
          public $headers = null;

          /** @var string|null deprecated. Last error message */
          public $error = null;

          /** @var string|null Last debug message */
          public $last_message = null;

          /** @var array List of matched +.php files in the route */
          public $routeFiles = [];

          /** @var bool activate dbg_msgs to get all messages */
          public $debug = false;

          /** @var array Debug messages */
          public $dbg_msgs = [];

     //#endregion

     /**
      * Debug dump of the current object state.
      */
     function Dump() {
          var_dump($this);
     }

     /**
      * Adds message to $dbg_msgs when $debug is active.
      */
     function log($msg, $value = null) {
          $this->last_message = $msg;
          if ($this->debug) 
               $dbg_msgs[] = $msg;
          if (ZP_DEBUG) 
               zp_log_debug($msg);
     }

     /**
      * Constructor: Initializes the router, parses the request, and collects +.php files.
      * @param array $env    Environment variables from .env or auto-generated.
      * @param bool  $debug  active debug to get all messages in $dbg_msgs;
      */
     function __construct($env, $debug = false) {
          global $zpTime;
          $zpTime->start('ZP_ApiRouter()');
          $this->debug = $debug;
          $this->log('ZP_ApiRouter()');

          $this->routeBaseApi = isset($env['PUBLIC_ZEELTEPHP_BASE']) ? $env['PUBLIC_ZEELTEPHP_BASE'] : '/';
          $this->contentType  = $_SERVER['CONTENT_TYPE'] ?? null;

          // get context
          $headers = getallheaders();
          if (  !empty($headers['X-ZPC-API']) || 
              ( !empty($headers['Access-Control-Request-Headers']) && str_contains($headers['Access-Control-Request-Headers'], 'X-ZPC-API') )
          ) {
               $this->context = 'api';
          }
          $this->log('  -- context: '.$this->context);

          $this->parse_zpRequest();
          $this->collect_plusPHPfilesInRoute($env['BASE']);  // PUBLIC_BASE now just BASE (whitelisted)
          zp_log_debug($this->dbg_msgs);
          zp_log_debug($zpTime->endN('ZP_ApiRouter()'));
     }

     /**
      * Parses the incoming request and sets route, action, value, and data.
      */
     function parse_zpRequest() {
          $this->log('  parse_zpRequest()');
          if ($_SERVER['REQUEST_METHOD'] == 'GET')
               $this->parse_request_GET();
          else {
               // POST, PUT, PATH, UPDATE, DELETE, HEAD
               $this->parse_request_POST();
          }
          //else $this->log('ZP unsupported method :'.$_SERVER['REQUEST_METHOD']);

          // Normalize: threat string "null" as null
          if ($this->action === "null")
               $this->action = null;
          if (is_string($this->action) && !str_starts_with($this->action, '?/'))
               $this->action = null;

          // Clean up route slashes
          $this->route = str_replace('//', '/', $this->route ?? '');
     }

     /**
      * Parses GET requests for routing.
      * Schema GET ?zp_route[&zp_action][&...params-is-zp_data]
      *   for exec load():     ?/route/&...
      *   for exec action():   ?/route/&?/action&...
      */
     function parse_request_GET() {
          $this->log('  -- request-method GET');
          $idx = -1;
          foreach ($_GET as $key => $value) {
               $idx++;
               if ($key == null) continue;
               if ( $idx == 0 && str_starts_with($key, '/') && str_ends_with($key, '/') ) {
                    // route can only be on index 0 
                    $this->route = $key;
                    $this->log('     route = '.$key);
                    unset($_GET[$key], $_REQUEST[$key]);
               }
               else if ( ($idx == 0 || $idx == 1) && str_starts_with($key,'?/') ) {
                    // action can only be on index 0 or 1
                    $this->action = $key;
                    $this->value  = $value;
                    $this->log('     action = '.$key);
                    unset($_GET[$key], $_REQUEST[$key]);
               }
          }
          $this->data = $_GET;
     }

     /**
      * Parses POST (or OPTIONS) requests for routing.
      * Schema POST (formData and JSON)
      *   zp_route  for route
      *   zp_action for action
      *   zp_value  is  value of action
      *   zp_data   is  data or just $_POST
      */
     function parse_request_POST() {
          $this->log('  -- Request_Method = POST');
          $this->method = $_SERVER['REQUEST_METHOD'];

          if (isset($_POST['zp_route'])) {
               // Standard PHP POST
               // contentType = 'multipart/form-data, application/x-www-form-urlencoded', etc;
               // nothing to do :-)
          } else {
               // JSON POST
               $this->log('  -- Request_Method = JSON');
               $json  = json_decode( file_get_contents('php://input') , true);
               if (is_array($json))
               $_POST = $json; // set JSON into $_POST so we can parse it like PHP-POST
               //$this->method = 'JSON';
          }
          if (isset($_POST['zp_route'])) { 
               $this->route  = $_POST['zp_route']  ?? $this->route;
               $this->action = $_POST['zp_action'] ?? $this->action;
               $this->value  = $_POST['zp_value']  ?? $this->value;
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

     /**
      * Collects all +.php files in the current route path (upwards).
      * @param string $replaceBaseRoute Path prefix to remove from route.
      */
     function collect_plusPHPfilesInRoute($replaceBaseRoute) {
          $this->log('  -- collect_plusPHPfilesInRoute()');
          $this->log('     @ replaceBaseRoute : '.$replaceBaseRoute);
          if (!$this->route) {
               $this->log(" ! route is missing, i need a route to collect +.php files"); 
               return;
          }

          // Remove base route prefix if present (tmpFix-001)
          $this->route = str_replace($replaceBaseRoute, '', $this->route);

          // v1.0.4 - supporting more +.php files
          $routePath = $this->route;
          $routeBase = str_replace('//', '/', $this->routeBase."/$routePath");

          // context here?
          if ($this->context == 'api') {
               if (is_file($routeBase."/+server.php")) {
                    // api-route will not have grouped routes
                    $this->routePath = $routePath; 
                    $this->routeBase = $routeBase;
                    zp_log_debug($routeBase."/+server.php");
                    $this->routeFiles[] = '+server.php';
               }    zp_log_debug($this->routeFiles);
          }
          else {
               // 'page'
               if (!is_dir($routeBase)) {
                    // page-route could have grouped routes
                    $routePath = $this->scandir_withGroupedRoutes();
                    $routeBase = str_replace('//', '/', $this->routeBase."/$routePath");
               }
               if (!is_dir($routeBase)) {
                    $this->log('  No route-path found! '.$routeBase);
                    return;
               }
               $this->routePath = $routePath;
               $this->routeBase = $routeBase;
               
               $route_path_depth = ($routePath === '//' || $routePath === '/') ? 0 : max(0, count(array_filter(explode('/', $routePath))));
               //$route_path_depth = count(explode('/', $route_path_depth)); // -2; // -2 because of / at start and end
               $routeFilesRP = zp_scandirRecursiveUp($routeBase, '#\+layout\.server\.#', $route_path_depth);
               $routeFilesTP = zp_scandir($routeBase, '#\+page\.server\.#', $route_path_depth);
               $routeFiles   = [];
               if (is_array($routeFilesRP) && sizeof($routeFilesRP) > 0)
                    $routeFiles = array_merge($routeFiles, $routeFilesRP);
               if (is_array($routeFilesTP) && sizeof($routeFilesTP) > 0)
                    $routeFiles = array_merge($routeFiles, $routeFilesTP);
               $this->routeFiles = $routeFiles;
          }
          return;
     }

     /**
      * Scan for SvelteKits Grouped-Routes. e.g. (admin)/**
      */
     function scandir_withGroupedRoutes() {
          // currently 1-level is supported
          $this->log('  -- scandir_withGroupedRoutes()');
          $grouped_paths = zp_scandir($this->routeBase, '#^\(.*\)$#');
          foreach ($grouped_paths as $_) {
               $route_path = $this->routeBase . $_ . $this->route;
               if (is_dir($route_path)) {
                    $real_route = $_ . $this->route;
                    return $real_route;
               }
          }
     }

}

?>
