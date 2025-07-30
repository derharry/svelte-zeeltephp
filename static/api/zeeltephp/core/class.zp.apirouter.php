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
          if (  !empty($headers['X-ZPC-API']) || !empty($headers['x-zpc-api']) ||
              ( !empty($headers['Access-Control-Request-Headers']) && 
                str_contains($headers['Access-Control-Request-Headers'], 'X-ZPC-API') 
              )
          ) {
               $this->context = 'api';
          }
          $this->log('  -- context: '.$this->context);

          // 1.0.4 support for CLI/php.exe
          //    new logic for decode/parse zpRequest
          // -- $this->parse_zpRequest();
          $this->decode_zpRequest();
          $this->parse_zpRequest();
          $this->collect_plusPHPfilesInRoute($env['BASE']);  // PUBLIC_BASE now just BASE (whitelisted)
          
          zp_log_debug($this->dbg_msgs);
          zp_log_debug($zpTime->endN('ZP_ApiRouter()'));
          $this->log('//ZP_ApiRouter()');

     }

     /**          
      * 1.0.4 support for CLI/php.exe
      * new logic for decode/parse zpRequest
      */
     function decode_zpRequest() {
          $this->log('  decode_zpRequest()');
          //file_put_contents(PATH_ZPLOG . 'php_env_vars.log', print_r($_SERVER['REQUEST_METHOD'], true));
          $this->method = $_SERVER['REQUEST_METHOD'];
          $this->log('    -- method '. $this->method);

          if ($_GET && is_array($_GET) && sizeof($_GET) > 0) {
               // default PHP GET 
               // deparse_GET()
               zp_log_debug('deparsed $_GET');
          }
          elseif ($_POST && is_array($_POST) && sizeof($_POST) > 0 && isset($_['zp_route'])) {
               // default PHP POST
               // contentType = 'multipart/form-data, application/x-www-form-urlencoded', etc;
               // nothing to do :-)
               zp_log_debug('deparsed $_POST');
          }
          else {
               // no $_GET or $_POST ? -> fallback read STDIN INPUT
               $rawInput = null;
               if (php_sapi_name() === 'cli' || isset($_SERVER['ZEELTEPHP_EXE'])) {  
                    // 1.0.4 - read CLI php://STDIN
                    // file_get_contents is empty reading stdin.
                    $rawInput = stream_get_contents(fopen('php://stdin', 'r'));
               }
               else {
                    // read input
                    $rawInput = file_get_contents('php://input');
               }
               if ($rawInput) {

                    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                    $json = null;
                    if (stripos($contentType, 'application/json')) {
                         zp_log_debug('rawInput JSON');
                         $json = json_decode($rawInput, true);
                         if (is_array($json)) {
                              zp_log_debug('rawInput JSON YES');
                              $_POST    = $json;
                              $_REQUEST = $_POST;
                         } else 
                              zp_log_debug('rawInput JSON NO !!!!!!!!!!!!!!!');
                    }
                    else {
                         // default browser - 'application/x-www-form-urlencoded'
                         zp_log_debug('  -- rawInput default/browser');
                         $trimmed = trim($rawInput);

                         if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
                              zp_log_debug('    -- JSON');
                              $json = json_decode($rawInput, true);
                              if (is_array($json)) {
                                   zp_log_debug('    -- JSON YES array');
                                   $_POST    = $json;
                                   $_REQUEST = $_POST;
                              } 
                              else $json = null;
                         }
                    }
                    
                    if (is_null($json)) {
                         zp_log_debug('  -- form-urlencoded / multipart');

                         $parsed = $this->parse_zpRequestRawMultipart($rawInput, $contentType);
                         $_POST  = $parsed['post'];
                         $_FILES = $parsed['files'];
                         if (is_array($_POST)) {
                              $_REQUEST = $_POST;
                         }
                    }
               }
               else {
                    $this->log('    -- possible GET()');
                    // Parse current request URI, fill $_GET accordingly
                    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
                    $requestUri = str_replace('/?', '', $requestUri);
                    $this->log("       REQUEST_URI: $requestUri");
                    $uriParts   = explode('&', $requestUri);
                    foreach ($uriParts as $_) {
                         $params = explode('=', $_);
                         $key    = $params[0] ?? null;
                         $value  = $params[1] ?? null;
                         $_GET[$key] = $value;
                         $this->log("       $key = $value");
                    }
               }
          }
          $this->log('  //decode_zpRequest()');
     }

     /**
      * Parses the incoming request and sets route, action, value, and data.
      */
     function parse_zpRequest() {
          $this->log('  parse_zpRequest()');
          if (isset($_POST['zp_route'])) {
               $this->log('    -- $_POST[zproute] '.$_POST['zp_route']);
               $this->route  = $_POST['zp_route']  ?? $this->route;
               $this->action = $_POST['zp_action'] ?? $this->action;
               $this->value  = $_POST['zp_value']  ?? $this->value;
               unset($_POST['zp_route']);  unset($_REQUEST['zp_route']);
               unset($_POST['zp_action']); unset($_REQUEST['zp_action']);
               unset($_POST['zp_value']);  unset($_REQUEST['zp_value']);
               if (isset($_POST['zp_data'])) {
                    $this->data = $_POST['zp_data'];
                    $_POST      = $_POST['zp_data'];
               } 
               $_REQUEST = $_POST;
          }
          else {
               $this->parse_request_GET();
          }
          $this->route = str_replace('//', '/', $this->route ?? '');
          // Clean up route slashes
          $this->log('  //parse_zpRequest()');
     }

     /**
      * Parses GET requests for routing.
      * Schema GET ?zp_route[&zp_action][&...params-is-zp_data]
      *   for exec load():     ?/route/&...
      *   for exec action():   ?/route/&?/action&...
      */
     function parse_request_GET() {
          $this->log('    parse_request_GET()');
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
          $this->log('    //parse_request_GET()');
     }

     /**
      * Collects all +.php files in the current route path (upwards).
      * @param string $replaceBaseRoute Path prefix to remove from route.
      */
     function collect_plusPHPfilesInRoute($replaceBaseRoute) {
          $this->log('  collect_plusPHPfilesInRoute()');
          //$this->log('     @ replaceBaseRoute : '.$replaceBaseRoute);
          if (!$this->route) {
               $this->log(" ! route is missing, i need a route to collect +.php files"); 
               return;
          }

          // Remove base route prefix if present (tmpFix-001)
          $this->route = str_replace($replaceBaseRoute, '', $this->route);

          // v1.0.4 - supporting more +.php files
          $routeFiles   = [];
          $routeFilesTP = [];
          $routePath = $this->route;
          $routeBase = str_replace('//', '/', $this->routeBase."/$routePath");

          // context here?
          if ($this->context == 'api') {
               $this->routePath = $routePath;          
               $this->routeBase = $routeBase;
               $routeFilesTP = zp_scandirRecursiveUp($routeBase, '#\+server\.#');
               #if (is_file($routeBase."/+server.php")) {
                    //$this->log('     @ +server.php '.$routeBase);
                    // api-route will not have grouped routes
               #     $this->routeFiles[] = '+server.php'; #]
          }
          else {
               // 'page'
               $this->log('     @ +page.server.php '.$routeBase);
               if (!is_dir($routeBase)) {
                    // page-route could have grouped routes
                    $routePath = $this->scandir_withGroupedRoutes();
                    $routeBase = str_replace('//', '/', $this->routeBase."/$routePath");
                    if (!is_dir($routeBase)) {
                         $this->log('  No route-path found! '.$routeBase);
                         return;
               }}
               $this->routePath = $routePath;
               $this->routeBase = $routeBase;
               
               $route_path_depth = ($routePath === '//' || $routePath === '/') ? 0 : max(0, count(array_filter(explode('/', $routePath))));
               //$route_path_depth = count(explode('/', $route_path_depth)); // -2; // -2 because of / at start and end
               $routeFilesRP = zp_scandirRecursiveUp($routeBase, '#\+layout\.server\.#', $route_path_depth);
               $routeFilesTP = zp_scandir($routeBase, '#\+page\.server\.#', $route_path_depth);
               if (is_array($routeFilesRP) && sizeof($routeFilesRP) > 0)
                    $routeFiles = array_merge($routeFiles, $routeFilesRP);
          }
          if (is_array($routeFilesTP) && sizeof($routeFilesTP) > 0)
               $routeFiles = array_merge($routeFiles, $routeFilesTP);
          $this->routeFiles = $routeFiles;
          $this->log('  //collect_plusPHPfilesInRoute()');
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

     /**
      * 1.04 support for CLI/php.exe
      * 
      */
     function parse_zpRequestRawMultipart($input, $contentType) {
          $this->log('  parse_zpRequestRawMultipart()');
          $result = ['post' => [], 'files' => []];

          if (!preg_match('/boundary=(.*)$/', $contentType, $matches)) {
               return $result; // No boundary found
          }
          $boundary = $matches[1];
          $parts = explode('--' . $boundary, $input);

          array_pop($parts); // Remove last closing --
          array_shift($parts); // Remove preamble if any

          foreach ($parts as $part) {
               if (empty(trim($part))) continue;

               list($rawHeaders, $body) = preg_split("/\R\R/", $part, 2);
               $headers = [];
               foreach (explode("\r\n", $rawHeaders) as $headerLine) {
                    $headerParts = explode(":", $headerLine, 2);
                    if (count($headerParts) == 2)
                         $headers[strtolower(trim($headerParts[0]))] = trim($headerParts[1]);
               }

               if (!isset($headers['content-disposition'])) continue;

               preg_match('/name="([^"]+)"/', $headers['content-disposition'], $nameMatch);
               $name = $nameMatch[1] ?? null;
          
               preg_match('/filename="([^"]*)"/', $headers['content-disposition'], $filenameMatch);
               $filename = $filenameMatch[1] ?? null;

               if ($filename) {
                    $tmpName = tempnam(sys_get_temp_dir(), 'php');
                    file_put_contents($tmpName, rtrim($body, "\r\n"));
                    $result['files'][$name] = [
                         'name' => $filename,
                         'type' => $headers['content-type'] ?? '',
                         'tmp_name' => $tmpName,
                         'error' => 0,
                         'size' => strlen($body),
                    ];
               } else {
                    $result['post'][$name] = rtrim($body, "\r\n");
               }
          }     

          $this->log('  //parse_zpRequestRawMultipart()');
          return $result;
     }

}

?>
