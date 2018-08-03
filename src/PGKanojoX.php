<?php 
include_once "KanojoX.php";
include_once "PGSQL_Result.php";
/**
 * A PostgreSQL Connection object
 * 
 * Kanojo means girlfriend in japanese and this class saves the connection data structure used to connect to
 * an PostgreSQL database.
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class PGKanojoX extends KanojoX
{
    const DEFT_STMT_NAME = "sql_statement";
    /**
     * @var string $schema The database schema used to filter the table definition
     */
    public $schema;
    /**
     * Open a PostgreSQL Database connection
     *
     * @return resource The database connection object
     */
    public function connect()
    {
        try {
            $host = $this->host;
            $port = $this->port;
            $dbname = $this->db_name;
            $username = $this->user_name;
            $passwd = $this->password;
            if (!isset($this->host) || strlen($host) == 0)
                $host = "127.0.0.1";
            $connString = $strConn = "host='$host' port='$port' dbname='$dbname' user='$username' ";
            if (isset($passwd) && strlen($passwd) > 0)
                $connString .= "password='$passwd'";
            $this->connection = pg_connect($connString);
            return $this->connection;
        } catch (Exception $e) {
            return $this->error(sprintf(ERR_BAD_CONNECTION, $e->getMessage()));
        }
    }
    /**
     * Closes a PostgreSQL database connection resource. 
     * The connection is the last connection made by pg_connect().
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close()
    {
        $this->free_result();
        if (!$this->connection)
            throw new Exception(ERR_NOT_CONNECTED);
        return pg_close($this->connection);
    }
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    public function free_result()
    {
        foreach ($this->statementsIds as &$statementId)
            pg_free_result($statementId);
    }

    /**
     * Get the last error message string of a connection
     *
     * @param string|null $sql The last executed statement. Can be null
     * @param ConnectionError $error If the error exists pass the error
     * @return ConnectionError The connection error 
     */
    public function error($sql, $error = null)
    {
        if (is_null($error)) {
            $this->error = new ConnectionError();
            $this->error->code = pg_last_error($this->connection);
            $this->error->message = pg_result_status($this->connection);
            $this->error->sql = $sql;
        } else
            $this->error = $error;
        return $this->error;
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param string $sql The SQL Statement
     * @param array|null $variables The colon-prefixed bind variables placeholder used in the statement, can be null.
     * @throws Exception En Exception is raised if the execution result fails
     * @return object Returns the execute response on success or the connection error on failure. 
     */
    public function execute($sql, $variables = null)
    {
        if (isset($variables) && is_array($variables)) {
            $result = pg_prepare($this->connection, self::DEFT_STMT_NAME, $sql);
            $sql = (object)(array(NODE_SQL => $sql, NODE_PARAMS => $variables));
            if ($result) {
                $vars = array();
                foreach ($variables as &$value)
                    array_push($vars, $value);
                $result = pg_execute($this->connection, self::DEFT_STMT_NAME, $vars);
            }
        } else {
            $result = pg_send_query($this->connection, $sql);
            $result = pg_get_result($this->connection);
        }
        if (!$result || pg_result_status($result) != PGSQL_Result::PGSQL_COMMAND_OK) {
            $err = $this->error($sql, $this->get_error($result == false ? null : $result, $sql));
            throw new UrabeSQLException($err);
        } else
            return (new UrabeResponse())->get_execute_response(true, pg_affected_rows($result), $sql);
    }
    /**
     * Returns an associative array containing the next result-set row of a 
     * query. Each array entry corresponds to a column of the row. 
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @throws Exception An Exception is thrown parsing the SQL statement or by connection error
     * @return array Returns an associative array. 
     * */
    public function fetch_assoc($sql, $variables = null)
    {

        $rows = array();
        if (!$this->connection)
            throw new Exception(ERR_NOT_CONNECTED);
        if (isset($variables) && is_array($variables)) {
            $result = pg_prepare($this->connection, self::DEFT_STMT_NAME, $sql);
            $sql = (object)(array(NODE_SQL => $sql, NODE_PARAMS => $variables));
            if ($result) {
                $vars = array();
                foreach ($variables as &$value)
                    array_push($vars, $value);
                $ok = pg_execute($this->connection, self::DEFT_STMT_NAME, $vars);
            } else {
                $err = $this->error($sql, $this->get_error($result == false ? null : $result, $sql));
                throw new UrabeSQLException($err);
            }
        } else
            $ok = pg_query($this->connection, $sql);
        
        //fetch result
        if ($ok) {
            while ($row = pg_fetch_assoc($ok))
                array_push($rows, $row);
        } else {
            $err = $this->error($sql, $this->get_error($ok == false ? null : $result, $sql));            
            throw new UrabeSQLException($err);
        }
        return $rows;
    }
    /**
     * Gets the query for selecting the table definition
     *
     * @param string $table_name The table name
     * @return string The table definition selection query
     */
    public function get_table_definition_query($table_name)
    {
        $fields = PG_FIELD_COL_ORDER . ", " . PG_FIELD_COL_NAME . ", " . PG_FIELD_DATA_TP . ", " .
            PG_FIELD_CHAR_LENGTH . ", " . PG_FIELD_NUM_PRECISION . ", " . PG_FIELD_NUM_SCALE;
        if (isset($this->schema)) {
            $schema = $this->schema;
            $sql = "SELECT $fields FROM information_schema.columns WHERE table_name = '$table_name' AND table_schema = '$schema'";
        } else
            $sql = "SELECT $fields FROM information_schema.columns WHERE table_name = '$table_name'";
        return $sql;
    }
    /**
     * Gets the error found in a ORACLE resource object could be a
     * SQL statement error or a connection error.
     *
     * @param string $sql The SQL statement
     * @param resource $resource The SQL connection
     * @return ConnectionError The connection or transaction error 
     */
    private function get_error($resource, $sql)
    {
        $this->error = new ConnectionError();
        $this->error->code = is_null($resource) ? PGSQL_Result::PGSQL_BAD_RESPONSE : pg_result_status($resource);
        $this->error->message = pg_last_error($this->connection);
        $this->error->sql = $sql;
        return $this->error;
    }

}
?>