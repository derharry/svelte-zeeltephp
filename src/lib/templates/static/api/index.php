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
#  Customizing /api/index.php:
#    - ZeeltePHP relies on this specific path structure.
#    - This /api is replaced with the /api from library package at postbuild.
#    - Currently its not supported for customization.
#      - You could do so by editing the /api/index.php in library package. (keep a copy!)
#      - Ensure ZeeltePHP can co-exist with your custom API logic and include init.php.
#      - If you need this feature earlier, please please let me know. 
#
#  Start ZeeltePHP:
include('../../node_modules/zeeltephp/dist/api/zeeltephp/init.php');
# chdir(__DIR__); // Uncomment if you implement logic after init.php, to restore the current working directory.
?>