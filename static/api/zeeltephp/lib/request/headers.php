<?php

/**
* 
*/
function set_request_headers_to_allow_from_anywhere() {
     header("Access-Control-Allow-Origin: *");
     //header("Access-Control-Allow-Origin: http://localhost:5173"); # should be with *
     header("Access-Control-Allow-Methods: POST");
     header("Access-Control-Allow-Headers: Content-Type");
}


function set_request_headers_to_json_api() {
     /**
      * API for Svelte-static NLVWenen Golden Pages
      */
     // set default headers for JSON response
     header("Access-Control-Allow-Origin: *");
     header('Content-Type: application/json; charset=utf-8');
     if (true) {
          /*
          To allow CORS (Cross-Origin Resource Sharing) in your PHP script, you can use the header() function to set the appropriate headers. You’ve already included a header to allow all origins (*), which is a good start.
          Here’s a basic example of how you can set up CORS in your PHP script:
          This script responds to OPTIONS requests with the appropriate Access-Control-Allow-* headers, allowing the browser to make the actual request (which could be a GET, POST, etc.).
          Please replace this code with your existing PHP script and see if it resolves the issue. If the problem persists, you might need to check your server configuration or consult with your hosting provider, as some hosts may not allow modification of these headers in the script itself.
          Remember, allowing all origins (*) is generally not recommended for production applications due to security concerns. It’s better to specify the exact origins that should be allowed.
          */
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
          }
     }
}


?>