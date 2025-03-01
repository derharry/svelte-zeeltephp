<?php


      function zp_scandir($path) {
            $path  = rtrim($path, '/') . '/';
            $files = array();
            $files_raw = scandir($path);
            foreach ($files_raw as $file) {
                  if ($file == '.' || $file == '..') 
                        continue;

                  if (is_file($path . $file)) {
                        array_push($files, $file);
                  } elseif (is_dir($path . $file)) {
                        $files_raw2 = zp_scandir($path . $file . '/');
                        foreach ($files_raw2 as $file2) {
                              array_push($files, $file . '/' . $file2);
                        }
                  }
            }
            return $files;
            /*
            foreach ($files_raw as $file) {
                  if ($file == '.' || $file == '..') 
                        continue;
                  if (is_dir($dir . $file)) {
                        $files = zp_scandir($dir . $file . '/');
                  }
                  else
                        array_push($files, $file);
            }
            return $files;
            */
      }


?>