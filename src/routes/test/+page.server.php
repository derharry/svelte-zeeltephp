<?php

      function load() {
            global $jsonResponse, $action, $server, $db, $env;
            // do anything
            $jsonResponse->data = [
                  'res_php2' => 'Hello from PHP/test',
                  'res_phpt' => lib_test2()
            ];
            $jsonResponse->ok = true;
      }


      function actions() {}

?>
