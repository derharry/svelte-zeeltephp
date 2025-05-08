<?php

      // rename to load when response via $jsonResponse
      function load() {
            global $zpAR;
            // do anything
            return [
                  '+page.server.php'  => 'Hello from load() /01_basics/+page.server.php',
                  'api/lib'     => lib_static(),
                  'api/lib/sub' => lib_sub_static(),
                  'zpAR'        => $zpAR
            ];
      }

      function actions($action, $value) {
            global $zpAR;
            $return_data = '';
            sleep(2);
            switch ($action) {
                 case '?/foo':
                      $return_data = 'hello from actions in 01/basics/+page.server.php';
                      break;
                 case 'default':
                      //mixing default and named Actions is not supported in SvelteKit
                      break;
            }
            return  [
                 'message' => 'Hello from actions in 01_basics/+page.server.php',
                 'zpAR'    => $zpAR
            ];
       }

?>