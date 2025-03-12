<?php

      // rename to load when response via $jsonResponse
      function load() {
            global $jsonResponse;
            // do anything
            $jsonResponse->ok = true;
            $jsonResponse->data = [
                  'res_pageserver' => 'Hello from /zp_phproutes/+page.server.php',
                  'res_phplib'     => lib_static(),
                  'res_phplibsub'  => lib_sub_static(),
                  'res_phpzplib'   => lib_zplib()
            ];
      }


      // rename to load when response for this way
      function load2() {
            // do anything
            return [
                  'ok'   => true,
                  'data' => [
                        'res_pageserver' => 'Hello from /zp_phproutes/+page.server.php',
                        'res_phplib'     => lib_static(),
                        'res_phplibsub'  => lib_sub_static(),
                        'res_phpzplib'   => lib_zplib()
                  ],
            ];
      }

      // test GET actions by changing the browser-URL 
      function actions($action, $value) {
            switch ($action) {
                  default:
                              return [
                                    'data' => 'Given action: '+ $action
                              ];
                        break;
            }
      }

?>
