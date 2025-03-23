<?php

class ZeeltePHP_DB_wordpress {
      
      public $message = null;
      public $dbconn  = null;


      public $wpdb         = null;
      public $pathToWPload = '../../wp-load.php';
      public $isConnected  = false;

      

      function __construct( $pathToWPload ) {
            $this->pathToWPload = str_replace('wordpress://', '', $pathToWPload);
            $file = $this->pathToWPload;
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
                  if (!$this->isConnected) {
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
                  zp_log('DB_WPDB connect()');
                  if (is_null($this->dbconn) && !is_file($this->pathToWPload)) 
                        $this->message = "path to wp-load.php not found.";
                  else if (is_null($this->dbconn) && is_file($this->pathToWPload)) {
                        //include_once(str_replace('/wp-load.php/', '', $this->pathToWPload).'/wp-load.php');
                        include_once($this->pathToWPload);
                        $this->dbconn = $wpdb;
                        $this->wpdb   = $wpdb;
                        $this->isConnected = true;
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
                  zp_log($sql);
                  $this->connect();
                  if (!$this->isConnected) return;
                  if (!$this->wpdb) return;
                  zp_log($sql);
                  $result = $this->wpdb->get_results( $sql, 'ARRAY_A' );
                  return $result;
            } catch (\Throwable $th) {
                  zp_error_handler($th);
            }
      }


}


?>