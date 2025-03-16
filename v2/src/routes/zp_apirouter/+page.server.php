<?php


      function load() {
            global $jsonResponse, $zpAR;
            //$jsonResponse->ok = true;
            //$jsonResponse->message = 'load';
            return true;
      }
      function actions($value) {
            global $jsonResponse, $zpAR;
            $jsonResponse->ok = false;
            $jsonResponse->message = 'actions';
            return $zpAR;
      }

      function action_example1_($value) {
            return [
                  'data' => 'hello from action_example1('.$value.')'
            ];
      }

      function action_example2($value) {
            return 'hello from action_go('.$value.')';
      }

      function action_example3($value) {
            return 'tbd';
      }
      
      function action_GoGo($value) {
            global $jsonResponse, $zpAR;
            $zpAR->data = "Hello from action_GoGo";
            return [
                  'ok' => false,
                  'data' => $zpAR
            ];
            //$jsonResponse->ok   = true;
            //$jsonResponse->data = $zpAR;
            //return true;
      }


?>