<?php


function zp_log($content) {
      if (is_array($content) || is_object($content)) $content = json_encode($content, JSON_PRETTY_PRINT, 3);
      error_log($content."\n", 3, 'zeeltephp/log/log.log');
 }

 function zp_log_debug($content) {
      global $debug;
      if ($debug) 
           error_log($content."\n", 3, 'zeeltephp/log/debug.log');
 }


 function zp_error_handler($error) {
      $errorText = [];
      $errorText[] = strstr($error->getFile(), "\src").':'; # strip all before \src (full-path) to get only the relative-path 
      $errorText[] = $error->getLine();
      $errorText[] = $error->getMessage();
      //$errorText[] = $error->getCode(); // 0? 
      //$errorText[] = 'at'; 
      //$errorText[] = $error->getPrevious();      // empty - hoped for parent function name 
      //$errorText[] = $error->getTraceAsString(); // not interesting 
      $errorText[] = "\n";
      $errorText   = implode(' ',$errorText);
      zp_log_debug(' !! zp_error_handler() '.$errorText);
      error_log($errorText, 3, 'zeeltephp/log/error.log');
 }


?>