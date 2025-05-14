<?php

     /**
      * 
      */
     function zp_errorMessageTranslation($code) {
          global $zpAR;
          // code [ message , filename , errorTypeName ]
          // -- todo : add also custom errorTypeNames
          $zeeltephp_errors = [
               400 => ['no route requested', ''],           // bad request
               404 => ['no +.php in route', ''],      // not found
               801 => ['no load()', $zpAR->routeFile],
               802 => ['no action( '.$zpAR->action.' )', $zpAR->routeFile],
          ];
          if (isset($zeeltephp_errors[$code])) 
               return [ $code, ...$zeeltephp_errors[$code] ];
          return [null, null, null]; // if no translation - return null for no match
     }


     /**
      * handles all zp errors, exceptions and outputs as JSON.
      * streamlines error logging and output.
      * changes full file-system paths to relative in $error (readability).
      * @param {Error}  $e         $error
      * @param {string} $message   your error string
      * @param {number} $code      your error code
      */
     function zp_handle_error($e, $message = null, $code = null) {
          global $zpAR;

          // # destruct php-Error
          $e_type     = get_class($e);
          $e_message  = $e->getMessage();
          $e_previous = $e->getPrevious();
          $e_code     = $e->getCode();
          $e_file     = $e->getFile();
          $e_line     = $e->getLine();
          // ! lets not expose 'trace' (security) => $e->getTrace(),
          // ! trace as string is ugly anyway     => $e->getTraceAsString()

          // # get translation
          // -- todo : add also custom errorTypeNames at zp_errorMessageTranslation()
          [$zp_code, $zp_msg, $zp_file] = zp_errorMessageTranslation($e_message);
          [$cc_code, $cc_msg, $cc_file] = zp_errorMessageTranslation($code);

          // # get real values from chaining
          $e_code    = $code    ?? $zp_code ?? $cc_code ?? ($e_code == 0 ? 500 : $_ecode) ?? $e_code;
          $e_message = $message ?? $zp_msg  ?? $cc_msg  ?? $e_message;
          $e_file    = $zp_file ?? $cc_file ?? $e_file;
          $e_line    = ($zp_code ? '' : $zp_code) ?? ($cc_code ? '' :  $cc_code) ?? $e_line;

          // # short filename:line
          $zp_short = basename($e_file);
          $zp_short .= $e_line ? ":$e_line" : '';

          // # set JSON response by schema @api/error
          $errorJsonRespone = [
               'ok'       => false,
               'error'    => $e_type,
               'code'     => $e_code,
               'message'  => $e_message,
               'short'    => $zp_short,
               'file'     => $e_file,
               'line'     => $e_line,
               'previous' => $e_previous,
               // debug my-self
               //'cpPath'   => PATH_CPROOT,
               //'zpAR'     => $zpAR,
               //'zpDB'     => $db,
          ];

          // # change $errorJsonRespone to JSON-string 
          $errorJsonRespone = json_encode($errorJsonRespone);

          // # remove full paths from JSON-string
          $errorJsonRespone = zp_change_full_paths_to_zp_relative($errorJsonRespone);

          // # do zp-log_error now 
          // -- todo
          error_log($errorJsonRespone."\n", 3, PATH_ZPLOG.'/error.log');

          // output error JSON
          //echo json_encode($e);
          echo $errorJsonRespone;
     }

     /**
      * write to /zp-log/log.log
      */
     function zp_log($content) {
          // if $content is not string - convert to json
          if (is_array($content) || is_object($content)) 
               $content = json_encode($content, JSON_PRETTY_PRINT, 3);
          error_log($content."\n", 3, PATH_ZPLOG.'/log.log');
     }

     /**
      * write to /zp-log/debug.log
      */
     function zp_log_debug($content) {
          global $debug;
          if ($debug) 
               error_log($content."\n", 3, PATH_ZPLOG.'/debug.log');
     }



?>