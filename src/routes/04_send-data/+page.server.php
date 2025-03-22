<?php


      function actions($action, $value) {
            global $jsonResponse, $zpAR;
            // do anything

            switch ($action) {
                  case 'sendJsonData':
                              return [
                                    'ok' => true,
                                    'message'  => 'Hello from actions send-form: '.$action.' '.$value,
                                    'debug' => [
                                          'zpAR' => $zpAR
                                    ],
                                    'jsonData' => $jsonResponse
                              ];
                        break;
                  case 'sendFormData':
                              return [
                                    'ok' => true,
                                    'message'  => 'Hello from actions send-form: '.$action.' '.$value,
                                    'jsonData' => $_REQUEST,
                                    'debug' => [
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
