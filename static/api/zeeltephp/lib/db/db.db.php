<?php
/**
 * db.db.php
 */

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


      // Handle property access
      public function __get($name) {
            return $this->dbsrc->$name;
            /*
            try {
                  if ($this->dbsrc && property_exists($this->dbsrc, $name)) {
                        return $this->dbsrc->$name;
                  }
                  elseif ($this->dbsrc && isset($this->dbsrc->$name)) {
                        return $this->dbsrc->$name;
                  }
                  throw new \Exception("ZPDB: Property $name not found in DB adapter");
            } catch (\Throwable $th) {
                  zp_handle_error($th);
            }
            if ($this->dbsrc && property_exists($this->dbsrc, $name)) {
                  return $this->dbsrc->$name;
            }*/
      }

      // Add magic method to forward calls to dbsrc
      public function __call($method, $args) {
            return $this->dbsrc->$method(...$args);
            /*
            try {
                  if ($this->dbsrc && method_exists($this->dbsrc, $method)) {
                        return $this->dbsrc->$method(...$args);
                  }
                  elseif ($this->dbsrc && isset($this->dbsrc->$method)) {
                        return $this->dbsrc->$method;
                  }
                  throw new \Exception("ZPDB: Method $method not found in DB adapter");
            }
            catch (\Throwable $th) {
                  zp_handle_error($th);
            }*/
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
                        if ($this->username != null) {
                              $this->dbsrc = new $className(
                                    $this->hostname, 
                                    $this->username, 
                                    $this->password, 
                                    $this->database);
                        }
                        else {
                              $this->dbsrc = new $className($ZEELTEPHP_DATABASE_URL);
                        }
                  } else {
                        $this->message = "Class $className not found.";
                  }

            } catch (\Throwable $th) {
                  zp_handle_error($th);
            }
      }

      function parse_connectionUrl($databaseUrl) {
            try {
                  //ZEELTEPHP_DATABASE_URL=provider://../wordpress-project/wp.load.php@database
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
                        // parse_url destructs wordpress url into:
                        //     dbtype   = wordpress
                        //     hostname = ../ 
                        //     database = /../wp-load.php 
                        // to be safe - let's destruct it my-self
                        $wpUrl   = str_replace('wordpress://', '', $databaseUrl);
                        $wploadDB = explode("@", $wpUrl);
                        if (count($wploadDB)>1)
                              $this->database = $wploadDB[1];
                        $this->hostname = $wploadDB[0];
                  }
            } catch (\Throwable $th) {
                  zp_handle_error($th);
            }
      }

}

?>