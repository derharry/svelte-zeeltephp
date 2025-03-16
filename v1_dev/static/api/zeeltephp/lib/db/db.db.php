<?php

class ZeelteDB {
      
      public $dbconn = null;


      public function connect() {
            $this->...
      }


      public function query($sql) {
            try {
                  //code...
            } catch (\Throwable $th) {
                  //throw $th;
            }
      }


}

?>