<?php
# example /src/routes/zpexample/+page.server.php

      function load() {
            return [
                  'message' => 'Hello from your-project/src/routes/zpexample/+page.server.php/LOAD()',
                  'zplib-method' => lip_example(),
                  '$_REQUEST'   => $_REQUEST
            ];
      }

?>