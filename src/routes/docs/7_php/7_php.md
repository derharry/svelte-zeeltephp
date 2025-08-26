# PHP


## Globals
These global variables are accessible from anywhere in your PHP code.
- **$zpAR**: class ZP_ApiRouter.
- **$env**:  `.env` configuration. 
- **$db**:   Shared DB-Connection. 


## PHP error log
- Errors are logged to `/php_log`, `/BUILD/api/zeeltephp/php_log/` when `ini_set('error_log')` is allowed.
- At error output or response  thhe paths are changed for readability 
  - the path `/api/zeeltephp` is shortened to `/api`.
  - and full system paths are changed to relative paths.
- For saving custom log-files you can use `PATH_ZPLOG` $path = PATH_ZPLOG . "mylog.txt"
  - the method **zp_log( $content )** writes the content into `log.log`.


## PHP.exe
ZeeltePHP runs on CLI/php.exe when the .env.ZEELTEPHP_EXE=/path/to/php.exe variable is set. 
A httpd is then not required. See [.env Configuration](#env-configuration) for details.


---


## Database Provider 

***experimental!  in development!***

<br>There are currently 2 classes for MySQL and WordPress available at `/api/zeeltephp/lib/db`. 
<br>They can be used for any custom query and contain some helper methods for like basic CRUD operations.
<br>Any attribute or method that is available at `mysqli` or `wp-load` can be used - they are forwarded via PHP's magic getter and method.
<br>
<br>When `.env.ZEELTEPHP_DATABASE_URL` is set, ZeeltePHP uses the corresponding internal database provider class and is available from Global `$db`.


```sh
ZEELTEPHP_DATABASE_URL=mysql2://username:password@hostname:port/database
ZEELTEPHP_DATABASE_URL=wordpress://path/to/your/wp-load.php
```


```php
<?php 

   function example_db_usage() {
      global $db;

      // optional connect
      //   the connect() is automatically opened as soon any $db->METHOD() is called;
      $db->connect();

      // is there a connection ?
      if ($db->connect()) {
            // returns true or false on succes 
            // returns true when already connected to avoid multiple connections.
      }
      if ($db->isConnected()) {
            // returns true or false if is connected.
      }

      // just a query
      $res = $db->query('SELECT * FROM ...'); 

      // prepare data for CRUD operations
      //  * $data must be an array key-value-list where key = column-name.
      //  * The data gets prepared, sanitized for matching key/columns. 
      //  * non matching key/columns will be ignored.
      $data = $_POST;

      // insert data
      //   returns the inserted ID on success;  false on failure
      $insertedID = $db->insert('tableName', $data); 

      // update data
      //  returns true of false; 
      //       true  on 1+ affected-rows
      //       false on 0  affected-rows
      //  * id, uuid are ignored if in $data
      //  @param id is an array key-value-list of matches combined with AND
      $success = $db->update('tableName', ['id' => $id], $data);
                        
      // delete data
      //  returns true of false; 
      //       true  on 1+ affected-rows
      //       false on 0  affected-rows
      //  @param id is an array key-value-list of matches combined with AND
      $success = $db->delete('tableName', ['id' => $id]);

      // the number of affected rows of last query
      $affectedRows = $db->affected_rows;

      // the error message of last query
      $error = $db->last_error();

      // optional disconnect
      //   the connection is automatically closed at __destruct();
      $db->close();

   }
   
?>