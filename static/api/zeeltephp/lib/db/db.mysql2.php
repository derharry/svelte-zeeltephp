<?php 


//ZeeltePHP_DB_wordpress
class ZeeltePHP_DB_MySQL2 {

    public $host = '';
    public $username = '';
    public $password = '';
    public $database = '';
    public $dbconn   = '';
    public $isConnected  = false;

    public $last_query = '';

    public $schemas = [];
    
    function __construct($host, $username, $password, $database) {
        $this->host     = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->dbconn    = null;
    }

    public function connect() {
        if (!$this->dbconn) {
            $this->dbconn = mysqli_connect($this->host, $this->username, $this->password, $this->database);
            $this->isConnected  = false;
            //$this->dbconn->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            //$this->dbconn->set_charset('utf8mb4');
        }
        if (!is_null($this->dbconn)) 
            return true;
        return false;
    }
    
    function __destruct() {
        $this->disconnect();
    } 

    public function disconnect() {
        if ($this->dbconn)
            mysqli_close($this->dbconn);
    }

    public function last_error() {
        if ($this->connect())
            return $this->dbconn->error;
    }

    public function get_table_schmema($name) {
        if (!isset($this->schemas[$name])) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$name'";
            $schema = $this->query($sql);
            $this->schemas[$name] = $schema;
        }
        return $this->schemas[$name];
    }

    public function get_table_columns($name) {
        $schema  = $this->get_table_schmema($name);
        $columns = [];
        foreach ($schema as $_) {
            $columns[] = $_['COLUMN_NAME'];
        }
        return $columns;
    }

    public function get_empty_dataset_from($name) {
        $columns  = $this->get_table_columns($name);
        $empty_ds = [];
        foreach ($columns as $key=>$colName)
            $empty_ds[$colName] = null;
        return $empty_ds;
    }

    public function query($sql) {
        //try {
            if (!$this->connect()) {
                throw new Error('db not connected');
            }
            if ($this->connect()) {
                $this->last_query = $sql;
                $result = $this->dbconn->query( $sql );
                if ($result) {
                    if (is_bool($result)) {
                        return $result;
                    }
                    else
                        return $result->fetch_all( MYSQLI_ASSOC );
                }
                //else
                //    return $this->dbconn->error;
            }
        //} catch (\Throwable $th) {
        //    throw new Error $th->getMessage();
        //}
    }

    public function prepare($data) {
        try {
            if ($this->isConnected) {
                
                //error_log('->prepare');
                if (is_array($data)) {
                    //error_log('->array');
                    $data_prepared = [];
                    foreach ($data as $key=>$value) {
                        //error_log($key."=".$value);
                        $data_prepared[$key] = @mysqli_real_escape_string($this->dbconn, $value);
                    }
                    return $data_prepared;
                }
                else if (is_object($data)) {
                    //error_log('->object');
                    return 'todo-object-prepare...';
                }
                else if (is_null($data)) {
                    $data = '';
                }
                else if (is_string($data)) {
                    return mysqli_real_escape_string($this->dbconn, $data);
                }
                else {
                    //error_log('->any');
                    return mysqli_real_escape_string($this->dbconn, $data);
                }
            }
        } catch (\Throwable $th) {
            error_log('class.db.mysql.php/prepare() '.$th);
        }
    }

    public function prepare_set($data) {
        try {
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
        } catch (\Throwable $th) {
            error_log('class.db.mysql.php/prepare_set() '.$th);
        }
    }

    /**
     * prepare $data to like `id`='0' AND `name`='x'
     */
    public function prepare_where($where) {
        $whereParts = [];
        foreach ($where as $key => $value) 
            $whereParts[] = "`$key`='$value'";
        $whereString = implode(' AND ', $whereParts);
        return $whereString;
    }

    /**
     * copy $data_in into $data_org
     */
    function prepare_merge_data($data_org, $data_in) {
        $data_new = [];
        foreach ($data_org as $key=>$value) {
            if ($key != 'id'  && isset($data_in[$key])) {
                //error_log('set_key: '.$key.' = '.$_POST[$key]);
                $data_new[$key] = $data_in[$key];
        }}
        return $data_new;
    }    

    public function insert($table, $data) {
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
    }

    /**
     * @param  string name of table
     * @param  array  list of key value pairs that will be 
     * @return boolean successfully deleted
     */
    public function delete($table, $where) {
        $whereString = $this->prepare_where($where);
        // set queries
        $sqlDelete = "DELETE FROM $table WHERE $whereString";
        $sqlSelect = "SELECT * FROM $table WHERE $whereString";

        // delete
        $this->query($sqlDelete);
        $sqlTemp = $this->last_query;
        // check deletion
        $check = $this->query($sqlSelect);

        $this->last_query = $sqlTemp;
        if (sizeof($check) == 0)
            return true;
        else
            return false;
    }

    public function update($table, $where, $data) {
        $whereString = $this->prepare_where($where);
        $dataUpdate  = $this->prepare_set($data);
        
        // first get current dataset
        $sqlSelect   = "SELECT * FROM $table";
        $currentData = $this->query($sqlSelect);

        // update the data
        $updateData  = $this->prepare_merge_data($currentData[0], $data);
        $updateData  = $this->prepare_set($updateData);
        $sqlUpdate   = "UPDATE $table SET $updateData WHERE $whereString";
        $res = $this->query($sqlUpdate);
        return $res;
    }
}


?>
