
<?php

interface IZP_DB {
     public function __get($name);
     public function __call($method, $args);

     public function last_error()  : string;
     public function isConnected() : bool;
     public function affected_rows() : int;
     // public function insert_id() : int; mysql2, wpdb - same insert_id


     public function connect();
     public function close();
     public function query($sql);

     public function real_escape_string($value);
}

/**
 * ZeeltePHP Database Adapter 
 *
 * Dynamically loads and proxies to the correct DB provider class.
 * Based on .env.ZEELTEPHP_DATABASE_URL. 
 */
class ZP_DB {

     //#region Properties 

          /** @var mysqli MySQLi connection resource */
          public $dbc = null;

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
          
          /** @var array Cached table schemas */
          public $schemas = [];

          /** @var string Last executed query */
          public $last_query = '';

          /** @var string Last error or status message */
          public $last_message = '';

          /** @var string|null is true when wp-load.php file is found */
          public $is_connected = false;

     //#endregion 

     //#region Getter, Calls 

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
               $this->last_message = "ZP_DB: Method $method not found in DB adapter";
               //throw new Exception("ZP_DB: Method $method not found in DB adapter");
          }

          /**
           * Returns if a connection exits.
           * @return bool
           */
          public function isConnected(): bool {
               return $this->dbsrc !== null && $this->dbsrc->isConnected();
          }
          
     //#endregion 

     //#region Constructor, Destructor, Connect, Close

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
                         return $this->dbsrc;
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
               $this->close();
          } 

          /**
           * Close the DB connection
           */
          public function connect() {
               if (!$this->isConnected()) {
                    $this->dbsrc->connect();
               }
               return $this->isConnected();
          } 

          /**
           * Close the DB connection
           */
          function close() {
               if ($this->isConnected() && $this->dbsrc !== null)
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
                    $result = $this->dbsrc->query($sqlStatement);
                    return $result;
               }
          }

     //#endregion 

     //#region Query, CRUD


          public function insert(string $table, array $data) : int|false {
               if ($this->connect()) {
                    $insert_data = $this->get_empty_dataset_from($table);
                    $insert_data = $this->prepare_copy_data($insert_data, $data);
                    $insert_data = $this->prepare_validate_columns($table, $insert_data);
                    unset($insert_data['id']);   // prevent inserting ID (safety, auto_inremenet)

                    // Prepare SQL-statement
                    $sql = sprintf("INSERT INTO %s SET %s",
                         //$this->prepare_value($table),
                         $this->real_escape_string($table),
                         $this->build_set_clause($insert_data)
                    );

                    // Execute SQL and return new ID on success
                    $this->query($sql);
                    return $this->dbsrc->insert_id;
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
                    unset($update_data['id']); // prevent change of ID (safety)

                    // Prepare SQL-statement
                    $sql = sprintf("UPDATE %s SET %s WHERE %s",
                         $this->real_escape_string($table),
                         $this->build_set_clause($update_data), 
                         $this->build_where_clause($where)
                    );

                    // Execute SQL and return true on success
                    $res = $this->query($sql);
                    return (bool)$this->dbsrc->affected_rows();
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
                    $sql = sprintf("DELETE FROM %s WHERE %s",
                         $this->real_escape_string($table),
                         $this->build_where_clause($where)
                    );

                    // Execute SQL and return true on success
                    $res = $this->query($sql);
                    return (bool)$this->dbsrc->affected_rows();
               }
               return false;
          }

     //#endregion 

     //#region Prepares

          /**
           * Escapes and quotes a value for safe use in SQL queries.
               * @param mixed   $value Value to escape
               * @return string Escaped and quoted value
               */
          public function prepare_value($value): string {
               $this->connect();
               return "'".$this->dbsrc->real_escape_string((string)$value)."'";
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

          /**
           * copy $data_src into $data_to
           */
          public function prepare_copy_data($data_to, $data_src) {
               $data_new = [];
               foreach ($data_to as $key=>$value) {
                    if (isset($data_src[$key]))
                         $data_new[$key] = $data_src[$key];
               }
               return $data_new;
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

     //#region Builders

          /**
           * Builds a WHERE clause from an associative array. Like `id`='0' AND `name`='Joel'
           * @param array   $where Associative array of conditions
           * @return string WHERE clause
           */
          public function build_where_clause(array $where): string {
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
          public function build_set_clause(array $data): string {
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