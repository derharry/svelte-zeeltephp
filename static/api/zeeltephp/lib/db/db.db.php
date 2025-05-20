<?php

/**
 * ZeeltePHP Database Adapter 
 *
 * Dynamically loads and proxies to the correct DB provider class.
 * Based on .env.ZEELTEPHP_DATABASE_URL. 
 */
class ZP_DB {

      /** @var string|null Last message  */
      public $last_message  = null;

      /** @var object|null Real DB provider */
      public $dbsrc = null;

      /** @var string|null Used DB provider/schmema */
      public $dbtype = null;

      /** @var string|null Hostname to connect to */
      public $hostname = "";

      /** @var int|null Port to connect to */
      public $port = null;

      /** @var string|null Username for authentication */
      public $username = "";

      /** @var string|null Password for authentication */
      public $password = "";

      /** @var string|null Name of database */
      public $database = "";

      /**
       * Magic getter: Proxy property access to the DB provider.
       */
      public function __get($name) {
            if ($this->dbsrc && property_exists($this->dbsrc, $name)) {
                  return $this->dbsrc->$name;
            }
            return null;
      }

      /**
       * Magic method: Proxy method calls to the DB provider.
       */
      public function __call($method, $args) {
            if ($this->dbsrc && method_exists($this->dbsrc, $method)) {
                  return $this->dbsrc->$method(...$args);
            }
            throw new Exception("ZP_DB: Method $method not found in DB adapter");
      }

      /**
       * Constructor: Parses the DB connection URL and loads the appropriate provider.
       *
       * @param string|null $DATABASE_URL or ZEELTEPHP_DATABASE_URL
      */
      public function __construct($DATABASE_URL = null) {
            if (empty($DATABASE_URL)) return;
            try {
                  $this->parse_connectionUrl($DATABASE_URL);

                  // Determine provider class and file
                  $className = 'ZeeltePHP_DB_' . $this->dbtype;
                  $classFile = 'lib/db/db.' . $this->dbtype . '.php';

                  // Load the DB-provider
                  if (!class_exists($className)) {
                        if (is_file($classFile)) {
                              require_once $classFile;
                        } else {
                              $this->last_message = "Provider file '$classFile' not found.";
                              return;
                        }
                  }
                  if (class_exists($className)) {
                        $this->dbsrc = new $className(
                              $this->hostname,
                              $this->username,
                              $this->password,
                              $this->database
                        );
                  } else {
                       $this->last_message = "Class $className not found."; 
                  }
            } catch (\Throwable $th) {
                  zp_handle_error($th);
            }
      }

      /**
       * Close the DB connection
       */
      function __destruct() {
            if ($this->dbsrc)
                  $this->dbsrc->close();
      }  
            
      /**
       * Parses a DB connection URL into its components.
       *
       * Supports:
       *   - provider://username:password@hostname/database
       *   - wordpress://../wp-load.php@database
       *
       * @param string $databaseUrl
       */
      function parse_connectionUrl($databaseUrl) {
            try {
                  $parts = parse_url($databaseUrl);
                  $this->dbtype   = $parts['scheme'] ?? null;
                  $this->hostname = $parts['host']   ?? null;
                  $this->port     = $parts['port']   ?? null;
                  $this->username = $parts['user']   ?? null;
                  $this->password = $parts['pass']   ?? null;
                  $this->database = str_replace('/', '', $parts['path']) ?? null;

                  // Special handling for WordPress URLs
                  if ($this->dbtype === 'wordpress') {
                        // Example: wordpress://../wp-load.php@database
                        $wpUrl    = str_replace('wordpress://', '', $databaseUrl);
                        $wploadDB = explode("@", $wpUrl, 2);
                        $this->hostname = $wploadDB[0] ?? null;
                        $this->database = $wploadDB[1] ?? null;

                        // todo - support select DB file_put_contents(PATH_ZPLOG.'/buu', $this->hostname .' '.$this->database);
                  }
            } catch (\Throwable $th) {
                  zp_handle_error($th);
            }
      }

}

?>