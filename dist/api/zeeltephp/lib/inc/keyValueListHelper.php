<?php

      /**
       * Merges two associative arrays, replacing values in $src with those 
       * from $insert for matching keys. Only keys present in $src are updated.
       *
       * @param array $src    The original array (keys to preserve)
       * @param array $insert The array with replacement values
       * @return array        The merged array
       */
      function keyValueListMerge(array $src, array $insert): array {
            foreach ($src as $key => $val) {
                  if (array_key_exists($key, $insert)) {
                        $src[$key] = $insert[$key];
                  }
            }
            return $src;
      }

      
     /**
      * 
      */
     function keyValueListToTextTableFormat($keyValueDataList) {
          // collect longest key-length
          $maxLenght = 0;
          foreach ($keyValueDataList as $key => $value) 
               $maxLenght = strlen($key) > $maxLenght ? strlen($key) : $maxLenght;

          // create text table form
          $textRows = [];
          foreach ($keyValueDataList as $key => $value) {
               $tab = str_repeat(" ", $maxLenght - strlen($key) );
               $textRows[] = "$key $tab: $value";
          }

          // return text
          return implode("\n", $textRows);
     }


?>