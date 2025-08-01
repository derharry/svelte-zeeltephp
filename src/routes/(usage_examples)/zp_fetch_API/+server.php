<?php namespace zp1753838398; $zpns=__NAMESPACE__;

     function fallback() {
          return 'hi from fallback() -same route';
     }

     function GET() {
          return 'hi from GET -same route';
     }
     
     function PUT() {
          return 'hi from PUT -same route';
     }
     
     function HEAD() {
          return 'hi from PUT -same route';
     }
     
     function DELETE() {
          return 'hi from DELETE -same route';
     }
     
?>