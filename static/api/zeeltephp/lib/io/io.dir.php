<?php

      /**
       * Scan a directory for files matching a pattern (non-recursive).
       *
       * @param string       $path      Directory to scan.
       * @param string|null  $regExp    Optional regular expression for filename matching (preg_match()). Include the / delimiters, for example: "/\.php$/"
       * @return array|false            List of matching filenames (not paths). False if invalid path.
       */
      function zp_scandir($path, $regExp = null) {
            $path = rtrim(str_replace('//', '/', $path), '/');
            $files = [];
            if (!is_dir($path)) {
                  error_log("zp_scandir($path) is not a directory");
                  return $files;
            }
            foreach (scandir($path) as $file) {
                  if ($file === '.' || $file === '..') continue;
                  $fullPath = "$path/$file";
                  if (is_dir($fullPath)) {
                        if ($regExp === null || preg_match("/$regExp/", $file)) {
                              $files[] = $file;
                        }
                  }
                  if (is_file($fullPath)) {
                        if ($regExp === null || preg_match("/$regExp/", $file)) {
                              $files[] = $file;
                        }
                  }
            }
            return $files;
      }

      /**
       * Recursively scan a directory and all subdirectories for files matching a pattern.
       *
       * @param string       $path       Directory to scan.
       * @param string|null  $regExp     Optional regular expression for filename matching (preg_match()). Include the / delimiters, for example: "/\.php$/"
       * @param int          $maxDepth   Maximum recursion depth (-1 for unlimited, 1 for only current dir).
       * @param int          $_depth     Internal use: current recursion depth.
       * @return array|false             List of matching filenames. False if invalid path.

       */
      function zp_scandirRecursiveDown($path, $regExp = null, $maxDepth = 1, $_depth = 0) {
            $path = rtrim(str_replace('//', '/', $path), '/');
            $files = [];
            if (!is_dir($path)) return false;
            foreach (zp_scandir($path, $regExp) as $file) {
                  $files[] = $file;
                  $fullPath = "$path/$file";
                  if (is_dir($fullPath) && ($maxDepth < 0 || $_depth < $maxDepth)) {
                        $subFiles = zp_scandirRecursiveDown($fullPath, $regExp, $maxDepth, $_depth + 1);
                        if (is_array($subFiles))
                              foreach ($subFiles as $subFile)
                                    $files[] = "$file/$subFile";
                  }
            }
            return $files;
      }

      /**
       * Recursively scan parent directories for files matching a pattern (upwards).
       *
       * @param string      $path        Starting directory.
       * @param string|null $regExp      Optional regular expression for filename matching (preg_match()). Include the / delimiters, for example: "/\.php$/"
       * @param int         $maxDepth    Maximum upward recursion (default 1).
       * @param int         $_depth      Internal use: current recursion depth.
       * @return array|false             List of matching filenames (with ../ prefixes). False if invalid path.
       * 
       */
      function zp_scandirRecursiveUp($path, $regExp = null, $maxDepth = 1, $_depth = 0) {
            $path = rtrim(str_replace('//', '/', $path), '/');
            $files = [];
            if ($_depth > $maxDepth) return $files;
            if (!is_dir($path))      return false;
            // Scan current directory
            foreach (zp_scandir($path, $regExp) as $file) {
                  $prefix  = str_repeat('../', $_depth);
                  $files[] = $prefix . $file;
            }
            // Recurse upwards if allowed
            if ($_depth < $maxDepth) {
                  $parentPath = dirname($path);
                  if ($parentPath !== $path) {
                        $parentFiles = zp_scandirRecursiveUp($parentPath, $regExp, $maxDepth, $_depth + 1);
                        if (is_array($parentFiles))
                              $files = array_merge($parentFiles, $files);
                  }
            }
            return $files;
      }

?>