<?php

      function load() {
            global $jsonResponse, $action, $server, $db, $env;
            // do anything
            $jsonResponse->data = [
                  'res_php1' => 'Hello from PHP/home'
            ];
            $jsonResponse->ok = true;
      }


      function actions() {}

?>
