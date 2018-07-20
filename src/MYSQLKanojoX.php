<?php 
include_once "KanojoX.php";
/**
 * A MySQL Connection object
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
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
        return $this->connection->close();
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
            $this->connection->set_charset(self::DEFAULT_CHAR_SET);
            return $this->connection;
        } catch (Exception $e) {
            return error(sprintf(ERR_BAD_CONNECTION, $e->getMessage()));
        }
    }
    /**
     * Get the last error message string of a connection
     *
     * @param string|null $sql The last excecuted statement. Can be null
     * @param ConnectionError $error If the error exists pass the erorr
     * @return ConnectionError The connection error 
     */
    public function error($sql, $error = null)
    {
        if (is_null($error)) {
            $error = new ConnectionError();
            $error->code = $this->connection->connect_errno;
            $error->message = $mysqli->connect_error;
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
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return resource Returns a statement object or FALSE if an error occurred. 
     */
    public function execute($sql, $variables = null)
    {
        try {
            $statement = $this->parse($sql);
            if (isset($variables) && is_array($variables))
                $this->bind($statement, $variables);
            $ok = $statement->execute();
            return $ok ? $statement : error($sql);
        } catch (Exception $e) {
            return error(sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage()));
        }
    }
    /**
     * Returns an associative array containing the next result-set row of a 
     * query. Each array entry corresponds to a column of the row. 
     * This function is typically called in a loop until it returns FALSE, 
     * indicating no more rows exist.
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return array Returns an associative array. If there are no more rows in the statement then the connection error is returned.
     * */
    public function fetch_assoc($sql, $variables = null)
    {
        $statement = $this->execute($sql, $variables);
        if (KanojoX::is_error($statement))
            return $statement;
        else {
            array_push($this->statementsIds, $statement);
            return $statement->fetch_assoc();
        }
    }
    /**
     * Prepares sql_text using connection and returns the statement identifier, 
     * which can be used with execute(). 
     *
     * @param string $sql The SQL text statement
     * @return mysqli Returns a statement handle on success, or FALSE on error. 
     */
    private function parse($link, $sql)
    {
        return $mysqli->prepare($sql);
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