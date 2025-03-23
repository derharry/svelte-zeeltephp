<?php


     function read_json_input() {
          try {
               zp_log('read_json_input');
               $rawInput = file_get_contents('php://input');
               zp_log('   typeof rawInput: '.gettype($rawInput));
               error_log($rawInput);
               zp_log($rawInput);

               /*
               $data = json_decode(file_get_contents('php://input'), true);
               zp_log('json_last_error()? '.json_last_error());
               zp_log('  data: '.$data);
               return $data;
               /*
               $data = file_get_contents('php://input');
               if ($data == null) {
                    zp_log('no_JSON data received');
                    zp_log(json_last_error());
                    $data = json_decode($data, true);
                    zp_log('error: '.json_last_error());
                    zp_log(gettype($data));
               }
               else {
                    $data = json_decode($data, true);
                    zp_log(gettype($data));
               }
               return $data;
               */
          }
          catch (\Exception $exp) {
                    zp_log('error: '.json_last_error());
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