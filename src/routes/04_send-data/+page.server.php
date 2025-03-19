<?php


      function load() {
            global $jsonResponse, $zpAR;
            // do anything
            $jsonResponse->data = [
                  'load'       => 'Hello from load() send-form'
            ];
            $jsonResponse->ok = true;
      }


      function actions($action, $value) {
            global $jsonResponse, $zpAR;
            // do anything

            switch ($action) {
                  case '?/yeah':
                              $jsonResponse->ok      = true;
                              $jsonResponse->message = 'Hello from actions send-form : '.$action.' '.$value;
                              $jsonResponse->data    = $zpAR->data;
                              return true;
                        break;
                  case '?/sendform':
                              return [
                                    'ok' => true,
                                    'message' => 'Hello from actions send-form : '.$action.' '.$value,
                                    'data' => $_POST,
                              ];
                        break;
                  default:
                              return [
                                    'message' => 'Hello from actions send-form DEFAULT : '.$action,
                                    'data' => $zpAR->data
                              ];
                        break;
            }
      }

      function action_btnSend($value) {
            global $jsonResponse, $zpAR;
            // do anything
            $jsonResponse->data = [
                  'ok'   => true,
                  'message' => 'Hello from action_btnSend #'.$value,
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
                  'message' => 'Hello from action_goGo #'.$value,
                  'data' => $_POST,
            ];
      }

?>
