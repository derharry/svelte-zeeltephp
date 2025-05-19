<?php

/**
 * ZeeltePHP MySQL Database Adapter
 *
 * Provides a simple and secure interface for MySQL database operations,
 * including schema inspection, CRUD operations, and safe value preparation.
 */
class ZeeltePHP_DB_MySQL2 {

      /** @var mysqli|null MySQLi connection resource */
      public $dbconn = null;

      /** @var string Database host */
      public $hostname = null;

      /** @var int|null Port to connect to */
      public $port = null;

      /** @var string Database username */
      public $username = null;

      /** @var string Database password */
      public $password = null;

      /** @var string Database name */
      public $database = null;

      /** @var bool Connection state */
      public $isConnected = false;

      /** @var array Cached table schemas */
      public $schemas = [];

      /** @var string Last executed query */
      public $last_query = '';

      /** @var string Last error or status message */
      public $last_message = '';

      /**
       * Constructor: Initializes connection parameters.
       * @param string $hostname MySQL server hostname
       * @param string $username MySQL username
       * @param string $password MySQL password
       * @param string $database MySQL database name
       */
      function __construct($hostname, $username, $password, $database) {
            $this->hostname = $hostname;
            $this->username = $username;
            $this->password = $password;
            $this->database = $database;
            $this->dbconn   = null;
      }

      /**
       * Establishes a connection to the MySQL database.
       * @return bool True on success, false on failure.
       */
      public function connect(): bool {
            if ($this->isConnected && $this->dbconn) return true;
            try {

                  $this->dbconn = new mysqli(
                        $this->hostname,
                        $this->username,
                        $this->password,
                        $this->database
                  );
                  if ($this->dbconn->connect_error) {
                        throw new RuntimeException("Connection failed: " . $this->dbconn->connect_error);
                  }
                  //$this->dbconn->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                  //$this->dbconn->set_charset('utf8mb4');
                  $this->isConnected = true;
                  return true;
            } catch (Throwable $e) {
                  $this->last_message = $e->getMessage();
                  $this->isConnected = false;
            }
            return false;
      }    
      
      /**
       * Close open connections 
       */
      function __destruct() {
            $this->disconnect();
      }

      /**
       * Closes the database connection.
       */
      public function disconnect(): void {
            if ($this->isConnected) {
                  $this->dbconn->close();
                  $this->isConnected = false;
                  // -- mysqli_close($this->dbconn);
            }
      }
      
