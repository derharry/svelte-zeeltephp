<?php namespace zp1753838398; $zpns=__NAMESPACE__;

     function fallback() {
          global $zpAR, $env, $db;
          return [
               'msg' => 'hi from fallback()',
               
          ];
     }

     function GET() {
          return 'hi from GET';
     }
     
     function PUT() {
          return 'hi from PUT';
     }
     
     function DELETE() {
          return 'hi from DELETE';
     }
     
?>