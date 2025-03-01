<?php

class ZP_ServerDetails {

      public $environment = 'build';
      public $routeFile   = null;
      public $error       = null;

    
      function __construct() {
            try {
                  $action = new ZP_ActionDetails();
                  //var_dump($action->route);

                  $path_file = $action->route .'/+page.server.php';

                  // include the route/main()
                  //   1st within /src/routes/...
                  //   2nd within /static/api/routes/...
                  //   3rd within /api/routes/...
                  $path_src   = '../../src/routes/'.$path_file;
                  $path_api   = 'routes/'.$path_file;
                  $path_build = 'routes/'.$path_file;

                  //var_dump($path_src);
                  //var_dump($path_api);
                  //var_dump($path_build);

                  //check build mode or dev mode (src/routes/..)
                  if (is_file('zeeltephp/.env')) {
                        $this->environment = 'build';
                        $this->routeFile   = $path_build;
                  } else if (is_file($path_src)) {
                        $this->environment = 'dev-src';
                        $this->routeFile   = $path_src;
                  } else if (is_file($path_api)) {
                        $this->environment = 'dev-api';
                        $this->routeFile   = $path_api;
                  }
                  // moved to if (is_file('zeeltephp/.env')) {
                  //else if (is_file($path_build)) {
                  //      $this->routeFile   = $path_build;
                  //      $this->environment = 'build';
                  //} 
                  else {
                        $this->environment = 'unknown';
                        $this->error       = 'ZP: Could not find set Environment and not found the route/+page.server.php';
                  }
            }
            catch (Exception $exp) {
                  error_log($exp);
            }
      }
}


// function $serverFile = zp_getRouteActionFile($action);



?>