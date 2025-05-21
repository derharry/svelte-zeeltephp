<?php

/**
 * ZeeltePHP WordPress Database Adapter
 *
 * Provides seamless integration with WordPress $wpdb while maintaining
 * isolation from full WordPress environment bootstrapping.
 */
class ZeeltePHP_DB_wordpress {

      public $dbconn = null;

      /** @var object|null Wordpress $wpdb */
      public $wpdb = null;

      /** @var string|null path to wp-load.php file */
      public $pathToWPload = '';

      /** @var string|null is true when wp-load.php file is found */
      public $isConnected = false;
      
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
            /*
            $this->connect();
            try {
                  if (!$this->isConnected) {
                        if ($this->wpdb && method_exists($this->wpdb, $method)) {
                              return $this->wpdb->$method(...$args);
                        }
                        //throw new Exception("Method $method not found in DB adapter");
                        $this->last_message = "Method $method not found in DB adapter";
                        //throw new \Exception($this->last_message); 
                  }
            }
            catch (\Throwable $th) {
                  zp_handle_error($th);
            }
            */
      }
      
      /**
       * Constructor - validates wp-load.php path
       * @param string $pathToWPload Path to wp-load.php (e.g. wordpress://../wp-load.php)
       * @throws Exception If wp-load.php not found
       */
      function __construct( $pathToWPload ) {
            $this->pathToWPload = str_replace('wordpress://', '', $pathToWPload);
            if (!is_file($this->pathToWPload)) {
                  $this->last_message = "path to wp-load.php not found.";
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
            try {
                  if ($this->isConnected) return;

                  //zp_log('DB_WPDB connect()');
                  if (is_null($this->dbconn) && !is_file($this->pathToWPload)) 
                        $this->last_message = "path to wp-load.php not found.";
                  else if (is_null($this->dbconn) && is_file($this->pathToWPload)) {
                        //include_once(str_replace('/wp-load.php/', '', $this->pathToWPload).'/wp-load.php');
                        include_once($this->pathToWPload);
                        global $wpdb;
                        $this->dbconn = $wpdb;
                        $this->wpdb   = $wpdb;
                        $this->isConnected = true;
                        // connect is done by including the wp-load.php file
                  }

                  /*
                  // Isolate WordPress environment
                  $wp_load = function() {
                        define('WP_USE_THEMES', false);
                        global $wpdb;
                        return $wpdb;
                  };
                  // Load WordPress database abstraction
                  include_once($this->pathToWPload);
                  $wpdb = require $this->pathToWPload;
                  if ($wpdb instanceof wpdb) {
                        $this->wpdb = $wpdb;
                        $this->isConnected = true;
                  } else {
                        throw new \Exception("Failed to initialize WordPress database");
                  }
                   */
            }
            catch (\Throwable $th) {
                  zp_handle_error($th);
            }
      }

      /**
       * Close the DB connection
       */
      public function disconnect() {
            if (!is_null($this->dbconn))
                  $this->wpdb->close();
      }

      /**
       * Returns last error message
       */
      public function last_error() 
      {
            return $this->wpdb->last_error ?? $this->last_message;
      }

      /**
       * Executes a SQL query and returns results
       * @param string $sql Valid SQL query
       * @return array|false Query results or false on failure
       */
      public function query($sqlStatement) {
            try {
                  $this->connect();
                  if (!$this->isConnected) return;
                  if (!$this->wpdb) return;
                  return $this->wpdb->get_results($sqlStatement, ARRAY_A);
            } catch (\Throwable $th) {
                  $this->last_message = $this->wpdb->last_error ?: $th->getMessage();
                  zp_handle_error($th);
                  return false;
            }
      }

}

?>