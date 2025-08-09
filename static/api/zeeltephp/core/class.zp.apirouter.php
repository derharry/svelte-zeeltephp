<?php namespace ZeeltePHP\Core;

use function ZeeltePHP\Error\log_debug;
use function ZeeltePHP\Lib\IO\scan_dir;
use function ZeeltePHP\Lib\IO\scan_dir_recursive_up;

/**
 * ZP_ApiRouter for PHP environment.
 * Parses requests from Svelte's ZP_ApiRouter and checks for +.php files.
 */
class ZP_ApiRouter
{

     //#region Svelte/SSR Routing Properties

          /** @var string|null Route string (e.g. /foo/bar/) */
          public $route = null;

          /** @var string|null Action string (e.g. ?/ACTION) */
          public $action = null;

          /** @var mixed Action value */
          public $value = null;

          /** @var mixed Additional data */
          public $data = null;

     //#endregion

     //#region PHP SSR specific

          /** @var string Environment name ('development', 'library', 'self-development') */
          public $environment = ZP_ENV;          
          
          /** @var string Environment name ('development', 'library', 'self-development') */
          public $context = null;

          /** @var string HTTP request method */
          public $method = null;

          /** @var string|null used content-type */
          public $contentType = null;

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
               log_debug($msg);
     }


     /**
      * Constructor: Initializes the router, parses the request, and collects +.php files.
      * @param array $env    Environment variables from .env or auto-generated.
      * @param bool  $debug  active debug to get all messages in $dbg_msgs;
      */
     function __construct($env, $debug = true) {
          global $zpTime;
          $zpTime->start('ZP_ApiRouter()');
          $this->log('ZP_ApiRouter()');

          $this->debug = $debug;
          $this->routeBaseApi = isset($env['PUBLIC_ZEELTEPHP_BASE']) ?? '/'; //--? $env['PUBLIC_ZEELTEPHP_BASE'] : '/';
          $this->contentType  = $_SERVER['CONTENT_TYPE'] ?? null;

          // 1.0.4 support for CLI/php.exe
          $this->decode_request_type();
          $this->fetch_zp_request();

          // 1.0.4 force using header ith X-ZPC-api,page context requests.
          //if (!defined('ZP_CONTEXT')) {
          $this->context = $this->context ?? ZP_CONTEXT;
          $this->route   = $this->route   ?? ZP_ROUTE;
          if (!$this->context) {
               $this->context = 'page';
               $this->log('  ! unsupported context !');
               //return;
          }

          $this->collect_plusServerFilesInRoute($env['BASE']);  // PUBLIC_BASE now just BASE (whitelisted)
          
          //log_debug($this->dbg_msgs);
          log_debug($zpTime->endN('ZP_ApiRouter()'));
          $this->log('//ZP_ApiRouter()');

     }

     /**          
      * 1.0.4 support for CLI/php.exe
      * new logic for decode/parse zpRequest
      */
     function decode_request_type() {
          try {
               $this->log('  decode_request_type()');

               //file_put_contents(PATH_ZPLOG . 'php_env_vars.log', print_r($_SERVER['REQUEST_METHOD'], true));
               $this->method = $_SERVER['REQUEST_METHOD'];
               $this->log('    .method '. $this->method);

               // Check $_GET, $_POST, CLI-stdin/PHP-input, json/form encoded, then unknown
               if ($this->method == 'GET' ) {
                    // default PHP GET 
                    $this->log('    .detected $_GET');
                    if ($_GET && is_array($_GET) && sizeof($_GET) > 0) {
                         $this->log('     .decoded by PHP into $_GET');
                         return;
                    }
               }
               // check $_POST
               if ($_POST && is_array($_POST) && sizeof($_POST) > 0) {
                    // default PHP POST - any default contentType = 'multipart/form-data, application/x-www-form-urlencoded', etc;
                    // nothing to do :-)
                    $this->log('    .decoded by PHP into $_POST');
                    return;
               }
               ////////////
               // fallback read and decode CLI-stdin/PHP-input, json/form encoded, then unknown
               $this->log('    .read rawInput');
               // read CLI-stdin/PHP-input
               $rawInput = null;
               if (php_sapi_name() === 'cli' || isset($_SERVER['ZEELTEPHP_EXE'])) 
                    // 1.0.4 - read CLI php://STDIN; file_get_contents is empty reading stdin.
                    $rawInput = stream_get_contents(fopen('php://stdin', 'r'));               
               else $rawInput = file_get_contents('php://input');
               // decode CLI-stdin/PHP-input
               if ($rawInput) {

                    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                    $json = null;

                    $this->log('    .method-type: '.$contentType);
                    $this->log('    .rawInput: '.$rawInput);
                    
                    function processJsonInput ($jsonInput, $log): bool {
                         $json = json_decode($jsonInput, true);
                         if (is_array($json)) {
                              $log->log('    .json decoded into $_POST, $_REQUEST');
                              $_POST    = $json;
                              $_REQUEST = $_POST;
                              return true;
                         } 
                         $log->log('    .json NOT decoded !');
                         return false;
                    };

                    // json encoded
                    if (stripos($contentType, 'application/json') >= 0 && processJsonInput($rawInput, $this))
                         return;

                    // default browser - 'application/x-www-form-urlencoded'
                    $this->log('    .check default/browser encoding');
                    $trimmed = trim($rawInput);

                    // json encoded 
                    if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}') && processJsonInput($rawInput, $this)) 
                         return;

                    // multipart, form-url-encoded, ..
                    $parsed = $this->parse_zpRequestRawMultipart($rawInput, $contentType);
                    $_POST  = $parsed['post'];
                    $_FILES = $parsed['files'];
                    if (is_array($_POST)  || is_array($_FILES)) {
                         $_REQUEST = $_POST;
                         $this->log('    .decoded multipart into $_POST, $_FILES');
                         return;
                    }
               }
               else {
                    $this->log('    .possible GET()');
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
               $this->log('    .unsupported request-type !');
          }
          finally {
               $this->log('  //decode_request_type()');
          }
     }

     /**
      * Parses the incoming request and sets route, action, value, and data.
      */
     function fetch_zp_request() {
          $this->log('  fetch_zp_request()');
          if (isset($_POST['zpx_route'])) {
               $this->context = $_POST['zpx_context'] ?? $this->context;
               $this->route   = $_POST['zpx_route']   ?? $this->route;
               $this->action  = $_POST['zpx_action']  ?? $this->action;
               $this->value   = $_POST['zpx_value']   ?? $this->value;
               unset($_POST['zpx_context']);  unset($_REQUEST['zpx_context']);
               unset($_POST['zpx_route']);    unset($_REQUEST['zpx_route']);
               unset($_POST['zpx_action']);   unset($_REQUEST['zpx_action']);
               unset($_POST['zpx_value']);    unset($_REQUEST['zpx_value']);
               //$this->data    = $_POST['zpx_data'] ?? $this->data;
               if (isset($_POST['zpx_data'])) {
                    $this->data = $_POST['zpx_data'];
                    unset($_POST['zpx_data']); unset($_REQUEST['zpx_data']);
                    $_POST = $this->data;
               }
               //$_POST = $this->data;
               $_REQUEST = $_POST;
               $this->log('     context '.$this->context);
               $this->log('     route   '.$this->route);
               $this->log('     action  '.$this->action);
               $this->log('     value   '.$this->value);
               $this->log(json_encode($this->data));
          }
          else {
               $this->fetch_zp_request_GET();
          }
          $this->route = str_replace('//', '/', $this->route ?? '');
          // Clean up route slashes
          $this->log('  //fetch_zp_request()');
     }

     /**
      * Parses GET requests for routing.
      * Schema GET ?zp_route[&zp_action][&...params-is-zp_data]
      *   for exec load():     ?/route/&...
      *   for exec action():   ?/route/&?/action&...
      */
     function fetch_zp_request_GET() {
          $this->log('    fetch_zp_request_GET()');
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
          $this->log('    //fetch_zp_request_GET()');
     }

     /**
      * Collects all +server*.php files in the current route path (upwards).
      * @param string $replaceBaseRoute Path prefix to remove from route.
      */
     function collect_plusServerFilesInRoute($replaceBaseRoute) {
          $this->log('  collect_plusServerFilesInRoute()');
          //$this->log('     . replaceBaseRoute : '.$replaceBaseRoute);
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

          if ($this->context == 'api') {
               $this->log('    for +server.php');
               $this->routePath = $routePath;
               $this->routeBase = $routeBase;
               $routeFilesTP = scan_dir_recursive_up($routeBase, '#\+server\.php#');
               #if (is_file($routeBase."/+server.php")) {
                    //$this->log('     @ +server.php '.$routeBase);
                    // api-route will not have grouped routes
               #     $this->routeFiles[] = '+server.php'; #]
          }
          else if ($this->context == 'page') {
               // 'page'
               $this->log('     +page.server.php '.$routeBase);
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
               $routeFilesRP = scan_dir_recursive_up($routeBase, '#\+layout\.server\.#', $route_path_depth);
               $routeFilesTP = scan_dir($routeBase, '#\+page\.server\.#', $route_path_depth);
               if (is_array($routeFilesRP) && sizeof($routeFilesRP) > 0)
                    $routeFiles = array_merge($routeFiles, $routeFilesRP);
          }
          if (is_array($routeFilesTP) && sizeof($routeFilesTP) > 0)
               $routeFiles = array_merge($routeFiles, $routeFilesTP);
          $this->routeFiles = $routeFiles;
          $this->log('  //collect_plusServerFilesInRoute()');
          return;
     }

     /**
      * Scan for SvelteKits Grouped-Routes. e.g. (admin)/**
      */
     function scandir_withGroupedRoutes() {
          // currently 1-level is supported
          $this->log('  -- scandir_withGroupedRoutes()');
          $grouped_paths = scan_dir($this->routeBase, '#^\(.*\)$#');
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

          array_pop($parts);   // Remove last closing --
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
