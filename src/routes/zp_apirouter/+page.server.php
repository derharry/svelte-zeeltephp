<?php

      $jsonResponse->ok = true;
      $jsonResponse->data = $zpAR;

      function OR_use_load() {
            global $jsonResponse, $zpAR;
            $jsonResponse->ok = true;
            $jsonResponse->data = $zpAR;

      }

/*

      function load() {
            global $jsonResponse;
            $jsonResponse->data = $zpAR;
            return false;
      }
            */
      //$jsonResponse->ok = true;
      //$jsonResponse->code = 69;
      //$jsonResponse->data = $zpAR;
      //$jsonResponse->message = 'Hello :-)';
      //$jsonResponse->error   = 'None :-)';

?>