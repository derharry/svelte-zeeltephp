<?php

      function load() {
            global $jsonResponse, $action, $server, $db, $env;
            // do anything
            $jsonResponse->data = [
                  'res_php2' => 'Hello from PHP/test'
            ];
            $jsonResponse->ok = true;
      }


      function actions() {}

?>
