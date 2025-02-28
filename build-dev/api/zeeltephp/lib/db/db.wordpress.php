<?php

class ZeeltePHP_DB_WordPress {
      
      public $dbconn = null;

      public $wpdb         = null;
      public $pathToWPload = '../../wp-load.php';


      function __construct( $ZEELTEPHP_DATABASE_URL ) {
            $this->pathToWPload = str_replace('wordpress://', '', $ZEELTEPHP_DATABASE_URL);
      }

      function __destruct() {
            $this->disconnect();
      }

      public function connect() {
            if (is_null($this->dbconn)) {
                  include_once($this->pathToWPload.'/wp-load.php');
                  $this->dbconn = $wpdb;
                  $this->wpdb   = $wpdb;
                  // connect is done by including the wp-load.php file
            }
      } 

      public function disconnect() {
            if (!is_null($this->dbconn))
                  $this->wpdb->close();
      }


      public function last_error() {
            return $this->wpdb->last_error;
      }


      public function query($sql) {
            try {
                  $this->connect();
                  $result = $this->wpdb->get_results( $sql, 'ARRAY_A' );
                  return $result;
            } catch (\Throwable $th) {
                  var_dump($th);
            }
      }


}


?>