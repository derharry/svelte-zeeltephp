<?php

      // rename to load when response via $jsonResponse
      function load() {
            global $jsonResponse;
            // do anything
            return [
                  '+page.server.php'  => 'Hello from /01_basics/+page.server.php',
                  'api/lib'     => lib_static(),
                  'api/lib/sub' => lib_sub_static(),
                  '$lib/zpi'    => lib_zplib_example()
            ];
      }


      function actions($action, $value) {
            try {
                  global $db, $zpAR;

                  switch ($action) {

                        case '?/getZpApiRouterPHP': 
                                          return $zpAR;  
                                    break;

                        default:
                                    return 'default actions';
                              break;
                              
                  }
            } catch (\Throwable $th) {
                  zp_error_handler($th);
                  return [
                        'ok' => false,
                        'error' => $th->getMessage()
                  ];
            }
      }



?>