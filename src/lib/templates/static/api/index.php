<?php
#
#  ZeeltePHP – /api Working Directory
#
#  This path is reserved for ZeeltePHP.
#
#  Usage:
#    - npm run dev   : ZeeltePHP is loaded from its library package (node_modules/zeeltephp/dist/api)
#    - npm run build : ZeeltePHP post-build finalizes the /build/api directory, including your PHP files.
#
#  Customizing api/index.php:
#    - ZeeltePHP relies on this specific path structure.
#    - If you need to customize this file, ensure ZeeltePHP can co-exist with your custom API logic.
#    - To integrate ZeeltePHP, simply include init.php as shown below. 
#    - If you add logic after including init.php, use `chdir(__DIR__);` to restore the current working directory.
#
#  Start ZeeltePHP:
include('../../node_modules/zeeltephp/dist/api/init.php');
# chdir(__DIR__); // Uncomment if you implement logic after init.php.
?>