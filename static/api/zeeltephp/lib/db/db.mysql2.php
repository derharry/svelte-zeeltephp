<?php

/**
 * ZeeltePHP MySQL Database Adapter
     *
     * Provides a simple interface for MySQL database operations,
     * Including schema inspection, CRUD operations, and safe value preparation.
     * 
     * early stage at developping
     */
class ZeeltePHP_DB_MySQL2 {

     //#region Properties

          /** @var mysqli MySQLi connection resource */
          public $dbc = null;

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

          /** @var array Cached table schemas */
          public $schemas = [];

          /** @var string Last executed query */
          public $last_query = '';

          /** @var string Last error or status message */
          public $last_message = '';

     //#endregion 

     //#region Getters

          /**
           * Returns if a connection exits.
          * @return bool
          */
          function isConnected() : bool {
               return !empty($this->dbc);
          }

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
          public function last_error() {
               return $this->isConnected() ? $this->dbc->error : 'Not connected';
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
           * Closes the database connection.
           */
          public function close(): void {
               if (!$this->dbc || $this->last_message == 'closed') return;
               if ($this->dbc && $this->isConnected()) {
                    $this->dbc->close();
                    $this->last_message = 'closed';
               }
          }

          /**
           * Establishes a connection to the MySQL database.
           * @return bool True on success, false on failure.
           */
          public function connect(): bool {
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

          /*
          public function select(string $table, array $where, array $fields) {
               if ($this->connect()) {
                    $this->get_table_schema($table);
                    //$sql = "SELECT * FROM $table";
                    //return $this->query($sql);
                    return 'currently not implemented';
               }
               return false;
          }
          */
               
          /**
           * Inserts a record into the specified table.
           * @param string $table Table name
           * @param array  $data Associative array of column => value
           * @return int|false  Inserted row ID; False on problem
           */
          public function insert(string $table, array $data) : int|false {
               if ($this->connect()) {
                    // Prepare insert data
                    $insert_data = $this->get_empty_dataset_from($table);
                    $insert_data = $this->prepare_copy_data($insert_data, $data);
                    $insert_data = $this->prepare_validate_columns($table, $insert_data);
                    unset($insert_data['id']); // prevent inserting ID (safety, auto_inremenet)

                    // Prepare SQL-statement
                    $sql = sprintf("INSERT INTO %s SET %s",
                         $this->dbc->real_escape_string($table),
                         $this->build_set_clause($insert_data)
                    );

                    // Execute SQL and return new ID on success
                    $this->query($sql);
                    return $this->dbc->insert_id;
               }
               return false;
          }     
          
          /**
           * Updates records in the specified table.
           * @param string $table Table name
           * @param array  $where Associative array for WHERE clause (combined with AND only)
           * @param array  $data Associative array of columns to update
           * @return bool  True on success
           */
          public function update(string $table, array $where, array $data) : bool {
               if ($this->connect()) {
                    // Prepare update data
                    $update_data = $this->get_empty_dataset_from($table);
                    $update_data = $this->prepare_copy_data($update_data, $data);
                    $update_data = $this->prepare_validate_columns($table, $update_data);
                    unset($insert_data['id']); // prevent change of ID (safety)

                    // Prepare SQL-statement
                    $setClause   = $this->build_set_clause($update_data);
                    $whereClause = $this->build_where_clause($where);
                    $sql = sprintf("UPDATE %s SET %s WHERE %s",
                         $this->dbc->real_escape_string($table), 
                         $setClause, 
                         $whereClause
                    );

                    // Execute SQL and return true on success
                    $res = $this->query($sql);
                    return (bool)$this->affected_rows;
               }
               return false;
          }

          /**
           * Deletes records from the specified table.
           * @param string $table Table name
           * @param array  $where Associative array for WHERE clause (combined with AND only)
           * @return bool  True if successful
           */
          public function delete(string $table, array $where): bool {
               if ($this->connect()) {
                    // Prepare SQL-statement
                    $whereClause = $this->build_where_clause($where);
                    $sql = sprintf("DELETE FROM %s WHERE %s",
                         $this->dbc->real_escape_string($table), 
                         $whereClause
                    );

                    // Execute SQL and return true on success
                    $res = $this->query($sql);
                    return (bool)$this->affected_rows;
               }
               return false;
          }


     //#endregion 
          
     //#region Builders

          /**
           * Builds a WHERE clause from an associative array. Like `id`='0' AND `name`='Joel'
           * @param array   $where Associative array of conditions
           * @return string WHERE clause
           */
          private function build_where_clause(array $where): string {
               $clauses = [];
               foreach ($where as $col => $value) {
                    //if ($value == 'CURRENT_TIMESTAMP()')
                    //     $clauses[] = "`$col`=".$value;
                    //else 
                         $clauses[] = "`$col`=".$this->prepare_value($value);
               }
               return implode(' AND ', $clauses);
          }

          /**
           * Builds a SET clause for an UPDATE or INSERT query. Like `id`='0', `name`='Joel', ...
           * @param array   $data Data to be set
           * @return string SET clause
           */
          private function build_set_clause(array $data): string {
               $clauses = [];
               foreach ($data as $col => $value) {
                    //if ($value == 'CURRENT_TIMESTAMP()')
                    //     $clauses[] = "`$col`=".$value;
                    //else 
                         $clauses[] = "`$col`=".$this->prepare_value($value);
               }
               return implode(', ', $clauses);
          }

     //#endregion 
          
     //#region Prepares

          /**
           * copy $data_src into $data_to
           */
          function prepare_copy_data($data_to, $data_src) {
               $data_new = [];
               foreach ($data_to as $key=>$value) {
                    if (isset($data_src[$key]))
                         $data_new[$key] = $data_src[$key];
               }
               return $data_new;
          }

          /**
           * Escapes and quotes a value for safe use in SQL queries.
           * @param mixed   $value Value to escape
           * @return string Escaped and quoted value
           */
          public function prepare_value($value): string {
               $this->connect();
               return "'" . $this->dbc->real_escape_string((string)$value) . "'";
          }

          /**
           * Escapes and quotes a values from data for safe use in SQL queries.
           * @param mixed   $value Value to escape
           * @return string Escaped and quoted value
           */
          public function prepare_values($data): string {
               $this->connect();
               foreach ($data as $key => $value) {
                    $data[$key] = $this->prepare_value($value);
               }
               return $data;
          }

          /** Validates and returns only columns that exist in the table schema. 
           * @param string $table Table name 
           * @param array  $data Data to be filtered 
           * @return array Filtered data with valid columns 
           */ 
          public function prepare_validate_columns(string $table, array $data): array { 
               $validColumns = $this->get_table_columns($table); 
               return array_intersect_key($data, array_flip($validColumns));
          }

     //#endregion 
          
     //#region Schemas 

          /**
           * Returns an empty resultset that can be used for like INSERT, UPDATE 
           * @param string $name Table name
           * @return array|false List of column names
           */
          public function get_empty_dataset_from($name) : array | false {
               $empty_ds = [];
               if ($this->connect()) {
                    $columns  = $this->get_table_columns($name);
                    foreach ($columns as $key=>$colName) 
                         $empty_ds[$colName] = null;
               }
               return $empty_ds;
          }

          /**
           * Returns a list of column names for a given table.
           * @param string $name Table name
           * @return array List of column names
           */
          public function get_table_columns(string $name): array {
               $this->connect();
               return array_column($this->get_table_schema($name), 'COLUMN_NAME');
          }

          /**
           * Retrieves and caches the schema for a table.
           * @param string $name Table name
           * @return array|false Schema information
           */
          public function get_table_schema(string $tableName): array | false {
               if (!$this->connect()) return false;
               if (!isset($this->schemas[$tableName])) {
                    $sql = "
                         SELECT 
                              COLUMN_NAME,
                              DATA_TYPE,
                              CHARACTER_MAXIMUM_LENGTH,
                              IS_NULLABLE,
                              COLUMN_KEY,
                              COLUMN_DEFAULT,
                              COLUMN_COMMENT         
                         FROM INFORMATION_SCHEMA.COLUMNS
                         WHERE 
                              TABLE_NAME = '$tableName';
                    ";
                    $result = $this->query($sql);

                    // Transform data
                    $dataSchemaFields = [];
                    foreach ($result as $field) {
                         $dataSchemaFields[  $field['COLUMN_NAME'] ] = $field;
                         //$dataSchemaFields[  $field['COLUMN_NAME'] ][ 'COLUMN_COMMENT' ] = load_comment_config($field['COLUMN_COMMENT']);
                    }

                    // Save schema
                    $this->schemas[$tableName] = $dataSchemaFields;
               }
               if (isset($this->schemas[$tableName])) 
                    return $this->schemas[$tableName];
               return false;
          }

     //#endregion 

}

?>