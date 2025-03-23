<?php

//require_once('zeeltephp/lib/db/db.sqlite3.php');
require_once('zeeltephp/lib/db/db.wordpress.php');
require_once('zeeltephp/lib/db/db.mysql2.php');
//require_once('zeeltephp/lib/db/db.mariadb.php');

class ZP_DB {

      public $message  = null;
      public $dbsrc    = null;
      public $dbconn   = null;

      public $dbtype   = null; // scheme
      public $hostname = null;
      public $port     = null;
      public $username = null;
      public $password = null;
      public $database = null;


      // Add magic method to forward calls to dbsrc
      public function __call($method, $args) {
            try {
                  if ($this->dbsrc && method_exists($this->dbsrc, $method)) {
                        return $this->dbsrc->$method(...$args);
                  }
                  throw new \Exception("Method $method not found in DB adapter");
            }
            catch (\Throwable $th) {
                  zp_error_handler($th);
            }
      }
      
      function __construct($ZEELTEPHP_DATABASE_URL = null) {
            if (is_null($ZEELTEPHP_DATABASE_URL)) return;
            try {
                  $this->parse_connectionUrl($ZEELTEPHP_DATABASE_URL);


                  // initialize
                  $className  = 'ZeeltePHP_DB_'.$this->dbtype;     // Correct format: ZP_DB_mysql // ZeeltePHP_DB_wordpress
                  $classFile  = 'db.'.$this->dbtype.'.php'; // File path

                  if (!class_exists($className)) {
                        if (is_file($classFile)) {
                              require_once $classFile;
                        } else {
                              $this->message = "Provider file '$classFile' not found.";
                              return;
                        }
                  }
                  
                  if (class_exists($className)) {
                        $this->dbsrc = new $className($ZEELTEPHP_DATABASE_URL);
                  } else {
                        $this->message = "Class $className not found.";
                  }

            } catch (\Throwable $th) {
                  zp_error_handler($th);
            }
      }

      function parse_connectionUrl($databaseUrl) {
            try {
                  //ZEELTEPHP_DATABASE_URL=provider://../wordpress-project/
                  //ZEELTEPHP_DATABASE_URL=provider://username:password@hostname/database
                  $parts = parse_url($databaseUrl);
                  $this->dbtype = isset($parts['scheme']) ? $parts['scheme'] : null;
                  // by path
                  $this->database = isset($parts['path']) ? $parts['path'] : null;
                  // by credentials
                  $this->username = isset($parts['user']) ? $parts['user'] : null;
                  $this->password = isset($parts['pass']) ? $parts['pass'] : null;
                  $this->hostname = isset($parts['host']) ? $parts['host'] : null;
                  $this->database = isset($parts['path']) ? $parts['path'] : null;
                  $this->port     = isset($parts['port']) ? $parts['port'] : null;

                  if ($this->dbtype == 'wordpress') {
                        //repair the string to datase (full path again because first .. is stripped)
                        $this->database = $this->hostname.$this->database;
                        $this->hostname = null;
                  }
            } catch (\Throwable $th) {
                  zp_error_handler($th);
            }
      }

}

?>