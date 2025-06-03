<?php

     /**
      * Translates error codes to human-readable messages and related info.
      *
      * @param int|string $code Error code or message.
      * @return array [code, message, filename, errorTypeName] or [null, null, null] if not found.
      */
     function zp_errorMessageTranslation($code) {
          global $zpAR;
          // code [ message , filename ]
          // -- todo : add also custom errorTypeNames
          $zp_route  = $zpAR->routeFile ?? '-?-';
          $zp_action = $zpAR->action    ?? '-?-';
          $zeeltephp_errors = [
               400 => ['no route requested', ''],  // bad request
               404 => ['no +.php in route', ''],   // not found
               801 => ['no load() ', $zp_route],
               802 => ["no action( $zp_action )", $zp_route],
          ];
          if (isset($zeeltephp_errors[$code])) 
               return [ $code, ...$zeeltephp_errors[$code] ];
          return [null, null, null];
     }

     /**
      * Collects information about the error including zp_errorMessageTranslation()
      * Converts full file-system paths to relative for readability.
      */
     function zp_errorDetails($e, $message = null, $code = null) {
          // Extract error details
          $e_type     = is_object($e) ? get_class($e) : '';
          $e_message  = $e->getMessage();
          $e_previous = $e->getPrevious();
          $e_code     = $e->getCode();
          $e_file     = $e->getFile();
          $e_line     = $e->getLine();
          // ! Do not expose 'trace' (security) => $e->getTrace(),
          // ! Trace as string is ugly anyway   => $e->getTraceAsString()

          // Translate error codes/messages
          [$zp_code, $zp_msg, $zp_file] = zp_errorMessageTranslation($e_message);
          [$cc_code, $cc_msg, $cc_file] = zp_errorMessageTranslation($code);
          
          // Determine final values, preferring provided arguments, then translations, then defaults
          $e_code    = $code    ?? $zp_code ?? $cc_code ?? ($e_code ?: 500);
          $e_message = $message ?? $zp_msg  ?? $cc_msg  ?? $e_message;
          $e_file    = $zp_file ?? $cc_file ?? $e_file;
          
          // Short filename:line for quick reference
          $short = basename($e_file);
          if ($e_line) $short .= ":$e_line";

          // Prepare error response
          $errorJsonRespone = [
               'ok'       => false,
               'error'    => $e_type,
               'code'     => $e_code,
               'message'  => $e_message,
               'short'    => $short,
               'file'     => $e_file,
               'line'     => $e_line,
               'previous' => $e_previous,
               // for debugging:
               //'cpPath'   => PATH_CPROOT,
               //'zpAR'     => $zpAR,
               //'zpDB'     => $db,
          ];

           // Encode to JSON and clean up full paths
          $json = json_encode($errorJsonRespone);
          $json = zp_change_full_paths_to_zp_relative($json);

          return $json;
     }


     /**
      * Handles all ZeeltePHP errors and exceptions, outputs as JSON, and logs the error.
      * Converts full file-system paths to relative for readability.
      *
      * @param Exception|Error $e       The error or exception object.
      * @param string|null     $message Optional custom error message.
      * @param int|null        $code    Optional custom error code.
      */
     function zp_handle_error($e, $message = null, $code = null) {
          global $zpAR;

          // Prepare Error
          $json = zp_errorDetails($e, $message, $code);
          
          // Log error
          error_log($json . "\n", 3, PATH_ZPLOG.'error_zp.log');
          
          // Output error JSON
          echo $json;
     }

     /**
      * Write a log entry to /php_log/error.log.
      *
      * @param mixed $content String or data to log.
      */
     function zp_log_error($e, $message = null, $code = null) {
          // Prepare Error
          $json = zp_errorDetails($e, $message, $code);
          // Log error
          error_log($json . "\n", 3, PATH_ZPLOG.'error.log');
     }

     /**
      * Write a log entry to /php_log/log.log.
      *
      * @param mixed $content String or data to log.
      */
     function zp_log($content) {
          // if $content is not string - convert to json
          if (is_array($content) || is_object($content)) 
               $content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES, 3);
          error_log($content."\n", 3, PATH_ZPLOG.'log.log');
     }

     /**
      * Write a debug log entry to /php_log/zp_debug.log.
      *
      * @param mixed $content  String or data to log.
      * @param bool  $restart  If true, restart (truncate) the log file.
      */
     function zp_log_debug($content, $restart = false) {
          if (defined('ZP_DEBUG') && ZP_DEBUG !== true) return;  // exit when dugging not activated
          $file = PATH_ZPLOG."zp_debug.log";  // set the file name
          
          // Restart log if requested
          if ($restart && is_file($file)) {
               file_put_contents($file, date("Y-m-d H:i:s")."\n");
               // -- unlink($file);  // delete file
               // -- usleep(500000); // 500ms to avoid race conditions
               // -- sleep(1);     // almost no 'permission-denied' at race requests but 1sec is too slow for UI.
               // -- touch($file);   // create file
               // -- error_log(date("Y-m-d H:i:s")."\n", 3, $file);
          } else if (!is_file($file)) {
               touch($file);   // create file
          }

          // if $content is not a string - convert to json (for now)
          if (is_array($content) || is_object($content)) 
               $content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES, 3);

          error_log($content."\n", 3, $file);
     }



?>