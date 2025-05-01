<?php

      function merge_key_value_lists($src, $insert) {
            foreach($src as $key => $val) {
                  if (isset($insert[$key]))
                        $src[$key] = $insert[$key];
            }
            return $src;
      }

?>