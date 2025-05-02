<?php
# example /src/routes/+page.server.php

      function load() {
            return [
                  'message' => 'Hello from /+page.server.php LOAD()',
                  '$_GET'   => lip_example()
            ];
      }

?>