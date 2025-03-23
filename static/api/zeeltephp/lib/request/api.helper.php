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
                    zp_error_handler($exp);
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


?>