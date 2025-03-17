<?php

      function load() {
            try {
                  global $db, $zpAR;

                  $x = og_getCompanyListByParentSlug('advocaten');
                  error_log($x);

                  return [
                        'ok' => true,
                        'data' => $x,
                        'message' => 'Hello from PHP'
                  ];
                  
            } catch (\Throwable $th) {
                  error_log($th);
            }
      }


      function actions($action) {
            try {
                  global $db, $zpAR;

                  return [
                        'ok' => true,
                        'data' => 'Hello from PHP'
                  ];
                  
            } catch (\Throwable $th) {
                  error_log($th);
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

?>