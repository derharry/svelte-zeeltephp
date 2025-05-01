<?php

     require_once('zeeltephp/lib/io/io.dir.php');
     
     function zp_load_php_files($path, $phpfiles = null) {
          $path  = rtrim($path, '/') . '/';
          if ($phpfiles == null) {
               $phpfiles = zp_scandir($path);
          }
          foreach ($phpfiles as $file) {
               include_once($path . $file);
          }
     }

?>