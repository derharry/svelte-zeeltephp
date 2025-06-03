<?php

require_once('db.db.php');

class ZeeltePHP_DB_wordpress extends ZP_DB implements IZP_DB {

     /** @var object|null Wordpress $wpdb */
     public $wpdb = null;

      /** @var string|null Last message  */
      public $last_message = null;


     /**
      * Magic getter - proxies to WordPress $wpdb properties
      */
     public function __get($name) 
     {
          $this->connect();
          return $this->wpdb->$name ?? null;
          /*
          if ($this->wpdb && property_exists($this->wpdb, $name)) {
               return $this->wpdb->$name;
          }
          $this->last_message = "Property $name not found in WPDB adapter";
          */
     }

     /**
      * Magic method - proxies to WordPress $wpdb methods
      */
     public function __call($method, $args) 
     {
          $this->connect();
          return $this->wpdb->$method(...$args);
     }

     /**
      * Returns the last error message from the database connection.
      * @return string Error message or "Not connected"
      */
     public function last_error():string {
          return $this->isConnected() ? $this->wpdb->last_error : 'Not connected';
          //return $this->wpdb->last_error ?? $this->last_message;
     }

     public function isConnected(): bool {
          return $this->wpdb !== null;
     }
     
     public function affected_rows(): int {
          return $this->wpdb->rows_affected;
     }

     /**
      * Constructor - validates wp-load.php path
      * @param string $pathToWPload Path to wp-load.php (e.g. wordpress://../wp-load.php)
      * @throws Exception If wp-load.php not found
      */
     function __construct( $pathToWPload, $username = null, $password = null, $database = null ) {
          $this->hostname = str_replace('wordpress://', '', $pathToWPload);
          if (!is_file($this->hostname)) {
               $this->last_message = "path to wp-load.php not found.";
          }
          if (is_file($this->hostname) && $database !== null) {
               $sql = "USE $database;";
               $this->query($sql);
          }
          //throw new \Exception($this->last_message); 
     }

     /**
      * Close the DB connection
      */
     function __destruct() {
          $this->disconnect();
     }

     /**
      * Establishes WordPress database connection
      */
     public function connect() {
          global $wpdb;
          try {
               if ($this->isConnected()) return;
               if (is_null($this->wpdb) && !is_file($this->hostname))
                    $this->last_message = "path to wp-load.php not found.";
               else if (is_null($this->wpdb) && is_file($this->hostname)) {
                    include_once($this->hostname);
                    $this->wpdb = $wpdb;
               }
               if ($this->isConnected()) return;
          }
          catch (\Throwable $th) {
               zp_handle_error($th);
          }
     }
            
     /**
      * Close the DB connection
      */
     public function disconnect() {
          if (!is_null($this->wpdb)) {
               $this->wpdb->close();
               $this->wpdb = null;
          }
     }

     


     /**
      * Executes a SQL query and returns results
      * @param string $sql Valid SQL query
      * @return array|false Query results or false on failure
      */
     public function query($sqlStatement) {
          try {
               $this->connect();
               if (!$this->isConnected()) return;
               if (!$this->wpdb) return;
               return $this->wpdb->get_results($sqlStatement, ARRAY_A);
          } catch (\Throwable $th) {
               $this->last_message = $this->wpdb->last_error ?: $th->getMessage();
               zp_handle_error($th);
               return false;
          }
     }

     public function real_escape_string($value): string {
          $this->connect();
          return $this->wpdb->_real_escape((string)$value);
     }
     
}

?>