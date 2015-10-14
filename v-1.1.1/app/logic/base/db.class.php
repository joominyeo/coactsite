<?php

/*
  adapted from
  http://php-pdo-wrapper-class.googlecode.com/files/ppwc-1.0.2.zip
  https://github.com/indieteq/PHP-MySQL-PDO-Database-Class.git

 */

class DB {
    # @object, The PDO object

    private $pdo;

    # @object, PDO statement object
    private $sQuery;

    # @array,  The database settings
    private $settings;

    # @bool ,  Connected to the database
    private $bConnected = false;

    # @object, Object for logging exceptions	
    private $log;

    # @array, The parameters of the SQL query
    private $parameters;
    private $dsn = '';
    private $user = '';
    private $passwd = '';
    private $options = array();
    private $error = '';
    private $RowCount = 0;

    public function RowCount() {
        return $this->RowCount;
    }

    public function __construct($dsn, $user = "", $passwd = "", $options = NULL) {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->passwd = $passwd;

        if ($options === NULL) {
            $this->options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
        } else {
            $this->options = $options;
        }
    }

    private function Connect() {
        try {
            # Read settings from INI file
            $this->pdo = new PDO($this->dsn, $this->user, $this->passwd);
            # We can now log any exceptions on Fatal error. 
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            # Disable emulation of prepared statements, use REAL prepared statements instead.
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            # Connection succeeded, set the boolean to true.
            $this->bConnected = true;
        } catch (PDOException $e) {
            # Write into log
            $this->ExceptionLog($e->getMessage());
            die();
        }
    }

    /**
     * 	Every method which needs to execute a SQL query uses this method.
     * 	
     * 	1. If not connected, connect to the database.
     * 	2. Prepare Query.
     * 	3. Parameterize Query.
     * 	4. Execute Query.	
     * 	5. On exception : Write Exception into the log + SQL query.
     * 	6. Reset the Parameters.
     */
    private function Init($query, $parameters = "") {
        # Connect to database
        if (!$this->bConnected) {
            $this->Connect();
        }
        try {
            # Prepare query
            $this->sQuery = $this->pdo->prepare($query);

            # Add parameters to the parameter array	
            $this->bindMore($parameters);

            # Bind parameters
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $this->sQuery->bindParam($parameters[0], $parameters[1]);
                }
            }

            # Execute SQL 
            $this->succes = $this->sQuery->execute();
        } catch (PDOException $e) {
            # Write into log
            if (!empty($this->sQuery->queryString))
                $query = $this->sQuery->queryString;
            $this->ExceptionLog($e->getMessage(), $query);
            die($e->getMessage());
        }

        # Reset the parameters
        $this->parameters = array();
    }

    /**
     * 	@void 
     *
     * 	Add the parameter to the parameter array
     * 	@param string $para  
     * 	@param string $value 
     */
    public function bind($para, $value) {
        $this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value;
    }

    /**
     * 	@void
     * 	
     * 	Add more parameters to the parameter array
     * 	@param array $parray
     */
    public function bindMore($parray) {
        if (empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }

    /**
     *   	If the SQL query  contains a SELECT statement it returns an array containing all of the result set row
     * 	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     *
     *   	@param  string $query
     * 	@param  array  $params
     * 	@param  int    $fetchmode
     * 	@return mixed
     */
    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {
        $query = trim($query);

        $this->Init($query, $params);

        if (stripos($query, 'select') === 0) {
            $this->RowCount = $this->sQuery->rowCount();
            return $this->sQuery->fetchAll($fetchmode);
        } elseif (stripos($query, 'insert') === 0 || stripos($query, 'update') === 0 || stripos($query, 'delete') === 0) {
            return $this->sQuery->rowCount();
        } else {
            return NULL;
        }
    }

    /**
     * 	Returns an array which represents a column from the result set 
     *
     * 	@param  string $query
     * 	@param  array  $params
     * 	@return array
     */
    public function column($query, $params = null) {
        $this->Init($query, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);

        $column = null;

        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }

        return $column;
    }

    /**
     * 	Returns an array which represents a row from the result set 
     *
     * 	@param  string $query
     * 	@param  array  $params
     *   	@param  int    $fetchmode
     * 	@return array
     */
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {
        $this->Init($query, $params);
        $this->RowCount = $this->sQuery->rowCount();
        return $this->sQuery->fetch($fetchmode);
    }

    /**
     * 	Returns the value of one single field/column
     *
     * 	@param  string $query
     * 	@param  array  $params
     * 	@return string
     */
    public function single($query, $params = null) {
        $this->Init($query, $params);
        return $this->sQuery->fetchColumn();
    }

    /**
     * 	Returns last insert id
     *
     * 	@param  string $query
     * 	@param  array  $params
     * 	@return string
     */
    public function insertId() {
        return $this->pdo->lastInsertId();
    }

    /** 	
     * Writes the log and returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return string
     */
    private function ExceptionLog($message, $sql = "") {
        $exception = 'Unhandled Exception. <br />';
        $exception .= $message;
        $exception .= "<br /> You can find the error back in the log.";

        if (!empty($sql)) {
            # Add the Raw SQL to the Log
            $message .= "\r\nRaw SQL : " . $sql;
        }
        # Write into log
        errorLog($message);
        $this->error = $message;

        //return error message
        return $exception;
    }

    /**
     * return list of tables
     * 
     * @param string $tableName table name to search 
     * @return array
     */
    public function list_tables($tableName = '') {
        $sql = 'SHOW TABLES';
        if (trim($tableName) != '')
            $sql .= ' LIKE "' . $tableName . '"';
        if (!$this->bConnected) {
            $this->Connect();
        }

        $query = $this->pdo->query($sql);
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * return DESCRIBE result for table
     * 
     * @param string $tableName table name to DESCRIBE
     * @return array
     */
    public function describeTable($tableName) {
        $sql = 'DESCRIBE '.$tableName;
        if (!$this->bConnected) {
            $this->Connect();
        }

        $query = $this->pdo->query($sql);
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

}

?>