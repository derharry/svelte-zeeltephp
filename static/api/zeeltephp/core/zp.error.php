<?php namespace ZeeltePHP\Error;

use function ZeeltePHP\Core\Lib\change_full_paths_to_zp_relative;

     /**
      * Checks if  error codes to human-readable messages and related info.
      *
      * @param int|string $input Error code or message.
      * @return array [code, message, filename] or [null, null, null] if not found.
      */
     function fetch_zp_error($input) {
          global $zpAR;
          $zp_route  = $zpAR->route  ?? '';
          $zp_action = $zpAR->action ?? '';
          $zeeltephp_errors = [
               400 => ['no route requested', ''],  // bad request
               404 => ['no +.php in route' , ''],  // not found
               801 => ['no load() in '     , $zp_route],
               802 => ["no action( $zp_action ) in ", $zp_route],
               803 => ['missing namespace in route', '']
          ];
          if (isset($zeeltephp_errors[$input])) 
               return [ $input, ...$zeeltephp_errors[$input] ];
          return [null, null, null];
     }

     /**
      * Collects information about the error including fetch_zp_error()
      * Converts full file-system paths to relative for readability.
      */
     function get_error_response($error, $message = null, $code = null) {
          [ $e_type, $e_code, $e_message, $e_short, $e_file, $e_line, $e_previous ] = null;

          // Extract error details from Error or json-fallback
          $e_type = is_object($error) ? get_class($error) : '';
          if ($error instanceof \Throwable) {
                 $e_code     = $error->getCode();
                 $e_file     = $error->getFile();
                 $e_line     = $error->getLine();
                 $e_message  = $error->getMessage();
                 $e_previous = $error->getPrevious();
                 // uncomment if required:
                 //$e_trace = $error->getTrace()
                 //$e_traceString => $error->getTraceAsString()
          } else $e_message  = json_encode($error);

          // Translate error codes/messages
          [$zp_code, $zp_msg, $zp_file] = fetch_zp_error($e_message);
          [$cc_code, $cc_msg, $cc_file] = fetch_zp_error($code);
          
          // destruct final values: arguments / translations zp,cc / fallback
          $e_code    = $code    ?? $zp_code ?? $cc_code ?? ($e_code ?: 500);
          $e_message = $message ?? $zp_msg  ?? $cc_msg  ?? $e_message;
          $e_file    = $zp_file ?? $cc_file ?? $e_file;
          
          // e_short filename:line for quick reference
          $e_short = $e_file && basename($e_file);
          if ($e_line) $e_short .= ":$e_line";

          $errorResponse = [
               'time'     => date("Y-m-d H:i:s"),
               'ok'       => false,
               'error'    => $e_type,
               'code'     => $e_code,
               'message'  => $e_message,
               'short'    => $e_short,
               'file'     => $e_file,
               'line'     => $e_line,
               'previous' => $e_previous,
          ];
          $errorResponse = (object)$errorResponse;

          //return $errorResponse;
          return change_full_paths_to_zp_relative($errorResponse);
     }


     /**
      * Handles all ZeeltePHP errors and exceptions, outputs as JSON, and logs the error.
      * Converts full file-system paths to relative for readability.
      *
      * @param Throwable   $error     
      * @param string|null $message Optional custom error message.
      * @param int|null    $code    Optional custom error code.
      */
     function handle_error($error, $message = null, $code = null) {
          //var_dump($error);
          $er = log_error($error, $message, $code);
          http_response_code($er->code);
          
          $response = new \stdClass();
          $response->error = $er;
          //var_dump($er);
          echo json_encode($response);

     }

     /**
      * Write a log entry to /php_log/error.log.
      *
      * @param Throwable $error String or data to log.
      */
     function log_error($error, $message = null, $code = null) {

          $content = '';
          if (is_string($error)) {
               $content = $error;
          } 
          else {
               // Prepare Error
               $er = get_error_response($error, $message, $code);
               $content = sprintf('%s %s %s %s:%s %s',
                    $er->time,
                    $er->code,
                    $er->error,
                    $er->file,
                    $er->line,
                    $er->message
               );
          }
          //error_log(json_encode($er)."$message\n" , 3, PATH_ZPLOG.'error.log');
          
          // Log error
          error_log($content."\n", 3, PATH_ZPLOG.'error.log');
          return $er;
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