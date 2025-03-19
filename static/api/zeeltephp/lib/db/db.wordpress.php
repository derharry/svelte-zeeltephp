<?php

class ZeeltePHP_DB_wordpress {
      
      public $message = null;
      public $dbconn  = null;


      public $wpdb         = null;
      public $pathToWPload = '../../wp-load.php';


      function is_connected () {
            if ($this->wpdb != null) return true;
            return false;
      }

      function __construct( $pathToWPload ) {
            $this->pathToWPload = str_replace('wordpress://', '', $pathToWPload);
            $file = str_replace('/wp-load.php', '', $this->pathToWPload.'/wp-load.php');
            if (!is_file($this->pathToWPload)) {
                  $this->message = "path to wp-load.php not found.";
                  throw new \Exception($this->message); 
            }
      }

      function __destruct() {
            $this->disconnect();
      }

      // Add magic method to forward calls to wpdb
      public function __call($method, $args) {
            try {
                  if (!$this->is_connected) {
                        if ($this->wpdb && method_exists($this->wpdb, $method)) {
                              return $this->wpdb->$method(...$args);
                        }
                        //throw new Exception("Method $method not found in DB adapter");
                        $this->message = "Method $method not found in DB adapter";
                        throw new \Exception($this->message); 
                  }
            }
            catch (\Throwable $th) {
                  zp_error_handler($th);
            }
      }      

      public function connect() {
            try {
                  if (is_null($this->dbconn) && !is_file($this->pathToWPload)) 
                        $this->message = "path to wp-load.php not found.";
                  else if (is_null($this->dbconn) && is_file($this->pathToWPload)) {
                        include_once($this->pathToWPload.'/wp-load.php');
                        $this->dbconn = $wpdb;
                        $this->wpdb   = $wpdb;
                        // connect is done by including the wp-load.php file
                  }
            }
            catch (\Throwable $th) {
                  zp_error_handler($th);
            }
      } 

      public function disconnect() {
            if (!is_null($this->dbconn))
                  $this->wpdb->close();
      }


      public function query($sql) {
            try {
                  if (!$this->is_connected) return;
                  $this->connect();
                  if (!$this->wpdb) return;
                  $result = $this->wpdb->get_results( $sql, 'ARRAY_A' );
                  return $result;
            } catch (\Throwable $th) {
                  var_dump($th);
            }
      }


}


?>