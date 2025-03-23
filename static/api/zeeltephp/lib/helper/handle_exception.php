<?php


function handle_Exception($exp) {
      try {
            //var_dump($exp);

            // write into file...

      } catch (\Throwable $th) {
            var_dump($th);
      }
}


class ZP_Exception extends Exception 
{

}


?>