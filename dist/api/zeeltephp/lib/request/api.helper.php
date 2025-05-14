<?php


     /**
      * Read JSON from phpInputStream and load it into $_POST, $_REQUEST
      */
     function read_json_input() {
          try {
               zp_log_debug('read_json_input()');
               // get rawInputStream
               $rawInput = file_get_contents('php://input');
               // - not required $rawInput = utf8_encode($rawInput);
               // read JSON
               $json = json_decode($rawInput, true);
               // put the JSON into $_POST and $_REQUEST
               $_POST = $json;
               $_REQUEST = $json;
               zp_log_debug('  --rawJSONInput      : '.$rawInput);
               zp_log_debug('  --$_POST, $_REQUEST : json=Array ');
               zp_log_debug('//read_json_input()');
               return $json;
          }
          catch (\Exception $exp) {
                    zp_log_debug('//read_json_input() !!'.json_last_error());
                    zp_handle_error($exp);
          }
          return null;
     }

     function getAllRequestHeaders() {
          $data = [];
          foreach (getallheaders() as $name => $value) {
               $data[] = [$name => $value];
          }
          return $data;
     }

     /**
      *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
      *  origin.
      *
      *  In a production environment, you probably want to be more restrictive, but this gives you
      *  the general idea of what is involved.  For the nitty-gritty low-down, read:
      *
      *  - https://developer.mozilla.org/en/HTTP_access_control
      *  - https://fetch.spec.whatwg.org/#http-cors-protocol
      *  - SOURCE: https://stackoverflow.com/questions/8719276/cross-origin-request-headerscors-with-php-headers
      */
     function cors() {
     
          // Allow from any origin
          if (isset($_SERVER['HTTP_ORIGIN'])) {
               // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
               // you want to allow, and if so:
               header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
               header('Access-Control-Allow-Credentials: true');
               header('Access-Control-Max-Age: 86400');    // cache for 1 day
          }
          
          // Access-Control headers are received during OPTIONS requests
          if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
          
               if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                    // may also be using PUT, PATCH, HEAD etc
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
               
               if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
          
               exit(0);
          }
          
          echo "You have CORS!";
     }


?>