<?php namespace ZeeltePHP\Lib\IO;

      /**
       * Removes the directory and its content.
       */
      function rm_dir($path, $reqursive = false) {
            if (is_dir($path)) {
                  empty_dir($path, $reqursive);
                  return rmdir($path);
            }
      }

      /**
       * Empties the directory from files and folders
       */
      function empty_dir($path, $reqursive = false, $regExp = null) {
            if (is_dir($path)) {
                  $content = zp_scandirRecursiveDown($path);
                  foreach ($content as $_) {
                        $path_content = "$path/$_";
                        is_file($path_content) && unlink($path_content);
                        is_dir ($path_content) && rmdir ($path_content);
                  }
                  $content = zp_scandir($path, $regExp);
                  return count($content) == 0;
            }
      }

      /**
       * Deprecated. Use scan_dir()
       */
      function zp_scandir($path, $regExp = null) {
            return scan_dir($path, $regExp);
      }
      /**
       * Scan a directory for files matching a pattern (non-recursive).
       *
       * @param string       $path      Directory to scan.
       * @param string|null  $regExp    Optional regular expression for filename matching (preg_match()). Include the / delimiters, for example: "/\.php$/"
       * @return array|false            List of matching filenames (not paths). False if invalid path.
       */

      function scan_dir($path, $regExp = null) {
            if (is_dir($path)) {
                  if ($regExp && !preg_match('/^([\/#]).*\1$/s', $regExp)) {
                        $regExp = '/'.trim($regExp, '/#').'/';
                  }
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
                              if ($regExp === null || preg_match($regExp, $file)) {
                                    $files[] = $file;
                              }
                        }
                        if (is_file($fullPath)) {
                              if ($regExp === null || preg_match($regExp, $file)) {
                                    $files[] = $file;
                              }
                        }
                  }
                  return $files;
            }
      }


      /**
       * Deprecated. Use scan_dir_recursive_down()
       */
      function zp_scandirRecursiveDown($path, $regExp = null, $maxDepth = 1, $_depth = 0) {
            return scan_dir_recursive_down($path, $regExp = null, $maxDepth, $_depth);
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
      function scan_dir_recursive_down($path, $regExp = null, $maxDepth = 1, $_depth = 0) {
            $path = rtrim(str_replace('//', '/', $path), '/');
            if (is_dir($path)) {
                  $files = [];
                  if (!is_dir($path)) return false;
                  foreach (zp_scandir($path, $regExp) as $file) {
                        $files[] = $file;
                        $fullPath = "$path/$file";
                        if (is_dir($fullPath) && ($maxDepth < 0 || $_depth < $maxDepth)) {
                              $subFiles = scan_dir_recursive_down($fullPath, $regExp, $maxDepth, $_depth + 1);
                              if (is_array($subFiles))
                                    foreach ($subFiles as $subFile)
                                          $files[] = "$file/$subFile";
                        }
                  }
                  return $files;
            }
      }

      /**
       * Deprecated. Use scan_dir_recursive_up()
       */
      function zp_scandirRecursive_up($path, $regExp = null, $maxDepth = 1, $_depth = 0) {
            return scan_dir_recursive_up($path, $regExp = null, $maxDepth, $_depth);
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
      function scan_dir_recursive_up($path, $regExp = null, $maxDepth = 1, $_depth = 0) {
            if (is_dir($path)) {
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
                              $parentFiles = scan_dir_recursive_up($parentPath, $regExp, --$maxDepth, ++$_depth);
                              if (is_array($parentFiles))
                                    $files = array_merge($parentFiles, $files);
                        }
                  }
                  return $files;
            }
      }

?>