      /**
       * Returns the last error message from the database connection.
       * @return string Error message or "Not connected"
       */
      public function last_error(): string {
            return $this->isConnected ? $this->dbconn->error : 'Not connected';
      }


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
                  $result = $this->dbconn->query($sqlStatement);
                  if ($result === false) {
                        throw new RuntimeException("Query failed: " . $this->last_error());
                  }
                  if ($result instanceof mysqli_result) {
                        return $result->fetch_all(MYSQLI_ASSOC);
                  }
                  return $result;
            }
      }

      /**
       * Inserts a record into the specified table.
       * @param string $table Table name
       * @param array  $data Associative array of column => value
       * @return int   Inserted row ID
       */
      public function insert(string $table, array $data): int {
            $columns = $this->validate_columns($table, $data);
            $values  = array_map([$this, 'prepare_value'], $data);
            $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)",
                  $this->dbconn->real_escape_string($table),
                  implode(', ', array_map(function($col) { return "`$col`"; }, $columns)),
                  implode(', ', $values)
            );
            $this->query($sql);
            return $this->dbconn->insert_id;
            /*
                  // first prepare an empty $insert_data set
                  $insert_data = $this->get_empty_dataset_from($table);
                  // lets ignore the 'id' for now... AutoIncrement
                  unset($insert_data['id']);
                  // copy $data to $insert_data
                  $insert_data = $this->prepare_merge_data($insert_data, $data);
                  // insert into db
                  $sql = "INSERT INTO $table SET ".$this->prepare_set($insert_data);
                  $iid = $this->query($sql);
                  return $this->dbconn->insert_id;
            */
      }
      
      /**
       * Updates records in the specified table.
       * @param string $table Table name
       * @param array  $where Associative array for WHERE clause
       * @param array  $data Associative array of columns to update
       * @return bool  True if successful
       */
      public function update(string $table, array $where, array $data): bool {
            $setClause   = $this->build_set_clause($table, $data);
            $whereClause = $this->build_where_clause($where);
            $sql = sprintf("UPDATE %s SET %s WHERE %s",
                  $this->dbconn->real_escape_string($table), $setClause, $whereClause);
            return (bool)$this->query($sql);
      }

      /**
       * Deletes records from the specified table.
       * @param string $table Table name
       * @param array  $where Associative array for WHERE clause
       * @return bool  True if successful
       */
      public function delete(string $table, array $where): bool {
            $whereClause  = $this->build_where_clause($where);
            $sqlStatement = "DELETE FROM " . $this->dbconn->real_escape_string($table) . " WHERE $whereClause";
            $this->query($sqlStatement);
            // Optionally, check affected rows
            return $this->dbconn->affected_rows > 0;
      }


      /**
       * copy $data_in into $data_org
       */
      function prepare_merge_data($data_org, $data_in) {
            $data_new = [];
            foreach ($data_org as $key=>$value) {
                  if ($key != 'id'  && isset($data_in[$key])) {
                  $data_new[$key] = $data_in[$key];
            }}
            return $data_new;
      }  

      /**
       * Builds a WHERE clause from an associative array. Like `id`='0' AND `name`='Joel'
       * @param array   $where Associative array of conditions
       * @return string WHERE clause
       */
      private function build_where_clause(array $where): string {
            $clauses = [];
            foreach ($where as $col => $val) {
                  $clauses[] = "`$col`=" . $this->prepare_value($val);
            }
            return implode(' AND ', $clauses);
      }

      /**
       * Builds a SET clause for an UPDATE or INSERT query. Like `id`='0', `name`='Joel', ...
       * @param string  $table Table name
       * @param array   $data Data to be set
       * @return string SET clause
       */
      private function build_set_clause(string $table, array $data): string {
            $columns = $this->validate_columns($table, $data);
            $set = [];
            foreach ($columns as $col) {
                  if ($data[$key] == 'current_timestamp()')
                        $set_data[] = "`$key`=".$data[$key];
                  else 
                        $set[] = "`$col`=" . $this->prepare_value($data[$col]);
            }
            return implode(', ', $set);
            /*
                  if (!is_null($data)) {
                  $columns  = array_keys($data);
                  $set_data = [];
                  foreach ($columns as $key) {
                        if ($data[$key] == 'current_timestamp()') {
                              $set_data[] = "`$key`='$data[$key]'";
                        }
                        else 
                              $set_data[] = "`$key`='$data[$key]'";
                  }
                  return implode(', ', $set_data);
                  } 
            */
      }

      /**
       * Escapes and quotes a value for safe use in SQL queries.
       * @param mixed   $value Value to escape
       * @return string Escaped and quoted value
       */
      public function prepare_value($value): string {
            $this->connect();
            return "'" . $this->dbconn->real_escape_string((string)$value) . "'";
      }
      
      /**
       * Retrieves and caches the schema for a table.
       * @param string $name Table name
       * @return array Schema information
       */
      public function get_table_schema(string $name): array {
            if (!isset($this->schemas[$name])) {
                  //--$sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
                  $sql  = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
                  $stmt = $this->dbconn->prepare($sql);
                  $stmt->bind_param('s', $name);
                  $stmt->execute();
                  $schema = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                  $this->schemas[$name] = $schema;
            }
            return $this->schemas[$name];
      }

      /**
       * Returns a list of column names for a given table.
       * @param string $name Table name
       * @return array List of column names
       */
      public function get_table_columns(string $name): array {
            return array_column($this->get_table_schema($name), 'COLUMN_NAME');
      }

      /**
       * Returns an empty resultset that can be used for like INSERT 
       * @param string $name Table name
       * @return array List of column names
       */
      public function get_empty_dataset_from($name) {
            $columns  = $this->get_table_columns($name);
            $empty_ds = [];
            foreach ($columns as $key=>$colName) $empty_ds[$colName] = null;
            return $empty_ds;
      }

}

?>