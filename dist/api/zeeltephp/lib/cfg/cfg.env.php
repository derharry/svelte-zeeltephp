<?php

     function zp_read_env_file() {
          $cfgFile = false;
          // presume being in build/productive 
          // load .env from current build './zeeltephp/.env'
          if (file_exists('./zeeltephp/.env'))  $cfgFile = './zeeltephp/.env';
          // otherwhise presume being in development /projectroot/.env.dev, .env.development
          else if (file_exists('../../.env.dev')) $cfgFile = '../../.env.dev';
          else if (file_exists('../../.env.development')) $cfgFile = '../../.env.development';
          # load the .env file or return false
          if (file_exists($cfgFile))
               return parse_ini_file($cfgFile);
          return $cfg;
     }

?>