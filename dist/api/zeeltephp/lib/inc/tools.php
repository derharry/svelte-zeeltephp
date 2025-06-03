<?php

     require_once('./keyValueListHelper.php');

      /**
       * deprecated - move to keyValueListHelper/keyValueListMerge()
       */
      function merge_key_value_list(array $src, array $insert): array {
            return keyValueListMerge($src, $insert);
      }

?>