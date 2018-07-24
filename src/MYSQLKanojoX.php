<?php 
include_once "KanojoX.php";
/**
 * A MySQL Connection object
 * 
 * Kanojo means girlfriend in japanese and this class saves the connection data structure used to connect to
 * an MySQL database.
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class MYSQLKanojoX extends KanojoX
{
    /**
     * @var string DEFAULT_CHAR_SET
     * The default char set, is UTF8
     */
    const DEFAULT_CHAR_SET = 'utf8';
    /**
     * Closes a connection
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close()
    {
        $this->free_result();
        if (!$this->connection)
            throw new Exception(ERR_NOT_CONNECTED);
        return mysqli_close($this->connection);
    }
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    public function free_result()
    {
        foreach ($this->statementsIds as &$statementId)
            mysqli_free_result($statementId);
    }
    /**
     * Open a MySQL Database connection
     *
     * @return stdClass The database connection object
     */
    public function connect()
    {
        try {
            $host = $this->host;
            $port = $this->port;
            $dbname = $this->db_name;
            $username = $this->user_name;
            $passwd = $this->password;
            $this->connection = mysqli_connect($host, $username, $passwd, $dbname, $port);
            if ($this->connection)
                $this->connection->set_charset(self::DEFAULT_CHAR_SET);
            return $this->connection;

        } catch (Exception $e) {
            return error(sprintf(ERR_BAD_CONNECTION, $e->getMessage()));
        }
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
            $error = new ConnectionError();
            $error->code = $this->connection->connect_errno;
            $error->message = $this->connection->error;
            $error->sql = $sql;
            return $error;
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
     * @throws Exception En Exception is raised if the connection is null
     * @return boolean|ConnectionError Returns TRUE on success or the connection error on failure. 
     */
    public function execute($sql, $variables = null)
    {
        try {
            if (!$this->connection)
                throw new Exception(ERR_NOT_CONNECTED);
            $statement = $this->parse($this->connection, $sql, $variables);
            if ($statement) {
                $ok = $statement->execute();
                return $ok ? $ok : $this->error($sql);
            } else {
                return $this->error($sql);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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
        $statement = $this->parse($this->connection, $sql, $variables);
        $class = get_class($statement);
        if ($class == CLASS_ERR)
            throw (!is_null($statement->sql) ? new UrabeSQLException($statement) : new Exception($statement->message, $statement->code));
        else {
            array_push($this->statementsIds, $statement);
            $ok = $statement->execute();
            if ($ok) {
                $result = $statement->get_result();
                while ($row = $result->fetch_assoc())
                    array_push($rows, $row);
            } else
                throw new UrabeSQLException($statement);
        }
        return $rows;
    }
    /**
     * Prepares sql_text using connection and returns the statement identifier, 
     * which can be used with execute(). 
     * @param mysqli $link MySQL active connection
     * @param string $sql The SQL text statement
     * @return mysqli_stmt Returns a statement handle on success, 
     * or a connection Error. 
     */
    private function parse($link, $sql, $variables = null)
    {
        if (!$link)
            throw new Exception(ERR_NOT_CONNECTED);
        $statement = $link->prepare($sql);
        if ($statement && isset($variables) && is_array($variables))
            $this->bind($statement, $variables);
        return $statement ? $statement : $this->error($sql);
    }
    /**
     * Binds a PHP variable to an Oracle placeholder
     *
     * @param resource $statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return void
     */
    private function bind($statement, $variables)
    {
        foreach ($variable as &$value) {
            if (is_int($value->variable))
                $tp = "i";
            else if (is_double($value->variable))
                $tp = "d";
            else if (is_string($value->variable))
                $tp = "s";
            else
                $tp = "b";
            return $stmt->bind_param($tp, $value->variable);
        }
    }
    /**
     * Gets the query for selecting the table definition
     *
     * @param string $table_name The table name
     * @return string The table definition selection query
     */
    public function get_table_definition_query($table_name)
    {
        $fields = MYSQL_FIELD_COL_ORDER . ", " . MYSQL_FIELD_COL_NAME . ", " + MYSQL_FIELD_DATA_TP . ", " .
            MYSQL_FIELD_CHAR_LENGTH . ", " . MYSQL_FIELD_NUM_PRECISION . ", " . MYSQL_FIELD_NUM_SCALE;
        if (isset($this->schema)) {
            $schema = $this->schema;
            $sql = "SELECT $fields FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME` = '$table_name' AND `TABLE_SCHEMA` = '$this->db_name'";
        } else
            $sql = "SELECT $fields FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME` = '$table_name'";
        return $sql;
    }
}
?>