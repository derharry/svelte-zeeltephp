<?php

/*
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
                  case '?/send':
                              $jsonResponse->ok      = true;
                              $jsonResponse->message = 'Hello from actions send-form : '.$action.' '.$value;
                              $jsonResponse->data    = $_GET;
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
                                    'message' => 'Hello from actions/default send-form : '.$action,
                                    'data' => $zpAR.data,
                              ];
                        break;
            }
      }

      function action_btnSend($value) {
            global $jsonResponse, $zpAR;
            // do anything
            $jsonResponse->data = [
                  'msg'     => 'Hello from action_btnSend #'.$value,
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
*/
?>
