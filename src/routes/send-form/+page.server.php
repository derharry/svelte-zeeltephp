<?php

      function load() {
            global $jsonResponse;
            // do anything
            $jsonResponse->data = [
                  'zp_send'       => 'Hello from ZP_send-form_load',
            ];
            $jsonResponse->ok = true;
      }


      function actions($action, $value) {
            global $jsonResponse, $zpAR;
            // do anything
            $jsonResponse->data = [
                  //'zpAR'     => $zpAR,
                  'data'     => 'Hello from ZP_send-form_actions '.$action.' '.$value,
            ];

            switch ($action) {
                  case 'btnSend':
                        $jsonResponse->data = [
                              'data' => 'Hello from send-form/actions/'.$action.' '.$value,
                              //'data2' => $_GET
                        ];
                        break;
            }
            
            $jsonResponse->ok = true;
      }

      function action_btnSend2($value) {
            global $jsonResponse, $zpAR;
            // do anything
            $jsonResponse->data = [
                  //'zpAR'     => $zpAR,
                  'zp_action'     => 'Hello from send-form/action_btnSend #'.$value,
                  //'data2' => $_GET
            ];
            $jsonResponse->ok = true;
      }

?>
