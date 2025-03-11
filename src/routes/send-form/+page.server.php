<?php

      function load() {
            global $jsonResponse, $zpAR;
            // do anything
            $jsonResponse->data = [
                  'load'       => 'Hello from ZP_send-form_load'
            ];
            $jsonResponse->ok = true;
      }


      function actions($action, $value) {
            global $jsonResponse, $zpAR;
            // do anything

            switch ($action) {
                  case '?/send':
                              $jsonResponse->data = [
                                    'msg' => 'Hello from send-form/actions/'.$action.' '.$value,
                                    'data' => $_GET,
                              ];
                              return true;
                        break;
                  case '?/sendform':
                                    $jsonResponse->data = [
                                          'msg' => 'Hello from send-form/actions/'.$action.' '.$value,
                                          'data' => $_POST['name'],
                                    ];
                                    return [
                                          'message' => 'Hello from send-form/actions/'.$action.' '.$value,
                                          'data' => $_POST['name'],
                                    ];
                        break;
            }
      }

      function action_btnSend($value) {
            global $jsonResponse, $zpAR;
            // do anything
            $jsonResponse->data = [
                  'msg'     => 'Hello from send-form/action_btnSend #'.$value,
                  'data' => $_GET
            ];
            $jsonResponse->ok = true;
            return true;
      }

      function action_sendform($value) {
            global $jsonResponse, $zpAR;
            // do anything
            return [
                  'ok'   => true,
                  'message' => 'Hello from send-form/action_goGo #'.$value,
                  'data' => $zpAR->data,
            ];
      }

?>
