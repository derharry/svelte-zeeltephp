<?php
//
// inc.demo.zpdemo.php   
//
// PHP in /src/routes/**/zpdemo 
//     - any custom PHP files inside your route, must be manually include(), require().
//
// PHP /$lib/zplib/
//     - your shared PHP-library. All php files are autoloaded.
//     - move this file to $lib/zplib/ where it will be autoloaded.
//     - move to $lib/zplib when needed as shared lib and autoloaded
//

    /**
     * return hello
     */
    function demo_lip_example() {
        return "inc.demo.zpdemo.php";
    }

?>