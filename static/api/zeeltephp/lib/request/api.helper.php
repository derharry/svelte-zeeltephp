<?php


     function read_json_input() {
          $data = file_get_contents('php://input');
          $data = json_decode($data, true);
          return $data;
     }

     function getAllRequestHeaders() {
          $data = [];
          foreach (getallheaders() as $name => $value) {
               $data[] = [$name => $value];
          }
          return $data;
     }


?>