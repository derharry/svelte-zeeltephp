<?php
# ZeeltePHP - main API entry point 
// respond to browser preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
     header('Access-Control-Allow-Origin: * ');
     header('Access-Control-Allow-Methods: *');
     header('Access-Control-Allow-Headers: CONTENT-TYPE');
     exit(0);
}
// initialize and load ZeeltePHP
include('zeeltephp/init.php');
?>