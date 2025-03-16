<?php

      $jsonResponse->ok = true;
      $jsonResponse->data = $zpAR;

      function OR_use_load() {
            global $jsonResponse, $zpAR;
            $jsonResponse->ok = true;
            $jsonResponse->data = $zpAR;

      }
      
      function action_GoGo($value) {
            global $jsonResponse, $zpAR;
            $zpAR->data = "Hello from action_GoGo";
            return [
                  'ok' => true,
                  'data' => $zpAR
            ];
            //$jsonResponse->ok   = true;
            //$jsonResponse->data = $zpAR;
            //return true;
      }


?>