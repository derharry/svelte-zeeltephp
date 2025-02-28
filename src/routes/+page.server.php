<?php

      function load() {
            global $jsonResponse, $action, $server, $db, $env;
            // do anything
            $jsonResponse->data = [
                  'res_php' => 'Hello from PHP/home'
            ];
            $jsonResponse->ok = true;
      }


      function actions() {}

?>
