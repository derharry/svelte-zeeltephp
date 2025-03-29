<?php

      function load() {
            global $zpAR;
            return [
                  'message' => '+page.server.php/load()/$zpAR',
                  'zpAR' => $zpAR,
                  'request' => [
                        '$_GET' => $_GET,
                        '$_POST' => $_POST,
                        '$_REQUEST' => $_REQUEST
                  ]
            ];
            return $zpAR;
      }

      function actions($action, $value) {
            global $zpAR;
            return [
                  'message' => '+page.server.php/actions()/$zpAR',
                  'zpAR' => $zpAR,
                  'request' => [
                        '$_GET' => $_GET,
                        '$_POST' => $_POST,
                        '$_REQUEST' => $_REQUEST
                  ]
            ];

            return $zpAR;
      }

?>