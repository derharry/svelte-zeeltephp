<?php 

     function load() {
          global $zpAR;
          return  [
               'message' => 'Hello from load() /+page.server.php',
               'zpAR'    => $zpAR
          ];
     }

     function actions($action, $value) {
          global $zpAR;
          return  [
               'message' => 'Hello from actions() /+page.server.php',
               'zpAR'    => $zpAR
          ];
     }

?>