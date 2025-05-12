<?php

     function read_env_file($cfgFile) {
          if (file_exists($cfgFile))
               return parse_ini_file($cfgFile);
          return false;
     }

?>