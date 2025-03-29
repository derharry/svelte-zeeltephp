<?php

      function load() {
            global $zpAR;
            return [
                  'zpAR' => $zpAR,
                  'message' => 'Hello from load()',
                  'data' => $_POST,
            ];
      }


      function actions($action, $value) {
            global $jsonResponse, $zpAR;
            // do anything

            switch ($action) {
                  case 'sendJsonData':
                              return [
                                    'zpAR' => $zpAR,
                                    'message' => 'Hello from actions/'.$action.':'.$value,
                                    'data' => $_POST,
                              ];
                        break;
                  case 'sendFormData':
                              return [
                                    'zpAR' => $zpAR,
                                    'message' => 'Hello from actions/'.$action.':'.$value,
                                    'data' => $_POST,
                              ];
                        break;
            }  
            return [
                  'zpAR' => $zpAR,
                  'message' => 'Hello from actions/DEFAULT/'.$action.':'.$value,
                  'data' => $_POST,
            ];
      }


?>
