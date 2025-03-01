<?php

      function load() {
            global $jsonResponse, $action, $server, $db, $env;
            // do anything
            $jsonResponse->data = [
                  'res_php'       => 'Hello from PHP/test',
                  'res_phpstatic' => lib_static_sub(),
                  'res_phpzplib'  => lib_zplib()
            ];
            $jsonResponse->ok = true;
      }


      function actions() {}

?>
