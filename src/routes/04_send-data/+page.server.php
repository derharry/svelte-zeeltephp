<?php


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
                  default:
                              return [
                                    'zpAR' => $zpAR,
                                    'message' => 'Hello from actions/DEFAULT/'.$action.':'.$value,
                                    'data' => $_POST,
                              ];
                        break;
            }
      }


?>
