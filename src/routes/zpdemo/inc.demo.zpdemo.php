<?php
//
// inc.demo.zpdemo.php   
//
// PHP in /src/routes/**/zpdemo 
//     - any custom PHP files inside your route, must be manually include(), require().
//
// PHP /src/lib_php/
//     - your shared PHP-library. All php files are autoloaded.
//     - move this file to /src/lib_php/ where it will be autoloaded.
//     - move to /src/lib_php/ when needed as shared lib and autoloaded
//

    /**
     * return hello from /src/lib_php/
     */
    function demo_lip_example() {
        return "inc.demo.zpdemo.php";
    }

?>