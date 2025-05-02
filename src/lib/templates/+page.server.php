<?php

      function load() {
            global $zpAR;
            return [
                  'message' => 'Hello from /+page.server.php LOAD()',
                  '$_GET'   => $_GET,
                  'zpAR'    => $zpAR
            ];
      }


      function actions($action, $value) {
            global $zpAR;
            return [
                  'message' => 'Hello from /+page.server.php actions('.$action.', '.$value.')',
                  'data'    => $_GET,
                  'zpAR'    => $zpAR
            ];
      }

?>