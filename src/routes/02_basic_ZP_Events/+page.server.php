<?php


      function actions($action, $value) {
            try {
                  global $db, $zpAR;

                  switch ($action) {
                        case '?/getZPAR': 
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