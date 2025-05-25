<?php

      /**
       * Merges two associative arrays, replacing values in $src with those 
       * from $insert for matching keys. Only keys present in $src are updated.
       *
       * @param array $src    The original array (keys to preserve)
       * @param array $insert The array with replacement values
       * @return array        The merged array
       */
      function merge_key_value_list(array $src, array $insert): array {
            foreach ($src as $key => $val) {
                  if (array_key_exists($key, $insert)) {
                        $src[$key] = $insert[$key];
                  }
            }
            return $src;
      }

?>