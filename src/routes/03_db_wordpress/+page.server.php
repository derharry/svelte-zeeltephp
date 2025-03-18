<?php

      function load() {
            try {
                  global $db, $zpAR;

                  // do some db request
                  //$x = og_getCompanyListByParentSlug('advocaten');
                  //error_log($x);
                  return [
                        'success' => true,
                        'data'    => ['xxxx'],
                        'message' => 'Hello from 03_db_wordpress/+page.server.php/load()'
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
                        case '?/testWPDB': 
                                    // this case won't  because action_testWPDB() is declared as function
                                    // rename that function to like action_testWPDB_off() then this case will work.
                                    return [
                                          'ok'   => true,
                                          'data' => select_from_db().' and '.$value
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


      function action_testWPDB_($value) {
            return 'Value of button is: '.$value;
      }

/*



      function actions($action) {
            try {
                  global $db, $zpAR;

                  switch ($action) {
                        case 'testWPDB': 
                                    // this case won't  because action_testWPDB() is declared as function
                                    // rename that function to like action_testWPDB_off() then this case will work.
                                    return [
                                          'ok'   => true,
                                          'data' => select_from_db()
                                    ];
                              break;
                  }
            } catch (\Throwable $th) {
                  error_log($th);
                  return [
                        'ok' => false,
                        'error' => $th->getMessage()
                  ];
            }
      }

      function action_testWPDB($value) {
            try {
                  global $db, $zpAR;
                  
            } catch (\Throwable $th) {
                  error_log($th);
                  return [
                        'ok'    => false,
                        'error' => $th->getMessage()
                  ];
            }
      }


      function og_getCompanyListByParentSlug( $slug ) {
            global $db, $jsonResponse;
            $results = [];
            try {
                  $sql = "
                        SELECT * FROM og_companies
                  ";
                  $sql     = str_replace('%%', $slug, trim($sql));
                  $results = $db->query($sql);
                  $jsonResponse->message = $sql;
                  //$results = $db->wpdb->get_results( $sql, 'ARRAY_A' );
                  error_log($sql);
            } catch (\Throwable $th) {
                  throw $th;
            }
            return $results;
      }
*/
?>