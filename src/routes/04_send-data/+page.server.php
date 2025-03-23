<?php


      function actions($action, $value) {
            global $jsonResponse, $zpAR;
            // do anything

            switch ($action) {
                  case 'sendJsonData':
                              return [
                                    'ok'      => true,
                                    'message' => 'Hello from actions send-form: '.$action.' '.$value,
                                    'data'    => $zpAR->data,
                                    'debug'   => [
                                          'zpAR' => $zpAR
                                    ],
                              ];
                        break;
                  case 'sendFormData':
                              return [
                                    'ok'      => true,
                                    'message' => 'Hello from actions send-form: '.$action.' '.$value,
                                    'data'    => $_POST,
                                    'debug'   => [
                                          'zpAR' => $zpAR
                                    ],
                              ];
                        break;
                  default:
                              return [
                                    'ok' => true,
                                    'message' => 'Hello from actions send-form DEFAULT : '.$action,
                                    'data' => $zpAR->data
                              ];
                        break;
            }
      }


?>
