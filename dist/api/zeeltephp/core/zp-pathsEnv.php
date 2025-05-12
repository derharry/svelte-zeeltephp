<?php
/*
     DEFAULT PATHS and RUNNING environment context
     Preload the paths

     default - production first - we presume to be in build (production)
             - development env  - no .env file
*/

//
// Default is BUILD (productive) environment
//     BUILD/api/zeeltephp environment (default)
//     all is setup by zeeltephp_postbuild()
//
$path_ZP_LIB     = './zeeltephp/zplib/';     // from CP /src/lib/zplib
$path_ZP_ROUTES  = './zeeltephp/zproutes/';  // from CP /src/routes/
$path_ZP_LOG     = './zeeltephp/log/';       // php errors
$path_ZP_ENVfile = './zeeltephp/.env';       // exist only in builds


if (!is_file($path_ZP_ENVfile)) {

     // no .env file - presume dev mode
     //       so in which dev-project context am I running? 
     //       as lib-package       (cp: consumer project)            (default) 
     //       or lib-development   (zp: self zeeltephp lib-project)
     $path_ZP_ENVfile = ''; 
     $consumerRoot = getcwd();  // expected /path/to/htdocs/<your-project>
     $pathDistApi  = $consumerRoot.'/node_modules/zeeltephp/dist/api';
     $isConsumer   = is_dir($pathDistApi);
     $isSelf       = str_contains(getcwd(), '/svelte-zeeltephp/');

     // default isConsumer
     $path_ZP_LIB    = "$consumerRoot/src/lib/zplib";
     $path_ZP_ROUTES = "$consumerRoot/src/routes";
     $path_ZP_LOG    = "$consumerRoot/static/api/zeeltephp/log"; // php errors
     $title = "";
     if ($isSelf) {
          // self project context
          $title = "self project"; 
          $pathDistApi = $consumerRoot;
          $consumerRoot .= '/../..';
          $path_ZP_LIB    = "$consumerRoot/src/lib/zplib/";
          $path_ZP_ROUTES = "$consumerRoot/src/routes/";
     } else {
          // consumer project context
          $title = "consumer project"; 
          if (!$isConsumer) {
               // is it linked mode?
               $consumerRoot   .= '/../..';
               $pathDistApi    = "$consumerRoot/../svelte-zeeltephp/static/api";
               $path_ZP_LIB    = "$consumerRoot/src/lib/zplib/";
               $path_ZP_ROUTES = "$consumerRoot/src/routes/";
          }
          //else 
          //     $consumerRoot .= '/../../../..';
     }

     $pathDistApi = realpath($pathDistApi);
     $path_ZP_LIB = realpath($path_ZP_LIB);
     $path_ZP_ROUTES = realpath($path_ZP_ROUTES);

     $debug = false;
     if ($debug) {
          echo " - $title \n"; 
          echo " - consumerRoot $consumerRoot\n"; 
          echo " - isSelf       ".($isSelf ? 'true':'false')."\n"; 
          echo " - isConsumer   ".($isConsumer ? 'true':'false')."\n"; 
          echo " - pathDistApi      $pathDistApi  \n"; 
          echo " - path_ZP_LIB      $path_ZP_LIB  \n"; 
          echo " - path_ZP_ROUTES   $path_ZP_ROUTES  \n"; 
          echo " - path_ZP_ENVfile  $path_ZP_ENVfile  \n"; 
          //echo "<li></li>\n"; 
     }
     if ($debug)  echo " - chdir ".getcwd()."\n";
     chdir($pathDistApi);
     if ($debug)  echo " - chdir ".getcwd()."\n";
} 

define('PATH_ZPLIB',      $path_ZP_LIB);
define('PATH_ZPROUTES',   $path_ZP_ROUTES);
define('PATH_ZP_LOG',     $path_ZP_LOG);
define('PATH_ZP_ENVfile', $path_ZP_ENVfile);

/*


if ($path_ZP_ROOT != '') {
     // presume dev mode when no build-/api/zeeltephp/.env file is found
     if (is_dir($path_ZP_ROOT)) 
         chdir($path_ZP_ROOT);
     }

if (!is_file($path_ZP_ENVfile)) {
     // not within BUILD
     // consumer (cp) is default
     $path_CP_ROOT = '../../';
     $path_ZP_ROOT = $path_CP_ROOT.'node_modules/zeeltephp/dist/api/';
     if (is_dir('zeeltephp')) {
          // self (zp)
          // zp/static/api
          // or 
          $path_ZP_ROOT = '';
     } else {
          $path_CP_ROOT = '../../../../';
     }
     // presume dev mode when no build-/api/zeeltephp/.env file is found
     if ($path_ZP_ROOT != '' && is_dir($path_ZP_ROOT)) 
          chdir($path_ZP_ROOT);

     $path_ZP_LIB = getcwd().'/../../../../src/lib/zplib/';


}


//
// 2nd - running environment 
//       self or consumer environment (dev build in lib or pp) - set /api/** 
if (!is_file('./zeeltephp/.env')) {
    // .env file is always at final build
    // not found means we are in ZP or CP development environment 
    // ZP = folder /static/api/zeeltephp exist
    // CP = folder /static/api/ contains only the index.php file 
    // default consumer CP
    $env  = 'consumer'; 
    $path_CP_ROOT = '../../';
    if (!is_dir('./zeeltephp')  is_file('../../package.json')) {
        $package = file_get_contents('../../package.json');
        if ($package->name == 'zeeltephp') {
            $env = 'self'; //self we are in /api
            $path_ZP_ROOT = '';
            $path_CP_ROOT = '/../../../../';
        }
    }
    if ($path_ZP_ROOT != '') {
        // presume dev mode when no build-/api/zeeltephp/.env file is found
        if (is_dir($path_ZP_ROOT)) 
            chdir($path_ZP_ROOT);
        
        // presume zp main() is at SELF or Consumer project
        $path_ZP_LIB = getcwd().'/../../../../src/lib/zplib/';
        $path_ZP_ROUTES = getcwd().'/../../../../src/routes/';
    }
}

}
*/
?>