<?php 
include_once "KanojoX.php";
/**
 * An ORACLE Connection object
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an Oracle database.
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class ORACLEKanojoX extends KanojoX
{
    /**
     * @var string DEFAULT_CHAR_SET
     * The default char set, is UTF8
     */
    const DEFAULT_CHAR_SET = 'AL32UTF8';
    /**
     * @var string ERR_CODE
     * The OCI Error field for error code
     */
    const ERR_CODE = 'code';
    /**
     * @var string ERR_MSG
     * The OCI Error field for error message
     */
    const ERR_MSG = 'message';
    /**
     * @var string ERR_SQL
     * The OCI Error field for error SQL
     */
    const ERR_SQL = 'sqltext';
    /**
     * @var string $owner The table owner used to filter the table definition
     */
    public $owner;
    /**
     * Open an ORACLE Database connection
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
            if (!isset($host) || strlen($host) == 0)
                $host = "127.0.0.1";
            $connString = $this->buildConnectionString($host, $dbname, $port);
            $this->connection = oci_connect($username, $passwd, $connString, self::DEFAULT_CHAR_SET);
            return $this->connection;
        } catch (Exception $e) {
            return $this->error(sprintf(ERR_BAD_CONNECTION, $e->getMessage()));
        }
    }
    /**
     * This function builds a connection string to connecto to ORACLE
     * by default is connected via SID
     *
     * @return string The connection string
     */
    public function buildConnectionString($host, $dbname, $port)
    {
        return create_SID_connection($host, $dbname, $port);
    }
    /**
     * Closes a connection
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close()
    {
        $this->free_result();
        return oci_close($this->connection);
    }
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    public function free_result()
    {
        foreach ($this->statementsIds as &$statementId)
            oci_free_statement($statementId);
    }
    /**
     * Get the last error message string of a connection
     *
     * @param ConnectionError $error If the error exists pass the eror
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($sql, $error = null)
    {
        if (is_null($error)) {
            $e = oci_error($this->connection);
            $this->error = new ConnectionError();
            $this->error->code = $e[self::ERR_CODE];
            $this->error->message = $e[self::ERR_MSG];
            $this->error->sql = isset($sql) ? $sql : $e[self::ERR_SQL];
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
     * @return resource Returns a statement handle on success, or FALSE on error.
     */
    public function execute($sql, $variables = null)
    {
        try {
            $statement = $this->parse($this->connection, $sql);
            if (isset($variables) && is_array($variables))
                $this->bind($statement, $variables);
            $ok = oci_execute($statement);
            return $ok ? $statement : $this->error($sql);
        } catch (Exception $e) {
            return $this->error(sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage()));
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
            return oci_fetch_assoc($statement);
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
        $fields = ORACLE_FIELD_COL_NAME+", "+
        if (isset($this->owner)){
            $format_query = "SELECT %s, %s, %s FROM ALL_TAB_COLS WHERE TABLE_NAME = '%s' AND OWNER = '%s'";
            $sql =sprintf($format_query,FIELD_COL_NAME, FIELD_DATA_TP, FIELD_DATA_LEN)
        }
        else
            $format_query = "SELECT %s, %s, %s FROM ALL_TAB_COLS WHERE TABLE_NAME = '%s'";

        return $sql;
    }
    /**
     * Prepares sql_text using connection and returns the statement identifier, 
     * which can be used with oci_execute(). 
     *
     * @param string $sql The SQL text statement
     * @return resource Returns a statement handle on success, or FALSE on error. 
     */
    private function parse($sql)
    {
        return oci_parse($this->connection, $sql);
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
        foreach ($variable as &$value)
            oci_bind_by_name($statement, ":" . $value->bv_name, $value->variable);
    }
}
/**
 * Creates a SID connection to an ORACLE database
 * @param string $host The connection host address
 * @param string $SID The database name or Service ID
 * @param string $port Oracle connection port
 * @return string The oracle connection string
 */
function create_SID_connection($host, $SID, $port)
{
    $strConn = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port)))(CONNECT_DATA=(SID=$SID)))";
    return $strConn;
}
?>