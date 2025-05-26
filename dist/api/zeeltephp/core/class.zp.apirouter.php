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

          /** @var string HTTP request method */
          public $method = 'GET';

          /** @var string|null used content-type */
          public $contentType = null;

          /** @var bool Whether a +page.server.php file exists for the route */
          public $routeFileExist = false;

          /** @var string Path to the matched +page.server.php file */
          public $routeFile = '/';

          /** @var string Base path for routes */
          public $routeBase = PATH_ZPROUTES;

          /** @var string Base API path */
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
          $this->debug = $debug;
          $this->log('ZP_ApiRouter()');

          $this->routeBaseApi = isset($env['PUBLIC_ZEELTEPHP_BASE']) ? $env['PUBLIC_ZEELTEPHP_BASE'] : '/';
          $this->contentType  = $_SERVER['CONTENT_TYPE'] ?? null;

          $this->parse_zpRequest();
          $this->collect_plusPHPfilesInRoute($env['BASE']);  // PUBLIC_BASE now just BASE (whitelisted)

          /*
          $this->log('  -- final outcome :');
          foreach ($this as $k=>$v) {
               try {
                    // lets make sure $v can always be in a String to error_log();
                    $v = "$v";
               } catch (Throwable $e) {
                    // convert any to JSON-String.
                    $v = json_encode($v);
               } finally {
                    // error_log the String.
                    $this->log("    $k = $v");
               }
          }
          $this->log('//ZP_ApiRouter()');
          */
     }

     /**
      * Parses the incoming request and sets route, action, value, and data.
      */
     function parse_zpRequest() {
          $this->log('  parse_zpRequest()');
          if ($_SERVER['REQUEST_METHOD'] == 'GET')
               $this->parse_request_GET();
          elseif (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'OPTIONS']))
               $this->parse_request_POST();
          //else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {}
          //else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {} 
          //else if ($_SERVER['REQUEST_METHOD'] == 'UPDATE') {}
          //else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {}
          else
               $this->log('ZP unsuxxpported method :'.$_SERVER['REQUEST_METHOD']);

          // Normalize: treat string "null" as null
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
          $this->method = 'POST';

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
               $this->method = 'JSON';
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
          if (!$this->route) {
               $this->log(" ! route is missing, i need a route to collect +.php files"); 
               return;
          }

          // Remove base route prefix if present (tmpFix-001)
          $this->route = str_replace($replaceBaseRoute, '', $this->route);

          // v1.0.3 overhaul
          //    from current route - collect +.php files
          //    check route in case for upwards +.php files that needs to be pre-executed
          //   
          $route = $this->route;
          $route_path       = str_replace('//', '/', $this->routeBase."/$route");
          $route_path_depth = ($route === '//' || $route === '/') ? 0 : max(0, count(array_filter(explode('/', $route))) - 2);
          $route_path_depth = count(explode('/', $route)) -2; // -2 because of / at start and end
          $this->log("     route depth: $route_path_depth");
          $this->log("     route path:  $route_path");

          // Collect all +.php files upwards from the route path
          // -- $phpFiles = ['+page.server.php', '+server.php', '+api.php'];
          $this->routeFiles = zp_scandirRecursiveUp($route_path, '^\+.*\.php$', $route_path_depth);
          if ($this->routeFiles) {
               // For now, only support +page.server.php
               $this->routeFile      = $route_path.'+page.server.php';
               $this->routeFileExist = is_file($this->routeFile);
               $this->log('     route file found');
          }
          else {
               $this->log('     ! route not found. check for grouped paths');
               $this->routeFiles = $this->scandir_withGroupedRoutes() ?? [];

          }
          //$this->log("     ".count($this->routeFiles)." +.php file matches");
          // -- why did we replace /api/ with / again?
          // -- $zp_routeBase  = str_replace('/api/', '/', $$this->routeBase);
     }

     /**
      * Scan for SvelteKits Grouped-Routes. e.g. (admin)/**
      */
     function scandir_withGroupedRoutes() {
          // currently 1-level is supported
          $this->log('  -- scandir_withGroupedRoutes()');
          $grouped_paths = zp_scandir($this->routeBase, '^\(.*\)$');
          foreach ($grouped_paths as $_) {
               $route_path = $this->routeBase . $_ . $this->route;
               if (is_dir($route_path)) {
                    $real_route = $_ . $this->route;
                    $this->log("     route match $real_route");
                    $this->log("     route path  $route_path");
                    $this->routeFiles = zp_scandir($route_path, '^\+.*\.php$');
                    if ($this->routeFiles) {
                         // For now, only support +page.server.php
                         $this->routeFile      = $route_path.'+page.server.php';
                         $this->routeFileExist = is_file($this->routeFile);
                         $this->log('     +page.server.php found in grouped route');
                    }
                    else {
                         $this->log('     ! no +.php files found in grouped route');
                    }
                    break;
               }
          }
     }

     function exec_route() {}

}

?>
