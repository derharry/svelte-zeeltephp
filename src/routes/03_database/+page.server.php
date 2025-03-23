<?php

      function load() {
            try {
                  global $db, $zpAR, $env;

                  // do some db request
                  //$x = og_getCompanyListByParentSlug('advocaten');
                  //error_log($x);
                  return [
                        'success' => true,
                        'data'    => ['xxxx'],
                        'message' => 'Hello from 03_db_wordpress/+page.server.php/load()',
                        'ZEELTEPHP_DATABASE_URL' => $env['ZEELTEPHP_DATABASE_URL']
                  ];
            } catch (\Throwable $th) {
                  zp_error_handler($th);
                  return [
                        'ok' => false,
                        'error' => $th->getMessage()
                  ];
            }
      }

      function actions($action, $value) {
            try {
                  global $db, $zpAR;

                  switch ($action) {
                        case 'test_db_query': 
                                    $sql = $value;
                                    $res = $db->query($sql);
                                    return [
                                          'data'  => $res,
                                          'zpar'  => $zpAR,
                                          'zpDB'  => $db
                                    ];
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