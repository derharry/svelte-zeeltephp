<?php

      function load() {
            global $db, $zpAR, $env;
            // do some db request
            //$x = companySelect('advocaten');
            //error_log($x);
            return [
                  'success' => true,
                  'data'    => ['xxxx'],
                  'message' => 'Hello from load() /03_database/+page.server.php',
                  'ZEELTEPHP_DATABASE_URL' => $env['ZEELTEPHP_DATABASE_URL']
            ];
      }

      function actions($action, $value) {
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
      }


?>