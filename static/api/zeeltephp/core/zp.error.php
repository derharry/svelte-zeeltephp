<?php namespace ZeeltePHP\Error;

use function ZeeltePHP\Core\Lib\change_full_paths_to_zp_relative;

     /**
      * Translates error codes to human-readable messages and related info.
      *
      * @param int|string $code Error code or message.
      * @return array [code, message, filename, errorTypeName] or [null, null, null] if not found.
      */
     function error_message_translation($code) {
          global $zpAR;
          // code [ message , filename ]
          // -- todo : add also custom errorTypeNames
          $zp_route  = $zpAR->route  ?? '-?-';
          $zp_action = $zpAR->action ?? '-?-';
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
      * Collects information about the error including error_message_translation()
      * Converts full file-system paths to relative for readability.
      */
     function get_event_details($e, $message = null, $code = null) {
          [ $e_type, $e_code, $e_message, $e_short, $e_file, $e_line, $e_previous ] = null;

          // Extract error details from Error or json-fallback
          $e_type     = is_object($e) ? get_class($e) : '';
          if ($e instanceof \Throwable) {
                 $e_code     = $e->getCode();
                 $e_file     = $e->getFile();
                 $e_line     = $e->getLine();
                 $e_message  = $e->getMessage();
                 $e_previous = $e->getPrevious();
                 // uncomment if required :
                 //$e_trace = $e->getTrace()
                 //$e_traceString => $e->getTraceAsString()
          } else $e_message  = json_encode($e);

          // Translate error codes/messages
          [$zp_code, $zp_msg, $zp_file] = error_message_translation($e_message);
          [$cc_code, $cc_msg, $cc_file] = error_message_translation($code);
          
          // Determine final values, preferring provided arguments, then translations, then defaults
          $e_code    = $code    ?? $zp_code ?? $cc_code ?? ($e_code ?: 500);
          $e_message = $message ?? $zp_msg  ?? $cc_msg  ?? $e_message;
          $e_file    = $zp_file ?? $cc_file ?? $e_file;
          
          // e_short filename:line for quick reference
          $e_short = $e_file && basename($e_file);
          if ($e_line) $e_short .= ":$e_line";

          // Prepare error response
          $errorJsonRespone = [
               'time'     => date("Y-m-d H:i:s"),
               'ok'       => false,
               'error'    => $e_type,
               'code'     => $e_code,
               'message'  => $e_message,
               'short'    => $e_short,
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
          $json = change_full_paths_to_zp_relative($json);
          
          return json_decode($json);
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
     function handle_error($e, $message = null, $code = null) {
          global $zpAR;

          // Prepare Error
          $ed = get_event_details($e, $message, $code);
          
          // Log error
          if (is_object($ed)) {
               //error_log($json . "\n", 3, PATH_ZPLOG.'error_zp.log');
               error_log( sprintf('%s %s %s %s:%s %s',
                    $ed->time,
                    $ed->code,
                    $ed->error,
                    $ed->file,
                    $ed->line,
                    $ed->message
               )."\n", 3, PATH_ZPLOG.'error.log');
          }
          else 
          
          // Output error JSON
          echo json_encode($ed);
     }

     /**
      * Write a log entry to /php_log/error.log.
      *
      * @param mixed $content String or data to log.
      */
     function log_error($e, $message = null, $code = null) {
          if (is_string($e)) {
               $content = $e;
          } 
          else {
               // Prepare Error
               $ed = get_event_details($e, $message, $code);
               $content = sprintf('%s %s %s %s:%s %s',
                    $ed->time,
                    $ed->code,
                    $ed->error,
                    $ed->file,
                    $ed->line,
                    $ed->message
               );
          }
          
          // Log error
          //error_log($json . "\n", 3, PATH_ZPLOG.'error_zp.log');
          error_log($content."\n", 3, PATH_ZPLOG.'error.log');
     }

     /**
      * Write a log entry to /php_log/log.log.
      *
      * @param mixed $content String or data to log.
      */
     function log($content) {
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
     function log_debug($content, $indent = 0, $restart = false) {
          if (ZP_DEBUG !== true) return;  // exit when dugging not activated
          $file = PATH_ZPLOG."zp_debug.log";  // set the file name
          
          // Restart log if requested
          if ($restart && is_file($file)) {
               // truncate file - instead unlink() and touch() -> io-speeds. ;-)
               file_put_contents($file, date("Y-m-d H:i:s")."\n");
          } else if (!is_file($file)) {
               touch($file);   // create file // todo: handle @touch Permission denied Exception
          }

          // if $content is not a string - convert to json (for now)
          if (is_array($content) || is_object($content)) 
               $content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES, 3);

          // fallback [int]$indent 
          $indent = is_int($indent) ? $indent : 0;

          error_log( str_repeat(' ', $indent).$content."\n", 3, $file);
     }



?>