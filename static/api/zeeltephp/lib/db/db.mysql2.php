<?php

require_once('db.db.php');

/**
 * ZeeltePHP MySQL Database Adapter
     *
     * Provides a simple interface for MySQL database operations,
     * Including schema inspection, CRUD operations, and safe value preparation.
     * 
     * early stage at developping
     */
class ZeeltePHP_DB_MySQL2 extends ZP_DB implements IZP_DB {

     //#region Properties

          /** @var string Database host */
          public $hostname = null;

          /** @var int|null Port to connect to */
          public $port = 3306;

          /** @var string Database username */
          public $username = null;

          /** @var string Database password */
          public $password = null;

          /** @var string Database name */
          public $database = null;

     //#endregion 

     //#region Getters

          /**
           * Magic getter - proxies to WordPress $wpdb properties
           */
          public function __get($name) 
          {
               $this->connect();
               return $this->dbc->$name ?? null;
          }

          /**
           * Magic method - proxies to WordPress $wpdb methods
           */
          public function __call($method, $args) {
               $this->connect();
               return $this->dbc->$method(...$args) ?? null;
          }

          /**
           * Returns the last error message from the database connection.
           * @return string Error message or "Not connected"
           */
          public function last_error():string {
               return $this->isConnected() ? $this->dbc->error : 'Not connected';
          }

          public function isConnected(): bool {
               return $this->dbc !== null;
          }

          public function affected_rows(): int {
               return $this->dbc->affected_rows;
          }

     //#endregion 

     //#region Constructor, connect, close 

          /**
           * Constructor: Initializes connection parameters.
           * @param string $hostname MySQL server hostname
           * @param string $username MySQL username
           * @param string $password MySQL password
           * @param string $database MySQL database name
           * @param int    $port     MySQL port
           */
          function __construct($hostname, $username, $password, $database, $port = 3306) {
               $this->hostname = $hostname;
               $this->username = $username;
               $this->password = $password;
               $this->database = $database;
               $this->port     = $port;
               $this->dbc      = null;
               $this->last_message = 'init';
          }

          /**
           * Close open connections 
           */
          function __destruct() {
               $this->close();
          }

          /**
           * Establishes a connection to the MySQL database.
           * @return bool True on success, false on failure.
           */
          public function connect(): bool {
               $this->last_message = "connect";
               if ($this->isConnected()) return true;
               try {
                    $this->last_message = 'connecting';

                    // Connect
                    $this->dbc = new mysqli(
                         $this->hostname,
                         $this->username,
                         $this->password,
                         $this->database
                    );

                    if ($this->isConnected()) {
                         $this->last_message = 'connected';

                         // Set Defaults
                         //$this->dbconn->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                         //$this->dbconn->set_charset('utf8mb4');

                         return true;
                    }
                    else {
                         $this->last_message = 'Connection failed: '. $this->dbconn->connect_error;
                         // Check the problem
                         //if ($this->dbconn->connect_error) {
                         //     throw new RuntimeException("Connection failed: " . $this->dbconn->connect_error);
                         //}
                    }
               } catch (Throwable $e) {
                    $this->last_message = $e->getMessage();
               }
               return false;
          }

          /**
           * Closes the database connection.
           */
          public function close(): void {
               if (!$this->dbc || $this->last_message == 'closed') return;
               if ($this->dbc && $this->isConnected()) {
                    $this->dbc->close();
                    $this->last_message = 'closed';
               }
          }

     //#endregion 

     //#region Query, CRUD

          /**
           * Executes a SQL query and returns the result.
           * @param string $sql SQL query string
           * @return array|bool Query result as array, true/false for non-select queries
           * @throws RuntimeException If the query fails
           */
          public function query($sqlStatement) {
               $this->last_query = $sqlStatement;
               if (!$this->connect()) {
                    throw new RuntimeException("Database connection failed. ".$this->last_error());
               }
               if ($this->connect()) {
                    $result = $this->dbc->query($sqlStatement);
                    if ($result === false) {
                         throw new RuntimeException("Query failed: " . $this->last_error());
                    }
                    if ($result instanceof mysqli_result) {
                         return $result->fetch_all(MYSQLI_ASSOC);
                    }
                    return $result;
               }
          }

     //#endregion 


          public function real_escape_string($value): string {
               $this->connect();
               return $this->dbc->real_escape_string((string)$value);
          }

}

?>